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
    public function index(Request $request)
    {
        $committees = Committee::all();
        $sessions = AttendanceSession::latest()->get();

        $query = User::query();

        // Filter by Committee
        if ($request->filled('committee_id')) {
            $query->whereHas('committees', function ($q) use ($request) {
                $q->where('committees.id', $request->committee_id);
            });
        }

        // Filter by Search (Name/Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Hide Top Management/Board from list unless user is Top Management
        if (!Auth::user()->hasRole('top_management')) {
            $query->whereNotIn('role', ['top_management', 'board']);
        }

        $users = $query->with('committees')->paginate(10)->withQueryString();

        return view('emails.send_qr', compact('committees', 'sessions', 'users'));
    }

    public function showImage($id)
    {
        $user = User::findOrFail($id);
        $qrData = $user->id; // Static QR Data (ID only)

        $qrImage = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($qrData);

        return response($qrImage)->header('Content-Type', 'image/png');
    }

    public function viewQr(Request $request, User $user)
    {
        // Check for valid signature if we want to protect it, but for now let's keep it simple or use signed route
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        // Generate QR as SVG
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($user->id);

        $data = [
            'member_name' => $user->name,
            'committee_name' => $user->committees->first()->name ?? 'General',
            'session_name' => null
        ];

        return view('emails.qr_code', compact('data', 'qrCode'));
    }
}
