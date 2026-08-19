<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// VULNERABILITY: Debug Mode Exposure — debug mode is switched on at runtime for
// every request that passes through, with no environment check at all. Any
// exception raised downstream then renders the full stack trace, the resolved
// configuration and the environment variables (DB credentials, APP_KEY, API keys)
// straight to the browser.
// Should be: never write app.debug at runtime, and keep APP_DEBUG=false in every
// environment that is not a developer's laptop.
//
// This middleware is attached to the /force-debug lab route only (see
// routes/web.php); it is deliberately not registered globally.
class ForceDebugMode
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['app.debug' => true]);

        return $next($request);
    }
}
