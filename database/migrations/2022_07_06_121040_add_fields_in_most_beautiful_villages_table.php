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
        Schema::table('most_beautiful_villages', function (Blueprint $table) {

            $table
                ->longText('history_village_description')
                ->after('cultural_traditions')
                ->nullable()
                ->comment('Описание истории (легенд) села (поселка, деревни)')
            ;

            $table
                ->longText('natural_monuments_description')
                ->after('history_village_description')
                ->nullable()
                ->comment('Описание памятников природы села (поселка, деревни)')
            ;

            $table
                ->longText('architectural_monuments_description')
                ->after('natural_monuments_description')
                ->nullable()
                ->comment('Описание памятников архитектуры')
            ;

            $table
                ->longText('degree_population_participation_description')
                ->after('architectural_monuments_description')
                ->nullable()
                ->comment('Описание степени участия населения')
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
        Schema::table('most_beautiful_villages', function (Blueprint $table) {

            $table
                ->dropColumn([
                    'history_village_description',
                    'natural_monuments_description',
                    'architectural_monuments_description',
                    'degree_population_participation_description',
                ])
            ;
        });
    }
};
