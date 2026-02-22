<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Image;
use App\Models\LTOSApplication;
use App\Models\Municipality;
use App\Models\PPMIApplication;
use App\Models\Register;
use App\Models\User;
use App\Services\MPDFService;
use App\Traits\CalculationLTOSApplicationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LTOSApplicationController extends CommonController
{
    use CalculationLTOSApplicationTrait;

    protected $entity = 'ltos';

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
     * @param LTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(LTOSApplication $model)
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

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('contests', $contests)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('registers', $registers)
            ->setCollect('users', $users)
            ->setCollect('statuses', $statuses)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param LTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(LTOSApplication $model)
    {
        $this->authorize('create', $model);

        $contest = $this->getContest($model, 'ltos');
        $user = auth()->user();
        $municipalities = $user->municipalitiesList;

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
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LTOSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, LTOSApplication $model)
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

        if ($model->status === 'published') {

            $totalApplicationPoints = $this->getCalculation($model);

            $model->total_application_points = $totalApplicationPoints;
            $model->save();
        }

        return redirect(route("applications.{$this->entity}.edit", $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(LTOSApplication $model)
    {
        $this->authorize('update', $model);

        if (!auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']) && ((int) $model->user_id !== (int) auth()->id())) {

            return abort(404);
        }

        $contest = $this->getContest($model, 'ltos');
        $user = $model->user;
        $municipalities = $user->municipalitiesList;

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
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LTOSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LTOSApplication $model)
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

        if ($model->status === 'published') {

            $totalApplicationPoints = $this->getCalculation($model);

            $model->total_application_points = $totalApplicationPoints;
            $model->save();
        }

        if(isset($requestData['images'])) {
            $ids = array_keys($requestData['images']);
            $images = Image::whereIn('id', $ids)->get();
            foreach ($images as $image) {
                $image->description = $requestData['images']["{$image->id}"];
                $image->update();
            }
        }

        return redirect(route("applications.{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param LTOSApplication $model
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, LTOSApplication $model)
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
     * @param LTOSApplication $model
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, LTOSApplication $model)
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

            $this->setCommonDataApplication($model);

            $contests = $this->getApplicationContests();
            $municipalities = $this->getApplicationMunicipalities();
            $users = $this->getApplicationUsers();
            $statuses = $this->getApplicationStatuses();
            $registers = $this->getApplicationRegisters();

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
                ->setCollect('redirectRouteName', $redirectRouteName)
                ->setCollect('contests', $contests)
                ->setCollect('municipalities', $municipalities)
                ->setCollect('registers', $registers)
                ->setCollect('users', $users)
                ->setCollect('statuses', $statuses)
                ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

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

    public function prepareForValidation(Request $request)
    {
        $requestData = $request->all();

        $requestData['status'] = $request->filled('draft') ? 'draft' : 'published';

        $request->merge($requestData);
    }

    public function getDraftRules()
    {
        return [
            'user_id'                               => 'required',  // Участник
            'contest_id'                            => 'required',  // Конкурс
            'municipality_id'                       => 'required',  // Наименование муниципального образования
            'status'                                => '',          // Статус заявки
            'register_id'                           => '',          // Полное наименование ТОС
            'region_id'                             => '',          // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                         => '',          // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'             => '',          // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'nomenclature_number'                   => '',          // Номенклатурный номер ТОС
            'is_tos_legal_entity'                   => '',          // Является ли ТОС юридическим лицом
            'full_name_chairman_tos'                => '',          // ФИО председателя ТОС
            'tos_address'                           => '',          // Почтовый адрес (с указанием индекса)
            'tos_phone'                             => '',          // Номер мобильного телефона
            'tos_email'                             => '',          // Адрес электронной почты
            'population_size_in_tos'                => '',          // Количество зарегистрированных граждан в ТОС
            'date_filling_in'                       => '',          // Дата заполнения
            'total_application_points'                      => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'points_from_administrator'                     => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'comment_on_points_from_administrator'  => '',          // Комментарий к баллам от администратора
        ];
    }

    public function getPublishedRules()
    {
        return [
            'user_id'                               => 'required',  // Участник
            'contest_id'                            => 'required',  // Конкурс
            'municipality_id'                       => 'required',  // Наименование муниципального образования
            'status'                                => '',          // Статус заявки
            'register_id'                           => '',          // Полное наименование ТОС
            'region_id'                             => '',          // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                         => '',          // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'             => '',          // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'nomenclature_number'                   => '',          // Номенклатурный номер ТОС
            'is_tos_legal_entity'                   => '',          // Является ли ТОС юридическим лицом
            'full_name_chairman_tos'                => '',          // ФИО председателя ТОС
            'tos_address'                           => '',          // Почтовый адрес (с указанием индекса)
            'tos_phone'                             => '',          // Номер мобильного телефона
            'tos_email'                             => '',          // Адрес электронной почты
            'population_size_in_tos'                => '',          // Количество зарегистрированных граждан в ТОС
            'date_filling_in'                       => '',          // Дата заполнения
            'total_application_points'                      => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'points_from_administrator'                     => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'comment_on_points_from_administrator'  => '',          // Комментарий к баллам от администратора
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
            'municipality_id' => 'Наименование муниципального образования',
            'status' => 'Статус заявки',
            'register_id' => 'Полное наименование ТОС',
            'region_id' => 'Наименование (муниципального района/городского округа), где реализуется проект',
            'settlement_id' => 'Наименование поселения в составе района, где реализуется проект',
            'date_registration_charter' => 'Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)',
            'nomenclature_number' => 'Номенклатурный номер ТОС',
            'is_tos_legal_entity' => 'Является ли ТОС юридическим лицом',
            'full_name_chairman_tos' => 'ФИО председателя ТОС',
            'tos_address' => 'Почтовый адрес (с указанием индекса)',
            'tos_phone' => 'Номер мобильного телефона',
            'tos_email' => 'Адрес электронной почты',
            'population_size_in_tos' => 'Количество зарегистрированных граждан в ТОС',
            'date_filling_in' => 'Дата заполнения',
            'total_application_points' => 'Общие баллы по заявке',
            'points_from_administrator' => 'Баллы от администратора',
            'comment_on_points_from_administrator' => 'Комментарий к баллам от администратора',
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

    public function saveMatrix($model, $requestData)
    {
        // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
        $model
            ->list_tos_board_members()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['list_tos_board_members']['field19'] as $key => $start) {

            $data = [
                'group' => 'list_tos_board_members',
                'user_id' => $model->user_id ?? auth()->id(),
                'field19' => isset($requestData['list_tos_board_members']['field19'][$key]) ? $requestData['list_tos_board_members']['field19'][$key] : null,
                'field20' => isset($requestData['list_tos_board_members']['field20'][$key]) ? $requestData['list_tos_board_members']['field20'][$key] : null,
                'field21' => isset($requestData['list_tos_board_members']['field21'][$key]) ? $requestData['list_tos_board_members']['field21'][$key] : null,
            ];

            $model
                ->list_tos_board_members()
                ->create($data)
            ;
        }

        // Организация культурно-массовых мероприятий, праздников, иных культурно-просветительных акций (не более 1 страницы)
        $model
            ->organization_cultural_events()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['organization_cultural_events']['field22'] as $key => $start) {

            $data = [
                'group' => 'organization_cultural_events',
                'user_id' => $model->user_id ?? auth()->id(),
                'field22' => isset($requestData['organization_cultural_events']['field22'][$key]) ? $requestData['organization_cultural_events']['field22'][$key] : null,
                'field23' => isset($requestData['organization_cultural_events']['field23'][$key]) ? $requestData['organization_cultural_events']['field23'][$key] : null,
                'field24' => isset($requestData['organization_cultural_events']['field24'][$key]) ? $requestData['organization_cultural_events']['field24'][$key] : null,
            ];

            $model
                ->organization_cultural_events()
                ->create($data)
            ;
        }

        // Проведение спортивных соревнований, гражданско-патриотических игр, туристических выездов (не более 1 страницы)
        $model
            ->conducting_sports_competitions()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['conducting_sports_competitions']['field25'] as $key => $start) {

            $data = [
                'group' => 'conducting_sports_competitions',
                'user_id' => $model->user_id ?? auth()->id(),
                'field25' => isset($requestData['conducting_sports_competitions']['field25'][$key]) ? $requestData['conducting_sports_competitions']['field25'][$key] : null,
                'field26' => isset($requestData['conducting_sports_competitions']['field26'][$key]) ? $requestData['conducting_sports_competitions']['field26'][$key] : null,
                'field27' => isset($requestData['conducting_sports_competitions']['field27'][$key]) ? $requestData['conducting_sports_competitions']['field27'][$key] : null,
            ];

            $model
                ->conducting_sports_competitions()
                ->create($data)
            ;
        }

        // Проведение мероприятий, направленных на профилактику наркомании, алкоголизма и формирование здорового образа жизни (не более 1 страницы)
        $model
            ->drug_addiction_prevention_measures()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['drug_addiction_prevention_measures']['field28'] as $key => $start) {

            $data = [
                'group' => 'drug_addiction_prevention_measures',
                'user_id' => $model->user_id ?? auth()->id(),
                'field28' => isset($requestData['drug_addiction_prevention_measures']['field28'][$key]) ? $requestData['drug_addiction_prevention_measures']['field28'][$key] : null,
                'field29' => isset($requestData['drug_addiction_prevention_measures']['field29'][$key]) ? $requestData['drug_addiction_prevention_measures']['field29'][$key] : null,
                'field30' => isset($requestData['drug_addiction_prevention_measures']['field30'][$key]) ? $requestData['drug_addiction_prevention_measures']['field30'][$key] : null,
            ];

            $model
                ->drug_addiction_prevention_measures()
                ->create($data)
            ;
        }

        // Наличие клубов, секций кружков, организованных при ТОС (не более 1 страницы)
        $model
            ->availability_clubs()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['availability_clubs']['field31'] as $key => $start) {

            $data = [
                'group' => 'availability_clubs',
                'user_id' => $model->user_id ?? auth()->id(),
                'field31' => isset($requestData['availability_clubs']['field31'][$key]) ? $requestData['availability_clubs']['field31'][$key] : null,
                'field32' => isset($requestData['availability_clubs']['field32'][$key]) ? $requestData['availability_clubs']['field32'][$key] : null,
            ];

            $model
                ->availability_clubs()
                ->create($data)
            ;
        }

        // Проведение мероприятий по организации благоустройства и улучшения санитарного состояния территории ТОС (не более 1 страницы)
        $model
            ->measures_organization_landscaping()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['measures_organization_landscaping']['field33'] as $key => $start) {

            $data = [
                'group' => 'measures_organization_landscaping',
                'user_id' => $model->user_id ?? auth()->id(),
                'field33' => isset($requestData['measures_organization_landscaping']['field33'][$key]) ? $requestData['measures_organization_landscaping']['field33'][$key] : null,
                'field34' => isset($requestData['measures_organization_landscaping']['field34'][$key]) ? $requestData['measures_organization_landscaping']['field34'][$key] : null,
            ];

            $model
                ->measures_organization_landscaping()
                ->create($data)
            ;
        }

        // Количество объектов социальной направленности, восстановленных, отремонтированных или построенных силами ТОС (не более 1 страницы)
        $model
            ->number_objects_social_orientation()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['number_objects_social_orientation']['field35'] as $key => $start) {

            $data = [
                'group' => 'number_objects_social_orientation',
                'user_id' => $model->user_id ?? auth()->id(),
                'field35' => isset($requestData['number_objects_social_orientation']['field35'][$key]) ? $requestData['number_objects_social_orientation']['field35'][$key] : null,
                'field36' => isset($requestData['number_objects_social_orientation']['field36'][$key]) ? $requestData['number_objects_social_orientation']['field36'][$key] : null,
                'field37' => isset($requestData['number_objects_social_orientation']['field37'][$key]) ? $requestData['number_objects_social_orientation']['field37'][$key] : null,
            ];

            $model
                ->number_objects_social_orientation()
                ->create($data)
            ;
        }

        // Оказание помощи многодетным семьям, инвалидам, одиноким пенсионерам, малоимущим гражданам (не более 1 страницы)
        $model
            ->providing_assistance()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['providing_assistance']['field38'] as $key => $start) {

            $data = [
                'group' => 'providing_assistance',
                'user_id' => $model->user_id ?? auth()->id(),
                'field38' => isset($requestData['providing_assistance']['field38'][$key]) ? $requestData['providing_assistance']['field38'][$key] : null,
                'field39' => isset($requestData['providing_assistance']['field39'][$key]) ? $requestData['providing_assistance']['field39'][$key] : null,
            ];

            $model
                ->providing_assistance()
                ->create($data)
            ;
        }

        // Создание на территории ТОС уголка здорового образа жизни, разработка буклетов, выпуск стенгазет по пропаганде здорового образа жизни (не более 1 страницы)
        $model
            ->healthy_lifestyle_corner()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['healthy_lifestyle_corner']['field40'] as $key => $start) {

            $data = [
                'group' => 'healthy_lifestyle_corner',
                'user_id' => $model->user_id ?? auth()->id(),
                'field40' => isset($requestData['healthy_lifestyle_corner']['field40'][$key]) ? $requestData['healthy_lifestyle_corner']['field40'][$key] : null,
                'field41' => isset($requestData['healthy_lifestyle_corner']['field41'][$key]) ? $requestData['healthy_lifestyle_corner']['field41'][$key] : null,
            ];

            $model
                ->healthy_lifestyle_corner()
                ->create($data)
            ;
        }

        // Участие членов ТОС в совместных с сотрудниками полиции профилактических мероприятиях, связанных с профилактикой преступлений и иных правонарушений (не более 1 страницы)
        $model
            ->joint_preventive_measures()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['joint_preventive_measures']['field42'] as $key => $start) {

            $data = [
                'group' => 'joint_preventive_measures',
                'user_id' => $model->user_id ?? auth()->id(),
                'field42' => isset($requestData['joint_preventive_measures']['field42'][$key]) ? $requestData['joint_preventive_measures']['field42'][$key] : null,
                'field43' => isset($requestData['joint_preventive_measures']['field43'][$key]) ? $requestData['joint_preventive_measures']['field43'][$key] : null,
                'field44' => isset($requestData['joint_preventive_measures']['field44'][$key]) ? $requestData['joint_preventive_measures']['field44'][$key] : null,
            ];

            $model
                ->joint_preventive_measures()
                ->create($data)
            ;
        }

        // Проведение мероприятий по профилактике пожаров (не более 1 страницы)
        $model
            ->fire_prevention()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['fire_prevention']['field45'] as $key => $start) {

            $data = [
                'group' => 'fire_prevention',
                'user_id' => $model->user_id ?? auth()->id(),
                'field45' => isset($requestData['fire_prevention']['field45'][$key]) ? $requestData['fire_prevention']['field45'][$key] : null,
                'field46' => isset($requestData['fire_prevention']['field46'][$key]) ? $requestData['fire_prevention']['field46'][$key] : null,
                'field47' => isset($requestData['fire_prevention']['field47'][$key]) ? $requestData['fire_prevention']['field47'][$key] : null,
            ];

            $model
                ->fire_prevention()
                ->create($data)
            ;
        }

        // Проведение ТОСами совещаний и семинаров с участием органов местного самоуправления (не более 0,5 страницы)
        $model
            ->meetings_and_seminars()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['meetings_and_seminars']['field48'] as $key => $start) {

            $data = [
                'group' => 'meetings_and_seminars',
                'user_id' => $model->user_id ?? auth()->id(),
                'field48' => isset($requestData['meetings_and_seminars']['field48'][$key]) ? $requestData['meetings_and_seminars']['field48'][$key] : null,
                'field49' => isset($requestData['meetings_and_seminars']['field49'][$key]) ? $requestData['meetings_and_seminars']['field49'][$key] : null,
                'field50' => isset($requestData['meetings_and_seminars']['field50'][$key]) ? $requestData['meetings_and_seminars']['field50'][$key] : null,
            ];

            $model
                ->meetings_and_seminars()
                ->create($data)
            ;
        }

        // Размещение информации в средствах массовой информации и в информационно-телекоммуникационной сети Интернет о деятельности ТОС по каждому направлению деятельности
        $model
            ->placement_information_in_mass_media()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['placement_information_in_mass_media']['field51'] as $key => $start) {

            $data = [
                'group' => 'placement_information_in_mass_media',
                'user_id' => $model->user_id ?? auth()->id(),
                'field51' => isset($requestData['placement_information_in_mass_media']['field51'][$key]) ? $requestData['placement_information_in_mass_media']['field51'][$key] : null,
                'field52' => isset($requestData['placement_information_in_mass_media']['field52'][$key]) ? $requestData['placement_information_in_mass_media']['field52'][$key] : null,
                'field53' => isset($requestData['placement_information_in_mass_media']['field53'][$key]) ? $requestData['placement_information_in_mass_media']['field53'][$key] : null,
            ];

            $model
                ->placement_information_in_mass_media()
                ->create($data)
            ;
        }

        // Участие ТОС в конкурсах за предыдущие три года (неудачное)
        $model
            ->participation_in_previous_contests_unsuccessful()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['participation_in_previous_contests_unsuccessful']['field54'] as $key => $start) {

            $data = [
                'group' => 'participation_in_previous_contests_unsuccessful',
                'user_id' => $model->user_id ?? auth()->id(),
                'field54' => isset($requestData['participation_in_previous_contests_unsuccessful']['field54'][$key]) ? $requestData['participation_in_previous_contests_unsuccessful']['field54'][$key] : null,
                'field55' => isset($requestData['participation_in_previous_contests_unsuccessful']['field55'][$key]) ? $requestData['participation_in_previous_contests_unsuccessful']['field55'][$key] : null,
            ];

            $model
                ->participation_in_previous_contests_unsuccessful()
                ->create($data)
            ;
        }

        // Участие ТОС в конкурсах за предыдущие три года (удачное)
        $model
            ->participation_in_previous_contests_successful()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['participation_in_previous_contests_successful']['field56'] as $key => $start) {

            $data = [
                'group' => 'participation_in_previous_contests_successful',
                'user_id' => $model->user_id ?? auth()->id(),
                'field56' => isset($requestData['participation_in_previous_contests_successful']['field56'][$key]) ? $requestData['participation_in_previous_contests_successful']['field56'][$key] : null,
                'field57' => isset($requestData['participation_in_previous_contests_successful']['field57'][$key]) ? $requestData['participation_in_previous_contests_successful']['field57'][$key] : null,
            ];

            $model
                ->participation_in_previous_contests_successful()
                ->create($data)
            ;
        }

        // Награды ТОС и членов ТОС за тосовскую деятельность (за последние три года)
        $model
            ->awards()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['awards']['field58'] as $key => $start) {

            $data = [
                'group' => 'awards',
                'user_id' => $model->user_id ?? auth()->id(),
                'field58' => isset($requestData['awards']['field58'][$key]) ? $requestData['awards']['field58'][$key] : null,
                'field59' => isset($requestData['awards']['field59'][$key]) ? $requestData['awards']['field59'][$key] : null,
            ];

            $model
                ->awards()
                ->create($data)
            ;
        }
    }

    public function exportApplication($type, LTOSApplication $application)
    {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path("templates/application_ltos_template.docx"));

        $templateProcessor->setValue("tosName", $application->tosName);
        $templateProcessor->setValue("municipalityName", $application->municipalityName);
        $templateProcessor->setValue("dateRegistrationCharter", $application->date_registration_charter);
        $templateProcessor->setValue("nomenclatureNumber", $application->nomenclature_number);
        $templateProcessor->setValue("fullNameChairmanTos", $application->full_name_chairman_tos);
        $templateProcessor->setValue("tosAddress", $application->tos_address);
        $templateProcessor->setValue("tosPhone", $application->tos_phone);
        $templateProcessor->setValue("tosEmail", $application->tos_email);

        $listLosBoardMembers = [];
        foreach ($application->list_tos_board_members as $key => $list_tos_board_member) {

            $listLosBoardMembers[] = [
                'field19' => $this->utf8ForXml($list_tos_board_member->field19),
                'field20' => $this->utf8ForXml($list_tos_board_member->field20),
                'field21' => $this->utf8ForXml($list_tos_board_member->field21),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field19', $listLosBoardMembers);

        $templateProcessor->setValue("populationSizeInTos", $application->population_size_in_tos);
        $templateProcessor->setValue("dateFillingIn", $application->date_filling_in);

        $organizationCulturalEvents = [];
        foreach ($application->organization_cultural_events as $key => $organization_cultural_event) {

            $organizationCulturalEvents[] = [
                'field22' => $this->utf8ForXml($organization_cultural_event->field22),
                'field23' => $this->utf8ForXml($organization_cultural_event->field23),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field22', $organizationCulturalEvents);

        $conductingSportsCompetitions = [];
        foreach ($application->conducting_sports_competitions as $key => $conducting_sports_competition) {

            $conductingSportsCompetitions[] = [
                'field25' => $this->utf8ForXml($conducting_sports_competition->field25),
                'field26' => $this->utf8ForXml($conducting_sports_competition->field26),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field25', $conductingSportsCompetitions);

        $drugAddictionPreventionMeasures = [];
        foreach ($application->drug_addiction_prevention_measures as $key => $drug_addiction_prevention_measure) {

            $drugAddictionPreventionMeasures[] = [
                'field28' => $this->utf8ForXml($drug_addiction_prevention_measure->field28),
                'field29' => $this->utf8ForXml($drug_addiction_prevention_measure->field29),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field28', $drugAddictionPreventionMeasures);

        $availabilityClubs = [];
        foreach ($application->availability_clubs as $key => $availability_club) {

            $availabilityClubs[] = [
                'field31' => $this->utf8ForXml($availability_club->field31),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field31', $availabilityClubs);

        $measuresOrganizationLandscaping = [];
        foreach ($application->measures_organization_landscaping as $key => $measures_organization_landscaping) {

            $measuresOrganizationLandscaping[] = [
                'field33' => $this->utf8ForXml($measures_organization_landscaping->field33),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field33', $measuresOrganizationLandscaping);

        $numberObjectsSocialOrientation = [];
        foreach ($application->number_objects_social_orientation as $key => $number_objects_social_orientation) {

            $numberObjectsSocialOrientation[] = [
                'field35' => $this->utf8ForXml($number_objects_social_orientation->field35),
                'field36' => $this->utf8ForXml($number_objects_social_orientation->field36),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field35', $numberObjectsSocialOrientation);

        $providingAssistance = [];
        foreach ($application->providing_assistance as $key => $providing_assistance) {

            $providingAssistance[] = [
                'field38' => $this->utf8ForXml($providing_assistance->field38),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field38', $providingAssistance);

        $healthyLifestyleCorner = [];
        foreach ($application->healthy_lifestyle_corner as $key => $healthy_lifestyle_corner) {

            $healthyLifestyleCorner[] = [
                'field40' => $this->utf8ForXml($healthy_lifestyle_corner->field40),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field40', $healthyLifestyleCorner);

        $jointPreventiveMeasures = [];
        foreach ($application->joint_preventive_measures as $key => $joint_preventive_measures) {

            $jointPreventiveMeasures[] = [
                'field42' => $this->utf8ForXml($joint_preventive_measures->field42),
                'field43' => $this->utf8ForXml($joint_preventive_measures->field43),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field42', $jointPreventiveMeasures);

        $firePrevention = [];
        foreach ($application->fire_prevention as $key => $fire_prevention) {

            $firePrevention[] = [
                'field45' => $this->utf8ForXml($fire_prevention->field45),
                'field46' => $this->utf8ForXml($fire_prevention->field46),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field45', $firePrevention);

        $meetingsAndSeminars = [];
        foreach ($application->meetings_and_seminars as $key => $meetings_and_seminars) {

            $meetingsAndSeminars[] = [
                'field48' => $this->utf8ForXml($meetings_and_seminars->field48),
                'field49' => $this->utf8ForXml($meetings_and_seminars->field49),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field48', $meetingsAndSeminars);

        $placementInformationInMassMedia = [];
        foreach ($application->placement_information_in_mass_media as $key => $placement_information_in_mass_media) {

            $placementInformationInMassMedia[] = [
                'field51' => $this->utf8ForXml($placement_information_in_mass_media->field51),
                'field52' => $this->utf8ForXml($placement_information_in_mass_media->field52),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field51', $placementInformationInMassMedia);

        $participationInPreviousContestsUnsuccessful = [];
        foreach ($application->participation_in_previous_contests_unsuccessful as $key => $participation_in_previous_contests_unsuccessful) {

            $participationInPreviousContestsUnsuccessful[] = [
                'field54' => $this->utf8ForXml($participation_in_previous_contests_unsuccessful->field54),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field54', $participationInPreviousContestsUnsuccessful);

        $participationInPreviousContestsSuccessful = [];
        foreach ($application->participation_in_previous_contests_successful as $key => $participation_in_previous_contests_successful) {

            $participationInPreviousContestsSuccessful[] = [
                'field56' => $this->utf8ForXml($participation_in_previous_contests_successful->field56),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field56', $participationInPreviousContestsSuccessful);

        $awards = [];
        foreach ($application->awards as $key => $award) {

            $awards[] = [
                'field58' => $this->utf8ForXml($award->field58),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('field58', $awards);

        $path = storage_path("app/applications/ltos/{$application->id}");

        if (!file_exists($path)) {

            File::makeDirectory($path, 0777, true);
        } else {

            File::chmod($path, 0777);
        }

        if ($type === 'pdf') {
            $templateProcessor->saveAs(storage_path("app/applications/ltos/{$application->id}/Заявка#{$application->id}.docx"));
            $phpWord = \PhpOffice\PhpWord\IOFactory::load(storage_path("app/applications/ltos/{$application->id}/Заявка#{$application->id}.docx"));

            $mpdfWriter = new MPDFService($phpWord);

            // Redirect output to a client’s web browser (PDF)
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="Заявка#' . $application->id . '.pdf"');
            header('Cache-Control: max-age=0');

            $mpdfWriter->save('php://output');
        } else {

            // Redirect output to a client’s web browser (docx)
            header("Content-Description: File Transfer");
            header('Content-Disposition: attachment; filename="Заявка#' . $application->id . '.docx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
//
//            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $templateProcessor->saveAs('php://output');
        }
        exit;
    }

    public function exportTable($type, LTOSApplication $application)
    {
        $applications = $application
            ->where('status', 'published')
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

            $activeSheet->setCellValue("A{$i}", $key);
            $activeSheet->setCellValue("B{$i}", $applicationItem->municipalityUserName);
            $activeSheet->setCellValue("C{$i}", $applicationItem->municipalityName);
            $activeSheet->setCellValue("D{$i}", $applicationItem->tosName);
            $activeSheet->setCellValue("E{$i}", $applicationItem->total_application_points);

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
                ->duplicateStyle($sharedStyle, "A{$i}:E{$i}")
            ;

            $activeSheet
                ->getStyle("A{$i}")->getBorders()->getLeft()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
            ;

            $activeSheet->getStyle("A{$i}:E{$i}")->getAlignment()->setWrapText(true);
        }

        $fileName = 'Рейтинговая таблица проектов';

        if ($type === 'pdf') {
            \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);

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
            ->setDescription('Рейтинговая таблица по итогам подачи заявок на конкурс «ЛУЧШЕЕ ТОС» в Республике Карелия в 2022 году.')
            ->setKeywords('Рейтинговая таблица проектов')
            ->setCategory('Рейтинговая таблица проектов');

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();

        $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $activeSheet->getRowDimension(1)->setRowHeight(30);
        $activeSheet->getColumnDimension('A')->setWidth(10);
        $activeSheet->getColumnDimension('B')->setWidth(60);
        $activeSheet->getColumnDimension('C')->setWidth(60);
        $activeSheet->getColumnDimension('D')->setWidth(35);
        $activeSheet->getColumnDimension('E')->setWidth(35);

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

        $activeSheet->mergeCells('A1:E1');
        $activeSheet->getStyle('A1:E1')->getFont()->setName('Arial')->setSize(16);
        $activeSheet
            ->setCellValue('A1', 'Рейтинговая таблица по итогам подачи заявок на конкурс «ЛУЧШЕЕ ТОС» в Республике Карелия в 2022 году.')
        ;

        $activeSheet->getRowDimension(2)->setRowHeight(60);

        $activeSheet->setCellValue('A2', '№ п/п');
        $activeSheet->setCellValue('B2', 'Район');
        $activeSheet->setCellValue('C2', 'Поселение');
        $activeSheet->setCellValue('D2', 'Название ТОС');
        $activeSheet->setCellValue('E2', 'Итоговый балл');

        $activeSheet
            ->duplicateStyle($headerStyle, 'A1:E2')
        ;

        $activeSheet
            ->getStyle('A1:E1')->getBorders()->getTop()
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

        $activeSheet->getStyle('A2:E2')->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('A2:E2')->getFont()->setSize(11);

        return $spreadsheet;
    }
}
