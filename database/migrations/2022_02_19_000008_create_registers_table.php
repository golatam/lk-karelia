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
        Schema::create('registers', function (Blueprint $table) {

            $table->id();

            $table
                ->unsignedBigInteger('name_region')
                ->nullable()
                ->default(0)
                ->comment('Наименование (муниципального района/городского округа)')
            ;

            $table
                ->unsignedBigInteger('name_settlement')
                ->nullable()
                ->default(0)
                ->comment('Наименование поселения в составе района')
            ;

            $table
                ->text('name_according_charter')
                ->nullable()
                ->comment('Наименование (согласно уставу)')
            ;

            $table
                ->unsignedTinyInteger('is_legal_entity')
                ->nullable()
                ->default(0)
                ->comment('Является ли ТОС юридическим лицом (да/нет)')
            ;

            $table
                ->text('address')
                ->nullable()
                ->comment('Адрес местонахождения ТОС (для юридических лиц - юридический адрес)')
            ;

            $table
                ->string('inn')
                ->nullable()
                ->comment('ИНН')
            ;

            $table
                ->string('kpp')
                ->nullable()
                ->comment('КПП')
            ;

            $table
                ->string('ogrn')
                ->nullable()
                ->comment('ОГРН')
            ;

            $table
                ->text('bank_details')
                ->nullable()
                ->comment('Банковские реквизиты')
            ;

            $table
                ->string('site')
                ->nullable()
                ->comment('Официальный сайт')
            ;

            $table
                ->string('vk')
                ->nullable()
                ->comment('Официальная группа в социальной сети ВКОНТАКТЕ')
            ;

            $table
                ->string('ok')
                ->nullable()
                ->comment('Официальная группа в социальной сети ОДНОКЛАССНИКИ')
            ;

            $table
                ->string('fb')
                ->nullable()
                ->comment('Официальная группа в социальной сети FACEBOOK')
            ;

            $table
                ->string('twitter')
                ->nullable()
                ->comment('Официальная группа в социальной сети TWITTER')
            ;

            $table
                ->string('instagram')
                ->nullable()
                ->comment('Официальная группа в социальной сети INSTAGRAM')
            ;

            $table
                ->text('boundaries')
                ->nullable()
                ->comment('Границы ТОС')
            ;

            $table
                ->text('legal_act')
                ->nullable()
                ->comment('Муниципальный правовой акт об утверждении устава ТОС (вид документа, дата, номер)')
            ;

            $table
                ->timestamp('registration_date_charter')
                ->useCurrent()
                ->comment('Дата учреждения ТОС (дата регистрации устава ТОС в органе местного самоуправления муниципального образования)')
            ;

            $table
                ->timestamp('registration_date_tos')
                ->useCurrent()
                ->comment('Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК')
            ;

            $table
                ->string('nomenclature_number')
                ->nullable()
                ->comment('Номенклатурный номер ТОС')
            ;

            $table
                ->unsignedBigInteger('number_members')
                ->nullable()
                ->default(0)
                ->comment('Кол-во членов ТОС')
            ;

            $table
                ->unsignedBigInteger('number_citizens')
                ->nullable()
                ->default(0)
                ->comment('Кол-во граждан, проживающих в границах ТОС')
            ;

            $table
                ->string('fio_chief')
                ->nullable()
                ->comment('ФИО руководителя ТОС')
            ;

            $table
                ->string('email_chief')
                ->nullable()
                ->comment('Электронный адрес руководителя ТОС')
            ;

            $table
                ->string('phone_chief')
                ->nullable()
                ->comment('Мобильный телефон руководителя ТОС')
            ;

            $table
                ->text('note')
                ->nullable()
                ->comment('Примечание')
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
        Schema::dropIfExists('registers');
    }
};
