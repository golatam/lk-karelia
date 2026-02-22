<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\LPTOSApplication;
use Ramsey\Uuid\Uuid;

class LPTOSApplicationObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function retrieved(LPTOSApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function creating(LPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function created(LPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function updating(LPTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function updated(LPTOSApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function saving(LPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function saved(LPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function deleting(LPTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function deleted(LPTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function restoring(LPTOSApplication $model)
    {
        //
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param LPTOSApplication $model
     */
    public function restored(LPTOSApplication $model)
    {
        //
    }
}
