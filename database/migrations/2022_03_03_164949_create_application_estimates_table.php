<?php

use App\Models\ApplicationScoreColumn;
use App\Models\LTOSApplication;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationEstimatesTable extends Migration
{
    public function up(): void
    {
        Schema::create('application_estimates', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LTOSApplication::class);
            $table->foreignIdFor(ApplicationScoreColumn::class);
            $table->foreignIdFor(User::class);
            $table->decimal('value', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_estimates');
    }
}
