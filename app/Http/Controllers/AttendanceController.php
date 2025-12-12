<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, AttendanceRecord $attendance)
    {
        $user = Auth::user();

        // Base validation
        $rules = [
            'notes' => 'nullable|string|max:1000',
        ];

        // Only top_management and board can update status
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            $rules['status'] = 'required|in:present,late';
        }

        $validated = $request->validate($rules);

        // If user is NOT top_management OR board, ensure we don't update status
        if (!$user->hasRole('top_management') && !$user->hasRole('board')) {
            unset($validated['status']);
        }

        $attendance->update(array_merge($validated, [
            'updated_by' => $user->id,
        ]));

        return back()->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(AttendanceRecord $attendance)
    {
        if (Auth::user()->role !== 'top_management') {
            abort(403);
        }

        $attendance->delete();

        return back()->with('success', 'Attendance record removed successfully.');
    }
}
