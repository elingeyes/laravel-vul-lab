<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VulnController extends Controller
{
    public function home(): \Illuminate\Contracts\View\View
    {
        return view('home');
    }

    // =========================================================================
    // 1. SQL Injection
    // =========================================================================

    public function sqli(Request $request): \Illuminate\Contracts\View\View
    {
        $results = [];
        $query = $request->input('q', '');

        if ($query !== '') {
            // VULNERABILITY: SQL Injection — raw user input interpolated into query.
            // Should use parameter binding: DB::select('SELECT * FROM users WHERE name = ?', [$query])
            $results = DB::select("SELECT id, name, email FROM users WHERE name = '$query'");
        }

        return view('sqli', compact('results', 'query'));
    }

    // =========================================================================
    // 2. Cross-Site Scripting (XSS)
    // =========================================================================

    public function xss(Request $request): \Illuminate\Contracts\View\View
    {
        if ($request->isMethod('post')) {
            DB::table('comments')->insert([
                'author' => $request->input('author', 'Anonymous'),
                'body' => $request->input('body'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $comments = DB::table('comments')->orderByDesc('id')->get();

        return view('xss', compact('comments'));
    }

    // =========================================================================
    // 3. Broken Authentication
    // =========================================================================

    public function authPage(): \Illuminate\Contracts\View\View
    {
        return view('auth-bypass');
    }

    public function authLogin(Request $request): \Illuminate\Http\RedirectResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // VULNERABILITY: Broken Authentication — hardcoded admin bypass.
        // Any password works if username is "admin".
        if ($username === 'admin') {
            Auth::loginUsingId(1);

            return redirect('/admin')->with('message', 'Logged in as admin (bypassed).');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    // =========================================================================
    // 4. Insecure Direct Object Reference (IDOR)
    // =========================================================================

    public function idor(): \Illuminate\Contracts\View\View
    {
        $users = User::select('id', 'name')->get();

        return view('idor', compact('users'));
    }

    public function idorProfile(int $id): \Illuminate\Contracts\View\View
    {
        // VULNERABILITY: IDOR — no authorization check. Any visitor can view
        // any user's profile by changing the ID in the URL.
        $user = User::findOrFail($id);

        return view('idor-profile', compact('user'));
    }

    // =========================================================================
    // 5. Command Injection
    // =========================================================================

    public function cmdi(Request $request): \Illuminate\Contracts\View\View
    {
        $output = '';
        $host = $request->input('host', '');

        if ($host !== '') {
            // VULNERABILITY: Command Injection — user input passed directly to shell_exec.
            // An attacker can append "; cat /etc/passwd" or "| ls -la" to the input.
            $output = shell_exec('ping -c 1 ' . $host);
        }

        return view('cmdi', compact('output', 'host'));
    }

    // =========================================================================
    // 6. Mass Assignment
    // =========================================================================

    public function massAssignment(): \Illuminate\Contracts\View\View
    {
        $users = User::select('id', 'name', 'email', 'is_admin')->get();

        return view('mass-assignment', compact('users'));
    }

    public function massAssignmentCreate(Request $request): \Illuminate\Http\RedirectResponse
    {
        // VULNERABILITY: Mass Assignment — $request->all() passed directly to create().
        // The User model has $guarded = [], so any field is writable.
        // An attacker can add "is_admin=1" to the request body.
        User::create($request->all());

        return back()->with('message', 'User created.');
    }

    // =========================================================================
    // 7. Sensitive Data Exposure
    // =========================================================================

    public function debug(): \Illuminate\Contracts\View\View
    {
        // VULNERABILITY: Sensitive Data Exposure — dumps all config values
        // including database credentials, API keys, and app secrets.
        $config = config()->all();
        $env = $_ENV;

        return view('debug', compact('config', 'env'));
    }

    public function phpinfo(): void
    {
        // VULNERABILITY: Sensitive Data Exposure — phpinfo() in production.
        phpinfo();
    }

    // =========================================================================
    // 8. Broken Access Control
    // =========================================================================

    public function admin(): \Illuminate\Contracts\View\View
    {
        // VULNERABILITY: Broken Access Control — this route has no auth middleware.
        // The middleware was commented out in routes/web.php.
        $users = User::all();

        return view('admin', compact('users'));
    }
}
