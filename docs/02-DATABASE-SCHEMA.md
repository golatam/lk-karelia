# Схема базы данных

## Таблицы и связи

### Диаграмма связей (упрощённая)

```
users ───────┬── role_user ──── roles ──── permission_role ──── permissions
  │          │
  ├── municipality_id → municipalities (parent_id → self)
  │                          │
  │                          └── registers
  │
  ├── ppmi_applications ──┬── contest_id → contests
  ├── szptos_applications │── municipality_id → municipalities
  ├── ltos_applications   │── register_id → registers
  ├── lptos_applications  │── user_id → users
  ├── ls_applications     │
  └── most_beautiful_..   │
                          │
                          ├── files (polymorphic: entity_type + entity_id)
                          ├── matrix (polymorphic: entity_type + entity_id)
                          ├── images (polymorphic: entity_type + entity_id)
                          └── application_estimates (polymorphic)
                                └── application_score_columns
```

---

## Детальное описание таблиц

### users (145 записей)

Пользователи системы — операторы от муниципалитетов.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| name | varchar(191) | ФИО |
| email | varchar(191) UNIQUE | Email для входа |
| email_verified_at | timestamp | Верификация email |
| password | varchar(191) | Хеш пароля |
| is_active | tinyint(1) DEFAULT 1 | Активен ли аккаунт |
| avatar | varchar(191) | Путь к аватару |
| municipality_id | bigint FK → municipalities | Муниципалитет пользователя |
| municipality_contact_person | varchar(191) | Контактное лицо |
| municipality_phone | varchar(191) | Телефон |
| executor_person | varchar(191) | Исполнитель |
| executor_phone | varchar(191) | Телефон исполнителя |
| remember_token | varchar(100) | Remember me |
| deleted_at | timestamp | Soft delete |
| created_at, updated_at | timestamp | Временные метки |

**Связи**: belongsToMany(roles), belongsTo(municipality)

---

### municipalities (967 записей)

Иерархическая структура муниципальных образований Карелии.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| name | varchar(191) | Название |
| short_name | varchar(191) | Короткое название |
| parent_id | bigint FK → municipalities | Родительский МО |
| type | enum(MO, MR, GO, GP, SP) | Тип муниципалитета |
| population | int | Население |
| is_active | tinyint(1) DEFAULT 1 | Активен |
| deleted_at | timestamp | Soft delete |
| created_at, updated_at | timestamp | |

**Связи**: parent/children (self-referential), hasMany(registers), hasMany(users)

**Типы**: MO (муниципальный округ), MR (муниципальный район), GO (городской округ), GP (городское поселение), SP (сельское поселение)

---

### contests (17 записей)

Конкурсы по годам и типам программ.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| name | varchar(191) | Название конкурса |
| type | varchar(191) | Тип (ppmi, ltos, lptos, szptos, ls, most_beautiful_village) |
| year_of_competition | varchar(191) | Год конкурса |
| is_active | tinyint(1) DEFAULT 1 | Активен (приём заявок открыт) |
| end_date_active | datetime | Дата окончания приёма |
| deleted_at | timestamp | Soft delete |
| created_at, updated_at | timestamp | |

---

### registers (587 записей)

Реестр территориальных общественных самоуправлений (ТОС).

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| name | varchar(191) | Название ТОС |
| name_region | bigint FK → municipalities | Район |
| name_settlement | bigint FK → municipalities | Поселение |
| charter_info | text | Информация об уставе |
| chairman_full_name | varchar(191) | ФИО председателя |
| contact_phone | varchar(191) | Телефон |
| entity_type | varchar(191) | Тип юрлица |
| is_active | tinyint(1) DEFAULT 1 | Активен |
| deleted_at | timestamp | Soft delete |
| created_at, updated_at | timestamp | |

---

### ppmi_applications (526 записей) — ОСНОВНАЯ ТАБЛИЦА

