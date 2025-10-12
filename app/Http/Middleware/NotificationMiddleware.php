<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
class NotificationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Share unread notification count
            $unreadCount = auth()->user()->notifications()
                ->where('type', 'dashboard')
                ->whereNull('read_at')
                ->count();

            View::share('unreadNotificationCount', $unreadCount);
            View::share('pendingMaterials', collect());
        }

        return $next($request);
    }
}
