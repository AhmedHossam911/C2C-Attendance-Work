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

class ExportImportController extends Controller
{
    public function index()
    {
        $committees = Committee::all();
        $sessions = AttendanceSession::all();
        return view('export_import.index', compact('committees', 'sessions'));
    }

    public function importUsers(Request $request)
    {
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
        $columns = ['name', 'email', 'password', 'role', 'status', 'committees'];

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
        ]);

        $committee = Committee::findOrFail($request->committee_id);
        $users = $committee->users;

        // 1. Generate Excel Data
        $data = [];
        $tempDir = storage_path('app/temp_qrs_' . time());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($users as $user) {
            // Filename: Member Name.png
            $qrFilename = "{$user->name}.png";
            // Sanitize filename
            $qrFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $qrFilename);

            $qrPath = $tempDir . '/' . $qrFilename;

            // Generate QR
            QrCode::format('png')->size(300)->generate($user->id, $qrPath);

            $data[] = [
                'committee_id' => $committee->id,
                'committee_name' => $committee->name,
                'member_id' => $user->id,
                'member_name' => $user->name,
                'email' => $user->email,
                'qr_filename' => $qrFilename,
                'qr_generated_at' => now()->toDateTimeString(),
            ];
        }

        // Create ZIP
        $zipFileName = "qrs_{$committee->name}.zip";
        $zipPath = storage_path('app/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Add Excel File
            $csvFile = fopen($tempDir . '/data.csv', 'w');
            fputcsv($csvFile, array_keys($data[0])); // Headers
            foreach ($data as $row) {
                fputcsv($csvFile, $row);
            }
            fclose($csvFile);

            $zip->addFile($tempDir . '/data.csv', 'data.csv');

            // Add Images
            foreach ($users as $user) {
                $qrFilename = "{$user->name}.png";
                $qrFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $qrFilename);
                if (file_exists($tempDir . '/' . $qrFilename)) {
                    $zip->addFile($tempDir . '/' . $qrFilename, $qrFilename);
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
