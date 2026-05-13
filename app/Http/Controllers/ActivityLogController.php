<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->search;

        // ✅ Multi-DB: Shop walba database-kiisa ayuu leeyahay, shop_id uma baahnid
        $query = ActivityLog::with('user');

        // ✅ Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.activity_logs.index', compact('logs'));
    }
}