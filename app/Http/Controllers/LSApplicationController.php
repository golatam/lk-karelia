<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\LSApplication;
use App\Models\Municipality;
use App\Models\Register;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LSApplicationController extends CommonController
{
    protected $entity = 'ls';

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
     * @param LSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(LSApplication $model)
    {
        $this->authorize('view', $model);

        $models = $model->filtering();

        if (auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee'])) {

            $models = $models
                ->orderBy($model->columnSorting, $model->directionSorting)
                ->paginate($model->totalRecords)
            ;
        } else {

            $models = $models
                ->where('user_id', auth()->id())
                ->orderBy($model->columnSorting, $model->directionSorting)
                ->paginate($model->totalRecords)
            ;
        }

        $redirectRouteName = __FUNCTION__;

        $models_count = $models->total();

        $this->setCommonDataApplication($model);

        $users = User::where('is_active', 1)->get()->pluck('first_name', 'id')->toArray();
        $contests = Contest::where('is_active', 1)->get()->pluck('name', 'id')->toArray();
        $municipalities = $this->getApplicationMunicipalities();
        $nominations = config("app.{$this->entity}_applications.nominations", []);
        $statuses = config("app.{$this->entity}_applications.statuses", []);

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('contests', $contests)
            ->setCollect('users', $users)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('nominations', $nominations)
            ->setCollect('statuses', $statuses)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
        ;

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param LSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(LSApplication $model)
    {
        $this->authorize('create', $model);

        $contest = $this->getContest($model, 'ls');
        $user = auth()->user();
        $nominations = config("app.{$this->entity}_applications.nominations", []);

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
            ->setCollect('nominations', $nominations)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, LSApplication $model)
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

        // Дополнительное образование
        $model
            ->additional_education()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['additional_education']['field11'] as $key => $start) {

            $data = [
                'group' => 'additional_education',
                'user_id' => $model->user_id ?? auth()->id(),
                'field11' => $requestData['additional_education']['field11'][$key],
                'field12' => $requestData['additional_education']['field12'][$key],
                'field13' => $requestData['additional_education']['field13'][$key],
            ];

            $model
                ->additional_education()
                ->create($data)
            ;
        }

        // Повышение квалификации
        $model
            ->professional_development()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['professional_development']['field14'] as $key => $start) {

            $data = [
                'group' => 'professional_development',
                'user_id' => $model->user_id ?? auth()->id(),
                'field14' => $requestData['professional_development']['field14'][$key],
                'field15' => $requestData['professional_development']['field15'][$key],
            ];

            $model
                ->professional_development()
                ->create($data)
            ;
        }

        // Опыт работы в органах власти
        $model
            ->work_experience_in_government()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['work_experience_in_government']['field16'] as $key => $start) {

            $data = [
                'group' => 'work_experience_in_government',
                'user_id' => $model->user_id ?? auth()->id(),
                'field16' => $requestData['work_experience_in_government']['field16'][$key],
                'field17' => $requestData['work_experience_in_government']['field17'][$key],
                'field18' => $requestData['work_experience_in_government']['field18'][$key],
            ];

            $model
                ->work_experience_in_government()
                ->create($data)
            ;
        }

        return redirect(route("applications.{$this->entity}.edit", $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LSApplication $model
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(LSApplication $model)
    {
        $this->authorize('update', $model);

        if (!auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']) && ((int) $model->user_id !== (int) auth()->id())) {

            return abort(404);
        }

        $contest = $this->getContest($model, 'ls');
        $user = auth()->user();
        $nominations = config("app.{$this->entity}_applications.nominations", []);

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
            ->setCollect('nominations', $nominations)
            ->setCollect('breadcrumbs', (string)View::make("partial.breadcrumb", $this->getCollect())->render())
        ;

        return view("applications." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LSApplication $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LSApplication $model)
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

        // Дополнительное образование
        $model
            ->additional_education()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['additional_education']['field11'] as $key => $start) {

            $data = [
                'group' => 'additional_education',
                'user_id' => $model->user_id ?? auth()->id(),
                'field11' => $requestData['additional_education']['field11'][$key],
                'field12' => $requestData['additional_education']['field12'][$key],
                'field13' => $requestData['additional_education']['field13'][$key],
            ];

            $model
                ->additional_education()
                ->create($data)
            ;
        }

        // Повышение квалификации
        $model
            ->professional_development()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['professional_development']['field14'] as $key => $start) {

            $data = [
                'group' => 'professional_development',
                'user_id' => $model->user_id ?? auth()->id(),
                'field14' => $requestData['professional_development']['field14'][$key],
                'field15' => $requestData['professional_development']['field15'][$key],
            ];

            $model
                ->professional_development()
                ->create($data)
            ;
        }

        // Опыт работы в органах власти
        $model
            ->work_experience_in_government()
            ->where('user_id', $model->user_id)
            ->delete()
        ;

        foreach ($requestData['work_experience_in_government']['field16'] as $key => $start) {

            $data = [
                'group' => 'work_experience_in_government',
                'user_id' => $model->user_id ?? auth()->id(),
                'field16' => $requestData['work_experience_in_government']['field16'][$key],
                'field17' => $requestData['work_experience_in_government']['field17'][$key],
                'field18' => $requestData['work_experience_in_government']['field18'][$key],
            ];

            $model
                ->work_experience_in_government()
                ->create($data)
            ;
        }

        return redirect(route("applications.{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param LSApplication $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, LSApplication $model)
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
     * @param LSApplication $model
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, LSApplication $model)
    {
        $this->authorize('restore', $model);

        if ($request->isMethod('GET')) {

            $models = $model->filtering();

            if (auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee'])) {

                $models = $models
                    ->onlyTrashed()
                    ->orderBy($model->columnSorting, $model->directionSorting)
                    ->paginate($model->totalRecords)
                ;
            } else {

                $models = $models
                    ->onlyTrashed()
                    ->where('user_id', auth()->id())
                    ->orderBy($model->columnSorting, $model->directionSorting)
                    ->paginate($model->totalRecords)
                ;
            }

            $redirectRouteName = __FUNCTION__;

            $models_count = $models->count();

            $this->setCommonDataApplication($model);

            $users = User::where('is_active', 1)->get()->pluck('first_name', 'id')->toArray();
            $contests = Contest::where('is_active', 1)->get()->pluck('name', 'id')->toArray();
            $municipalities = $this->getApplicationMunicipalities();
            $nominations = config("app.{$this->entity}_applications.nominations", []);
            $statuses = config("app.{$this->entity}_applications.statuses", []);

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
                ->setCollect('users', $users)
                ->setCollect('municipalities', $municipalities)
                ->setCollect('nominations', $nominations)
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
        ];
    }

    public function getPublishedRules()
    {
        return [
            'user_id'                                       => 'required',
            'contest_id'                                    => 'required',
        ];
    }

    public function getMessages()
    {
        return [];
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
}
