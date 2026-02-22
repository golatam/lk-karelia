<?php

namespace App\Models\Import;

use App\Models\DefaultUserModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends DefaultUserModel
{
    use Notifiable;

    protected $connection = 'import';
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
        'municipality_id',
        'register_tos_id', // ID из реестра ТОС

        'app_glava',
        'app_glava_phone',
        'app_glava_email',
        'app_post_address',
        'app_executor',
        'app_executor_phone',
        'app_executor_email',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    public function getRoleIdAttribute()
    {
        $role = $this->roles->first();

        return $role ? $role->id : 0;
    }
}
