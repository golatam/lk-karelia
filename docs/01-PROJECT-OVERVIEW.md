# Обзор проекта: Личный кабинет ИБ РК

## Что это

Система управления грантами и конкурсами для территориальных общественных самоуправлений (ТОС) Республики Карелия. Личный кабинет, через который муниципальные образования подают заявки на участие в программах поддержки местных инициатив.

**URL**: https://v2.lk.init-rk.ru
**Алиасы**: lk.init-rk.ru → редирект на v2, ppmi.init-rk.ru → редирект на v2
**Сервер**: 78.40.219.206 (Nginx + PHP-FPM 8.1, MariaDB 10.6, Ubuntu 22.04)
**Оригинальный репо**: gitlab.com/chieftec/initrk-lk-v2 (доступ утерян)

---

## Технический стек

| Компонент | Технология | Версия |
|-----------|-----------|--------|
| Backend framework | Laravel | 10 |
| Язык сервера | PHP | 8.1 |
| База данных | MariaDB | 10.6 |
| Frontend JS | jQuery | 3.6 |
| Frontend CSS | Кастомный SCSS | — |
| Шаблоны | Blade | — |
| Сборка | Laravel Mix (Webpack) | 6 |
| Веб-сервер | Nginx | — |
| SSL | Let's Encrypt (Certbot) | — |

### Используемые PHP-пакеты

| Пакет | Назначение |
|-------|-----------|
| laravel/framework ^10 | Основной фреймворк |
| laravel/breeze ^1.8 | Аутентификация (scaffolding) |
| guzzlehttp/guzzle ^7.9 | HTTP-клиент |
| mpdf/mpdf ^8.2 | Генерация PDF |
| phpoffice/phpspreadsheet ^4.5 | Экспорт в Excel |
| phpoffice/phpword ^1.4 | Экспорт в Word |
| doctrine/dbal ^3.10 | Работа с миграциями |

### Используемые JS-пакеты

| Пакет | Назначение | Используется? |
|-------|-----------|--------------|
| jquery 3.6 | DOM-манипуляции, AJAX | Да, основной |
| jquery-ui 1.12 | Datepicker, autocomplete, sortable | Да |
| select2 4.0 | Продвинутые выпадающие списки | Да |
| micromodal 0.4 | Модальные окна | Да |
| @fortawesome/fontawesome-free 5 | Иконки | Да |
| vanilla-masker 1.2 | Маски ввода | Да |
| bootstrap 5.0 | CSS-фреймворк | **НЕТ** (установлен, не используется) |
| vue 2.6 | SPA-фреймворк | **НЕТ** (установлен, не используется) |
| axios 0.21 | HTTP-клиент | **НЕТ** (установлен, не используется) |

---

## Бизнес-домен

### Типы конкурсов (6 штук)

| Тип | Полное название | Модель | Сложность формы |
|-----|---------------|--------|----------------|
| **PPMI** | Поддержка местных инициатив | PPMIApplication | Очень высокая (~45 полей + матрицы + 10 групп файлов) |
| **SZPTOS** | Социально значимые проекты ТОС | SZPTOSApplication | Высокая (~40 полей + матрицы + файлы) |
| **LTOS** | Лучшее ТОС | LTOSApplication | Высокая (~35 полей + 20+ матриц + изображения) |
| **LPTOS** | Лучшая практика ТОС | LPTOSApplication | Средняя (~30 полей + матрицы) |
| **LS** | Лучший специалист | LSApplication | Низкая (~20 полей + 3 матрицы) |
| **MBV** | Самая красивая деревня | MostBeautifulVillage | Средняя (~15 полей + изображения + оценки) |

### Ключевые сущности

| Сущность | Таблица | Записей | Назначение |
|----------|---------|---------|-----------|
| Пользователи | users | 145 | Операторы ЛК от муниципалитетов |
| Муниципалитеты | municipalities | 967 | Иерархия: районы → поселения |
| Регистры (ТОС) | registers | 587 | Реестр общественных самоуправлений |
| Конкурсы | contests | 17 | Конкурсы по годам и типам |
| Заявки PPMI | ppmi_applications | 526 | Заявки на программу поддержки |
| Заявки SZPTOS | szptos_applications | 543 | Заявки на соц. значимые проекты |
| Файлы | files | 29 921 | Полиморфное хранение документов |
| Матрицы | matrix | 25 612 | Полиморфные динамические таблицы |
| Изображения | images | 2 314 | Полиморфное хранение фото |
| Оценки | application_estimates | 821 | Оценки экспертов |
| Роли | roles | 5 | Роли доступа |
| Пермишены | permissions | 53 | Права доступа |

