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
        return view('export_import.index', compact('committees', 'sessions'));
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
        ]);

        $committee = Committee::findOrFail($request->committee_id);

        $user = Auth::user();
        if ($user->hasRole('hr')) {
            if (!$user->authorizedCommittees->contains($committee->id)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $users = $committee->users;

        // 1. Generate Excel Data
        $data = [];
        $tempDir = storage_path('app/temp_qrs_' . time());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($users as $user) {
            // Filename: Member Name.svg
            $qrFilename = "{$user->name}.svg";
            // Sanitize filename
            $qrFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $qrFilename);
            $qrPath = $tempDir . '/' . $qrFilename;

            // Generate QR as SVG File
            QrCode::format('svg')->size(300)->generate($user->id, $qrPath);

            // Generate QR as SVG String for embedding in HTML
            $qrSvgString = QrCode::format('svg')->size(300)->generate($user->id);

            // Generate HTML File
            $htmlFilename = "{$user->name}.html";
            $htmlFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $htmlFilename);
            $htmlPath = $tempDir . '/' . $htmlFilename;

            // Generate PDF File
            $pdfFilename = "{$user->name}.pdf";
            $pdfFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $pdfFilename);
            $pdfPath = $tempDir . '/' . $pdfFilename;

            $viewData = [
                'member_name' => $user->name,
                'committee_name' => $committee->name,
                'session_name' => null
            ];
            $htmlContent = view('emails.qr_code', ['data' => $viewData, 'qrCode' => $qrSvgString])->render();
            file_put_contents($htmlPath, $htmlContent);

            // Save PDF
            Pdf::loadHTML($htmlContent)->setPaper('a4', 'portrait')->save($pdfPath);

            // Generate Mailto Link
            $qrUrl = URL::signedRoute('qr.view', ['user' => $user->id]);
            $subject = 'Membership QR - ' . $committee->name;
            $body = "Hello {$user->name},\n\nHere is your membership QR code link:\n{$qrUrl}\n\nPlease click the link to view your QR code page.\n\nPlease keep it safe.\n\nBest regards,";
            $mailtoLink = 'https://mail.google.com/mail/?view=cm&fs=1&to=' . $user->email . '&su=' . urlencode($subject) . '&body=' . urlencode($body);

            $data[] = [
                'committee_id' => $committee->id,
                'committee_name' => $committee->name,
                'member_id' => $user->id,
                'member_name' => $user->name,
                'email' => $user->email,
                'qr_filename' => $qrFilename,
                'html_filename' => $htmlFilename,
                'pdf_filename' => $pdfFilename,
                'mailto_link' => $mailtoLink,
                'html_body' => $htmlContent,
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

            // Add Images and HTML files
            foreach ($users as $user) {
                // Add SVG
                $qrFilename = "{$user->name}.svg";
                $qrFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $qrFilename);
                if (file_exists($tempDir . '/' . $qrFilename)) {
                    $zip->addFile($tempDir . '/' . $qrFilename, $qrFilename);
                }

                // Add HTML
                $htmlFilename = "{$user->name}.html";
                $htmlFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $htmlFilename);
                if (file_exists($tempDir . '/' . $htmlFilename)) {
                    $zip->addFile($tempDir . '/' . $htmlFilename, $htmlFilename);
                }

                // Add PDF
                $pdfFilename = "{$user->name}.pdf";
                $pdfFilename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $pdfFilename);
                if (file_exists($tempDir . '/' . $pdfFilename)) {
                    $zip->addFile($tempDir . '/' . $pdfFilename, $pdfFilename);
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
