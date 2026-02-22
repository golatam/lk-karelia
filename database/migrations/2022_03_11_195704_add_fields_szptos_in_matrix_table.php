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
                ->longText('field60')
                ->nullable()
                ->after('field59')
                ->comment('ФИО')
            ;

            $table
                ->longText('field61')
                ->nullable()
                ->after('field60')
                ->comment('Контактный телефон')
            ;

            $table
                ->longText('field62')
                ->nullable()
                ->after('field61')
                ->comment('Электронная почта')
            ;

            $table
                ->longText('field63')
                ->nullable()
                ->after('field62')
                ->comment('Основные этапы проекта и мероприятия')
            ;

            $table
                ->longText('field64')
                ->nullable()
                ->after('field63')
                ->comment('Срок реализации')
            ;

            $table
                ->longText('field65')
                ->nullable()
                ->after('field64')
                ->comment('Ответственные исполнители')
            ;

            $table
                ->longText('field66')
                ->nullable()
                ->after('field65')
                ->comment('Название файла')
            ;

            $table
                ->longText('field67')
                ->nullable()
                ->after('field66')
                ->comment('Ссылки на подтверждени')
            ;

            $table
                ->longText('field68')
                ->nullable()
                ->after('field67')
                ->comment('Виды участия')
            ;

            $table
                ->longText('field69')
                ->nullable()
                ->after('field68')
                ->comment('Виды участия')
            ;

            $table
                ->longText('field70')
                ->nullable()
                ->after('field69')
                ->comment('Виды участия')
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
                    'field60',
                    'field61',
                    'field62',
                    'field63',
                    'field64',
                    'field65',
                    'field66',
                    'field67',
                    'field68',
                    'field69',
                    'field70',
                ])
            ;
        });
    }
};