### Роли пользователей

1. **Администратор** — полный доступ, управление пользователями, скоринг
2. **Комиссия (committee)** — просмотр всех заявок, оценка
3. **Пользователь** — подача заявок только от своего муниципалитета

### Workflow заявки

```
Черновик (draft) → Автосохранение каждые 30 сек
                 → Публикация (published) → Экспорт (Word/Excel/PDF)
                                           → Скоринг (автоматический)
                                           → Оценка комиссией
                                           → Допуск к конкурсу
```

---

## Архитектура

### Серверная часть

```
app/
├── Console/Commands/      # Artisan-команды (импорт данных)
├── Enums/                 # MunicipalityTypeEnum (MO, MR, GO, GP, SP)
├── Exceptions/            # Обработка ошибок
├── Extensions/MenuBuilder # Построение навигации по пермишенам
├── Http/
│   ├── Controllers/       # 15 контроллеров
│   ├── Middleware/         # IsActive — проверка активности юзера
│   ├── Requests/          # LoginRequest
│   └── helpers.php        # Хелперы (image_path, cookie, url_frontend)
├── Interfaces/            # ValidateInterface
├── Models/                # 15+ моделей с Observer'ами
│   ├── DefaultModel       # Базовая модель (сортировка, скоупы)
│   ├── DefaultUserModel   # Базовая модель юзера
│   ├── Import/            # Модели для импорта из старой БД
│   └── Observers/         # 10 обсерверов жизненного цикла
├── Notifications/         # ResetPasswordNotification
├── Policies/Policy        # Единая политика авторизации
├── Providers/             # 5 провайдеров
├── Services/MPDFService   # Генерация PDF
├── Traits/                # 6 трейтов
│   ├── CalculationPPMI*   # Скоринг PPMI (864 строки)
│   ├── CalculationLTOS*   # Скоринг LTOS
│   ├── CalculationLPTOS*  # Скоринг LPTOS
│   ├── CalculationSZPTOS* # Скоринг SZPTOS
│   ├── ParentCRUD*        # Общий CRUD для заявок (352 строки)
│   └── ImageTrait         # Обработка изображений
└── View/Components/       # Blade-компоненты (Matrix, Images)
```

### Клиентская часть

```
resources/
├── js/
│   ├── main.js            # 1625 строк jQuery (ВСЁ в одном файле)
│   └── libs/
│       ├── notify.js      # Тост-уведомления
│       └── timepicker.js  # jQuery UI Timepicker
├── sass/
│   ├── main.scss          # Точка входа
│   ├── vars.scss          # Переменные и миксины
│   └── components/        # 19 SCSS-компонентов
│       ├── grid.scss      # 12-колоночная сетка
│       ├── forms.scss     # Стили форм
│       ├── buttons.scss   # Кнопки (6 цветов × 3 размера)
│       ├── panel.scss     # Панели контента
│       ├── sidebar.scss   # Боковое меню
│       ├── modal.scss     # Модальные окна
│       ├── tabs.scss      # Табы
│       └── ...            # + 12 других
├── views/                 # ~110 Blade-шаблонов
│   ├── layouts/           # app.blade.php, auth.blade.php
│   ├── applications/      # Формы 6 типов заявок
│   ├── partial/           # Переиспользуемые части
│   ├── components/        # Blade-компоненты
│   └── modals/            # Модальные окна
└── images/                # Статические изображения
```

### Скомпилированные ассеты

| Файл | Размер |
|------|--------|
| public/assets/js/app.js | 784 KB |
| public/assets/css/app.css | 161 KB |
| public/uploads/ | **46 GB** (пользовательские файлы) |

---

## Внешние интеграции

| Интеграция | Назначение | Статус |
|-----------|-----------|--------|
| WordPress API | Получение новостей (xn----7sbbgrnaabetoq4cya5d0ewd.xn--p1ai) | Активна |
| SMTP (Timeweb) | Отправка email (info@init-rk.ru) | Активна |
| Intradesk чат | Виджет онлайн-чата | Внешний скрипт |
| TinyMCE | Визуальный редактор | Локальный |

---

## Конфигурация сервера

```nginx
server {
    server_name v2.lk.init-rk.ru lk.init-rk.ru;
    root /var/www/v2.lk.init-rk.ru/public;
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ .php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    listen 443 ssl; # Let's Encrypt
}
```

### База данных

- **СУБД**: MariaDB 10.6.22
- **БД**: v2
- **Пользователь**: v2
- **Кодировка**: utf8mb4_unicode_ci
- **21 таблица**, ~60 000 записей
- Дамп схемы: `database/schema.sql`
