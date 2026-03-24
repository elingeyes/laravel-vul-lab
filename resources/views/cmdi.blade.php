@extends('layout')
@section('title', 'Command Injection')

@section('content')
    <h2>Command Injection</h2>
    <p class="subtitle">A03:2021 — Injection</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> User input is concatenated directly into a <code>shell_exec()</code> call without any sanitization.
        <br><br>
        <strong>Vulnerable code:</strong> <code>shell_exec('ping -c 1 ' . $input)</code>
        <br><br>
        <strong>Try it:</strong> Enter <code>127.0.0.1; whoami</code> or <code>127.0.0.1 && cat /etc/passwd</code> or <code>; ls -la /</code>
    </div>

    <form method="GET" action="/cmdi">
        <input type="text" name="host" placeholder="Enter hostname or IP to ping..." value="{{ $host }}">
        <button type="submit" class="danger">Ping</button>
    </form>

    @if($host !== '')
        <p style="color: #8b949e; font-size: 12px; margin-bottom: 5px;">
            Executing: <code style="color: #f85149;">ping -c 1 {{ $host }}</code>
        </p>
    @endif

    @if($output)
        <div class="output">{{ $output }}</div>
    @endif
@endsection
