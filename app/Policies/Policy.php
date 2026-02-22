<?php

namespace App\Policies;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function __call(string $action, array $arguments)
    {
        $result = false;
        switch ($action) {
            case 'view':
            case 'update':
            case 'delete':
            case 'create':
            case 'restore':
                $user = $arguments[0];
                $model = $arguments[1];
                $tableName = $model->getTable();
                $permission = [
                    'group' => Str::singular($tableName),
                    'action' => $action,
                ];

                $result = $user->hasPermission($permission);

                break;
            case 'show_admin':
            case 'show_committee':
            case 'show_user':

                $user = $arguments[0];
                $tableName = 'other';
                $permission = [
                    'group' => Str::singular($tableName),
                    'action' => $action,
                ];

                $result = $user->hasPermission($permission);

                break;
            default:
                break;
        }

        return $result;
    }
}
