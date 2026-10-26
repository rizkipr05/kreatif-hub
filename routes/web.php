<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// Authentication
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register-owner', [AuthController::class, 'registerOwner'])->name('register.owner');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tugas (Tasks)
    Route::get('/tugas', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tugas', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tugas/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tugas/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tugas/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tugas/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');

    // Pengumpulan Tim (Submissions)
    Route::get('/pengumpulan', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::post('/tugas/{task}/kumpul', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::patch('/pengumpulan/{submission}/review', [SubmissionController::class, 'review'])->name('submissions.review');

    // Notifikasi
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifikasi/{notification}/baca', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifikasi/tandai-semua', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');

    // Profil
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Superadmin Only Routes
    Route::middleware(['superadmin'])->group(function () {
        // Laporan (Khusus Superadmin)
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export');

        // Manajemen User (Khusus Superadmin)
        Route::get('/manajemen-user', [UserController::class, 'index'])->name('users.index');
        Route::post('/manajemen-user', [UserController::class, 'store'])->name('users.store');
        Route::put('/manajemen-user/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/manajemen-user/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
