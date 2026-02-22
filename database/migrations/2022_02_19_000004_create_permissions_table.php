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
        Schema::create('permissions', function (Blueprint $table) {

            $table->id();

            $table
                ->string('type')
                ->comment('Тип разрешения');

            $table
                ->string('group')
                ->comment('Группа разрешения');

            $table
                ->string('action')
                ->comment('Действие разрешения');

            $table
                ->string('name')
                ->nullable()
                ->comment('Наименование разрешения');

            $table
                ->string('description')
                ->nullable()
                ->comment('Описание разрешения');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
