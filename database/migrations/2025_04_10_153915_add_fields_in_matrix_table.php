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
    public function up(): void
    {
        Schema::table('matrix', function (Blueprint $table) {
            $table
                ->longText('field73')
                ->after('field72')
                ->nullable()
                ->comment('Основные этапы проекта и мероприятия');
            $table
                ->longText('field74')
                ->after('field73')
                ->nullable()
                ->comment('Срок реализации');
            $table
                ->longText('field75')
                ->after('field74')
                ->nullable()
                ->comment('Ответственные исполнители');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('matrix', function (Blueprint $table) {
            $table->dropColumn(['field73', 'field74', 'field75']);
        });
    }
};
