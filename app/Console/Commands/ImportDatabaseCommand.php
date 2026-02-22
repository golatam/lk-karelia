<?php

namespace App\Console\Commands;

use App\Models\Import\Municipality as ImportMunicipality;
use App\Models\Import\Register as ImportRegister;
use App\Models\Import\User as ImportUser;
use App\Models\Import\Contest as ImportContest;
use App\Models\Import\ApplicationTOS as ImportApplicationTOS;
use App\Models\Import\Application as ImportApplication;
use App\Models\Matrix;
use App\Models\Municipality;
use App\Models\Register;
use App\Models\User;
use App\Models\Contest;
use App\Models\LPTOSApplication;
use App\Models\LTOSApplication;
use App\Models\PPMIApplication;
use App\Models\SZPTOSApplication;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ImportDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:db {--municipalities} {--registers} {--users} {--contests} {--applications} {--szptos} {--ltos} {--lptos} {--ppmi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт из старой базы в новую';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('municipalities')) {

            $this->municipalities();

            return 1;
        } elseif ($this->option('registers')) {

            $this->registers();

            return 1;
        } elseif ($this->option('users')) {

            $this->users();

            return 1;
        } elseif ($this->option('contests')) {

            $this->contests();

            return 1;
        } elseif ($this->option('applications')) {

            $this->applications();

            return 1;
        } elseif ($this->option('szptos')) {

            $this->szptos();

            return 1;
        } elseif ($this->option('ltos')) {

            $this->ltos();

            return 1;
        } elseif ($this->option('lptos')) {

            $this->ltos();

            return 1;
        } elseif ($this->option('ppmi')) {

            $this->ppmi();

            return 1;
        } else {

            $this->all();

            return 1;
        }
    }

    public function all()
    {
        $this->municipalities();
        $this->registers();
        $this->users();
        $this->contests();
        $this->applications();
    }

    public function municipalities($i = 0)
    {
        try {

            $models = ImportMunicipality::all();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,
                    'parent_id' => $model->parent_id,
                    'name' => $this->trimall($model->name),
                ];

                $municipality = Municipality::create($data);

                if (!!$municipality) {

                    $i++;
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} муниципалитетов из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function registers($i = 0)
    {
        try {

            $models = ImportRegister::all();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $municipalityNameRegion = Municipality::where('name', $this->trimall($model->nmrgo))->first();
                $municipalityNameSettlement = Municipality::where('name', $this->trimall($model->npvsr))->first();

                $data = [
                    'id' => $model->id,
                    'name_region' => $municipalityNameRegion ? $municipalityNameRegion->id : 0,               // Наименование (муниципального района/городского округа)
                    'name_settlement' => $municipalityNameSettlement ? $municipalityNameSettlement->id : 0,       // Наименование поселения в составе района
                    'name_according_charter' => $model->nsu,                    // Наименование (согласно уставу)
                    'is_legal_entity' => $model->yaltosyul === 'нет' ? 0 : 1,   // Является ли ТОС юридическим лицом (да/нет)
                    'address' => $model->amtos,                                 // Адрес местонахождения ТОС (для юридических лиц - юридический адрес)
//                    'inn => $model',                                        // ИНН
//                    'kpp' => $model,                      // КПП
//                    'ogrn',                     // ОГРН
//                    'bank_details',             // Банковские реквизиты
//                    'site',                     // Официальный сайт
//                    'vk',                       // Официальная группа в социальной сети ВКОНТАКТЕ
//                    'ok',                       // Официальная группа в социальной сети ОДНОКЛАССНИКИ
//                    'fb',                       // Официальная группа в социальной сети FACEBOOK
//                    'twitter',                  // Официальная группа в социальной сети TWITTER
//                    'instagram',                // Официальная группа в социальной сети INSTAGRAM
                    'boundaries' => $model->gtos,                               // Границы ТОС
                    'legal_act' => $model->mpaouutos,                           // Муниципальный правовой акт об утверждении устава ТОС (вид документа, дата, номер
                    'registration_date_charter' => $model->registration_date,   // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
//                    'registration_date_tos',    // Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК
//                    'nomenclature_number',      // Номенклатурный номер ТОС
                    'number_members' => $model->kchtos,           // Кол-во членов ТОС
                    'number_citizens' => $model->kgpvgtos,          // Кол-во граждан, проживающих в границах ТОС
                    'fio_chief' => $model->fio_rtos,                // ФИО руководителя ТОС
                    'email_chief' => $model->email_rtos,              // Электронный адрес руководителя ТОС
                    'phone_chief' => $model->phone_rtos,              // Мобильный телефон руководителя ТОС
                    'note' => $model->note,                     // Примечание
                ];

                $register = Register::create($data);

                if (!!$register) {

                    $i++;
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} элементов реестра из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function users($i = 0)
    {
        try {

            $models = ImportUser::all();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,
                    'last_name' => '',                                  // Фамилия
                    'first_name' => $model->name,                       // Имя
                    'second_name' => '',                                // Отчество
                    'email' => $model->email,                           // Электронная почта
                    'email_verified_at' => $model->email_verified_at,   // Время проверки электронной почты
                    'phone' => '',                                      // Телефон
                    'password' => $model->password,                     // Пароль
                    'avatar' => '',                                     // Аватар
                    'role_id' => $model->roleId,                        // ID роли
                    'is_active' => 1,                                   // Является активным
                    'municipality_id' => $model->municipality_id,       // Муниципалитет
                    'register_id' => $model->register_tos_id,           // ТОС из реестра
                    'municipality_chief' => $model->app_glava,          // Глава (глава администрации) муниципального образования
                    'municipality_phone' => $model->app_glava_phone,    // Контактный телефон администрации
                    'municipality_email' => $model->app_glava_email,    // E-mail администрации
                    'municipality_address' => $model->app_post_address, // Адрес администрации
                    'executor' => $model->app_executor,                 // Исполнитель
                    'executor_phone' => $model->app_executor_phone,     // Контактный телефон исполнителя
                    'executor_email' => $model->app_executor_email,     // E-mail исполнителя
                ];

                $user = User::firstOrNew(['id' => $model->id]);

                $user->fill($data)->save();

                $i++;

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} пользователей из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function contests($i = 0)
    {
        try {

            $models = ImportContest::all();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,
                    'type' => $model->contest_type,                 // Тип конкурса
                    'name' => $model->contest_title,                // Название конкурса
                    'description' => $model->contest_description,   // Описание конкурса
                    'end_date_active' => $model->end_date_active,   // Конечная дата активности
                    'is_active' => $model->contest_active,          // Сейчас активен для подачи заявок
                ];

                $register = Contest::create($data);

                if (!!$register) {

                    $i++;
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} конкурсов из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function applications()
    {
        $this->szptos();
        $this->ltos();
        $this->lptos();
        $this->ppmi();
    }

    public function lptos($i = 0)
    {
        try {

            $models = ImportApplicationTOS::whereHas('contest', function ($application) {
                $application->where('contest_type', 'lptos');
            })->get();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,
                    'user_id' => $model->user_id,                           // Участник
                    'contest_id' => $model->contest_id,                     // Конкурс
                    'contest_nomination' => $model->contest_nomination,     // 1. Укажите номинацию конкурса
                    'category' => $model->tos_category,                     // 2. Укажите категорию:
                    'municipality_id' => $model->municipality_id,           // 3. Наименование муниципального образования:
                    'register_id' => (int) $model->tos_name,                // 4. Полное наименование ТОС
                    'nomenclature_number' => $model->tos_3,                 // 5. Номенклатурный номер ТОС
                    'date_registration_charter' => $model->tos_2,           // 6. Дата регистрации устава ТОС уполномоченным органом местного самоуправления:
                    'population_size_in_tos' => $model->tos_7,              // 7. Количество жителей, проживающих в границах ТОС
                    'full_name_chairman_tos' => $model->tos_5_fio,          // 8. ФИО председателя ТОС
                    'tos_address' => $model->tos_5_address,                 // 9. Почтовый адрес (с указанием индекса)
                    'tos_phone' => $model->tos_5_phone,                     // 10. Номер мобильного телефона
                    'tos_email' => $model->tos_5_email,                     // 11. Адрес электронной почты
                    'is_tos_legal_entity' => $model->tos_4,                 // 12. Является ли ТОС юридическим лицом
                    'registration_date_tos' => $model->tos_4_date_reg,      // 13.1. Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК
                    'ogrn' => $model->tos_4_ogrn,                           // 13.2. ОГРН
                    'inn' => $model->tos_4_inn,                             // 13.3. ИНН
                    'kpp' => $model->tos_4_kpp,                             // 13.4. КПП
                    'bank_details' => $model->tos_4_bank_details,           // 13.5. Банковские реквизиты:
                    'website' => $model->website,                           // 14.1. - официальный сайт
                    'vk' => $model->vk,                                     // 14.2. - официальная группа в социальной сети ВКОНТАКТЕ
                    'ok' => $model->ok,                                     // 14.3. - официальная группа в социальной сети ОДНОКЛАССНИКИ
                    'fb' => $model->fb,                                     // 14.4. - официальная группа в социальной сети FACEBOOK
                    'twitter' => $model->twitter,                           // 14.5. - официальная группа в социальной сети TWITTER
                    'instagram' => $model->instagram,                       // 14.6. - официальная группа в социальной сети INSTAGRAM
                    'practice_name' => $model->practice_name,               // 15. Название практики (проекта):
                    'practice_purpose' => $model->practice_purpose,         // 16. Цель практики (проекта):
                    'practice_tasks' => $model->practice_tasks,             // 17. Задачи практики (проекта):
                    'duration_practice' => $model->ssp_6,                   // 18. Срок реализации практики (проекта)
                    'practice_implementation_geography' => $model->practice_implementation_geography,   // 19. География реализации практики (проекта)
                    'activity_social_significance' => $model->activity_social_significance,             // 20. Социальная значимость деятельности ТОС:
                    'problem_description' => $model->problem_description,                               // 21. Описание проблемы, на решение которой была направлена практика (проект)
                    'number_people_part_in_project_implementation' => $model->ssp_9,                    // 22. Количество человек, принявших участие в реализации проекта
                    'implementation_resources_involved_practice_own' => $model->implementation_resources_involved_practice_own,         // 24.1. Собственные финансовые средства:
                    'implementation_resources_involved_practice_budget' => $model->implementation_resources_involved_practice_budget,   // 24.2. Привлеченные финансовые средства (из регионального и муниципального бюджетов - при наличии):
                    'implementation_resources_involved_practice_other' => $model->implementation_resources_involved_practice_other,     // 24.3. Организационные ресурса: (волонтерство, благотворительность, социальное партнерство, информационная поддержка проекта
                    'achieved_results' => $model->achieved_results,                                     // 25. Укажите основные результаты, достигнутые при реализации практики (проекта)
                    'coverage_information_media' => $model->calc_ciaaaatosim,                           // 26. Освещение информации о деятельности и достижениях ТОС в средствах массовой информации
                    'total_application_points' => $model->total_application_points,                     // 27. Общие баллы по заявке
                    'status' => $model->status,                                                         // Статус заявки
                ];

                $application = LPTOSApplication::create($data);

                if (!!$application) {

                    // matrix list_documents_regulating_activity
                    // Перечень документов, регламентирующих деятельность в рамках реализации практики (проекта)
                    foreach ($model->list_documents_regulating_activity as $item) {

                        $matrixData = [
                            'group' => 'list_documents_regulating_activity',
                            'user_id' => $model->user_id,
                            'field7' => $item['date'],
                            'field8' => $item['name'],
                            'field9' => $item['note'],
                            'field10' => $item['number'],
                        ];

                        $application
                            ->list_documents_regulating_activity()
                            ->create($matrixData)
                        ;
                    }

                    $i++;
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} заявок на конкурс Лучшая практика гражданских инициатив из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function ppmi($i = 0)
    {
        try {

            $models = ImportApplication::whereHas('contest', function ($application) {
                $application->where('contest_type', 'ppmi');
            })->get();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,                                                         // ID
                    'user_id' => $model->user_id,                                               // ID пользователя
                    'contest_id' => $model->contest_id,                                         // ID конкурса
                    'municipality_id' => $model->municipality_id ?? $model->user->municipality_id,                               // ID муниципалитета
                    'project_name' => $model->{"1_0"},                                          // Наименование проекта
                    'population_size_settlement' => $model->{"2_3"},                            // Численность населенного пункта
                    'project_typology' => $model->{"3_1"},                                      // Типология проекта
                    'description_problem' => $model->{"3_3"},                                   // Описание проблемы
                    'cost_repair_work' => $model->{"3_4_1_value"},                              // Стоимость ремонтных работ
                    'comment_on_cost_repairs' => $model->{"3_4_1_comment"},                     // Комментарий к стоимости ремонтных работ
                    'cost_purchasing_materials' => $model->{"3_4_2_value"},                     // Стоимость приобретения материалов
                    'comment_on_cost_purchasing_materials' => $model->{"3_4_2_comment"},        // Комментарий к стоимости приобретения материалов
                    'cost_purchasing_equipment' => $model->{"3_4_3_value"},                     // Стоимость приобретения оборудования
                    'comment_on_cost_purchasing_equipment' => $model->{"3_4_3_comment"},        // Комментарий к стоимости приобретения оборудования
                    'cost_construction_control' => $model->{"3_4_4_value"},                     // Стоимость строительного контроля
                    'comment_on_cost_construction_control' => $model->{"3_4_4_comment"},        // Комментарий к стоимости строительного контроля
                    'cost_other_expenses' => $model->{"3_4_5_value"},                           // Стоимость прочих расходов
                    'comment_on_cost_other_expenses' => $model->{"3_4_5_comment"},              // Комментарий к стоимость прочих расходов
                    'expected_results' => $model->{"3_6"},                                      // Ожидаемые результаты
                    'funds_municipal' => $model->{"4_1_1_value"},                               // Средства муниципального образования
                    'funds_individuals' => $model->{"4_1_2_1_value"},                           // Безвозмездно от физ. лиц
                    'funds_legal_entities' => $model->{"4_1_2_2_value"},                        // Безвозмездно от юр. лиц
                    'funds_republic' => $model->{"4_1_3_value"},                                // Средства республики
                    'population_that_benefit_from_results_project' => $model->{"4_2_comment"},  // Население, которое будет регулярно пользоваться результатами от реализации проекта
                    'population_size' => $model->{"4_2_value"},                                 // Кол-во человек населения
                    'population_size_in_congregation' => $model->{"4_3_value"},                 // Кол-во лиц в собрании
                    'population_in_project_implementation' => $model->{"4_4"},                  // Участие населения в реализации проекта
                    'population_in_project_provision' => $model->{"4_6"},                       // Участие населения в обеспечении проекта
                    'implementation_date' => $model->{"project_implementation_period"},         // Срок реализации
                    'comment' => $model->{"additional_information"},                            // Дополнительная информация и комментарии
                    'is_unpaid_work_of_population' => $model->{"is_unpaid_work_of_population"}, // Неоплачиваемый труд населения
                    'is_media_participation' => $model->{"is_media_participation"},             // Участие СМИ
                    'total_application_points' => $model->{"total_application_points"},         // Общие баллы по заявке
                    'points_from_administrator' => $model->{"points_from_administrator"},       // Баллы от администратора
                    'comment_on_points_from_administrator' => $model->{"comment_on_points_from_administrator"}, // Комментарий к баллам от администратора
                    'status' => $model->status,                                                 // Статус заявки
                ];

                $application = PPMIApplication::create($data);

                if (!!$application) {

                    $this->saveMatrixPPMI($model, $application);

                    try {

                        $fileRelations = [
                            'extracts',                     // Наличие выписки из реестра муниципального имущества
                            'documentation',                // Наличие технической, проектной и сметной документации
                            'planned_sources_financing',    // Планируемые источники финансирования мероприятий проекта
                            'protocols',                    // Протоколы собрания
                            'questionnaires',               // Предварительное обсуждение проекта
                            'mass_media',                   // Участие СМИ
                            'acts',                         // Заверенные копии актов выполненных работ
                            'payment',                      // Заверенные копии документов подтверждающих оплату выполненных работ
                            'publications',                 // Заверенные копии публикаций в средствах массовой информации
                        ];

                        foreach ($fileRelations as $fileRelation) {

                            $this->createFiles($model, $application, $fileRelation);
                        }
                    } catch (\Exception $e) {

                        $this->output->newLine();
                        $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
                    }

                    $i++;
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} заявок на конкурс ППМИ из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function szptos($i = 0)
    {
        try {

            $models = ImportApplicationTOS::whereHas('contest', function ($application) {
                $application->where('contest_type', 'szptos');
            })->get();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,                                                                     // ID
                    'user_id' => $model->user_id,                                                           // Участник
                    'contest_id' => $model->contest_id,                                                     // Конкурс
                    'municipality_id' => $model->municipality_id,                                           // Населенный пункт, где реализуется проект
                    'register_id' => (int) $model->tos_name,                                                // Наименование ТОС
                    'region_id' => (int) $model->tos_name,                                                  // Наименование (муниципального района/городского округа), где реализуется проект
                    'settlement_id' => (int) $model->tos_name,                                              // Наименование поселения в составе района, где реализуется проект
                    'date_registration_charter' => Carbon::parse($model->tos_2),                            // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
                    'is_tos_legal_entity' => $model->tos_4,                                                 // Является ли ТОС юридическим лицом
                    'nomenclature_number' => $model->tos_3,                                                 // Номенклатурный номер ТОС
                    'full_name_chairman_tos' => !empty($model->tos_5_fio) ? $model->tos_5_fio : $model->tos_5,  // ФИО председателя ТОС
                    'tos_address' => $model->tos_5_address,                                                 // Почтовый адрес (с указанием индекса)
                    'tos_phone' => $model->tos_5_phone,                                                     // Номер мобильного телефона
                    'tos_email' => $model->tos_5_email,                                                     // Адрес электронной почты
                    'population_size_settlement' => $model->population_number,                              // Численность населения
                    'population_size_in_tos' => $model->tos_7,                                              // Количество жителей, проживающих в границах ТОС
                    'project_name' => $model->ssp_name_project,                                             // Наименование проекта
                    'project_direction' => $model->ssp_directions_project,                                  // Направление проекта
                    'problem_description' => $model->ssp_3,                                                 // Описание актуальности проблемы, на решение которой направлен проект
                    'project_purpose' => $model->ssp_4,                                                     // Цель проекта
                    'project_tasks' => $model->ssp_5,                                                       // Задачи проекта
                    'duration_practice_start' => Carbon::parse($model->ssp_6),                              // Дата начала реализации проекта
                    'duration_practice_end' => Carbon::parse($model->ssp_6_end),                            // Дата окончания реализации проекта
                    'results_project_implementation' => $model->ssp_8,                                      // Ожидаемые результаты реализации проекта
                    'number_beneficiaries' => $model->ssp_9,                                                // Количество человек (благополучателей), которые будут пользоваться результатами проекта
                    'description_need' => $model->ssp_10,                                                   // Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации
                    'total_cost_project' => $model->ssp_budget_1,                                           // Общая стоимость проекта
                    'budget_funds_republic' => $model->ssp_budget_2,                                        // Средства бюджета Республики Карелия
                    'funds_raised' => $model->ssp_budget_3,                                                 // Привлеченные средства
                    'extra_budgetary_sources' => $model->ssp_budget_4,                                      // Внебюджетные источники
                    'funds_tos' => $model->ssp_budget_5,                                                    // Средства ТОС
                    'funds_legal_entities' => $model->ssp_budget_6,                                         // Средства юридических лиц
                    'funds_local_budget' => $model->ssp_budget_7,                                           // Средства местного бюджета
                    'person_responsible_implementation_project' => $model->ssp_14,                          // Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)
                    'number_present_at_general_meeting' => $model->tos_9,                                   // Количество присутствующих на общем собрании членов ТОС
                    'is_grand_opening_with_media_coverage' => $model->is_grand_opening_with_media_coverage, // По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»
                    'date_filling_in' => $model->tos_date_filling_in,                                       // Дата заполнения заявки
                    'total_application_points' => $model->total_application_points,                         // Общие баллы по заявке
                    'points_from_administrator' => $model->points_from_administrator,                       // Баллы от администратора
                    'comment_on_points_from_administrator' => $model->comment_on_points_from_administrator, // Комментарий к баллам от администратора
                    'status' => $model->status,                                                             // Статус заявки
                ];

                $application = SZPTOSApplication::firstOrNew(['id' => $model->id]);

                $application->fill($data)->save();

                $this->saveMatrixSZPTOS($model, $application);

                $fileRelations = [
                    'preliminary_work_on_selection_project',    // Протоколы собраний по выбору проекта
                    'information_project_support',              // Информационное сопровождение проекта (файлы)
                    'extract_from_registry',                    // Выписка из реестра муниципального имущества (копии иных документов, подтверждающих право муниципальной собственности) на недвижимое имущество, предназначенное для реализации проекта
                    'documentation',                            // Техническая, проектная и сметная или иная документация, лицензия разработчика сметы
                    'letter_guarantee',                         // Гарантийное письмо администрации муниципального образования о принятии в собственность муниципального образования объектов, реализованных в рамках проекта, в течение трех месяцев со дня окончания работ по проекту
                    'other_documents',                          // Иные документы и фотоматериалы, подтверждающие актуальность и остроту проблемы и позволяющие наиболее полно описать проект
                    'app_four',                                 // Приложение 4 (Согласование с главой администрации об участии в конкурсе)
                ];

                foreach ($fileRelations as $fileRelation) {

                    $this->createFiles($model, $application, $fileRelation);
                }

                $i++;


                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} заявок на конкурс Социально значимые проекты ТОС из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function ltos($i = 0)
    {
        try {

            $models = ImportApplicationTOS::whereHas('contest', function ($application) {
                $application->where('contest_type', 'ltos');
            })->get();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $data = [
                    'id' => $model->id,
                    'user_id' => $model->user_id,                                                           // Участник
                    'contest_id' => $model->contest_id,                                                     // Конкурс
                    'municipality_id' => $model->municipality_id,                                           // Населенный пункт, где реализуется проект
                    'register_id' => (int) $model->tos_name,                                                // Наименование ТОС
                    'region_id' => (int) $model->tos_name,                                                  // Наименование (муниципального района/городского округа), где реализуется проект
                    'settlement_id' => (int) $model->tos_name,                                              // Наименование поселения в составе района, где реализуется проект
                    'date_registration_charter' => Carbon::parse($model->tos_2),                            // Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)
                    'is_tos_legal_entity' => $model->tos_4,                                                 // Является ли ТОС юридическим лицом
                    'nomenclature_number' => $model->tos_3,                                                 // Номенклатурный номер ТОС
                    'full_name_chairman_tos' => !empty($model->tos_5_fio) ? $model->tos_5_fio : $model->tos_5,  // ФИО председателя ТОС
                    'tos_address' => $model->tos_5_address,                                                 // Почтовый адрес (с указанием индекса)
                    'tos_phone' => $model->tos_5_phone,                                                     // Номер мобильного телефона
                    'tos_email' => $model->tos_5_email,                                                     // Адрес электронной почты
                    'population_size_in_tos' => $model->tos_7,                                              // Количество жителей, проживающих в границах ТОС
                    'date_filling_in' => $model->tos_date_filling_in,                                       // Дата заполнения заявки
                    'total_application_points' => $model->total_application_points,                         // Общие баллы по заявке
                    'points_from_administrator' => $model->points_from_administrator,                       // Баллы от администратора
                    'comment_on_points_from_administrator' => $model->comment_on_points_from_administrator, // Комментарий к баллам от администратора
                    'status' => $model->status,                                                             // Статус заявки
                ];

                $application = LPTOSApplication::firstOrNew(['id' => $model->id]);

                $application->fill($data)->save();

                $this->saveMatrixLTOS($model, $application);

                $i++;

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Добавлено {$i} заявок на конкурс Лучшее ТОС из {$models->count()}</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function trimall($line){
        $line = preg_replace('/ +/', ' ', $line);
        $line = preg_replace('/\r/', '', $line);
        $line = preg_replace('/\n/', '', $line);
        $line = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $line);
        $line = str_replace('﻿', '', $line);
        $line = str_replace('\xD0', '', $line);
        $line = str_replace('�', '', $line);
        return trim($line);
    }

    /**
     * --------------------------
     * Добавляем Файлы на сервер
     * --------------------------
     *
     * @param $fromModel
     * @param $toModel
     * @param $group
     * @return string|null
     */
    public function createFiles($fromModel, $toModel, $group)
    {
        foreach ($fromModel->{$group} as $item) {

            $path = $this->copyFile($item, $toModel);

            $originalNameToArray = explode('.', $item->original_name);

            $data = [
                'path' => $path,
                'name' => array_shift($originalNameToArray),
                'extension' => $item->extension,
                'group' => $group,
            ];

            $toModel
                ->{$group}()
                ->create($data)
            ;
        }
    }

    /**
     * ------------------------
     * Копируем файл на сервер
     * ------------------------
     *
     * @param $photoModel
     * @param $toModel
     * @return string|null
     */
    public function copyFile($photoModel, $toModel)
    {
        $domain = env('APP_URL_OLD');
        $linkToPhoto = "{$domain}/storage/{$photoModel->path}{$photoModel->name}.{$photoModel->extension}";
        $originalNameToArray = explode('.', $photoModel->original_name);
        $fileName = Str::slug(array_shift($originalNameToArray));

        $path = "/uploads/files/{$toModel->getTable()}/{$toModel->id}/{$fileName}.{$photoModel->extension}";

        $returnLinkToPhoto = public_path($path);

        if (!file_exists(dirname($returnLinkToPhoto))) {

            File::makeDirectory(dirname($returnLinkToPhoto), 0777, true);
        } else {

            File::chmod(dirname($returnLinkToPhoto), 0777);
        }

        $copy = copy($linkToPhoto, $returnLinkToPhoto);

        return $copy ? $path : null;
    }

    public function saveMatrixPPMI($fromModel, $toModel)
    {
        // Расшифровка безвозмездных поступлений от юридических лиц
        foreach ($fromModel->gratuitous_receipts as $item) {

            $matrixData = [
                'group' => 'gratuitous_receipts',
                'user_id' => $fromModel->user_id,
                'field1' => $item['name'],  // Наименование организации
                'field2' => $item['value'], // Денежный вклад, (рублей)
            ];

            $toModel
                ->gratuitous_receipts()
                ->create($matrixData)
            ;
        }

        // Расходы на эксплуатацию и содержание муниципального имущества, предусмотренного проектом в первый год после завершения реализации проекта
        foreach ($fromModel->operating_and_maintenance_costs as $item) {

            $matrixData = [
                'group' => 'operating_and_maintenance_costs',
                'user_id' => $fromModel->user_id,
                'field3' => $item['name'],          // Мероприятия
                'field4' => $item['value_one'],     // Средства из бюджета муниципального образования (руб. в год)
                'field5' => $item['value_two'],     // Средства юридических и физических лиц (руб. в год)
                'field6' => $item['value_three'],   // Средства приносящие доход деятельности (руб. в год)
            ];

            $toModel
                ->operating_and_maintenance_costs()
                ->create($matrixData)
            ;
        }
    }

    public function saveMatrixSZPTOS($fromModel, $toModel)
    {
        // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
        foreach ($fromModel->tos_6 as $item) {

            $matrixData = [
                'group' => 'list_members_council_tos',
                'user_id' => $fromModel->user_id,
                'field60' => $item['fio'],      // ФИО
                'field61' => $item['phone'],    // Контактный телефон
                'field62' => $item['email'],    // Электронная почта
            ];

            $toModel
                ->list_members_council_tos()
                ->create($matrixData)
            ;
        }

        // Календарный план работ по проекту
        foreach ($fromModel->calendar_plan_work_on_project as $item) {

            $matrixData = [
                'group' => 'calendar_plan_work_on_project',
                'user_id' => $fromModel->user_id,
                'field63' => $item['stages'],   // Основные этапы проекта и мероприятия
                'field64' => $item['period'],   // Срок реализации
                'field65' => $item['executors'],// Ответственные исполнители
            ];

            $toModel
                ->calendar_plan_work_on_project()
                ->create($matrixData)
            ;
        }

        // Информационное сопровождение проекта (указать, каким образом будет обеспечено освещение проекта в
        // целом и его ключевых мероприятий в СМИ,  социальных сетях (группы ТОС в социальных сетях, группа
        // Ассоциации ТОС в Республике Карелия в социальной сети «Вконтакте» (https://vk.com/tosrk), портал
        // «Инициативное бюджетирование в Республике Карелии» (инициативы-карелия.рф)), реклама, листовки,
        // специальные мероприятия, информирование партнеров.
        foreach ($fromModel->information_project_support_info as $item) {

            $matrixData = [
                'group' => 'information_project_support_info',
                'user_id' => $fromModel->user_id,
                'field66' => $item['file'], // Название файла
                'field67' => $item['link'], // Ссылки на подтверждени
            ];

            $toModel
                ->information_project_support_info()
                ->create($matrixData)
            ;
        }

        // Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.) - описать виды участия
        foreach ($fromModel->ssp_12 as $item) {

            $matrixData = [
                'group' => 'participation_population_in_implementation_project',
                'user_id' => $fromModel->user_id,
                'field68' => $item['name'], // Виды участия
            ];

            $toModel
                ->participation_population_in_implementation_project()
                ->create($matrixData)
            ;
        }

        // Участие населения в обеспечении эксплуатации и содержании объекта, после завершения проекта
        foreach ($fromModel->ssp_13 as $item) {

            $matrixData = [
                'group' => 'public_participation_in_operation_facility',
                'user_id' => $fromModel->user_id,
                'field69' => $item['name'], // Виды участия
            ];

            $toModel
                ->public_participation_in_operation_facility()
                ->create($matrixData)
            ;
        }

        // Реализацией проекта предусмотрено его информационное сопровождение
        foreach ($fromModel->ssp_11 as $item) {

            $matrixData = [
                'group' => 'project_implementation_provides_informational_support',
                'user_id' => $fromModel->user_id,
                'field70' => $item['name'], // Виды участия
            ];

            $toModel
                ->project_implementation_provides_informational_support()
                ->create($matrixData)
            ;
        }
    }

    public function saveMatrixLTOS($fromModel, $toModel)
    {
        // Список членов совета ТОС (ФИО, контактный телефон, электронная почта)
        foreach ($fromModel->tos_6 as $item) {

            $matrixData = [
                'group' => 'list_members_council_tos',
                'user_id' => $fromModel->user_id,
                'field19' => $item['fio'],      // ФИО
                'field20' => $item['phone'],    // Контактный телефон
                'field21' => $item['email'],    // Электронная почта
            ];

            $toModel
                ->list_members_council_tos()
                ->create($matrixData)
            ;
        }

        // Организация культурно-массовых мероприятий, праздников, иных культурно-просветительных акций (не более 1 страницы)
        foreach ($fromModel->best_1 as $item) {

            $matrixData = [
                'group' => 'organization_cultural_events',
                'user_id' => $fromModel->user_id,
                'field22' => $item['count'],        // Кол-во мероприятий
                'field23' => $item['description'],  // Описание проведенных мероприятий
                'field24' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->organization_cultural_events()
                ->create($matrixData)
            ;
        }

        // Проведение спортивных соревнований, гражданско-патриотических игр, туристических выездов (не более 1 страницы)
        foreach ($fromModel->best_2 as $item) {

            $matrixData = [
                'group' => 'conducting_sports_competitions',
                'user_id' => $fromModel->user_id,
                'field25' => $item['count'],        // Кол-во мероприятий
                'field26' => $item['description'],  // Описание проведенных мероприятий
                'field27' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->conducting_sports_competitions()
                ->create($matrixData)
            ;
        }

        // Проведение мероприятий, направленных на профилактику наркомании, алкоголизма и формирование здорового образа жизни (не более 1 страницы)
        foreach ($fromModel->best_3 as $item) {

            $matrixData = [
                'group' => 'conducting_sports_competitions',
                'user_id' => $fromModel->user_id,
                'field28' => $item['count'],        // Кол-во мероприятий
                'field29' => $item['description'],  // Описание проведенных мероприятий
                'field30' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->conducting_sports_competitions()
                ->create($matrixData)
            ;
        }

        // Наличие клубов, секций кружков, организованных при ТОС (не более 1 страницы)
        foreach ($fromModel->best_4 as $item) {

            $matrixData = [
                'group' => 'availability_clubs',
                'user_id' => $fromModel->user_id,
                'field31' => $item['description'],  // Описание клубов, секций, кружков, организованных при ТОС
                'field32' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->availability_clubs()
                ->create($matrixData)
            ;
        }

        // Проведение мероприятий по организации благоустройства и улучшения санитарного состояния территории ТОС (не более 1 страницы)
        foreach ($fromModel->best_5 as $item) {

            $matrixData = [
                'group' => 'measures_organization_landscaping',
                'user_id' => $fromModel->user_id,
                'field33' => $item['description'],  // Краткое описание проводимых мероприятий по организации благоустройства и улучшения санитарного состояния территории ТОС
                'field34' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->measures_organization_landscaping()
                ->create($matrixData)
            ;
        }

        // Количество объектов социальной направленности, восстановленных, отремонтированных или построенных силами ТОС (не более 1 страницы)
        foreach ($fromModel->best_6 as $item) {

            $matrixData = [
                'group' => 'number_objects_social_orientation',
                'user_id' => $fromModel->user_id,
                'field35' => $item['count'],        // Кол-во мероприятий
                'field36' => $item['description'],  // Описание проведенных мероприятий
                'field37' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->number_objects_social_orientation()
                ->create($matrixData)
            ;
        }

        // Оказание помощи многодетным семьям, инвалидам, одиноким пенсионерам, малоимущим гражданам (не более 1 страницы)
        foreach ($fromModel->best_7 as $item) {

            $matrixData = [
                'group' => 'providing_assistance',
                'user_id' => $fromModel->user_id,
                'field38' => $item['description'],  // Описание проведенных работ
                'field39' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->providing_assistance()
                ->create($matrixData)
            ;
        }

        // Создание на территории ТОС уголка здорового образа жизни, разработка буклетов, выпуск стенгазет по пропаганде здорового образа жизни (не более 1 страницы)
        foreach ($fromModel->best_8 as $item) {

            $matrixData = [
                'group' => 'healthy_lifestyle_corner',
                'user_id' => $fromModel->user_id,
                'field40' => $item['description'],  // Описание проведенных работ
                'field41' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->healthy_lifestyle_corner()
                ->create($matrixData)
            ;
        }

        // Участие членов ТОС в совместных с сотрудниками полиции профилактических мероприятиях, связанных с профилактикой преступлений и иных правонарушений (не более 1 страницы)
        foreach ($fromModel->best_9 as $item) {

            $matrixData = [
                'group' => 'joint_preventive_measures',
                'user_id' => $fromModel->user_id,
                'field42' => $item['count'],        // Кол-во мероприятий
                'field43' => $item['description'],  // Описание проведенных мероприятий
                'field44' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->joint_preventive_measures()
                ->create($matrixData)
            ;
        }

        // Проведение мероприятий по профилактике пожаров (не более 1 страницы)
        foreach ($fromModel->best_10 as $item) {

            $matrixData = [
                'group' => 'fire_prevention',
                'user_id' => $fromModel->user_id,
                'field45' => $item['count'],        // Кол-во мероприятий
                'field46' => $item['description'],  // Описание проведенных мероприятий
                'field47' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->fire_prevention()
                ->create($matrixData)
            ;
        }

        // Проведение ТОСами совещаний и семинаров с участием органов местного самоуправления (не более 0,5 страницы)
        foreach ($fromModel->best_11 as $item) {

            $matrixData = [
                'group' => 'meetings_and_seminars',
                'user_id' => $fromModel->user_id,
                'field48' => $item['count'],        // Кол-во совещаний с участием ОМСУ
                'field49' => $item['description'],  // Описание совещаний с участием органов местного самоуправления
                'field50' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->meetings_and_seminars()
                ->create($matrixData)
            ;
        }

        // Размещение информации в средствах массовой информации и в информационно-телекоммуникационной сети Интернет о деятельности ТОС по каждому направлению деятельности
        foreach ($fromModel->best_12 as $item) {

            $matrixData = [
                'group' => 'placement_information_in_mass_media',
                'user_id' => $fromModel->user_id,
                'field51' => $item['count'],        // Кол-во публикаций на сайте
                'field52' => $item['description'],  // Копии статей в печатных изданиях, активные гиперссылки на публикации
                'field53' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->placement_information_in_mass_media()
                ->create($matrixData)
            ;
        }

        // Участие ТОС в конкурсах за предыдущие три года (неудачное)
        foreach ($fromModel->best_13 as $item) {

            $matrixData = [
                'group' => 'participation_in_previous_contests_unsuccessful',
                'user_id' => $fromModel->user_id,
                'field54' => $item['name'],     // Наименование проектных заявок, не прошедших конкурсный отбор
                'field55' => $item['numbers'],  // Номера слайдов
            ];

            $toModel
                ->participation_in_previous_contests_unsuccessful()
                ->create($matrixData)
            ;
        }

        // Участие ТОС в конкурсах за предыдущие три года (удачное)
        foreach ($fromModel->best_14 as $item) {

            $matrixData = [
                'group' => 'participation_in_previous_contests_successful',
                'user_id' => $fromModel->user_id,
                'field56' => $item['name'],     // Наименование реализованных проектов
                'field57' => $item['numbers'],  // Номера слайдов
            ];

            $toModel
                ->participation_in_previous_contests_successful()
                ->create($matrixData)
            ;
        }

        // Награды ТОС и членов ТОС за тосовскую деятельность (за последние три года)
        foreach ($fromModel->best_15 as $item) {

            $matrixData = [
                'group' => 'awards',
                'user_id' => $fromModel->user_id,
                'field58' => $item['description'],  // Перечислить (год награждения, кем награжден, за что, кто награжден)
                'field59' => $item['numbers'],      // Номера слайдов
            ];

            $toModel
                ->awards()
                ->create($matrixData)
            ;
        }
    }
}
