<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    use LogsActivity; // N'oubliez pas d'utiliser le trait

    /**
     * Affiche la liste des journaux d'activités avec des options de filtrage améliorées.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        $activityLogs = $query->paginate(8)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);
        $eventTypes = DB::table('activity_logs')->distinct()->pluck('action');
        $tableNames = DB::table('activity_logs')->distinct()->pluck('table_name');
        
        return view('activity_logs.index', compact('activityLogs', 'users', 'eventTypes', 'tableNames'));
    }

    /**
     * Affiche les détails d'un journal d'activité spécifique.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('activity_logs.show', compact('activityLog'));
    }

    /**
     * Supprime un journal d'activité unique.
     */
    public function destroy(ActivityLog $activityLog)
    {
        try {
            $activityLog->delete();
            // Journalisation de l'action de suppression
            $this->recordLog(
                'suppression_log_unique',
                'activity_logs',
                $activityLog->id,
                ['log_id' => $activityLog->id, 'action' => $activityLog->action],
                null
            );
            return redirect()->back()->with('success', 'Le journal d\'activité a été supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du journal.');
        }
    }

    /**
     * Supprime tous les journaux d'activité.
     */
    public function clearAll(Request $request)
    {
        // Vérification du mot de confirmation
        if ($request->input('confirmation_text') !== 'SUPPRIMER') {
            return redirect()->back()->with('error', 'Le mot de confirmation est incorrect.');
        }

        try {
            $count = ActivityLog::count();
            ActivityLog::truncate();
            // Journalisation de l'action de suppression massive
            $this->recordLog(
                'suppression_totale_logs',
                'activity_logs',
                null,
                ['count_deleted' => $count],
                null
            );
            return redirect()->route('activity-logs.index')->with('success', 'Tous les journaux d\'activité ont été supprimés avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression des journaux.');
        }
    }
}