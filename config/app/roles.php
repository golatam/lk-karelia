<?php

return [
    // Поля что выводятся изначально в списке
    'fields_selected_default' => [
        'id',
        'name',
        'alias',
    ],
    // Поля которые можно вывести
    'fields_for_showing' => [
        'name',
        'alias',
        'description',
    ],
    // Миниатюры для фото если есть
    'miniatures' => [],
    // Вывод типов полей в списке
    'fields_type' => [
        // Вывод по ключу из массива
        'config' => [],
        // Вывод отношения
        'relationships' => [],
        // Картинки
        'images' => [],
        // Ссылки
        'links' => [
            'id',
            'name',
        ],
        // checkbox
        'checkbox' => [],
        // Класс full
        'full' => [
            'name',
        ],
        // Сортировка по полям
        'sorting' => [
            'id',
            'name',
        ],
    ],
];
