<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\SZPTOSApplication;
use App\Services\MPDFService;
use App\Traits\CalculationSZPTOSApplicationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use File;

class SZPTOSApplicationController extends CommonController
{
    use CalculationSZPTOSApplicationTrait;

    protected $entity = 'szptos';

    public $valuesArray = [1,2,3,4,5,6,7,8,9,10];

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
                ->setCollect('matrixKeysShow', collect([0, 1, 2]))
                ->setCollect('years', $this->years('szptos'))
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
     * @param SZPTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(SZPTOSApplication $model)
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
     * @param SZPTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(SZPTOSApplication $model)
    {
        $this->authorize('create', $model);

        $contest = $this->getContest($model, 'szptos');
        $user = auth()->user();
        $municipalities = $user->municipalitiesList;
        $projectDirections = $this->getProjectDirections();

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
            ->setCollect('projectDirections', $projectDirections)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param SZPTOSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, SZPTOSApplication $model)
    {
        $this->prepareForValidation($request);

        $requestData = $request->all();

        if ($requestData['status'] === 'published') {

            $rules = $this->getPublishedRules();
        } else {

            $rules = $this->getDraftRules();
        }

        if ($request->has('nomenclature_number')) {
            $rules = array_merge($rules, ['nomenclature_number' =>['required', 'min:18', 'max:20']]);
        }

        $validator = Validator::make($requestData, $rules, $this->getMessages(), $this->getAttributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model = $model->create($request->all());

        $this->saveMatrix($model, $requestData);

        if ($model->status === 'published') {

            $totalApplicationPoints = $this->getCalculation($model);

            $model->total_application_points = $totalApplicationPoints;
            $model->save();
        }

        $model?->register?->update($request->only($model?->register?->getFillable()));

        return redirect(route("applications.{$this->entity}.edit", $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SZPTOSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(SZPTOSApplication $model)
    {
        $this->authorize('update', $model);

        if (!auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']) && ((int) $model->user_id !== (int) auth()->id())) {

            return abort(404);
        }

        $contest = $this->getContest($model, 'szptos');
        $user = $model->user;
        $municipalities = $user->municipalitiesList;
        $projectDirections = $this->getProjectDirections();

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
            ->setCollect('projectDirections', $projectDirections)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param SZPTOSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SZPTOSApplication $model)
    {
        $this->prepareForValidation($request);

        $requestData = $request->all();

        if ($requestData['status'] === 'published') {

            $rules = $this->getPublishedRules();
        } else {

            $rules = $this->getDraftRules();
        }

        if ($request->has('nomenclature_number')) {
            $rules = array_merge($rules, ['nomenclature_number' =>['required', 'min:18', 'max:20']]);
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

        $model?->register?->update($request->only($model?->register?->getFillable()));

        return redirect(route("applications.{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param SZPTOSApplication $model
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, SZPTOSApplication $model)
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
     * @param SZPTOSApplication $model
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, SZPTOSApplication $model)
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

        $requestData['extra_budgetary_sources'] = $requestData['funds_tos'] + $requestData['funds_legal_entities'];
        $requestData['funds_raised'] = $requestData['extra_budgetary_sources'] + $requestData['funds_local_budget'];
        $requestData['total_cost_project'] = $requestData['budget_funds_republic'] + $requestData['funds_raised'];

        $request->merge($requestData);
    }

    public function getDraftRules()
    {
        return [
            'user_id'                                   => 'required',                                                      // Участник
            'contest_id'                                => 'required',                                                      // Конкурс
            'municipality_id'                           => 'required',                                                      // Наименование муниципального образования
            'register_id'                               => '',                                                              // Наименование ТОС
            'region_id'                                 => '',                                                              // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                             => '',                                                              // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'                 => '',                                                              // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'is_tos_legal_entity'                       => '',                                                              // Является ли ТОС юридическим лицом
            'nomenclature_number'                       => '',                                                              // Номенклатурный номер ТОС
            'full_name_chairman_tos'                    => '',                                                              // ФИО председателя ТОС
            'tos_address'                               => '',                                                              // Почтовый адрес (с указанием индекса)
            'tos_phone'                                 => '',                                                              // Номер мобильного телефона
            'tos_email'                                 => '',                                                              // Адрес электронной почты
            'population_size_settlement'                => '',                                                              // Численность населения
            'population_size_in_tos'                    => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество жителей, проживающих в границах ТОС
            'project_name'                              => 'required',                                                      // Наименование проекта
            'project_direction'                         => '',                                                              // Направление проекта
            'problem_description'                       => '',                                                              // Описание актуальности проблемы, на решение которой направлен проект
            'project_purpose'                           => '',                                                              // Цель проекта
            'project_tasks'                             => '',                                                              // Задачи проекта
            'duration_practice_start'                   => 'required',                                                      // Дата начала реализации проекта
            'duration_practice_end'                     => 'required',                                                      // Дата окончания реализации проекта
            'results_project_implementation'            => '',                                                              // Ожидаемые результаты реализации проекта
            'number_beneficiaries'                      => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество человек (благополучателей), которые будут пользоваться результатами проекта
            'description_need'                          => '',                                                              // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
            'total_cost_project'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общая стоимость проекта
            'budget_funds_republic'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства бюджета Республики Карелия
            'funds_raised'                              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Привлеченные средства
            'extra_budgetary_sources'                   => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Внебюджетные источники
            'funds_tos'                                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства ТОС
            'funds_legal_entities'                      => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства юридических лиц
            'funds_local_budget'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства местного бюджета
            'person_responsible_implementation_project' => '',                                                              // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)
            'number_present_at_general_meeting'         => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество присутствующих на общем собрании членов ТОС
            'is_grand_opening_with_media_coverage'      => '',                                                              // По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»
            'date_filling_in'                           => '',                                                              // Дата заполнения заявки
            'total_application_points'                  => ['regex:/^-?([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общие баллы по заявке
            'points_from_administrator'                 => ['regex:/^-?([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Баллы от администратора
            'comment_on_points_from_administrator'      => '',                                                              // Комментарий к баллам от администратора
            'status'                                    => '',                                                              // Статус заявки
        ];
    }

    public function getPublishedRules()
    {
        return [
            'user_id'                                   => 'required',                                                      // Участник
            'contest_id'                                => 'required',                                                      // Конкурс
            'municipality_id'                           => 'required',                                                      // Наименование муниципального образования
            'register_id'                               => '',                                                              // Наименование ТОС
            'region_id'                                 => '',                                                              // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                             => '',                                                              // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'                 => '',                                                              // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'is_tos_legal_entity'                       => '',                                                              // Является ли ТОС юридическим лицом
            'nomenclature_number'                       => '',                                                              // Номенклатурный номер ТОС
            'full_name_chairman_tos'                    => '',                                                              // ФИО председателя ТОС
            'tos_address'                               => '',                                                              // Почтовый адрес (с указанием индекса)
            'tos_phone'                                 => '',                                                              // Номер мобильного телефона
            'tos_email'                                 => '',                                                              // Адрес электронной почты
            'population_size_settlement'                => ['regex:/^([0-9]{0,11})?$/'],                                                              // Численность населения
            'population_size_in_tos'                    => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество жителей, проживающих в границах ТОС
            'project_name'                              => 'required',                                                      // Наименование проекта
            'project_direction'                         => 'required',                                                      // Направление проекта
            'problem_description'                       => '',                                                              // Описание актуальности проблемы, на решение которой направлен проект
            'project_purpose'                           => '',                                                              // Цель проекта
            'project_tasks'                             => '',                                                              // Задачи проекта
            'duration_practice_start'                   => 'required',                                                      // Дата начала реализации проекта
            'duration_practice_end'                     => 'required',                                                      // Дата окончания реализации проекта
            'results_project_implementation'            => '',                                                              // Ожидаемые результаты реализации проекта
            'number_beneficiaries'                      => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество человек (благополучателей), которые будут пользоваться результатами проекта
            'description_need'                          => '',                                                              // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
            'total_cost_project'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общая стоимость проекта
            'budget_funds_republic'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства бюджета Республики Карелия
            'funds_raised'                              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Привлеченные средства
            'extra_budgetary_sources'                   => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Внебюджетные источники
            'funds_tos'                                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства ТОС
            'funds_legal_entities'                      => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства юридических лиц
            'funds_local_budget'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства местного бюджета
            'person_responsible_implementation_project' => '',                                                              // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)
            'number_present_at_general_meeting'         => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество присутствующих на общем собрании членов ТОС
            'is_grand_opening_with_media_coverage'      => '',                                                              // По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»
            'date_filling_in'                           => '',                                                              // Дата заполнения заявки
            'total_application_points'                  => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общие баллы по заявке
            'points_from_administrator'                 => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Баллы от администратора
            'comment_on_points_from_administrator'      => '',                                                              // Комментарий к баллам от администратора
            'status'                                    => '',                                                              // Статус заявки
        ];
    }

    public function getMessages()
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
            'regex' => 'У поля ":attribute" недопустимый формат',
            'min' => [
                'numeric' => 'Поле :attribute не может быть меньше, чем :min.',
                'file' => 'Поле :attribute не может быть меньше, чем :min килобайт.',
                'string' => 'Поле :attribute не может быть меньше, чем :min символов.',
                'array' => 'Поле :attribute не может быть меньше, чем :min элементов.',
            ],
            'max' => [
                'numeric' => 'Поле :attribute не может быть больше, чем :max.',
                'file' => 'Поле :attribute не может быть больше, чем :max килобайт.',
                'string' => 'Поле :attribute не может быть больше, чем :max символов.',
                'array' => 'Поле :attribute не может быть больше, чем :max элементов.',
            ],
        ];
    }

    public function getAttributes()
    {
        return [
            'user_id' => 'Участник',
            'contest_id' => 'Конкурс',
            'municipality_id' => 'Наименование муниципального образования',
            'register_id' => 'Наименование ТОС',
            'region_id' => 'Наименование (муниципального района/городского округа), где реализуется проект',
            'settlement_id' => 'Наименование поселения в составе района, где реализуется проект',
            'date_registration_charter' => 'Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)',
            'is_tos_legal_entity' => 'Является ли ТОС юридическим лицом',
            'nomenclature_number' => 'Номенклатурный номер ТОС',
            'full_name_chairman_tos' => 'ФИО председателя ТОС',
            'tos_address' => 'Почтовый адрес (с указанием индекса)',
            'tos_phone' => 'Номер мобильного телефона',
            'tos_email' => 'Адрес электронной почты',
            'population_size_settlement' => 'Численность населения',
            'population_size_in_tos' => 'Количество жителей, проживающих в границах ТОС',
            'project_name' => 'Наименование проекта',
            'project_direction' => 'Направление проекта',
            'problem_description' => 'Описание актуальности проблемы, на решение которой направлен проект',
            'project_purpose' => 'Цель проекта',
            'project_tasks' => 'Задачи проекта',
            'duration_practice_start' => 'Дата начала реализации проекта',
            'duration_practice_end' => 'Дата окончания реализации проекта',
            'results_project_implementation' => 'Ожидаемые результаты реализации проекта',
            'number_beneficiaries' => 'Количество человек (благополучателей), которые будут пользоваться результатами проекта',
            'description_need' => 'Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации',
            'total_cost_project' => 'Общая стоимость проекта',
            'budget_funds_republic' => 'Средства бюджета Республики Карелия',
            'funds_raised' => 'Привлеченные средства',
            'extra_budgetary_sources' => 'Внебюджетные источники',
            'funds_tos' => 'Средства ТОС',
            'funds_legal_entities' => 'Средства юридических лиц',
            'funds_local_budget' => 'Средства местного бюджета',
            'person_responsible_implementation_project' => 'Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)',
            'number_present_at_general_meeting' => 'Количество присутствующих на общем собрании членов ТОС',
            'is_grand_opening_with_media_coverage' => 'По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»',
            'date_filling_in' => 'Дата заполнения заявки',
            'total_application_points' => 'Общие баллы по заявке',
            'points_from_administrator' => 'Баллы от администратора',
            'comment_on_points_from_administrator' => 'Комментарий к баллам от администратора',
            'status' => 'Статус заявки',
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
            ->list_members_council_tos()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['list_members_council_tos']['field60'] as $key => $start) {

            $data = [
                'group' => 'list_members_council_tos',
                'user_id' => $model->user_id ?? auth()->id(),
                'field60' => $requestData['list_members_council_tos']['field60'][$key],
                'field61' => $requestData['list_members_council_tos']['field61'][$key],
                'field62' => $requestData['list_members_council_tos']['field62'][$key],
            ];

            $model
                ->list_members_council_tos()
                ->create($data)
            ;
        }

        // Календарный план работ по проекту
        $model
            ->calendar_plan_work_on_project()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['calendar_plan_work_on_project']['field63'] as $key => $start) {

            $data = [
                'group' => 'calendar_plan_work_on_project',
                'user_id' => $model->user_id ?? auth()->id(),
                'field63' => $requestData['calendar_plan_work_on_project']['field63'][$key],
                'field64' => $requestData['calendar_plan_work_on_project']['field64'][$key],
                'field65' => $requestData['calendar_plan_work_on_project']['field65'][$key],
            ];

            $model
                ->calendar_plan_work_on_project()
                ->create($data)
            ;
        }

        // Информационное сопровождение проекта (указать, каким образом будет обеспечено освещение проекта в целом и его ключевых мероприятий в СМИ,  социальных сетях (группы ТОС в социальных сетях, группа Ассоциации ТОС в Республике Карелия в социальной сети «Вконтакте» (https://vk.com/tosrk), портал «Инициативное бюджетирование в Республике Карелии» (инициативы-карелия.рф)), реклама, листовки, специальные мероприятия, информирование партнеров.
//        $model
//            ->information_project_support_info()
//            ->where('user_id', $model->user_id)
//            ->delete()
//        ;

//        foreach ($requestData['information_project_support_info']['field66'] as $key => $start) {
//
//            $data = [
//                'group' => 'information_project_support_info',
//                'user_id' => $model->user_id ?? auth()->id(),
//                'field66' => $requestData['information_project_support_info']['field66'][$key],
//                'field67' => $requestData['information_project_support_info']['field67'][$key],
//            ];
//
//            $model
//                ->information_project_support_info()
//                ->create($data)
//            ;
//        }

        // Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.) - описать виды участия
        $model
            ->participation_population_in_implementation_project()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['participation_population_in_implementation_project']['field68'] as $key => $start) {

            $data = [
                'group' => 'participation_population_in_implementation_project',
                'user_id' => $model->user_id ?? auth()->id(),
                'field68' => $requestData['participation_population_in_implementation_project']['field68'][$key],
            ];

            $model
                ->participation_population_in_implementation_project()
                ->create($data)
            ;
        }

        // Участие населения в обеспечении эксплуатации и содержании объекта, после завершения проекта
        $model
            ->public_participation_in_operation_facility()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['public_participation_in_operation_facility']['field69'] as $key => $start) {

            $data = [
                'group' => 'public_participation_in_operation_facility',
                'user_id' => $model->user_id ?? auth()->id(),
                'field69' => $requestData['public_participation_in_operation_facility']['field69'][$key],
            ];

            $model
                ->public_participation_in_operation_facility()
                ->create($data)
            ;
        }

        // Реализацией проекта предусмотрено его информационное сопровождение
        $model
            ->project_implementation_provides_informational_support()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['project_implementation_provides_informational_support']['field70'] as $key => $start) {

            $data = [
                'group' => 'project_implementation_provides_informational_support',
                'user_id' => $model->user_id ?? auth()->id(),
                'field70' => $requestData['project_implementation_provides_informational_support']['field70'][$key],
            ];

            $model
                ->project_implementation_provides_informational_support()
                ->create($data)
            ;
        }
    }

    public function getProjectDirections()
    {
        return config("app.{$this->entity}_applications.project_directions", []);
    }

    public function exportApplication($type, SZPTOSApplication $application)
    {
        try {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path("szptos_template.docx"));

            $templateProcessor->setValue("tosName", $application->tosName);
            $templateProcessor->setValue("municipalityParentName", $application->municipalityParentName);
            $templateProcessor->setValue("municipalityName", $application->municipalityName);
            $templateProcessor->setValue("tos_2", $application->date_registration_charter);
            $templateProcessor->setValue("tos4String", $application->isTosLegalEntityString);
            $templateProcessor->setValue("tos_3", $application->nomenclature_number);
            $templateProcessor->setValue("tos_5", "{$application->full_name_chairman_tos}, {$application->tos_address}, {$application->tos_phone}, {$application->tos_email}");
            $templateProcessor->setValue("tos6String", $application->listMembersCouncilTosString);
            $templateProcessor->setValue("tos_7", $application->population_size_in_tos);

            $templateProcessor->setValue("sspNameProject", $application->project_name);
            $templateProcessor->setValue("sspDirectionsProjectName", config("app.{$this->entity}_applications.project_directions.{$application->project_direction}", ''));
            $templateProcessor->setValue("preliminaryWorkOnSelectionProject", $application->preliminaryWorkOnSelectionProjectStringName);
            $templateProcessor->setValue("ssp_3", $application->problem_description);
            $templateProcessor->setValue("ssp_4", $application->project_purpose);
            $templateProcessor->setValue("ssp_5", $application->project_tasks);
            $templateProcessor->setValue("ssp_6", "{$application->duration_practice_start} - {$application->duration_practice_end}");

            $filtering = $application
                ->calendar_plan_work_on_project
                ->filter(function ($query) {

                    if(!empty($query->field63) || !empty($query->field64) || !empty($query->field65)) {

                        return $query;
                    }
                })
                ->toArray();

            foreach ($filtering as $key => $calendarPlanWorkOnProject) {

                $templateProcessor->setValue("cPWOPPeriod" . ($key + 1), $calendarPlanWorkOnProject['field63']);
                $templateProcessor->setValue("cPWOPStages" . ($key + 1), $calendarPlanWorkOnProject['field64']);
                $templateProcessor->setValue("cPWOPExecutors" . ($key + 1), $calendarPlanWorkOnProject['field65']);
            }

            $keys = array_keys($filtering);
            $arrayDiff = array_diff($this->valuesArray, $keys);

            foreach ($arrayDiff as $item) {

                $templateProcessor->setValue("cPWOPPeriod{$item}", '');
                $templateProcessor->setValue("cPWOPStages{$item}", '');
                $templateProcessor->setValue("cPWOPExecutors{$item}", '');
            }

            $templateProcessor->setValue("linksInformationProjectSupportInfo", $application->linksInformationProjectSupportInfo);

            $templateProcessor->setValue("ssp_8", $application->results_project_implementation);
            $templateProcessor->setValue("ssp_9", $application->number_beneficiaries);
            $templateProcessor->setValue("ssp_10", $application->description_need);

            $templateProcessor->setValue("ssp_budget_1", $application->total_cost_project);
            $templateProcessor->setValue("ssp_budget_2", $application->budget_funds_republic);
            $templateProcessor->setValue("ssp_budget_3", $application->funds_raised);
            $templateProcessor->setValue("ssp_budget_4", $application->extra_budgetary_sources);
            $templateProcessor->setValue("ssp_budget_5", $application->funds_tos);
            $templateProcessor->setValue("ssp_budget_6", $application->funds_legal_entities);
            $templateProcessor->setValue("ssp_budget_7", $application->funds_local_budget);
//        dd(!!(float)$application->total_cost_project, $application->budget_funds_republic, !!$application->total_cost_project && !!(float)$application->budget_funds_republic);
            $templateProcessor->setValue("ssp_budget_2_ratio", (!!(float)$application->total_cost_project && !!(float)$application->budget_funds_republic) ? (($application->budget_funds_republic / $application->total_cost_project) * 100) : 0);
            $templateProcessor->setValue("ssp_budget_3_ratio", (!!(float)$application->total_cost_project && !!(float)$application->funds_raised) ? (($application->funds_raised / $application->total_cost_project) * 100) : 0);
            $templateProcessor->setValue("ssp_budget_4_ratio", !!(float)$application->total_cost_project && !!(float)$application->extra_budgetary_sources ? (($application->extra_budgetary_sources / $application->total_cost_project) * 100) : 0);
            $templateProcessor->setValue("ssp_budget_5_ratio", (!!(float)$application->total_cost_project && !!(float)$application->funds_tos) ? (($application->funds_tos / $application->total_cost_project) * 100) : 0);
            $templateProcessor->setValue("ssp_budget_6_ratio", (!!(float)$application->total_cost_project && !!(float)$application->funds_legal_entities) ? (($application->funds_legal_entities / $application->total_cost_project) * 100) : 0);
            $templateProcessor->setValue("ssp_budget_7_ratio", (!!(float)$application->total_cost_project && !!(float)$application->funds_local_budget) ? (($application->funds_local_budget / $application->total_cost_project) * 100) : 0);

            $templateProcessor->setValue("extractFromRegistryStringName", $application->extractFromRegistryStringName);
            $templateProcessor->setValue("documentationStringName", $application->documentationStringName);
            $templateProcessor->setValue("letterGuaranteeStringName", $application->letterGuaranteeStringName);
            $templateProcessor->setValue("otherDocumentsStringName", $application->otherDocumentsStringName);

            $templateProcessor->setValue("ssp_12", $application->participationPopulationInImplementationProjectString);
            $templateProcessor->setValue("ssp13String", $application->publicParticipationInOperationFacilityString);
            $templateProcessor->setValue("executor", $application->person_responsible_implementation_project);
            $templateProcessor->setValue("tos_date_filling_in", $application->date_filling_in);

            $path = storage_path("app/application-tos/{$application->id}");

            $this->createDirectory($path);

            if ($type === 'pdf') {
                $templateProcessor->saveAs(storage_path("app/application-tos/{$application->id}/Заявка#{$application->id}.docx"));
                $phpWord = \PhpOffice\PhpWord\IOFactory::load(storage_path("app/application-tos/{$application->id}/Заявка#{$application->id}.docx"));

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
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    public function exportTable($type, SZPTOSApplication $application)
    {
        $applications = $this
            ->getApplicationModels($application)
//            ->orderBy('total_application_points', 'desc')
//            ->orderBy('created_at', 'asc')
//            ->take(30)
            ->get()
            ->sortBy('created_at')
            ->sortByDesc('finalPointsResult')
        ;

        $spreadsheet = $this->getHeaderTable();
        $activeSheet = $spreadsheet->getActiveSheet();

        foreach ($applications->values() as $applicationKey => $applicationItem) {

            $i = ($applicationKey + 3);

            $key = ($applicationKey + 1);

            $activeSheet->getRowDimension($i)->setRowHeight(-1);

            $activeSheet->setCellValue("A{$i}", $key);
            $activeSheet->setCellValue("B{$i}", $applicationItem->municipalityParentName);
            $activeSheet->setCellValue("C{$i}", $applicationItem->municipalityUserName);
            $activeSheet->setCellValue("D{$i}", $applicationItem->tosName);
            $activeSheet->setCellValue("E{$i}", $applicationItem->project_name);
            $activeSheet->setCellValue("F{$i}", config("app.{$this->entity}_applications.project_directions.{$applicationItem->project_direction}", ''));
            $activeSheet->setCellValue("G{$i}", $applicationItem->created_at->format('Y-m-d'));
            $activeSheet->setCellValue("H{$i}", $applicationItem->created_at->format('H:i'));
            $activeSheet->setCellValue("I{$i}", $applicationItem->municipalityName);
            $activeSheet->setCellValue("J{$i}", $applicationItem->population_size_in_tos);
            $activeSheet->setCellValue("K{$i}", $applicationItem->number_present_at_general_meeting);
            $activeSheet->setCellValue("L{$i}", $applicationItem->number_beneficiaries);
            $activeSheet->setCellValue("M{$i}", "{$applicationItem->duration_practice_start} - {$applicationItem->duration_practice_end}");
            $activeSheet->setCellValue("N{$i}", $applicationItem->total_application_points ?? 0);
//            $activeSheet->setCellValue("L{$i}", $applicationItem->total_application_points ?? 0);
//            $activeSheet->setCellValue("M{$i}", $applicationItem->points_from_administrator > 0 ? $applicationItem->points_from_administrator : 0);
//            $activeSheet->setCellValue("N{$i}", $applicationItem->points_from_administrator < 0 ? abs($applicationItem->points_from_administrator) : 0);
            $activeSheet->setCellValue("O{$i}", $applicationItem->points_from_administrator);
            $activeSheet->setCellValue("P{$i}", "=N{$i}+O{$i}");
            $activeSheet->setCellValue("Q{$i}", "=R{$i}+S{$i}+T{$i}+U{$i}");
            $activeSheet->setCellValue("R{$i}", $applicationItem->budget_funds_republic);
            $activeSheet->setCellValue("S{$i}", $applicationItem->funds_tos);
            $activeSheet->setCellValue("T{$i}", $applicationItem->funds_legal_entities);
            $activeSheet->setCellValue("U{$i}", $applicationItem->funds_local_budget);
            $activeSheet->setCellValue("V{$i}", "=S{$i}+T{$i}+U{$i}");
            $activeSheet->setCellValue("W{$i}", $applicationItem->id);
            $activeSheet->setCellValue("X{$i}", $applicationItem->isAdmittedToCompetitionLabel);

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
                ->duplicateStyle($sharedStyle, "A{$i}:X{$i}")
            ;

            $activeSheet
                ->getStyle("A{$i}")->getBorders()->getLeft()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
            ;

            $activeSheet->getStyle("A{$i}:X{$i}")->getAlignment()->setWrapText(true);
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
        $activeSheet->getColumnDimension('W')->setWidth(10);
        $activeSheet->getColumnDimension('X')->setWidth(17);

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

        $activeSheet->mergeCells('A1:X1');
        $activeSheet->getStyle('A1:X1')->getFont()->setName('Arial')->setSize(16);
        $year = date('Y');
        $activeSheet
            ->setCellValue('A1', "Рейтинговая таблица проектов, допущенных для участия в конкурсном отборе по Программе поддержки местных инициатив в Республике Карелия в {$year} году.")
        ;

        $activeSheet->getRowDimension(2)->setRowHeight(120);

        $activeSheet->setCellValue('A2', '№ п/п');
        $activeSheet->setCellValue('B2', 'Муниципальный район (городской округ)');
        $activeSheet->setCellValue('C2', 'Наименование поселения в составе района');
        $activeSheet->setCellValue('D2', 'Название ТОС (по уставу)');
        $activeSheet->setCellValue('E2', 'Название проекта');
        $activeSheet->setCellValue('F2', 'Направление проекта');
        $activeSheet->setCellValue('G2', 'Дата приема документов');
        $activeSheet->setCellValue('H2', 'Время приема документов');
        $activeSheet->setCellValue('I2', 'Место реализации проекта (населенный пункт)');
        $activeSheet->setCellValue('J2', 'Кол-во жителей, проживающих в границах ТОС');
        $activeSheet->setCellValue('K2', 'Количество участников собрания по выбору проекта');
        $activeSheet->setCellValue('L2', 'Количество благополучателей');
        $activeSheet->setCellValue('M2', 'Ожидаемый срок реализации');
        $activeSheet->setCellValue('N2', 'Предварительный балл');
        $activeSheet->setCellValue('O2', 'Увеличение баллов');
        $activeSheet->setCellValue('P2', 'Итоговый балл');
        $activeSheet->setCellValue('Q2', 'Стоимость проекта, руб.');
        $activeSheet->setCellValue('R2', 'Средства  бюджета Республики Карелия, руб.');
        $activeSheet->setCellValue('S2', 'Средства ТОС, руб');
        $activeSheet->setCellValue('T2', 'Средства юр.лиц, руб');
        $activeSheet->setCellValue('U2', 'Средства бюджета МО, руб.');
        $activeSheet->setCellValue('V2', 'Доля софинансирования');
        $activeSheet->setCellValue('W2', 'ID заявки');
        $activeSheet->setCellValue('X2', 'Допущен к участию в конкурсе.');

        $activeSheet
            ->duplicateStyle($headerStyle, 'A1:X2')
        ;

        $activeSheet
            ->getStyle('A1:X1')->getBorders()->getTop()
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

        $activeSheet->getStyle('A2:X2')->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('A2:X2')->getFont()->setSize(11);

        return $spreadsheet;
    }

    public function reCalculation()
    {
        $contest = Contest::where('type', 'szptos')->where('is_active', 1)->first();

        if (!$contest) {

            return redirect()->back()->with('status', 'error')->with('message', 'Нет активных конкурсов для заявок!');
        }

        $applications = SZPTOSApplication::where('contest_id', $contest->id)->where('status', 'published')->where('total_cost_project', '>', 0)->get();

        foreach ($applications as $application) {

            $totalApplicationPoints = $this->getCalculation($application);

            $application->total_application_points = $totalApplicationPoints;
            $application->save();
        }

        $message = "Общие баллы у опубликованных заявок для конкурса {$contest->name} успешно пересчитаны!";

        return redirect()
            ->back()
            ->with('status', 'success')
            ->with('message', $message)
        ;
    }
}
