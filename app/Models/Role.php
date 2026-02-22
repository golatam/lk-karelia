<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends DefaultModel
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
        'name',         // Наименование
        'alias',        // Техническое наименование
        'description'   // Описание
    ];

    protected $with = [
        'permissions',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\RoleObserver::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Grant the given permission to a role.
     *
     * @param Permission $permission
     *
     * @return mixed
     */
    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }

    public function permissionsAll($permissions)
    {
        return ($this->exists && ($this->permissions->count() - $this->permissions->diff($permissions)->count()) === $permissions->count());
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

        if (in_array('alias', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('alias', 'like', '%' . session("{$this->getTable()}.alias") . '%');
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
