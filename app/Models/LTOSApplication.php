<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LTOSApplication extends DefaultModel
{
    use SoftDeletes;

    protected $table = 'ltos_applications';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                                   // ID
        'user_id',                              // Участник
        'contest_id',                           // Конкурс
        'municipality_id',                      // Наименование муниципального образования
        'status',                               // Статус заявки
        'register_id',                          // Полное наименование ТОС
        'region_id',                            // Наименование (муниципального района/городского округа), где реализуется проект
        'settlement_id',                        // Наименование поселения в составе района, где реализуется проект
        'date_registration_charter',            // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
        'nomenclature_number',                  // Номенклатурный номер ТОС
        'is_tos_legal_entity',                  // Является ли ТОС юридическим лицом
        'full_name_chairman_tos',               // ФИО председателя ТОС
        'tos_address',                          // Почтовый адрес (с указанием индекса)
        'tos_phone',                            // Номер мобильного телефона
        'tos_email',                            // Адрес электронной почты
        'population_size_in_tos',               // Количество зарегистрированных граждан в ТОС
        'date_filling_in',                      // Дата заполнения
        'total_application_points',             // Общие баллы по заявке
        'points_from_administrator',            // Баллы от администратора
        'comment_on_points_from_administrator', // Комментарий к баллам от администратора
    ];

    protected $casts = [
        'registration_date_charter' => 'datetime',
        'tos_date_filling_in' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\LTOSApplicationObserver::class);
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

    public function register()
    {
        return $this->hasOne(Register::class, 'id', 'register_id');
    }

    public function getTosNameAttribute()
    {
        return $this->register?->name_according_charter;
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
        return $this->municipalityThrough?->name;
    }

    public function getMunicipalityNameAttribute()
    {
        return $this->municipality?->name;
    }

    public function getMunicipalityParentNameAttribute()
    {
        return $this->municipality?->parent?->name;
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

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity');
    }

    // Наличие выписки из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта
    public function extracts(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'extracts');
    }

    public function matrix(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity');
    }

    // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
    public function list_tos_board_members(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'list_tos_board_members');
    }

    // Организация культурно-массовых мероприятий, праздников, иных культурно-просветительных акций (не более 1 страницы)
    public function organization_cultural_events(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'organization_cultural_events');
    }

    // Проведение спортивных соревнований, гражданско-патриотических игр, туристических выездов (не более 1 страницы)
    public function conducting_sports_competitions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'conducting_sports_competitions');
    }

    // Проведение мероприятий, направленных на профилактику наркомании, алкоголизма и формирование здорового образа жизни (не более 1 страницы)
    public function drug_addiction_prevention_measures(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'drug_addiction_prevention_measures');
    }

    // Наличие клубов, секций кружков, организованных при ТОС (не более 1 страницы)
    public function availability_clubs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'availability_clubs');
    }

    // Проведение мероприятий по организации благоустройства и улучшения санитарного состояния территории ТОС (не более 1 страницы)
    public function measures_organization_landscaping(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'measures_organization_landscaping');
    }

    // Количество объектов социальной направленности, восстановленных, отремонтированных или построенных силами ТОС (не более 1 страницы)
    public function number_objects_social_orientation(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'number_objects_social_orientation');
    }

    // Оказание помощи многодетным семьям, инвалидам, одиноким пенсионерам, малоимущим гражданам (не более 1 страницы)
    public function providing_assistance(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'providing_assistance');
    }

    // Создание на территории ТОС уголка здорового образа жизни, разработка буклетов, выпуск стенгазет по пропаганде здорового образа жизни (не более 1 страницы)
    public function healthy_lifestyle_corner(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'healthy_lifestyle_corner');
    }

    // Участие членов ТОС в совместных с сотрудниками полиции профилактических мероприятиях, связанных с профилактикой преступлений и иных правонарушений (не более 1 страницы)
    public function joint_preventive_measures(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'joint_preventive_measures');
    }

    // Проведение мероприятий по профилактике пожаров (не более 1 страницы)
    public function fire_prevention(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'fire_prevention');
    }

    // Проведение ТОСами совещаний и семинаров с участием органов местного самоуправления (не более 0,5 страницы)
    public function meetings_and_seminars(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'meetings_and_seminars');
    }

    // Размещение информации в средствах массовой информации и в информационно-телекоммуникационной сети Интернет о деятельности ТОС по каждому направлению деятельности
    public function placement_information_in_mass_media(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'placement_information_in_mass_media');
    }

    // Участие ТОС в конкурсах за предыдущие три года (неудачное)
    public function participation_in_previous_contests_unsuccessful(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'participation_in_previous_contests_unsuccessful');
    }

    // Участие ТОС в конкурсах за предыдущие три года (удачное)
    public function participation_in_previous_contests_successful(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'participation_in_previous_contests_successful');
    }

    // Награды ТОС и членов ТОС за тосовскую деятельность (за последние три года)
    public function awards(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'awards');
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
     * Организация культурно-массовых мероприятий
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getOrganizationCulturalEventsSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'organization_cultural_events_slides');
    }

    /**
     * --------------------------------------
     * Проведение спортивных соревнований
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getConductingSportsCompetitionsSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'conducting_sports_competitions_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getDrugAddictionPreventionMeasuresSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'drug_addiction_prevention_measures_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getAvailabilityClubsSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'availability_clubs_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getMeasuresOrganizationLandscapingSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'measures_organization_landscaping_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getNumberObjectsSocialOrientationSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'number_objects_social_orientation_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getProvidingAssistanceSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'providing_assistance_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getHealthyLifestyleCornerSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'healthy_lifestyle_corner_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getJointPreventiveMeasuresSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'joint_preventive_measures_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getFirePreventionSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'fire_prevention_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getMeetingsAndSeminarsSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'meetings_and_seminars_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getPlacementInformationInMassMediaSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'placement_information_in_mass_media_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getParticipationInPreviousContestsUnsuccessfulSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'participation_in_previous_contests_unsuccessful_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getParticipationInPreviousContestsSuccessfulSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'participation_in_previous_contests_successful_slides');
    }

    /**
     * --------------------------------------
     *
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getAwardsSlidesAttribute(): Collection
    {
        return $this->images->where('group', 'awards_slides');
    }

    /**
     * --------------------------------------
     * Дополнительная документация
     * Загрузка слайдов
     * --------------------------------------
     *
     * @return Collection
     */
    public function getAdditionalDocumentationAttribute(): Collection
    {
        return $this->images->where('group', 'additional_documentation');
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

    public function getDraftRulesValidation()
    {
        return [
            'user_id'                               => 'required',  // Участник
            'contest_id'                            => 'required',  // Конкурс
            'municipality_id'                       => 'required',  // Наименование муниципального образования
            'status'                                => '',          // Статус заявки
            'register_id'                           => '',          // Полное наименование ТОС
            'region_id'                             => '',          // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                         => '',          // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'             => '',          // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'nomenclature_number'                   => '',          // Номенклатурный номер ТОС
            'is_tos_legal_entity'                   => '',          // Является ли ТОС юридическим лицом
            'full_name_chairman_tos'                => '',          // ФИО председателя ТОС
            'tos_address'                           => '',          // Почтовый адрес (с указанием индекса)
            'tos_phone'                             => '',          // Номер мобильного телефона
            'tos_email'                             => '',          // Адрес электронной почты
            'population_size_in_tos'                => '',          // Количество зарегистрированных граждан в ТОС
            'date_filling_in'                       => '',          // Дата заполнения
            'total_application_points'              => '',          // Общие баллы по заявке
            'points_from_administrator'             => '',          // Баллы от администратора
            'comment_on_points_from_administrator'  => '',          // Комментарий к баллам от администратора
        ];
    }

    public function getPublishedRulesValidation()
    {
        return [
            'user_id'                               => 'required',  // Участник
            'contest_id'                            => 'required',  // Конкурс
            'municipality_id'                       => 'required',  // Наименование муниципального образования
            'status'                                => '',          // Статус заявки
            'register_id'                           => '',          // Полное наименование ТОС
            'region_id'                             => '',          // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                         => '',          // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'             => '',          // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'nomenclature_number'                   => '',          // Номенклатурный номер ТОС
            'is_tos_legal_entity'                   => '',          // Является ли ТОС юридическим лицом
            'full_name_chairman_tos'                => '',          // ФИО председателя ТОС
            'tos_address'                           => '',          // Почтовый адрес (с указанием индекса)
            'tos_phone'                             => '',          // Номер мобильного телефона
            'tos_email'                             => '',          // Адрес электронной почты
            'population_size_in_tos'                => '',          // Количество зарегистрированных граждан в ТОС
            'date_filling_in'                       => '',          // Дата заполнения
            'total_application_points'              => '',          // Общие баллы по заявке
            'points_from_administrator'             => '',          // Баллы от администратора
            'comment_on_points_from_administrator'  => '',          // Комментарий к баллам от администратора
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
            'municipality_id' => 'Наименование муниципального образования',
            'status' => 'Статус заявки',
            'register_id' => 'Полное наименование ТОС',
            'region_id' => 'Наименование (муниципального района/городского округа), где реализуется проект',
            'settlement_id' => 'Наименование поселения в составе района, где реализуется проект',
            'date_registration_charter' => 'Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)',
            'nomenclature_number' => 'Номенклатурный номер ТОС',
            'is_tos_legal_entity' => 'Является ли ТОС юридическим лицом',
            'full_name_chairman_tos' => 'ФИО председателя ТОС',
            'tos_address' => 'Почтовый адрес (с указанием индекса)',
            'tos_phone' => 'Номер мобильного телефона',
            'tos_email' => 'Адрес электронной почты',
            'population_size_in_tos' => 'Количество зарегистрированных граждан в ТОС',
            'date_filling_in' => 'Дата заполнения',
            'total_application_points' => 'Общие баллы по заявке',
            'points_from_administrator' => 'Баллы от администратора',
            'comment_on_points_from_administrator' => 'Комментарий к баллам от администратора',
        ];
    }

}
