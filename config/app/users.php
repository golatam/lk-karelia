<?php

return [
    // Поля что выводятся изначально в списке
    'fields_selected_default' => [
        'id',
        'first_name',
        'email',
        'is_active',
    ],
    // Поля которые можно вывести
    'fields_for_showing' => [
        'last_name',            // Фамилия
        'first_name',           // Имя
        'second_name',          // Отчество
        'email',                // Электронная почта
        'email_verified_at',    // Время проверки электронной почты
        'phone',                // Телефон
        'municipality_id',      // Муниципалитет
        'municipality_chief',   // Глава (глава администрации) муниципального образования
        'municipality_phone',   // Контактный телефон администрации
        'municipality_email',   // E-mail администрации
        'municipality_address', // Адрес администрации
        'executor',             // Исполнитель
        'executor_phone',       // Контактный телефон исполнителя
        'executor_email',       // E-mail исполнителя
        'password',             // Пароль
        'avatar',               // Аватар
        'role_id',              // ID роли
        'is_active',            // Является активным
    ],
    // Миниатюры для фото если есть
    'miniatures' => [
        "thumbnail" => [
            "width" => 200,
            "height" => 200,
        ],
    ],
    // Вывод типов полей в списке
    'fields_type' => [
        // Вывод по ключу из массива
        'config' => [],
        // Вывод отношения
        'relationships' => [
            'role_id' => 'role',
        ],
        // Картинки
        'images' => [
            'avatar',
        ],
        // Ссылки
        'links' => [
            'id',
            'avatar',
            'email',
        ],
        // checkbox
        'checkbox' => [
            'is_active'
        ],
        // Класс full
        'full' => [
            'email',
        ],
        // Сортировка по полям
        'sorting' => [
            'id',
            'email',
        ],
    ],
];
