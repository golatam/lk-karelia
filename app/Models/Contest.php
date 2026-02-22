<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contest extends DefaultModel
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'id',                   // ID
        'type',                 // Тип конкурса
        'name',                 // Название конкурса
        'year_of_competition',  // Год проведения конкурса
        'end_date_active',      // Конечная дата активности
        'is_active',            // Сейчас активен для подачи заявок
    ];

    protected $casts = [
        'year_of_competition' => 'datetime',
        'end_date_active' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\ContestObserver::class);
    }

    public function getCheckActiveDateAttribute()
    {
        return now()->diffInMinutes(Carbon::parse($this->end_date_active), false) > 0;
    }

    protected function yearOfCompetition(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y'),
            set: fn ($value) => Carbon::parse("{$value}-01-01"),
        );
    }

    public function getIsPPMIAttribute()
    {
        return $this->type === 'ppmi';
    }

    public function getIsLTOSAttribute()
    {
        return $this->type === 'ltos';
    }

    public function getIsSZPTOSAttribute()
    {
        return $this->type === 'szptos';
    }

    public function getIsLPTOSAttribute()
    {
        return $this->type === 'lptos';
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

        if (in_array('is_active', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('is_active', session("{$this->getTable()}.is_active"));
        }

        $sessionData['filter'] = $filter;

        // Пишем его в сессию
        session([$this->entity() => $sessionData]);

        return $queryBuilder;
    }

}
