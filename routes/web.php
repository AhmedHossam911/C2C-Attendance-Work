<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Route for Viewing QR (Signed)
Route::get('/my-qr/{user}', [App\Http\Controllers\QrEmailController::class, 'viewQr'])->name('qr.view')->middleware('signed');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Task System Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
    Route::post('/tasks/{task}/remind', [App\Http\Controllers\TaskController::class, 'remind'])->name('tasks.remind');
    Route::post('/tasks/{task}/submit', [App\Http\Controllers\TaskSubmissionController::class, 'store'])->name('tasks.submit');
    Route::patch('/submissions/{submission}', [App\Http\Controllers\TaskSubmissionController::class, 'update'])->name('submissions.update');



    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    /*
    |--------------------------------------------------------------------------
    | Top Management Only
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Top Management Only
    |--------------------------------------------------------------------------
    */

    Route::middleware(['checkRole:top_management'])->group(function () {

        // User Management
        Route::resource('users', UserController::class);
        Route::get('/pending-users', [UserController::class, 'pending'])->name('users.pending');
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');

        // Committees (Top Management can manage, except index/show)
        Route::resource('committees', CommitteeController::class)->except(['index', 'show']);

        // Authorizations
        Route::resource('authorizations', \App\Http\Controllers\AuthorizedCommitteeController::class)->only(['index', 'store', 'destroy']);

        // Report Permissions
        Route::get('/report-permissions', [App\Http\Controllers\ReportPermissionController::class, 'index'])->name('report-permissions.index');
        Route::post('/report-permissions', [App\Http\Controllers\ReportPermissionController::class, 'update'])->name('report-permissions.update');

        // Import/Export
        Route::get('/export-import', [App\Http\Controllers\ExportImportController::class, 'index'])->name('export_import.index');
        Route::post('/import-users', [App\Http\Controllers\ExportImportController::class, 'importUsers'])->name('import.users');
        Route::get('/import-template', [App\Http\Controllers\ExportImportController::class, 'downloadTemplate'])->name('import.template');
        Route::post('/export-qr', [App\Http\Controllers\ExportImportController::class, 'exportQrData'])->name('export.qr');
        Route::get('/cleanup-temp-files', [App\Http\Controllers\ExportImportController::class, 'cleanupTempFiles'])->name('cleanup.temp.files');

        // Send QR Emails
        Route::get('/send-qr', [App\Http\Controllers\QrEmailController::class, 'index'])->name('qr.index');

        // Reports - Top Management Only
        Route::get('/reports/export/committees', [ReportController::class, 'exportCommittees'])->name('reports.export.committees');
        Route::get('/reports/export/members', [ReportController::class, 'exportMembers'])->name('reports.export.members');
    });

    /*
    |--------------------------------------------------------------------------
    | Board + Top Management + HR
    | Requirement: Board can create attendance sessions. HR can also create sessions for their committees.
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board,hr'])->group(function () {

        // Sessions (Modify)
        Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
        Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
        Route::post('/sessions/{session}/toggle', [SessionController::class, 'toggleStatus'])->name('sessions.toggle');
        Route::get('/sessions/{session}/export', [SessionController::class, 'export'])->name('sessions.export');

        // Attendance Management (Edit/Delete - Controller handles specific permission checks)
        Route::resource('attendance', \App\Http\Controllers\AttendanceController::class)->only(['update', 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Board + Top Management (No HR)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board'])->group(function () {
        // Route::get('/sessions/{session}/feedback-results', [SessionController::class, 'feedbackResults'])->name('sessions.feedback-results'); // Moved to common
    });

    /*
    |--------------------------------------------------------------------------
    | General Authenticated Routes (Members + Everyone)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        Route::get('/my-history', [SessionController::class, 'history'])->name('sessions.history');
        Route::get('/sessions/{session}/my-details', [SessionController::class, 'memberDetails'])->name('sessions.member_details'); // NEW
        Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');

        Route::post('/sessions/{session}/feedback', [App\Http\Controllers\SessionFeedbackController::class, 'store'])->name('sessions.feedback');
        Route::get('/sessions/{session}/feedback-results', [App\Http\Controllers\SessionFeedbackController::class, 'show'])->name('sessions.feedback-results');
    });

    /*
    |--------------------------------------------------------------------------
    | Board + Top Management + HR + Committee Head
    | Requirement: Board can create attendance sessions. HR/Head can also create sessions for their committees.
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board,hr,committee_head'])->group(function () {

        // Committee View/Show
        Route::get('/committees', [CommitteeController::class, 'index'])->name('committees.index');
        Route::get('/committees/{committee}', [CommitteeController::class, 'show'])->name('committees.show');
        Route::post('/committees/{committee}/assign', [CommitteeController::class, 'assignUser'])->name('committees.assign');
        Route::delete('/committees/{committee}/remove/{user}', [CommitteeController::class, 'removeUser'])->name('committees.remove');
    });

    /*
    |--------------------------------------------------------------------------
    | Scanning (Top Management, Board, HR)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board,hr'])->group(function () {
        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::post('/session/{session}/scan', [ScanController::class, 'store'])->name('scan.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports (Dynamic Access)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board,committee_head,hr,member'])->group(function () {
        // Reports - Dashboard
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Reports - Individual
        Route::get('/reports/committees', [ReportController::class, 'committees'])->name('reports.committees');
        Route::get('/reports/ghost-members', [ReportController::class, 'ghostMembers'])->name('reports.ghost_members');
        Route::get('/reports/committee-performance', [ReportController::class, 'committeePerformance'])->name('reports.committee_performance');
        Route::get('/reports/top-performers', [ReportController::class, 'topPerformers'])->name('reports.top_performers');
        Route::get('/reports/session-quality', [App\Http\Controllers\SessionFeedbackController::class, 'index'])->name('reports.session_quality');
        Route::get('/reports/session-quality/{session}', [App\Http\Controllers\SessionFeedbackController::class, 'show'])->name('reports.feedback_details');
        Route::get('/feedbacks', [App\Http\Controllers\SessionFeedbackController::class, 'index'])->name('feedbacks.index');
        Route::get('/reports/attendance-trends', [ReportController::class, 'attendanceTrends'])->name('reports.attendance_trends');
        Route::get('/reports/member', [ReportController::class, 'member'])->name('reports.member');


        // QR Image (Shared access for verification)
        Route::get('/qr-image/{id}', [App\Http\Controllers\QrEmailController::class, 'showImage'])->name('qr.image');
    });
});
