<?php

use App\Models\Municipality;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public array $municipalityNames = [
        'Сортавальский муниципальный округ'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        foreach ($this->municipalityNames as $municipalityName) {
            $municipality = Municipality::query()->firstOrNew(['name' => $municipalityName]);
            $municipality->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        foreach ($this->municipalityNames as $municipalityName) {
            $municipality = Municipality::query()->firstOrNew(['name' => $municipalityName]);
            if ($municipality->exists) {
                $municipality->delete();
            }
        }
    }
};
