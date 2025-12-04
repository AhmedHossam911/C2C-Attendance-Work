<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function index()
    {
        $committees = Committee::with('users')->get();
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

    public function show(Committee $committee)
    {
        $committee->load('users');
        $users = User::where('status', 'active')->get();
        return view('committees.show', compact('committee', 'users'));
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
