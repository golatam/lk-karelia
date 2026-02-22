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
                ->longText('appearance_village_description')
                ->after('degree_population_participation_description')
                ->nullable()
                ->comment('Описание внешнего облика села (поселка, деревни)')
            ;

            $table
                ->longText('reservoirs_description')
                ->after('appearance_village_description')
                ->nullable()
                ->comment('Описание водоемов (родников, колодцев)')
            ;

            $table
                ->longText('illumination_description')
                ->after('reservoirs_description')
                ->nullable()
                ->comment('Описание освещенности улиц и площадей')
            ;

            $table
                ->longText('common_areas_and_recreation_description')
                ->after('illumination_description')
                ->nullable()
                ->comment('Описание мест общего пользования и отдыха, парки, скамейки, беседки, спортивные и детские площадки')
            ;

            $table
                ->longText('artistic_expressiveness_description')
                ->after('common_areas_and_recreation_description')
                ->nullable()
                ->comment('Описание художественной выразительности и национального своеобразия жилой застройки')
            ;

            $table
                ->longText('condition_burial_sites_description')
                ->after('artistic_expressiveness_description')
                ->nullable()
                ->comment('Описание состояниея мест захоронений (кладбищ) села (поселка, деревни)')
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
                    'appearance_village_description',               // Описание внешнего облика села (поселка, деревни)
                    'reservoirs_description',                       // Описание водоемов (родников, колодцев)
                    'illumination_description',                     // Описание освещенности улиц и площадей
                    'common_areas_and_recreation_description',      // Описание мест общего пользования и отдыха, парки, скамейки, беседки, спортивные и детские площадки
                    'artistic_expressiveness_description',          // Описание художественной выразительности и национального своеобразия жилой застройки
                    'condition_burial_sites_description',           // Описание состояниея мест захоронений (кладбищ) села (поселка, деревни)
                ])
            ;
        });
    }
};
