<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends DefaultModel
{
    use SoftDeletes;

    /**
     * ----------------------------------------------------------
     * The attributes that are mass assignable.
     * ----------------------------------------------------------
     * Атрибуты, которые могут быть присвоены в массовом порядке
     * ----------------------------------------------------------
     *
     * @var array
     */
    protected $fillable = [
        'id',           // ID
        'name',         // Наименование разрешения
        'description',  // Описание разрешения
        'type',         // Тип разрешения
        'group',        // Группа разрешения
        'action',       // Действие разрешения
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Фильтрация
    public function filtering()
    {
        $queryBuilder = $this;

        $sessionData = session("{$this->entity()}", []);

        $filter = collect($sessionData)->except('page', 'method', 'sort_column', 'sort_direction')->toArray();

        $filter['used'] = false;

        $fields = collect($sessionData)
            ->keys()
            ->intersect($this->getFillable())
            ->toArray()
        ;

        if (in_array('id', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('id', session("{$this->getTable()}.id"));
        }

        if (in_array('name', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('name', 'like', '%' . session("{$this->getTable()}.name") . '%');
        }

        if (in_array('description', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('description', 'like', '%' . session("{$this->getTable()}.description") . '%');
        }

        $sessionData['filter'] = $filter;

        // Пишем его в сессию
        session([$this->entity() => $sessionData]);

        return $queryBuilder;
    }
}
