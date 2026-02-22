<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ImagesComponent extends Component
{
    /**
     * -----------------------------------------------
     * Модель сущности к которой добавляются картинки
     * -----------------------------------------------
     *
     * @var object
     */
    public object $model;

    /**
     * --------------------------------------
     * Коллекция моделей добавленых картинок
     * --------------------------------------
     *
     * @var object
     */
    public object $images;

    /**
     * -------------------------------------
     * Название группы (отношения) картинок
     * -------------------------------------
     *
     * @var string
     */
    public string $group = '';

    /**
     * ---------------
     * Имеет описание
     * ---------------
     *
     * @var bool
     */
    public bool $hasDescription = false;

    /**
     * ------------------------------
     * Тип описания (input, textarea)
     * ------------------------------
     *
     * @var string
     */
    public string $typeDescription = 'input';

    /**
     * ----------------------
     * Лимит добавления фото
     * ----------------------
     *
     * @var int
     */
    public int $limit = 0;

    /**
     * Create a new component instance.
     *
     * @param $model
     * @param $group
     * @param $hasDescription
     * @param $typeDescription
     * @param $limit
     */
    public function __construct($model, $group, $hasDescription, $typeDescription, $limit)
    {
        $this->model = $model;
        $this->images = !is_null($model->{$group}) ? $model->{$group} : collect();
        $this->group = $group;

        if (!empty($hasDescription)) {

            $this->hasDescription = $hasDescription;
        }

        if (!empty($typeDescription)) {

            $this->typeDescription = $typeDescription;
        }

        if (!empty($limit)) {

            $this->limit = $limit;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.images-component');
    }
}
