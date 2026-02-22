<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `registers` MODIFY COLUMN `registration_date_tos` timestamp NULL DEFAULT NULL COMMENT 'Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `registers` MODIFY COLUMN `registration_date_tos` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации ТОС в Управлении Министерства юстиции РФ по РК';");
    }
};
