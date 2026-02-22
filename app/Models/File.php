<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = "files";

    protected $fillable = [
        'entity_type',    // Morph Class
        'entity_id',      // ID смежной таблицы
        'path',         // Путь файла
        'name',         // Имя файла
        'extension',    // Расширение файла
        'group',        // Группа файлов
    ];

    /**
     * Значения атрибутов модели по умолчанию.
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [];

    public function file(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
