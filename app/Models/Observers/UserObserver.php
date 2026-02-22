<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class UserObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param User $model
     */
    public function retrieved(User $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::user()->cannot('user.view'))) {
//            return false;
//        }
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param User $model
     */
    public function creating(User $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::user()->cannot('core.user.create'))) {
//            return false;
//        }
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param User $model
     */
    public function created(User $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param User $model
     */
    public function updating(User $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::user()->cannot('core.user.update'))) {
//            return false;
//        }
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param User $model
     */
    public function updated(User $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param User $model
     */
    public function saving(User $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::user()->cannot('core.user.create'))) {
//            return false;
//        }
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param User $model
     */
    public function saved(User $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param User $model
     */
    public function deleting(User $model)
    {
        $model->is_active = 0;
        $model->save();
//        if (!App::runningInConsole() && (Auth::guest() || Auth::user()->cannot('core.user.delete'))) {
//            return false;
//        }
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param User $model
     */
    public function deleted(User $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param User $model
     */
    public function restoring(User $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::user()->cannot('core.user.restore'))) {
//            return false;
//        }
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param User $model
     */
    public function restored(User $model)
    {
        //
    }
}
