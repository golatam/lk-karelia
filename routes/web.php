<?php

use App\Http\Controllers\MostBeautifulVillageController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StartController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PPMIApplicationController;
use App\Http\Controllers\LTOSApplicationController;
use App\Http\Controllers\LPTOSApplicationController;
use App\Http\Controllers\SZPTOSApplicationController;
use App\Http\Controllers\LSApplicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AjaxController;

use Illuminate\Support\Facades\Artisan;

Artisan::call('route:clear');

Route::middleware(['auth', 'active'])
    ->group(function ($router){

        $router->get('/', StartController::class)->name('start');
        $router->get('/dashboard', StartController::class)->name('dashboard');

        // Конкурсы
        $router
            ->controller(ContestController::class)
            ->name('contests.')
            ->group(function ($router) {

                $router->any('/contests/filter', 'filter')->name('filter');
                $router->get('/contests/restore',  'restore')->name('restore');
                $router->post('/contests/restore',  'restore')->name('restoring');
                $router->delete('/contests',  'destroy')->name('destroy');
            });

        $router->resource('contests', ContestController::class)->except(['destroy']);

        // Конкурсы
        $router
            ->controller(MunicipalityController::class)
            ->name('municipalities.')
            ->group(function ($router) {

                $router->any('/municipalities/filter', 'filter')->name('filter');
                $router->get('/municipalities/restore',  'restore')->name('restore');
                $router->post('/municipalities/restore',  'restore')->name('restoring');
                $router->delete('/municipalities',  'destroy')->name('destroy');
            });

        $router->resource('municipalities', MunicipalityController::class)->except(['destroy']);

        // Реестр ТОС
        $router
            ->controller(RegisterController::class)
            ->name('registers.')
            ->group(function ($router) {

                // Экспорт реестра
                $router
                    ->name('export.')
                    ->prefix('export')
                    ->group(function ($router) {

                        $router->get('/table/{type}',  'exportTable')->name('list');
                    });

                $router->any('/registers/filter', 'filter')->name('filter');
                $router->get('/registers/restore',  'restore')->name('restore');
                $router->post('/registers/restore',  'restore')->name('restoring');
                $router->delete('/registers',  'destroy')->name('destroy');
            });

        $router->resource('registers', RegisterController::class)->except(['destroy']);

        // Заявки
        $router
            ->name('applications.')
            ->prefix('applications')
            ->group(function ($router) {

                // ППМИ
                $router
                    ->controller(PPMIApplicationController::class)
                    ->name('ppmi.')
                    ->group(function ($router) {

                        // Экспорт заявки и рейтинговой таблицы проектов
                        $router
                            ->name('export.')
                            ->prefix('export')
                            ->group(function ($router) {

                                $router->get('/ppmi/table/{type}',  'exportTable')->name('list');
                                $router->get('/ppmi/{type}/{application}',  'exportApplication')->name('item');
                            });

                        $router->any('/ppmi/reCalculation', 'reCalculation')->name('reCalculation');
                        $router->any('/ppmi/filter', 'filter')->name('filter');
                        $router->get('/ppmi/restore',  'restore')->name('restore');
                        $router->post('/ppmi/restore',  'restore')->name('restoring');
                        $router->delete('/ppmi',  'destroy')->name('destroy');
                    });


                $router->resource('ppmi', PPMIApplicationController::class)->except(['show', 'destroy']);

                // Лучшее ТОС
                $router
                    ->controller(LTOSApplicationController::class)
                    ->name('ltos.')
                    ->group(function ($router) {

                        // Экспорт заявки и рейтинговой таблицы проектов
                        $router
                            ->name('export.')
                            ->prefix('export')
                            ->group(function ($router) {

                                $router->get('/ltos/table/{type}',  'exportTable')->name('list');
                                $router->get('ltos/{type}/{application}',  'exportApplication')->name('item');
                            });

                        $router->any('/ltos/filter', 'filter')->name('filter');
                        $router->get('/ltos/restore',  'restore')->name('restore');
                        $router->post('/ltos/restore',  'restore')->name('restoring');
                        $router->delete('/ltos',  'destroy')->name('destroy');
                    });

                $router->resource('ltos', LTOSApplicationController::class)->except(['show', 'destroy']);

                // Лучшая практика гражданских инициатив
                $router
                    ->controller(LPTOSApplicationController::class)
                    ->name('lptos.')
                    ->group(function ($router) {

                        // Экспорт заявки и рейтинговой таблицы проектов
                        $router
                            ->name('export.')
                            ->prefix('export')
                            ->group(function ($router) {

                                $router->get('/lptos/table/{type}',  'exportTable')->name('list');
//                                $router->get('/{type}/{application}',  'exportApplication')->name('item');
                            });

                        $router->any('/lptos/filter', 'filter')->name('filter');
                        $router->get('/lptos/restore',  'restore')->name('restore');
                        $router->post('/lptos/restore',  'restore')->name('restoring');
                        $router->delete('/lptos',  'destroy')->name('destroy');

                        $router->post('/lptos/estimate', 'estimate')->name('estimate');
                    });

                $router->resource('lptos', LPTOSApplicationController::class)->except(['show', 'destroy']);

                // Социально значимые проекты ТОС
                $router
                    ->controller(SZPTOSApplicationController::class)
                    ->name('szptos.')
                    ->group(function ($router) {

                        // Экспорт заявки и рейтинговой таблицы проектов
                        $router
                            ->name('export.')
                            ->prefix('export')
                            ->group(function ($router) {

                                $router->get('/szptos/table/{type}',  'exportTable')->name('list');
                                $router->get('/szptos/{type}/{application}',  'exportApplication')->name('item');
                            });

                        $router->any('/szptos/reCalculation', 'reCalculation')->name('reCalculation');
                        $router->any('/szptos/filter', 'filter')->name('filter');
                        $router->get('/szptos/restore',  'restore')->name('restore');
                        $router->post('/szptos/restore',  'restore')->name('restoring');
                        $router->delete('/szptos',  'destroy')->name('destroy');
                    });

                $router->resource('szptos', SZPTOSApplicationController::class)->except(['show', 'destroy']);

                // Лучший специалист
                $router
                    ->controller(LSApplicationController::class)
                    ->name('ls.')
                    ->group(function ($router) {

                        $router->any('/ls/filter', 'filter')->name('filter');
                        $router->get('/ls/restore',  'restore')->name('restore');
                        $router->post('/ls/restore',  'restore')->name('restoring');
                        $router->delete('/ls',  'destroy')->name('destroy');
                    });

                $router->resource('ls', LSApplicationController::class)->except(['show', 'destroy']);

                // Самое красивое село
                $router
                    ->controller(MostBeautifulVillageController::class)
                    ->name('most_beautiful_villages.')
                    ->group(function ($router) {

                        $router->any('/most_beautiful_villages/filter', 'filter')->name('filter');
                        $router->get('/most_beautiful_villages/restore',  'restore')->name('restore');
                        $router->post('/most_beautiful_villages/restore',  'restore')->name('restoring');
                        $router->delete('/most_beautiful_villages',  'destroy')->name('destroy');

                        $router->post('/most_beautiful_villages/estimate', 'estimate')->name('estimate');

                        $router->any('/most_beautiful_villages/export/{type}', 'exportTable')->name('export.list');
                    });

                $router->resource('most_beautiful_villages', MostBeautifulVillageController::class)->except(['show', 'destroy']);
            });

        $router
            ->controller(PostController::class)
            ->name('posts.')
            ->group(function ($router) {

                $router->any('/posts/filter', 'filter')->name('filter');
                $router->post('/posts/removeFile', 'removeFile')->name('removeFile');
            });

        $router->resource('posts', PostController::class)->except(['show', 'destroy']);

        // Пользователи
        $router
            ->controller(UserController::class)
            ->name('users.')
            ->group(function ($router) {

                $router->any('/users/filter', 'filter')->name('filter');
                $router->get('/profile',  'profile')->name('profile');
                $router->put('/profile/update',  'profileUpdate')->name('profile.update');
                $router->get('/users/restore',  'restore')->name('restore');
                $router->post('/users/restore',  'restore')->name('restoring');
                $router->delete('/users',  'destroy')->name('destroy');
            });

        $router->resource('users', UserController::class)->except(['destroy']);

        // Роли
        $router
            ->controller(RoleController::class)
            ->name('roles.')
            ->group(function ($router) {

                $router->any('/roles/filter', 'filter')->name('filter');
                $router->get('/roles/restore',  'restore')->name('restore');
                $router->post('/roles/restore',  'restore')->name('restoring');
                $router->delete('/roles',  'destroy')->name('destroy');
            });

        $router->resource('roles', RoleController::class)->except(['destroy']);

        // Разрешения
        $router
            ->controller(PermissionController::class)
            ->name('permissions.')
            ->group(function ($router) {

                $router->any('/permissions/filter', 'filter')->name('filter');
            });

        $router->resource('permissions', PermissionController::class)->except(['destroy']);

        // Ajax
        $router
            ->controller(AjaxController::class)
            ->prefix('ajax')
            ->group(function ($router) {

                $router->any('/{action}', 'index');
            });
    });

require __DIR__.'/auth.php';
