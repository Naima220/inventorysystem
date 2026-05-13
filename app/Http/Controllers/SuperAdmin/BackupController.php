<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return view('superadmin.backups.index', compact('shops'));
    }

    public function download($id)
    {
        $shop = Shop::findOrFail($id);
        $dbName = $shop->database()->getName();
        $fileName = "backup_{$shop->id}_" . date('Y-m-d_H-i-s') . ".sql";
        $filePath = storage_path("app/backups/{$fileName}");

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $mysqlPath = $this->getMysqlPath('mysqldump');
        $command = "\"{$mysqlPath}\" --user=" . env('DB_USERNAME') . " --password=\"" . env('DB_PASSWORD') . "\" --host=" . env('DB_HOST') . " {$dbName} > \"{$filePath}\"";

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Failed to create backup. Make sure mysqldump is in your PATH.');
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function restore(Request $request, $id)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);

        $shop = Shop::findOrFail($id);
        $dbName = $shop->database()->getName();
        $file = $request->file('backup_file');
        $filePath = $file->storeAs('temp_backups', $file->getClientOriginalName());
        $fullPath = storage_path("app/{$filePath}");

        $mysqlPath = $this->getMysqlPath('mysql');
        $command = "\"{$mysqlPath}\" --user=" . env('DB_USERNAME') . " --password=\"" . env('DB_PASSWORD') . "\" --host=" . env('DB_HOST') . " {$dbName} < \"{$fullPath}\"";

        exec($command, $output, $returnVar);

        unlink($fullPath);

        if ($returnVar !== 0) {
            return back()->with('error', 'Failed to restore backup.');
        }

        return back()->with('success', "Backup restored successfully for shop: {$shop->name}");
    }

    public function runGlobalBackup()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            return back()->with('success', 'Global backup started successfully. Check storage/app/Laravel for the file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error running global backup: ' . $e->getMessage());
        }
    }

    private function getMysqlPath($command)
    {
        // Try to find in common XAMPP paths if not in PATH
        $paths = [
            'C:\xampp\mysql\bin\\' . $command,
            'C:\Program Files\MySQL\MySQL Server 8.0\bin\\' . $command,
            'C:\Program Files\MySQL\MySQL Server 5.7\bin\\' . $command,
            $command // Default to PATH
        ];

        foreach ($paths as $path) {
            if (file_exists($path . '.exe') || file_exists($path)) {
                return $path;
            }
        }

        return $command;
    }
}
