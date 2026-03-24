@extends('layout')
@section('title', 'Broken Auth')

@section('content')
    <h2>Broken Authentication</h2>
    <p class="subtitle">A07:2021 — Identification and Authentication Failures</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> The login handler has a hardcoded bypass — if the username is <code>admin</code>, any password is accepted and the user is logged in via <code>Auth::loginUsingId(1)</code>.
        <br><br>
        <strong>Try it:</strong> Enter username <code>admin</code> with any password (even blank).
    </div>

    <form method="POST" action="/auth" style="max-width: 400px;">
        @csrf
        <input type="text" name="username" placeholder="Username" value="admin">
        <input type="password" name="password" placeholder="Password (anything works)">
        <button type="submit" class="danger">Login</button>
    </form>

    <div style="margin-top: 20px; font-size: 12px; color: #8b949e;">
        <strong>Vulnerable code:</strong>
        <div class="output">if ($username === 'admin') {
    Auth::loginUsingId(1); // no password check
}</div>
    </div>
@endsection
