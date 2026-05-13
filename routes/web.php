<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\College\DashboardController as CollegeDashboardController;
use App\Http\Controllers\Org\DashboardController as OrgDashboardController;
use App\Http\Controllers\AuthController;

// Authentication routes (protected entry point)
Route::middleware(['login.access'])->group(function () {
    Route::get('/fms-portal-entry', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/fms-portal-entry', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// home route for viewer/general
Route::get('/', function () {
    return view('welcome'); // later: calendar view
})->name('home');

// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

// College Staff
Route::middleware(['auth', 'role:college_staff'])->group(function () {
    Route::get('/college/dashboard', [CollegeDashboardController::class, 'index'])->name('college.dashboard');
});

// Org Staff
Route::middleware(['auth', 'role:org_staff'])->group(function () {
    Route::get('/org/dashboard', [OrgDashboardController::class, 'index'])->name('org.dashboard');
});

// TEMP: create test users for login (remove after you test)
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/make-admin', function () {
    $user = User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]
    );

    return $user;
});

Route::get('/make-college-staff', function () {
    $user = User::updateOrCreate(
        ['email' => 'college@example.com'],
        [
            'name' => 'College Staff User',
            'password' => Hash::make('password123'),
            'role' => 'college_staff',
            'college_name' => 'College of Engineering',
        ]
    );

    return $user;
});

Route::get('/make-org-staff', function () {
    $user = User::updateOrCreate(
        ['email' => 'org@example.com'],
        [
            'name' => 'Organization Staff User',
            'password' => Hash::make('password123'),
            'role' => 'org_staff',
            'organization_name' => 'Student Council',
        ]
    );

    return $user;
});

Route::get('/make-user', function () {
    $user = User::updateOrCreate(
        ['email' => 'user@example.com'],
        [
            'name' => 'Regular User',
            'password' => Hash::make('password123'),
            'role' => 'viewer',
        ]
    );

    return $user;
});
