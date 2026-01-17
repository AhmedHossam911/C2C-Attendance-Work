<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\Committee;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class ExportImportController extends Controller
{
    public function index()
    {
        $committees = Committee::all();
        $sessions = AttendanceSession::all();
        return view('Top Management.ExportImport.index', compact('committees', 'sessions'));
    }

    public function importUsers(Request $request)
    {
        set_time_limit(300); // Increase timeout to 5 minutes

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return back()->with('success', 'Users imported successfully.');
    }

    public function downloadTemplate()
    {
        // Simple CSV template
        $headers = ['Content-Type' => 'text/csv'];
        $columns = ['name', 'email', 'password', 'role', 'status', 'committees', 'authorized_committees'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportQrData(Request $request)
    {
        $request->validate([
            'committee_id' => 'required|exists:committees,id',
            'session_id' => 'nullable|exists:attendance_sessions,id',
        ]);

        $committee = Committee::findOrFail($request->committee_id);
        
        $sessionName = null;
        if($request->session_id){
             $session = AttendanceSession::find($request->session_id);
             $sessionName = $session ? $session->title : null;
        }

        $user = Auth::user();
        if ($user->hasRole('hr')) {
            if (!$user->authorizedCommittees->contains($committee->id)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $users = $committee->users;

        // 1. Generate Data
        $tempDir = storage_path('app/temp_qrs_' . time());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($users as $user) {
            // Generate QR as SVG String for embedding in HTML
            $qrSvgString = QrCode::format('svg')->size(300)->generate($user->id);

            // Generate HTML File
            $htmlFilename = "{$user->name}.html";
            $htmlFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $htmlFilename);
            $htmlPath = $tempDir . '/' . $htmlFilename;

            $viewData = [
                'member_name' => $user->name,
                'committee_name' => $committee->name,
                'session_name' => $sessionName
            ];
            $htmlContent = view('Common.Emails.qr_code', ['data' => $viewData, 'qrCode' => $qrSvgString])->render();
            file_put_contents($htmlPath, $htmlContent);
        }

        // Create ZIP
        $zipFileName = "qrs_{$committee->name}.zip";
        $zipPath = storage_path('app/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Add HTML files
            foreach ($users as $user) {
                $htmlFilename = "{$user->name}.html";
                $htmlFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $htmlFilename);
                if (file_exists($tempDir . '/' . $htmlFilename)) {
                    $zip->addFile($tempDir . '/' . $htmlFilename, $htmlFilename);
                }
            }

            $zip->close();

            // Cleanup: Delete the temporary directory and all its contents
            File::deleteDirectory($tempDir);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function cleanupTempFiles()
    {
        // Define the pattern for temp directories
        $tempPattern = storage_path('app/temp_qrs_*');
        $dirs = glob($tempPattern, GLOB_ONLYDIR);
        $count = 0;

        foreach ($dirs as $dir) {
            File::deleteDirectory($dir);
            $count++;
        }

        return "Cleanup complete. Deleted {$count} temporary directories.";
    }
}
