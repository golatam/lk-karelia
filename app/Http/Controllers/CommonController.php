<?php

namespace App\Http\Controllers;

use App\Extensions\MenuBuilder;
use App\Models\Contest;
use App\Models\Municipality;
use App\Models\Register;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

class CommonController extends Controller
{
    private $collect;

    protected $required;

    protected $directions = ['asc', 'desc'];

    protected $columnDefault = 'id';

    protected $directionDefault = 'desc';

    protected $total = 20;

    protected $entity = '';

    protected $miniatures = [];

    protected $imagePath = "uploads/images";

    protected $filePath = "uploads/files";

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            MenuBuilder::build();

            View::share('user', optional(auth()->user()));
            View::share('sidebar', app('config')->get("app.common.menus.sidebar"));
            View::share('directions', collect($this->directions));
            View::share('columnDefault', $this->columnDefault);
            View::share('directionDefault', $this->directionDefault);
            View::share('yandexMapToken', config('app.common.yandex_map_token'));

//          тоже присутствует в каждом контроллере
            View::share('entity', $this->entity);
//            стандартные заголовки подменяются в каждой сущности
            $this
                ->setCollect([
                    'titleIndex' => __("{$this->entity}.title_index"),
                    'titleRestore' => __("{$this->entity}.title_restore"),
                    'titleCreate' => __("{$this->entity}.title_create"),
                    'titleEdit' => __("{$this->entity}.title_edit"),
                ])
            ;

            $this->setCollect([
                'breadcrumbs' => [
                    [
                        'name' => __("common.main"),
                        'url' => route('start')
                    ],
                ],
            ]);

