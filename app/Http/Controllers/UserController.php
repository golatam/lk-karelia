<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Register;
use App\Models\Role;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends CommonController
{
    use ImageTrait;

    protected $entity = 'users';

    public function __construct()
    {
        parent::__construct();

        $this->roles = Role::all()->pluck('name', 'id');

        $this->middleware(function ($request, $next) {

            View::share('entity', $this->entity);

            $this
                ->setCollect([
                    'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                        [
                            'name' => $this->getCollect('titleIndex'),
                            'url' => route("{$this->entity}.index")
                        ],
                ]),
                'roles' => $this->roles,
            ]);

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(User $model)
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

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view(__FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(User $model)
    {
        $this->authorize('create', $model);

        $municipalities = Municipality::all()->pluck('name', 'id');
        $registers = [];

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
            ->setCollect('registers', $registers)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view(__FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, User $model)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, ['password' => ['required', Password::min(8)]] , $this->messages(), $this->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!empty($requestData['password'])) {

            $requestData['password'] = Hash::make($requestData['password']);
        }

        $request->merge($requestData);

        $rules = ['password' => 'required|min:8'];

        $this->prepareForValidation($request);

        $requestData = $request->all();

        $validator = Validator::make($requestData, $this->rules($rules), $this->messages(), $this->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model = $model->create($requestData);

        if (!isset($requestData['roles'])) {

            $requestData['roles'] = [];
        }

        $model->roles()->sync($requestData['roles']);

        return redirect(route("{$this->entity}.edit", $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $model)
    {
        $this->authorize('update', $model);

        $id = $model->id;
        $columnName = "avatar";
        $entity = $this->entity;
        $imageFilePath = $model->{$columnName};
        $modelFullName = $model->getMorphClass();
        $name = __("{$this->entity}.{$columnName}");

        $avatar = (String) View::make("partial.avatar")
            ->with('id', $id)
            ->with('name', $name)
            ->with('entity', $entity)
            ->with('columnName', $columnName)
            ->with('modelFullName', $modelFullName)
            ->with('imageFilePath', $imageFilePath)
            ->render();

        $municipalities = Municipality::all()->pluck('name', 'id');
        $registers = $model->tosNames;

//        dd($model->roles);

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
            ->setCollect('registers', $registers)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('avatar', $avatar)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view(__FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, User $model)
    {
        $requestData = $request->all();

        if (empty($requestData['password'])) {

            unset($requestData['password']);

            $request->request->remove('password');
        } else {

            $validator = Validator::make($requestData, ['password' => ['required', Password::min(8)]] , $this->messages(), $this->attributes());

            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $requestData['password'] = Hash::make($requestData['password']);
        }

        $request->merge($requestData);

        $this->prepareForValidation($request);

        $rules = [
            'email' => [
                'required',
                'email:rfc,dns',
                Rule::unique($model->getTable())->ignore($model->email, 'email')
            ]
        ];

        $requestData = $request->all();

        $validator = Validator::make($requestData, $this->rules($rules), $this->messages(), $this->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model->update($requestData);

        if (!isset($requestData['roles'])) {

            $requestData['roles'] = [];
        }

        $model->roles()->sync($requestData['roles']);

        return redirect(route("{$this->entity}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param User $model
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, User $model)
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
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, User $model)
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

            $this->setCommonData($model);

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
                ->setCollect('redirectRouteName', $redirectRouteName)
                ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

            return view(__FUNCTION__, $this->getCollect());
        } else {

            $result = $this->restore_entity(
                $model,
                $request->input('ids')
            );

            return response()->json($result);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function profile()
    {
        $model = auth()->user();

        $id = $model->id;
        $columnName = "avatar";
        $entity = $this->entity;
        $imageFilePath = $model->{$columnName};
        $modelFullName = $model->getMorphClass();
        $name = __("{$this->entity}.{$columnName}");

        $avatar = (String) View::make("partial.avatar")
            ->with('id', $id)
            ->with('name', $name)
            ->with('entity', $entity)
            ->with('columnName', $columnName)
            ->with('modelFullName', $modelFullName)
            ->with('imageFilePath', $imageFilePath)
            ->render();

        $municipalities = Municipality::all()->pluck('name', 'id');
        $registers = $model->tosNames;

        $this
            ->setCollect([
                'titleProfile' => __("{$this->entity}.title_profile"),
            ])
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleProfile'),
                        'url' => route("{$this->entity}." . __FUNCTION__)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('avatar', $avatar)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('registers', $registers)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("{$this->entity}." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function profileUpdate(Request $request, User $model)
    {
        $model = auth()->user();

        $requestData = $request->all();

        if (empty($requestData['password'])) {

            unset($requestData['password']);
        } else {

            $requestData['password'] = Hash::make($requestData['password']);
        }

        $model->update($requestData);


        return redirect(route("{$this->entity}.profile"));
    }

    /**
     * ----------------------------------------------------
     * Предварительная обработка данных полученых из формы
     * ----------------------------------------------------
     *
     * @param Request $request
     */
    public function prepareForValidation(Request $request)
    {
        $requestData = $request->all();

        $request->merge($requestData);
    }

    /**
     * ---------------------------------------------------------
     * Получаем правила проверки, которые применяются к запросу
     * Get the validation rules that apply to the request
     * ---------------------------------------------------------
     *
     * @param array $rules
     * @return array
     */
    public function rules($rules = []): array
    {
        return array_merge([
            'last_name'             => '',                      // Фамилия
            'first_name'            => 'required',              // Имя
            'second_name'           => '',                      // Отчество
            'email'                 => 'required|unique:users', // Электронная почта
            'phone'                 => '',                      // Телефон
            'password'              => '',                      // Пароль
            'avatar'                => '',                      // Аватар
//            'role_id'               => '',              // ID роли
            'is_active'             => '',                      // Является активным
            'municipality_id'       => 'required',              // Муниципалитет
            'municipality_chief'    => '',                      // Глава (глава администрации) муниципального образования
            'municipality_phone'    => '',                      // Контактный телефон администрации
            'municipality_email'    => '',                      // E-mail администрации
            'municipality_address'  => '',                      // Адрес администрации
            'executor'              => '',                      // Исполнитель
            'executor_phone'        => '',                      // Контактный телефон исполнителя
            'executor_email'        => '',                      // E-mail исполнителя
        ], $rules);
    }

    /**
     * ----------------------------------------------------------------
     * Получаем сообщения об ошибках для определенных правил проверки.
     * Get the error messages for the defined validation rules.
     * ----------------------------------------------------------------
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'  => 'Поле ":attribute" обязательно к заполнению.',
            'unique'    => 'Поле ":attribute" должно быть уникально.',
            'min' => [
                'string' => 'Поле :attribute должно содержать не менее :min символов.',
            ],
        ];
    }

    /**
     * --------------------------------------------------------
     * Получаем пользовательские атрибуты для проверки ошибок.
     * Get custom attributes for validator errors.
     * --------------------------------------------------------
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'last_name'             => 'Фамилия',
            'first_name'            => 'Имя',
            'second_name'           => 'Отчество',
            'email'                 => 'Почта',
            'phone'                 => 'Телефон',
            'password'              => 'Пароль',
            'avatar'                => 'Фото',
//            'role_id'               => 'Роль',
            'is_active'             => 'Статус',
            'municipality_id'       => 'Муниципалитет',
            'municipality_chief'    => 'Глава (глава администрации) муниципального образования',
            'municipality_phone'    => 'Контактный телефон администрации',
            'municipality_email'    => 'E-mail администрации',
            'municipality_address'  => 'Адрес администрации',
            'executor'              => 'Исполнитель',
            'executor_phone'        => 'Контактный телефон исполнителя',
            'executor_email'        => 'E-mail исполнителя',
        ];
    }

}
