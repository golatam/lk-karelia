<?php

namespace App\Models;

use App\Interfaces\ValidateInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MostBeautifulVillage extends DefaultModel implements ValidateInterface
{
    use HasFactory, SoftDeletes;

    protected $table = 'most_beautiful_villages';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                                           // ID
        'user_id',                                      // Пользователь
        'contest_id',                                   // Конкурс
        'settlement_id',                                // Наименование населенного пункта
        'applicant_fio',                                // Фамилия, имя, отчество заявителя
        'applicant_position',                           // Должность заявителя
        'contact_details',                              // Контактные данные
        'population_size_in_settlement',                // Количество жителей, проживающих в населенном пункте
        'demographic_parameters',                       // Демографические показатели
        'forms_self_organization_citizens',             // Формы самоорганизации граждан, распространенные на территории села (поселка, деревни)
        'landscaping',                                  // Положительный опыт села (поселка, деревни) в области благоустройства, озеленения и поддержания чистоты и порядка
        'cultural_traditions',                          // Культурные традиций и обычаи села (поселка, деревни)
        'history_village_description',                  // Описание истории (легенд) села (поселка, деревни)
        'natural_monuments_description',                // Описание памятников природы села (поселка, деревни)
        'architectural_monuments_description',          // Описание памятников архитектуры'
        'degree_population_participation_description',  // Описание степени участия населения

        'appearance_village_description',               // Описание внешнего облика села (поселка, деревни)
        'reservoirs_description',                       // Описание водоемов (родников, колодцев)
        'illumination_description',                     // Описание освещенности улиц и площадей
        'common_areas_and_recreation_description',      // Описание мест общего пользования и отдыха, парки, скамейки, беседки, спортивные и детские площадки
        'artistic_expressiveness_description',          // Описание художественной выразительности и национального своеобразия жилой застройки
        'condition_burial_sites_description',           // Описание состояниея мест захоронений (кладбищ) села (поселка, деревни)

        'total_application_points',                     // Общие баллы по заявке
        'status',                                       // Статус заявки
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\MostBeautifulVillageObserver::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'settlement_id', 'id');
    }

    public function municipalityThrough()
    {
        return $this->hasOneThrough(Municipality::class, User::class, 'id', 'id', 'user_id', 'municipality_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class)
//            ->where('is_active', 1)
            ;
    }

    public function estimates(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ApplicationEstimate::class, 'entity')->with('column');
    }

    public function recalculateTotalRating()
    {
        $totalApplicationPoints = $this
            ->estimates
            ->sum(function ($estimate) {

                return $estimate->value * $estimate->column->significance_factor;
            })
        ;

        $this->total_application_points = $totalApplicationPoints;
        $this->save();
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity');
    }

    /**
     * ------------------------------------------------------------------------------------------
     * Рекомендация администрации муниципального района (городского округа) в произвольной форме
     * ------------------------------------------------------------------------------------------
     *
     * @return MorphMany
     */
    public function administration_recommendation(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'administration_recommendation');
    }

    /**
     * -----
     * Фото
     * -----
     *
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this
            ->morphMany(Image::class, 'entity')
            ->orderBy('position')
        ;
    }

    /**
     * --------------------------------------
     * Внешний облик села (поселка, деревни)
     * Загрузка фото
     * --------------------------------------
     *
     * @return Collection
     */
    public function getAppearanceVillageAttribute(): Collection
    {
        return $this->images->where('group', 'appearance_village');
    }

    /**
     * ------------------------------------------
     * История (легенда) села (поселка, деревни)
     * Загрузка фото и описание (textarea)
     * ------------------------------------------
     *
     * @return Collection
     */
    public function getHistoryVillageAttribute(): Collection
    {
        return $this->images->where('group', 'history_village');
    }

    /**
     * ---------------------------
     * Водоемы (родники, колодцы)
     * Загрузка фото
     * ---------------------------
     *
     * @return Collection
     */
    public function getReservoirsAttribute(): Collection
    {
        return $this->images->where('group', 'reservoirs');
    }

    /**
     * ------------------------------------------
     * Памятники природы села (поселка, деревни)
     * Загрузка фото и описание (textarea)
     * ------------------------------------------
     *
     * @return Collection
     */
    public function getNaturalMonumentsAttribute(): Collection
    {
        return $this->images->where('group', 'natural_monuments');
    }

    /**
     * ------------------------------------
     * Памятники архитектуры
     * Загрузка фото и описание (textarea)
     * ------------------------------------
     *
     * @return Collection
     */
    public function getArchitecturalMonumentsAttribute(): Collection
    {
        return $this->images->where('group', 'architectural_monuments');
    }

    /**
     * -----------------------------
     * Освещенность улиц и площадей
     * Загрузка фото
     * -----------------------------
     *
     * @return Collection
     */
    public function getIlluminationAttribute(): Collection
    {
        return $this->images->where('group', 'illumination');
    }

    /**
     * -------------------------------------------------------------------------------------------
     * Места общего пользования и отдыха, парки, скамейки, беседки, спортивные и детские площадки
     * Загрузка фото
     * -------------------------------------------------------------------------------------------
     *
     * @return Collection
     */
    public function getCommonAreasAndRecreationAttribute(): Collection
    {
        return $this->images->where('group', 'common_areas_and_recreation');
    }

    /**
     * --------------------------------------------------------------------------
     * Художественная выразительность и национальное своеобразие жилой застройки
     * Загрузка фото
     * --------------------------------------------------------------------------
     *
     * @return Collection
     */
    public function getArtisticExpressivenessAttribute(): Collection
    {
        return $this->images->where('group', 'artistic_expressiveness');
    }

    /**
     * -------------------------------------------------------------
     * Состояние мест захоронений (кладбищ) села (поселка, деревни)
     * Загрузка фото
     * -------------------------------------------------------------
     *
     * @return Collection
     */
    public function getConditionBurialSitesAttribute(): Collection
    {
        return $this->images->where('group', 'condition_burial_sites');
    }

    /**
     * ------------------------------------------------------------------
     * Степень участия населения в совместной работе по уборке, ремонту,
     * благоустройству и озеленению территорий, охране окружающей среды
     * Фото с textarea (максимум 5)
     * ----------------------------------------------------------------
     *
     * @return Collection
     */
    public function getDegreePopulationParticipationAttribute(): Collection
    {
        return $this->images->where('group', 'degree_population_participation');
    }

    /**
     * -----------------------------------------------------------------------------------
     * Наличие Интернет-сайта села (поселка, деревни), группы, сообщества в сети Интернет
     * -----------------------------------------------------------------------------------
     *
     * @return MorphMany
     */
    public function data_on_internet(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'data_on_internet');
    }

    /**
     * -------------------------------------------------
     * Культурно-массовые мероприятия за предыдущий год
     * -------------------------------------------------
     *
     * @return MorphMany
     */
    public function cultural_events(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'cultural_events');
    }

    /**
     * -----------
     * Фильтрация
     * -----------
     *
     * @return MostBeautifulVillage
     */
    public function filtering()
    {
        $queryBuilder = $this;

        $sessionData = session("{$this->entity()}", []);

        if ($sessionData instanceof Collection) {
            $sessionData = $sessionData->toArray();
        }

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

        if (in_array('settlement_id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('settlement_id', session("{$this->entity()}.settlement_id"));
        }

        if (in_array('status', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('status', session("{$this->entity()}.status"));
        }

        if (in_array('from_total_application_points', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('total_application_points', '>=', session("{$this->entity()}.from_total_application_points"));
        }

        if (in_array('to_total_application_points', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('total_application_points', '<=', session("{$this->entity()}.to_total_application_points"));
        }

        session()->put("{$this->entity()}", $sessionData);

        return $queryBuilder;
    }

    /**
     * @param string $status
     * @param array $rules
     * @return string[]
     */
    public function rules($status = 'draft', array $rules = []): array
    {
        return array_merge([
            'settlement_id'                 => $status === 'published' ? 'required' : '',
            'applicant_fio'                 => $status === 'published' ? 'required' : '',
            'applicant_position'            => $status === 'published' ? 'required' : '',
            'contact_details'               => $status === 'published' ? 'required' : '',
            'population_size_in_settlement' => $status === 'published' ? 'required|numeric|gt:0' : '',
            'demographic_parameters'        => $status === 'published' ? 'required' : '',
        ], $rules);
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
            'numeric' => 'Поле ":attribute" должно быть числом',
            'gt' => [
                'numeric' => 'Поле :attribute должно быть больше, чем :value.',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'Пользователь',
            'contest_id' => 'Конкурс',
            'settlement_id' => 'Наименование населенного пункта',
            'applicant_fio' => 'Фамилия, имя, отчество заявителя',
            'applicant_position' => 'Должность заявителя',
            'contact_details' => 'Контактные данные',
            'population_size_in_settlement' => 'Количество жителей , проживающих в населенном пункте',
            'demographic_parameters' => 'Демографические показатели',
            'forms_self_organization_citizens' => 'Формы самоорганизации граждан , распространенные на территории села (поселка, деревни)',
            'landscaping' => 'Положительный опыт села (поселка, деревни) в области благоустройства, озеленения и поддержания чистоты и порядка',
            'cultural_traditions' => 'Культурные традиций и обычаи села (поселка, деревни)',
        ];
    }

    /**
     * --------------------------------
     * Поля которые выводятся в списке (Поля которые можно вывести)
     * --------------------------------
     *
     * @return array
     */
    public function fieldsSelected()
    {
        if (has_cookie("fields_{$this->entity()}")) {

            $fields = collect(get_cookie("fields_{$this->entity()}", []))->unique()->toArray();
        } else {

            $fields = collect(config("app.{$this->entity()}_applications.fields_selected_default", []))
                ->unique()
                ->toArray()
            ;
        }

        if(!auth()->user()->hasPermissions(['other.show_admin'])) {

            $fields = array_diff($fields, config("app.{$this->entity()}_applications.fields_private", []));
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
        return (string) Str::of(str_replace('_applications', '', $this->getTable()));
    }

    /**
     * -----------------------------------------------------------------------
     * Поля для отображения в списке (Поля что выводятся изначально в списке)
     * -----------------------------------------------------------------------
     *
     * @return array
     */
    public function fieldsForShowing()
    {
        $fields = collect(config("app.{$this->entity()}_applications.fields_for_showing", []))
            ->unique()
            ->toArray()
        ;

        if(!auth()->user()->hasPermissions(['other.show_admin'])) {

            $fields = array_diff($fields, config("app.{$this->entity()}_applications.fields_private", []));
        }

        return $fields;
    }
}
