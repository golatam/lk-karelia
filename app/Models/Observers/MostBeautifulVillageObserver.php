<?php

namespace App\Models\Observers;

use App\Models\MostBeautifulVillage;

class MostBeautifulVillageObserver
{
    /**
     * -------------------------------------------------------------
     * Событие срабатывает , после извлечения модели из базы данных.
     * -------------------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function retrieved(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function creating(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function created(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед изменением в базе данных.
     * -----------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function updating(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после изменения в базе данных.
     * ----------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function updated(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * ------------------------------------------------------
     * Событие срабатывает , перед сохранением в базу данных.
     * ------------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function saving(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , после сохранения в базу данных.
     * -----------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function saved(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * -----------------------------------------------------
     * Событие срабатывает , перед удалением из базы данных.
     * -----------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function deleting(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * ----------------------------------------------------
     * Событие срабатывает , после удаления из базы данных.
     * ----------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function deleted(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * ----------------------------------------------------------
     * Событие срабатывает , перед востановлением в базе данных.
     * ----------------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function restoring(MostBeautifulVillage $model)
    {
        //
    }

    /**
     * --------------------------------------------------------
     * Событие срабатывает , после востановления в базе данных.
     * --------------------------------------------------------
     *
     * @param MostBeautifulVillage $model
     */
    public function restored(MostBeautifulVillage $model)
    {
        //
    }
}
