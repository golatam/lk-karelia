<?php

namespace App\Models\Import;

use App\Models\DefaultModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Register extends DefaultModel
{
    protected $connection = 'import';

    protected $table = 'register_tos';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                       // ID
        'nmrgo', // Наименование муниципального района / городского округа
        'npvsr', // Наименование поселения в составе района
        'nsu', // Наименование (согласно уставу)
        'yaltosyul', // Является ли ТОС юридическим лицом (да/нет)
        'amtos', // Адрес местонахождения ТОС (для юридических лиц - юридический адрес)
        'gtos', // Границы ТОС
        'mpaouutos', // Муниципальный правовой акт об утверждении устава ТОС (вид документа, дата, номер)
        'registration_date', // Дата регистрации
        'kchtos', // Кол-во членов ТОС
        'kgpvgtos', // Кол-во граждан, проживающих в границах ТОС
        'fio_rtos', // ФИО руководителя ТОС
        'email_rtos', // Электронный адрес руководителя ТОС
        'phone_rtos', // Мобильный телефон руководителя ТОС
        'note', // Примечание

    ];

    protected $casts = [
        'registration_date_charter' => 'datetime',
        'registration_date_tos' => 'datetime',
    ];

    protected $appends = [
        'full_name'
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
    }

    public function getFullNameAttribute()
    {
        return "{$this->nsu} - {$this->amtos}";
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

        $sessionData['filter'] = $filter;

        // Пишем его в сессию
        session([$this->entity() => $sessionData]);

        return $queryBuilder;
    }

}
