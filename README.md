# Личный кабинет инициативного бюджетирования

Список команд для запуска:

* `git pull origin master`
* копировать .env.example  в .env
* заполнить поля подключения к базе в .env
* `composer install`
* `php artisan key:generate`

* `php artisan migrate`
* `php artisan db:seed`
* `or`
* `php artisan migrate --seed`
* `php artisan db:seed --class=RegisterTableSeeder`

* прописать настройки подключения к smtp в файле .env
* `npm install` -- установка пакетов для сборки фронта
* `npm run development` -- установка стилей/скриптов фронтальной части
* `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1 `  -- добавить cron

