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
                ->renameColumn('l_t_o_s_application_id', 'entity_id')
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
                ->renameColumn('entity_id', 'l_t_o_s_application_id')
            ;
        });
    }
};
