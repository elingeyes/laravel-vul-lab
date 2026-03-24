@extends('layout')
@section('title', 'Admin Dashboard')

@section('content')
    <h2>Admin Dashboard</h2>
    <p class="subtitle">A01:2021 — Broken Access Control</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> This admin route has no authentication middleware.
        The <code>->middleware('auth')</code> call is commented out in <code>routes/web.php</code>.
        Any unauthenticated visitor can access this page.
        <br><br>
        <strong>Vulnerable code in routes/web.php:</strong>
        <div class="output" style="margin-top: 8px; margin-bottom: 0;">Route::get('/admin', [VulnController::class, 'admin'])
    // ->middleware('auth')    &lt;-- commented out!
    ->name('admin');</div>
    </div>

    <h3 style="color: #f0f6fc; margin-bottom: 10px;">All Users (admin-only data)</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td style="color: {{ $user->is_admin ? '#f85149' : '#8b949e' }};">
                        {{ $user->is_admin ? 'ADMIN' : 'user' }}
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
