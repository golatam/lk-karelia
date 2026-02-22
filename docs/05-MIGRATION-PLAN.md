# План миграции: Laravel + jQuery → Laravel + Vue 3 + Inertia.js

## Стратегия

**Паттерн**: Strangler Fig (постепенное замещение)
**Ключевая технология**: Inertia.js — мост между Laravel и Vue 3 без написания REST API
**Принцип**: Одна страница за раз. Старые Blade-шаблоны и новые Vue-компоненты сосуществуют.

---

## Выбор стека

### Почему оставить Laravel на бекенде

| Аргумент | Детали |
|----------|--------|
| Скоринг — 3500+ строк PHP | 4 trait'а расчёта (PPMI: 864 строки, LTOS: ~450, LPTOS: ~370, SZPTOS: ~400) с 5 вариантами по типам МО. Переписывать — месяцы работы, высокий риск регрессий |
| Экспорт документов | PhpWord (шаблоны .docx), PhpSpreadsheet (Excel со стилями), MPDF (PDF) — нет аналогов такого качества в Node.js |
| 42 миграции | Вся работа с БД через Eloquent, полиморфные связи (files, matrix, images) отлажены |
| Авторизация | Policy + middleware + пермишены — рабочая система на 53 пермишена и 5 ролей |

### Почему Vue 3 + Inertia.js на фронтенде

| Аргумент | Детали |
|----------|--------|
| Без REST API | Inertia подменяет `view()` на `Inertia::render()` — контроллеры менять минимально |
| v-model для форм | 45-60 полей с реактивным подсчётом итогов — Vue v-model идеален |
| Простота миграции | Blade → Vue однозначный маппинг: `@foreach` → `v-for`, `@if` → `v-if` |
| Один деплой | Нет отдельного Node.js процесса, Inertia работает через PHP |
| Экосистема | VeeValidate, Pinia, HeadlessUI — зрелые решения для всех задач |

### Почему Vite вместо Webpack/Laravel Mix

| Аргумент | Детали |
|----------|--------|
| Скорость | HMR <50ms vs Webpack ~3-5 сек |
| Tree-shaking | Бандл <200KB вместо 784KB |
| Официальная поддержка | `laravel-vite-plugin` — стандарт с Laravel 10+ |
| Простой конфиг | 10 строк vite.config.js вместо webpack.mix.js + webpack.config.js |

### Почему SCSS + Tailwind (гибрид)

| Аргумент | Детали |
|----------|--------|
| Существующие стили работают | 19 SCSS-файлов с BEM-именованием — выбрасывать бессмысленно |
| Tailwind для нового | Новые Vue-компоненты пишутся с Tailwind utility-классами |
| Постепенный переход | По мере замены Blade → Vue, SCSS-компоненты заменяются на Tailwind |

---

## Что НЕ делать

| Решение | Почему нет |
|---------|-----------|
| Переписать бекенд на Node/Go/Rust | Скоринг + экспорт = 5000+ строк отлаженного PHP. Нулевая польза для юзера |
| Строить отдельное SPA с REST API | 80-100 эндпоинтов = месяцы работы. Inertia даёт SPA-опыт без API |
| Менять СУБД на PostgreSQL | MariaDB работает, 60 000 записей — менять незачем |
| Docker/K8s | Один VPS, один разработчик. Контейнеры добавят сложность без пользы |
| TypeScript с первого дня | Сначала Vue 3 + JS, потом постепенно добавить типы |
| Big Bang переписывание | Система в продакшене. Постепенная замена безопаснее |

---

## Фазы миграции

### Фаза 0: Инфраструктура (1-2 недели)

**Цель**: Настроить новые инструменты, ничего не ломая для пользователей.

**Шаги**:

1. **Обновить Laravel 10 → 11**
   - `composer.json`: `laravel/framework` → `^11.0`
   - Перенести middleware из `app/Http/Kernel.php` в `bootstrap/app.php`
   - Обновить пакеты: `composer update`

2. **Заменить Laravel Mix → Vite**
   - Удалить: `npm remove laravel-mix webpack webpack-cli cross-env`
   - Установить: `npm install -D vite laravel-vite-plugin @vitejs/plugin-vue`
   - Создать `vite.config.js`:
     ```js
     import { defineConfig } from 'vite'
     import laravel from 'laravel-vite-plugin'
     import vue from '@vitejs/plugin-vue'

     export default defineConfig({
       plugins: [
         laravel({ input: ['resources/css/app.css', 'resources/js/app.js'] }),
         vue()
       ]
     })
     ```
   - Обновить `layouts/app.blade.php`: `<link>/<script>` → `@vite()`

