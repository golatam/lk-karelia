<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AjaxController extends CommonController
{
    use ImageTrait;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            // ini_set('memory_limit', '512M');     // OK - 512MB
            // ini_set('upload_max_filesize', '200M');     // OK - 200MB
            ini_set('memory_limit', 512000000);  // OK - 512MB
            ini_set('upload_max_filesize', 10000000);  // OK - 10MB

            return $next($request);
        });
    }

    public function index(Request $request, $action)
    {
        if (method_exists($this, $action)) {

            return $this->$action($request);
        } else {

            abort(404);
        }
    }

    // Поиск по базе
    public function searching(Request $request) {
        $this->entity = $request->input('db');
        $search = $request->input('s');
        $columns = config("common.{$this->entity}.searching", []);

        $query = DB::table($this->entity)->select($columns);

        if($request->input('byID')) {

            $query->where('id', '=', $search)->whereNull('deleted_at');
        } else {

            $i = 1;
            foreach ($columns as $column) {
                if($i == 1) {
                    $query->where($column, 'like', '%' . $search . '%')->whereNull('deleted_at');
                } else {
                    $query->orWhere($column, 'like', '%' . $search . '%')->whereNull('deleted_at');
                }
                $i++;
            }
        }

        $model = $query->get();

        $response = ['data' => $model, 'entity' => $this->entity, 'type' => '/edit'];

        if($this->entity === 'questionnaires') {
            $response['types'] = config("common.{$this->entity}.types");
        }

        return response()->json($response);
    }

    // Добавление одного фото
    public function uploadImage(Request $request)
    {
        $result = [];

        try {

            if ($request->hasFile('files')) {

                $id = $request->input('id');
                $this->entity = $request->input('entity');
                $columnName = $request->input('columnName');
                $modelFullName = $request->input('modelFullName');

                $model = (new $modelFullName())->find($id);

                if ($model) {

                    $pathImage = "{$this->imagePath}/{$this->entity}/{$id}";

                    $this->miniatures = $this->miniatures($model->getTable());

                    foreach ($this->miniatures as $miniatureKey => $miniatureValue) {

                        $this->{$miniatureKey} = $miniatureValue;
                    }

                    $this->createDirectory($pathImage);

                    $file = $request->file('files')[0];
                    $path = public_path($pathImage);
                    $fileName = $this->createImage($file, $path);
                    $imageFilePath = "/" . trim($pathImage, "/") . "/{$fileName}";

                    if ($model->methodExists('entities')) {

                        $modelI18n = $model
                            ->entities()
                            ->firstOrNew([
                                'locale' => get_current_locale(),
                            ])
                        ;

                        $modelI18n
                            ->fill(['value' => $imageFilePath])
                            ->save()
                        ;
                    } else {

                        $model->{$columnName} = $imageFilePath;
                        $model->save();
                    }

                    $result['imageHeader'] = false;

                    if ($model instanceof \App\Models\User && (int) auth()->id() === (int) $id) {

                        $result['imageHeader'] = true;
                    }

                    $result['imageFilePath'] = $imageFilePath;
                    $result['miniature'] = image_path($imageFilePath, 'thumbnail');
                    $result['success'] = true;
                    $result['message'] = __('common.file_successfully_created_and_written_to_database');
                } else {

                    $result['success'] = false;
                    $result['message'] = __('common.entry_missing', ['id' => $id]);
                }
            } else {

                $result['success'] = false;
                $result['message'] = __('common.no_file');
            }
        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    // Удаление одного фото
    public function deleteImage(Request $request)
    {
        $result = [];

        try {

            $id = $request->input('id');
            $this->entity = $request->input('entity');
            $columnName = $request->input('columnName');
            $modelFullName = $request->input('modelFullName');
            $imageFilePath = $request->input('imageFilePath');

            $model = (new $modelFullName())->find($id);

            if ($model) {

                $this->miniatures = $this->miniatures($model->getTable());

                if (!empty($imageFilePath) && file_exists(public_path($imageFilePath))) {

                    $this->removeLink($imageFilePath);
                }

                if ($model->methodExists('entities')) {

                    $modelI18n = $model
                        ->entities()
                        ->firstOrNew([
                            'locale' => get_current_locale(),
                        ])
                    ;

                    $modelI18n
                        ->fill(['value' => null])
                        ->save()
                    ;
                } else {

                    $model->{$columnName} = null;
                    $model->save();
                }

                $result['imageHeader'] = false;

                if ($model instanceof \App\Models\User && (int) auth()->id() === (int) $id) {

                    $result['imageHeader'] = true;
                }

                $result['success'] = true;
                $result['message'] = __('common.file_successfully_deleted');
            } else {

                $result['success'] = false;
                $result['message'] = __('common.entry_missing', ['id' => $id]);
            }
        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    // Переключение статуса
    public function activeToggle(Request $request)
    {
        $result = [];

        try {

            $id = (int) $request->input('id');
            $isActive = (int) $request->input('is_active');
            $name = $request->input('name');
            $entity = $request->input('entity');
            $modelFullName = $request->input('modelFullName');

            $model = (new $modelFullName())->find($id);

//            $model = DB::table($entity)->where('id', $id)->first();

            if ($model) {

                $model->{$name} = $isActive;
                $model->save();

//                DB::table($entity)->where('id', $id)->update(["{$name}" => $isActive]);

                $result['success'] = true;
                $result['message'] = "Статус у записи с id {$id} успешно изменен!";
            } else {

                $result['success'] = false;
                $result['message'] = "Запись с id {$id} отсутствует!";
            }
        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    public function multiExplode ($delimiters, $string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);

        return  collect($launch)
            ->filter()
            ->values()
            ->toArray()
        ;
    }

    public function stdClass(Request $request)
    {
        $requestData = $this->requestData($request);
        $stdClass = new \stdClass();

        foreach ($requestData as $key => $requestDatum) {

            $stdClass->{$key} = $requestDatum;
        }

        return $stdClass;
    }

    // Добавление множества фото
    public function uploadImages(Request $request, $result = [], $images = []): \Illuminate\Http\JsonResponse
    {
        try {

            if ($request->hasFile('files')) {

                $id = $request->input('id');
                $morphClass = $request->input('morphClass');
                $group = $request->input('group');
                $hasDescription = $request->input('hasDescription');
                $typeDescription = $request->input('typeDescription');
                $limit = $request->input('limit');

                $model = (new $morphClass())->find($id);

                if ($model) {

                    $pathImage = "/{$this->imagePath}/{$model->entity()}/{$id}";

                    $this->miniatures = $this->miniatures($model->getTable());

                    foreach ($this->miniatures as $miniatureKey => $miniatureValue) {

                        $this->{$miniatureKey} = $miniatureValue;
                    }

                    $this->createDirectory($pathImage);

                    $i = 0;
                    $countImages = $model->{$group}->count();

                    if (!!$limit && $limit > $countImages) {

                        $i = ($limit - $countImages);
                    }

                    foreach ($request->file('files') as $key => $file) {

                        if (!!$limit && ($i < ($key + 1))) {

                            break;
                        }

                        $path = public_path($pathImage);
                        $fileName = $this->createImage($file, $path);
                        $imageFilePath = "/" . trim($pathImage, "/") . "/{$fileName}";

                        $image = $model
                            ->images()
                            ->firstOrNew([
                                'path' => $imageFilePath,
                                'group' => $group,
                            ])
                        ;

                        $image->position = 999;
                        $image->save();

                        $images[] = [
                            'id' => $image->id,
                            'group' => $image->group,
                            'position' => $image->position,
                            'path' => asset(image_path("{$imageFilePath}", "thumbnail")),
                        ];
                    }

                    $result['id'] = $id;
                    $result['images'] = $images;
                    $result['morphClass'] = $morphClass;
                    $result['group'] = $group;
                    $result['hasDescription'] = (bool) $hasDescription;
                    $result['typeDescription'] = $typeDescription;
                    $result['success'] = true;
                    $result['message'] = __('common.file_successfully_created_and_written_to_database');
                } else {

                    $result['success'] = false;
                    $result['message'] = __('common.entry_missing', ['id' => $id]);
                }
            } else {

                $result['success'] = false;
                $result['message'] = __('common.no_file');
            }
        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    // Удаление фото множества
    public function removeImages(Request $request, $result = []): \Illuminate\Http\JsonResponse
    {
        try {

            $id = $request->input('id');
            $imageId = $request->input('imageId');
            $morphClass = $request->input('morphClass');
            $group = $request->input('group');

            $model = (new $morphClass())->find($id);

            if ($model) {

                $this->miniatures = $this->miniatures($model->getTable());

                $image = $model->images()->where('id', $imageId)->first();

                if ($image && file_exists(public_path("{$image->path}"))) {

                    $this->removeLink($image->path);
                }

                $model->images()->where('id', $image->id)->delete();

                $result['success'] = true;
                $result['message'] = 'Файл успешно удален!';
            } else {

                $result['success'] = false;
                $result['message'] = __('common.entry_missing', ['id' => $id]);
            }

        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    // Смена позиции у фото
    public function changePositionImages(Request $request, $result = [])
    {
        try {

            $images = $request->input('images');

            foreach ($images as $key => $image) {

                Image::where('id', $image['imageId'])->update(['position' => $key]);
            }

            $result['success'] = true;
            $result['message'] = 'Позиция успешно поменена!';
        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    // Добавление множества файлов
    public function uploadFiles(Request $request, $result = [], $files = []): \Illuminate\Http\JsonResponse
    {
        try {

            if ($request->hasFile('files')) {

                $id = $request->input('modelId');
                $morphClass = $request->input('modelMorphClass');
                $group = $request->input('group');

                $model = (new $morphClass())->find($id);

                if ($model) {

                    $pathFile = "/{$this->filePath}/{$model->entity()}/{$id}";

                    $this->createDirectory($pathFile);

                    foreach ($request->file('files') as $file) {

                        $fileNameWithoutExtension = basename($file->getClientOriginalName(), ".{$file->getClientOriginalExtension()}");
                        $fileNameWithoutExtensionSlug = Str::slug($fileNameWithoutExtension);
                        $prefix = true ? now()->format('d_m_Y_H_i_s') . "_" : '';
                        $fileNameWithExtension = "{$prefix}{$fileNameWithoutExtensionSlug}.{$file->getClientOriginalExtension()}";

                        $publicPath = public_path($pathFile);

                        $file->move($publicPath, $fileNameWithExtension);

                        $pathWithFileName = "{$pathFile}/$fileNameWithExtension";

                        $fileData = [
                            'path' => $pathWithFileName,
                            'name' => $fileNameWithoutExtension,
                            'extension' => $file->getClientOriginalExtension(),
                            'group' => $group,
                        ];

                        $file = $model
                            ->files()
                            ->create($fileData)
                        ;

                        $files[] = array_merge($fileData, ['id' => $file->id]);
                    }

                    $result['id'] = $id;
                    $result['morphClass'] = $morphClass;
                    $result['files'] = $files;
                    $result['success'] = true;
                    $result['message'] = __('common.file_successfully_created_and_written_to_database');
                } else {

                    $result['success'] = false;
                    $result['message'] = __('common.entry_missing', ['id' => $id]);
                }
            } else {

                $result['success'] = false;
                $result['message'] = __('common.no_file');
            }
        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = "{$e->getMessage()} - {$e->getLine()}";
        }

        return response()->json($result);
    }

    // Удаление файла
    public function removeFile(Request $request, $result = []): \Illuminate\Http\JsonResponse
    {
        try {

            $id = $request->input('modelId');
            $fileId = $request->input('fileId');
            $morphClass = $request->input('morphClass');

            $model = (new $morphClass())->find($id);

            if ($model) {

                $file = $model->files()->where('id', $fileId)->first();

                if ($file && file_exists(public_path("{$file->path}"))) {

                    $this->removeLink($file->path);
                }

                $model->files()->where('id', $file->id)->delete();

                $result['success'] = true;
                $result['message'] = 'Файл успешно удален!';
            } else {

                $result['success'] = false;
                $result['message'] = __('common.entry_missing', ['id' => $id]);
            }

        } catch (\Exception $e) {

            $result['success']  = false;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    // Получаем миниатюры по названию сущности
    public function miniatures($entity)
    {
        $this->miniatures = config("common.{$entity}.miniatures", []);

        if (empty($this->miniatures)) {

            $this->miniatures = config("common.miniatures", []);
        }

        return $this->miniatures;
    }

    /**
     * ----------------------------
     * Сохранения черновика заявок
     * ----------------------------
     *
     * @param Request $request
     * @param array $result
     * @param array $rules
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDraftData(Request $request, $result = [], array $rules = [])
    {
        try {

            $morphClass = $request->input('morph_class');
            $modelId = $request->input('model_id');

            $model = (new $morphClass())->firstOrNew(['id' => $modelId]);

            if (!$model->exists || ($model->exists && ($model->status === 'draft' || empty($model->status)))) {

                $requestData = $request->all();

                $validator = Validator::make($requestData, $model->rules('draft', $rules), $model->messages(), $model->attributes());

                if ($validator->fails()) {

                    $result['success'] = false;
                    $result['errors'] = $validator->errors();
                    $result['message'] = "Валидация не пройдена, не удалось сохранить черновик!";
                } else {

                    if (!$model->exists || ($model->exists && empty($model->status))) {

                        $requestData['status'] = 'draft';
                    }

                    $model->fill($requestData)->save();

                    $contest = $model->contest;

                    if (!$modelId && !!$contest) {

                        $result['redirect'] = route("applications.{$contest->type}.edit", $model);
                    }

                    $result['success'] = true;
                    $result['message'] = "Черновик успешно сохранен!";
                }
            } else {

                $result['success'] = true;
                $result['message'] = "Сохранение черновика не требуется!";
            }
        } catch (\Exception $e) {

            $result['success'] = false;
            $result['message'] = "{$e->getMessage()} - {$e->getLine()}";
//            $result['message'] = "Не удалось сохранить черновик!";
        }

        return response()->json($result);
    }
}
