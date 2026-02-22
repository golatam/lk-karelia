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
        Schema::create('permission_role', function (Blueprint $table) {

            $table
                ->unsignedBigInteger('permission_id')
                ->index('permission_id')
                ->nullable()
            ;
            $table
                ->unsignedBigInteger('role_id')
                ->index('role_id')
                ->nullable()
            ;
            $table
                ->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE')
            ;
            $table
                ->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE')
            ;

            $table
                ->unique([
                    'permission_id',
                    'role_id'
                ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
    }
};
