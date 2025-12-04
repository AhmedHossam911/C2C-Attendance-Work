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
            $query = $user->committees();
        } else {
            $query = Committee::query();
        }

        $query->with('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $committees = $query->paginate(9)->withQueryString();
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
        $query = $committee->users()->where('role', '!=', 'top_management');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(10)->withQueryString();

        $users = User::where('status', 'active')
            ->where('role', '!=', 'top_management')
            ->get();

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
