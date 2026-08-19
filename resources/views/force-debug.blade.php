@extends('layout')
@section('title', 'Debug Mode Exposure')

@section('content')
    <h2>Debug Mode Exposure</h2>
    <p class="subtitle">A05:2021 — Security Misconfiguration</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> <code>App\Http\Middleware\ForceDebugMode</code> turns <code>app.debug</code> on at runtime for
        every request that passes through it, with no environment check. Any exception raised afterwards renders a full
        stack trace, the resolved configuration and the environment variables — DB credentials, <code>APP_KEY</code>, API keys —
        straight to the browser.
        <br><br>
        <strong>Vulnerable code:</strong> <code>config(['app.debug' =&gt; true]);</code>
        <br><br>
        <strong>Try it:</strong> The block below reports <code>app.debug</code> as the middleware left it — <code>true</code>, whatever
        <code>APP_DEBUG</code> says. Look up <code>app.debug</code> in the config dump on <a href="/debug">/debug</a>, which runs
        without this middleware, to see the value the environment actually configured.
        <br><br>
        <strong>Scope:</strong> the middleware is attached to this one lab route, never globally.
    </div>

    <div class="output">app.debug (on this request): {{ $debug ? 'true' : 'false' }}
APP_ENV: {{ app()->environment() }}</div>
@endsection
