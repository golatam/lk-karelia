<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Database\Eloquent\SoftDeletes;


class Municipality extends DefaultModel
{
    use SoftDeletes;

    /**
    * @var array
    */
    protected $fillable = [
      'parent_id',              // Родитель
      'name',                   // Наименование муниципального образования
      'type',                   // Тип муниципального образования
      'is_district_plus_gp',    // Муниципальный район + Администрация городского поселения
    ];

    public function children()
    {
        return $this->hasMany($this, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo($this, 'parent_id', 'id');
    }

    public function getMunicipalitiesParentLevelAttribute()
    {
        if ($this->parent) {

            return $this->where('parent_id', $this->parent->parent_id)->get();
        } else {

            return $this->where('parent_id', $this->parent_id)->get();
        }
    }

    public function getParentTopLevelAttribute()
    {
        return $this->getParentTopLevel();
    }

    public function getParentTopLevelNameAttribute()
    {
        return $this->parentTopLevel?->name;
    }

    public function getParentTopLevel($modelParent = null)
    {
        if (!$modelParent) {

            $modelParent = $this;
        }

        if (!$modelParent->parent) {

            return $modelParent;
        } else {

            return $this->getParentTopLevel($modelParent->parent);
        }
    }

    public function registersRegion()
    {
        return $this->hasMany(Register::class, 'name_region', 'id');
    }

    public function getRegistersRegionAllAttribute()
    {
        return collect($this->recursiveResultRegistersRegion($this));
    }

    public function recursiveResultRegistersRegion($municipality, $result = [])
    {
        $result = array_merge($result, $municipality->registersRegion->toArray());

        if ($municipality->children->isNotEmpty()) {

            foreach ($municipality->children as $municipalityChildren) {

                $result = array_merge($result, $this->recursiveResultRegistersRegion($municipalityChildren));
            }
        }

        return $result;
    }

    public function registers()
    {
        return $this->hasMany(Register::class, 'name_settlement', 'id');
    }

    public function getRegistersAllAttribute()
    {
        return collect($this->recursiveResultRegisters($this));
    }

    public function recursiveResultRegisters($municipality, $result = [])
    {
        $result = array_merge($result, $municipality->registers->toArray());

        if ($municipality->children->isNotEmpty()) {

            foreach ($municipality->children as $municipalityChildren) {

                $result = array_merge($result, $this->recursiveResultRegisters($municipalityChildren));
            }
        }

        return $result;
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

        if (in_array('parent_id', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('parent_id', session("{$this->getTable()}.parent_id"));
        }

        if (in_array('name', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('name', 'like', '%' . session("{$this->getTable()}.name") . '%');
        }

        $sessionData['filter'] = $filter;

        // Пишем его в сессию
        session([$this->entity() => $sessionData]);

        return $queryBuilder;
    }
}
