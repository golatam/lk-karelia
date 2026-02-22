<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LPTOSApplication extends DefaultModel
{
    use SoftDeletes;

    protected $table = 'lptos_applications';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                                                   // ID
        'user_id',                                              // Участник
        'contest_id',                                           // Конкурс
        'contest_nomination',                                   // 1. Укажите номинацию конкурса
        'category',                                             // 2. Укажите категорию:
        'municipality_id',                                      // 3. Наименование муниципального образования:
        'register_id',                                          // 4. Полное наименование ТОС
        'nomenclature_number',                                  // 5. Номенклатурный номер ТОС
        'date_registration_charter',                            // 6. Дата регистрации устава ТОС уполномоченным органом местного самоуправления:
        'population_size_in_tos',                               // 7. Количество жителей, проживающих в границах ТОС
        'number_beneficiaries',                                 // Количество человек (благополучателей), которые будут пользоваться результатами проекта
        'full_name_chairman_tos',                               // 8. ФИО председателя ТОС
        'tos_address',                                          // 9. Почтовый адрес (с указанием индекса)
        'tos_phone',                                            // 10. Номер мобильного телефона
        'tos_email',                                            // 11. Адрес электронной почты
        'is_tos_legal_entity',                                  // 12. Является ли ТОС юридическим лицом
        'registration_date_tos',                                // 13.1. Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК
        'ogrn',                                                 // 13.2. ОГРН
        'inn',                                                  // 13.3. ИНН
        'kpp',                                                  // 13.4. КПП
        'bank_details',                                         // 13.5. Банковские реквизиты:
        'website',                                              // 14.1. - официальный сайт
        'vk',                                                   // 14.2. - официальная группа в социальной сети ВКОНТАКТЕ
        'ok',                                                   // 14.3. - официальная группа в социальной сети ОДНОКЛАССНИКИ
        'fb',                                                   // 14.4. - официальная группа в социальной сети FACEBOOK
        'twitter',                                              // 14.5. - официальная группа в социальной сети TWITTER
        'instagram',                                            // 14.6. - официальная группа в социальной сети INSTAGRAM
        'practice_name',                                        // 15. Название практики (проекта):
        'practice_purpose',                                     // 16. Цель практики (проекта):
        'practice_tasks',                                       // 17. Задачи практики (проекта):
        'duration_practice',                                    // 18. Срок реализации практики (проекта)
        'practice_implementation_geography',                    // 19. География реализации практики (проекта)
        'activity_social_significance',                         // 20. Социальная значимость деятельности ТОС:
        'problem_description',                                  // 21. Описание проблемы, на решение которой была направлена практика (проект)
        'number_people_part_in_project_implementation',         // 22. Количество человек, принявших участие в реализации проекта
        'implementation_resources_involved_practice_own',       // 24.1. Собственные финансовые средства:
        'implementation_resources_involved_practice_budget',    // 24.2. Привлеченные финансовые средства (из регионального и муниципального бюджетов - при наличии):
        'implementation_resources_involved_practice_other',     // 24.3. Организационные ресурса: (волонтерство, благотворительность, социальное партнерство, информационная поддержка проекта
        'achieved_results',                                     // 25. Укажите основные результаты, достигнутые при реализации практики (проекта)
        'total_application_points',                             // 36. Общие баллы по заявке
        'status',                                               // Статус заявки
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

        self::observe(Observers\LPTOSApplicationObserver::class);
    }

    public function register()
    {
        return $this->belongsTo(Register::class);
    }

    public function getTosNameAttribute($tosName)
    {
        return !!$this->register ? $this->register->name_according_charter : '';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTosNamesAttribute()
    {
        $user = $this->user ?? Auth::user();
        $municipality = $user ? $user->municipality : null;

        if (!!$municipality) {

            if ($municipality->registersAll->isNotEmpty()) {

                $tos = $user->municipality->registersAll->pluck('name_according_charter', 'id')->toArray();

                return $tos;
            } else {

                $tos = $user->municipality->registersRegionAll->pluck('name_according_charter', 'id')->toArray();

                return $tos;
            }
        } else {

            return [];
        }
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class)
//            ->where('is_active', 1)
        ;
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function municipalityThrough()
    {
        return $this->hasOneThrough(Municipality::class, User::class, 'id', 'id', 'user_id', 'municipality_id');
    }

    public function getMunicipalityUserNameAttribute()
    {
        return $this->municipalityThrough ? $this->municipalityThrough->name : null;
    }

    public function getMunicipalityNameAttribute()
    {
        return $this->municipality ? $this->municipality->name : null;
    }

    public function getMunicipalityParentNameAttribute()
    {
        return $this->municipality && $this->municipality->parent ? $this->municipality->parent->name : null;
    }

    public function matrix(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity');
    }

    public function list_documents_regulating_activity(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'list_documents_regulating_activity');
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

        if (in_array('municipality_id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('municipality_id', session("{$this->entity()}.municipality_id"));
        }

        if (in_array('year_id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('contest_id', session("{$this->entity()}.year_id"));
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

    public function estimates(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ApplicationEstimate::class, 'entity');
    }

    public function getEstimatesGroupByUserIdAttribute()
    {
        return $this->estimates->groupBy('user_id')->values();
    }

    public function recalculateTotalRating()
    {
        $this->total_application_points = $this->estimates->sum(function ($estimate) {
            return $estimate->value * $estimate->column->significance_factor;
        });
        $this->save();
    }

    public function getDraftRulesValidation()
    {
        return [
            'user_id'       => 'required',
            'contest_id'    => 'required',
        ];
    }

    public function getPublishedRulesValidation()
    {
        return [
            'user_id'       => 'required',
            'contest_id'    => 'required',
        ];
    }

    public function getMessagesValidation()
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
        ];
    }

    public function getAttributesValidation()
    {
        return [
            'user_id' => 'Участник',
            'contest_id' => 'Конкурс',
        ];
    }

    public function setRegistrationDateTosAttribute($value)
    {
        $this->attributes['registration_date_tos'] = Carbon::parse($value);
    }

    public function getRegistrationDateTosAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function setDateRegistrationCharterAttribute($value)
    {
        $this->attributes['date_registration_charter'] = Carbon::parse($value);
    }

    public function getDateRegistrationCharterAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function setDurationPracticeAttribute($value)
    {
        $this->attributes['duration_practice'] = Carbon::parse($value);
    }

    public function getDurationPracticeAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

}
