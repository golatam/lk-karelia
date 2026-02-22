<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Register extends DefaultModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',                       // ID
        'name_region',              // Наименование (муниципального района/городского округа)
        'name_settlement',          // Наименование поселения в составе района
        'name_according_charter',   // Наименование (согласно уставу)
        'is_legal_entity',          // Является ли ТОС юридическим лицом (да/нет)
        'membership',               // Членство в АР ТОС РК
        'address',                  // Адрес местонахождения ТОС (для юридических лиц - юридический адрес)
        'inn',                      // ИНН
        'kpp',                      // КПП
        'ogrn',                     // ОГРН
        'bank_details',             // Банковские реквизиты
        'site',                     // Официальный сайт
        'vk',                       // Официальная группа в социальной сети ВКОНТАКТЕ
        'ok',                       // Официальная группа в социальной сети ОДНОКЛАССНИКИ
        'fb',                       // Официальная группа в социальной сети FACEBOOK
        'twitter',                  // Официальная группа в социальной сети TWITTER
        'instagram',                // Официальная группа в социальной сети INSTAGRAM
        'boundaries',               // Границы ТОС
        'legal_act',                // Муниципальный правовой акт об утверждении устава ТОС (вид документа, дата, номер
        'registration_date_charter',// Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
        'registration_date_tos',    // Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК
        'nomenclature_number',      // Номенклатурный номер ТОС
        'number_members',           // Кол-во членов ТОС
        'number_citizens',          // Кол-во граждан, проживающих в границах ТОС
        'fio_chief',                // ФИО руководителя ТОС
        'email_chief',              // Электронный адрес руководителя ТОС
        'phone_chief',              // Мобильный телефон руководителя ТОС
        'note',                     // Примечание
        'date_tos_was_added_to_registry',                     // Дата добавления ТОС в Реестр

    ];

    protected $casts = [
        'registration_date_charter' => 'datetime',
        'registration_date_tos' => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'regionName',
        'settlementName',
        'tosFullName',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\RegisterObserver::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->nsu} - {$this->amtos}";
    }

    public function region()
    {
        return $this->belongsTo(Municipality::class, 'name_region', 'id');
    }

    public function settlement()
    {
        return $this->belongsTo(Municipality::class, 'name_settlement', 'id');
    }

    public function getSettlementNameAttribute()
    {
        return $this->settlement?->name;
    }

    public function getRegionNameAttribute()
    {
        return $this->region?->name;
    }

    public function dateTosWasAddedToRegistry(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d'),
            set: fn ($value) => Carbon::parse("{$value}"),
        );
    }

    public function getTosFullNameAttribute(): string
    {
        $municipalityName = !empty($this->name_settlement) ? $this->settlementName : $this->regionName;
        return "{$this->name_according_charter} ({$municipalityName})";
    }

    // Фильтрация
    public function filtering()
    {
        $queryBuilder = $this;

        $sessionData = session("{$this->entity()}", []);
        unset($sessionData['filter']);
        $sessionData['filter'] = array_diff_key($sessionData, array_flip(['page', 'method', 'sort_column', 'sort_direction']));
        $sessionData['filter']['used'] = false;

        $fields = collect($sessionData['filter'])
            ->keys()
            ->toArray()
        ;

        if (in_array('id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('id', session("{$this->entity()}.id"));
        }

        if (in_array('name_region', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('name_region', session("{$this->entity()}.name_region"));
        }

        if (in_array('name_settlement', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('name_settlement', session("{$this->entity()}.name_settlement"));
        }

        session()->put("{$this->entity()}", $sessionData);

        return $queryBuilder;
    }

}
