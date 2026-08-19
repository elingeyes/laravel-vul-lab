<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\VulnController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\ForceDebugMode;
use App\Http\Middleware\ReflectOriginCors;
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

// 12. Broken Access Control (Policy defined but never applied)
// VULNERABILITY: App\Policies\PostPolicy defines update() and delete(), but these
// routes carry no `can:update,post` / `can:delete,post` middleware and the
// controller never calls authorize(), so the policy is never reached.
Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts/{id}', [PostController::class, 'update']);
Route::post('/posts/{id}/delete', [PostController::class, 'destroy']);

// 13. Dynamic Column Injection
Route::get('/sort', [VulnController::class, 'sort']);

// 14. CORS Misconfiguration
// The origin-reflecting middleware is attached here only — it is deliberately not
// global, so the rest of the lab keeps Laravel's default CORS behaviour.
Route::get('/cors', [VulnController::class, 'cors'])
    ->middleware(ReflectOriginCors::class);

// 15. Insecure Cookie Configuration
Route::get('/insecure-cookie', [VulnController::class, 'insecureCookie']);

// 16. Unverified Webhook Signature
// Teaching fixture only — not a working integration endpoint. It is wired to no
// provider and stays inside the `web` group, so CSRF still blocks outside POSTs.
Route::get('/webhook', [WebhookController::class, 'page']);
Route::post('/webhook/receive', [WebhookController::class, 'receive']);

// 17. Debug Mode Exposure
Route::get('/force-debug', [VulnController::class, 'forceDebug'])
    ->middleware(ForceDebugMode::class);
