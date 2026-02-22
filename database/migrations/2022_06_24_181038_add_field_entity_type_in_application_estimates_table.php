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
        Schema::table('application_estimates', function (Blueprint $table) {

            $table
                ->string('entity_type')
                ->nullable()
                ->after('l_t_o_s_application_id')
                ->comment('Morph Class модели смежной таблицы')
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
        Schema::table('application_estimates', function (Blueprint $table) {

            $table
                ->dropColumn([
                    'entity_type',
                ])
            ;
        });
    }
};
