<?php

namespace App\Models\Import;

use App\Models\DefaultModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationTOS extends DefaultModel
{
    protected $connection = 'import';

    protected $table = 'applications_tos';

    /**
     * @var array
     */
    protected $fillable = [

        // Общее
        'user_id', // Пользователь подавший заявку
        'contest_id', // Проводимый конкурс
        'municipality_id', // Заявитель (полное наименование муниципального образования)
        'tos_name', // Наименование ТОС
        'tos_1', // Населенный пункт (с указанием района, поселения), где реализуется проект
        'tos_2', // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
        'tos_3', // Номенклатурный номер ТОС
        'tos_4', // Является ли ТОС юридическим лицом (нет/да, дата государственной регистрации в качестве юридического лица)
        'tos_5', // Сведения о председателе ТОС (фамилия, имя, отчество, контактный телефон, электронная почта)
        'tos_6', // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
        'tos_7', // Количество зарегистрированных граждан в ТОС
        'tos_8', // Кол-во жителей ТОС
        'tos_9', // Количества присутствующих на общем собрании членов ТОС
        'tos_date_filling_in', // Дата заполнения


        // Социально-значимые проекты ТОС
        'ssp_name_project', // Наименование проекта
        'ssp_directions_project', // Направление проекта
//        'preliminary_work_on_selection_project', // Файл // Предварительная работа по выбору проекта
        'ssp_3', // Описание актуальности проблемы, на решение которой направлен проект
        'ssp_4', // Цель проекта
        'ssp_5', // Задачи проекта
        'ssp_6', // Дата начала реализации проекта
        'ssp_6_end', // Дата окончания реализации проекта
        'calendar_plan_work_on_project', // Календарный план работ по проекту
        'information_project_support_info', // Информационное сопровождение проекта
//        'information_project_support', // Файл // Информационное сопровождение проекта
        'ssp_8', // Ожидаемые результаты реализации проекта
        'ssp_9', // Количество человек (благополучателей), которые будут пользоваться результатами проекта
        'ssp_10', // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
        'ssp_budget_1', // Общая стоимость проекта
        'ssp_budget_2', // Средства бюджета Республики Карелия
        'ssp_budget_3', // Привлеченные средства
        'ssp_budget_4', // Внебюджетные источники
        'ssp_budget_5', // Средства ТОС
        'ssp_budget_6', // Средства юридических лиц
        'ssp_budget_7', // Средства местного бюджета
//        'extract_from_registry', // Файл // Выписка из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта
//        'documentation', // Файл // Техническая, проектная и сметная или иная документация, лицензия разработчика сметы
//        'letter_guarantee', // Файл // Гарантийное письмо администрации муниципального образования о принятии в собственность муниципального образования объектов, реализованных в рамках проекта, в течение трех месяцев со дня окончания работ по проекту
//        'other_documents', // Файл // Иные документы и фотоматериалы, подтверждающие актуальность и остроту проблемы и позволяющие наиболее полно описать проект
        'ssp_12', // Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.) (описать виды участия)
        'ssp_13', // Участие населения в обеспечении эксплуатации и содержании объекта, после   завершения проекта (описать виды участия)
        'ssp_11', // Реализацией проекта предусмотрено его информационное сопровождение
        'ssp_14', // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)

        // Лучшее ТОС
        'best_1', // Организация культурно-массовых мероприятий, праздников, иных культурно-просветительных акций (не более 1 страницы)
        'best_2', // Проведение спортивных соревнований, гражданско-патриотических игр, туристических выездов (не более 1 страницы)
        'best_3', // Проведение мероприятий, направленных на профилактику наркомании, алкоголизма и формирование здорового образа жизни (не более 1 страницы)
        'best_4', // Наличие клубов, секций кружков, организованных при ТОС (не более 1 страницы)
        'best_5', // Проведение мероприятий по организации благоустройства и улучшения санитарного состояния территории ТОС (не более  1 страницы)
        'best_6', // Количество объектов социальной направленности, восстановленных, отремонтированных или построенных силами ТОС (не более 1 страницы)
        'best_7', // Оказание помощи многодетным семьям, инвалидам, одиноким пенсионерам, малоимущим гражданам (не более 1 страницы)
        'best_8', // Создание на территории ТОС уголка здорового образа жизни, разработка буклетов, выпуск стенгазет по пропаганде здорового образа жизни (не более 1 страницы)
        'best_9', // Участие членов ТОС в совместных с сотрудниками полиции профилактических мероприятиях, связанных с профилактикой преступлений и иных правонарушений (не более 1 страницы)
        'best_10', // Проведение мероприятий по профилактике пожаров (не более 1 страницы)
        'best_11', // Проведение ТОСами совещаний и семинаров с участием органов местного самоуправления (не более 0,5 страницы)
        'best_12', // Размещение информации в средствах массовой информации и в информационно-телекоммуникационной сети Интернет о деятельности ТОС по каждому направлению деятельности
        'best_13', // Участие  ТОС в конкурсах за предыдущие три года
        'best_14', // Участие ТОС в конкурсах за предыдущие три года
        'best_15', // Награды ТОС и членов ТОС за тосовскую деятельность (за последние три года)

        'status', // Статус заявки

        // Лучшая практика гражданских инициатив

        'contest_nomination',
        'tos_category',
        'tos_5_fio',
        'tos_5_address',
        'tos_5_phone',
        'tos_5_email',
        'tos_4_date_reg',
        'tos_4_ogrn',
        'tos_4_inn',
        'tos_4_kpp',
        'tos_4_bank_details',
        'website',
        'vk',
        'ok',
        'fb',
        'twitter',
        'instagram',
        'practice_name',
        'practice_purpose',
        'practice_tasks',
        'practice_implementation_geography',
        'activity_social_significance',
        'problem_description',
        'list_documents_regulating_activity',
        'implementation_resources_involved_practice_own',
        'implementation_resources_involved_practice_budget',
        'implementation_resources_involved_practice_other',
        'calc_pri',
        'calc_npbp',
        'calc_nip',
        'calc_varp',
        'calc_papiwaf',
        'calc_wsdop',
        'calc_epfpi',
        'calc_epfpb',
        'calc_vaebf',
        'calc_uvm',
        'calc_uspm',
        'calc_nmh',
        'calc_ciaaaatosim',

        'achieved_results', // Укажите основные результаты, достигнутые при реализации практики (проекта)
    ];

    public $with = [
        'preliminary_work_on_selection_project',
        'information_project_support',
        'extract_from_registry',
        'documentation',
        'letter_guarantee',
        'other_documents',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function getContestNameAttribute()
    {
        return $this->contest ? $this->contest->contest_title : null;
    }

    public function getTos4StringAttribute()
    {
        return !!$this->tos_4 ? 'Да' : 'Нет';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTosNamesAttribute()
    {
        if (!!$this->user && !!$this->user->municipality) {

            $tos = RegisterTos::where('nmrgo', $this->user->municipality->name)->orWhere('npvsr', $this->user->municipality->name)->get()->pluck('nsu', 'id')->toArray();

            return $tos;
        } else {

            if (!!Auth::user()->municipality) {

                $tos = RegisterTos::where('nmrgo', Auth::user()->municipality->name)->orWhere('npvsr', Auth::user()->municipality->name)->get()->pluck('nsu', 'id')->toArray();

                return $tos;
            } else {

                return [];
            }
        }
    }

    public function getTosNameAttribute($tosName)
    {
        return (int) $tosName;
    }

    public function getAppGlavaAttribute()
    {
        return $this->user ? $this->user->app_glava : null;
    }

    public function getAppGlavaPhoneAttribute()
    {
        return $this->user ? $this->user->app_glava_phone : null;
    }

    public function getAppGlavaEmailAttribute()
    {
        return $this->user ? $this->user->app_glava_email : null;
    }

    public function getAppPostAddressAttribute()
    {
        return $this->user ? $this->user->app_post_address : null;
    }

    public function getAppExecutorAttribute()
    {
        return $this->user ? $this->user->app_executor : null;
    }

    public function getAppExecutorPhoneAttribute()
    {
        return $this->user ? $this->user->app_executor_phone : null;
    }

    public function getAppExecutorEmailAttribute()
    {
        return $this->user ? $this->user->app_executor_email : null;
    }

    public function municipalityThrough()
    {
        return $this->hasOneThrough(Municipality::class, User::class, 'id', 'id', 'user_id', 'municipality_id');
    }

    public function getMunicipalityFullNameAttribute()
    {
        if (!!$this->municipality) {

            $municipalityParents = collect($this->municipalityParents($this->municipality))
                ->reverse()
                ->map(function ($value) {

                    return $value->name;
                })
                ->implode(', ')
            ;

            return "{$municipalityParents}";
        }

        return null;
    }

    public function municipalityParents($municipality, $result = [])
    {
        array_push($result, $municipality);

        if (!!$municipality->parent) {

            $result = array_merge($result, $this->municipalityParents($municipality->parent));
        }

        return $result;
    }

    public function municipalityTopLevel($municipality)
    {
        if ($municipality->parent) {

            $result = $this->municipalityTopLevel($municipality->parent);
        } else {
            $result = $municipality;
        }

        return $result;
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

    // Предварительная работа по выбору проекта
    public function preliminary_work_on_selection_project()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','preliminary_work_on_selection_project')
            ;
    }

    public function getPreliminaryWorkOnSelectionProjectStringNameAttribute()
    {
        $result = $this
            ->preliminary_work_on_selection_project
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Информационное сопровождение проекта
    public function information_project_support()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group', 'information_project_support')
            ;
    }

//    public function getInformationProjectSupportStringNameAttribute()
//    {
//        $result = $this
//            ->informationProjectSupport
//            ->map(function ($image) {
//
//                return $image->original_name;
//            })
//            ->implode(", ")
//        ;
//
//        return $result;
//    }

    // Выписка из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта
    public function extract_from_registry()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','extract_from_registry')
            ;
    }

    public function getExtractFromRegistryStringNameAttribute()
    {
        $result = $this
            ->extract_from_registry
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Техническая, проектная и сметная или иная документация, лицензия разработчика сметы
    public function documentation()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','documentation')
            ;
    }

    public function getDocumentationStringNameAttribute()
    {
        $result = $this
            ->documentation
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Гарантийное письмо администрации муниципального образования о принятии в собственность муниципального образования объектов, реализованных в рамках проекта, в течение трех месяцев со дня окончания работ по проекту
    public function letter_guarantee()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','letter_guarantee')
            ;
    }

    public function getLetterGuaranteeStringNameAttribute()
    {
        $result = $this
            ->letter_guarantee
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Иные документы и фотоматериалы, подтверждающие актуальность и остроту проблемы и позволяющие наиболее полно описать проект
    public function other_documents()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','other_documents')
            ;
    }

    public function getOtherDocumentsStringNameAttribute()
    {
        $result = $this
            ->other_documents
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    public function getCalendarPlanWorkOnProjectAttribute($calendarPlanWorkOnProject)
    {
        return collect(json_decode($calendarPlanWorkOnProject, true))
            ->filter(function ($value) {

                if (!!$value['stages'] && !!$value['period'] && !!$value['executors']) {

                    return $value;
                }
            })
            ->toArray()
            ;
    }

    public function getCalendarPlanWorkOnProjectFirstAttribute()
    {
        return collect($this->calendarPlanWorkOnProject)->first();
    }

    public function getSsp13Attribute($ssp13)
    {
        $sspResult = collect(json_decode($ssp13, true))
            ->filter(function ($value) {

                if (!!$value['name']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $sspResult;
    }

    public function getSsp13StringAttribute()
    {
        return collect($this->ssp_13)
            ->map(function ($value) {

                return implode(', ', $value);
            })
            ->implode('; ')
            ;
    }

    public function getSsp12Attribute($ssp12)
    {
        $sspResult = collect(json_decode($ssp12, true))
            ->filter(function ($value) {

                if (!!$value['name']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $sspResult;
    }

    public function getSsp12StringAttribute()
    {
        return collect($this->ssp_12)
            ->map(function ($value) {

                return implode(', ', $value);
            })
            ->implode('; ')
            ;
    }

    public function getSsp11Attribute($ssp11)
    {
        $sspResult = collect(json_decode($ssp11, true))
            ->filter(function ($value) {

                if (!!$value['name']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $sspResult;
    }

    public function getTos6Attribute($tos6)
    {
        $projectBudgetResult = collect(json_decode($tos6, true))
            ->filter(function ($value) {

                if (!!$value['fio'] && !!$value['phone'] && !!$value['email']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $projectBudgetResult;
    }

    public function getTos6FirstAttribute()
    {
        return collect($this->tos6)->first();
    }

    public function getSspDirectionsProjectNameAttribute()
    {
        return config("app.common.tos_directions.{$this->ssp_directions_project}", null);
    }

    public function getTos6StringAttribute()
    {
        return collect($this->tos_6)
            ->map(function ($value) {

                return implode(', ', $value);
            })
            ->implode('; ')
            ;
    }

    public function getInformationProjectSupportInfoAttribute($informationProjectSupportInfo)
    {
        $projectBudgetResult = collect(json_decode($informationProjectSupportInfo, true))
            ->filter(function ($value) {

                if (!!$value['file'] && !!$value['link']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $projectBudgetResult;
    }

    public function getListDocumentsRegulatingActivityAttribute($listDocumentsRegulatingActivity)
    {
        $result = collect(json_decode($listDocumentsRegulatingActivity, true))
            ->filter(function ($value) {

                return $value;
            })
            ->toArray()
        ;

        return $result;
    }

    public function getLinksInformationProjectSupportInfoAttribute()
    {
        return collect($this->information_project_support_info)->map(function ($value) {return $value["link"];})->implode(', ');
    }

    public function getBest1Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest2Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest3Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest4Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest5Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest6Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest7Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest8Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest9Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest10Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest11Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest12Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['count'] && !!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest13Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['name'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest14Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['name'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    public function getBest15Attribute($best)
    {
        $bestResult = collect(json_decode($best, true))
            ->filter(function ($value) {

                if (!!$value['description'] && !!$value['numbers']) {

                    return $value;
                }
            })
            ->toArray()
        ;

        return $bestResult;
    }

    // Приложение 1 к положению (Заявление о включении ТОС в реестр)
    public function appOne()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
            ->where('group','app_one')
            ;
    }

    public function getAppOneStringNameAttribute()
    {
        $result = $this
            ->appOne
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

    // Приложение 5 (Согласование с главой администрации об участии в конкурсе)
    public function app_four()
    {
        return $this
            ->belongsToMany(Attachment::class, 'attachmentable', 'attachmentable_id', 'attachment_id')
            ->withPivot('attachmentable_type')
            ->wherePivot('attachmentable_type', str_replace('Import\\', '', self::getMorphClass()))
//            ->where('group','app_five')
            ;
    }

    public function getAppFiveStringNameAttribute()
    {
        $result = $this
            ->appFive
            ->map(function ($image) {

                return $image->original_name;
            })
            ->implode(", ")
        ;

        return $result;
    }

}
