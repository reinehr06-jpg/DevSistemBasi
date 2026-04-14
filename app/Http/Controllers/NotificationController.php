<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('system')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function api()
    {
        $notifications = Notification::with('system')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
        return back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);
        return back();
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->delete();
        }
        return back();
    }

    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Todas as notificações foram limpas');
    }
}