3. **Установить Inertia.js**
   - Сервер: `composer require inertiajs/inertia-laravel`
   - Клиент: `npm install @inertiajs/vue3 vue@3`
   - Создать `resources/js/app.js` (новый, рядом с main.js):
     ```js
     import { createApp, h } from 'vue'
     import { createInertiaApp } from '@inertiajs/vue3'

     createInertiaApp({
       resolve: name => import(`./Pages/${name}.vue`),
       setup({ el, App, props, plugin }) {
         createApp({ render: () => h(App, props) })
           .use(plugin)
           .mount(el)
       }
     })
     ```
   - Модифицировать `layouts/app.blade.php` для поддержки `@inertia`

4. **Установить Tailwind CSS**
   - `npm install -D tailwindcss postcss autoprefixer`
   - `npx tailwindcss init -p`
   - Настроить `tailwind.config.js` с путями к Vue-компонентам

5. **Написать тесты скоринга**
   - Для каждого типа МО (MO, MR, GO, GP, SP) × типа заявки (PPMI, LTOS, LPTOS, SZPTOS)
   - Зафиксировать текущие результаты как baseline
   - Использовать `phpunit` с тестовыми данными

6. **Удалить неиспользуемые зависимости**
   - `npm remove vue@2 vue-template-compiler bootstrap axios`

**Контрольная точка**: `npm run build` работает с Vite. Все существующие страницы рендерятся через Blade без изменений. jQuery main.js по-прежнему подключён.

---

### Фаза 1: Layout + простые страницы (2-3 недели)

**Цель**: Установить компонентный каркас и мигрировать простые страницы.

**Шаги**:

1. **Создать Vue-layout**
   ```
   resources/js/
   ├── Layouts/
   │   ├── AppLayout.vue      # Header + Sidebar + Content + Footer
   │   └── AuthLayout.vue     # Простой layout для login/register
   ├── Components/
   │   ├── Sidebar.vue        # Навигация с пермишенами
   │   ├── Header.vue         # Шапка с профилем
   │   ├── Breadcrumb.vue     # Хлебные крошки
   │   ├── Toast.vue          # Уведомления (замена notify.js)
   │   └── ConfirmDialog.vue  # Подтверждение (замена MicroModal)
   └── Pages/
       └── Start.vue          # Первая страница
   ```

2. **Мигрировать StartController**
   ```php
   // Было:
   return view('start', $this->getCollect());
   // Стало:
   return Inertia::render('Start', $this->getCollect());
   ```

3. **Мигрировать страницу постов (список)**
   - Создать `Pages/Posts/Index.vue`
   - Первый опыт с таблицей + пагинацией через Inertia

**Контрольная точка**: Дашборд и посты через Vue. Навигация работает. Остальное через Blade.

---

### Фаза 2: Табличные списки (3-4 недели)

**Цель**: Мигрировать все list-view, создать переиспользуемый DataTable.

**Шаги**:

1. **Создать DataTable.vue**
   ```
   Components/
   ├── DataTable/
   │   ├── DataTable.vue         # Основная таблица
   │   ├── TableHeader.vue       # Сортируемые заголовки
   │   ├── TablePagination.vue   # Пагинация
   │   ├── ColumnSelector.vue    # Выбор колонок
   │   ├── BulkActions.vue       # Массовые действия
   │   └── StatusToggle.vue      # Inline переключатель
   ```

2. **Мигрировать списки в порядке сложности**:
   - Конкурсы (`contests/index`)
   - Муниципалитеты (`municipalities/index`)
   - Регистры (`registers/index`)
   - Роли, Пермишены
   - Пользователи
   - Заявки (6 типов) — используют одинаковый шаблон `index.blade.php`

3. **Реализовать фильтрацию через Inertia**
   ```js
   // Вместо session-based POST filter:
   router.get(url, filters, { preserveState: true })
   ```

**Контрольная точка**: Все таблицы на Vue. Сортировка, фильтрация, пагинация, bulk-delete/restore работают.

---

### Фаза 3: Простые формы (2-3 недели)

**Цель**: Создать библиотеку форм-компонентов, мигрировать простые CRUD-формы.

**Шаги**:

1. **Создать компоненты форм**
   ```
   Components/
   ├── Form/
   │   ├── FormInput.vue         # text, number, email
   │   ├── FormTextarea.vue      # textarea
   │   ├── FormSelect.vue        # Select с поиском (замена Select2)
   │   ├── FormDatePicker.vue    # Датапикер (замена jQuery UI)
   │   ├── FormTimePicker.vue    # Таймпикер
   │   ├── FormToggle.vue        # Toggle-переключатель
   │   ├── FormFileUpload.vue    # Загрузка файлов
   │   ├── FormImage.vue         # Загрузка фото + кроп
   │   ├── FormRichText.vue      # WYSIWYG (Tiptap вместо TinyMCE)
   │   ├── FormMaskedInput.vue   # Маска ввода
   │   └── FormError.vue         # Inline-ошибка под полем
   ```

