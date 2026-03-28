# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Vulnz Agent is a WordPress plugin that syncs installed WordPress plugins to the [Vulnz](https://vulnz.net) vulnerability management SaaS API for security monitoring. It runs hourly via WP-Cron and supports manual sync via AJAX.

- **Repo:** [github.com/headwalluk/vulnz-agent](https://github.com/headwalluk/vulnz-agent)
- **SaaS platform:** [vulnz.net](https://vulnz.net)
- **Self-hosted option:** [github.com/headwalluk/vulnz](https://github.com/headwalluk/vulnz)

## Commands

```bash
phpcs                  # Check WordPress coding standards
phpcbf                 # Auto-fix fixable violations
phpcs includes/        # Check specific directory
```

PHPCS config: `phpcs.xml`. WordPress Coding Standards with short array syntax `[]` allowed. Global prefixes: `vulnz_agent` / `Vulnz_Agent`.

### Release Process

1. Bump version in `vulnz-agent.php` (plugin header + `PLUGIN_VERSION` constant) and `readme.txt`
2. Tag: `git tag v2.x.x` and push
3. GitHub Actions (`.github/workflows/release.yml`) builds ZIP and publishes to Releases

### Pre-Commit Checklist

```bash
phpcs && phpcbf && phpcs   # Check → auto-fix → verify
```

Commit message format: `type: brief description` — types: `feat:` `fix:` `chore:` `refactor:` `docs:` `style:` `test:`

## Architecture

### Bootstrap Chain

`vulnz-agent.php` → includes `constants.php`, `functions.php`, `functions-public.php` → `vulnz_agent_run()` → instantiates `Vulnz_Agent\Plugin`.

### Core Classes (`includes/`)

- **`Plugin`** — Orchestrator. Registers WP hooks (cron, AJAX, admin menu, settings). Lazy-loads `Admin_Hooks` and `Api_Client`. Contains sync logic (`sync_website_with_vulnz()`).
- **`Admin_Hooks`** — Admin UI: asset enqueuing, page rendering (delegates to `admin-views/` templates), admin notices, settings link.
- **`Api_Client`** — Vulnz API communication. GET/POST/PUT to `/api/websites/{domain}`. Enforces HTTPS, blocks SSRF. Caches website data as transients (1-minute TTL).

### Function Files

- `functions.php` — Internal helpers: `get_option_or_constant()` (wp-config.php constants take precedence over DB options), sanitizers, plugin instance accessor.
- `functions-public.php` — Public API: `vulnz_agent_get_instance()`.
- `constants.php` — All option keys (`wp_vulnz_*`), schedule names, defaults, admin UI constants.

### Configuration

Options set via WP admin (stored as `wp_vulnz_*` in `wp_options`) or `wp-config.php` constants (`VULNZ_AGENT_ENABLED`, `VULNZ_AGENT_API_URL`, `VULNZ_AGENT_API_KEY`). Constants take precedence.

### Data Flow

1. WP-Cron (hourly) or "Sync Now" AJAX → `sync_website_with_vulnz()`
2. Collects: site title, URL, WP version, SSL status, installed plugins (slug + version)
3. `Api_Client` checks if website exists (GET), then creates (POST) or updates (PUT)

## Coding Standards

See `.github/copilot-instructions.md` for full guidelines and `dev-notes/patterns/` for implementation patterns.

### Key Rules

- **PHP 8.0+ minimum**, 8.3+ recommended, 8.4 preferred
- **Do NOT use `declare(strict_types=1)`** — WordPress/WooCommerce don't use it; causes type errors with hook interop
- **Namespace:** `Vulnz_Agent` for all classes
- **Text domain:** `vulnz-agent`
- **SESE pattern:** Functions should have a single return statement at the end (single-entry single-exit)
- **No inline HTML:** Templates must use `printf()`/`echo`, not mixed HTML/PHP snippets
- **Boolean options:** Use `filter_var($val, FILTER_VALIDATE_BOOLEAN)`, not string comparison
- **Date/time storage:** Human-readable `Y-m-d H:i:s T` format, not Unix timestamps
- **Constants:** All magic strings/numbers in `constants.php`. Prefix: `DEF_` for defaults, `OPT_` for option keys
- **Class organization:** Properties (public → protected → private), `__construct()`, methods (public → protected → private)
- Short array syntax `[]` is allowed
- Class files named `class-{name}.php`

### Security

- Sanitize all input, escape all output (`esc_html()`, `esc_url()`, `esc_attr()`)
- Verify nonces and check capabilities on all AJAX/form handlers
- API client enforces HTTPS, blocks SSRF (localhost/private IPs)
- API keys masked in settings UI, sanitized to alphanumeric only
- Error logging gated behind `WP_DEBUG`

## Development References

- `dev-notes/00-project-tracker.md` — Current milestones and progress
- `dev-notes/patterns/` — Implementation patterns (caching, database, settings API, templates, JS, admin tabs, WooCommerce)
- `dev-notes/workflows/code-standards.md` — PHPCS setup and usage
- `dev-notes/workflows/commit-to-git.md` — Git commit workflow
