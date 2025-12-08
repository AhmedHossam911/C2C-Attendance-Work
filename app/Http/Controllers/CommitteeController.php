<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('hr')) {
            $query = $user->authorizedCommittees();
        } else {
            $query = Committee::query();
        }

        $query->with('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $committees = $query->paginate(9)->withQueryString();

        // Privacy Filter: Hide Top Management, Board, and HR from member counts unless user is Top Management
        if (!$user->hasRole('top_management')) {
            $committees->getCollection()->each(function ($committee) use ($user) {
                $filteredUsers = $committee->users->filter(function ($member) use ($user) {
                    $hiddenRoles = ['top_management', 'board', 'hr'];
                    if ($user->hasRole('board')) {
                        $hiddenRoles = ['top_management', 'board'];
                    }
                    return !in_array($member->role, $hiddenRoles);
                });
                $committee->setRelation('users', $filteredUsers);
            });
        }

        return view('committees.index', compact('committees'));
    }

    public function create()
    {
        return view('committees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Committee::create($validated);

        return redirect()->route('committees.index')->with('success', 'Committee created successfully.');
    }

    public function show(Request $request, Committee $committee)
    {
        $user = auth()->user();
        $query = $committee->users();

        // Privacy Filter: Hide Top Management, Board, and HR unless user is Top Management
        if (!$user->hasRole('top_management')) {
            $hiddenRoles = ['top_management', 'board', 'hr'];
            if ($user->hasRole('board')) {
                $hiddenRoles = ['top_management', 'board'];
            }
            $query->whereNotIn('role', $hiddenRoles);
        } else {
            $query->where('role', '!=', 'top_management'); // Top Mgmt sees everyone except other Top Mgmt (or maybe they should see everyone?)
            // Original code was: where('role', '!=', 'top_management'). Let's keep it consistent with original intent but maybe Top Mgmt should see everyone?
            // Actually, usually Top Mgmt can see everyone. But let's stick to the stricter filter for non-Top Mgmt.
            // For Top Mgmt, let's keep the original filter: hide Top Mgmt from list?
            // The original code hid Top Management. Let's preserve that.
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(10)->withQueryString();

        $usersQuery = User::where('status', 'active');

        // Privacy Filter for Dropdown
        if (!$user->hasRole('top_management')) {
            $hiddenRoles = ['top_management', 'board', 'hr'];
            if ($user->hasRole('board')) {
                $hiddenRoles = ['top_management', 'board'];
            }
            $usersQuery->whereNotIn('role', $hiddenRoles);
        } else {
            $usersQuery->where('role', '!=', 'top_management');
        }

        $users = $usersQuery->get();

        return view('committees.show', compact('committee', 'members', 'users'));
    }

    public function assignUser(Request $request, Committee $committee)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $committee->users()->syncWithoutDetaching($request->user_id);

        return back()->with('success', 'User assigned to committee.');
    }

    public function removeUser(Committee $committee, User $user)
    {
        $committee->users()->detach($user->id);
        return back()->with('success', 'User removed from committee.');
    }
}
