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
        Schema::create('ltos_applications', function (Blueprint $table) {

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
                ->string('nomenclature_number')
                ->nullable()
                ->comment('Номенклатурный номер ТОС')
            ;

            $table
                ->unsignedTinyInteger('is_tos_legal_entity')
                ->nullable()
                ->default(0)
                ->comment('Является ли ТОС юридическим лицом')
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
                ->unsignedBigInteger('population_size_in_tos')
                ->nullable()
                ->default(0)
                ->comment('Количество зарегистрированных граждан в ТОС')
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
        Schema::dropIfExists('ltos_applications');
    }
};
