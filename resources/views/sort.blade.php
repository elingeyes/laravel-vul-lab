@extends('layout')
@section('title', 'Dynamic Column Injection')

@section('content')
    <h2>Dynamic Column Injection</h2>
    <p class="subtitle">A03:2021 — Injection</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> The <code>ORDER BY</code> column comes straight from the query string with no allow-list.
        Query bindings protect <em>values</em>, never identifiers — a column name is interpolated into the SQL as text.
        <br><br>
        <strong>Vulnerable code:</strong> <code>User::orderBy($request-&gt;input('sort'), $dir)-&gt;get()</code>
        <br><br>
        <strong>Try it:</strong> <code>?sort=password</code> orders the table by password hash — an oracle that leaks their order.
        Then try <code>?sort=is_admin&amp;dir=desc</code> to float every admin account to the top.
        <br><br>
        <strong>Not the flaw:</strong> the <em>direction</em> is safe. Laravel's query builder validates it against
        <code>asc</code>/<code>desc</code> and throws <code>InvalidArgumentException</code> on anything else, so
        <code>?dir=asc--</code> injects nothing. Only the identifier is attacker-controlled — that asymmetry is the whole lesson.
    </div>

    <form method="GET" action="/sort">
        <input type="text" name="sort" placeholder="Column to sort by (try: password)" value="{{ $sort }}">
        <select name="dir">
            <option value="asc" @selected($dir === 'asc')>asc</option>
            <option value="desc" @selected($dir === 'desc')>desc</option>
        </select>
        <button type="submit" class="danger">Sort</button>
    </form>

    <p style="color: #8b949e; font-size: 12px; margin-bottom: 5px;">
        Executing: <code style="color: #f85149;">select * from users order by {{ $sort }} {{ $dir }}</code>
    </p>

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
