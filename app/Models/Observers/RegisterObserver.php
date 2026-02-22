<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\Register;
use Ramsey\Uuid\Uuid;

class RegisterObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param Register $model
     */
    public function retrieved(Register $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Register()->cannot('Register.view'))) {
//            return false;
//        }
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param Register $model
     */
    public function creating(Register $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Register()->cannot('core.Register.create'))) {
//            return false;
//        }
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param Register $model
     */
    public function created(Register $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param Register $model
     */
    public function updating(Register $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Register()->cannot('core.Register.update'))) {
//            return false;
//        }
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param Register $model
     */
    public function updated(Register $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param Register $model
     */
    public function saving(Register $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Register()->cannot('core.Register.create'))) {
//            return false;
//        }
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param Register $model
     */
    public function saved(Register $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param Register $model
     */
    public function deleting(Register $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Register()->cannot('core.Register.delete'))) {
//            return false;
//        }
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param Register $model
     */
    public function deleted(Register $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param Register $model
     */
    public function restoring(Register $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Register()->cannot('core.Register.restore'))) {
//            return false;
//        }
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param Register $model
     */
    public function restored(Register $model)
    {
        //
    }
}
