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
        Schema::create('contests', function (Blueprint $table) {

            $table->id();

            $table
                ->string('type')
                ->comment('Тип конкурса');

            $table
                ->string('name')
                ->comment('Название конкурса');

            $table
                ->text('description')
                ->nullable()
                ->comment('Описание конкурса');

            $table
                ->timestamp('end_date_active')
                ->useCurrent()
                ->comment('Конечная дата активности');

            $table
                ->tinyInteger('is_active')
                ->default(0)
                ->comment('Статус');

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
        Schema::dropIfExists('contests');
    }
};
