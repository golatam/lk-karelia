<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use App\Models\Permission;

class PermissionController extends CommonController
{
    protected $entity = 'permissions';

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

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
     * @param Permission $model
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Permission $model)
    {
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
}
