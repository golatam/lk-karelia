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
        Schema::create('lptos_applications', function (Blueprint $table) {

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
                ->unsignedBigInteger('category')
                ->nullable()
                ->default(0)
                ->comment('Категория конкурса')
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
                ->string('nomenclature_number')
                ->nullable()
                ->comment('Номенклатурный номер ТОС')
            ;

            $table
                ->timestamp('date_registration_charter')
                ->nullable()
                ->comment('Дата регистрации устава ТОС уполномоченным органом местного самоуправления')
            ;

            $table
                ->unsignedBigInteger('population_size_in_tos')
                ->nullable()
                ->default(0)
                ->comment('Количество жителей, проживающих в границах ТОС')
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
                ->unsignedTinyInteger('is_tos_legal_entity')
                ->nullable()
                ->default(0)
                ->comment('Является ли ТОС юридическим лицом')
            ;

            $table
                ->timestamp('registration_date_tos')
                ->nullable()
                ->comment('Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК')
            ;

            $table
                ->string('ogrn')
                ->nullable()
                ->comment('ОГРН')
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
                ->text('bank_details')
                ->nullable()
                ->comment('Банковские реквизиты')
            ;

            $table
                ->string('website')
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
                ->string('practice_name')
                ->nullable()
                ->comment('Название практики')
            ;

            $table
                ->longText('practice_purpose')
                ->nullable()
                ->comment('Цель практики')
            ;

            $table
                ->longText('practice_tasks')
                ->nullable()
                ->comment('Задачи практики')
            ;

            $table
                ->timestamp('duration_practice')
                ->nullable()
                ->comment('Срок реализации практики (проекта)')
            ;

            $table
                ->longText('practice_implementation_geography')
                ->nullable()
                ->comment('География реализации практики')
            ;

            $table
                ->longText('activity_social_significance')
                ->nullable()
                ->comment('Социальная значимость деятельности ТОС')
            ;

            $table
                ->longText('problem_description')
                ->nullable()
                ->comment('Описание проблемы, на решение которой была направлена практика')
            ;

            $table
                ->unsignedBigInteger('number_people_part_in_project_implementation')
                ->nullable()
                ->default(0)
                ->comment('Количество человек, принявших участие в реализации проекта')
            ;

//            $table
//                ->json('list_documents_regulating_activity')
//                ->nullable()
//                ->comment('Перечень документов, регламентирующих деятельность в рамках реализации практики')
//            ;

            $table
                ->string('implementation_resources_involved_practice_own')
                ->nullable()
                ->comment('Собственные финансовые средства')
            ;

            $table
                ->string('implementation_resources_involved_practice_budget')
                ->nullable()
                ->comment('Привлеченные финансовые средства (из регионального или муниципального бюджетов)')
            ;

            $table
                ->longText('implementation_resources_involved_practice_other')
                ->nullable()
                ->comment('Организационные ресурса: (волонтерство, благотворительность, социальное партнерство, информационная поддержка проекта)')
            ;

            $table
                ->longText('achieved_results')
                ->nullable()
                ->comment('Укажите основные результаты, достигнутые при реализации практики (проекта)')
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
        Schema::dropIfExists('lptos_applications');
    }
};
