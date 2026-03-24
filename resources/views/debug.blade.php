@extends('layout')
@section('title', 'Data Exposure')

@section('content')
    <h2>Sensitive Data Exposure</h2>
    <p class="subtitle">A02:2021 — Cryptographic Failures / A05:2021 — Security Misconfiguration</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> This page dumps all application configuration (including secrets, database credentials, and API keys)
        and exposes a <code>phpinfo()</code> endpoint. Both should never be accessible in production.
        <br><br>
        <a href="/phpinfo" target="_blank" style="color: #f85149;">Open phpinfo() &rarr;</a>
    </div>

    <h3 style="color: #f0f6fc; margin-bottom: 10px;">Environment Variables ($_ENV)</h3>
    <div class="output config-dump">@foreach($env as $key => $value)
<span style="color: #d29922;">{{ $key }}</span>=<span style="color: #f85149;">{{ is_string($value) ? $value : json_encode($value) }}</span>
@endforeach</div>

    <h3 style="color: #f0f6fc; margin: 20px 0 10px;">Full Application Config</h3>
    <div class="output config-dump">{{ json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
@endsection
