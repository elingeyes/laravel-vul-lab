<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VulnLab') — Laravel VulnLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; background: #0d1117; color: #c9d1d9; display: flex; min-height: 100vh; }
        a { color: #58a6ff; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Sidebar */
        .sidebar { width: 260px; background: #161b22; border-right: 1px solid #30363d; padding: 20px 0; flex-shrink: 0; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar h1 { font-size: 16px; color: #f85149; padding: 0 20px 15px; border-bottom: 1px solid #30363d; }
        .sidebar h1 span { color: #8b949e; font-weight: normal; font-size: 12px; display: block; margin-top: 4px; }
        .sidebar nav { padding: 10px 0; }
        .sidebar nav a { display: flex; align-items: center; gap: 10px; padding: 10px 20px; color: #c9d1d9; font-size: 13px; border-left: 3px solid transparent; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #1c2128; border-left-color: #f85149; text-decoration: none; color: #f0f6fc; }
        .sidebar nav a .badge { font-size: 10px; padding: 2px 6px; border-radius: 10px; font-weight: bold; }
        .badge-critical { background: #f85149; color: #fff; }
        .badge-high { background: #d29922; color: #fff; }
        .badge-medium { background: #388bfd; color: #fff; }
        .badge-low { background: #3fb950; color: #fff; }

        /* Main content */
        .main { margin-left: 260px; flex: 1; padding: 30px 40px; max-width: 900px; }
        .main h2 { font-size: 22px; color: #f0f6fc; margin-bottom: 6px; }
        .main .subtitle { color: #8b949e; font-size: 13px; margin-bottom: 25px; }

        /* Vuln banner */
        .vuln-banner { background: #1c1208; border: 1px solid #d29922; border-radius: 6px; padding: 14px 18px; margin-bottom: 25px; font-size: 13px; }
        .vuln-banner strong { color: #d29922; }
        .vuln-banner code { background: #0d1117; padding: 2px 6px; border-radius: 3px; font-size: 12px; }

        /* Forms */
        form { margin-bottom: 20px; }
        input[type="text"], input[type="password"], textarea { background: #0d1117; border: 1px solid #30363d; color: #c9d1d9; padding: 10px 14px; border-radius: 6px; width: 100%; font-family: inherit; font-size: 14px; margin-bottom: 10px; }
        textarea { min-height: 80px; resize: vertical; }
        input:focus, textarea:focus { outline: none; border-color: #58a6ff; }
        button { background: #238636; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-family: inherit; font-size: 14px; }
        button:hover { background: #2ea043; }
        button.danger { background: #f85149; }
        button.danger:hover { background: #da3633; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 10px 14px; border-bottom: 1px solid #21262d; font-size: 13px; }
        th { color: #8b949e; font-weight: 600; background: #161b22; }
        tr:hover { background: #161b22; }

        /* Code / output blocks */
        .output { background: #161b22; border: 1px solid #30363d; border-radius: 6px; padding: 16px; font-size: 13px; white-space: pre-wrap; word-break: break-all; margin: 15px 0; max-height: 400px; overflow-y: auto; }

        /* Flash messages */
        .flash { padding: 10px 16px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; }
        .flash-success { background: #0d2818; border: 1px solid #238636; color: #3fb950; }
        .flash-error { background: #2d1214; border: 1px solid #f85149; color: #f85149; }

        /* Comment cards */
        .comment { background: #161b22; border: 1px solid #30363d; border-radius: 6px; padding: 14px; margin-bottom: 10px; }
        .comment .meta { color: #8b949e; font-size: 11px; margin-bottom: 6px; }

        /* Config dump */
        .config-dump { max-height: 500px; overflow-y: auto; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h1>
            VulnLab
            <span>Intentionally Vulnerable Laravel App</span>
        </h1>
        <nav>
            <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="/sqli" class="{{ request()->is('sqli') ? 'active' : '' }}">SQL Injection <span class="badge badge-critical">CRIT</span></a>
            <a href="/xss" class="{{ request()->is('xss') ? 'active' : '' }}">XSS (Stored) <span class="badge badge-critical">CRIT</span></a>
            <a href="/auth" class="{{ request()->is('auth') ? 'active' : '' }}">Broken Auth <span class="badge badge-critical">CRIT</span></a>
            <a href="/idor" class="{{ request()->is('idor*') || request()->is('profile*') ? 'active' : '' }}">IDOR <span class="badge badge-high">HIGH</span></a>
            <a href="/cmdi" class="{{ request()->is('cmdi') ? 'active' : '' }}">Command Injection <span class="badge badge-critical">CRIT</span></a>
            <a href="/mass-assignment" class="{{ request()->is('mass-assignment') ? 'active' : '' }}">Mass Assignment <span class="badge badge-high">HIGH</span></a>
            <a href="/debug" class="{{ request()->is('debug') ? 'active' : '' }}">Data Exposure <span class="badge badge-high">HIGH</span></a>
            <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">Broken Access Ctrl <span class="badge badge-high">HIGH</span></a>
        </nav>
    </aside>

    <main class="main">
        @if(session('message'))
            <div class="flash flash-success">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
