<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\Contest;
use Ramsey\Uuid\Uuid;

class ContestObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param Contest $model
     */
    public function retrieved(Contest $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Contest()->cannot('Contest.view'))) {
//            return false;
//        }
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param Contest $model
     */
    public function creating(Contest $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Contest()->cannot('core.Contest.create'))) {
//            return false;
//        }
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param Contest $model
     */
    public function created(Contest $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param Contest $model
     */
    public function updating(Contest $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Contest()->cannot('core.Contest.update'))) {
//            return false;
//        }
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param Contest $model
     */
    public function updated(Contest $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param Contest $model
     */
    public function saving(Contest $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Contest()->cannot('core.Contest.create'))) {
//            return false;
//        }
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param Contest $model
     */
    public function saved(Contest $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param Contest $model
     */
    public function deleting(Contest $model)
    {
        $model->is_active = 0;
        $model->save();
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Contest()->cannot('core.Contest.delete'))) {
//            return false;
//        }
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param Contest $model
     */
    public function deleted(Contest $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param Contest $model
     */
    public function restoring(Contest $model)
    {
//        if (!App::runningInConsole() && (Auth::guest() || Auth::Contest()->cannot('core.Contest.restore'))) {
//            return false;
//        }
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param Contest $model
     */
    public function restored(Contest $model)
    {
        //
    }
}
