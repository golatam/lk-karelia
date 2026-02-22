<?php

namespace App\Http\Controllers;

use App\Models\ApplicationEstimate;
use App\Models\ApplicationScoreColumn;
use App\Models\Contest;
use App\Models\LPTOSApplication;
use App\Models\Municipality;
use App\Models\Register;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LPTOSApplicationController extends CommonController
{
    protected $entity = 'lptos';

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 1200);

            View::share('entity', $this->entity);

            $this
                ->setCollect([
                    'titleIndex' => __("{$this->entity}_applications.title_index"),
                    'titleRestore' => __("{$this->entity}_applications.title_restore"),
                    'titleCreate' => __("{$this->entity}_applications.title_create"),
                    'titleEdit' => __("{$this->entity}_applications.title_edit"),
                ])
                ->setCollect('years', $this->years($this->entity))
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
     * Display a listing of the resource.
     *
     * @param LPTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(LPTOSApplication $model)
    {
        $this->authorize('view', $model);

        if (auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee'])) {

            $models = $this
                ->getApplicationModels($model)
                ->orderBy($model->columnSorting, $model->directionSorting)
                ->orderBy('total_application_points', 'desc')
                ->orderBy('created_at', 'asc')
                ->paginate($model->totalRecords)
            ;
        } else {

            $models = $this
                ->getApplicationModels($model, auth()->id())
                ->orderBy($model->columnSorting, $model->directionSorting)
                ->orderBy('total_application_points', 'desc')
                ->orderBy('created_at', 'asc')
                ->paginate($model->totalRecords)
            ;
        }

        $redirectRouteName = __FUNCTION__;

        $models_count = $models->total();

        $this->setCommonDataApplication($model);

        $contests = $this->getApplicationContests();
        $municipalities = $this->getApplicationMunicipalities();
        $users = $this->getApplicationUsers();
        $statuses = $this->getApplicationStatuses();
        $registers = $this->getApplicationRegisters();
        $nominations = $this->getNominations();
        $categories = $this->getCategories();

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('contests', $contests)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('registers', $registers)
            ->setCollect('users', $users)
            ->setCollect('nominations', $nominations)
            ->setCollect('categories', $categories)
            ->setCollect('statuses', $statuses)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
        ;

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param LPTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(LPTOSApplication $model)
    {
        $this->authorize('create', $model);

        $contest = $this->getContest($model, 'lptos');
        $user = auth()->user();
        $municipalities = $user->municipalitiesList;
        $nominations = $this->getNominations();
        $categories = $this->getCategories();

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleCreate'),
                        'url' => route("applications.{$this->entity}." . __FUNCTION__)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('contest', $contest)
            ->setCollect('user', $user)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('nominations', $nominations)
            ->setCollect('categories', $categories)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LPTOSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, LPTOSApplication $model)
    {
        $this->prepareForValidation($request);

        $requestData = $request->all();

        if ($requestData['status'] === 'published') {

            $rules = $this->getPublishedRules();
        } else {

            $rules = $this->getDraftRules();
        }

        $validator = Validator::make($requestData, $rules, $this->getMessages(), $this->getAttributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model = $model->create($requestData);

        $this->saveMatrix($model, $requestData);

        return redirect(route("applications.{$this->entity}.edit", $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LPTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(LPTOSApplication $model)
    {
        $this->authorize('update', $model);

        if (!auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']) && ((int) $model->user_id !== (int) auth()->id())) {

            return abort(404);
        }

        $contest = $this->getContest($model, 'lptos');
        $user = $model->user;
        $municipalities = $user->municipalitiesList;
        $nominations = $this->getNominations();
        $categories = $this->getCategories();
        $estimateColumns = ApplicationScoreColumn::where('application_type', 'lptos_applications')->get();
        $estimates = $model->estimates->where('user_id', auth()->id());

        $totalRating = $estimates
            ->sum(function ($item) {

                return $item->value * $item->column->significance_factor;
            })
        ;

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleEdit'),
                        'url' => route("applications.{$this->entity}." . __FUNCTION__, $model)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('contest', $contest)
            ->setCollect('user', $user)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('nominations', $nominations)
            ->setCollect('categories', $categories)
            ->setCollect('breadcrumbs', (string)View::make("partial.breadcrumb", $this->getCollect())->render())
            ->setCollect('estimateColumns', $estimateColumns)
            ->setCollect('estimates', $estimates)
            ->setCollect('totalRating', $totalRating)
        ;


        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LPTOSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LPTOSApplication $model)
    {
        $this->prepareForValidation($request);

        $requestData = $request->all();

        if ($requestData['status'] === 'published') {

            $rules = $this->getPublishedRules();
        } else {

            $rules = $this->getDraftRules();
        }

        $validator = Validator::make($requestData, $rules, $this->getMessages(), $this->getAttributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model->update($requestData);

        $this->saveMatrix($model, $requestData);

        return redirect(route("applications.{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param LPTOSApplication $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, LPTOSApplication $model)
    {
        $this->authorize('delete', $model);

        $result = [
            'data' => [],
        ];

        $ids = $request->input('ids');

        try{

            if (is_array($ids) && !empty($ids)) {

                foreach($ids as $k => $id) {

                    $foundModel = $model->find($id);

                    if ($foundModel) {

                        $foundModel->delete();

                        $result['data'][] = [
                            'success' => true,
                            'id' => $id,
                            'message' => __("common.entry_successfully_deleted", ['id' => $id]),
                        ];
                    } else {

                        $result['data'][] = [
                            'success' => false,
                            'id' => $id,
                            'message' => __("common.entry_missing", ['id' => $id]),
                        ];
                    }
                }
            } else {

                $result['data'][] = [
                    'success' => false,
                    'message' => __("common.no_data_was_sent_for_deletion"),
                ];
            }
        } catch (\Exception $e) {

            $result['data'][] = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param Request $request
     * @param LPTOSApplication $model
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, LPTOSApplication $model)
    {
        $this->authorize('restore', $model);

        if ($request->isMethod('GET')) {

            if (auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee'])) {

                $models = $this
                    ->getApplicationModels($model)
                    ->orderBy($model->columnSorting, $model->directionSorting)
                    ->orderBy('total_application_points', 'desc')
                    ->orderBy('created_at', 'asc')
                    ->onlyTrashed()
                    ->paginate($model->totalRecords)
                ;
            } else {

                $models = $this
                    ->getApplicationModels($model, auth()->id())
                    ->orderBy($model->columnSorting, $model->directionSorting)
                    ->orderBy('total_application_points', 'desc')
                    ->orderBy('created_at', 'asc')
                    ->onlyTrashed()
                    ->paginate($model->totalRecords)
                ;
            }

            $redirectRouteName = __FUNCTION__;

            $models_count = $models->count();

            $this->setCommonDataApplication($model);

            $contests = $this->getApplicationContests();
            $municipalities = $this->getApplicationMunicipalities();
            $users = $this->getApplicationUsers();
            $statuses = $this->getApplicationStatuses();
            $registers = $this->getApplicationRegisters();
            $nominations = $this->getNominations();
            $categories = $this->getCategories();

            $this
                ->setCollect([
                    'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                        [
                            'name' => $this->getCollect('titleRestore'),
                            'url' => route("applications.{$this->entity}." . __FUNCTION__)
                        ],
                    ]),
                ])
                ->setCollect('model', $model)
                ->setCollect('models', $models)
                ->setCollect('models_count', $models_count)
                ->setCollect('redirectRouteName', $redirectRouteName)
                ->setCollect('contests', $contests)
                ->setCollect('municipalities', $municipalities)
                ->setCollect('registers', $registers)
                ->setCollect('users', $users)
                ->setCollect('nominations', $nominations)
                ->setCollect('categories', $categories)
                ->setCollect('statuses', $statuses)
                ->setCollect('breadcrumbs', (string)View::make("partial.breadcrumb", $this->getCollect())->render());

            return view("applications." . __FUNCTION__, $this->getCollect());
        } else {

            $result = [
                'data' => [],
            ];

            $ids = $request->input('ids');

            try{

                if (is_array($ids) && !empty($ids)) {

                    foreach($ids as $k => $id) {

                        $foundModel = $model
                            ->onlyTrashed()
                            ->find($id)
                        ;

                        if ($foundModel) {

                            $foundModel->restore();

                            $result['data'][] = [
                                'success' => true,
                                'id' => $id,
                                'message' => __("common.entry_successfully_restored", ['id' => $id]),
                            ];
                        } else {

                            $result['data'][] = [
                                'success' => false,
                                'id' => $id,
                                'message' => __("common.entry_missing", ['id' => $id]),
                            ];
                        }
                    }
                } else {

                    $result['data'][] = [
                        'success' => false,
                        'message' => __("common.no_data_was_sent_for_restoration"),
                    ];
                }
            } catch (\Exception $e) {

                $result['data'][] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }

            return response()->json($result);
        }
    }

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

    public function getDraftRules()
    {
        return [
            'user_id'       => 'required',
            'contest_id'    => 'required',
        ];
    }

    public function getPublishedRules()
    {
        return [
            'user_id'       => 'required',
            'contest_id'    => 'required',
        ];
    }

    public function getMessages()
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
        ];
    }

    public function getAttributes()
    {
        return [
            'user_id' => 'Участник',
            'contest_id' => 'Конкурс',
        ];
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

    public function getNominations()
    {
        return config("app.{$this->entity}_applications.nominations", []);
    }

    public function getCategories()
    {
        return config("app.{$this->entity}_applications.categories", []);
    }

    public function saveMatrix($model, $requestData)
    {
        // Перечень документов, регламентирующих деятельность в рамках реализации практики (проекта)
        $model
            ->list_documents_regulating_activity()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['list_documents_regulating_activity']['field7'] as $key => $start) {

            $data = [
                'group' => 'list_documents_regulating_activity',
                'user_id' => $model->user_id ?? auth()->id(),
                'field7' => $requestData['list_documents_regulating_activity']['field7'][$key],
                'field8' => $requestData['list_documents_regulating_activity']['field8'][$key],
                'field9' => $requestData['list_documents_regulating_activity']['field9'][$key],
                'field10' => $requestData['list_documents_regulating_activity']['field10'][$key],
            ];

            $model
                ->list_documents_regulating_activity()
                ->create($data)
            ;
        }
    }

    public function exportTable($type, LPTOSApplication $application)
    {
        $applications = $application
            ->orderBy('total_application_points', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
        ;

        $spreadsheet = $this->getHeaderTable();
        $activeSheet = $spreadsheet->getActiveSheet();

        foreach ($applications as $applicationKey => $applicationItem) {

            $i = ($applicationKey + 3);

            $key = ($applicationKey + 1);

            $activeSheet->getRowDimension($i)->setRowHeight(-1);

            $estimateStart = $i;

            $relations = [
                1 => 'K',
                2 => 'L',
                3 => 'M',
                4 => 'N',
                5 => 'O',
                6 => 'P',
                7 => 'Q',
                8 => 'R',
                9 => 'S',
                10 => 'T',
                11 => 'U',
                12 => 'V',
                13 => 'W',
            ];

//            dd($applicationItem->estimatesGroupByUserId);

            foreach ($applicationItem->estimatesGroupByUserId as $estimateKey => $estimateItem) {

                foreach ($relations as $relationKey => $relationItem) {

                    $estimate = $estimateItem->where('application_score_column_id', $relationKey)->first();

                    if ($estimate) {

                        $activeSheet->setCellValue("{$relationItem}{$i}", "{$estimate->userName} - {$estimate->value}");
                    } else {

                        $activeSheet->setCellValue("{$relationItem}{$i}", "---");
                    }
                }

                if ($applicationItem->estimatesGroupByUserId->count() > ($estimateKey + 1)) {

                    $i++;
                }
            }

            $activeSheet->mergeCells("A{$estimateStart}:A{$i}");
            $activeSheet->mergeCells("B{$estimateStart}:B{$i}");
            $activeSheet->mergeCells("C{$estimateStart}:C{$i}");
            $activeSheet->mergeCells("D{$estimateStart}:D{$i}");
            $activeSheet->mergeCells("E{$estimateStart}:E{$i}");
            $activeSheet->mergeCells("F{$estimateStart}:F{$i}");
            $activeSheet->mergeCells("G{$estimateStart}:G{$i}");
            $activeSheet->mergeCells("G{$estimateStart}:G{$i}");
            $activeSheet->mergeCells("H{$estimateStart}:H{$i}");
            $activeSheet->mergeCells("I{$estimateStart}:I{$i}");
            $activeSheet->mergeCells("J{$estimateStart}:J{$i}");

            $projectCost = "Собственные финансовые средства: {$applicationItem->implementation_resources_involved_practice_own}; \nПривлеченные финансовые средства (из регионального и муниципального бюджетов - при наличии): {$applicationItem->implementation_resources_involved_practice_budget}";

            $activeSheet->setCellValue("A{$estimateStart}", $key);
            $activeSheet->setCellValue("B{$estimateStart}", $applicationItem->municipalityParentName);
            $activeSheet->setCellValue("C{$estimateStart}", $applicationItem->municipalityUserName);
            $activeSheet->setCellValue("D{$estimateStart}", $applicationItem->tosName);
            $activeSheet->setCellValue("E{$estimateStart}", config("app.{$this->entity}_applications.nominations.{$applicationItem->contest_nomination}", ''));
            $activeSheet->setCellValue("F{$estimateStart}", $applicationItem->practice_name);
            $activeSheet->setCellValue("G{$estimateStart}", $applicationItem->is_tos_legal_entity ? 'Да' : 'Нет');
            $activeSheet->setCellValue("H{$estimateStart}", $applicationItem->population_size_in_tos);
            $activeSheet->setCellValue("I{$estimateStart}", $applicationItem->number_beneficiaries);
            $activeSheet->setCellValue("J{$estimateStart}", $projectCost);

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
                ->duplicateStyle($sharedStyle, "A{$estimateStart}:W{$estimateStart}")
            ;

            $activeSheet
                ->getStyle("A{$estimateStart}")->getBorders()->getLeft()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
            ;

            $activeSheet->getStyle("A{$estimateStart}:W{$estimateStart}")->getAlignment()->setWrapText(true);
        }

        $fileName = 'Рейтинговая таблица проектов';

        if ($type === 'pdf') {
            \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);
            \PhpOffice\PhpWord\Settings::setTempDir(\PhpOffice\PhpSpreadsheet\Shared\File::sysGetTempDir());

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

    public function getHeaderTable()
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.common.app_name', ''))
            ->setLastModifiedBy(config('app.common.app_name', ''))
            ->setTitle('Рейтинговая таблица проектов')
            ->setSubject('Рейтинговая таблица проектов')
            ->setDescription('Рейтинговая таблица проектов, допущенных для участия в конкурсном отборе по Программе поддержки местных инициатив в Республике Карелия в 2022 году.')
            ->setKeywords('Рейтинговая таблица проектов')
            ->setCategory('Рейтинговая таблица проектов');

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();

        $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $activeSheet->getRowDimension(1)->setRowHeight(30);
        $activeSheet->getColumnDimension('A')->setWidth(7.63);
        $activeSheet->getColumnDimension('B')->setWidth(18.38);
        $activeSheet->getColumnDimension('C')->setWidth(16.89);
        $activeSheet->getColumnDimension('D')->setWidth(16.89);
        $activeSheet->getColumnDimension('E')->setWidth(17.33);
        $activeSheet->getColumnDimension('F')->setWidth(17.33);
        $activeSheet->getColumnDimension('G')->setWidth(8.11);
        $activeSheet->getColumnDimension('H')->setWidth(8.11);
        $activeSheet->getColumnDimension('I')->setWidth(22.22);
        $activeSheet->getColumnDimension('J')->setWidth(14.56);
        $activeSheet->getColumnDimension('K')->setWidth(14.56);
        $activeSheet->getColumnDimension('L')->setWidth(9.33);
        $activeSheet->getColumnDimension('M')->setWidth(8.11);
        $activeSheet->getColumnDimension('N')->setWidth(9.67);
        $activeSheet->getColumnDimension('O')->setWidth(8.11);
        $activeSheet->getColumnDimension('P')->setWidth(11.78);
        $activeSheet->getColumnDimension('Q')->setWidth(11.78);
        $activeSheet->getColumnDimension('R')->setWidth(10.67);
        $activeSheet->getColumnDimension('S')->setWidth(10.67);
        $activeSheet->getColumnDimension('T')->setWidth(10.67);
        $activeSheet->getColumnDimension('U')->setWidth(10.67);
        $activeSheet->getColumnDimension('V')->setWidth(16.78);
        $activeSheet->getColumnDimension('W')->setWidth(16.78);

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

        $activeSheet->mergeCells('A1:W1');
        $activeSheet->getStyle('A1:W1')->getFont()->setName('Arial')->setSize(16);
        $activeSheet
            ->setCellValue('A1', 'Рейтинговая таблица проектов, допущенных для участия в конкурсном отборе по Программе поддержки местных инициатив в Республике Карелия в 2022 году.')
        ;

        $activeSheet->getRowDimension(2)->setRowHeight(120);

        $activeSheet->setCellValue('A2', '№ п/п');
        $activeSheet->setCellValue('B2', 'Муниципальный район');
        $activeSheet->setCellValue('C2', 'Поселение');
        $activeSheet->setCellValue('D2', 'Наименование ТОС');
        $activeSheet->setCellValue('E2', 'Номинация конкурса');
        $activeSheet->setCellValue('F2', 'Название проекта');
        $activeSheet->setCellValue('G2', 'Является ли или нет юр.лицом');
        $activeSheet->setCellValue('H2', 'Количество граждан, проживающих в границах ТОС');
        $activeSheet->setCellValue('I2', 'Количество человек (благополучателей), которые будут пользоваться результатами практики (проекта)');
        $activeSheet->setCellValue('J2', 'Стоимость проекта, в том числе средства РК, местный бюджет, собственные средства');
        $activeSheet->setCellValue('K2', 'Доля жителей вовлеченных в деятельность ТОС при реализации практики');
        $activeSheet->setCellValue('L2', 'Количество человек, проживающих в границах ТОС, которые пользуются результатами Проекта');
        $activeSheet->setCellValue('M2', 'Количество реализованных практик (проектов) и инициатив ТОС за предыдущий год (кроме заявляемой практики (проекта))');
        $activeSheet->setCellValue('N2', 'Обоснованность и актуальность проблемы, на решение которой направлен проект');
        $activeSheet->setCellValue('O2', 'Перспектива дополнительной реализации проекта (без дополнительного финансирования)');
        $activeSheet->setCellValue('P2', 'Масштаб проделанных по проекту работ');
        $activeSheet->setCellValue('Q2', 'Финансовая эффективность проекта - на одного жителя');
        $activeSheet->setCellValue('R2', 'Финансовая эффективность проекта - на одного благополучателя');
        $activeSheet->setCellValue('S2', 'Привлечение внебюджетных средств на осуществление практики (проекта) ТОС, объемы привлеченного внебюджетного финансирования');
        $activeSheet->setCellValue('T2', 'Использование механизмов волонтерства (привлечение жителей территории, на которой осуществляется проект, к выполнению определенного перечня работ на безвозмездной основе)');
        $activeSheet->setCellValue('U2', 'Использование механизмов социального партнерства (взаимодействие с органами государственной власти, органами местного самоуправления муниципальных образований, организациями и учреждениями, действующими на территории осуществления проекта)');
        $activeSheet->setCellValue('V2', 'Количество проведенных собраний (советов, конференций, заседаний органов ТОС) и рассматриваемые вопросы');
        $activeSheet->setCellValue('W2', 'Освещение информации о деятельности и достижениях ТОС в средствах массовой информации, в том числе в официальных группах (чатах) популярных социальных сетей');

        $activeSheet
            ->duplicateStyle($headerStyle, 'A1:W2')
        ;

        $activeSheet
            ->getStyle('A1:W1')->getBorders()->getTop()
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

        $activeSheet->getStyle('A2:W2')->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('A2:W2')->getFont()->setSize(11);

        return $spreadsheet;
    }
}
