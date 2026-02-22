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
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            $table
                ->string('last_name')
                ->nullable()
                ->comment('Фамилия')
            ;

            $table
                ->string('first_name')
                ->nullable()
                ->comment('Имя')
            ;

            $table
                ->string('second_name')
                ->nullable()
                ->comment('Отчество')
            ;

            $table
                ->string('email')
                ->unique()
                ->comment('Электронная почта')
            ;

            $table
                ->timestamp('email_verified_at')
                ->nullable()
                ->comment('Время проверки электронной почты')
            ;

            $table
                ->string('phone')
                ->nullable()
                ->comment('Телефон')
            ;

            $table
                ->string('password')
                ->comment('Пароль')
            ;

            $table
                ->unsignedBigInteger('municipality_id')
                ->default(0)
                ->comment('ID муниципалитета')
            ;

            $table
                ->unsignedBigInteger('register_id')
                ->default(0)
                ->comment('ID ТОС из реестра')
            ;

            $table
                ->string('municipality_chief')
                ->nullable()
                ->comment('Глава (глава администрации) муниципального образования')
            ;

            $table
                ->string('municipality_phone')
                ->nullable()
                ->comment('Контактный телефон администрации муниципального образования')
            ;

            $table
                ->string('municipality_email')
                ->nullable()
                ->comment('E-mail администрации муниципального образования')
            ;

            $table
                ->text('municipality_address')
                ->nullable()
                ->comment('Почтовый адрес администрации муниципального образования')
            ;

            $table
                ->string('executor')
                ->nullable()
                ->comment('Исполнитель')
            ;

            $table
                ->string('executor_phone')
                ->nullable()
                ->comment('Контактный телефон исполнителя')
            ;

            $table
                ->string('executor_email')
                ->nullable()
                ->comment('E-mail исполнителя')
            ;

            $table
                ->string('avatar')
                ->nullable()
                ->comment('Аватар')
            ;

            $table
                ->unsignedInteger('role_id')
                ->comment('ID роли')
            ;

            $table
                ->tinyInteger('is_active')
                ->default(0)
                ->comment('Является активным')
            ;

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
