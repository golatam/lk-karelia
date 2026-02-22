<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PPMIApplication extends DefaultModel
{
    use SoftDeletes;

    protected $table = 'ppmi_applications';

    /**
     * @var array
     */
    protected $fillable = [
        'id',                                           // ID
        'user_id',                                      // ID пользователя
        'contest_id',                                   // ID конкурса v
        'municipality_id',                              // ID муниципалитета
        'project_name',                                 // Наименование проекта
        'population_size_settlement',                   // Численность населенного пункта
        'project_typology',                             // Типология проекта
        'description_problem',                          // Описание проблемы
        'cost_repair_work',                             // Стоимость ремонтных работ
        'comment_on_cost_repairs',                      // Комментарий к стоимости ремонтных работ
        'cost_purchasing_materials',                    // Стоимость приобретения материалов
        'comment_on_cost_purchasing_materials',         // Комментарий к стоимости приобретения материалов
        'cost_purchasing_equipment',                    // Стоимость приобретения оборудования
        'comment_on_cost_purchasing_equipment',         // Комментарий к стоимости приобретения оборудования
        'cost_construction_control',                    // Стоимость строительного контроля
        'comment_on_cost_construction_control',         // Комментарий к стоимости строительного контроля
        'cost_other_expenses',                          // Стоимость прочих расходов
        'comment_on_cost_other_expenses',               // Комментарий к стоимость прочих расходов
        'expected_results',                             // Ожидаемые результаты
        'funds_municipal',                              // Средства муниципального образования
        'funds_individuals',                            // Безвозмездно от физ. лиц
        'funds_legal_entities',                         // Безвозмездно от юр. лиц
        'funds_republic',                               // Средства республики
        'population_that_benefit_from_results_project', // Население, которое будет регулярно пользоваться результатами от реализации проекта
        'population_size',                              // Кол-во человек населения
        'population_size_in_congregation',              // Кол-во лиц в собрании
        'population_in_project_implementation',         // Участие населения в реализации проекта
        'population_in_project_provision',              // Участие населения в обеспечении проекта
        'implementation_date',                          // Срок реализации
        'comment',                                      // Дополнительная информация и комментарии
        'is_unpaid_work_of_population',                 // Неоплачиваемый труд населения
        'is_media_participation',                       // Участие СМИ
        'total_application_points',                     // Общие баллы по заявке
        'points_from_administrator',                    // Баллы от администратора
        'comment_on_points_from_administrator',         // Комментарий к баллам от администратора
        'status',                                       // Статус заявки
        'is_admitted_to_competition',                   // Допущен к участию в конкурсе
    ];

    protected $appends = [
        'municipalityUserName',
        'finalPointsResult',
        'isAdmittedToCompetitionLabel',
        'comment',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\PPMIApplicationObserver::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
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

    public function getProjectTypologyNameAttribute()
    {
        return config("app.ppmi_applications.project_typologies.{$this->project_typology}", '');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'entity');
    }

    // Наличие выписки из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта
    public function extracts(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'extracts');
    }

    public function getExtractsStringNameAttribute()
    {
        $result = $this
            ->extracts
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Наличие технической, проектной и сметной документации
    public function documentation(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation');
    }

    // Утвержденная (-ые) главой (руководителем) локальная (-ые) смета (-ы) на работы (услуги) в рамках проекта
    public function documentation_a(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_a');
    }

    // Свидетельство, сертификат (либо иной подтверждающий документ) разработчика сметы
    public function documentation_b(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_b');
    }

    // Коммерческие предложения (не менее трех)
    public function documentation_c(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'documentation_c');
    }

    // Сводный сметный расчет (при необходимости)
    public function documentation_d(): MorphMany
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

    // Планируемые источники финансирования мероприятий проекта
    public function planned_sources_financing(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'planned_sources_financing');
    }

    // Протоколы собрания
    public function protocols(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'protocols');
    }

    public function getProtocolsStringNameAttribute()
    {
        $result = $this
            ->protocols
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.) - описать виды участия
    public function participation_population_in_implementation_project(): MorphMany
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

    // Участие населения в обеспечении эксплуатации и содержании муниципального имущества, предусмотренного проектом, после завершения реализации проекта (каждый вид участия указывается отдельной строкой)
    public function public_participation_in_operation_facility(): MorphMany
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

    // Предварительное обсуждение проекта (опросные листы, анкеты, собрания, подомовой обход и т.д.)
    public function questionnaires(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'questionnaires');
    }

    public function getQuestionnairesStringNameAttribute()
    {
        $result = $this
            ->questionnaires
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Реализацией проекта предусмотрено его информационное сопровождение
    public function project_implementation_provides_informational_support(): MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'project_implementation_provides_informational_support');
    }

    /**
     *------------------------------------
     * Подставлять данные из поля comment
     *------------------------------------
     * @return bool
     */
    public function checkCommentOldField(): bool
    {
        $changeDate = '10.04.2025'; // Дата когда были внесены изменения
        $changeDateCarbon = Carbon::createFromFormat('d.m.Y', $changeDate);
        $createDateCarbon = Carbon::parse($this->created_at);
        $diffInDays = $changeDateCarbon->diffInDays($createDateCarbon, false);
        return $diffInDays < 0;
    }

    public function getCommentAttribute($value)
    {
        if ($this->checkCommentOldField()) {
            return $value;
        }

        $result = $this
            ->planned_activities_within_project
            ->map(function ($value) {

                return implode(', ', $value->only('field73', 'field74', 'field75'));
            })
            ->implode("; ");

        return $result;
    }

    // Запланированные мероприятия в рамках реализации проекта
    public function planned_activities_within_project(): MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'planned_activities_within_project');
    }

    // Участие СМИ
    public function mass_media(): MorphMany
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'mass_media');
    }

    public function getMassMediaStringNameAttribute()
    {
        $result = $this
            ->mass_media
            ->map(function ($image) {

                return $image->name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Заверенные копии актов выполненных работ
    public function acts()
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'acts');
    }

    // Заверенные копии документов подтверждающих оплату выполненных работ
    public function payment()
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'payment');
    }

    // Заверенные копии публикаций в средствах массовой информации
    public function publications()
    {
        return $this->morphMany(File::class, 'entity')->where('group', 'publications');
    }

    public function matrix(): MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity');
    }

    public function gratuitous_receipts(): MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'gratuitous_receipts');
    }

    public function operating_and_maintenance_costs(): MorphMany
    {
        return $this->morphMany(Matrix::class, 'entity')->where('group', 'operating_and_maintenance_costs');
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

        if (in_array('contest_id', $fields)) {

            $sessionData['filter']['used'] = true;

            $queryBuilder = $queryBuilder->where('contest_id', session("{$this->entity()}.contest_id"));
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

    public function rules($status, $rules = [])
    {
        if ($status === 'published') {

            $rules = $this->getPublishedRulesValidation($rules);
        } else {

            $rules = $this->getDraftRulesValidation($rules);
        }

        return $rules;
    }

    public function messages(): array
    {
        return $this->getMessagesValidation();
    }

    public function attributes(): array
    {
        return $this->getAttributesValidation();
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

    public function getDraftRulesValidation($rules = []): array
    {
        return array_merge([
            'user_id'                           => 'required',
            'contest_id'                        => 'required',
            'project_name'                      => 'required',
            'municipality_id'                   => 'required',
            'cost_repair_work'                  => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_materials'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_equipment'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_construction_control'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_other_expenses'               => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_municipal'                   => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_individuals'                 => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_legal_entities'              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_republic'                    => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'population_size'                   => ['regex:/^([0-9]{0,11})?$/'],
            'population_size_in_congregation'   => ['regex:/^([0-9]{0,11})?$/'],
            'total_application_points'          => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'points_from_administrator'         => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
        ], $rules);
    }

    public function getPublishedRulesValidation($rules = []): array
    {
        return array_merge([
            'user_id'                                       => 'required',
            'contest_id'                                    => 'required',
            'project_name'                                  => 'required',
            'municipality_id'                               => 'required',
            'project_typology'                              => 'required',
            'description_problem'                           => 'required',
            'cost_repair_work'                              => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_materials'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_purchasing_equipment'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_construction_control'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'cost_other_expenses'                           => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'expected_results'                              => 'required',
            'funds_municipal'                               => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_individuals'                             => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_legal_entities'                          => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'funds_republic'                                => ['required', 'regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'population_that_benefit_from_results_project'  => 'required',
            'population_size'                               => 'required|numeric',
            'population_size_in_congregation'               => 'required|numeric',
            'population_in_project_implementation'          => 'required',
            'population_in_project_provision'               => 'required',
            'implementation_date'                           => 'required',
            'comment'                                       => 'required',
            'total_application_points'                      => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
            'points_from_administrator'                     => ['regex:/^([0-9]{0,20})$|^([0-9]{1,20})(\,|\.)([0-9]{1,2})$/'],
        ], $rules);
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
            'project_name' => '1. Наименование проекта для предоставления субсидий на поддержку местных инициатив граждан, проживающих в муниципальных образованиях в Республике Карелия:',
            'municipality_id' => '2. Муниципальное образование в Республике Карелия или Населенный пункт:',
            'population_size_settlement' => '3. Численность населения населенного пункта:',
            'project_typology' => '4. Типология проекта:',
            'extracts' => '5. Наличие выписки из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта:',
            'description_problem' => '6.Описание проблемы, на решение которой направлен проект:',
            'cost_repair_work' => '7.1.1. Стоимость ремонтных работ',
            'comment_on_cost_repairs' => '7.1.2. Комментарий к стоимости ремонтных работ',
            'cost_purchasing_materials' => '7.2.1. Стоимость приобретения материалов',
            'comment_on_cost_purchasing_materials' => '7.2.2. Комментарий к стоимости приобретения материалов',
            'cost_purchasing_equipment' => '7.3.1. Стоимость приобретения оборудования',
            'comment_on_cost_purchasing_equipment' => '7.3.2. Комментарий к стоимости приобретения оборудования',
            'cost_construction_control' => '7.4.1. Стоимость строительного контроля',
            'comment_on_cost_construction_control' => '7.4.2. Комментарий к стоимости строительного контроля',
            'cost_other_expenses' => '7.5.1. Стоимость прочих расходов',
            'comment_on_cost_other_expenses' => '6.5.2. Комментарий к стоимость прочих расходов',
            'documentation' => '8. Наличие технической, проектной и сметной документации:',
            'expected_results' => '9.Ожидаемые результаты:',
            'funds_municipal' => '10.1. Средства бюджета муниципального образования:',
            'funds_individuals' => '10.2. Безвозмездные поступления от физических лиц (жителей):',
            'funds_legal_entities' => '10.3. Безвозмездные поступления от юридических лиц:',
            'funds_republic' => '10.4. Средства бюджета Республики Карелия:',
            'planned_sources_financing' => '11. Планируемые источники финансирования мероприятий проекта:',
            'gratuitous_receipts' => '12. Расшифровка безвозмездных поступлений от юридических лиц:',
            'population_that_benefit_from_results_project' => '13. Население, которое будет регулярно пользоваться результатами от реализации проекта:',
            'population_size' => '14. Количество человек:',
            'population_size_in_congregation' => '15. Количество лиц, принявших участие в собрании граждан:',
            'protocols' => '16. Протоколы собрания:',
            'population_in_project_implementation' => '17. Участие населения в реализации проекта:',
            'operating_and_maintenance_costs' => '18. Расходы на эксплуатацию и содержание муниципального имущества, предусмотренного проектом в первый год после завершения реализации проекта:',
            'population_in_project_provision' => '19. Участие населения в обеспечении эксплуатации и содержании муниципального имущества, предусмотренного проектом, после завершения реализации проекта:',
            'questionnaires' => '20. Предварительное обсуждение проекта (опросные листы, анкеты, собрания, подомовой обход и т.д.):',
            'implementation_date' => '21. Ожидаемый срок реализации проекта',
            'comment' => '22. Дополнительная информация и комментарии',
            'is_unpaid_work_of_population' => '23. Неоплачиваемый труд населения',
            'is_media_participation' => '24. Участие СМИ',
            'mass_media' => '25. Участие СМИ',
            'total_application_points' => '26. Общие баллы по заявке',
            'points_from_administrator' => '27. Баллы от администратора',
            'comment_on_points_from_administrator' => '28. Комментарий к баллам от администратора',
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

    public function getIsPopulationInProjectImplementationNewAttribute()
    {
        $dateSeparated = Carbon::parse('23.11.2023');
        $dateCreated = Carbon::parse($this->created_at);
        $dateDiff = $dateSeparated->diffInDays($dateCreated, false);
        return $dateDiff >= 0;
//            || empty($this->population_in_project_implementation)

    }

    public function getIsPopulationInProjectProvisionNewAttribute()
    {
        $dateSeparated = Carbon::parse('23.11.2023');
        $dateCreated = Carbon::parse($this->created_at);
        $dateDiff = $dateSeparated->diffInDays($dateCreated, false);
        return $dateDiff >= 0;
//            || empty($this->population_in_project_provision)

    }

    public function getIsProjectImplementationProvidesInformationalSupportNewAttribute()
    {
        $dateSeparated = Carbon::parse('23.11.2023');
        $dateCreated = Carbon::parse($this->created_at);
        $dateDiff = $dateSeparated->diffInDays($dateCreated, false);
        return $dateDiff >= 0;
//            || empty($this->is_media_participation)
    }

}
