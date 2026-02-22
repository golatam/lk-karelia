<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SZPTOSApplication extends DefaultModel
{
    use SoftDeletes;

    protected $table = 'szptos_applications';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                                           // ID
        'user_id',                                      // Участник
        'contest_id',                                   // Конкурс
        'municipality_id',                              // Населенный пункт, где реализуется проект
        'register_id',                                  // Наименование ТОС
        'region_id',                                    // Наименование (муниципального района/городского округа), где реализуется проект
        'settlement_id',                                // Наименование поселения в составе района, где реализуется проект
        'date_registration_charter',                    // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
        'is_tos_legal_entity',                          // Является ли ТОС юридическим лицом
        'nomenclature_number',                          // Номенклатурный номер ТОС
//        '',                                           // Сведения о председателе ТОС (фамилия, имя, отчество (полностью), контактный телефон, электронная почта)
        'full_name_chairman_tos',                       // ФИО председателя ТОС
        'tos_address',                                  // Почтовый адрес (с указанием индекса)
        'tos_phone',                                    // Номер мобильного телефона
        'tos_email',                                    // Адрес электронной почты
        'population_size_settlement',                   // Численность населения
        'population_size_in_tos',                       // Количество жителей, проживающих в границах ТОС
        'project_name',                                 // Наименование проекта
        'project_direction',                            // Направление проекта
        'problem_description',                          // Описание актуальности проблемы, на решение которой направлен проект
        'project_purpose',                              // Цель проекта
        'project_tasks',                                // Задачи проекта
        'duration_practice_start',                      // Дата начала реализации проекта
        'duration_practice_end',                        // Дата окончания реализации проекта
        'results_project_implementation',               // Ожидаемые результаты реализации проекта
        'number_beneficiaries',                         // Количество человек (благополучателей), которые будут пользоваться результатами проекта
        'description_need',                             // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
        'total_cost_project',                           // Общая стоимость проекта
        'budget_funds_republic',                        // Средства бюджета Республики Карелия
        'funds_raised',                                 // Привлеченные средства
        'extra_budgetary_sources',                      // Внебюджетные источники
        'funds_tos',                                    // Средства ТОС
        'funds_legal_entities',                         // Средства юридических лиц
        'funds_local_budget',                           // Средства местного бюджета
        'person_responsible_implementation_project',    // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)
        'number_present_at_general_meeting',            // Количество присутствующих на общем собрании членов ТОС
        'is_grand_opening_with_media_coverage',         // По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»
        'date_filling_in',                              // Дата заполнения заявки
        'total_application_points',                     // Общие баллы по заявке
        'points_from_administrator',                    // Баллы от администратора
        'comment_on_points_from_administrator',         // Комментарий к баллам от администратора
        'status',                                       // Статус заявки
        'is_admitted_to_competition',                   // Допущен к участию в конкурсе
    ];

    protected $casts = [
        'registration_date_charter' => 'datetime',
        'tos_date_filling_in' => 'datetime',
    ];

    protected $appends = [
        'finalPointsResult',
        'isAdmittedToCompetitionLabel',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\SZPTOSApplicationObserver::class);
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

    public function getTosNamesAttribute()
    {
        $user = $this->user ?? Auth::user();
        $municipality = $user ? $user->municipality : null;

        if (!!$municipality) {

            $tos = $municipality
                ->registersRegionAll
                ->merge($municipality->registersAll)
                ->pluck('tosFullName', 'id')
                ->sort()
                ->toArray()
            ;

            return $tos;
        } else {

            return [];
        }
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
        if (!!$this->municipality) {

            if (!!$this->municipality->parent_id) {

                return $this->getMunicipalityTopName($this->municipality->parent);
            } else {

                return $this->municipality->name;
            }
        } else {

            return '---';
        }
    }

    public function getMunicipalityTopName($municipality)
    {
        if (!!$municipality->parent_id) {

            $result = $this->getMunicipalityTopName($municipality->parent);
        } else {

            $result = $municipality->name;
        }

        return $result;
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity');
    }

    // Протоколы собраний по выбору проекта
    public function preliminary_work_on_selection_project(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'preliminary_work_on_selection_project');
    }

    // Протокол собрания граждан
    public function preliminary_work_on_selection_project_a(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'preliminary_work_on_selection_project_a');
    }

    // Листы регистрации участников общего собрания граждан
    public function preliminary_work_on_selection_project_b(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'preliminary_work_on_selection_project_b');
    }

    // Планируемые источники финансирования мероприятий проекта (сканы в формате PDF)
    public function planned_sources_financing_project_activities(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'planned_sources_financing_project_activities');
    }

    // Гарантийного письма, оформленного на бланке администрации муниципального образования, подтверждающего
    // обеспечение необходимого размера средств на софинансирование проекта из бюджета муниципального образования,
    // заверенного подписью главы и печатью администрации (если есть софинансирование местного бюджета)
    public function planned_sources_financing_project_activities_a(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'planned_sources_financing_project_activities_a');
    }

    // Гарантийных писем юридических лиц(-а), подписанных руководителем и подтверждающих предоставление средств на софинансирование проекта
    public function planned_sources_financing_project_activities_b(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'planned_sources_financing_project_activities_b');
    }

    public function getPreliminaryWorkOnSelectionProjectStringNameAttribute()
    {
        $result = $this
            ->preliminary_work_on_selection_project
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Информационное сопровождение проекта (файлы)
    public function information_project_support(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'information_project_support');
    }

    // Выписка из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта
    public function extract_from_registry(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'extract_from_registry');
    }

    public function getExtractFromRegistryStringNameAttribute()
    {
        $result = $this
            ->extract_from_registry
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Техническая, проектная и сметная или иная документация, лицензия разработчика сметы
    public function documentation(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation');
    }

    //
    public function documentation_a(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_a');
    }

    //
    public function documentation_b(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_b');
    }

    //
    public function documentation_c(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_c');
    }

    //
    public function documentation_d(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_d');
    }

    public function getDocumentationStringNameAttribute()
    {
        $result = $this
            ->documentation
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Гарантийное письмо администрации муниципального образования о принятии в собственность муниципального образования объектов, реализованных в рамках проекта, в течение трех месяцев со дня окончания работ по проекту
    public function letter_guarantee(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'letter_guarantee');
    }

    public function getLetterGuaranteeStringNameAttribute()
    {
        $result = $this
            ->letter_guarantee
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Иные документы и фотоматериалы, подтверждающие актуальность и остроту проблемы и позволяющие наиболее полно описать проект
    public function other_documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'other_documents');
    }

    public function getOtherDocumentsStringNameAttribute()
    {
        $result = $this
            ->other_documents
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Приложение 4 (Согласование с главой администрации об участии в конкурсе)
    public function app_four(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'app_four');
    }

    public function matrix(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity');
    }

    // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
    public function list_members_council_tos(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'list_members_council_tos');
    }

    // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
    public function getListMembersCouncilTosStringAttribute()
    {
        return $this
            ->list_members_council_tos
            ->map(function ($value) {

                return implode(', ', $value->only('field60', 'field61', 'field62'));
            })
            ->implode('; ')
        ;
    }

    // Календарный план работ по проекту
    public function calendar_plan_work_on_project(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'calendar_plan_work_on_project');
    }

    // Информационное сопровождение проекта (указать, каким образом будет обеспечено освещение проекта в целом и его ключевых мероприятий в СМИ,
    //  социальных сетях (группы ТОС в социальных сетях, группа Ассоциации ТОС в Республике Карелия в социальной сети «Вконтакте» (https://vk.com/tosrk),
    // портал «Инициативное бюджетирование в Республике Карелии» (инициативы-карелия.рф)), реклама, листовки, специальные мероприятия, информирование партнеров.
    public function information_project_support_info(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'information_project_support_info');
    }

    public function getLinksInformationProjectSupportInfoAttribute()
    {
        return $this->information_project_support_info->map(function ($value) {return $value["field67"];})->filter()->implode(', ');
    }

    // Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.) - описать виды участия
    public function participation_population_in_implementation_project(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'participation_population_in_implementation_project');
    }

    public function getParticipationPopulationInImplementationProjectStringAttribute()
    {
        return $this
            ->participation_population_in_implementation_project
            ->map(function ($value) {

                return implode(', ', $value->only('field68'));
            })
            ->implode('; ')
        ;
    }

    // Участие населения в обеспечении эксплуатации и содержании объекта, после завершения проекта - описать виды участия
    public function public_participation_in_operation_facility(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'public_participation_in_operation_facility');
    }

    public function getPublicParticipationInOperationFacilityStringAttribute()
    {
        return $this
            ->public_participation_in_operation_facility
            ->map(function ($value) {

                return implode(', ', $value->only('field69'));
            })
            ->implode('; ')
            ;
    }

    // Реализацией проекта предусмотрено его информационное сопровождение
    public function project_implementation_provides_informational_support(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'project_implementation_provides_informational_support');
    }

    public function getIsTosLegalEntityStringAttribute()
    {
        return !!$this->is_tos_legal_entity ? 'Да' : 'Нет';
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

    public function getDraftRulesValidation()
    {
        return [
            'user_id'                                   => 'required',                                                      // Участник
            'contest_id'                                => 'required',                                                      // Конкурс
            'municipality_id'                           => 'required',                                                      // Наименование муниципального образования
            'register_id'                               => '',                                                              // Наименование ТОС
            'region_id'                                 => '',                                                              // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                             => '',                                                              // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'                 => '',                                                              // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'is_tos_legal_entity'                       => '',                                                              // Является ли ТОС юридическим лицом
            'nomenclature_number'                       => '',                                                              // Номенклатурный номер ТОС
            'full_name_chairman_tos'                    => '',                                                              // ФИО председателя ТОС
            'tos_address'                               => '',                                                              // Почтовый адрес (с указанием индекса)
            'tos_phone'                                 => '',                                                              // Номер мобильного телефона
            'tos_email'                                 => '',                                                              // Адрес электронной почты
            'population_size_settlement'                => '',                                                              // Численность населения
            'population_size_in_tos'                    => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество жителей, проживающих в границах ТОС
            'project_name'                              => 'required',                                                      // Наименование проекта
            'project_direction'                         => '',                                                              // Направление проекта
            'problem_description'                       => '',                                                              // Описание актуальности проблемы, на решение которой направлен проект
            'project_purpose'                           => '',                                                              // Цель проекта
            'project_tasks'                             => '',                                                              // Задачи проекта
            'duration_practice_start'                   => 'required',                                                      // Дата начала реализации проекта
            'duration_practice_end'                     => 'required',                                                      // Дата окончания реализации проекта
            'results_project_implementation'            => '',                                                              // Ожидаемые результаты реализации проекта
            'number_beneficiaries'                      => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество человек (благополучателей), которые будут пользоваться результатами проекта
            'description_need'                          => '',                                                              // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
            'total_cost_project'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общая стоимость проекта
            'budget_funds_republic'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства бюджета Республики Карелия
            'funds_raised'                              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Привлеченные средства
            'extra_budgetary_sources'                   => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Внебюджетные источники
            'funds_tos'                                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства ТОС
            'funds_legal_entities'                      => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства юридических лиц
            'funds_local_budget'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства местного бюджета
            'person_responsible_implementation_project' => '',                                                              // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)
            'number_present_at_general_meeting'         => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество присутствующих на общем собрании членов ТОС
            'is_grand_opening_with_media_coverage'      => '',                                                              // По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»
            'date_filling_in'                           => '',                                                              // Дата заполнения заявки
            'total_application_points'                  => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общие баллы по заявке
            'points_from_administrator'                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Баллы от администратора
            'comment_on_points_from_administrator'      => '',                                                              // Комментарий к баллам от администратора
            'status'                                    => '',                                                              // Статус заявки
        ];
    }

    public function getPublishedRulesValidation()
    {
        return [
            'user_id'                                   => 'required',                                                      // Участник
            'contest_id'                                => 'required',                                                      // Конкурс
            'municipality_id'                           => 'required',                                                      // Наименование муниципального образования
            'register_id'                               => '',                                                              // Наименование ТОС
            'region_id'                                 => '',                                                              // Наименование (муниципального района/городского округа), где реализуется проект
            'settlement_id'                             => '',                                                              // Наименование поселения в составе района, где реализуется проект
            'date_registration_charter'                 => '',                                                              // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
            'is_tos_legal_entity'                       => '',                                                              // Является ли ТОС юридическим лицом
            'nomenclature_number'                       => '',                                                              // Номенклатурный номер ТОС
            'full_name_chairman_tos'                    => '',                                                              // ФИО председателя ТОС
            'tos_address'                               => '',                                                              // Почтовый адрес (с указанием индекса)
            'tos_phone'                                 => '',                                                              // Номер мобильного телефона
            'tos_email'                                 => '',                                                              // Адрес электронной почты
            'population_size_settlement'                => ['regex:/^([0-9]{0,11})?$/'],                                                              // Численность населения
            'population_size_in_tos'                    => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество жителей, проживающих в границах ТОС
            'project_name'                              => 'required',                                                      // Наименование проекта
            'project_direction'                         => 'required',                                                      // Направление проекта
            'problem_description'                       => '',                                                              // Описание актуальности проблемы, на решение которой направлен проект
            'project_purpose'                           => '',                                                              // Цель проекта
            'project_tasks'                             => '',                                                              // Задачи проекта
            'duration_practice_start'                   => 'required',                                                      // Дата начала реализации проекта
            'duration_practice_end'                     => 'required',                                                      // Дата окончания реализации проекта
            'results_project_implementation'            => '',                                                              // Ожидаемые результаты реализации проекта
            'number_beneficiaries'                      => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество человек (благополучателей), которые будут пользоваться результатами проекта
            'description_need'                          => '',                                                              // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
            'total_cost_project'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общая стоимость проекта
            'budget_funds_republic'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства бюджета Республики Карелия
            'funds_raised'                              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Привлеченные средства
            'extra_budgetary_sources'                   => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Внебюджетные источники
            'funds_tos'                                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства ТОС
            'funds_legal_entities'                      => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства юридических лиц
            'funds_local_budget'                        => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Средства местного бюджета
            'person_responsible_implementation_project' => '',                                                              // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)
            'number_present_at_general_meeting'         => ['regex:/^([0-9]{0,11})?$/'],                                    // Количество присутствующих на общем собрании членов ТОС
            'is_grand_opening_with_media_coverage'      => '',                                                              // По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»
            'date_filling_in'                           => '',                                                              // Дата заполнения заявки
            'total_application_points'                  => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Общие баллы по заявке
            'points_from_administrator'                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],  // Баллы от администратора
            'comment_on_points_from_administrator'      => '',                                                              // Комментарий к баллам от администратора
            'status'                                    => '',                                                              // Статус заявки
        ];
    }

    public function getMessagesValidation()
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
            'regex' => 'У поля ":attribute" недопустимый формат',
        ];
    }

    public function getAttributesValidation()
    {
        return [
            'user_id' => 'Участник',
            'contest_id' => 'Конкурс',
            'municipality_id' => 'Наименование муниципального образования',
            'register_id' => 'Наименование ТОС',
            'region_id' => 'Наименование (муниципального района/городского округа), где реализуется проект',
            'settlement_id' => 'Наименование поселения в составе района, где реализуется проект',
            'date_registration_charter' => 'Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)',
            'is_tos_legal_entity' => 'Является ли ТОС юридическим лицом',
            'nomenclature_number' => 'Номенклатурный номер ТОС',
            'full_name_chairman_tos' => 'ФИО председателя ТОС',
            'tos_address' => 'Почтовый адрес (с указанием индекса)',
            'tos_phone' => 'Номер мобильного телефона',
            'tos_email' => 'Адрес электронной почты',
            'population_size_settlement' => 'Численность населения',
            'population_size_in_tos' => 'Количество жителей, проживающих в границах ТОС',
            'project_name' => 'Наименование проекта',
            'project_direction' => 'Направление проекта',
            'problem_description' => 'Описание актуальности проблемы, на решение которой направлен проект',
            'project_purpose' => 'Цель проекта',
            'project_tasks' => 'Задачи проекта',
            'duration_practice_start' => 'Дата начала реализации проекта',
            'duration_practice_end' => 'Дата окончания реализации проекта',
            'results_project_implementation' => 'Ожидаемые результаты реализации проекта',
            'number_beneficiaries' => 'Количество человек (благополучателей), которые будут пользоваться результатами проекта',
            'description_need' => 'Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации',
            'total_cost_project' => 'Общая стоимость проекта',
            'budget_funds_republic' => 'Средства бюджета Республики Карелия',
            'funds_raised' => 'Привлеченные средства',
            'extra_budgetary_sources' => 'Внебюджетные источники',
            'funds_tos' => 'Средства ТОС',
            'funds_legal_entities' => 'Средства юридических лиц',
            'funds_local_budget' => 'Средства местного бюджета',
            'person_responsible_implementation_project' => 'Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)',
            'number_present_at_general_meeting' => 'Количество присутствующих на общем собрании членов ТОС',
            'is_grand_opening_with_media_coverage' => 'По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»',
            'date_filling_in' => 'Дата заполнения заявки',
            'total_application_points' => 'Общие баллы по заявке',
            'points_from_administrator' => 'Баллы от администратора',
            'comment_on_points_from_administrator' => 'Комментарий к баллам от администратора',
            'status' => 'Статус заявки',
        ];
    }

    public function getFinalPointsResultAttribute()
    {
        $increaseInPoints = $this->points_from_administrator > 0 ? $this->points_from_administrator : 0;
        $decreaseInPoints = $this->points_from_administrator < 0 ? abs($this->points_from_administrator) : 0;
        $totalApplicationPoints = $this->total_application_points ?? 0;

        return $totalApplicationPoints + $increaseInPoints - $decreaseInPoints;
    }

    public function getIsAdmittedToCompetitionLabelAttribute()
    {
        return $this->is_admitted_to_competition ? 'Допущен' : 'Не допущен';
    }
}
