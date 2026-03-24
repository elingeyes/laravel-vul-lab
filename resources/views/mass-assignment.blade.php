@extends('layout')
@section('title', 'Mass Assignment')

@section('content')
    <h2>Mass Assignment</h2>
    <p class="subtitle">A04:2021 — Insecure Design</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> <code>User::create($request->all())</code> is used with <code>$guarded = []</code> on the model.
        Any field — including <code>is_admin</code> — can be set by adding hidden form fields or crafting a POST request.
        <br><br>
        <strong>Try it:</strong> Create a user, then use browser dev tools or curl to add <code>is_admin=1</code> to the request.
        <br><br>
        <strong>Curl example:</strong> <code>curl -X POST /mass-assignment -d "name=hacker&email=h@h.com&password=x&is_admin=1"</code>
    </div>

    <form method="POST" action="/mass-assignment">
        @csrf
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        {{-- The is_admin field is not in the form, but an attacker can add it --}}
        <button type="submit">Create User</button>
    </form>

    <h3 style="color: #f0f6fc; margin: 20px 0 10px;">All Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Admin?</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td style="color: {{ $user->is_admin ? '#f85149' : '#3fb950' }};">
                        {{ $user->is_admin ? 'YES' : 'No' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
