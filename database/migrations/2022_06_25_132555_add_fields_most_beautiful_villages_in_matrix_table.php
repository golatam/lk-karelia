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
                ->longText('field71')
                ->nullable()
                ->after('field70')
                ->comment('Адрес')
            ;

            $table
                ->longText('field72')
                ->nullable()
                ->after('field71')
                ->comment('Перечень проводимых населением мероприятий')
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
                    'field71',
                    'field72',
                ])
            ;
        });
    }
};
