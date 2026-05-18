<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $user = auth()->user();
            $unreadNotificationsCount = 0;

            if ($user && method_exists($user, 'notifications')) {
                $unreadNotificationsCount = $user->notifications()
                    ->where('is_read', false)
                    ->count();
            }

            $view->with('unreadNotificationsCount', $unreadNotificationsCount);
        });
    }
}
  