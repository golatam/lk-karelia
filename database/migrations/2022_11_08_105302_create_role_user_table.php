<?php

use App\Models\Role;
use App\Models\User;
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
        Schema::create('role_user', function (Blueprint $table) {

            $table
                ->foreignIdFor(Role::class)
                ->comment('ID роли')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
            ;

            $table
                ->foreignIdFor(User::class)
                ->comment('ID пользователя')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
            ;
        });

        $users = User::all();

        foreach ($users as $user) {

            $user->roles()->sync([$user->role_id]);
        }

        Schema::table('users', function (Blueprint $table) {

            $table
                ->unsignedInteger('role_id')
                ->nullable(true)
                ->comment('ID роли')
                ->change()
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table
                ->unsignedInteger('role_id')
                ->nullable(false)
                ->comment('ID роли')
                ->change()
            ;
        });

        Schema::dropIfExists('role_user');
    }
};
