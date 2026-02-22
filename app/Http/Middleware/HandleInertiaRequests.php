<?php

namespace App\Http\Middleware;

use App\Extensions\MenuBuilder;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'layouts.inertia';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            MenuBuilder::build();
        }

        return array_merge(parent::share($request), [
            'appName' => config('app.name', 'Личный кабинет ИБ РК'),
            'sidebar' => fn () => $user
                ? config('app.common.menus.sidebar', [])
                : [],
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('alias'),
                    'avatar_url' => $user->avatar
                        ? url(image_path($user->avatar))
                        : null,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ]);
    }
}