2. **Установить VeeValidate 4 + Zod**
   - `npm install vee-validate@4 zod @vee-validate/zod`
   - Создать Zod-схемы для draft и published режимов

3. **Мигрировать формы**:
   - Конкурсы (5-6 полей, без файлов)
   - Муниципалитеты
   - Роли (+ чекбоксы пермишенов)
   - Пользователи (+ аватар)
   - Регистры

**Контрольная точка**: Все простые формы на Vue. Клиентская валидация работает. Загрузка файлов/фото через Vue.

---

### Фаза 4: Сложные формы заявок (4-6 недель) — ЯДРО

**Цель**: Мигрировать 6 типов заявок с визардом.

**Шаги**:

1. **Создать инфраструктуру**
   ```
   Components/
   ├── Wizard/
   │   ├── FormWizard.vue        # Контейнер визарда
   │   ├── WizardStep.vue        # Шаг визарда
   │   └── WizardProgress.vue    # Прогресс-бар
   ├── Matrix/
   │   ├── MatrixTable.vue       # Динамическая таблица
   │   └── MatrixRow.vue         # Строка матрицы
   ├── Upload/
   │   ├── FileUploadGroup.vue   # Группа файлов (extracts, protocols...)
   │   └── ImageGallery.vue      # Галерея с сортировкой
   ```

2. **Создать composables**
   ```
   composables/
   ├── useAutoDraft.js           # Автосохранение (debounce 5 сек после изменения)
   ├── useFileUpload.js          # Загрузка файлов с прогрессом
   ├── useImageUpload.js         # Загрузка фото
   └── useCostCalculation.js     # Подсчёт итогов в реальном времени
   ```

3. **Мигрировать PPMI первой** (самая сложная)
   - 7 шагов визарда (см. структуру ниже)
   - Реактивный подсчёт итогов
   - 7 групп файлов
   - 6 матричных таблиц
   - Dual-mode валидация (draft/published)
   - Поля только для админа

4. **Структура визарда PPMI**
   ```
   Шаг 1: Основные данные
   ├── Название проекта
   ├── Муниципалитет (select)
   ├── Типология проекта (15 вариантов)
   └── Население населённого пункта

   Шаг 2: Бюджет
   ├── Ремонтные работы (сумма + комментарий)
   ├── Материалы (сумма + комментарий)
   ├── Оборудование (сумма + комментарий)
   ├── Строительный контроль (сумма + комментарий)
   ├── Прочие расходы (сумма + комментарий)
   └── ИТОГО (автоматический подсчёт)

   Шаг 3: Финансирование
   ├── Муниципальный бюджет
   ├── Физические лица
   ├── Юридические лица
   ├── Республиканский бюджет
   ├── Безвозмездные поступления (матрица)
   └── Эксплуатационные расходы (матрица)

   Шаг 4: Участие населения
   ├── Описание благополучателей
   ├── Количество благополучателей
   ├── Участие на собрании
   ├── Участие в реализации (матрица)
   ├── Участие в эксплуатации (матрица)
   └── Информационное сопровождение (матрица)

   Шаг 5: Документы
   ├── Выписки из реестра
   ├── Техническая документация
   ├── Утверждённые сметы
   ├── Коммерческие предложения (мин. 3)
   ├── Сводный расчёт бюджета
   ├── Протоколы собраний
   └── Анкеты/опросы

   Шаг 6: Сроки
   ├── Дата реализации
   ├── Общий комментарий
   └── Мероприятия (матрица: этапы, сроки, ответственные)

   Шаг 7: Проверка и отправка
   ├── Обзор всех данных
   ├── Валидация полноты
   └── Кнопки: Сохранить черновик / Опубликовать
   ```

5. **Мигрировать остальные типы** (переиспользуя компоненты):
   - SZPTOS (аналог PPMI с другими матрицами)
   - LTOS (другой набор полей, 20+ матриц с изображениями)
   - LPTOS (среднее, + оценка экспертом)
   - LS (простое, 3 матрицы)
   - MostBeautifulVillages (галерея + оценки)

**Контрольная точка**: Все 6 типов заявок работают через Vue-визарды. Автосохранение, валидация, загрузка файлов — всё через Vue.

---

### Фаза 5: Экспорт и авторизация (1-2 недели)

**Цель**: Проверить работу экспорта, мигрировать auth-страницы.

**Шаги**:

1. **Экспорт** — контроллерные методы `exportApplication()` и `exportTable()` не меняются, они возвращают file download. Нужно только проверить что кнопки экспорта в Vue-компонентах корректно ссылаются на маршруты.

