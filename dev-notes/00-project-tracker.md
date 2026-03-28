# Project Tracker

**Version:** 2.3.0
**Last Updated:** 2026-03-28
**Current Phase:** Complete
**Overall Progress:** 100%

---

## Overview

Tidy up the Vulnz Agent codebase: align with current WordPress coding standards and copilot-instructions guidelines, run plugin checks, restructure documentation into a `docs/` directory, and slim down the README.

---

## Active TODO Items

(None — all milestones complete)

---

## Milestones

### M1 — Code Audit & Standards Alignment (DONE)

- [x] Remove `declare(strict_types=1)` from all PHP files (per current WP guidance)
- [x] Verify no type-dependent logic breaks after strict_types removal
- [x] Enforce SESE pattern (single return at end of functions)
- [x] Audit inline HTML in `admin-views/` templates — already using `printf()`/`echo` style
- [x] Verify boolean option handling uses `filter_var( $val, FILTER_VALIDATE_BOOLEAN )`
- [x] Fix date/time storage to use human-readable `Y-m-d H:i:s T` format
- [x] Run `phpcbf` then `phpcs` — clean (fixed `$default` → `$fallback` parameter name)
- [x] Run `wp plugin check vulnz-agent` — fixed `Tested up to`, restored `Domain Path` with `languages/` dir

### M2 — Documentation Restructure (DONE)

- [x] Create `docs/` directory
- [x] Write `docs/installation.md` (regular plugin + mu-plugin deployment)
- [x] Write `docs/configuration.md` (admin UI settings + wp-config.php constants)
- [x] Write `docs/api-integration.md` (sync behaviour, data transmitted, privacy)
- [x] Slim down `README.md` to lean project summary with links to `docs/`
- [x] Remove "Migrating from wp-vulnz (1.x)" section from README
- [x] Update `readme.txt` — condensed 2.0.0 changelog, removed upgrade notice

### M2.5 — GitHub Auto-Updater (DONE)

- [x] Build portable `Headwall_GitHub_Plugin_Updater` class with `class_exists` guard
- [x] 12-hour transient caching of GitHub release data
- [x] `headwall_github_updater_enabled` filter for conditional disable
- [x] Integrate into Vulnz Agent bootstrap
- [x] Write `docs/github-auto-updates.md`

### M2.6 — API & UX Improvements (DONE)

- [x] Send `versions` object to API: `wordpress_version`, `php_version`, `db_server_type`, `db_server_version`
- [x] Move WP version from `meta` to `versions` (was ignored by backend)
- [x] Show last synced timestamp on Summary page
- [x] Add Vulnz dashboard and site-specific detail links to Summary page
- [x] Sync Now button shows "Syncing..." during AJAX call; success reloads without alert

### M3 — Final Polish (DONE)

- [x] Final `phpcs` + `wp plugin check` pass — clean (updater warnings expected, dev files excluded from ZIP)
- [x] Update CHANGELOG.md — added 2.3.0 entry with all changes
- [x] Update `readme.txt` — added 2.3.0 changelog, updated data transmitted sections
- [x] Update `docs/api-integration.md` — added PHP and database version fields
- [x] Version bump — 2.2.1 → 2.3.0 in plugin header, constant, and readme.txt

---

## Technical Debt

(None currently tracked)

---

## Notes for Development

- User prefers strict types personally but accepts current WP guidance to avoid it
- Option keys remain `wp_vulnz_*` (backwards compatibility from wp-vulnz 1.x rebrand) — this is intentional, not debt
- The `pwpl/` directory rule from copilot-instructions does not apply to this plugin (no licence controller)
- Translation files added for 8 locales (de, el, en_GB, es, fr, it, nl, pl) plus POT template
