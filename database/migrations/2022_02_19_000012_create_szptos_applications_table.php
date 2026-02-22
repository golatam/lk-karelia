<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('szptos_applications', function (Blueprint $table) {

            $table->id();

            $table
                ->foreignId('user_id')
                ->comment("ID пользователя")
                ->constrained()
            ;

            $table
                ->foreignId('contest_id')
                ->comment("ID конкурса")
                ->constrained()
            ;

            $table
                ->foreignId('municipality_id')
                ->comment("ID муниципалитета")
                ->constrained()
            ;

            $table
                ->unsignedBigInteger('register_id')
                ->nullable()
                ->default(0)
                ->comment('Полное наименование ТОС')
            ;

            $table
                ->unsignedBigInteger('region_id')
                ->nullable()
                ->default(0)
                ->comment('Наименование (муниципального района/городского округа), где реализуется проект')
            ;

            $table
                ->unsignedBigInteger('settlement_id')
                ->nullable()
                ->default(0)
                ->comment('Наименование поселения в составе района, где реализуется проект')
            ;

            $table
                ->timestamp('date_registration_charter')
                ->nullable()
                ->comment('Дата регистрации устава ТОС уполномоченным органом местного самоуправления')
            ;

            $table
                ->unsignedTinyInteger('is_tos_legal_entity')
                ->nullable()
                ->default(0)
                ->comment('Является ли ТОС юридическим лицом')
            ;

            $table
                ->string('nomenclature_number')
                ->nullable()
                ->comment('Номенклатурный номер ТОС')
            ;

            $table
                ->string('full_name_chairman_tos')
                ->nullable()
                ->comment('ФИО председателя ТОС')
            ;

            $table
                ->string('tos_address')
                ->nullable()
                ->comment('Почтовый адрес (с указанием индекса)')
            ;

            $table
                ->string('tos_phone')
                ->nullable()
                ->comment('Номер мобильного телефона')
            ;

            $table
                ->string('tos_email')
                ->nullable()
                ->comment('Адрес электронной почты')
            ;

            $table
                ->unsignedBigInteger('population_size_settlement')
                ->nullable()
                ->default(0)
                ->comment('Численность населения')
            ;

            $table
                ->unsignedBigInteger('population_size_in_tos')
                ->nullable()
                ->default(0)
                ->comment('Количество жителей, проживающих в границах ТОС')
            ;

            $table
                ->string('project_name')
                ->nullable()
                ->comment('Наименование проекта')
            ;

            $table
                ->unsignedBigInteger('project_direction')
                ->nullable()
                ->default(0)
                ->comment('Направление проекта')
            ;

            $table
                ->longText('problem_description')
                ->nullable()
                ->comment('Описание актуальности проблемы, на решение которой направлен проект')
            ;

            $table
                ->longText('project_purpose')
                ->nullable()
                ->comment('Цель проекта')
            ;

            $table
                ->longText('project_tasks')
                ->nullable()
                ->comment('Задачи проекта')
            ;

            $table
                ->timestamp('duration_practice_start')
                ->nullable()
                ->comment('Дата начала реализации проекта')
            ;

            $table
                ->timestamp('duration_practice_end')
                ->nullable()
                ->comment('Дата окончания реализации проекта')
            ;

            $table
                ->longText('results_project_implementation')
                ->nullable()
                ->comment('Ожидаемые результаты реализации проекта')
            ;

            $table
                ->unsignedBigInteger('number_beneficiaries')
                ->nullable()
                ->default(0)
                ->comment('Количество человек (благополучателей), которые будут пользоваться результатами проекта')
            ;

            $table
                ->longText('description_need')
                ->nullable()
                ->comment('Описание необходимости и возможностей дальнейшего развития проекта после окончания его реализации')
            ;

            $table
                ->decimal('total_cost_project', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Общая стоимость проекта")
            ;

            $table
                ->decimal('budget_funds_republic', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Средства бюджета Республики Карелия")
            ;

            $table
                ->decimal('funds_raised', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Привлеченные средства")
            ;

            $table
                ->decimal('extra_budgetary_sources', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Внебюджетные источники")
            ;

            $table
                ->decimal('funds_tos', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Средства ТОС")
            ;

            $table
                ->decimal('funds_legal_entities', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Средства юридических лиц")
            ;

            $table
                ->decimal('funds_local_budget', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Средства местного бюджета")
            ;

            $table
                ->longText('person_responsible_implementation_project')
                ->nullable()
                ->comment('Лицо, ответственное за реализацию проекта (фамилия, имя, отчество, контактный телефон, электронная почта)')
            ;

            $table
                ->unsignedBigInteger('number_present_at_general_meeting')
                ->nullable()
                ->default(0)
                ->comment('Количество присутствующих на общем собрании членов ТОС')
            ;

            $table
                ->unsignedTinyInteger('is_grand_opening_with_media_coverage')
                ->nullable()
                ->default(0)
                ->comment('По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ»')
            ;

            $table
                ->decimal('total_application_points', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Общие баллы по заявке")
            ;

            $table
                ->decimal('points_from_administrator', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Баллы от администратора")
            ;

            $table
                ->longText('comment_on_points_from_administrator')
                ->nullable()
                ->comment("Комментарий к баллам от администратора")
            ;

            $table
                ->string('status')
                ->nullable()
                ->comment('Статус заявки')
            ;

            $table
                ->timestamp('date_filling_in')
                ->nullable()
                ->comment('Дата заполнения')
            ;

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('szptos_applications');
    }
};
