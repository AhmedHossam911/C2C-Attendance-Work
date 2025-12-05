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

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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

        // Import/Export
        Route::get('/export-import', [App\Http\Controllers\ExportImportController::class, 'index'])->name('export_import.index');
        Route::post('/import-users', [App\Http\Controllers\ExportImportController::class, 'importUsers'])->name('import.users');
        Route::get('/import-template', [App\Http\Controllers\ExportImportController::class, 'downloadTemplate'])->name('import.template');
        Route::post('/export-qr', [App\Http\Controllers\ExportImportController::class, 'exportQrData'])->name('export.qr');

        // Send QR Emails
        Route::get('/send-qr', [App\Http\Controllers\QrEmailController::class, 'index'])->name('qr.index');
        Route::get('/qr-image/{id}', [App\Http\Controllers\QrEmailController::class, 'showImage'])->name('qr.image');
    });

    /*
    |--------------------------------------------------------------------------
    | Top Management + Board
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board'])->group(function () {
        // Authorizations
        Route::resource('authorizations', \App\Http\Controllers\AuthorizedCommitteeController::class)->only(['index', 'store', 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Board + Top Management + HR
    | Requirement: Board can create attendance sessions. HR can also create sessions for their committees.
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board,hr'])->group(function () {

        // Sessions
        Route::resource('sessions', SessionController::class);
        Route::post('/sessions/{session}/toggle', [SessionController::class, 'toggleStatus'])->name('sessions.toggle');
        Route::get('/sessions/{session}/export', [SessionController::class, 'export'])->name('sessions.export');

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
    | Reports (Top Management, Board, HR)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board,hr'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-committees', [ReportController::class, 'exportCommittees'])->name('reports.export.committees');
        Route::get('/reports/member', [ReportController::class, 'member'])->name('reports.member');
        Route::get('/reports/export-members', [ReportController::class, 'exportMembers'])->name('reports.export.members');
    });
});
