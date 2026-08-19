@extends('layout')
@section('title', 'Unverified Webhook')

@section('content')
    <h2>Unverified Webhook Signature</h2>
    <p class="subtitle">A08:2021 — Software and Data Integrity Failures</p>

    <div class="vuln-banner" style="border-color: #f85149; background: #2d1214;">
        <strong style="color: #f85149;">NOT A REAL ENDPOINT:</strong> <code>/webhook/receive</code> is a teaching fixture.
        It is wired to no provider, the payload shape is invented for this lab, and it stays inside the <code>web</code>
        middleware group so Laravel's CSRF guard still rejects outside POSTs. Never point a real integration at it.
    </div>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> <code>WebhookController::receive()</code> reads <code>$request-&gt;json()</code> and mutates a
        <code>Post</code> without ever verifying the <code>X-Lab-Signature</code> header. There is no <code>hash_hmac()</code>
        computation and no <code>hash_equals()</code> comparison, so the handler cannot tell a real delivery from a forged one.
        <br><br>
        <strong>Vulnerable code:</strong> <code>$payload = $request-&gt;json()-&gt;all(); $post-&gt;update(['is_admin' =&gt; $payload['is_admin']]);</code>
        <br><br>
        <strong>Impact:</strong> anyone who can reach the URL can promote any post to an official admin announcement.
        <br><br>
        <strong>Fix:</strong> <code>hash_equals(hash_hmac('sha256', $request-&gt;getContent(), $secret), (string) $request-&gt;header('X-Lab-Signature'))</code>
        before touching the model, and reject on mismatch.
    </div>

    <h3 style="color: #f0f6fc; margin-bottom: 10px;">Forged payload</h3>
    <div class="output">POST /webhook/receive
Content-Type: application/json
X-Lab-Signature: whatever-you-like

{"post_id": 2, "is_admin": true}</div>

    <p style="color: #8b949e; font-size: 12px;">
        The signature header is never read. In this lab the CSRF token is what stops the request, not the missing
        verification — which is exactly the point: the only control standing between a forged payload and the database
        is one that a real webhook route would not have.
    </p>
@endsection
