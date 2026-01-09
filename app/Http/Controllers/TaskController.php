<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Committee;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get accessible committee IDs based on role
        if ($user->role === 'top_management' || $user->role === 'board') {
            // Top Management & HR Board: See all committees
            $accessibleCommitteeIds = \App\Models\Committee::pluck('id');
        } elseif ($user->role === 'hr') {
            // HR Member: Their own committees (HR) + authorized committees (for read-only)
            $authorizedIds = $user->authorizedCommittees->pluck('id');
            $memberOfIds = $user->committees->pluck('id');
            $accessibleCommitteeIds = $authorizedIds->merge($memberOfIds)->unique();
        } elseif ($user->role === 'committee_head') {
            // Committee Head: Their authorized committees only
            $accessibleCommitteeIds = $user->authorizedCommittees->pluck('id');
        } else {
            // Members: Their committees only
            $accessibleCommitteeIds = $user->committees->pluck('id');
        }

        // Get committees with task counts
        $committees = \App\Models\Committee::whereIn('id', $accessibleCommitteeIds)
            ->withCount(['tasks' => function ($query) {
                $query->whereNull('deleted_at');
            }])
            ->orderBy('name')
            ->get();

        // If a committee is selected, get its tasks
        $selectedCommittee = null;
        $tasks = collect();

        if ($request->filled('committee_id')) {
            $selectedCommittee = $committees->firstWhere('id', $request->committee_id);
            if ($selectedCommittee) {
                $tasks = Task::with(['committee', 'creator'])
                    ->withCount('submissions')
                    ->where('committee_id', $request->committee_id)
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('tasks.index', compact('committees', 'selectedCommittee', 'tasks'));
    }

    public function create()
    {
        $user = Auth::user();
        // Get committees user can create tasks for
        if ($user->role === 'committee_head' || $user->role === 'board') {
            $committees = $user->authorizedCommittees;
            $sessions = \App\Models\AttendanceSession::whereIn('committee_id', $committees->pluck('id'))->latest()->get();
        } else {
            abort(403);
        }

        return view('tasks.create', compact('committees', 'sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'committee_id' => 'required|exists:committees,id',
            'type' => 'required|in:basic,extra',
            'deadline' => 'required|date',
            'session_id' => 'required|exists:attendance_sessions,id',
        ]);

        $user = Auth::user();

        // Policy Check
        $policy = new \App\Policies\TaskPolicy();
        if (!$policy->createForCommittee($user, $validated['committee_id'])) {
            abort(403, 'You are not authorized to create tasks for this committee.');
        }

        $validated['created_by'] = $user->id;

        $task = Task::create($validated);

        // Notify committee members
        if ($task->committee && $task->committee->users) {
            $recipients = $task->committee->users->reject(function ($u) use ($user) {
                return $u->id === $user->id;
            });
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\NewTaskNotification($task));
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function remind(Task $task)
    {
        if (Auth::user()->cannot('update', $task)) {
            abort(403);
        }

        $submitterIds = $task->submissions->pluck('user_id');
        $recipients = $task->committee->users->reject(function ($u) use ($submitterIds) {
            return $submitterIds->contains($u->id);
        });

        \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\TaskDeadlineNotification($task));

        return back()->with('success', 'Reminder sent.');
    }

    public function show(Request $request, Task $task)
    {
        $user = Auth::user();
        if ($user->cannot('view', $task)) {
            abort(403);
        }

        $submission = $task->submissionFor($user->id);

        // Load all submissions if user can view them (not same as review permission)
        $submissions = null;
        if ($user->can('viewAllSubmissions', $task)) {
            $query = $task->submissions()->with('user');

            // Filter: Status
            if ($request->filled('status')) {
                if ($request->status === 'late') {
                    $query->where('is_late', true);
                } elseif (in_array($request->status, ['pending', 'reviewed'])) {
                    $query->where('status', $request->status);
                }
            }

            // Filter: Search User
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $submissions = $query->paginate(15)->withQueryString();
        }

        $task->loadCount('submissions');

        return view('tasks.show', compact('task', 'submission', 'submissions'));
    }

    public function edit(Task $task)
    {
        if (Auth::user()->cannot('update', $task)) {
            abort(403);
        }

        $committees = Auth::user()->authorizedCommittees;
        // Fetch sessions for the committee this task is assigned to
        $sessions = \App\Models\AttendanceSession::where('committee_id', $task->committee_id)->latest()->get();

        return view('tasks.edit', compact('task', 'committees', 'sessions'));
    }

    public function update(Request $request, Task $task)
    {
        if (Auth::user()->cannot('update', $task)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:basic,extra',
            'deadline' => 'required|date',
            'session_id' => 'required|exists:attendance_sessions,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if (Auth::user()->cannot('delete', $task)) {
            abort(403);
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }
}
