<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// VULNERABILITY: CORS Misconfiguration — the inbound Origin header is echoed back
// into Access-Control-Allow-Origin and paired with Access-Control-Allow-Credentials:
// true. That combination turns every site on the internet into a trusted origin:
// any page the victim visits can read this response with the victim's cookies
// attached, because the browser only checks that the returned origin matches the
// requesting one — which a reflection always does.
// Should be: a fixed allow-list of origins, and never credentials alongside a
// reflected or wildcard origin.
//
// This middleware is attached to the /cors lab route only (see routes/web.php);
// it is deliberately not registered globally.
class ReflectOriginCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', $request->header('Origin', '*'));
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', '*');
        $response->headers->set('Access-Control-Allow-Methods', '*');

        return $response;
    }
}
