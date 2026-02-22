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
        Schema::table('szptos_applications', function (Blueprint $table) {
            $table
                ->unsignedTinyInteger('is_admitted_to_competition')
                ->after('status')
                ->nullable()
                ->default(0)
                ->comment('Допущен к участию в конкурсе');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('szptos_applications', function (Blueprint $table) {
            $table->dropColumn('is_admitted_to_competition');
        });
    }
};
