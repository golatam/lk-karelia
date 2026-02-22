<?php

namespace App\Providers;

use App\Models\Contest;
use App\Models\LPTOSApplication;
use App\Models\LSApplication;
use App\Models\LTOSApplication;
use App\Models\MostBeautifulVillage;
use App\Models\Municipality;
use App\Models\PPMIApplication;
use App\Models\Register;
use App\Models\Role;
use App\Models\SZPTOSApplication;
use App\Models\User;
use App\Policies\Policy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => Policy::class,
        User::class => Policy::class,
        Contest::class => Policy::class,
        Register::class => Policy::class,
        PPMIApplication::class => Policy::class,
        LTOSApplication::class => Policy::class,
        LPTOSApplication::class => Policy::class,
        SZPTOSApplication::class => Policy::class,
        LSApplication::class => Policy::class,
        MostBeautifulVillage::class => Policy::class,
        Municipality::class => Policy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
