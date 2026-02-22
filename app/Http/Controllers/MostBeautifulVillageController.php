<?php

namespace App\Http\Controllers;

use App\Models\ApplicationScoreColumn;
use App\Models\Image;
use App\Models\MostBeautifulVillage;
use App\Models\Municipality;
use App\Models\User;
use App\Traits\ParentCRUDApplicationTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MostBeautifulVillageController extends CommonController
{
    use ParentCRUDApplicationTrait;

    protected $entity = 'most_beautiful_villages';

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            $contests = $this->getApplicationContests();
            $statuses = $this->getApplicationStatuses();
            $settlements = Municipality::whereNotNull('parent_id')->get()->pluck('name', 'id');
            $users = $this->getApplicationUsers();

            $this
                ->setCollect([
                    'titleIndex' => __("{$this->entity}_applications.title_index"),
                    'titleRestore' => __("{$this->entity}_applications.title_restore"),
                    'titleCreate' => __("{$this->entity}_applications.title_create"),
                    'titleEdit' => __("{$this->entity}_applications.title_edit"),
                ])
                ->setCollect('contests', $contests)
                ->setCollect('statuses', $statuses)
                ->setCollect('settlements', $settlements)
                ->setCollect('users', $users)
                ->setCollect([
                    'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                        [
                            'name' => $this->getCollect('titleIndex'),
                            'url' => route("applications.{$this->entity}.index")
                        ],
                    ]),
                ])
            ;

            return $next($request);
        });
    }

    /**
     * -----------------------------------
     * Вывод списка элементов.
     * Display a listing of the resource.
     * -----------------------------------
     *
     * @param Request $request
     * @param MostBeautifulVillage $model
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index(Request $request, MostBeautifulVillage $model): Application|Factory|View
    {
        $this->indexParent($request, $model);

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * ---------------------------------------------
     * Показать форму для создания нового элемента.
     * Show the form for creating a new resource.
     * ---------------------------------------------
     *
     * @param MostBeautifulVillage $model
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create(MostBeautifulVillage $model): Application|Factory|View
    {
        $this->createParent($model);

        $contest = $this->getContest($model, $this->entity);
        $user = auth()->user();

        $this
            ->setCollect('contest', $contest)
            ->setCollect('user', $user)
        ;

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * --------------------------------------------------
     * Сохранение вновь созданного элемента в хранилище.
     * Store a newly created resource in storage.
     * --------------------------------------------------
     *
     * @param Request $request
     * @param MostBeautifulVillage $model
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request, MostBeautifulVillage $model): Application|Redirector|RedirectResponse
    {
        $result = $this->storeParent($request, $model);

        if ($result instanceof RedirectResponse) {

            return $result;
        }

        return redirect(route("applications.{$this->entity}.edit", $result));
    }

    /**
     * --------------------------------------------------
     * Показать форму для редактирования элемента.
     * Show the form for editing the specified resource.
     * --------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(MostBeautifulVillage $model): Application|Factory|View
    {
        $this->editParent($model);

        $contest = $this->getContest($model, $this->entity);
        $user = $model->user;
        $estimateColumns = ApplicationScoreColumn::where('application_type', 'most_beautiful_villages')->get();
        $estimates = $model->estimates->where('user_id', auth()->id());

        $totalRating = $estimates
            ->sum(function ($item) {

                return $item->value * $item->column->significance_factor;
            })
        ;

        $this
            ->setCollect('contest', $contest)
            ->setCollect('user', $user)
            ->setCollect('estimateColumns', $estimateColumns)
            ->setCollect('estimates', $estimates)
            ->setCollect('totalRating', $totalRating)
        ;

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * ------------------------------------------
     * Обновление элемента в хранилище.
     * Update the specified resource in storage.
     * ------------------------------------------
     *
     * @param Request $request
     * @param MostBeautifulVillage $model
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, MostBeautifulVillage $model): Application|Redirector|RedirectResponse
    {
        $result = $this->updateParent($request, $model);

        if ($result instanceof RedirectResponse) {

            return $result;
        }

        $requestData = $request->all();

        if(isset($requestData['images'])) {
            $ids = array_keys($requestData['images']);
            $images = Image::whereIn('id', $ids)->get();
            foreach ($images as $image) {
                $image->description = $requestData['images']["{$image->id}"];
                $image->update();
            }
        }

        return redirect(route("applications.{$this->entity}.edit", $result));
    }

    /**
     * --------------------------------------------
     * Удаление элементов из хранилища.
     * Remove the specified resource from storage.
     * --------------------------------------------
     *
     * @param Request $request
     * @param MostBeautifulVillage $model
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Request $request, MostBeautifulVillage $model)
    {
        $result = $this->destroyParent($request, $model);

        return response()->json($result);
    }

    /**
     * ---------------------------------------------
     * Восстановите элементов из хранилища.
     * Restore the specified resource from storage.
     * ---------------------------------------------
     *
     * @param Request $request
     * @param MostBeautifulVillage $model
     * @return Application|Factory|JsonResponse|View
     * @throws AuthorizationException
     */
    public function restore(Request $request, MostBeautifulVillage $model): Application|Factory|JsonResponse|View
    {
        $this->authorize('restore', $model);

        if ($request->isMethod('GET')) {

            $this->restoreParent($request, $model);

            $contests = $this->getApplicationContests();
            $statuses = $this->getApplicationStatuses();

            $this
                ->setCollect('contests', $contests)
                ->setCollect('statuses', $statuses)
            ;

            return view("applications." . __FUNCTION__, $this->getCollect());
        } else {

            $result = $this->restoreParent($request, $model);

            return response()->json($result);
        }
    }

    /**
     * ---------------------------
     * Сохранение оценок комиссии
     * ---------------------------
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function estimate(Request $request)
    {
        $morphClass = $request->input('morph_class');
        $modelId = $request->input('model_id');

        $model = (new $morphClass())->find($modelId);

        if ($model) {

            foreach ((array)$request->get('columns') as $columnId => $value) {

                $estimate = $model
                    ->estimates()
                    ->firstOrNew([
                        'application_score_column_id' => $columnId,
                        'user_id' => auth()->id(),
                    ])
                ;

                $estimate
                    ->fill([
                        'value' => $value
                    ])
                    ->save()
                ;
            }

            $model->recalculateTotalRating();
        }

        return back();
    }

    public function prepareForValidation(Request $request)
    {
        $requestData = $request->all();

        $requestData['status'] = $request->filled('draft') ? 'draft' : 'published';

        $request->merge($requestData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function filter(Request $request)
    {
        // Инициализируем переменную
        $path = '';

        // Если есть параметр 'page' в request или 'page' в сессии
        if ($request->has('page') || session()->has("{$this->entity}.page")) {

            // Если есть параметр 'page' в request
            if ($request->has('page')) {

                // Пишем его в сессию
                session()->put("{$this->entity}.page", $request->input('page'));
            }

            if (!$request->filled('pagination')) {

                session()->put("{$this->entity}.page", 1);
            }

            // Если 'page' в сессии есть и не null и начиная со второй
            if (session()->has("{$this->entity}.page") && (int) session("{$this->entity}.page") > 1) {

                // Получаем гет параметр page (если локали нету то гет параметр page стоит первым)
                $path = '?page=' . session("{$this->entity}.page");
            }
        }

        // Если метод отправки get
        if ($request->isMethod('GET')) {

            // и сброс
            if ($request->has('reset')) {

                // Очищаем сессию
                session()->forget("{$this->entity}");
            }

            // Если page присутствует в сессии и не была передана
            if (session()->has("{$this->entity}.page") && !$request->has('page')) {

                // Очищаем page в сессии
                session()->put("{$this->entity}.page", null);
            }

            return redirect(route("applications.{$this->entity}.{$request->input('method')}") . $path);

            // Если метод отправки post
        } else {

            // Удаляем токен из данных
            $requestData = collect($request->except(['_token']))
                ->filter(function ($value, $key){

                    return !in_array($value, [null, [], '']);
                })
                ->toArray()
            ;

            session()->put("{$this->entity}", $requestData);

            return redirect(route("applications.{$this->entity}.{$request->input('method')}") . $path);
        }
    }

    public function saveMatrix($model, $requestData)
    {
        if (isset($requestData['data_on_internet'])) {

            // Наличие Интернет-сайта села (поселка, деревни), группы, сообщества в сети Интернет
            $model
                ->data_on_internet()
                ->where('user_id', $model->user_id)
                ->delete()
            ;

            $dataOnInternetFields = array_filter($requestData['data_on_internet']['field71']);

            foreach ($dataOnInternetFields as $key => $start) {

                $data = [
                    'group' => 'data_on_internet',
                    'user_id' => $model->user_id ?? auth()->id(),
                    'field71' => $requestData['data_on_internet']['field71'][$key],
                ];

                $model
                    ->data_on_internet()
                    ->create($data)
                ;
            }
        }

        if (isset($requestData['cultural_events'])) {

            // Культурно-массовые мероприятия за предыдущий год
            $model
                ->cultural_events()
                ->where('user_id', $model->user_id)
                ->delete()
            ;

            $dataOnInternetFields = array_filter($requestData['cultural_events']['field72']);

            foreach ($dataOnInternetFields as $key => $start) {

                $data = [
                    'group' => 'cultural_events',
                    'user_id' => $model->user_id ?? auth()->id(),
                    'field72' => $requestData['cultural_events']['field72'][$key],
                ];

                $model
                    ->cultural_events()
                    ->create($data)
                ;
            }
        }
    }

    public function exportTable($type, MostBeautifulVillage $application)
    {
        $idUsers = DB::table('application_estimates')->where('entity_type', $application->getMorphClass())->get()->pluck('user_id')->unique()->toArray();

        $users = User::whereIn('id', $idUsers)
            ->orderBy('id')
            ->get()
        ;

        $symbols = collect(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']);

        $symbols = $symbols->slice(3, $users->count() + 1)->values();

        $userNames = $users->pluck('full_name', 'id');

        $applications = $application
            ->withCount('estimates')
            ->get()
            ->where('estimates_count', '>', 0)
        ;

        $estimateColumns = ApplicationScoreColumn::where('application_type', 'most_beautiful_villages')->orderBy('id')->get();

        $spreadsheet = $this->getHeaderTable($userNames, $symbols);
        $activeSheet = $spreadsheet->getActiveSheet();

        $i = 2;

        $userKeys = $userNames->keys()->flip()->toArray();

        foreach ($userKeys as $key => $userKey) {
            $userKeys[$key] = 0;
        }

        $estimationAll = [
            'users' => $userKeys,
            'sum' => 0,
        ];

        foreach ($applications as $applicationKey => $applicationModel) {

            foreach ($estimateColumns as $estimateKey => $estimateColumn) {

                $i++;

                $activeSheet->getRowDimension($i)->setRowHeight(-1);

                $activeSheet->setCellValue("A{$i}", $estimateColumn->name);
                $activeSheet->setCellValue("B{$i}", $applicationModel->municipality?->parent?->name);
                $activeSheet->setCellValue("C{$i}", $applicationModel->municipality?->name);

                $estimationSum = 0;
                foreach ($userNames->keys() as $key => $userId) {

                    $estimation = (float)$applicationModel->estimates->where('user_id', $userId)->where('application_score_column_id', $estimateColumn->id)->first()?->value ?? 0;

                    $estimationAll['users'][$userId] += $estimation;
                    $estimationSum += $estimation;

                    $activeSheet->setCellValue("{$symbols->get($key)}{$i}", $estimation);
                }
                $estimationAll['sum'] += $estimationSum;
                $activeSheet->setCellValue("{$symbols->last()}{$i}", $estimationSum);

                $sharedStyle = new \PhpOffice\PhpSpreadsheet\Style\Style();

                $sharedStyle->applyFromArray(
                    [
                        'font' => [
                            'name' => 'Times New Roman',
                            'size' => 11,
                            'color' => ['argb' => '00000000'],
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        ],
                        'borders' => [
                            'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                            'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                        ],
                    ]
                );

                $activeSheet
                    ->duplicateStyle($sharedStyle, "A{$i}:{$symbols->last()}{$i}");

                $activeSheet
                    ->getStyle("A{$i}")->getBorders()->getLeft()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));

                $activeSheet->getStyle("A{$i}:{$symbols->last()}{$i}")->getAlignment()->setWrapText(true);

                $activeSheet->getStyle("A{$i}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }

            $i++;

            $activeSheet->mergeCells("A{$i}:C{$i}");

            $activeSheet->getRowDimension($i)->setRowHeight(-1);

            $activeSheet->setCellValue("A{$i}", "Итого по: {$applicationModel->municipality?->parent?->name} / {$applicationModel->municipality?->name}");

            foreach ($userNames->keys() as $key => $userId) {

                $activeSheet->setCellValue("{$symbols->get($key)}{$i}", $estimationAll['users'][$userId]);
            }
            $activeSheet->setCellValue("{$symbols->last()}{$i}", $estimationAll['sum']);

            $sharedStyle = new \PhpOffice\PhpSpreadsheet\Style\Style();

            $sharedStyle->applyFromArray(
                [
                    'font' => [
                        'name' => 'Times New Roman',
                        'bold' => true,
                        'size' => 11,
                        'color' => ['argb' => '00000000'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    ],
                    'borders' => [
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                        'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    ],
                ]
            );

            $activeSheet
                ->duplicateStyle($sharedStyle, "A{$i}:{$symbols->last()}{$i}");

            $activeSheet
                ->getStyle("A{$i}")->getBorders()->getLeft()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));

            $activeSheet->getStyle("A{$i}:{$symbols->last()}{$i}")->getAlignment()->setWrapText(true);

            $activeSheet->getStyle("A{$i}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }

        $fileName = 'Рейтинговая таблица проектов';

        if ($type === 'pdf') {

            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);

            // Redirect output to a client’s web browser (PDF)
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $fileName . '.pdf"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');

        } else {

            // Перенаправление вывода в веб-браузер клиента (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');

            // Если вы обслуживаете IE 9, то может потребоваться следующее
            header('Cache-Control: max-age=1');

            // Если вы обслуживаете IE по протоколу SSL, то может потребоваться следующее
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        }

        $writer->save('php://output');
        exit;
    }

    public function getHeaderTable($userNames, $symbols)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.common.app_name', ''))
            ->setLastModifiedBy(config('app.common.app_name', ''))
            ->setTitle('Рейтинговая таблица проектов')
            ->setSubject('Рейтинговая таблица проектов')
            ->setDescription('Шаблон рейтинговой таблицы по итогам подачи заявок  на конкурс «Самое красивое село (деревня, поселок)»')
            ->setKeywords('Рейтинговая таблица проектов')
            ->setCategory('Рейтинговая таблица проектов');

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();

        $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $activeSheet->getRowDimension(1)->setRowHeight(30);
        $activeSheet->getColumnDimension('A')->setWidth(60);
        $activeSheet->getColumnDimension('B')->setWidth(50);
        $activeSheet->getColumnDimension('C')->setWidth(50);
        foreach ($symbols as $key => $symbol) {

            $activeSheet->getColumnDimension("{$symbol}")->setWidth(15);
        }
        $activeSheet->getColumnDimension("{$symbols->last()}")->setWidth(15);

        $headerStyle = new \PhpOffice\PhpSpreadsheet\Style\Style();

        $headerStyle->applyFromArray(
            [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 15,
                    'color' => ['argb' => '00000000'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                ],
                'borders' => [
                    'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]
        );

        $activeSheet->mergeCells("A1:{$symbols->last()}1");
        $activeSheet->getStyle("A1:{$symbols->last()}1")->getFont()->setName('Arial')->setSize(16);
        $activeSheet
            ->setCellValue('A1', 'Шаблон рейтинговой таблицы по итогам подачи заявок  на конкурс «Самое красивое село (деревня, поселок)»')
        ;

        $activeSheet->getRowDimension(2)->setRowHeight(60);

        $activeSheet->setCellValue('A2', '');
        $activeSheet->setCellValue('B2', 'Район/ Поселение');
        $activeSheet->setCellValue('C2', 'Населенный пункт');
        foreach ($symbols as $key => $symbol) {

            $activeSheet->setCellValue("{$symbol}2", "{$userNames->values()->get($key)}");
        }
        $activeSheet->setCellValue("{$symbols->last()}2", 'Итоговый балл');

        $activeSheet
            ->duplicateStyle($headerStyle, "A1:{$symbols->last()}2")
        ;

        $activeSheet
            ->getStyle("A1:{$symbols->last()}1")->getBorders()->getTop()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
        ;

        $activeSheet
            ->getStyle('A1')->getBorders()->getLeft()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
        ;

        $activeSheet
            ->getStyle('A2')->getBorders()->getLeft()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
        ;

        $activeSheet->getStyle("A2:{$symbols->last()}2")->getAlignment()->setWrapText(true);
        $activeSheet->getStyle("A2:{$symbols->last()}2")->getFont()->setSize(11);

        return $spreadsheet;
    }
}
