<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
   
    public function creerNotification(Request $request)
    {
        return "creerNotification";
    }
    public function consulterNotification(Request $request)
    {
        return "consulterNotification";
    }
    public function supprimerNotification(Request $request)
    {
        return "supprimerNotification";
    }

   
}