Заявки на программу поддержки местных инициатив.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| user_id | bigint FK → users | Автор заявки |
| contest_id | bigint FK → contests | Конкурс |
| municipality_id | bigint FK → municipalities | Муниципалитет |
| register_id | bigint FK → registers | ТОС |
| status | varchar(191) DEFAULT 'draft' | Статус (draft / published) |
| project_name | varchar(191) | Название проекта |
| project_typology | varchar(191) | Типология (15 вариантов) |
| population_size | varchar(191) | Население населённого пункта |
| description_problem | longtext | Описание проблемы |
| cost_repair_work | varchar(191) | Стоимость ремонтных работ |
| cost_purchasing_materials | varchar(191) | Стоимость материалов |
| cost_purchasing_equipment | varchar(191) | Стоимость оборудования |
| cost_construction_control | varchar(191) | Стоимость строительного контроля |
| cost_other_expenses | varchar(191) | Прочие расходы |
| cost_repair_work_comment | text | Комментарий к ремонту |
| cost_purchasing_materials_comment | text | Комментарий к материалам |
| cost_purchasing_equipment_comment | text | Комментарий к оборудованию |
| cost_construction_control_comment | text | Комментарий к контролю |
| cost_other_expenses_comment | text | Комментарий к прочим |
| funds_municipal | varchar(191) | Средства муниципального бюджета |
| funds_individuals | varchar(191) | Средства физлиц |
| funds_legal_entities | varchar(191) | Средства юрлиц |
| funds_republic | varchar(191) | Средства республиканского бюджета |
| population_benefiting_description | longtext | Описание благополучателей |
| population_benefiting | varchar(191) | Кол-во благополучателей |
| population_in_congregation | varchar(191) | Участие на собрании |
| population_implementation_participation | varchar(191) | Участие в реализации |
| population_provision_participation | varchar(191) | Участие в эксплуатации |
| implementation_date | varchar(191) | Срок реализации |
| comment | longtext | Общий комментарий |
| total_application_points | decimal(20,2) | Итоговые баллы скоринга |
| points_from_administrator | decimal(20,2) | Баллы от администратора |
| comment_on_points_from_administrator | longtext | Комментарий к баллам |
| is_admitted_to_competition | tinyint(1) | Допущена к конкурсу |
| is_unpaid_work_of_population | tinyint(1) | Безвозмездный труд |
| deleted_at | timestamp | Soft delete |
| created_at, updated_at | timestamp | |

**Связи**: belongsTo(user, contest, municipality, register), morphMany(files, matrix, images)

**Скоринг**: 9 компонентов с весовыми коэффициентами, зависящими от типа муниципалитета (5 вариантов расчёта)

---

### Остальные таблицы заявок

Структуры `szptos_applications`, `ltos_applications`, `lptos_applications`, `ls_applications`, `most_beautiful_villages` аналогичны ppmi_applications с вариациями полей. Детальная схема в `database/schema.sql`.

---

### files (29 921 запись)

Полиморфное хранение документов.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| entity_id | bigint | ID связанной записи |
| entity_type | varchar(191) | Morph-класс (App\Models\PPMIApplication и т.д.) |
| path | varchar(191) | Путь к файлу |
| name | varchar(191) | Имя файла |
| extension | varchar(191) | Расширение |
| group | varchar(191) | Группа (extracts, documentation, protocols и т.д.) |
| created_at, updated_at | timestamp | |

---

### matrix (25 612 записей)

Полиморфное хранение табличных данных (динамические строки в формах).

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| entity_id | bigint | ID связанной записи |
| entity_type | varchar(191) | Morph-класс |
| group | varchar(191) | Тип матрицы (gratuitous_receipts, operating_costs и т.д.) |
| field1...field75 | varchar/text | Динамические поля (используются разные поля для разных групп) |
| created_at, updated_at | timestamp | |

**Примечание**: Антипаттерн — вместо JSON-колонки или нормализованных таблиц используются пронумерованные поля field1-field75.

---

### images (2 314 записей)

Полиморфное хранение изображений.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| entity_id | bigint | ID связанной записи |
| entity_type | varchar(191) | Morph-класс |
| path | varchar(191) | Путь к изображению |
| group | varchar(191) | Группа фото |
| position | int | Позиция для сортировки |
| description | text | Описание |
| created_at, updated_at | timestamp | |

---

### application_estimates (821 запись)

Оценки экспертов/комиссии.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | bigint PK | |
| entity_id | bigint | ID заявки |
| entity_type | varchar(191) | Morph-класс заявки |
| application_score_column_id | bigint FK | Критерий оценки |
| user_id | bigint FK → users | Эксперт |
| value | decimal(20,2) | Оценка |

---

### Сводные таблицы (pivots)

**role_user**: user_id + role_id
**permission_role**: permission_id + role_id

---

## Миграции

42 файла миграций в `database/migrations/`, покрывающие период 2022-2026. Ключевые:

1. `create_users_table` — базовая таблица юзеров
2. `create_municipalities_table` — муниципалитеты с parent_id
3. `create_ppmi_applications_table` — PPMI заявки (30+ колонок)
4. `create_matrix_table` — полиморфная матрица с field1-field75
5. `create_files_table` — полиморфные файлы
6. `add_*_columns` — множество миграций по добавлению полей

Полный SQL-дамп схемы: `database/schema.sql`
