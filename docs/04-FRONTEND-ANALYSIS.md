# Анализ фронтенда

## Архитектура

**Тип**: Серверный рендеринг (Blade) + jQuery для интерактивности
**Подход**: Multi-page application (MPA), все маршруты — полная перезагрузка страницы
**Точка входа JS**: `resources/js/main.js` (1625 строк)
**Точка входа CSS**: `resources/sass/main.scss` → 19 компонентных файлов

---

## Структура main.js (1625 строк)

| Блок | Строки | % | Описание |
|------|--------|---|----------|
| Инфраструктура | ~350 | 21% | jQuery UI, CSRF-токен, MicroModal, sidebar, dropdown, Select2, tabs |
| Формы | ~900 | 55% | Чекбоксы, delete/restore, datepicker, type-switch, auto-save, динамические строки, masker |
| Загрузка файлов | ~375 | 23% | files() — загрузка файлов, image() — одиночное фото + кроп, images() — галерея с сортировкой |

### Ключевые функции

```javascript
// Автосохранение черновика (каждые 30 сек)
setInterval(saveDraftData, 30000);

// Динамическое добавление строк в матрицу
$('.js-add').on('click', function() { /* clone first .js-group-item */ });
$('.js-delete').on('click', function() { /* remove row */ });

// Загрузка файлов
function files(el) { /* FormData AJAX upload с progress */ }
function image(el) { /* single image + optional Cropper.js crop */ }
function images(el) { /* multiple images + jQuery Sortable reorder */ }

// Bulk-операции
$('.js-checks').change(function() { /* select all checkboxes */ });
$('.js-delete-btn').click(function() { /* show MicroModal, AJAX delete */ });
```

---

## UI-компоненты (11 паттернов)

### 1. DataTable (index.blade.php)
- Сортируемые колонки (состояние в cookies)
- Настраиваемая видимость колонок (dropdown)
- Мульти-выбор чекбоксами + bulk-действия
- Пагинация через form resubmit
- Inline toggle-переключатели (active/inactive)
- Double-scroll для широких таблиц

### 2. MatrixTable (components/matrix-component.blade.php)
- Шаблон строки (clone при добавлении)
- Поля: input, textarea, date, datetime
- Кнопки: добавить строку / удалить строку
- Опциональное описание

### 3. FileUpload (в main.js, function files())
- AJAX FormData загрузка
- Валидация расширений
- Список загруженных файлов со скачиванием
- Удаление по одному

### 4. ImageUpload (в main.js, function image())
- Предпросмотр до загрузки
- Опциональный кроп (Cropper.js)
- Замена/удаление

### 5. ImageGallery (в main.js, function images())
- Множественная загрузка
- Drag & drop для сортировки (jQuery Sortable)
- Описания к каждому фото
- Лимит загрузок

### 6. FormFields (partial/fields/input.blade.php)
- Input: text, number, email, date, datetime, time
- Textarea
- Select2 (large/medium)
- Toggle/checkbox
- TinyMCE (визуальный редактор)
- Masked input (VMasker)

### 7. ConfirmModal (modals/remove.blade.php, modals/restore.blade.php)
- MicroModal
- Подтверждение удаления
- Подтверждение восстановления

### 8. CropModal (modals/crop.blade.php)
- Cropper.js внутри MicroModal
- Обрезка изображения

### 9. Search (в main.js)
- Live AJAX поиск
- Определение ID (если поиск начинается с пробела)
- Dropdown с результатами

### 10. Sidebar (include/header.blade.php)
- Сворачиваемое меню
- Hamburger-кнопка
- Иерархические пункты

### 11. Tabs (в main.js + tabs.scss)
- Переключение вкладок по href
- Состояние активной вкладки

---

## CSS-архитектура

### Файловая структура

```
resources/sass/
├── main.scss          # @import всех компонентов
├── vars.scss          # Переменные, миксины, reset
└── components/
    ├── common.scss    # Общие стили, типография
    ├── grid.scss      # 12-колоночная flexbox-сетка
    ├── buttons.scss   # Кнопки (6 цветов × 3 размера)
    ├── forms.scss     # Поля ввода, textarea, select
    ├── panel.scss     # Панели контента
    ├── header.scss    # Шапка сайта
    ├── sidebar.scss   # Боковое меню
    ├── footer.scss    # Подвал
    ├── modal.scss     # Модальные окна
    ├── tabs.scss      # Табы
    ├── select2.scss   # Кастомизация Select2
    ├── searching.scss # Поисковая строка
    ├── dropdown.scss  # Выпадающие меню
    ├── alert.scss     # Уведомления
    ├── errors.scss    # Ошибки валидации
    ├── auth-page.scss # Страница входа
    ├── jquery-ui.scss # Кастомизация jQuery UI
    └── custom.scss    # Допстили
```

### Переменные (vars.scss)

```scss
// Цвета
$blue: #117dd8;
$blueLight: #1d9df9;
$green: #00a562;
$red: #ff0000;
$orange: #ff7100;

// Брейкпоинты
$tablet: 768px;
$desktop: 1024px;
$desktopLg: 1400px;
$desktopXl: 1920px;

// Миксин адаптивной типографики
@mixin fluid-type($min-font-size, $max-font-size, $min-vw: 320px, $max-vw: 1920px)
```

### Сетка

12-колоночная flexbox-сетка: `.row`, `.col-xs-*`, `.col-sm-*`, `.col-md-*`, `.col-lg-*`

---

## Проблемы фронтенда

### Критические

1. **Монолитный JS** — всё в одном файле, нет модулей/компонентов, невозможно code-split
2. **784KB бандл** — jQuery + jQuery UI + Select2 + вся логика в одном файле
3. **Нет клиентской валидации** — ошибки только после отправки формы на сервер
4. **45-60 полей на одной странице** — форма PPMI перегружена для неопытных пользователей
5. **Нет прогресс-баров при загрузке** — только спиннер, юзер не видит прогресс
6. **Неиспользуемые зависимости** — Vue, Bootstrap, Axios тянутся в бандл без пользы

### Средние

7. **Cookie-based state** — видимость колонок и сортировка хранятся в cookies
8. **Автосохранение без обратной связи** — черновик сохраняется каждые 30 сек молча
9. **Нет undo** — удаление файлов/изображений необратимо
10. **Жёсткие URL в JS** — AJAX-пути захардкожены (`/ajax/uploadFiles`)

### UX-проблемы для неопытных пользователей

11. **Перегруженные формы** — все поля на одном экране без группировки по шагам
12. **Непонятные ошибки** — валидационные сообщения появляются списком вверху страницы
13. **Нет подсказок** — поля без tooltip/help-текста
14. **Нет визуального прогресса** — пользователь не видит сколько осталось заполнить
15. **Мелкий шрифт при печати** — формы не адаптированы для распечатки

---

## Возможности для UX-улучшений

| Улучшение | Влияние | Сложность |
|-----------|---------|-----------|
| Мульти-шаговый визард для заявок | Очень высокое | Средняя |
| Клиентская валидация в реальном времени | Высокое | Низкая |
| Drag & drop для файлов | Высокое | Низкая |
| Прогресс-бар загрузки | Среднее | Низкая |
| Автосохранение с визуальным подтверждением | Среднее | Низкая |
| Tooltip-подсказки к полям | Среднее | Низкая |
| Прогресс-бар заполнения формы | Среднее | Средняя |
| Автоподсчёт итогов при вводе | Высокое | Низкая |
| Мобильная адаптация форм | Среднее | Средняя |
| Inline-ошибки у каждого поля | Высокое | Низкая |