            return $next($request);
        });
    }

    /**
     *
     * Метод для получения элементов коллекции (для отправки в шаблон)
     *
     * @param null $key
     * @return mixed
     */
    public function getCollect($key = null)
    {
        if (!$this->collect) {

            $this->collect = [];
        }

        if ($key instanceof Collection) {

            return array_intersect_key($this->collect, array_flip($key->toArray()));
        } elseif (is_array($key)) {

            return array_intersect_key($this->collect, array_flip($key));
        } elseif ($key) {

            return $this->collect[$key];
        } else {

            return $this->collect;
        }
    }

    /**
     * Метод для добавления элементов в коллекцию (для отправки в шаблон)
     *
     * @param $key
     * @param null $value
     * @return CommonController
     */
    public function setCollect($key, $value = null)
    {
        if (!$this->collect) {

            $this->collect = [];
        }

        if ($key instanceof Collection) {

            $this->collect = array_merge($this->collect, $key->toArray());
        } elseif (is_array($key)) {

            $this->collect = array_merge($this->collect, $key);
        } else {

            $this->collect[$key] = $value;
        }

        return $this;
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

    public function setCommonData($model)
    {
        $modelFullName = $model->getMorphClass();

        $searching = config("app.{$this->entity}.fields_type.searching", []);
        $fieldsLinks = config("app.{$this->entity}.fields_type.links", []);
        $fieldsImages = config("app.{$this->entity}.fields_type.images", []);
        $fieldsConfig = config("app.{$this->entity}.fields_type.config", []);
        $fieldsCheckbox = config("app.{$this->entity}.fields_type.checkbox", []);
        $fieldsRelationships = config("app.{$this->entity}.fields_type.relationships", []);
        $fieldsFull = config("app.{$this->entity}.fields_type.full", []);
        $fieldsSorting = config("app.{$this->entity}.fields_type.sorting", []);

        $this
            ->setCollect('modelFullName', $modelFullName)
            ->setCollect('filter', optional(session("{$this->entity}.filter")))
            ->setCollect('fieldsFull', $fieldsFull)
            ->setCollect('searching', $searching)
            ->setCollect('fieldsLinks', $fieldsLinks)
            ->setCollect('fieldsConfig', $fieldsConfig)
            ->setCollect('fieldsImages', $fieldsImages)
            ->setCollect('fieldsSorting', $fieldsSorting)
            ->setCollect('fieldsCheckbox', $fieldsCheckbox)
            ->setCollect('fieldsRelationships', $fieldsRelationships)
            ->setCollect('column', $model->columnSorting)
            ->setCollect('direction', $model->directionSorting)
        ;
    }

    public function setCommonDataApplication($model)
    {
        $modelFullName = $model->getMorphClass();

        $searching = config("app.{$this->entity}_applications.fields_type.searching", []);
        $fieldsLinks = config("app.{$this->entity}_applications.fields_type.links", []);
        $fieldsImages = config("app.{$this->entity}_applications.fields_type.images", []);
        $fieldsConfig = config("app.{$this->entity}_applications.fields_type.config", []);
        $fieldsCheckbox = config("app.{$this->entity}_applications.fields_type.checkbox", []);
        $fieldsRelationships = config("app.{$this->entity}_applications.fields_type.relationships", []);
        $fieldsFull = config("app.{$this->entity}_applications.fields_type.full", []);
        $fieldsSorting = config("app.{$this->entity}_applications.fields_type.sorting", []);

        $this
            ->setCollect('modelFullName', $modelFullName)
            ->setCollect('filter', optional(session("{$this->entity}.filter")))
            ->setCollect('fieldsFull', $fieldsFull)
            ->setCollect('searching', $searching)
            ->setCollect('fieldsLinks', $fieldsLinks)
            ->setCollect('fieldsConfig', $fieldsConfig)
            ->setCollect('fieldsImages', $fieldsImages)
            ->setCollect('fieldsSorting', $fieldsSorting)
            ->setCollect('fieldsCheckbox', $fieldsCheckbox)
            ->setCollect('fieldsRelationships', $fieldsRelationships)
            ->setCollect('column', $model->columnSorting)
            ->setCollect('direction', $model->directionSorting)
        ;
    }

    public function getContest($model, $type)
    {
        if (
            $model->exists
            && (auth()->user()->hasPermission('other.show_admin')
                || auth()->user()->hasPermission('other.show_committee'))
        ) {
            $contest = Contest::where('type', $type)
                ->where('id', $model->contest?->id)->first();
        } elseif (
            $model->exists
            && !(auth()->user()->hasPermission('other.show_admin')
                || auth()->user()->hasPermission('other.show_committee'))
        ) {
            $contest = Contest::where('type', $type)
                ->where('is_active', 1)
                ->where('id', $model->contest?->id)
                ->first();
        } else {
            $contest = Contest::where('type', $type)->where('is_active', 1)->first();
        }

        return $contest;
    }

    public function getApplicationModels($model, $userId = 0)
    {
        $models = $model->filtering();

//        $models = $models
//            ->orderBy($model->columnSorting, $model->directionSorting)
//            ->orderBy('total_application_points', 'desc')
//            ->orderBy('created_at', 'asc')
//        ;

        if ($userId) {

            $models = $models->where('user_id', $userId);
        }

        return $models;
    }

    public function getApplicationContests()
    {
        $contests = Contest::where('is_active', 1)
            ->get()
            ->pluck('name', 'id')
            ->toArray()
        ;

        return $contests;
    }

    public function getApplicationMunicipalities()
    {
        $municipalities = Municipality::all()
            ->pluck('name', 'id')
            ->toArray()
        ;

        return $municipalities;
    }

    public function getApplicationUsers()
    {
        $users = User::where('is_active', 1)
            ->get()
            ->pluck('first_name', 'id')
            ->toArray()
        ;

        return $users;
    }

    public function getApplicationStatuses()
    {
        return config("app.{$this->entity}_applications.statuses", []);
    }

    public function getApplicationRegisters()
    {
        return Register::all()->pluck('name_according_charter', 'id')->toArray();
    }

    public function saveMatrix($model, $requestData)
    {

    }

    public function years($type): array
    {
        return Contest::orderBy('year_of_competition', 'asc')->where('type', $type)->get()->pluck('year_of_competition', 'id')->toArray();
    }

    public function utf8ForXml($string): array|string|null
    {
        return htmlspecialchars(preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string));
    }

    public function createDirectory($path)
    {
        try {
//            mkdir($path, 0777, true);

            if (!file_exists($path)) {
                \File::makeDirectory($path, 0777, true);
            } else {
                \File::chmod($path, 0777);
            }
        } catch (\Exception $e) {

        }

    }
}
