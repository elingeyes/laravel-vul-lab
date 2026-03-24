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
| 6 | Mass Assignment | `/mass-assignment` | A04:2021 | High | Enlightn, Hack Auditor |
| 7 | Sensitive Data Exposure | `/debug`, `/phpinfo` | A02:2021 | High | Enlightn, Hack Auditor |
| 8 | Broken Access Control | `/admin` | A01:2021 | High | Enlightn, Hack Auditor |

Each page includes a description of the vulnerability, the vulnerable code, and example payloads to test with.

## What each tool catches (and misses)

This is the real value of the repo — seeing which tools find which classes of vulnerability.

| Vulnerability | Psalm Taint | PHPStan | Enlightn | Hack Auditor | Manual |
|---|:---:|:---:|:---:|:---:|:---:|
| SQL Injection | :white_check_mark: | | | :white_check_mark: | :white_check_mark: |
| Stored XSS | :white_check_mark: | | | :white_check_mark: | :white_check_mark: |
| Command Injection | :white_check_mark: | | | :white_check_mark: | :white_check_mark: |
| Broken Authentication | | | | :white_check_mark: | :white_check_mark: |
| IDOR | | | | :white_check_mark: | :white_check_mark: |
| Mass Assignment | | | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| Sensitive Data Exposure | | | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| Broken Access Control | | | :white_check_mark: | :white_check_mark: | :white_check_mark: |

**Key takeaway:** Psalm's taint analysis is excellent at catching data-flow vulnerabilities (SQLi, XSS, command injection) where untrusted input reaches a dangerous sink. But it fundamentally cannot catch logic flaws — broken auth, IDOR, missing middleware — because those aren't taint problems, they're authorization problems. Enlightn catches Laravel-specific misconfigurations but misses injection flaws. AI-powered analysis (Hack Auditor) covers both categories because it reasons about intent, not just data flow.

No single tool catches everything. That's the point.

## Testing with security tools

### Laravel Hack Auditor (AI-powered)

```bash
composer require mahdisphp/laravel-hack-auditor --dev
php artisan hack:scan
```

### Psalm taint analysis

```bash
composer require --dev vimeo/psalm
vendor/bin/psalm --taint-analysis
```

### Enlightn (Laravel-specific)

```bash
composer require --dev enlightn/enlightn
php artisan enlightn
```

## CI/CD

Two GitHub Actions workflows are included:

- **`sast.yml`** — Runs PHPStan, Psalm taint analysis, and Enlightn on every push and PR. Findings are uploaded as artifacts. Uses `continue-on-error: true` so the full report is always generated.
- **`deploy.yml`** — Full build pipeline: installs deps, runs migrations with seed, executes baseline test, posts a vulnerability summary.

## Project structure

```
laravel-vuln-lab/
├── app/Http/Controllers/VulnController.php   # All vulnerable endpoints
├── app/Models/User.php                       # $guarded = [] (mass assignment)
├── routes/web.php                            # All routes, commented-out middleware
├── resources/views/                          # Blade templates for each vuln
├── database/seeders/VulnLabSeeder.php        # Admin + 5 fake users + comments
├── .github/workflows/sast.yml               # SAST scanning pipeline
└── .github/workflows/deploy.yml             # CI/CD pipeline
```

## Requirements

- PHP 8.3+
- Composer
- SQLite (included with most PHP installations)

## License

MIT — for educational and security research purposes only.

---

**Disclaimer:** This software is provided for educational purposes only. The authors are not responsible for any misuse or damage caused by this application. Use responsibly and only in controlled, local environments.
