<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VulnController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    // =========================================================================
    // 1. SQL Injection
    // =========================================================================

    public function sqli(Request $request): View
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

    public function xss(Request $request): View
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

    public function authPage(): View
    {
        return view('auth-bypass');
    }

    public function authLogin(Request $request): RedirectResponse
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

    public function idor(): View
    {
        $users = User::select('id', 'name')->get();

        return view('idor', compact('users'));
    }

    public function idorProfile(int $id): View
    {
        // VULNERABILITY: IDOR — no authorization check. Any visitor can view
        // any user's profile by changing the ID in the URL.
        $user = User::findOrFail($id);

        return view('idor-profile', compact('user'));
    }

    // =========================================================================
    // 5. Command Injection
    // =========================================================================

    public function cmdi(Request $request): View
    {
        $output = '';
        $host = $request->input('host', '');

        if ($host !== '') {
            // VULNERABILITY: Command Injection — user input passed directly to shell_exec.
            // An attacker can append "; cat /etc/passwd" or "| ls -la" to the input.
            $output = shell_exec('ping -c 1 '.$host);
        }

        return view('cmdi', compact('output', 'host'));
    }

    // =========================================================================
    // 6. Mass Assignment
    // =========================================================================

    public function massAssignment(): View
    {
        $users = User::select('id', 'name', 'email', 'is_admin')->get();

        return view('mass-assignment', compact('users'));
    }

    public function massAssignmentCreate(Request $request): RedirectResponse
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

    public function debug(): View
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

    public function admin(): View
    {
        // VULNERABILITY: Broken Access Control — this route has no auth middleware.
        // The middleware was commented out in routes/web.php.
        $users = User::all();

        return view('admin', compact('users'));
    }

    // =========================================================================
    // 9. Server-Side Request Forgery (SSRF)
    // =========================================================================

    public function ssrf(Request $request): Response
    {
        // VULNERABILITY: SSRF — a user-controlled URL is fetched server-side with
        // no host allowlist, so an attacker can reach internal services or the
        // cloud metadata endpoint (http://169.254.169.254/...).
        // Should validate against an allowlist before fetching.
        $url = $request->input('url');

        $response = Http::get($url);

        return response($response->body());
    }

    // =========================================================================
    // 10. Sensitive Data Exposure (API)
    // =========================================================================

    public function apiProfile(int $id): JsonResponse
    {
        // VULNERABILITY: Sensitive Data Exposure — the password hash, remember
        // token, and API secret are serialized straight into the JSON response.
        // Secret fields should never leave the server.
        $user = User::findOrFail($id);

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'password_hash' => $user->password,
            'remember_token' => $user->remember_token,
        ]);
    }

    // =========================================================================
    // 11. Open Redirect
    // =========================================================================

    public function redirectTo(Request $request): RedirectResponse
    {
        // VULNERABILITY: Open Redirect — the destination comes straight from the
        // request with no domain validation, enabling phishing redirects.
        // Should validate the target against an allowlist of internal routes.
        return redirect($request->input('next'));
    }

    // =========================================================================
    // 13. Dynamic Column Injection
    // =========================================================================

    public function sort(Request $request): View
    {
        // VULNERABILITY: Dynamic Column Injection — the ORDER BY column is taken
        // straight from the query string with no allow-list. Query bindings
        // protect VALUES, never IDENTIFIERS, so the column name is stitched into
        // the SQL as text: ?sort=password orders the listing by password hash,
        // turning the page into an oracle that leaks their relative order, and
        // ?sort=is_admin&dir=desc floats every admin account to the top.
        // Should validate first: in_array($sort, ['id', 'name', 'email'], true)
        //
        // The DIRECTION is not part of the attack surface. Laravel's query
        // builder checks it against asc/desc and throws InvalidArgumentException
        // on anything else, so no trailing SQL survives that argument. It is
        // normalised here only so a junk `dir` cannot turn this teaching page
        // into a 500 — the column, above, is the flaw this fixture is about.
        $sort = (string) $request->input('sort', 'id');
        $dir = strtolower((string) $request->input('dir', 'asc'));
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'asc';

        $users = User::orderBy($sort, $dir)->get();

        return view('sort', [
            'users' => $users,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    // =========================================================================
    // 14. CORS Misconfiguration
    // =========================================================================

    public function cors(): View
    {
        // VULNERABILITY: CORS Misconfiguration — the flaw is in the middleware
        // attached to this route in routes/web.php. App\Http\Middleware\ReflectOriginCors
        // echoes the inbound Origin header back into Access-Control-Allow-Origin and
        // pairs it with Access-Control-Allow-Credentials: true.
        return view('cors');
    }

    // =========================================================================
    // 15. Insecure Cookie Configuration
    // =========================================================================

    public function insecureCookie(): Response
    {
        // VULNERABILITY: Insecure Cookie Configuration — this session-style token is
        // issued with secure=false and httpOnly=false, so it is sent over plain HTTP
        // and any injected script can read it with document.cookie. Chained with the
        // stored XSS on /xss that is a one-request session hijack.
        // Should be: ->cookie('lab_session', $token, 120, '/', null, true, true)
        //
        // Two things about the header the browser actually receives:
        //   * The value is plaintext only because bootstrap/app.php lists
        //     'lab_session' in encryptCookies(except: [...]). Laravel encrypts
        //     response cookies by default, which would otherwise hand the browser
        //     ciphertext and make the document.cookie lesson untrue.
        //   * SameSite=Lax IS present. CookieJar stamps it from
        //     config('session.same_site') onto every cookie regardless of this
        //     call. It does not help: SameSite decides when a cookie is ATTACHED
        //     to cross-site requests (a CSRF control), not whether same-origin
        //     script can READ it. Only HttpOnly does that.
        $token = bin2hex(random_bytes(16));

        return response()
            ->view('insecure-cookie', ['token' => $token])
            ->cookie('lab_session', $token, 120, '/', null, false, false);
    }

    // =========================================================================
    // 17. Debug Mode Exposure
    // =========================================================================

    public function forceDebug(): View
    {
        // VULNERABILITY: Debug Mode Exposure — the flaw is in the middleware attached
        // to this route in routes/web.php. App\Http\Middleware\ForceDebugMode turns
        // app.debug on at runtime, so any exception raised afterwards renders a full
        // stack trace plus the resolved config and environment to the browser.
        return view('force-debug', ['debug' => config('app.debug')]);
    }
}
// hack-auditor test
