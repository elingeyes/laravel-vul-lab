@extends('layout')
@section('title', 'Profile — ' . $user->name)

@section('content')
    <h2>User Profile</h2>
    <p class="subtitle">Viewing profile for user #{{ $user->id }}</p>

    <div class="vuln-banner">
        <strong>IDOR:</strong> This data is served without any authorization check.
        Change the number in the URL to access any user's profile.
    </div>

    <table>
        <tr><th style="width: 150px;">ID</th><td>{{ $user->id }}</td></tr>
        <tr><th>Name</th><td>{{ $user->name }}</td></tr>
        <tr><th>Email</th><td>{{ $user->email }}</td></tr>
        <tr><th>Admin</th><td>{{ $user->is_admin ? 'Yes' : 'No' }}</td></tr>
        <tr><th>Created</th><td>{{ $user->created_at }}</td></tr>
    </table>

    <p style="margin-top: 15px;"><a href="/idor">&larr; Back to users list</a></p>
@endsection
