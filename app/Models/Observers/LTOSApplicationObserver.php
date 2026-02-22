<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\LTOSApplication;
use Ramsey\Uuid\Uuid;

class LTOSApplicationObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function retrieved(LTOSApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function creating(LTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function created(LTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function updating(LTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function updated(LTOSApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function saving(LTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function saved(LTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function deleting(LTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function deleted(LTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function restoring(LTOSApplication $model)
    {
        //
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param LTOSApplication $model
     */
    public function restored(LTOSApplication $model)
    {
        //
    }
}
