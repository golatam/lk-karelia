<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class PostController extends CommonController
{
    protected $entity = 'posts';
    protected object $httpClient;
    protected $auth = [
        'login' => '',
        'password' => '',
    ];
    protected string $login = '';
    protected string $password = '';
    protected string $baseUrl = '';
    protected bool $isAdmin = false;

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            View::share('entity', $this->entity);

            $this->isAdmin = auth()->check() ? auth()->user()->hasRole('su') : $this->isAdmin;

            $this->login = config('app.wordpress.api.karelia_initiatives.login');
            $this->password = config('app.wordpress.api.karelia_initiatives.password');
            $this->baseUrl = config('app.wordpress.api.karelia_initiatives.base_url');

            $this->httpClient = Http::withBasicAuth($this->login, $this->password)
                ->withHeaders([
                    'X-WP-Total', 'X-WP-TotalPages'
                ])
                ->baseUrl($this->baseUrl)
            ;

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
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * X-WP-Total: общее количество записей в коллекции
     * X-WP-TotalPages: общее количество страниц, охватывающих все доступные записи.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $this->total;
        $params = [
            'page' => $page,
            'per_page' => $perPage,
            'order' => $request->input('order', 'desc'), // asc
            'orderby' => $request->input('orderby', 'id'),
            'post_type' => 'points',
            'status' => 'any',
        ];

        if (!$this->isAdmin) {
            $params['email'] = auth()->user()->email;
        }

        $response = $this->httpClient->get('points', $params);

        $models = (new LengthAwarePaginator($response->collect(), $response->header('X-WP-Total'), $perPage, $page, $options = []))->withPath(Paginator::resolveCurrentPath());

        $redirectRouteName = __FUNCTION__;

        $models_count = $models->total();

        $searching = config("app.{$this->entity}.fields_type.searching", []);
        $fieldsLinks = config("app.{$this->entity}.fields_type.links", []);
        $fieldsImages = config("app.{$this->entity}.fields_type.images", []);
        $fieldsConfig = config("app.{$this->entity}.fields_type.config", []);
        $fieldsCheckbox = config("app.{$this->entity}.fields_type.checkbox", []);
        $fieldsRelationships = config("app.{$this->entity}.fields_type.relationships", []);
        $fieldsFull = config("app.{$this->entity}.fields_type.full", []);
        $fieldsSorting = config("app.{$this->entity}.fields_type.sorting", []);
        $fieldsSelected = config("app.{$this->entity}.fields_selected_default", []);
        $fieldsForShowing = config("app.{$this->entity}.fields_for_showing", []);

        $this
            ->setCollect('fieldsSelected', $fieldsSelected)
            ->setCollect('fieldsForShowing', $fieldsForShowing)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('modelFullName', '')
            ->setCollect('filter', optional(session("{$this->entity}.filter")))
            ->setCollect('fieldsFull', $fieldsFull)
            ->setCollect('searching', $searching)
            ->setCollect('fieldsLinks', $fieldsLinks)
            ->setCollect('fieldsConfig', $fieldsConfig)
            ->setCollect('fieldsImages', $fieldsImages)
            ->setCollect('fieldsSorting', $fieldsSorting)
            ->setCollect('fieldsCheckbox', $fieldsCheckbox)
            ->setCollect('fieldsRelationships', $fieldsRelationships)
            ->setCollect('column', $params['orderby'])
            ->setCollect('direction', $params['order'])
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("{$this->entity}." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Contest $model
     * @return \Illuminate\Contracts\View\View
     */
    public function create($model = [])
    {
        $projects = config("app.{$this->entity}.projects", []);
        $projectTypes = config("app.{$this->entity}.project_types", []);
        $municipalities = config("app.{$this->entity}.municipalities", []);
        $authUser = auth()->user();

        $image = '';

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
            ->setCollect('projects', $projects)
            ->setCollect('projectTypes', $projectTypes)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('authUser', $authUser)
            ->setCollect('image', $image)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("{$this->entity}." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, $this->rules(), $this->messages(), $this->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resultUploadFile = [];
        if ($request->hasFile('image')) {
            $resultUploadFile = $this->uploadFile($request, 'image');
        }

        $params = [
            'title' => Arr::get($requestData, 'title.rendered', ''),
            'status' => 'pending',
            'acf' => [
                'project' => Arr::get($requestData, 'acf.project', ''),
                'year' => Arr::get($requestData, 'acf.year', ''),
                'district' => Arr::get($requestData, 'acf.district', ''),
                'type_proj' => Arr::get($requestData, 'acf.type_proj', ''),
                'coordinates' => Arr::get($requestData, 'acf.coordinates', ''),
                'description' => Arr::get($requestData, 'acf.description', ''),
                'budget' => Arr::get($requestData, 'acf.budget', ''),
                'budget_rk' => Arr::get($requestData, 'acf.budget_rk', ''),
                'budget_priv' => Arr::get($requestData, 'acf.budget_priv', ''),
                'budget_mo' => Arr::get($requestData, 'acf.budget_mo', ''),
                'email' => Arr::get($requestData, 'acf.email', ''),
            ],
        ];

        if (!empty($resultUploadFile) && in_array($resultUploadFile['status'], ['success']) && $featuredImageId = Arr::get($resultUploadFile, 'response.id', null)) {
            $params['featured_media'] = $featuredImageId;
        }

        $response = $this->httpClient->post("points", $params);

        $model = $response->collect()->toArray();

        return redirect(route("{$this->entity}.edit", Arr::get($model, 'id', '')));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contest $model
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($postId, $model = [])
    {
        $params = [];

        $response = $this->httpClient->get("points/{$postId}", $params);
        $model = $response->collect()->toArray();

        if (!$this->isAdmin && !in_array(auth()->user()->email, [Arr::get($model, 'acf.email', '')])) {
            return abort(403);
        }

        $imagePath = Arr::get($model, '_links.wp:featuredmedia.0.href', '');

        $image = !empty($imagePath) ? Arr::get(Http::withBasicAuth($this->login, $this->password)->get($imagePath)->collect()->toArray(), 'media_details.sizes.news-small-image.source_url', '') : '';

        $projects = config("app.{$this->entity}.projects", []);
        $projectTypes = config("app.{$this->entity}.project_types", []);
        $municipalities = config("app.{$this->entity}.municipalities", []);
        $authUser = auth()->user();

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleEdit'),
                        'url' => route("{$this->entity}." . __FUNCTION__, Arr::get($model, 'id', 0))
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('projects', $projects)
            ->setCollect('projectTypes', $projectTypes)
            ->setCollect('municipalities', $municipalities)
            ->setCollect('authUser', $authUser)
            ->setCollect('image', $image)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render());

        return view("{$this->entity}." . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $postId
     * @return RedirectResponse
     */
    public function update(Request $request, $postId)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, $this->rules(), $this->messages(), $this->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resultUploadFile = [];
        if ($request->hasFile('image')) {
            $resultUploadFile = $this->uploadFile($request, 'image');
        }

        $params = [
            'title' => Arr::get($requestData, 'title.rendered', ''),
            'status' => 'pending',
            'acf' => [
                'project' => Arr::get($requestData, 'acf.project', ''),
                'year' => Arr::get($requestData, 'acf.year', ''),
                'district' => Arr::get($requestData, 'acf.district', ''),
                'type_proj' => Arr::get($requestData, 'acf.type_proj', ''),
                'coordinates' => Arr::get($requestData, 'acf.coordinates', ''),
                'description' => Arr::get($requestData, 'acf.description', ''),
                'budget' => Arr::get($requestData, 'acf.budget', ''),
                'budget_rk' => Arr::get($requestData, 'acf.budget_rk', ''),
                'budget_priv' => Arr::get($requestData, 'acf.budget_priv', ''),
                'budget_mo' => Arr::get($requestData, 'acf.budget_mo', ''),
                'email' => Arr::get($requestData, 'acf.email', ''),
            ],
        ];

        if (!empty($resultUploadFile) && in_array($resultUploadFile['status'], ['success']) && $featuredImageId = Arr::get($resultUploadFile, 'response.id', null)) {
            $params['featured_media'] = $featuredImageId;
        }

        $response = $this->httpClient->post("points/{$postId}", $params);

        $model = $response->collect()->toArray();

        return redirect(route("{$this->entity}.edit", Arr::get($model, 'id', '')));
    }

    public function uploadFile(Request $request, $fileField, $result = [])
    {
        $curl = curl_init();

        $file = $request->file("{$fileField}");

        $data = [
            CURLOPT_URL => trim($this->baseUrl, '/') . "/media",
            CURLOPT_USERPWD => "{$this->login}:{$this->password}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "content-disposition: attachment; filename={$file->getClientOriginalName()}",
                "content-type: {$file->getClientMimeType()}",
            ],
            CURLOPT_POSTFIELDS => $file->getContent(),
        ];

        curl_setopt_array($curl, $data);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            $result['status'] = 'error';
            $result['error'] = $error;
        } else {
            $result['status'] = 'success';
            $result['response'] = json_decode($response, true);
        }

        return $result;
    }

    public function removeFile(Request $request, $result = [])
    {
        $postId = $request->input('postId', null);
        if (!$postId) {
            return response(['message' => 'Не был передан ID поста.'], 404);
        }

        $params = [];
        $postModel = $this->httpClient->get("points/{$postId}", $params)->collect()->toArray();

        $featuredImageId = Arr::get($postModel, 'featured_media', null);
        if (!$featuredImageId) {
            return response(['message' => 'Нельзя удалить картинку у поста, картинка отсутствует.'], 404);
        }

        $deleteMedia = $this->httpClient->delete("media/{$featuredImageId}", ['force'=>true])->collect()->toArray();

        if (!Arr::get($deleteMedia, 'deleted', false)) {
            return response(['message' => 'Не удалось удалить картинку поста.'], 500);
        }

        $response = $this->httpClient->post("points/{$postId}", ['featured_media' => $featuredImageId])->collect()->toArray();

        if (Arr::get($response, 'featured_media', true)) {
            return response(['message' => 'Не удалось удалить картинку у поста.'], 500);
        }

        $result['message'] = "Картинка успешно удалена у поста с ID {$postId}";

        return response($result, 200);
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'acf.project' => '',
            'acf.type_proj' => '',
            'acf.coordinates' => '',
            'acf.description' => '',
            'acf.budget' => '',
            'acf.budget_rk' => '',
            'acf.budget_priv' => '',
            'acf.budget_mo' => '',
            'acf.district' => '',
            'acf.year' => '',
            'acf.email' => '',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Название проекта',
            'acf.project' => 'Проект',
            'acf.type_proj' => 'Тип пректа',
            'acf.coordinates' => 'Координаты реализации проекта',
            'acf.description' => 'Описание проекта',
            'acf.budget' => 'Бюджет (общий)',
            'acf.budget_rk' => 'Бюджет РК',
            'acf.budget_priv' => 'Бюджет привлекаемый (софинансирование)',
            'acf.budget_mo' => 'Бюджет местный (МО)',
            'acf.district' => 'Район',
            'acf.year' => 'Год создания',
            'acf.email' => 'Год создания',
        ];
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
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
                session(["{$this->entity}.page" => $request->input('page')]);
            }

            if (!$request->filled('pagination')) {

                session(["{$this->entity}.page" => 1]);
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
                session(["{$this->entity}.page" => null]);
            }

            return redirect(route("{$this->entity}.{$request->input('method')}") . $path);

            // Если метод отправки post
        } else {

            // Удаляем токен из данных
            $data = collect($request->except(['_token']))
                ->filter(function ($value, $key){

                    return !in_array($value, [null, [], '']);
                })
                ->toArray()
            ;

            // Пишем данные в сессию
            session(["{$this->entity}" => $data]);

            return redirect(route("{$this->entity}.{$request->input('method')}") . $path);
        }
    }
}
