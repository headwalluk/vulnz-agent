# Changelog

All notable changes to Vulnz Agent will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-12-29

### Breaking Changes

- **Renamed plugin** from `wp-vulnz` to `vulnz-agent`
- **Changed namespace** from `WP_Vulnz` to `Vulnz_Agent`
- **Renamed global functions:**
  - `wp_vulnz_run()` → `vulnz_agent_run()`
  - `wp_vulnz_get_instance()` → `vulnz_agent_get_instance()`
- **Updated global variable** from `$wp_vulnz_plugin` to `$vulnz_agent_plugin`
- **Changed admin menu slugs:**
  - `wp-vulnz-summary` → `vulnz-agent-summary`
  - `wp-vulnz-settings` → `vulnz-agent-settings`
- **Updated text domain** from `wp-vulnz` to `vulnz-agent`
- **Renamed hooks and actions:**
  - Schedule: `wp_vulnz` → `vulnz_agent`
  - AJAX action: `wp_vulnz_sync_now` → `vulnz_agent_sync_now`
  - Cron hook: `wp_vulnz_hourly_event` → `vulnz_agent_hourly_event`
- **Updated asset handles:**
  - CSS: `wp-vulnz-admin` → `vulnz-agent-admin`
  - JS: `wp-vulnz-admin` → `vulnz-agent-admin`
  - JS object: `wp_vulnz` → `vulnz_agent`
- **Raised minimum PHP version** to 8.3 (8.0 minimum, 8.4 preferred)

### Added

- Plugin list sorting functionality with configurable sort order
- `PLUGIN_SORT_ORDER` constant for choosing 'title' or 'slug' sorting
- Multi-level sorting: vulnerable plugins first, then alphabetically
- Constants for all WordPress option names (eliminates magic strings)
- GitHub README.md for developer documentation
- Comprehensive CHANGELOG.md
- Must-use plugin installation instructions

### Changed

- Plugin URI updated to GitHub repository
- Improved code organization with namespace consistency
- All `@package` tags updated to `Vulnz_Agent`
- Admin UI labels updated to "Vulnz Agent"
- Plugin description improved for clarity

### Fixed

- Plugin sorting now uses case-insensitive comparison
- Proper closing of `usort()` function in API client

### Maintained

- **Database compatibility:** Option names remain `wp_vulnz_*` for backward compatibility
- Existing installations will retain all settings
- No data migration required

## [1.0.2] - Previous Release

### Added

- Settings link on wp-admin/plugins.php page

### Changed

- Moved PLUGIN_VERSION constant for proper version updates
- General code tidying

## [1.0.0] - Previous Release

### Added

- Admin Summary page showing plugin inventory
- Admin Settings page for API configuration
- Configurable API URL and API Key
- Hourly background sync via WP-Cron
- Manual "Sync Now" button for immediate sync
- Vulnerability status display for installed plugins

### Changed

- First stable release with comprehensive documentation

## [0.1.0] - Initial Release

### Added

- Initial plugin structure
- Basic API client functionality
- WordPress admin integration
- Cron-based background syncing

---

[2.0.0]: https://github.com/headwalluk/vulnz-agent/compare/v1.0.2...v2.0.0
[1.0.2]: https://github.com/headwalluk/vulnz-agent/compare/v1.0.0...v1.0.2
[1.0.0]: https://github.com/headwalluk/vulnz-agent/compare/v0.1.0...v1.0.0
[0.1.0]: https://github.com/headwalluk/vulnz-agent/releases/tag/v0.1.0
