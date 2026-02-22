<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_score_columns', static function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->integer('significance_factor');
            $table->integer('max_rating');
            $table->string('application_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_score_columns');
    }
};
