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
        Schema::table('matrix', function (Blueprint $table) {

            $table
                ->longText('field19')
                ->nullable()
                ->after('field18')
                ->comment('ФИО')
            ;

            $table
                ->longText('field20')
                ->nullable()
                ->after('field19')
                ->comment('Контактный телефон')
            ;

            $table
                ->longText('field21')
                ->nullable()
                ->after('field20')
                ->comment('Электронная почта')
            ;

            $table
                ->longText('field22')
                ->nullable()
                ->after('field21')
                ->comment('Кол-во мероприятий')
            ;

            $table
                ->longText('field23')
                ->nullable()
                ->after('field22')
                ->comment('Описание проведенных мероприятий')
            ;

            $table
                ->longText('field24')
                ->nullable()
                ->after('field23')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field25')
                ->nullable()
                ->after('field24')
                ->comment('Кол-во мероприятий')
            ;

            $table
                ->longText('field26')
                ->nullable()
                ->after('field25')
                ->comment('Описание проведенных мероприятий')
            ;

            $table
                ->longText('field27')
                ->nullable()
                ->after('field26')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field28')
                ->nullable()
                ->after('field27')
                ->comment('Кол-во мероприятий')
            ;

            $table
                ->longText('field29')
                ->nullable()
                ->after('field28')
                ->comment('Описание проведенных мероприятий')
            ;

            $table
                ->longText('field30')
                ->nullable()
                ->after('field29')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field31')
                ->nullable()
                ->after('field30')
                ->comment('Описание клубов, секций, кружков, организованных при ТОС')
            ;

            $table
                ->longText('field32')
                ->nullable()
                ->after('field31')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field33')
                ->nullable()
                ->after('field32')
                ->comment('Краткое описание проводимых мероприятий по организации благоустройства и улучшения санитарного состояния территории ТОС')
            ;

            $table
                ->longText('field34')
                ->nullable()
                ->after('field33')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field35')
                ->nullable()
                ->after('field34')
                ->comment('Кол-во мероприятий')
            ;

            $table
                ->longText('field36')
                ->nullable()
                ->after('field35')
                ->comment('Описание проведенных мероприятий')
            ;

            $table
                ->longText('field37')
                ->nullable()
                ->after('field36')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field38')
                ->nullable()
                ->after('field37')
                ->comment('Описание проведенных работ')
            ;

            $table
                ->longText('field39')
                ->nullable()
                ->after('field38')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field40')
                ->nullable()
                ->after('field39')
                ->comment('Описание проведенных работ')
            ;

            $table
                ->longText('field41')
                ->nullable()
                ->after('field40')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field42')
                ->nullable()
                ->after('field41')
                ->comment('Кол-во мероприятий')
            ;

            $table
                ->longText('field43')
                ->nullable()
                ->after('field42')
                ->comment('Описание проведенных мероприятий')
            ;

            $table
                ->longText('field44')
                ->nullable()
                ->after('field43')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field45')
                ->nullable()
                ->after('field44')
                ->comment('Кол-во мероприятий')
            ;

            $table
                ->longText('field46')
                ->nullable()
                ->after('field45')
                ->comment('Описание проведенных мероприятий')
            ;

            $table
                ->longText('field47')
                ->nullable()
                ->after('field46')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field48')
                ->nullable()
                ->after('field47')
                ->comment('Кол-во совещаний с участием ОМСУ')
            ;

            $table
                ->longText('field49')
                ->nullable()
                ->after('field48')
                ->comment('Описание совещаний с участием органов местного самоуправления')
            ;

            $table
                ->longText('field50')
                ->nullable()
                ->after('field49')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field51')
                ->nullable()
                ->after('field50')
                ->comment('Кол-во публикаций на сайте')
            ;

            $table
                ->longText('field52')
                ->nullable()
                ->after('field51')
                ->comment('Копии статей в печатных изданиях, активные гиперссылки на публикации')
            ;

            $table
                ->longText('field53')
                ->nullable()
                ->after('field52')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field54')
                ->nullable()
                ->after('field53')
                ->comment('Наименование проектных заявок, не прошедших конкурсный отбор')
            ;

            $table
                ->longText('field55')
                ->nullable()
                ->after('field54')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field56')
                ->nullable()
                ->after('field55')
                ->comment('Наименование реализованных проектов')
            ;

            $table
                ->longText('field57')
                ->nullable()
                ->after('field56')
                ->comment('Номера слайдов')
            ;

            $table
                ->longText('field58')
                ->nullable()
                ->after('field57')
                ->comment('Перечислить (год награждения, кем награжден, за что, кто награжден)')
            ;

            $table
                ->longText('field59')
                ->nullable()
                ->after('field58')
                ->comment('Номера слайдов')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matrix', function (Blueprint $table) {

            $table
                ->dropColumn([
                    'field19',
                    'field20',
                    'field21',
                    'field22',
                    'field23',
                    'field24',
                    'field25',
                    'field26',
                    'field27',
                    'field28',
                    'field29',
                    'field30',
                    'field31',
                    'field32',
                    'field33',
                    'field34',
                    'field35',
                    'field36',
                    'field37',
                    'field38',
                    'field39',
                    'field40',
                    'field41',
                    'field42',
                    'field43',
                    'field44',
                    'field45',
                    'field46',
                    'field47',
                    'field48',
                    'field49',
                    'field50',
                    'field51',
                    'field52',
                    'field53',
                    'field54',
                    'field55',
                    'field56',
                    'field57',
                    'field58',
                    'field59',
                ])
            ;
        });
    }
};
