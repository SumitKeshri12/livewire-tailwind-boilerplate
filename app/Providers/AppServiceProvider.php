<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (App::environment(['local'])) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Validation\Rules\Password::defaults(function () {
            return \Illuminate\Validation\Rules\Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Flux UI components are automatically registered by Laravel
        // when placed in resources/views/components/flux/
        // They are available as x-flux.field, x-flux.input, etc.

        $permissions = [
            'permission',
            'edit-permission',
            'roles',
            'view-role',
            'show-role',
            'add-role',
            'edit-role',
            'delete-role',
            'bulkDelete-role',
            'import-role',
            'export-role',
            'users',
            'view-user',
            'show-user',
            'add-user',
            'edit-user',
            'delete-user',
            'bulkDelete-user',
            'import-user',
            'export-user',
            'brands',
            'view-brand',
            'show-brand',
            'add-brand',
            'edit-brand',
            'delete-brand',
            'bulkDelete-brand',
            'import-brand',
            'export-brand',
            'emailformats',
            'view-emailformats',
            'edit-emailformats',
            'emailtemplates',
            'view-emailtemplates',
            'show-emailtemplates',
            'edit-emailtemplates',
            'delete-emailtemplates',
        ];
        if (App::isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        foreach ($permissions ?? [] as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission, $user->role_id);
            });
        }
    }
}
