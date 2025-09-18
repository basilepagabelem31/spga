<?php
// app/Traits/LogsActivity.php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function recordLog($action, $table_name, $record_id, $old_values = null, $new_values = null)
    {
        ActivityLog::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'action' => $action,
            'table_name' => $table_name,
            'record_id' => $record_id,
            'old_values' => json_encode($old_values),
            'new_values' => json_encode($new_values),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}