<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RegisterController extends CommonController
{
    protected $entity = 'registers';

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            // ini_set('memory_limit', '512M');     // OK - 512MB
            // ini_set('upload_max_filesize', '200M');     // OK - 200MB
            ini_set('memory_limit', 512000000);  // OK - 512MB
            ini_set('upload_max_filesize', 10000000);  // OK - 10MB

            View::share('entity', $this->entity);

            $this->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleIndex'),
                        'url' => route("{$this->entity}.index")
                    ],
                ]),
            ]);

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param Register $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Register $model)
    {
        $this->authorize('view', $model);

        $models = $model->filtering();

        $models = $models
            ->orderBy($model->columnSorting, $model->directionSorting)
            ->paginate($model->totalRecords)
        ;

        $redirectRouteName = __FUNCTION__;

        $models_count = $models->total();

        $this->setCommonData($model);

        $municipalities = Municipality::all()->pluck('name', 'id')->toArray();

//        $regionIds = $model->all()->pluck('name_region');
//        $regions = Municipality::all()->whereIn('id', $regionIds)->pluck('name', 'id')->toArray();

        $regions = Municipality::whereNull('parent_id')->get()->pluck('name', 'id')->toArray();
        $settlements = Municipality::whereNotNull('parent_id')->get()->pluck('name', 'id')->toArray();

//        dd($regions, $settlements, $municipalities);

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('regions', $regions)
            ->setCollect('settlements', $settlements)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view(__FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Register $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Register $model)
    {
        $this->authorize('create', $model);

        $regions = Municipality::whereNull('parent_id')->get();
        $settlements = Municipality::whereNotNull('parent_id')->get();

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleCreate'),
                        'url' => route("{$this->entity}." . __FUNCTION__)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('regions', $regions)
            ->setCollect('settlements', $settlements)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view(__FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Register $model
     * @return \Illuminate\Routing\Redirector
     */
    public function store(Request $request, Register $model)
    {
        $model = $model->create($request->all());

        return redirect(route("{$this->entity}.edit", $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Register $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Register $model)
    {
        $this->authorize('update', $model);

        $regions = Municipality::whereNull('parent_id')->get();
        $settlements = Municipality::whereNotNull('parent_id')->get();

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleEdit'),
                        'url' => route("{$this->entity}." . __FUNCTION__, $model)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('regions', $regions)
            ->setCollect('settlements', $settlements)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view(__FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Register $model
     * @return \Illuminate\Routing\Redirector
     */
    public function update(Request $request, Register $model)
    {
        $requestData = $request->all();

        $model->update($requestData);

        return redirect(route("{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Register $model
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, Register $model)
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
     * @param Register $model
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, Register $model)
    {
        $this->authorize('restore', $model);

        if ($request->isMethod('GET')) {

            $models = $model->filtering();

            $models = $models
                ->onlyTrashed()
                ->orderBy($model->columnSorting, $model->directionSorting)
                ->paginate($model->totalRecords)
            ;

            $redirectRouteName = __FUNCTION__;

            $models_count = $models->count();

            $this->setCommonData($model);

            $municipalities = Municipality::all()->pluck('name', 'id')->toArray();

            $regionIds = $model->all()->pluck('name_region');
            $regions = Municipality::all()->whereIn('id', $regionIds)->pluck('name', 'id')->toArray();

            $this
                ->setCollect([
                    'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                        [
                            'name' => $this->getCollect('titleRestore'),
                            'url' => route("{$this->entity}." . __FUNCTION__)
                        ],
                    ]),
                ])
                ->setCollect('model', $model)
                ->setCollect('models', $models)
                ->setCollect('models_count', $models_count)
                ->setCollect('redirectRouteName', $redirectRouteName)
                ->setCollect('municipalities', $municipalities)
                ->setCollect('regions', $regions)
                ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

            return view(__FUNCTION__, $this->getCollect());
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

    public function exportTable($type, Register $register)
    {
        $groupRegisters = $register->all()->sortBy('regionName')->groupBy('name_region');

        $spreadsheet = $this->getHeaderTable();
        $activeSheet = $spreadsheet->getActiveSheet();

        $groupKey = 0;
        foreach ($groupRegisters as $regionKey => $registers) {

            $regionName = $registers->first()->regionName;

            foreach ($registers->sortBy('settlementName')->groupBy('name_settlement')->values() as $settlementKey => $settlementNames) {//

                $settlementName = $settlementNames->first()->settlementName;
                $settlementKey++;

                foreach ($settlementNames->sortBy('name_according_charter')->values() as $itemKey => $registerItem) {

                    $i = ($groupKey + 3);
                    $groupKey++;

                    $itemKey++;

                    $activeSheet->getRowDimension($i)->setRowHeight(-1);

                    $activeSheet->setCellValue("A{$i}", $groupKey);
                    $activeSheet->setCellValue("B{$i}", $settlementKey);
                    $activeSheet->setCellValue("C{$i}", $itemKey);
                    $activeSheet->setCellValue("D{$i}", $registerItem->id);
                    $activeSheet->setCellValue("E{$i}", $regionName);
                    $activeSheet->setCellValue("F{$i}", $settlementName);
                    $activeSheet->setCellValue("G{$i}", $registerItem->name_according_charter);
                    $activeSheet->setCellValue("H{$i}", !!$registerItem->membership ? 'Да' : 'Нет');
                    $activeSheet->setCellValue("I{$i}", !!$registerItem->is_legal_entity ? 'Да' : 'Нет');
                    $activeSheet->setCellValue("J{$i}", $registerItem->address);
                    $activeSheet->setCellValue("K{$i}", $registerItem->boundaries);
                    $activeSheet->setCellValue("L{$i}", $registerItem->legal_act);
                    $activeSheet->setCellValue("M{$i}", $registerItem->registration_date_charter?->format('d.m.Y'));
                    $activeSheet->setCellValue("N{$i}", $registerItem->number_members);
                    $activeSheet->setCellValue("O{$i}", $registerItem->number_citizens);
                    $activeSheet->setCellValue("P{$i}", $registerItem->fio_chief);
                    $activeSheet->setCellValue("Q{$i}", $registerItem->email_chief);
                    $activeSheet->setCellValue("R{$i}", $registerItem->phone_chief);
                    $activeSheet->setCellValue("S{$i}", $registerItem->note);
                    $activeSheet->setCellValue("T{$i}", $registerItem->registration_date_tos?->format('d.m.Y'));
                    $activeSheet->setCellValue("U{$i}", $registerItem->nomenclature_number);

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
                        ->duplicateStyle($sharedStyle, "A{$i}:U{$i}");

                    $activeSheet
                        ->getStyle("A{$i}")->getBorders()->getLeft()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));

                    $activeSheet->getStyle("A{$i}:U{$i}")->getAlignment()->setWrapText(true);
                }
            }

        }

        $fileName = 'Реестр ТОС';

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

    public function getHeaderTable()
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.common.app_name', ''))
            ->setLastModifiedBy(config('app.common.app_name', ''))
            ->setTitle('Реестр ТОС')
            ->setSubject('Реестр ТОС')
            ->setDescription('Реестр ТОС')
            ->setKeywords('Реестр ТОС')
            ->setCategory('Реестр ТОС');

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();

        $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $activeSheet->getRowDimension(1)->setRowHeight(30);
        $activeSheet->getColumnDimension('A')->setWidth(9);
        $activeSheet->getColumnDimension('B')->setWidth(7);
        $activeSheet->getColumnDimension('C')->setWidth(9);
        $activeSheet->getColumnDimension('D')->setWidth(9);
        $activeSheet->getColumnDimension('E')->setWidth(15);
        $activeSheet->getColumnDimension('F')->setWidth(15);
        $activeSheet->getColumnDimension('G')->setWidth(20);
        $activeSheet->getColumnDimension('H')->setWidth(6);
        $activeSheet->getColumnDimension('I')->setWidth(6);
        $activeSheet->getColumnDimension('J')->setWidth(20);
        $activeSheet->getColumnDimension('K')->setWidth(25);
        $activeSheet->getColumnDimension('L')->setWidth(20);
        $activeSheet->getColumnDimension('M')->setWidth(9);
        $activeSheet->getColumnDimension('N')->setWidth(7);
        $activeSheet->getColumnDimension('O')->setWidth(7);
        $activeSheet->getColumnDimension('P')->setWidth(15);
        $activeSheet->getColumnDimension('Q')->setWidth(20);
        $activeSheet->getColumnDimension('R')->setWidth(17);
        $activeSheet->getColumnDimension('S')->setWidth(17);
        $activeSheet->getColumnDimension('T')->setWidth(11);
        $activeSheet->getColumnDimension('U')->setWidth(17);

        $headerStyle = new \PhpOffice\PhpSpreadsheet\Style\Style();

        $headerStyle->applyFromArray(
            [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 16,
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

        $activeSheet->mergeCells('A1:U1');
        $activeSheet->getStyle('A1:U1')->getFont()->setName('Arial')->setSize(16);
        $activeSheet
            ->setCellValue('A1', 'Реестр территориальных общественных самоуправлений Республики Карелия  по состоянию на ' . now()->format('d.m.Y') . '.')
        ;

        $activeSheet->getRowDimension(2)->setRowHeight(120);

        $activeSheet->setCellValue('A2', '№ п/п');
        $activeSheet->setCellValue('B2', '№ п/п МР ГО');
        $activeSheet->setCellValue('C2', '№ п/п ТОС');
        $activeSheet->setCellValue('D2', 'ID ТОС');
        $activeSheet->setCellValue('E2', 'Наименование муниципального района городского округа');
        $activeSheet->setCellValue('F2', 'Наименование поселения в составе района');
        $activeSheet->setCellValue('G2', 'Наименование (согласно уставу)');
        $activeSheet->setCellValue('H2', 'Членство в АР ТОС РК');
        $activeSheet->setCellValue('I2', 'Является ли ТОС юридическим лицом (да/нет)');
        $activeSheet->setCellValue('J2', 'Адрес местонахождения ТОС');
        $activeSheet->setCellValue('K2', 'Границы ТОС');
        $activeSheet->setCellValue('L2', 'Муниципальный правовой акт об утверждении устава ТОС (вид документа, дата, номер)');
        $activeSheet->setCellValue('M2', 'дата');
        $activeSheet->setCellValue('N2', 'Кол-во членов ТОС');
        $activeSheet->setCellValue('O2', 'Кол-во граждан, проживающих в границах ТОС');
        $activeSheet->setCellValue('P2', 'ФИО руководителя ТОС');
        $activeSheet->setCellValue('Q2', 'Электронный адрес руководителя ТОС');
        $activeSheet->setCellValue('R2', 'Мобильный телефон руководителя ТОС');
        $activeSheet->setCellValue('S2', 'Примечание');
        $activeSheet->setCellValue('T2', 'Дата внесения в реестр ТОС Республики Карелия');
        $activeSheet->setCellValue('U2', 'Номенклатурный номер ТОС');

        $activeSheet
            ->duplicateStyle($headerStyle, 'A1:U1')
        ;

        $activeSheet
            ->getStyle('A1:U1')->getBorders()->getTop()
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

        $activeSheet->getStyle('A2:U2')->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('A2:U2')->getFont()->setName('Times New Roman')->setSize(11)->setBold(700);
        $activeSheet->getStyle('A2:U2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        $activeSheet->getStyle('A2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('A2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('B2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('B2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('C2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('C2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('D2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('D2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('E2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('E2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('F2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('F2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('G2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('G2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('H2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('H2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('I2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('I2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('J2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('J2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('K2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('K2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('L2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('L2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('M2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('M2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('N2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('N2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('O2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('O2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('P2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('P2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('Q2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('Q2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('R2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('R2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('S2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('S2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('T2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('T2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('U2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $activeSheet->getStyle('U2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return $spreadsheet;
    }
}
