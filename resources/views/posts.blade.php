@extends('layout')
@section('title', 'Policy Not Applied')

@section('content')
    <h2>Broken Access Control — Policy Never Applied</h2>
    <p class="subtitle">A01:2021 — Broken Access Control</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> <code>App\Policies\PostPolicy</code> defines <code>update()</code> and <code>delete()</code> abilities,
        but <code>PostController</code> never calls <code>$this-&gt;authorize()</code>, never calls <code>$this-&gt;authorizeResource()</code>,
        and the routes carry no <code>can:</code> middleware. Declaring a policy does not enforce it.
        <br><br>
        <strong>Vulnerable code:</strong> <code>$post = Post::findOrFail($id); $post-&gt;update($request-&gt;all());</code>
        <br><br>
        <strong>Try it:</strong> Edit or delete a post you do not own — every button below works for every row, signed in or not.
        <br><br>
        <strong>Bonus:</strong> <code>Post::$fillable</code> contains <code>user_id</code> and <code>is_admin</code>, so adding
        <code>is_admin=1</code> or <code>user_id=1</code> to the edit request also steals or promotes the post.
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Owner</th>
                <th>Title</th>
                <th>Admin?</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>user #{{ $post->user_id }}</td>
                    <td>{{ $post->title }}</td>
                    <td style="color: {{ $post->is_admin ? '#f85149' : '#3fb950' }};">
                        {{ $post->is_admin ? 'YES' : 'No' }}
                    </td>
                    <td>
                        <form method="POST" action="/posts/{{ $post->id }}" style="display: inline; margin: 0;">
                            @csrf
                            <input type="hidden" name="title" value="{{ $post->title }} (edited by anyone)">
                            {{-- user_id and is_admin are not in this form, but an attacker can add them --}}
                            <button type="submit">Edit</button>
                        </form>
                        <form method="POST" action="/posts/{{ $post->id }}/delete" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
