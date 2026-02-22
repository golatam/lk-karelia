# Маршруты и API

## Сводка

**Всего маршрутов**: 154
**Защищённых auth + active**: ~130 (все кроме auth/* и api/user)
**AJAX-эндпоинт**: `/ajax/{action}` — динамическая маршрутизация к методам AjaxController

---

## Аутентификация (routes/auth.php)

| Метод | URI | Контроллер | Описание |
|-------|-----|-----------|----------|
| GET | /auth/register | RegisteredUserController@create | Форма регистрации |
| POST | /auth/register | RegisteredUserController@store | Создание аккаунта |
| GET | /auth/login | AuthenticatedSessionController@create | Форма входа |
| POST | /auth/login | AuthenticatedSessionController@store | Вход |
| POST | /auth/logout | AuthenticatedSessionController@destroy | Выход |
| GET | /auth/forgot-password | PasswordResetLinkController@create | Забыли пароль |
| POST | /auth/forgot-password | PasswordResetLinkController@store | Отправка ссылки |
| GET | /auth/reset-password/{token} | NewPasswordController@create | Форма сброса |
| POST | /auth/reset-password | NewPasswordController@store | Сброс пароля |
| GET | /auth/verify-email | EmailVerificationPromptController | Подтверждение email |
| GET | /auth/verify-email/{id}/{hash} | VerifyEmailController | Верификация |
| POST | /auth/email/verification-notification | EmailVerificationNotificationController | Повторная отправка |
| GET | /auth/confirm-password | ConfirmablePasswordController@show | Подтверждение пароля |
| POST | /auth/confirm-password | ConfirmablePasswordController@store | Подтверждение |

---

## Дашборд

| Метод | URI | Контроллер | Описание |
|-------|-----|-----------|----------|
| GET | / | StartController | Главная страница |
| GET | /dashboard | StartController | Алиас главной |

---

## CRUD-ресурсы (routes/web.php)

Все защищены middleware `auth` + `active`.

### Заявки PPMI

| Метод | URI | Имя | Описание |
|-------|-----|-----|----------|
| GET | /applications/ppmi | applications.ppmi.index | Список заявок |
| GET | /applications/ppmi/create | applications.ppmi.create | Форма создания |
| POST | /applications/ppmi | applications.ppmi.store | Сохранение |
| GET | /applications/ppmi/{ppmi}/edit | applications.ppmi.edit | Форма редактирования |
| PUT | /applications/ppmi/{ppmi} | applications.ppmi.update | Обновление |
| DELETE | /applications/ppmi | applications.ppmi.destroy | Удаление (bulk) |
| GET | /applications/ppmi/restore | applications.ppmi.restore | Список удалённых |
| POST | /applications/ppmi/restore | applications.ppmi.restoring | Восстановление |
| ANY | /applications/ppmi/filter | applications.ppmi.filter | Фильтрация |
| ANY | /applications/ppmi/reCalculation | applications.ppmi.reCalculation | Пересчёт скоринга |
| GET | /applications/export/ppmi/table/{type} | - | Экспорт таблицы (xlsx/pdf) |
| GET | /applications/export/ppmi/{type}/{application} | - | Экспорт заявки (docx/pdf) |

### Заявки SZPTOS, LTOS, LPTOS, LS

Аналогичная структура. LPTOS и MostBeautifulVillages имеют дополнительно:

| Метод | URI | Описание |
|-------|-----|----------|
| POST | /applications/lptos/estimate | Оценка экспертом |
| POST | /applications/most_beautiful_villages/estimate | Оценка экспертом |
| ANY | /applications/most_beautiful_villages/export/{type} | Экспорт |

### Конкурсы

| Метод | URI | Имя | Описание |
|-------|-----|-----|----------|
| GET | /contests | contests.index | Список |
| GET | /contests/create | contests.create | Создание |
| POST | /contests | contests.store | Сохранение |
| GET | /contests/{contest}/edit | contests.edit | Редактирование |
| PUT | /contests/{contest} | contests.update | Обновление |
| DELETE | /contests | contests.destroy | Удаление |
| GET | /contests/restore | contests.restore | Восстановление |
| ANY | /contests/filter | contests.filter | Фильтрация |

### Муниципалитеты, Регистры, Пользователи, Роли, Пермишены

Аналогичная RESTful-структура с filter и restore.

### Посты

| Метод | URI | Описание |
|-------|-----|----------|
| GET/POST | /posts | Список / Создание |
| GET | /posts/create | Форма создания |
| GET | /posts/{post}/edit | Редактирование |
| PUT | /posts/{post} | Обновление |
| ANY | /posts/filter | Фильтрация |
| POST | /posts/removeFile | Удаление файла |

### Профиль

| Метод | URI | Описание |
|-------|-----|----------|
| GET | /profile | Просмотр профиля |
| PUT | /profile/update | Обновление профиля |

### Экспорт регистров

| Метод | URI | Описание |
|-------|-----|----------|
| GET | /export/table/{type} | Экспорт реестра (xlsx/pdf) |

---

## AJAX-эндпоинты

**Маршрут**: `ANY /ajax/{action}` → `AjaxController@index`

Параметр `{action}` маппится на метод контроллера. Доступные действия:

### Поиск

| Действие | Метод | Параметры | Ответ |
|----------|-------|----------|-------|
| searching | POST | `{db, s, byID}` | `{data: [], entity, type}` |

### Файлы

| Действие | Метод | Параметры | Ответ |
|----------|-------|----------|-------|
| uploadFiles | POST | `FormData {entity, id, modelFullName, files[], group}` | `{files: [{id, path, name, extension}], success, message}` |
| removeFile | POST | `{id, modelFullName}` | `{success, message}` |

### Изображения

| Действие | Метод | Параметры | Ответ |
|----------|-------|----------|-------|
| uploadImage | POST | `FormData {id, entity, columnName, modelFullName, files[]}` | `{imageFilePath, miniature, success, message}` |
| deleteImage | POST | `{id, entity, columnName, modelFullName}` | `{success, message}` |
| uploadImages | POST | `FormData {entity, id, modelFullName, group, files[]}` | `{images: [{id, path, position}], success, message}` |
| removeImages | POST | `{id}` | `{success, message}` |
| changePositionImages | POST | `{positions: {id: position}}` | `{success, message}` |

### Статус

| Действие | Метод | Параметры | Ответ |
|----------|-------|----------|-------|
| activeToggle | POST | `{id, modelFullName, field}` | `{success, message}` |

### Черновики

| Действие | Метод | Параметры | Ответ |
|----------|-------|----------|-------|
| saveDraftData | POST | `{morph_class, model_id, [form fields]}` | `{success, redirect?, message, errors?}` |

---

## Авторизация

### Middleware

- **auth** — проверка аутентификации (Laravel стандарт)
- **active** — `IsActive` middleware: проверяет `is_active == 1` у юзера

### Политики (Policy.php)

Единая политика через магические методы. Маппинг:

```
$tableName = $model->getTable();  // 'ppmi_applications'
$permission = ['group' => Str::singular($tableName), 'action' => $action]

// Пример: view для PPMIApplication
// → group: 'ppmi_application', action: 'view'
// → user->hasPermission(['group' => 'ppmi_application', 'action' => 'view'])
```

### Ключевые пермишены

| Group | Action | Назначение |
|-------|--------|-----------|
| other | show_admin | Доступ администратора (видит все заявки) |
| other | show_committee | Доступ комиссии (видит все заявки) |
| other | show_user | Доступ обычного пользователя |
| ppmi_application | view/create/update/delete/restore | CRUD для PPMI |
| *(аналогично для каждого типа)* | | |

### Фильтрация данных по ролям

```php
// В контроллерах заявок:
if (auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee'])) {
    // Показать ВСЕ заявки
} else {
    // Показать только заявки текущего пользователя
    $query->where('user_id', auth()->id());
}
```
