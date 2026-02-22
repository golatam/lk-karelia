<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DefaultModel extends Model
{
    /**
     * ---------------------------------
     * Поле для сортировки по умолчанию
     * ---------------------------------
     *
     * @var string
     */
    public $columnDefault = 'id';

    /**
     * ----------------------------------------
     * Направление для сортировки по умолчанию
     * ----------------------------------------
     *
     * @var string
     */
    protected $directionDefault = 'desc';

    /**
     * -------------------------------------
     * Количество выбранных записей из базы
     * -------------------------------------
     *
     * @var int
     */
    protected $total = 20;

    /**
     * ---------------------
     * Статус is_active = 1
     * ---------------------
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * --------------------------------
     * Поля которые выводятся в списке
     * --------------------------------
     *
     * @return array
     */
    public function fieldsSelected()
    {
        if (has_cookie("fields_{$this->entity()}")) {

            $fields = collect(get_cookie("fields_{$this->entity()}", []))->unique()->toArray();
        } else {

            $fields = collect(config("app.{$this->entity()}.fields_selected_default", []))
                ->unique()
                ->toArray()
            ;
        }

        return $fields;
    }

    /**
     * ------------------
     * Название сущности
     * ------------------
     *
     * @return string
     */
    public function entity()
    {
        return (string) Str::of($this->getTable())->replace('_', '-');
    }

    /**
     * ------------------------------
     * Поля для отображения в списке
     * ------------------------------
     *
     * @return array
     */
    public function fieldsForShowing()
    {
        return collect(config("app.{$this->entity()}.fields_for_showing", []))
            ->unique()
            ->toArray()
        ;
    }

//    public function getSorting($key = null)
//    {
//        return $key ? get_cookie("sorting_{$this->entity()}.{$key}") : get_cookie("sorting_{$this->entity()}");
//    }

    public function getColumnSortingAttribute()
    {
        $sorting = get_cookie("sorting_{$this->entity()}");
        $sortColumn = isset($sorting['sortColumn']) ? $sorting['sortColumn'] : $this->columnDefault;

        return $sortColumn;
    }

    public function getDirectionSortingAttribute()
    {
        $sorting = get_cookie("sorting_{$this->entity()}");
        $direction = isset($sorting['sortDirection']) ? $sorting['sortDirection'] : $this->directionDefault;

        return $direction;
    }

    public function methodExists($method)
    {
        return method_exists($this, $method);
    }

    public function setMetaTitle($metaTitle)
    {
        if ($this->methodExists('metaTags') && !empty($metaTitle)) {

            $metaTag = $this
                ->metaTags()
                ->firstOrNew()
            ;
            $metaTag->save();

            if ($metaTag->exists) {

                $metaTagI18n = $metaTag
                    ->entities()
                    ->firstOrNew([
                        'locale' => get_current_locale()
                    ])
                ;

                $metaTagI18n
                    ->fill([
                        'meta_title' => $metaTitle,
                    ])
                    ->save()
                ;
            }
        }
    }

    public function setMetaKeywords($metaKeywords)
    {
        if ($this->methodExists('metaTags') && !empty($metaKeywords)) {

            $metaTag = $this
                ->metaTags()
                ->firstOrNew()
            ;

            if ($metaTag->exists) {


                $metaTagI18n = $metaTag
                    ->entities()
                    ->firstOrNew([
                        'locale' => get_current_locale()
                    ])
                ;

                $metaTagI18n
                    ->fill([
                        'meta_keywords' => $metaKeywords,
                    ])
                    ->save()
                ;
            }
        }
    }

    public function setMetaDescription($metaDescription)
    {
        if ($this->methodExists('metaTags') && !empty($metaDescription)) {

            $metaTag = $this
                ->metaTags()
                ->firstOrNew()
            ;

            if ($metaTag->exists) {


                $metaTagI18n = $metaTag
                    ->entities()
                    ->firstOrNew([
                        'locale' => get_current_locale()
                    ])
                ;

                $metaTagI18n
                    ->fill([
                        'meta_description' => $metaDescription,
                    ])
                    ->save()
                ;
            }
        }
    }

    public function getTotalRecordsAttribute()
    {
        return $this->total;
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published';
    }
}
