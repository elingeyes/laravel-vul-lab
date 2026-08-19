# Laravel VulnLab

![SAST](https://github.com/mahdi-salmanzade/laravel-vuln-lab/actions/workflows/sast.yml/badge.svg)

An intentionally vulnerable Laravel application for security testing and education. Think [DVWA](https://dvwa.co.uk/) but built on Laravel 13.

> **WARNING: This application is intentionally insecure. Never deploy it to a public server or production environment. Use it only for local security research, tool testing, and learning.**

## Quick start

```bash
git clone https://github.com/mahdi-salmanzade/laravel-vuln-lab.git
cd laravel-vuln-lab
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Open http://localhost:8000 — no Docker, no external database. SQLite is used by default.

**Seeded credentials:** `admin@lab.com` / `password123`

## Vulnerabilities

| # | Vulnerability | Route | OWASP | Severity | Detection |
|---|--------------|-------|-------|----------|-----------|
| 1 | SQL Injection | `/sqli` | A03:2021 | Critical | Psalm taint, SQLMap, Hack Auditor |
| 2 | Stored XSS | `/xss` | A03:2021 | Critical | Psalm taint, Burp Suite, Hack Auditor |
| 3 | Broken Authentication | `/auth` | A07:2021 | Critical | Manual review, Hack Auditor |
| 4 | IDOR | `/profile/{id}` | A01:2021 | High | Manual review, Hack Auditor |
| 5 | Command Injection | `/cmdi` | A03:2021 | Critical | Psalm taint, Hack Auditor |
| 6 | Mass Assignment | `/mass-assignment` | A04:2021 | High | Manual review, Hack Auditor |
| 7 | Sensitive Data Exposure | `/debug`, `/phpinfo` | A02:2021 | High | Manual review, Hack Auditor |
| 8 | Broken Access Control | `/admin` | A01:2021 | High | Manual review, Hack Auditor |
| 9 | SSRF | `/ssrf` | A10:2021 | High | Manual review, Hack Auditor |
| 10 | Sensitive Data Exposure (API) | `/api/profile/{id}` | A02:2021 | High | Manual review, Hack Auditor |
| 11 | Open Redirect | `/redirect` | A01:2021 | Medium | Manual review, Hack Auditor |
| 12 | Broken Access Control (Policy Not Applied) | `/posts` | A01:2021 | High | Manual review, Hack Auditor |
| 13 | Dynamic Column Injection | `/sort` | A03:2021 | High | Manual review, Hack Auditor |
| 14 | CORS Misconfiguration | `/cors` | A05:2021 | Medium | Manual review, Hack Auditor |
| 15 | Insecure Cookie Configuration | `/insecure-cookie` | A05:2021 | Medium | Manual review, Hack Auditor |
| 16 | Unverified Webhook Signature | `/webhook` | A08:2021 | High | Manual review, Hack Auditor |
| 17 | Debug Mode Exposure | `/force-debug` | A05:2021 | High | Manual review, Hack Auditor |

Each page includes a description of the vulnerability, the vulnerable code, and example payloads to test with.

`/webhook/receive` and `/cors` are demonstration fixtures, not working integrations. The webhook handler is wired to no provider and stays inside the `web` middleware group so CSRF still rejects outside POSTs; the origin-reflecting CORS middleware is attached to the `/cors` route alone and never registered globally.

## What each tool catches (and misses)

This is the real value of the repo — seeing which tools find which classes of vulnerability.

In the Hack Auditor column, :white_check_mark: means a **deterministic detector** covers the type — the finding is reproducible on every run. :robot: means the type is only reachable by the **AI pass**, which varies run to run. See the note under the table.

| Vulnerability | Psalm Taint | PHPStan | Security Checker | Pint | Hack Auditor | Manual |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| SQL Injection | :white_check_mark: | | | | :robot: | :white_check_mark: |
| Stored XSS | :white_check_mark: | | | | :robot: | :white_check_mark: |
| Command Injection | :white_check_mark: | | | | :robot: | :white_check_mark: |
| Broken Authentication | | | | | :robot: | :white_check_mark: |
| IDOR | | | | | :white_check_mark: | :white_check_mark: |
| Mass Assignment | | | | | :white_check_mark: | :white_check_mark: |
| Sensitive Data Exposure | | | | | :white_check_mark: | :white_check_mark: |
| Broken Access Control | | | | | :white_check_mark: | :white_check_mark: |
| SSRF | | | | | :white_check_mark: | :white_check_mark: |
| Open Redirect | | | | | :robot: | :white_check_mark: |
| Policy defined but never applied | | | | | :white_check_mark: | :white_check_mark: |
| Dynamic Column Injection | | | | | :robot: | :white_check_mark: |
| CORS Misconfiguration | | | | | :robot: | :white_check_mark: |
| Insecure Cookie Configuration | | | | | :robot: | :white_check_mark: |
| Unverified Webhook Signature | | | | | :robot: | :white_check_mark: |
| Debug Mode Exposure | | | | | :robot: | :white_check_mark: |
| Known CVEs in deps | | | :white_check_mark: | | | |

> **:robot: = AI pass only, not reproducible.** Hack Auditor ships exactly five deterministic detectors — `SensitiveFillableDetector` (mass assignment), `UnauthorizedModelFetchDetector` (IDOR), `PolicyRouteMismatchDetector` (policy defined but never applied), `SsrfDetector`, and `SensitiveDataExposureDetector`. Those five are pure static analysis: same input, same findings, every time, no API key involved. Every other row depends entirely on what the model notices on a given run, so a :robot: row may be reported on one scan and missed on the next. Treat those ticks as "the fixture is here and the AI has found it before", not as a coverage guarantee. This distinction is the honest version of the table — a tool that reports a type *sometimes* has not *covered* it.

**Key takeaway:** Psalm's taint analysis is excellent at catching data-flow vulnerabilities (SQLi, XSS, command injection) where untrusted input reaches a dangerous sink. But it fundamentally cannot catch logic flaws — broken auth, IDOR, missing middleware — because those aren't taint problems, they're authorization problems. PHPStan catches type errors but not security issues. Security Checker only looks at known CVEs in your `composer.lock`. Pint enforces code style, not security. AI-powered analysis (Hack Auditor) reaches both taint flows and logic flaws because it reasons about intent rather than data flow alone — but reach is not the same as reliability, which is why its column is split above. Its five deterministic detectors are the part you can put in a CI gate; the AI pass is the part that finds things nothing else will, on the runs where it finds them.

No single tool catches everything. That's the point. And a tool that catches something *most* of the time is a different tool from one that catches it *every* time — worth knowing which you are holding.

There is deliberately no fixture for Hack Auditor's `dependency_vulnerability` type. That check reads a dependency inventory, and the scanner only reads PHP source — it never opens `composer.lock` — so a vulnerable-package fixture could not be detected here no matter how it were written. Dependency CVEs are the Security Checker's row in the table above.

## Testing with security tools

### Laravel Hack Auditor (AI-powered)

```bash
composer require mahdisphp/laravel-hack-auditor:^2.0 --dev
php artisan hack:scan
```

### Psalm taint analysis

```bash
composer require --dev vimeo/psalm
vendor/bin/psalm --taint-analysis
```

### PHP Security Checker (dependency CVEs)

```bash
# Checks composer.lock for packages with known vulnerabilities
curl -sL https://github.com/fabpot/local-php-security-checker/releases/latest/download/local-php-security-checker_darwin_amd64 -o security-checker
chmod +x security-checker
./security-checker security:check composer.lock
```

### Laravel Pint (code style)

```bash
vendor/bin/pint --test
```

## CI/CD

Three GitHub Actions workflows are included:

- **`sast.yml`** — Runs PHPStan, Psalm taint analysis, PHP Security Checker (dependency CVEs), and Laravel Pint on every push and PR. Findings are uploaded as artifacts. Uses `continue-on-error: true` so the full report is always generated.
- **`deploy.yml`** — Full build pipeline: installs deps, runs migrations with seed, executes baseline test, posts a vulnerability summary.
- **`hack-auditor.yml`** — Runs AI-powered security scanning on pull requests via the [Laravel Hack Auditor action](https://github.com/mahdi-salmanzade/laravel-hack-auditor-action). It posts findings as a PR comment with a severity breakdown and inline annotations. Requires action **v2.0.0 or newer** (it consumes the v2 JSON contract).

  **What actually gets scanned:** the action always runs `hack:scan --json --diff`, so the AI pass sees the **PHP files changed in the pull request** against its base branch, intersected with the configured scan paths — `app/Http/Controllers`, `app/Models`, `app/Http/Requests`, `app/Http/Middleware`, `routes`. It is not a full-tree scan, and no `scan-path` input narrows it further. A PR that touches no PHP in those paths will legitimately report a clean score; that reflects the diff scope, not the security of this repo, which is vulnerable by design. To scan everything, run `php artisan hack:scan` locally without `--diff`.

  `app/Policies` is not in that list on purpose: it is not sent to the AI pass, it is read as context by the deterministic policy/route-mismatch detector, which is what catches "policy defined but never applied" reproducibly.

  `fail-on: none` keeps findings informational — every one of them here is intentional. `fail-on-error: 'true'` is set alongside it so a scan that fails to *run* (bad API key, unbootable app, non-JSON output) still fails the job instead of reporting green.

## Project structure

```
laravel-vuln-lab/
├── app/Http/Controllers/VulnController.php    # Most vulnerable endpoints
├── app/Http/Controllers/PostController.php    # Policy exists, is never applied
├── app/Http/Controllers/WebhookController.php # Unsigned webhook (fixture only)
├── app/Http/Middleware/ReflectOriginCors.php  # Reflects Origin + credentials
├── app/Http/Middleware/ForceDebugMode.php     # Forces app.debug on at runtime
├── app/Models/User.php                        # $guarded = [] (mass assignment)
├── app/Models/Post.php                        # $fillable with user_id + is_admin
├── app/Policies/PostPolicy.php                # Correct policy, never invoked
├── bootstrap/app.php                          # lab_session excluded from cookie encryption
├── routes/web.php                             # All routes, commented-out middleware
├── resources/views/                           # Blade templates for each vuln
├── database/seeders/VulnLabSeeder.php         # Admin + 5 fake users, comments, posts
├── .github/workflows/sast.yml                # SAST scanning pipeline
├── .github/workflows/deploy.yml              # CI/CD pipeline
└── .github/workflows/hack-auditor.yml        # AI security scan on PRs
```

## Requirements

- PHP 8.3+
- Composer
- SQLite (included with most PHP installations)

## License

MIT — for educational and security research purposes only.

---

**Disclaimer:** This software is provided for educational purposes only. The authors are not responsible for any misuse or damage caused by this application. Use responsibly and only in controlled, local environments.
