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
        Schema::create('most_beautiful_villages', function (Blueprint $table) {

            $table->id();

            $table
                ->foreignId('user_id')
                ->comment("Пользователь")
                ->constrained()
            ;

            $table
                ->foreignId('contest_id')
                ->comment("Конкурс")
                ->constrained()
            ;

            $table
                ->unsignedBigInteger('settlement_id')
                ->nullable()
                ->default(0)
                ->comment('Наименование населенного пункта')
            ;

            $table
                ->string('applicant_fio')
                ->nullable()
                ->comment('Фамилия, имя, отчество заявителя')
            ;

            $table
                ->string('applicant_position')
                ->nullable()
                ->comment('Должность заявителя')
            ;

            $table
                ->string('contact_details')
                ->nullable()
                ->comment('Контактные данные')
            ;

            $table
                ->unsignedBigInteger('population_size_in_settlement')
                ->nullable()
                ->default(0)
                ->comment('Количество жителей, проживающих в населенном пункте')
            ;

            $table
                ->text('demographic_parameters')
                ->nullable()
                ->comment('Демографические показатели')
            ;

            $table
                ->text('forms_self_organization_citizens')
                ->nullable()
                ->comment('Формы самоорганизации граждан, распространенные на территории села (поселка, деревни)')
            ;

            $table
                ->text('landscaping')
                ->nullable()
                ->comment('Положительный опыт села (поселка, деревни) в области благоустройства, озеленения и поддержания чистоты и порядка')
            ;

            $table
                ->text('cultural_traditions')
                ->nullable()
                ->comment('Культурные традиций и обычаи села (поселка, деревни)')
            ;

            $table
                ->decimal('total_application_points', $precision = 20, $scale = 2)
                ->nullable()
                ->default(0.00)
                ->comment("Общие баллы по заявке")
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
        Schema::dropIfExists('most_beautiful_villages');
    }
};
