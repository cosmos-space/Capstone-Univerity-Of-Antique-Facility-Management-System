<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\College\DashboardController as CollegeDashboardController;
use App\Http\Controllers\College\FacilityController as CollegeFacilityController;
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

// Simple health check (for debugging)
Route::get('/healthz', function () {
    return response()->json([
        'status' => 'ok',
        'time'   => now()->toDateTimeString(),
    ]);
});

// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Facilities
    Route::get('/admin/facilities', [AdminFacilityController::class, 'index'])->name('admin.facilities.index');
    Route::get('/admin/facilities/create', [AdminFacilityController::class, 'create'])->name('admin.facilities.create');
    Route::post('/admin/facilities', [AdminFacilityController::class, 'store'])->name('admin.facilities.store');
    Route::get('/admin/facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('admin.facilities.edit');
    Route::put('/admin/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('admin.facilities.update');
    Route::delete('/admin/facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('admin.facilities.destroy');
});

// College Staff
Route::middleware(['auth', 'role:college_staff'])->group(function () {
    Route::get('/college/dashboard', [CollegeDashboardController::class, 'index'])->name('college.dashboard');
    
    // Facilities
    Route::get('/college/facilities', [CollegeFacilityController::class, 'index'])->name('college.facilities.index');
    Route::get('/college/facilities/create', [CollegeFacilityController::class, 'create'])->name('college.facilities.create');
    Route::post('/college/facilities', [CollegeFacilityController::class, 'store'])->name('college.facilities.store');
    Route::get('/college/facilities/{facility}/edit', [CollegeFacilityController::class, 'edit'])->name('college.facilities.edit');
    Route::put('/college/facilities/{facility}', [CollegeFacilityController::class, 'update'])->name('college.facilities.update');
    Route::delete('/college/facilities/{facility}', [CollegeFacilityController::class, 'destroy'])->name('college.facilities.destroy');
    
    // Bookings (placeholder - to be implemented)
    Route::get('/college/bookings', function() {
        return view('college.bookings.index');
    })->name('college.bookings.index');
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
 