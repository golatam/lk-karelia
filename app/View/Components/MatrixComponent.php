<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MatrixComponent extends Component
{
    public object $model;

    public string $entity = '';

    public string $fieldName = '';

    public string $fieldType = '';

    public array $matrixFields = [];

    public object $matrixModels;

    public int $colNumber = 3;

    /**
     * Create a new component instance.
     *
     * @param $model
     * @param $entity
     * @param $fieldName
     * @param $fieldType
     */
    public function __construct($model, $entity, $fieldName, $fieldType)
    {
        $this->model = $model;
        $this->entity = $entity;
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->matrixFields = config("app.{$entity}.matrix.{$fieldName}", []);
        $this->matrixModels = $this->model->{$fieldName};
        $this->colNumber = (12 - 2) / (!!count($this->matrixFields) ? count($this->matrixFields) : 1);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.matrix-component');
    }
}
