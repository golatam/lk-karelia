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
        Schema::create('ls_applications', function (Blueprint $table) {

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
                ->unsignedBigInteger('contest_nomination')
                ->nullable()
                ->default(0)
                ->comment('Номинация конкурса')
            ;

            $table
                ->string('fio')
                ->nullable()
                ->comment('Фамилия, имя, отчество (полностью)')
            ;

            $table
                ->timestamp('date_birth')
                ->nullable()
                ->comment('Число, месяц, год рождения')
            ;

            $table
                ->string('phone')
                ->nullable()
                ->comment('Контактный телефон (желательно мобильный)')
            ;

            $table
                ->string('email')
                ->nullable()
                ->comment('Адрес электронной почты')
            ;

            $table
                ->longText('education')
                ->nullable()
                ->comment('Название, год окончания учебного заведения, специальность')
            ;

            $table
                ->unsignedBigInteger('total_work_experience')
                ->nullable()
                ->default(0)
                ->comment('Общий трудовой стаж (в месяцах)')
            ;

            $table
                ->string('place_work')
                ->nullable()
                ->comment('Место работы, занимаемая должность')
            ;

            $table
                ->string('organization_phone')
                ->nullable()
                ->comment('Телефон организации')
            ;

            $table
                ->string('organization_email')
                ->nullable()
                ->comment('Адрес электронной почты организации')
            ;

            $table
                ->string('working_hours_in_this_organization')
                ->nullable()
                ->comment('Время работы в данной организации')
            ;

            $table
                ->string('working_hours_in_this_position')
                ->nullable()
                ->comment('Время работы в данной должности')
            ;

            $table
                ->unsignedBigInteger('number_employees_division_total')
                ->nullable()
                ->default(0)
                ->comment('Количество штатных сотрудников подразделения – всего')
            ;

            $table
                ->unsignedBigInteger('number_employees_division_under_your_command')
                ->nullable()
                ->default(0)
                ->comment('Количество штатных сотрудников подразделения находящихся в Вашем подчинении')
            ;

            $table
                ->longText('job_responsibilities')
                ->nullable()
                ->comment('Должностные обязанности')
            ;

            $table
                ->longText('consulting')
                ->nullable()
                ->comment('Занятие консультационной деятельностью. Основные вопросы консультирования')
            ;

            $table
                ->longText('awards')
                ->nullable()
                ->comment('Наличие государственных и иных наград, премий, почетных званий')
            ;

            $table
                ->longText('participation_in_projects')
                ->nullable()
                ->comment('Участие в проектах по проблемам местного самоуправления (да, нет, перечислите)')
            ;

            $table
                ->longText('results_activity_in_current_year')
                ->nullable()
                ->comment('Результаты деятельности в текущем  году (приведите краткое описание)')
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
        Schema::dropIfExists('ls_applications');
    }
};
