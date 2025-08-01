<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Affiche la liste des notifications.
     */
    public function index(Request $request)
    {
        $query = Notification::with('user');

        // Optionnel: filtrer par utilisateur si c'est pour un tableau de bord spécifique
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        } else {
            // Pour l'utilisateur actuellement connecté, par exemple
            // $query->where('user_id', auth()->id());
        }

        $notifications = $query->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Affiche les détails d'une notification spécifique.
     */
    public function show(Notification $notification)
    {
        $notification->markAsRead(); // Marque la notification comme lue lorsqu'elle est consultée
        return view('notifications.show', compact('notification'));
    }

    // Les méthodes 'create', 'store', 'edit', 'update' et 'destroy' sont souvent gérées
    // par le système de notification (Laravel Notifications) ou via d'autres logiques métier,
    // plutôt qu'un CRUD direct par un utilisateur.
    // Cependant, pour un exemple complet :

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Supprime une notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notification supprimée.');
    }
}