2. **Auth-страницы** (опционально) — login, register, forgot-password, reset-password через Inertia + Vue.

3. **Создать ExportButton.vue** — кнопка с выбором формата (PDF/XLSX/DOCX).

---

### Фаза 6: Очистка и оптимизация (2-3 недели)

**Цель**: Удалить весь legacy-код, оптимизировать бандл.

**Шаги**:

1. **Удалить JS-зависимости**:
   ```bash
   npm remove jquery jquery-ui select2 micromodal vanilla-masker
   npm remove laravel-mix webpack webpack-cli cross-env
   ```

2. **Удалить файлы**:
   - `resources/js/main.js` (1625 строк)
   - `resources/js/libs/notify.js`, `timepicker.js`
   - Все замещённые `.blade.php` файлы
   - `webpack.mix.js`

3. **Оптимизация бандла**:
   - Code splitting по маршрутам (lazy import)
   - Tree-shake Font Awesome (только используемые иконки)
   - Сжатие изображений в `resources/images/`

4. **Целевые метрики**:
   | Метрика | Было | Цель |
   |---------|------|------|
   | JS бандл | 784 KB | <200 KB (initial) |
   | CSS бандл | 161 KB | <80 KB |
   | Зависимости | jQuery+jQueryUI+Select2+MicroModal+VMasker | Vue 3 + Inertia |
   | Время загрузки | ~3 сек | <1.5 сек |

---

## Сроки

| Фаза | Длительность | Кумулятивно |
|------|-------------|-------------|
| 0. Инфраструктура | 1-2 нед | 1-2 нед |
| 1. Layout + простые страницы | 2-3 нед | 3-5 нед |
| 2. Табличные списки | 3-4 нед | 6-9 нед |
| 3. Простые формы | 2-3 нед | 8-12 нед |
| 4. Сложные формы (ядро) | 4-6 нед | 12-18 нед |
| 5. Экспорт + auth | 1-2 нед | 13-20 нед |
| 6. Очистка | 2-3 нед | 15-23 нед |

**Важно**: Каждая фаза даёт рабочую систему. Можно остановиться на любой фазе — старые страницы продолжают работать через Blade.

---

## Структура файлов (целевая)

```
resources/js/
├── app.js                    # Точка входа Inertia + Vue 3
├── Layouts/
│   ├── AppLayout.vue         # Основной layout
│   └── AuthLayout.vue        # Layout авторизации
├── Pages/
│   ├── Start.vue             # Дашборд
│   ├── Applications/
│   │   ├── Ppmi/
│   │   │   ├── Index.vue     # Список заявок PPMI
│   │   │   ├── Create.vue    # Создание (визард)
│   │   │   └── Edit.vue      # Редактирование (визард)
│   │   ├── Szptos/...
│   │   ├── Ltos/...
│   │   ├── Lptos/...
│   │   ├── Ls/...
│   │   └── MostBeautifulVillages/...
│   ├── Contests/
│   │   ├── Index.vue
│   │   └── Form.vue
│   ├── Municipalities/...
│   ├── Registers/...
│   ├── Users/...
│   ├── Roles/...
│   ├── Permissions/...
│   ├── Posts/...
│   └── Auth/
│       ├── Login.vue
│       ├── Register.vue
│       └── ForgotPassword.vue
├── Components/
│   ├── DataTable/...         # Табличные компоненты
│   ├── Form/...              # Форм-компоненты
│   ├── Wizard/...            # Визард-компоненты
│   ├── Matrix/...            # Матрица (динамические строки)
│   ├── Upload/...            # Загрузка файлов/фото
│   ├── Sidebar.vue
│   ├── Header.vue
│   ├── Breadcrumb.vue
│   ├── Toast.vue
│   └── ConfirmDialog.vue
└── composables/
    ├── useAutoDraft.js       # Автосохранение черновика
    ├── useFileUpload.js      # Загрузка файлов
    ├── useImageUpload.js     # Загрузка изображений
    ├── useCostCalculation.js # Подсчёт итогов
    └── usePermissions.js     # Проверка пермишенов
```

---

## Риски и митигации

| Риск | Вероятность | Митигация |
|------|-------------|----------|
| Регрессия скоринга | Средняя | Тесты до начала миграции (Фаза 0) |
| 46GB загрузок | Низкая | Файлы остаются на месте, новые Vue-компоненты пишут по тем же путям |
| Совместимость браузеров | Средняя | `@vitejs/plugin-legacy` для Chrome 70+, Firefox 68+ |
| Нехватка времени | Высокая | Strangler Fig — остановка безопасна после любой фазы |
| Сложность форм | Средняя | PPMI мигрируется первой, задаёт паттерны для остальных 5 типов |
