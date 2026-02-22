<?php

namespace App\Models\Observers;

use App;
use Auth;
use App\Models\PPMIApplication;
use Ramsey\Uuid\Uuid;

class PPMIApplicationObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function retrieved(PPMIApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function creating(PPMIApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function created(PPMIApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function updating(PPMIApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function updated(PPMIApplication $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function saving(PPMIApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function saved(PPMIApplication $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function deleting(PPMIApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function deleted(PPMIApplication $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function restoring(PPMIApplication $model)
    {
        //
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param PPMIApplication $model
     */
    public function restored(PPMIApplication $model)
    {
        //
    }
}
