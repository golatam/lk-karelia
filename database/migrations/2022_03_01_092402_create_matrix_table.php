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
        Schema::create('matrix', function (Blueprint $table) {
            $table->id();

            $table
                ->morphs('entity')
            ;

            $table
                ->string('group')
                ->nullable()
                ->comment('Группа файлов')
            ;

            $table
                ->unsignedBigInteger('user_id')
                ->comment('ID Пользователя')
            ;

            $table
                ->longText('field1')
                ->nullable()
                ->comment('Наименование организации')
            ;

            $table
                ->longText('field2')
                ->nullable()
                ->comment('Денежный вклад, (рублей)')
            ;

            $table
                ->longText('field3')
                ->nullable()
                ->comment('Мероприятия')
            ;

            $table
                ->longText('field4')
                ->nullable()
                ->comment('Средства из бюджета муниципального образования (руб. в год)')
            ;

            $table
                ->longText('field5')
                ->nullable()
                ->comment('Средства юридических и физических лиц (руб. в год)')
            ;

            $table
                ->longText('field6')
                ->nullable()
                ->comment('Средства приносящие доход деятельности (руб. в год)')
            ;

            $table
                ->longText('field7')
                ->nullable()
                ->comment('Дата документа')
            ;

            $table
                ->longText('field8')
                ->nullable()
                ->comment('№ документа')
            ;

            $table
                ->longText('field9')
                ->nullable()
                ->comment('Наименование документа')
            ;

            $table
                ->longText('field10')
                ->nullable()
                ->comment('Примечание')
            ;

            $table
                ->longText('field11')
                ->nullable()
                ->comment('Название')
            ;

            $table
                ->longText('field12')
                ->nullable()
                ->comment('Год окончания')
            ;

            $table
                ->longText('field13')
                ->nullable()
                ->comment('Специальность')
            ;

            $table
                ->longText('field14')
                ->nullable()
                ->comment('Название')
            ;

            $table
                ->longText('field15')
                ->nullable()
                ->comment('Год окончания')
            ;

            $table
                ->longText('field16')
                ->nullable()
                ->comment('Орган власти')
            ;

            $table
                ->longText('field17')
                ->nullable()
                ->comment('Должность')
            ;

            $table
                ->longText('field18')
                ->nullable()
                ->comment('Годы работы')
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
        Schema::dropIfExists('matrix');
    }
};
