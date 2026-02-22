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
        Schema::table('municipalities', function (Blueprint $table) {

            $table
                ->tinyInteger('is_district_plus_gp')
                ->after('name')
                ->nullable()
                ->default(0)
                ->comment('Муниципальный район + Администрация городского поселения')
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
        Schema::table('municipalities', function (Blueprint $table) {

            $table
                ->dropColumn([
                    'is_district_plus_gp',
                ])
            ;
        });
    }
};
