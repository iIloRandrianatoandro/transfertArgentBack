<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DB;

class NotificationController extends Controller
{
   
    public function listeNotificationNonLues($id)
    {
        $user = User::find($id);
        $unreadNotifications = $user->unreadNotifications;
        return $unreadNotifications;
    }
    public function listeNotificationLues($id)
    {
        $user = User::find($id);
        $readNotifications = $user->readNotifications;
        return $readNotifications;
    }
    public function markAsRead($id, $notificationId)
    {
        $user = User::find($id);
        $notification = $user->notifications()->where('id', $notificationId)->first();
        
        if ($notification) {
            // Marquer la notification comme lue
            $notification->markAsRead();    
            return'Notification marquée comme lue.';
        } else {
            return 'error Notification non trouvée.';
        }
    }

    public function supprimerNotification($notificationId)
    {
        DB::update("DELETE from notifications WHERE id = ? ", [
            $notificationId
        ]);
    }

   
}
