<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\AttendanceSession;
use App\Models\User;
use App\Models\EmailLog;
use App\Mail\SendQrEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class QrEmailController extends Controller
{
    public function index()
    {
        $committees = Committee::all();
        $sessions = AttendanceSession::where('status', 'open')->get(); // Or all sessions? Requirement says "Choose session".
        // Let's show all sessions to be safe, or maybe just active ones. 
        // Requirement doesn't specify status. "Choose session".
        $sessions = AttendanceSession::latest()->get();

        return view('emails.send_qr', compact('committees', 'sessions'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'committee_id' => 'required|exists:committees,id',
            'session_id' => 'required|exists:attendance_sessions,id',
        ]);

        $committee = Committee::findOrFail($request->committee_id);
        $session = AttendanceSession::findOrFail($request->session_id);

        // Get users of this committee
        $users = $committee->users;

        $sender = Auth::user();
        $count = 0;

        foreach ($users as $user) {
            // Generate QR Code
            // Format: "ID,Name,Committee" - wait, requirement says "Static Qr with ID ,Name , Committee"
            // But previous scanner code parses "ID,Name,Committee" or just ID.
            // Let's stick to just ID for robustness or the full string if requested.
            // "Static Qr with ID ,Name , Committee"
            $qrData = $user->id . ',' . $user->name . ',' . $committee->name;

            $qrImage = QrCode::format('png')
                ->size(300)
                ->generate($qrData);

            $data = [
                'member_name' => $user->name,
                'committee_name' => $committee->name,
                'session_name' => $session->title,
            ];

            try {
                // Send Email
                // Note: Mail config must be set in .env
                Mail::to($user->email)->send(new SendQrEmail($data, $qrImage));

                // Log Success
                EmailLog::create([
                    'sender_id' => $sender->id,
                    'recipient_email' => $user->email,
                    'subject' => 'Membership QR – ' . $committee->name . ' – ' . $session->title,
                    'status' => 'sent',
                ]);

                $count++;
            } catch (\Exception $e) {
                // Log Failure
                EmailLog::create([
                    'sender_id' => $sender->id,
                    'recipient_email' => $user->email,
                    'subject' => 'Membership QR – ' . $committee->name . ' – ' . $session->title,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', "QR Codes sent to {$count} members.");
    }
}
