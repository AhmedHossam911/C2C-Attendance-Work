<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\CommitteeAuthorization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizedCommitteeController extends Controller
{
    public function index()
    {
        $authorizations = CommitteeAuthorization::with(['user', 'committee', 'granter'])->latest()->paginate(10);
        $hrUsers = User::where('role', 'hr')->get();
        $committees = Committee::all();

        return view('admin.authorizations.index', compact('authorizations', 'hrUsers', 'committees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'committee_id' => 'required|exists:committees,id',
        ]);

        // Check if already exists
        if (CommitteeAuthorization::where('user_id', $request->user_id)
            ->where('committee_id', $request->committee_id)->exists()
        ) {
            return back()->with('error', 'This user is already authorized for this committee.');
        }

        CommitteeAuthorization::create([
            'user_id' => $request->user_id,
            'committee_id' => $request->committee_id,
            'granted_by' => Auth::id(),
        ]);

        // Log to Audit (Assuming AuditLog model exists or we just rely on the table itself for now)
        // For now, the table tracks 'granted_by' which is sufficient for the requirement "who made the change".

        return back()->with('success', 'Authorization granted successfully.');
    }

    public function destroy(CommitteeAuthorization $authorization)
    {
        $authorization->delete();
        return back()->with('success', 'Authorization revoked successfully.');
    }
}
