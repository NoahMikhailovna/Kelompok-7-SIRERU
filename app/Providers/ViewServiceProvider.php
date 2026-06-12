<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Inject jumlah notif belum dibaca ke semua view (untuk badge di sidebar)
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->role === 'user') {
                $unreadNotif = auth()->user()
                    ->notifications()
                    ->whereNull('read_at')
                    ->count();
                $view->with('unreadNotif', $unreadNotif);
            }
        });
    }
}
