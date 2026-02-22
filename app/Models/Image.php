<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = "images";

    protected $fillable = [
        'entity_type',  // Morph Class
        'entity_id',    // ID смежной таблицы
        'path',         // Путь файла
        'description',  // Описание
        'position',     // Прядок показа
        'group',        // Группа фото
    ];

    /**
     * Значения атрибутов модели по умолчанию.
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [];

    public function entity(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
