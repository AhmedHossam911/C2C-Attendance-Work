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
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
    });

    /*
    |--------------------------------------------------------------------------
    | Board + Top Management
    | Requirement: Board can create attendance sessions
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:top_management,board'])->group(function () {

        // Sessions
        Route::resource('sessions', SessionController::class);
        Route::post('/sessions/{session}/toggle', [SessionController::class, 'toggleStatus'])->name('sessions.toggle');

        // Committee viewing + member assignments
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
        Route::get('/reports/member', [ReportController::class, 'member'])->name('reports.member');
    });
});
    