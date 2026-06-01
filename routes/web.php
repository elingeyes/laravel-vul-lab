<?php

use App\Http\Controllers\VulnController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VulnController::class, 'home']);

// 1. SQL Injection
Route::get('/sqli', [VulnController::class, 'sqli']);

// 2. XSS
Route::match(['get', 'post'], '/xss', [VulnController::class, 'xss']);

// 3. Broken Authentication
Route::get('/auth', [VulnController::class, 'authPage']);
Route::post('/auth', [VulnController::class, 'authLogin']);

// 4. IDOR
Route::get('/idor', [VulnController::class, 'idor']);
Route::get('/profile/{id}', [VulnController::class, 'idorProfile']);

// 5. Command Injection
Route::get('/cmdi', [VulnController::class, 'cmdi']);

// 6. Mass Assignment
Route::get('/mass-assignment', [VulnController::class, 'massAssignment']);
Route::post('/mass-assignment', [VulnController::class, 'massAssignmentCreate']);

// 7. Sensitive Data Exposure
Route::get('/debug', [VulnController::class, 'debug']);
Route::get('/phpinfo', [VulnController::class, 'phpinfo']);

// 8. Broken Access Control
// VULNERABILITY: Broken Access Control — middleware is commented out.
// Anyone can access the admin dashboard without authentication.
Route::get('/admin', [VulnController::class, 'admin'])
    // ->middleware('auth')
    ->name('admin');

// 9. SSRF
Route::get('/ssrf', [VulnController::class, 'ssrf']);

// 10. Sensitive Data Exposure (API)
Route::get('/api/profile/{id}', [VulnController::class, 'apiProfile']);

// 11. Open Redirect
Route::get('/redirect', [VulnController::class, 'redirectTo']);
