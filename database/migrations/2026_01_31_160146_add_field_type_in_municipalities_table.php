<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('municipalities', function (Blueprint $table) {
            $table
                ->string('type')
                ->after('name')
                ->nullable()
                ->comment('Тип муниципального образования');
        });

        $municipalities = \App\Models\Municipality::all();
        foreach ($municipalities as $municipality) {
            if (preg_match('/муниципальный округ/', $municipality->name)) {
                $municipality->type = \App\Enums\MunicipalityTypeEnum::MO->value;
            } elseif (preg_match('/муниципальный район/', $municipality->name)) {
                $municipality->type = \App\Enums\MunicipalityTypeEnum::MR->value;
            } elseif (preg_match('/городское поселение/', $municipality->name)) {
                $municipality->type = \App\Enums\MunicipalityTypeEnum::GP->value;
            } elseif (preg_match('/сельское/', $municipality->name)) {
                $municipality->type = \App\Enums\MunicipalityTypeEnum::SP->value;
            } elseif (preg_match('/городской округ/', $municipality->name)) {
                $municipality->type = \App\Enums\MunicipalityTypeEnum::GO->value;
            }
            $municipality->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('municipalities', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
