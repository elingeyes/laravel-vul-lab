@extends('layout')
@section('title', 'SQL Injection')

@section('content')
    <h2>SQL Injection</h2>
    <p class="subtitle">A03:2021 — Injection</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> User input is interpolated directly into a raw SQL query without parameter binding.
        <br><br>
        <strong>Vulnerable code:</strong> <code>DB::select("SELECT * FROM users WHERE name = '$input'")</code>
        <br><br>
        <strong>Try it:</strong> Enter <code>admin' OR '1'='1</code> to dump all users, or <code>admin' UNION SELECT 1,sql,3 FROM sqlite_master--</code> to extract the schema.
    </div>

    <form method="GET" action="/sqli">
        <input type="text" name="q" placeholder="Search users by name..." value="{{ $query }}">
        <button type="submit">Search</button>
    </form>

    @if($query !== '')
        <p style="color: #8b949e; font-size: 12px; margin-bottom: 10px;">
            Query: <code style="color: #f85149;">SELECT id, name, email FROM users WHERE name = '{{ $query }}'</code>
        </p>
    @endif

    @if(count($results) > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($query !== '')
        <p style="color: #8b949e;">No results found.</p>
    @endif
@endsection
