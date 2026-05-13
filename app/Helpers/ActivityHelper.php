<?php

use App\Models\ActivityLog;

function logActivity($action, $description = null)
{
    if (!auth()->check()) {
        return;
    }

    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => $action,
        'description' => $description,
    ]);
}