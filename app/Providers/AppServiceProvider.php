<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force pakai custom pagination view
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.default');

        // Share unread notification count hanya ke layout utama
        View::composer('layouts.app', function ($view) {
            try {
                if (auth()->check()) {
                    $user = auth()->user();
                    $unreadCount = Notification::where('user_id', $user->id)
                        ->whereNull('read_at')
                        ->count();

                    if ($user->role === 'admin') {
                        $view->with('adminUnreadNotif', $unreadCount);
                    } else {
                        $view->with('unreadNotif', $unreadCount);
                    }
                }
            } catch (\Exception $e) {
                // skip jika tabel belum ada
            }
        });
    }
}