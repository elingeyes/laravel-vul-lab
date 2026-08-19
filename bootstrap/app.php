<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // VULNERABILITY (fixture support): Laravel's EncryptCookies middleware
        // encrypts every response cookie by default, which would hand the browser
        // ciphertext and quietly defeat the /insecure-cookie lesson — the page
        // teaches that a token issued without Secure/HttpOnly is readable from
        // document.cookie, and that is only true if the raw value ships.
        // Excluding lab_session makes the fixture behave the way it is described.
        // Nothing else is excluded: the real session and CSRF cookies stay encrypted.
        $middleware->encryptCookies(except: [
            'lab_session',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
