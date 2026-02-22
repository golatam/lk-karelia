<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\SZPTOSApplication;
use Ramsey\Uuid\Uuid;

class SZPTOSApplicationObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function retrieved(SZPTOSApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function creating(SZPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function created(SZPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function updating(SZPTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function updated(SZPTOSApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function saving(SZPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function saved(SZPTOSApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function deleting(SZPTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function deleted(SZPTOSApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function restoring(SZPTOSApplication $model)
    {
        //
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param SZPTOSApplication $model
     */
    public function restored(SZPTOSApplication $model)
    {
        //
    }
}
