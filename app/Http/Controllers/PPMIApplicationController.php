<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\PPMIApplication;
use App\Models\User;
use App\Services\MPDFService;
use App\Traits\CalculationPPMIApplicationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PPMIApplicationController extends CommonController
{
    use CalculationPPMIApplicationTrait;

    protected $entity = 'ppmi';

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
     * @param PPMIApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(PPMIApplication $model)
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
        $projectTypologies = $this->getApplicationProjectTypologies();
        $statuses = $this->getApplicationStatuses();

//        $contests = Contest::where('is_active', 1)->get()->pluck('name', 'id')->toArray();
//        $municipalities = Municipality::all()->pluck('name', 'id')->toArray();
//        $users = User::where('is_active', 1)->get()->pluck('first_name', 'id')->toArray();
//        $projectTypologies = config("app.{$this->entity}_applications.project_typologies", []);
//        $statuses = config("app.{$this->entity}_applications.statuses", []);

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('contests', $contests)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('projectTypologies', $projectTypologies)
            ->setCollect('users', $users)
            ->setCollect('statuses', $statuses)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
        ;

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param PPMIApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(PPMIApplication $model)
    {
        $this->authorize('create', $model);

        $contest = $this->getContest($model, 'ppmi');
        $user = auth()->user();
        $municipalities = $user->municipalitiesList;
        $projectTypologies = $this->getApplicationProjectTypologies();

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
            ->setCollect('projectTypologies', $projectTypologies)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param PPMIApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, PPMIApplication $model)
    {
        $this->prepareForValidation($request);

        if (!$this->validateExceedingLevelCoFinancing($request)) {
            return back()->withInput();
        }

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
     * @param PPMIApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(PPMIApplication $model)
    {
        $this->authorize('update', $model);

        if (!auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']) && ((int) $model->user_id !== (int) auth()->id())) {

            return abort(404);
        }

        $contest = $this->getContest($model, 'ppmi');
        $user = $model->user;
        $municipalities = $user->municipalitiesList;
        $projectTypologies = $this->getApplicationProjectTypologies();

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
            ->setCollect('projectTypologies', $projectTypologies)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param PPMIApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PPMIApplication $model)
    {
        $this->prepareForValidation($request);

        if (!$this->validateExceedingLevelCoFinancing($request)) {
            return back()->withInput();
        }

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

        $model->refresh();

        if ($model->status === 'published') {

            $totalApplicationPoints = $this->getCalculation($model);

            $model->total_application_points = $totalApplicationPoints;
            $model->save();
        }

        return redirect(route("applications.{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param PPMIApplication $model
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, PPMIApplication $model)
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
     * @param PPMIApplication $model
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, PPMIApplication $model)
    {
        $this->authorize('restore', $model);

        if ($request->isMethod('GET')) {

            if (auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee'])) {

                $models = $this
                    ->getApplicationModels($model)
                    ->orderBy($model->columnSorting, $model->directionSorting)
                    ->onlyTrashed()
                    ->paginate($model->totalRecords)
                ;
            } else {

                $models = $this
                    ->getApplicationModels($model, auth()->id())
                    ->orderBy($model->columnSorting, $model->directionSorting)
                    ->onlyTrashed()
                    ->paginate($model->totalRecords)
                ;
            }

            $redirectRouteName = __FUNCTION__;

            $this->setCommonDataApplication($model);

            $contests = $this->getApplicationContests();
            $municipalities = $this->getApplicationMunicipalities();
            $users = $this->getApplicationUsers();
            $projectTypologies = $this->getApplicationProjectTypologies();
            $statuses = $this->getApplicationStatuses();

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
                ->setCollect('projectTypologies', $projectTypologies)
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

    public function getFundsMunicipalData(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if (!$user) {
            return [
                'errorMessage' => 'Пользователь не найден.',
            ];
        }

        $municipalityName =$user->municipality?->name;
        if (!$municipalityName) {
            return [
                'errorMessage' => 'У пользователя нет связи с муниципалитетом.',
            ];
        }

        $calculationConfig = config('app.calculation.ppmi', []);

        if (preg_match('/муниципальный округ/', $municipalityName)) {
            $result = Arr::get($calculationConfig, 'mo.budget_funds', []);
            $result = array_merge($result, ['municipality' => $municipalityName]);
        } elseif (preg_match('/муниципальный район/', $municipalityName)) {
            $result = Arr::get($calculationConfig, 'mr.budget_funds', []);
            $result = array_merge($result, ['municipality' => $municipalityName]);
        } elseif (preg_match('/городское поселение/', $municipalityName)) {
            $result = Arr::get($calculationConfig, 'gp.budget_funds', []);
            $result = array_merge($result, ['municipality' => $municipalityName]);
        } elseif (preg_match('/сельское/', $municipalityName)) {
            $prefix = $request->input('population_size_settlement', 0) > 3000 ? 'from1000' : 'to1000';
            $result = Arr::get($calculationConfig, "sp.{$prefix}.budget_funds", []);
            $result = array_merge($result, ['municipality' => $municipalityName]);
        } elseif (preg_match('/городской округ/', $municipalityName)) {
            $result = Arr::get($calculationConfig, 'go.budget_funds', []);
            $result = array_merge($result, ['municipality' => $municipalityName]);
        } else {
            $result =  [
                'errorMessage' => "Нет данных по софинансированию из бюджета Республики Карелия для {$municipalityName}.",
            ];
        }

        return $result;
    }

    public function addMessageBag(Request $request, $key, $message)
    {
        $messageBag = new MessageBag();
        $messageBag->add($key, $message);

        $errors = $request->session()->get('errors', new ViewErrorBag());

        if (!$errors instanceof ViewErrorBag) {
            $errors = new ViewErrorBag;
        }

        $request->session()->flash('errors', $errors->put('default', $messageBag));
    }

    /**
     *---------------------------------------------------------------------------
     * Проверка превышения уровня софинансирования из бюджета Республики Карелия
     *---------------------------------------------------------------------------
     ** @param Request $request
     * @return bool
     *
     */
    public function validateExceedingLevelCoFinancing(Request $request): bool
    {
        $fundsMunicipalData = $this->getFundsMunicipalData($request);
        $errorMessage = Arr::get($fundsMunicipalData, 'errorMessage', '');
        if (!empty($errorMessage)) {
            $this->addMessageBag($request, 'exceeding_level_co_financing', $errorMessage);
            return false;
        }
        $projectCost = collect($request->only(['funds_municipal', 'funds_individuals', 'funds_legal_entities', 'funds_republic']))->sum();

        if ($projectCost < 1 && $request->input('status') !== 'published') {
            return true;
        } elseif ($projectCost < 1 && $request->input('status') === 'published') {
            return false;
        }

        $budgetFundsMax = Arr::get($fundsMunicipalData, 'sum', 0);

        // Проверка превышения уровня софинансирования
        if ($projectCost < $budgetFundsMax) {
            $fundsRepublic = $request->input('funds_republic');
            $significantPercentage = $fundsRepublic / $projectCost * 100;
            $percentage = Arr::get($fundsMunicipalData, 'percentage', 0);
            if ($significantPercentage > $percentage) {
                $municipalityName = Arr::get($fundsMunicipalData, 'municipality', '');
                $this->addMessageBag($request, 'exceeding_level_co_financing', "Превышение уровня софинансирования. Сумма софинансирования из бюджета Республики Карелия для {$municipalityName} не должна быть больше чем {$percentage}% от общей стоимости проекта.");
                return false;
            }
        }

        return true;
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
            'user_id'                           => 'required',
            'contest_id'                        => 'required',
            'project_name'                      => 'required',
            'municipality_id'                   => 'required',
            'cost_repair_work'                  => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_materials'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_equipment'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_construction_control'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_other_expenses'               => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_municipal'                   => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_individuals'                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_legal_entities'              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_republic'                    => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'population_size'                   => ['regex:/^([0-9]{0,11})?$/'],
            'population_size_in_congregation'   => ['regex:/^([0-9]{0,11})?$/'],
            'total_application_points'          => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'points_from_administrator'         => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
        ];
    }

    public function getPublishedRules()
    {
        return [
            'user_id'                                       => 'required',
            'contest_id'                                    => 'required',
            'project_name'                                  => 'required',
            'municipality_id'                               => 'required',
            'project_typology'                              => 'required',
            'description_problem'                           => 'required',
            'cost_repair_work'                              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_materials'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_equipment'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_construction_control'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_other_expenses'                           => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'expected_results'                              => 'required',
            'funds_municipal'                               => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_individuals'                             => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_legal_entities'                          => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_republic'                                => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'population_that_benefit_from_results_project'  => 'required',
            'population_size'                               => 'required|numeric',
            'population_size_in_congregation'               => 'required|numeric',
            'population_in_project_implementation'          => '',
            'population_in_project_provision'               => '',
            'implementation_date'                           => 'required',
            'comment'                                       => '',
            'total_application_points'                      => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'points_from_administrator'                     => ['regex:/^-?([0-9]{0,20})$|^-?([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
        ];
    }

    public function getMessages()
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
            'regex' => 'У поля ":attribute" недопустимый формат',
        ];
    }

    public function getAttributes()
    {
        return [
            'user_id' => 'Участник',
            'contest_id' => 'Конкурс',
            'project_name' => '1. Наименование проекта для предоставления субсидий на поддержку местных инициатив граждан, проживающих в муниципальных образованиях в Республике Карелия:',
            'municipality_id' => '2. Муниципальное образование в Республике Карелия или Населенный пункт:',
            'population_size_settlement' => '3. Численность населения населенного пункта:',
            'project_typology' => '4. Типология проекта:',
            'extracts' => '5. Наличие выписки из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта:',
            'description_problem' => '6.Описание проблемы, на решение которой направлен проект:',
            'cost_repair_work' => '7.1.1. Стоимость ремонтных работ',
            'comment_on_cost_repairs' => '7.1.2. Комментарий к стоимости ремонтных работ',
            'cost_purchasing_materials' => '7.2.1. Стоимость приобретения материалов',
            'comment_on_cost_purchasing_materials' => '7.2.2. Комментарий к стоимости приобретения материалов',
            'cost_purchasing_equipment' => '7.3.1. Стоимость приобретения оборудования',
            'comment_on_cost_purchasing_equipment' => '7.3.2. Комментарий к стоимости приобретения оборудования',
            'cost_construction_control' => '7.4.1. Стоимость строительного контроля',
            'comment_on_cost_construction_control' => '7.4.2. Комментарий к стоимости строительного контроля',
            'cost_other_expenses' => '7.5.1. Стоимость прочих расходов',
            'comment_on_cost_other_expenses' => '6.5.2. Комментарий к стоимость прочих расходов',
            'documentation' => '8. Наличие технической, проектной и сметной документации:',
            'expected_results' => '9.Ожидаемые результаты:',
            'funds_municipal' => '10.1. Средства бюджета муниципального образования:',
            'funds_individuals' => '10.2. Безвозмездные поступления от физических лиц (жителей):',
            'funds_legal_entities' => '10.3. Безвозмездные поступления от юридических лиц:',
            'funds_republic' => '10.4. Средства бюджета Республики Карелия:',
            'planned_sources_financing' => '11. Планируемые источники финансирования мероприятий проекта:',
            'gratuitous_receipts' => '12. Расшифровка безвозмездных поступлений от юридических лиц:',
            'population_that_benefit_from_results_project' => '13. Население, которое будет регулярно пользоваться результатами от реализации проекта:',
            'population_size' => '14. Количество человек:',
            'population_size_in_congregation' => '15. Количество лиц, принявших участие в собрании граждан:',
            'protocols' => '16. Протоколы собрания:',
            'population_in_project_implementation' => '17. Участие населения в реализации проекта:',
            'operating_and_maintenance_costs' => '18. Расходы на эксплуатацию и содержание муниципального имущества, предусмотренного проектом в первый год после завершения реализации проекта:',
            'population_in_project_provision' => '19. Участие населения в обеспечении эксплуатации и содержании муниципального имущества, предусмотренного проектом, после завершения реализации проекта:',
            'questionnaires' => '20. Предварительное обсуждение проекта (опросные листы, анкеты, собрания, подомовой обход и т.д.):',
            'implementation_date' => '21. Ожидаемый срок реализации проекта',
            'comment' => '22. Дополнительная информация и комментарии',
            'is_unpaid_work_of_population' => '23. Неоплачиваемый труд населения',
            'is_media_participation' => '24. Участие СМИ',
            'mass_media' => '25. Участие СМИ',
            'total_application_points' => '26. Общие баллы по заявке',
            'points_from_administrator' => '27. Баллы от администратора',
            'comment_on_points_from_administrator' => '28. Комментарий к баллам от администратора',
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

    public function getApplicationProjectTypologies()
    {
        return config("app.{$this->entity}_applications.project_typologies", []);
    }

    public function saveMatrix($model, $requestData)
    {
        // Расшифровка безвозмездных поступлений
        $model
            ->gratuitous_receipts()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['gratuitous_receipts']['field1'] as $key => $start) {

            $data = [
                'group' => 'gratuitous_receipts',
                'user_id' => $model->user_id ?? auth()->id(),
                'field1' => $requestData['gratuitous_receipts']['field1'][$key],
                'field2' => $requestData['gratuitous_receipts']['field2'][$key],
            ];

            $model
                ->gratuitous_receipts()
                ->create($data)
            ;
        }

        // Расходы на эксплуатацию и содержание муниципального имущества
        $model
            ->operating_and_maintenance_costs()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['operating_and_maintenance_costs']['field3'] as $key => $start) {

            $data = [
                'group' => 'operating_and_maintenance_costs',
                'user_id' => $model->user_id ?? auth()->id(),
                'field3' => $requestData['operating_and_maintenance_costs']['field3'][$key],
                'field4' => $requestData['operating_and_maintenance_costs']['field4'][$key],
                'field5' => $requestData['operating_and_maintenance_costs']['field5'][$key],
                'field6' => $requestData['operating_and_maintenance_costs']['field6'][$key],
            ];

            $model
                ->operating_and_maintenance_costs()
                ->create($data)
            ;
        }

        if (isset($requestData['participation_population_in_implementation_project']) && is_array($requestData['participation_population_in_implementation_project'])) {

            // Участие населения в реализации проекта - описать виды участия (каждый вид участия указывается отдельной строкой)
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
        }

        if (isset($requestData['public_participation_in_operation_facility']) && is_array($requestData['public_participation_in_operation_facility'])) {

            // Участие населения в обеспечении эксплуатации и содержании муниципального имущества, предусмотренного проектом, после завершения реализации проекта (каждый вид участия указывается отдельной строкой)
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
        }
        if (isset($requestData['project_implementation_provides_informational_support']) && is_array($requestData['project_implementation_provides_informational_support'])) {

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

        if (isset($requestData['planned_activities_within_project']) && is_array($requestData['planned_activities_within_project'])) {
            // Запланированные мероприятия в рамках реализации проекта
            $model
                ->planned_activities_within_project()
                ->where('user_id', $model->user_id)
                ->delete();

            foreach ($requestData['planned_activities_within_project']['field73'] as $key => $start) {
                $data = [
                    'group' => 'planned_activities_within_project',
                    'user_id' => $model->user_id ?? auth()->id(),
                    'field73' => $requestData['planned_activities_within_project']['field73'][$key],
                    'field74' => $requestData['planned_activities_within_project']['field74'][$key],
                    'field75' => $requestData['planned_activities_within_project']['field75'][$key],
                ];

                $model
                    ->planned_activities_within_project()
                    ->create($data);
            }
        }
    }

    public function exportApplication($type, PPMIApplication $application)
    {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path("application_template.docx"));

        $templateProcessor->setValue("1_0", $application->{"project_name"});
        $templateProcessor->setValue("municipalityParentName", $application->municipalityUserName);
        $templateProcessor->setValue("municipalityName", $application->municipalityName);
        $templateProcessor->setValue("2_3", $application->{"population_size_settlement"});
        $templateProcessor->setValue("3_1", config("app.ppmi_applications.project_typologies.{$application->{"project_typology"}}", ""));
        $templateProcessor->setValue("3_3", $application->{"description_problem"});

        $sum = ($application->{"cost_repair_work"} + $application->{"cost_purchasing_materials"} + $application->{"cost_purchasing_equipment"} + $application->{"cost_construction_control"} + $application->{"cost_other_expenses"});

        $templateProcessor->setValue("3_4_1_value", number_format($application->{"cost_repair_work"}, '2', '.', ''));
        $templateProcessor->setValue("3_4_1_comment", $application->{"comment_on_cost_repairs"});
        $templateProcessor->setValue("3_4_2_value", number_format($application->{"cost_purchasing_materials"}, '2', '.', ''));
        $templateProcessor->setValue("3_4_2_comment", $application->{"comment_on_cost_purchasing_materials"});
        $templateProcessor->setValue("3_4_3_value", number_format($application->{"cost_purchasing_equipment"}, '2', '.', ''));
        $templateProcessor->setValue("3_4_3_comment", $application->{"comment_on_cost_purchasing_equipment"});
        $templateProcessor->setValue("3_4_4_value", number_format($application->{"cost_construction_control"}, '2', '.', ''));
        $templateProcessor->setValue("3_4_4_comment", $application->{"comment_on_cost_construction_control"});
        $templateProcessor->setValue("3_4_5_value", number_format($application->{"cost_other_expenses"}, '2', '.', ''));
        $templateProcessor->setValue("3_4_5_comment", $application->{"comment_on_cost_other_expenses"});
        $templateProcessor->setValue("3_4_sum", number_format($sum, '2', '.', ''));

        $templateProcessor->setValue("3_6", $application->{"expected_results"});

        $templateProcessor->setValue("extractsStringName", $application->extractsStringName);
        $templateProcessor->setValue("documentationStringName", $application->documentationStringName);
        $templateProcessor->setValue("protocolsStringName", $application->protocolsStringName);
        $templateProcessor->setValue("questionnairesStringName", $application->questionnairesStringName);
        $templateProcessor->setValue("massMediaStringName", $application->massMediaStringName);

        $sum = ($application->{"funds_individuals"} + $application->{"funds_legal_entities"});
        $sumFull = ($application->{"funds_municipal"} + $sum + $application->{"funds_republic"});

        if (!!$sumFull) {

            $percent1 = ($application->{"funds_municipal"} / $sumFull) * 100;
            $percent2 = ($sum / $sumFull) * 100;
            $percent21 = ($application->{"funds_individuals"} / $sumFull) * 100;
            $percent22 = ($application->{"funds_legal_entities"} / $sumFull) * 100;
            $percent3 = ($application->{"funds_republic"} / $sumFull) * 100;
        } else {

            $percent1 = 0;
            $percent2 = 0;
            $percent21 = 0;
            $percent22 = 0;
            $percent3 = 0;
        }

        $templateProcessor->setValue("4_1_1_value", number_format($application->{"funds_municipal"}, '2', '.', ''));
        $templateProcessor->setValue("4_1_2_sum", number_format($sum, '2', '.', ''));
        $templateProcessor->setValue("4_1_2_1_value", number_format($application->{"funds_individuals"}, '2', '.', ''));
        $templateProcessor->setValue("4_1_2_2_value", number_format($application->{"funds_legal_entities"}, '2', '.', ''));
        $templateProcessor->setValue("4_1_3_value", number_format($application->{"funds_republic"}, '2', '.', ''));
        $templateProcessor->setValue("4_1_1_percent", $percent1);
        $templateProcessor->setValue("4_1_2_percent", $percent2);
        $templateProcessor->setValue("4_1_2_1_percent", $percent21);
        $templateProcessor->setValue("4_1_2_2_percent", $percent22);
        $templateProcessor->setValue("4_1_3_percent", $percent3);

        $templateProcessor->setValue("4_1_sum", number_format($sumFull, '2', '.', ''));

        $gratuitousReceiptsSum = 0.00;

        $filtering = $application
            ->gratuitous_receipts
            ->filter(function ($query) {

                if(!empty($query->field1) || !empty($query->field2)) {

                    return $query;
                }
            })
            ->toArray();

        foreach ($filtering as $key => $gratuitousReceipt) {

            $value = floatval(str_replace(" ", '', str_replace(",", '.', $gratuitousReceipt['field2'])));
            $templateProcessor->setValue("gratuitousReceiptsName" . ($key + 1), $gratuitousReceipt['field1']);
            $templateProcessor->setValue("gratuitousReceiptsValue" . ($key + 1), $value);

            $gratuitousReceiptsSum += $value;
        }

        $templateProcessor->setValue("gratuitousReceiptsSum", $gratuitousReceiptsSum);

        $keys = array_keys($filtering);
        $arrayDiff = array_diff($this->valuesArray, $keys);

        foreach ($arrayDiff as $item) {

            $templateProcessor->setValue("gratuitousReceiptsName{$item}", '');
            $templateProcessor->setValue("gratuitousReceiptsValue{$item}", '');
        }

        $templateProcessor->setValue("4_2_comment", $application->{"population_that_benefit_from_results_project"});

        $templateProcessor->setValue("4_2_value", $application->{"population_size"});
        $templateProcessor->setValue("4_3_value", $application->{"population_size_in_congregation"});

        $populationInProjectImplementation = $application->{"population_in_project_implementation"};

        if (empty($populationInProjectImplementation)) {
            $populationInProjectImplementation = $application->participationPopulationInImplementationProjectString;
        }

        $templateProcessor->setValue("4_4", $populationInProjectImplementation);

        $operatingAndMaintenanceCostsOneSum = 0.00;
        $operatingAndMaintenanceCostsTwoSum = 0.00;
        $operatingAndMaintenanceCostsThreeSum = 0.00;

        $filtering = $application
            ->operating_and_maintenance_costs
            ->filter(function ($query) {

                if(!empty($query->field3) || !empty($query->field4) || !empty($query->field5) || !empty($query->field6)) {

                    return $query;
                }
            })
            ->toArray();

        foreach ($filtering as $key => $operatingAndMaintenanceCost) {

            $valueOne = floatval(str_replace(",", '.', $operatingAndMaintenanceCost['field4']));
            $valueTwo = floatval(str_replace(",", '.', $operatingAndMaintenanceCost['field5']));
            $valueThree = floatval(str_replace(",", '.', $operatingAndMaintenanceCost['field6']));
            $templateProcessor->setValue("operatingAndMaintenanceCostsName" . ($key + 1), $operatingAndMaintenanceCost['field3']);
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueOne" . ($key + 1), $valueOne);
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueTwo" . ($key + 1), $valueTwo);
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueThree" . ($key + 1), $valueThree);
            $sum = ($valueOne + $valueTwo + $valueThree);
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueSum" . ($key + 1), $sum);

            $operatingAndMaintenanceCostsOneSum += $valueOne;
            $operatingAndMaintenanceCostsTwoSum += $valueTwo;
            $operatingAndMaintenanceCostsThreeSum += $valueThree;
        }
        $templateProcessor->setValue("operatingAndMaintenanceCostsOneSum", $operatingAndMaintenanceCostsOneSum);
        $templateProcessor->setValue("operatingAndMaintenanceCostsTwoSum", $operatingAndMaintenanceCostsTwoSum);
        $templateProcessor->setValue("operatingAndMaintenanceCostsThreeSum", $operatingAndMaintenanceCostsThreeSum);

        $keys = array_keys($filtering);
        $arrayDiff = array_diff($this->valuesArray, $keys);

        foreach ($arrayDiff as $item) {

            $templateProcessor->setValue("operatingAndMaintenanceCostsName{$item}", '');
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueOne{$item}", '');
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueTwo{$item}", '');
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueThree{$item}", '');
            $templateProcessor->setValue("operatingAndMaintenanceCostsValueSum{$item}", '');
        }

        $populationInProjectProvision = $application->{"population_in_project_provision"};

        if (empty($populationInProjectProvision)) {
            $populationInProjectProvision = $application->publicParticipationInOperationFacilityString;
        }

        $templateProcessor->setValue("4_6", $populationInProjectProvision);
        $templateProcessor->setValue("implementation_date", Carbon::parse($application->implementation_date)->format("d.m.Y"));
        $templateProcessor->setValue("additional_information", $application->additional_information);

        $templateProcessor->setValue("app_glava", Auth::user()->municipality_chief);
        $templateProcessor->setValue("app_glava_phone", Auth::user()->municipality_phone);
        $templateProcessor->setValue("app_glava_email", Auth::user()->municipality_email);
        $templateProcessor->setValue("app_post_address", Auth::user()->municipality_address);

        $templateProcessor->setValue("created_at", $application->created_at->format("d.m.Y"));

        $templateProcessor->setValue("app_executor", Auth::user()->executor);
        $templateProcessor->setValue("app_executor_phone", Auth::user()->executor_phone);
        $templateProcessor->setValue("app_executor_email", Auth::user()->executor_email);

        $path = storage_path("app/applications/{$application->id}");

        if (!file_exists($path)) {

            File::makeDirectory($path, 0777, true);
        } else {

            File::chmod($path, 0777);
        }

        if ($type === 'pdf') {
            $templateProcessor->saveAs(storage_path("app/applications/{$application->id}/Заявка#{$application->id}.docx"));
            $phpWord = \PhpOffice\PhpWord\IOFactory::load(storage_path("app/applications/{$application->id}/Заявка#{$application->id}.docx"));

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

    public function exportTable($type, PPMIApplication $application)
    {
        $applications = $this
            ->getApplicationModels($application)
//            ->orderBy('total_application_points', 'desc')
//            ->orderBy('created_at', 'asc')
//            ->take(30)
            ->get()
            ->sortBy('created_at')
            ->sortByDesc('finalPointsResult')
//            ->sortBy(function ($application) {
//
//                return $application->finalPointsResult;
//            }, SORT_NUMERIC, true)
        ;

        $spreadsheet = $this->getHeaderTable($applications->first());
        $activeSheet = $spreadsheet->getActiveSheet();

        foreach ($applications->values() as $applicationKey => $applicationItem) {

            $i = ($applicationKey + 3);

            $key = ($applicationKey + 1);

            $activeSheet->getRowDimension($i)->setRowHeight(-1);

            $fundsMunicipal = (float) $applicationItem->{"funds_municipal"};
            $fundsIndividuals = (float) $applicationItem->{"funds_individuals"};
            $fundsLegalEntities = (float) $applicationItem->{"funds_legal_entities"};
            $fundsRepublic = (float) $applicationItem->{"funds_republic"};

            $sumFunds = ($fundsMunicipal + $fundsIndividuals + $fundsLegalEntities + $fundsRepublic);
            $sumFundsIndividualsAndLegalEntities = ($fundsIndividuals + $fundsLegalEntities);
            $sumFundsMunicipalAndRepublic = $fundsMunicipal + $fundsRepublic;

            $activeSheet->setCellValue("A{$i}", $key);
            $activeSheet->setCellValue("B{$i}", $applicationItem->id);
            $activeSheet->setCellValue("C{$i}", $applicationItem->municipality?->parentTopLevelName);
            $activeSheet->setCellValue("D{$i}", $applicationItem->municipalityUserName);
            $activeSheet->setCellValue("E{$i}", $applicationItem->{"project_name"});
            $activeSheet->setCellValue("F{$i}", $applicationItem->projectTypologyName);
            $activeSheet->setCellValue("G{$i}", $applicationItem->created_at->format('Y-m-d'));
            $activeSheet->setCellValue("H{$i}", $applicationItem->created_at->format('H:i'));
            $activeSheet->setCellValue("I{$i}", $applicationItem->municipalityName);
            $activeSheet->setCellValue("J{$i}", $applicationItem->{"population_size_settlement"});
            $activeSheet->setCellValue("K{$i}", $applicationItem->{"population_size"});
            $activeSheet->setCellValue("L{$i}", $applicationItem->{"population_size_in_congregation"});
            $activeSheet->setCellValue("M{$i}", $applicationItem->implementation_date);
            $activeSheet->setCellValue("N{$i}", $applicationItem->total_application_points ?? 0);
            $activeSheet->setCellValue("O{$i}", $applicationItem->points_from_administrator > 0 ? $applicationItem->points_from_administrator : 0);
            $activeSheet->setCellValue("P{$i}", $applicationItem->points_from_administrator < 0 ? abs($applicationItem->points_from_administrator) : 0);
            $activeSheet->setCellValue("Q{$i}", "=N{$i}+O{$i}-P{$i}");
            $activeSheet->setCellValue("R{$i}", (string) number_format($sumFunds, 2, ',', ''));
            $activeSheet->setCellValue("S{$i}", (string) number_format($fundsRepublic, 2, ',', ''));
            $activeSheet->setCellValue("T{$i}", (string) number_format($fundsMunicipal, 2, ',', ''));
            $activeSheet->setCellValue("U{$i}", (string) number_format($sumFundsIndividualsAndLegalEntities, 2, ',', ''));
            $activeSheet->setCellValue("V{$i}", (string) number_format($sumFundsMunicipalAndRepublic, 2, ',', ''));
            $activeSheet->setCellValue("W{$i}", $applicationItem->isAdmittedToCompetitionLabel);

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
                ->duplicateStyle($sharedStyle, "A{$i}:W{$i}")
            ;

            $activeSheet
                ->getStyle("A{$i}")->getBorders()->getLeft()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'))
            ;

            $activeSheet->getStyle("A{$i}:W{$i}")->getAlignment()->setWrapText(true);
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

    public function getHeaderTable(PPMIApplication $application)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.common.app_name', ''))
            ->setLastModifiedBy(config('app.common.app_name', ''))
            ->setTitle('Рейтинговая таблица проектов')
            ->setSubject('Рейтинговая таблица проектов')
            ->setDescription("Рейтинговая таблица проектов, допущенных для участия в конкурсном отборе по Программе поддержки местных инициатив в Республике Карелия в {$application->contest?->year_of_competition} году.")
            ->setKeywords('Рейтинговая таблица проектов')
            ->setCategory('Рейтинговая таблица проектов');

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();

        $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $activeSheet->getRowDimension(1)->setRowHeight(30);
        $activeSheet->getColumnDimension('A')->setWidth(7.63);
        $activeSheet->getColumnDimension('B')->setWidth(7.63);
        $activeSheet->getColumnDimension('C')->setWidth(18.38);
        $activeSheet->getColumnDimension('D')->setWidth(15.25);
        $activeSheet->getColumnDimension('E')->setWidth(12.63);
        $activeSheet->getColumnDimension('F')->setWidth(12.63);
        $activeSheet->getColumnDimension('G')->setWidth(7.63);
        $activeSheet->getColumnDimension('H')->setWidth(7.63);
        $activeSheet->getColumnDimension('I')->setWidth(12.13);
        $activeSheet->getColumnDimension('J')->setWidth(11.25);
        $activeSheet->getColumnDimension('K')->setWidth(7.63);
        $activeSheet->getColumnDimension('L')->setWidth(7.63);
        $activeSheet->getColumnDimension('M')->setWidth(7.63);
        $activeSheet->getColumnDimension('N')->setWidth(7.63);
        $activeSheet->getColumnDimension('O')->setWidth(7.63);
        $activeSheet->getColumnDimension('P')->setWidth(7.63);
        $activeSheet->getColumnDimension('Q')->setWidth(12.63);
        $activeSheet->getColumnDimension('R')->setWidth(7.63);
        $activeSheet->getColumnDimension('S')->setWidth(7.63);
        $activeSheet->getColumnDimension('T')->setWidth(17.13);
        $activeSheet->getColumnDimension('U')->setWidth(11.88);
        $activeSheet->getColumnDimension('V')->setWidth(12.25);
        $activeSheet->getColumnDimension('W')->setWidth(17);

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
            ->setCellValue('A1', "Рейтинговая таблица проектов, допущенных для участия в конкурсном отборе по Программе поддержки местных инициатив в Республике Карелия в {$application->contest?->year_of_competition} году.")
        ;

        $activeSheet->getRowDimension(2)->setRowHeight(120);

        $activeSheet->setCellValue('A2', '№ п/п');
        $activeSheet->setCellValue('B2', 'ID заявки');
        $activeSheet->setCellValue('C2', 'Муниципальный район (городской округ)');
        $activeSheet->setCellValue('D2', 'Администрация муниципального образования, представившая проект');
        $activeSheet->setCellValue('E2', 'Проект');
        $activeSheet->setCellValue('F2', 'Типология проекта');
        $activeSheet->setCellValue('G2', 'Дата приема документов');
        $activeSheet->setCellValue('H2', 'Время приема документов');
        $activeSheet->setCellValue('I2', 'Место реализации проекта (населенный пункт)');
        $activeSheet->setCellValue('J2', 'Численность жителей населенного пункта, чел.');
        $activeSheet->setCellValue('K2', 'Количество благополучателей');
        $activeSheet->setCellValue('L2', 'Количество участников собрания');
        $activeSheet->setCellValue('M2', 'Ожидаемый срок реализации');
        $activeSheet->setCellValue('N2', 'Предварительный балл');
        $activeSheet->setCellValue('O2', 'Увеличение баллов');
        $activeSheet->setCellValue('P2', 'Снижение баллов');
        $activeSheet->setCellValue('Q2', 'Итоговый балл');
        $activeSheet->setCellValue('R2', 'Стоимость проекта, руб.');
        $activeSheet->setCellValue('S2', 'Субсидия из бюджета Республики Карелия, руб.');
        $activeSheet->setCellValue('T2', 'Средства бюджета муниципального образования, руб.');
        $activeSheet->setCellValue('U2', 'Доля софинансирования физ. и юр. лиц, руб.');
        $activeSheet->setCellValue('V2', 'Суммарный объем субсидий из бюджета РК, руб.');
        $activeSheet->setCellValue('W2', 'Допущен к участию в конкурсе.');

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

    public function getApplicationContests()
    {
        $contests = Contest::where('type', 'ppmi')
            ->get()
            ->pluck('name', 'id')
            ->toArray()
        ;

        return $contests;
    }

    public function reCalculation()
    {
        $contest = Contest::where('type', 'ppmi')->where('is_active', 1)->first();

        if (!$contest) {

            return redirect()->back()->with('status', 'error')->with('message', 'Нет активных конкурсов для заявок!');
        }

        $applications = PPMIApplication::where('contest_id', $contest->id)->where('status', 'published')->get();

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
