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
        Schema::table('contests', function (Blueprint $table) {

            $table
                ->dateTime('year_of_competition')
                ->after('name')
                ->useCurrent()
                ->comment('Год проведения конкурса')
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
        Schema::table('contests', function (Blueprint $table) {

            $table
                ->dropColumn([
                    'year_of_competition',
                ])
            ;
        });
    }
};
