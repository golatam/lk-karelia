<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

trait ParentCRUDApplicationTrait
{
    /**
     * -----------------------------------
     * Вывод списка элементов.
     * Display a listing of the resource.
     * -----------------------------------
     *
     * @param Request $request
     * @param Model $model
     * @param bool $hasAuthorize
     * @throws AuthorizationException
     */
    public function indexParent(Request $request, Model $model, $hasAuthorize = true)
    {
        if ($hasAuthorize) {

            $this->authorize('view', $model);
        }

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

        $redirectRouteName = 'index';

        $models_count = $models->total();

        $this->setCommonDataApplication($model);

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $models)
            ->setCollect('models_count', $models_count)
            ->setCollect('redirectRouteName', $redirectRouteName)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
        ;
    }

    /**
     * ---------------------------------------------
     * Показать форму для создания нового элемента.
     * Show the form for creating a new resource.
     * ---------------------------------------------
     *
     * @param Model $model
     * @throws AuthorizationException
     */
    public function createParent(Model $model)
    {
        $this->authorize('create', $model);

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleCreate'),
                        'url' => route("applications.{$this->entity}.create")
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
        ;
    }

    /**
     * --------------------------------------------------
     * Сохранение вновь созданного элемента в хранилище.
     * Store a newly created resource in storage.
     * --------------------------------------------------
     *
     * @param Request $request
     * @param Model $model
     * @param array $rules
     * @return Model|RedirectResponse
     * @throws AuthorizationException
     */
    public function storeParent(Request $request, Model $model, array $rules = []): Model|RedirectResponse
    {
        $this->authorize('create', $model);

        $this->prepareForValidation($request);

        $requestData = $request->all();

        $validator = Validator::make($requestData, $model->rules($requestData['status'], $rules), $model->messages(), $model->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model = $model->create($requestData);

        $this->saveMatrix($model, $requestData);

        return $model;
    }

    /**
     * --------------------------------------------------
     * Показать форму для редактирования элемента.
     * Show the form for editing the specified resource.
     * --------------------------------------------------
     *
     * @param Model $model
     * @throws AuthorizationException
     */
    public function editParent(Model $model)
    {
        $this->authorize('update', $model);

        if (!auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']) && ((int) $model->user_id !== (int) auth()->id())) {

            return abort(404);
        }

        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleEdit'),
                        'url' => route("applications.{$this->entity}.edit", $model)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
        ;
    }

    /**
     * ------------------------------------------
     * Обновление элемента в хранилище.
     * Update the specified resource in storage.
     * ------------------------------------------
     *
     * @param Request $request
     * @param Model $model
     * @param array $rules
     * @return Model|RedirectResponse
     * @throws AuthorizationException
     */
    public function updateParent(Request $request, Model $model, array $rules = []): Model|RedirectResponse
    {
        $this->authorize('update', $model);

        $this->prepareForValidation($request);

        $requestData = $request->all();

        $validator = Validator::make($requestData, $model->rules($requestData['status'], $rules), $model->messages(), $model->attributes());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model->update($requestData);

        $this->saveMatrix($model, $requestData);

        return $model;
    }

    /**
     * --------------------------------------------
     * Удаление элементов из хранилища.
     * Remove the specified resource from storage.
     * --------------------------------------------
     *
     * @param Request $request
     * @param Model $model
     * @param array $result
     * @return array
     * @throws AuthorizationException
     */
    public function destroyParent(Request $request, Model $model, $result = []): array
    {
        $this->authorize('delete', $model);

        $ids = $request->input('ids');

        try{

            if (is_array($ids) && !empty($ids)) {

                foreach($ids as $k => $id) {

                    $foundModel = $model->find($id);

                    if ($foundModel) {

                        $foundModel->delete();

                        $result[] = [
                            'success' => true,
                            'id' => $id,
                            'message' => __("common.entry_successfully_deleted", ['id' => $id]),
                        ];
                    } else {

                        $result[] = [
                            'success' => false,
                            'id' => $id,
                            'message' => __("common.entry_missing", ['id' => $id]),
                        ];
                    }
                }
            } else {

                $result[] = [
                    'success' => false,
                    'message' => __("common.no_data_was_sent_for_deletion"),
                ];
            }
        } catch (\Exception $e) {

            $result[] = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return ['data' => $result];
    }

    /**
     * ---------------------------------------------
     * Восстановите элементов из хранилища.
     * Restore the specified resource from storage.
     * ---------------------------------------------
     *
     * @param Request $request
     * @param Model $model
     * @param array $result
     * @return array
     * @throws AuthorizationException
     */
    public function restoreParent(Request $request, Model $model, $result = [])
    {
        $this->authorize('restore', $model);

        if ($request->isMethod('GET')) {

            $models = $model->filtering();

            $models = $models
                ->onlyTrashed()
                ->orderBy($model->columnSorting, $model->directionSorting)
                ->paginate($model->totalRecords)
            ;

            $redirectRouteName = 'restore';

            $this->setCommonData($model);

            $this
                ->setCollect([
                    'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                        [
                            'name' => $this->getCollect('titleRestore'),
                            'url' => route("{$this->prefix}{$this->entity}.restore")
                        ],
                    ]),
                ])
                ->setCollect('model', $model)
                ->setCollect('models', $models)
                ->setCollect('redirectRouteName', $redirectRouteName)
                ->setCollect('breadcrumbs', (String) View::make("partial.breadcrumb", $this->getCollect())->render())
            ;
        } else {

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

                            $result[] = [
                                'success' => true,
                                'id' => $id,
                                'message' => __("common.entry_successfully_restored", ['id' => $id]),
                            ];
                        } else {

                            $result[] = [
                                'success' => false,
                                'id' => $id,
                                'message' => __("common.entry_missing", ['id' => $id]),
                            ];
                        }
                    }
                } else {

                    $result[] = [
                        'success' => false,
                        'message' => __("common.no_data_was_sent_for_restoration"),
                    ];
                }
            } catch (\Exception $e) {

                $result[] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }

            return ['data' => $result];
        }
    }
}
