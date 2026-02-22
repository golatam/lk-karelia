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
        Schema::create('ppmi_applications', function (Blueprint $table) {

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
                ->longText('project_name')
                ->nullable()
                ->comment("Наименование проекта")
            ;

            $table
                ->integer('population_size_settlement')
                ->nullable()
                ->default(0)
                ->comment("Численность населенного пункта")
            ;

            $table
                ->unsignedBigInteger('project_typology')
                ->nullable()
                ->default(0)
                ->comment("Типология проекта")
            ;

            $table
                ->longText('description_problem')
                ->nullable()
                ->comment("Описание проблемы")
            ;

            $table
                ->decimal('cost_repair_work', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Стоимость ремонтных работ")
            ;

            $table
                ->longText('comment_on_cost_repairs')
                ->nullable()
                ->comment("Комментарий к стоимости ремонтных работ")
            ;

            $table
                ->decimal('cost_purchasing_materials', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Стоимость приобретения материалов")
            ;

            $table
                ->longText('comment_on_cost_purchasing_materials')
                ->nullable()
                ->comment("Комментарий к стоимости приобретения материалов")
            ;

            $table
                ->decimal('cost_purchasing_equipment', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Стоимость приобретения оборудования")
            ;

            $table
                ->longText('comment_on_cost_purchasing_equipment')
                ->nullable()
                ->comment("Комментарий к стоимости приобретения оборудования")
            ;

            $table
                ->decimal('cost_construction_control', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Стоимость строительного контроля")
            ;

            $table
                ->longText('comment_on_cost_construction_control')
                ->nullable()
                ->comment("Комментарий к стоимости строительного контроля")
            ;

            $table
                ->decimal('cost_other_expenses', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Стоимость прочих расходов")
            ;

            $table
                ->longText('comment_on_cost_other_expenses')
                ->nullable()
                ->comment("Комментарий к стоимость прочих расходов")
            ;

            $table
                ->longText('expected_results')
                ->nullable()
                ->comment("Ожидаемые результаты")
            ;

            $table
                ->decimal('funds_municipal', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Средства муниципального образования")
            ;

            $table
                ->decimal('funds_individuals', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Безвозмездно от физ. лиц")
            ;

            $table
                ->decimal('funds_legal_entities', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Безвозмездно от юр. лиц")
            ;

            $table
                ->decimal('funds_republic', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Средства республики")
            ;

            $table
                ->longText('population_that_benefit_from_results_project')
                ->nullable()
                ->comment("Население, которое будет регулярно пользоваться результатами от реализации проекта")
            ;

            $table
                ->integer('population_size')
                ->nullable()
                ->default(0)
                ->comment("Кол-во человек населения")
            ;

            $table
                ->integer('population_size_in_congregation')
                ->nullable()
                ->default(0)
                ->comment("Кол-во лиц в собрании")
            ;

            $table
                ->longText('population_in_project_implementation')
                ->nullable()
                ->comment("Участие населения в реализации проекта")
            ;

            $table
                ->longText('population_in_project_provision')
                ->nullable()
                ->comment("Участие населения в обеспечении проекта")
            ;

            $table
                ->timestamp('implementation_date')
                ->nullable()
                ->comment("Срок реализации")
            ;

            $table
                ->longText('comment')
                ->nullable()
                ->comment("Комментарий")
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
                ->unsignedTinyInteger('is_media_participation')
                ->nullable()
                ->default(0)
                ->comment("Участие СМИ")
            ;

            $table
                ->unsignedTinyInteger('is_unpaid_work_of_population')
                ->nullable()
                ->default(0)
                ->comment("Неоплачиваемый труд населения")
            ;

            $table
                ->string('status')
                ->nullable()
                ->comment('Статус заявки')
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
        Schema::dropIfExists('ppmi_applications');
    }
};
