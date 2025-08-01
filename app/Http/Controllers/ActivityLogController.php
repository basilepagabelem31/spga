<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Affiche la liste des journaux d'activités.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest(); // Les plus récents d'abord

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->has('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        $activityLogs = $query->paginate(20);
        return view('activity_logs.index', compact('activityLogs'));
    }

    /**
     * Affiche les détails d'un journal d'activité spécifique.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('activity_logs.show', compact('activityLog'));
    }

    // Les méthodes 'create', 'store', 'edit', 'update', 'destroy' ne sont PAS incluses
    // car les logs d'activité sont généralement gérés automatiquement par l'application
    // et ne doivent pas être modifiés manuellement.
}