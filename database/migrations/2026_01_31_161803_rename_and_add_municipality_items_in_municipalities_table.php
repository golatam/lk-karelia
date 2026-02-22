<?php

use App\Models\Municipality;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $municipality1Old = Municipality::where('name', 'Кемский муниципальный район')->first();
        if ($municipality1Old) {
            $municipality1Old->name = 'Кемский муниципальный район (Устаревшее до 2025)';
            $municipality1Old->save();
            $municipality1 = Municipality::create(['name' => 'Кемский муниципальный округ', 'type' => \App\Enums\MunicipalityTypeEnum::MO->value]);
            DB::table('municipalities')
                ->where('parent_id', $municipality1Old->id)
                ->update(['parent_id' => $municipality1->id]);
        }

        $municipality2Old = Municipality::where('name', 'Лахденпохский муниципальный район')->first();
        if ($municipality2Old) {
            $municipality2Old->name = 'Лахденпохский муниципальный район (Устаревшее до 2025)';
            $municipality2Old->save();
            $municipality2 = Municipality::create(['name' => 'Лахденпохский муниципальный округ', 'type' => \App\Enums\MunicipalityTypeEnum::MO->value]);
            DB::table('municipalities')
                ->where('parent_id', $municipality2Old->id)
                ->update(['parent_id' => $municipality2->id]);
        }

        $municipality3Old = Municipality::where('name', 'Медвежьегорский муниципальный район')->first();
        if ($municipality3Old) {
            $municipality3Old->name = 'Медвежьегорский муниципальный район (Устаревшее до 2025)';
            $municipality3Old->save();
            $municipality3 = Municipality::create(['name' => 'Медвежьегорский муниципальный округ', 'type' => \App\Enums\MunicipalityTypeEnum::MO->value]);
            DB::table('municipalities')
                ->where('parent_id', $municipality3Old->id)
                ->update(['parent_id' => $municipality3->id]);
        }

        $municipality4Old = Municipality::where('name', 'Муезерский муниципальный район')->first();
        if ($municipality4Old) {
            $municipality4Old->name = 'Муезерский муниципальный район (Устаревшее до 2025)';
            $municipality4Old->save();
            $municipality4 = Municipality::create(['name' => 'Муезерский муниципальный округ', 'type' => \App\Enums\MunicipalityTypeEnum::MO->value]);
            DB::table('municipalities')
                ->where('parent_id', $municipality4Old->id)
                ->update(['parent_id' => $municipality4->id]);
        }

        $municipality5Old = Municipality::where('name', 'Костомукшский городской округ')->whereNotNull('parent_id')->first();
        if ($municipality5Old) {
            $municipality5 = Municipality::create(['name' => 'Костомукшский муниципальный округ', 'type' => \App\Enums\MunicipalityTypeEnum::MO->value]);

            DB::table('municipalities')
                ->where('parent_id', $municipality5Old->id)
                ->update(['parent_id' => $municipality5->id]);

            DB::table('municipalities')
                ->where('name', '=', 'Костомукшский городской округ')
                ->update(['name' => 'Костомукшский городской округ (Устаревшее до 2025)']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $municipality1 = Municipality::where('name', 'Кемский муниципальный округ')->first();
        $municipality1Old = Municipality::where('name', 'Кемский муниципальный район (Устаревшее до 2025)')->first();
        if ($municipality1 && $municipality1Old) {
            DB::table('municipalities')
                ->where('parent_id', $municipality1->id)
                ->update(['parent_id' => $municipality1Old->id]);

            $municipality1Old->name = 'Кемский муниципальный район';
            $municipality1Old->save();

            $municipality1->delete();
        }

        $municipality2 = Municipality::where('name', 'Лахденпохский муниципальный округ')->first();
        $municipality2Old = Municipality::where('name', 'Лахденпохский муниципальный район (Устаревшее до 2025)')->first();
        if ($municipality2 && $municipality2Old) {
            DB::table('municipalities')
                ->where('parent_id', $municipality2->id)
                ->update(['parent_id' => $municipality2Old->id]);

            $municipality2Old->name = 'Лахденпохский муниципальный район';
            $municipality2Old->save();

            $municipality2->delete();
        }

        $municipality3 = Municipality::where('name', 'Медвежьегорский муниципальный округ')->first();
        $municipality3Old = Municipality::where('name', 'Медвежьегорский муниципальный район (Устаревшее до 2025)')->first();
        if ($municipality3 && $municipality3Old) {
            DB::table('municipalities')
                ->where('parent_id', $municipality3->id)
                ->update(['parent_id' => $municipality3Old->id]);

            $municipality3Old->name = 'Медвежьегорский муниципальный район';
            $municipality3Old->save();

            $municipality3->delete();
        }

        $municipality4 = Municipality::where('name', 'Муезерский муниципальный округ')->first();
        $municipality4Old = Municipality::where('name', 'Муезерский муниципальный район (Устаревшее до 2025)')->first();
        if ($municipality4 && $municipality4Old) {
            DB::table('municipalities')
                ->where('parent_id', $municipality4->id)
                ->update(['parent_id' => $municipality4Old->id]);

            $municipality4Old->name = 'Муезерский муниципальный район';
            $municipality4Old->save();

            $municipality4->delete();
        }

        $municipality5 = Municipality::where('name', 'Костомукшский муниципальный округ')->first();
        $municipality5Old = Municipality::where('name', 'Костомукшский городской округ (Устаревшее до 2025)')->whereNotNull('parent_id')->first();
        if ($municipality5 && $municipality5Old) {
            DB::table('municipalities')
                ->where('parent_id', $municipality5->id)
                ->update(['parent_id' => $municipality5Old->id]);

            DB::table('municipalities')
                ->where('name', '=', 'Костомукшский городской округ (Устаревшее до 2025)')
                ->update(['name' => 'Костомукшский городской округ']);

            $municipality5->delete();
        }
    }
};
