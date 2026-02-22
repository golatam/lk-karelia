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
        Schema::table('lptos_applications', function (Blueprint $table) {

            $table
                ->unsignedBigInteger('number_beneficiaries')
                ->nullable()
                ->default(0)
                ->after('population_size_in_tos')
                ->comment('Количество человек (благополучателей), которые будут пользоваться результатами практики (проекта)')
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
        Schema::table('lptos_applications', function (Blueprint $table) {

            $table->dropColumn(['number_beneficiaries']);
        });
    }
};
