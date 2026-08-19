@extends('layout')
@section('title', 'Home')

@section('content')
    <h2>Laravel VulnLab</h2>
    <p class="subtitle">An intentionally vulnerable Laravel application for security testing and education.</p>

    <div class="vuln-banner" style="border-color: #f85149; background: #2d1214;">
        <strong style="color: #f85149;">WARNING:</strong> This application is intentionally insecure. Never deploy it to a public server or production environment. It exists solely for learning, security research, and testing security tools.
    </div>

    <h3 style="color: #f0f6fc; margin-bottom: 15px;">Vulnerabilities</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vulnerability</th>
                <th>Severity</th>
                <th>OWASP</th>
                <th>Page</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><a href="/sqli">SQL Injection</a></td>
                <td><span class="badge badge-critical">CRITICAL</span></td>
                <td>A03:2021</td>
                <td><code>/sqli</code></td>
            </tr>
            <tr>
                <td>2</td>
                <td><a href="/xss">Stored XSS</a></td>
                <td><span class="badge badge-critical">CRITICAL</span></td>
                <td>A03:2021</td>
                <td><code>/xss</code></td>
            </tr>
            <tr>
                <td>3</td>
                <td><a href="/auth">Broken Authentication</a></td>
                <td><span class="badge badge-critical">CRITICAL</span></td>
                <td>A07:2021</td>
                <td><code>/auth</code></td>
            </tr>
            <tr>
                <td>4</td>
                <td><a href="/idor">IDOR</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A01:2021</td>
                <td><code>/profile/{id}</code></td>
            </tr>
            <tr>
                <td>5</td>
                <td><a href="/cmdi">Command Injection</a></td>
                <td><span class="badge badge-critical">CRITICAL</span></td>
                <td>A03:2021</td>
                <td><code>/cmdi</code></td>
            </tr>
            <tr>
                <td>6</td>
                <td><a href="/mass-assignment">Mass Assignment</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A04:2021</td>
                <td><code>/mass-assignment</code></td>
            </tr>
            <tr>
                <td>7</td>
                <td><a href="/debug">Sensitive Data Exposure</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A02:2021</td>
                <td><code>/debug</code></td>
            </tr>
            <tr>
                <td>8</td>
                <td><a href="/admin">Broken Access Control</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A01:2021</td>
                <td><code>/admin</code></td>
            </tr>
            <tr>
                <td>9</td>
                <td><a href="/ssrf?url=https://example.com">SSRF</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A10:2021</td>
                <td><code>/ssrf?url=</code></td>
            </tr>
            <tr>
                <td>10</td>
                <td><a href="/api/profile/1">Sensitive Data Exposure (API)</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A02:2021</td>
                <td><code>/api/profile/{id}</code></td>
            </tr>
            <tr>
                <td>11</td>
                <td><a href="/redirect?next=/">Open Redirect</a></td>
                <td><span class="badge badge-medium">MEDIUM</span></td>
                <td>A01:2021</td>
                <td><code>/redirect?next=</code></td>
            </tr>
            <tr>
                <td>12</td>
                <td><a href="/posts">Broken Access Control (Policy Not Applied)</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A01:2021</td>
                <td><code>/posts</code></td>
            </tr>
            <tr>
                <td>13</td>
                <td><a href="/sort">Dynamic Column Injection</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A03:2021</td>
                <td><code>/sort</code></td>
            </tr>
            <tr>
                <td>14</td>
                <td><a href="/cors">CORS Misconfiguration</a></td>
                <td><span class="badge badge-medium">MEDIUM</span></td>
                <td>A05:2021</td>
                <td><code>/cors</code></td>
            </tr>
            <tr>
                <td>15</td>
                <td><a href="/insecure-cookie">Insecure Cookie Configuration</a></td>
                <td><span class="badge badge-medium">MEDIUM</span></td>
                <td>A05:2021</td>
                <td><code>/insecure-cookie</code></td>
            </tr>
            <tr>
                <td>16</td>
                <td><a href="/webhook">Unverified Webhook Signature</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A08:2021</td>
                <td><code>/webhook</code></td>
            </tr>
            <tr>
                <td>17</td>
                <td><a href="/force-debug">Debug Mode Exposure</a></td>
                <td><span class="badge badge-high">HIGH</span></td>
                <td>A05:2021</td>
                <td><code>/force-debug</code></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; color: #8b949e; font-size: 12px;">
        Built with Laravel {{ app()->version() }} &middot; PHP {{ PHP_VERSION }} &middot; SQLite
    </div>
@endsection
