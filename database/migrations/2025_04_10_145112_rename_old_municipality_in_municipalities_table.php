<?php

use App\Models\Municipality;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public array $municipalityNames = [
        [
            'old' => 'Сортавальский муниципальный район',
            'new' => 'Сортавальский район (Устаревшее 2015-2024)',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        foreach ($this->municipalityNames as $municipalityName) {
            $municipality = Municipality::query()->firstOrNew(['name' => $municipalityName['old']]);
            if ($municipality->exists) {
                $municipality->fill(['name' => $municipalityName['new']])->save();
            }
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
            $municipality = Municipality::query()->firstOrNew(['name' => $municipalityName['new']]);
            if ($municipality->exists) {
                $municipality->fill(['name' => $municipalityName['old']])->save();
            }
        }
    }
};
