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
        Schema::create('images', function (Blueprint $table) {

            $table->id();
            $table->morphs('entity');

            $table
                ->string('path')
                ->comment('Путь файла')
            ;

            $table
                ->text('description')
                ->nullable()
                ->comment('Описание')
            ;

            $table
                ->string('group')
                ->nullable()
                ->comment('Группа фото')
            ;

            $table
                ->integer('position')
                ->nullable()
                ->default(0)
                ->comment('Прядок показа')
            ;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
