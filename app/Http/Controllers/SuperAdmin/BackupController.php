<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        $backupDir = storage_path('app/backups');
        $existingBackups = [];

        if (is_dir($backupDir)) {
            $files = array_diff(scandir($backupDir), ['.', '..']);
            foreach ($files as $file) {
                $existingBackups[] = [
                    'name' => $file,
                    'size' => round(filesize($backupDir . '/' . $file) / 1024, 1) . ' KB',
                    'date' => date('Y-m-d H:i', filemtime($backupDir . '/' . $file)),
                ];
            }
        }

        return view('superadmin.backups.index', compact('shops', 'existingBackups'));
    }

    public function download($id)
    {
        $shop = Shop::findOrFail($id);
        $prefix = config('tenancy.database.prefix', 'tenant');
        $sqlitePath = database_path($prefix . $shop->id);

        if (!file_exists($sqlitePath)) {
            return back()->with('error', 'Database file not found for this shop.');
        }

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $fileName = "backup_{$shop->id}_" . date('Y-m-d_H-i-s') . ".sqlite";
        $backupPath = $backupDir . '/' . $fileName;

        // Simple PHP file copy - no exec() needed
        if (!copy($sqlitePath, $backupPath)) {
            return back()->with('error', 'Failed to create backup.');
        }

        return response()->download($backupPath)->deleteFileAfterSend(false);
    }

    public function restore(Request $request, $id)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);

        $shop = Shop::findOrFail($id);
        $prefix = config('tenancy.database.prefix', 'tenant');
        $sqlitePath = database_path($prefix . $shop->id);

        $uploadedFile = $request->file('backup_file');

        // Backup current DB before restoring
        $currentBackup = $sqlitePath . '.before_restore_' . date('YmdHis');
        if (file_exists($sqlitePath)) {
            copy($sqlitePath, $currentBackup);
        }

        // Copy uploaded file to the SQLite path
        if (!copy($uploadedFile->getRealPath(), $sqlitePath)) {
            return back()->with('error', 'Failed to restore backup.');
        }

        return back()->with('success', "Backup restored successfully for shop: {$shop->name}");
    }

    public function runGlobalBackup()
    {
        $shops = Shop::all();
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $prefix = config('tenancy.database.prefix', 'tenant');
        $backed = 0;

        foreach ($shops as $shop) {
            $sqlitePath = database_path($prefix . $shop->id);
            if (file_exists($sqlitePath)) {
                $fileName = "backup_{$shop->id}_" . date('Y-m-d_H-i-s') . ".sqlite";
                if (copy($sqlitePath, $backupDir . '/' . $fileName)) {
                    $backed++;
                }
            }
        }

        return back()->with('success', "Global backup completed: {$backed} shop(s) backed up.");
    }
}
