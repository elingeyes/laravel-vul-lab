@extends('layout')
@section('title', 'XSS')

@section('content')
    <h2>Cross-Site Scripting (XSS)</h2>
    <p class="subtitle">A03:2021 — Injection (Stored XSS)</p>

    <div class="vuln-banner">
        <strong>Vulnerability:</strong> Comments are rendered with <code>&#123;!! $body !!&#125;</code> (unescaped) instead of <code>&#123;&#123; $body &#125;&#125;</code>.
        Any HTML or JavaScript in the comment body will execute in every visitor's browser.
        <br><br>
        <strong>Try it:</strong> Post a comment with <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> or <code>&lt;img src=x onerror=alert(document.cookie)&gt;</code>
    </div>

    <form method="POST" action="/xss">
        @csrf
        <input type="text" name="author" placeholder="Your name">
        <textarea name="body" placeholder="Write a comment... try some HTML/JS"></textarea>
        <button type="submit">Post Comment</button>
    </form>

    <h3 style="color: #f0f6fc; margin: 20px 0 10px;">Comments</h3>

    @foreach($comments as $comment)
        <div class="comment">
            <div class="meta">{{ $comment->author }} &middot; {{ $comment->created_at }}</div>
            {{-- VULNERABILITY: XSS — unescaped output renders raw HTML/JS from user input --}}
            <div>{!! $comment->body !!}</div>
        </div>
    @endforeach

    @if($comments->isEmpty())
        <p style="color: #8b949e;">No comments yet.</p>
    @endif
@endsection
