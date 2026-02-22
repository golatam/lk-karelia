<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends DefaultUserModel
{
    use Notifiable, SoftDeletes;

    /**
     * ----------------------------------------------------------
     * The attributes that are mass assignable.
     * ----------------------------------------------------------
     * Атрибуты, которые могут быть присвоены в массовом порядке
     * ----------------------------------------------------------
     *
     * @var array
     */
    protected $fillable = [
        'id',                   // ID
        'last_name',            // Фамилия
        'first_name',           // Имя
        'second_name',          // Отчество
        'email',                // Электронная почта
        'email_verified_at',    // Время проверки электронной почты
        'phone',                // Телефон
        'password',             // Пароль
        'avatar',               // Аватар
//        'role_id',              // ID роли
        'is_active',            // Является активным
        'municipality_id',      // Муниципалитет
        'municipality_chief',   // Глава (глава администрации) муниципального образования
        'municipality_phone',   // Контактный телефон администрации
        'municipality_email',   // E-mail администрации
        'municipality_address', // Адрес администрации
        'executor',             // Исполнитель
        'executor_phone',       // Контактный телефон исполнителя
        'executor_email',       // E-mail исполнителя
    ];

    /**
     * --------------------------------------------------
     * The attributes that should be hidden for arrays.
     * --------------------------------------------------
     * Атрибуты, которые должны быть скрыты для массивов
     * --------------------------------------------------
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * ------------------------------------------------------------
     * The attributes that should be cast to native types.
     * ------------------------------------------------------------
     * Атрибуты, которые должны быть приведены к собственным типам
     * ------------------------------------------------------------
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'roles',
    ];

    protected $appends = [
        'full_name',
    ];

    public $permission;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->permission = (new Permission());
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(Observers\UserObserver::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function getTosNamesAttribute()
    {
        if (!!$this->municipality) {

            $tos = $this->municipality->registersAll->pluck('name_according_charter', 'id')->toArray();

            return $tos;
        } else {

            return [];
        }
    }

    /**
     * A user may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->with('permissions');
    }

    /**
     * Assign the given role to the user.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function assignRole($role)
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    /**
     * Determine if the user has the given role.
     *
     * @param mixed $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {

            return $this->roles->contains('alias', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    /**
     * @param array $permissions
     * @return bool
     */
    public function hasPermissions(array $permissions)
    {
        $check = false;

        foreach ($permissions as $permission) {

            $hasPermission = auth()->user()->hasPermission($permission);

            if ($hasPermission) {

                $check = $hasPermission;
            }
        }

        return $check;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param $attributes
     * @return bool
     */
    public function hasPermission($attributes): bool
    {
        if (empty($attributes)) {

            return true;
        }

        if (is_array($attributes)) {

            list($group, $action) = [$attributes['group'], $attributes['action']];
        } elseif ($attributes instanceof Collection) {

            list($group, $action) = [$attributes->get('group'), $attributes->get('action')];
        } else {

            list($group, $action) = explode('.', $attributes);
        }

        $permission = Permission::make()
            ->where('group', $group)
            ->where('action', $action)
            ->first()
        ;

        return ((bool) $permission) && $this->hasRole($permission->roles);
    }

    // Фильтрация
    public function filtering()
    {
        $queryBuilder = $this;

        $sessionData = session("{$this->entity()}", []);

        $filter = collect($sessionData)->except(['page', 'filter', 'method', 'sort_column', 'sort_direction'])->toArray();

        $fields = array_keys($filter);

        $filter['used'] = false;

        if (in_array('id', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('id', session("{$this->getTable()}.id"));
        }

        if (in_array('email', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('email', 'like', '%' . session("{$this->getTable()}.email") . '%');
        }
        if (in_array('first_name', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('first_name', 'like', '%' . session("{$this->getTable()}.first_name") . '%');
        }
        if (in_array('role_id', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder
                ->whereHas('roles', function ($role) {
                    $role->where('id', session("{$this->getTable()}.role_id"));
                })
            ;
        }

        if (in_array('is_active', $fields)) {

            $filter['used'] = true;

            $queryBuilder = $queryBuilder->where('is_active', session("{$this->getTable()}.is_active"));
        }

        $sessionData['filter'] = $filter;

        // Пишем его в сессию
        session([$this->entity() => $sessionData]);

        return $queryBuilder;
    }

    public function getShortNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->last_name} {$this->first_name} {$this->second_name}";
    }

    public function isShowComittee()
    {
        return $this->hasPermission(['action' => 'show_committee', 'group' => 'other']);
    }

    public function getMunicipalitiesListAttribute()
    {
        if ($this->municipality) {

            return  $this->municipality->children->isEmpty() ? collect()->push($this->municipality) : $this->childrenCollection($this->municipality);
        } else {

            return collect();
        }
    }

    public function childrenCollection($municipality = null, $result = [])
    {
        if (!$municipality) {

            $result = collect();
            $municipality = $this->municipality;
        } else {

            $result = collect()->push($municipality);
        }

        foreach ($municipality->children as $child) {

            if ($child->children->isNotEmpty()) {

                $result = $this->childrenCollection($child)->merge($result);
            } else {

                $result = $result->push($child);
            }
        }

        return collect($result);
    }

    /**
     * Send a password reset notification to the user.
     * Отправьте пользователю уведомление о сбросе пароля.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function verificationUserDataFilling()
    {
        $attributes = collect($this->getAttributes())->except(['email_verified_at', 'role_id', 'remember_token', 'register_id', 'deleted_at']);
        $filterAttributes = $attributes->filter();
        $emptyAttributes = $attributes->diff($filterAttributes);
        return $emptyAttributes->isNotEmpty();
    }
}
