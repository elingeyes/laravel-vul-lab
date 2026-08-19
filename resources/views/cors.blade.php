@extends('layout')
@section('title', 'CORS Misconfiguration')

@section('content')
    <h2>CORS Misconfiguration</h2>
    <p class="subtitle">A05:2021 — Security Misconfiguration</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> <code>App\Http\Middleware\ReflectOriginCors</code> echoes the inbound <code>Origin</code> header
        back in <code>Access-Control-Allow-Origin</code> and pairs it with <code>Access-Control-Allow-Credentials: true</code>.
        A reflected origin always matches the requesting one, so every site on the internet becomes a trusted origin —
        any page the victim visits can read this response with the victim's cookies attached.
        <br><br>
        <strong>Vulnerable code:</strong> <code>$response-&gt;headers-&gt;set('Access-Control-Allow-Origin', $request-&gt;header('Origin'))</code>
        <br><br>
        <strong>Try it:</strong> <code>curl -I http://localhost:8000/cors -H "Origin: https://evil.example"</code> —
        the response comes back with <code>Access-Control-Allow-Origin: https://evil.example</code>.
        <br><br>
        <strong>Scope:</strong> the middleware is attached to this one lab route, never globally. It is a demonstration
        of a broken CORS policy, not an API for anything.
    </div>

    <div class="output">Access-Control-Allow-Origin: {{ request()->header('Origin', '*') }}
Access-Control-Allow-Credentials: true
Access-Control-Allow-Headers: *
Access-Control-Allow-Methods: *</div>

    <p style="color: #8b949e; font-size: 12px;">
        The block above mirrors the headers this response actually carries. Send an <code>Origin</code> header and it changes with you.
    </p>
@endsection
