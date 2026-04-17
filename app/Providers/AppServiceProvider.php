<?php

namespace App\Providers;

use App\Domain\Models\StockIn;
use App\Domain\Models\User;
use App\Domain\Observers\StockInObserver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\\Factories\\'.class_basename($modelName).'Factory';
        });

        StockIn::observe(StockInObserver::class);

        Gate::define('manage-users', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
