@extends('layout')
@section('title', 'IDOR')

@section('content')
    <h2>Insecure Direct Object Reference (IDOR)</h2>
    <p class="subtitle">A01:2021 — Broken Access Control</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> The <code>/profile/{id}</code> route returns any user's full data based on the URL parameter.
        There is no authorization check — no Gate, Policy, or ownership validation.
        <br><br>
        <strong>Try it:</strong> Click any user below, then change the ID in the URL to view other users' data.
    </div>

    <h3 style="color: #f0f6fc; margin-bottom: 10px;">Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Profile</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td><a href="/profile/{{ $user->id }}">/profile/{{ $user->id }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
