# Vulnz Agent

A WordPress plugin that integrates with the [Vulnz](https://vulnz.net) vulnerability management platform.

## Overview

Vulnz Agent is a companion WordPress plugin for the Vulnz SaaS platform. It syncs your WordPress site's plugin inventory to the Vulnz API, enabling vulnerability tracking and security monitoring for your WordPress installations.

**SaaS Platform:** [https://vulnz.net](https://vulnz.net)  
**API Endpoint:** `https://api.vulnz.net/api`  
**Self-Hosted Option:** Technical users can host their own instance using [github.com/headwalluk/vulnz](https://github.com/headwalluk/vulnz)
**Latest Download:** https://github.com/headwalluk/vulnz-agent/releases/latest/download/vulnz-agent.zip

## Features

- **Automated Inventory Sync:** Hourly background task syncs installed plugin data to Vulnz API
- **On-Demand Sync:** Manual "Sync Now" button for immediate updates
- **Vulnerability Dashboard:** View plugins with known vulnerabilities in WordPress admin
- **Configurable API Settings:** Set custom API URL and authentication key
- **Sortable Plugin List:** Plugins sorted by vulnerability status, then alphabetically by title

## Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 8.3+ (8.0 minimum, 8.4 preferred)
- **Vulnz API Key:** Required for sync functionality

## Installation

### As a Regular Plugin

1. Clone or download this repository:
   ```bash
   cd wp-content/plugins/
   git clone git@github.com:headwalluk/vulnz-agent.git
   ```

2. Activate the plugin in WordPress Admin → Plugins

3. Configure settings at **Vulnz Agent → Settings**:
   - Enable the connection
   - Set API URL (default: `https://api.vulnz.net` - base URL without `/api` path)
   - Add your API Key

### As a Must-Use Plugin

For hosting providers or managed WordPress installations:

1. Copy the plugin to `wp-content/mu-plugins/vulnz-agent/`:
   ```bash
   mkdir -p wp-content/mu-plugins/vulnz-agent
   cp -r vulnz-agent/* wp-content/mu-plugins/vulnz-agent/
   ```

2. Create a loader file at `wp-content/mu-plugins/vulnz-agent-loader.php`:
   ```php
   <?php
   /**
    * Plugin Name: Vulnz Agent (MU)
    * Description: Must-use plugin loader for Vulnz Agent
    */
   require_once WPMU_PLUGIN_DIR . '/vulnz-agent/vulnz-agent.php';
   ```

3. Configure via WordPress Admin as above, or pre-configure with constants:
   ```php
   // In wp-config.php
   define('VULNZ_AGENT_API_URL', 'https://api.vulnz.net');
   define('VULNZ_AGENT_API_KEY', 'your-api-key-here');
   define('VULNZ_AGENT_ENABLED', true);
   ```

## Usage

### Admin Interface

Navigate to **Vulnz Agent** in the WordPress admin menu:

- **Summary:** View your site's plugin inventory with vulnerability status
- **Settings:** Configure API connection and enable/disable syncing

### Sync Behavior

- **Automatic:** Runs hourly via WP-Cron when enabled
- **Manual:** Click "Sync Now" on the Summary page
- **Data Sent:** Site metadata (title, URL, WP version) and plugin list (slug, version)

### Data Privacy

The plugin sends only:
- Site title and URL
- WordPress version
- Admin login URL
- SSL status
- Plugin slugs and versions

**No personal data, user information, or site content is transmitted.**

## Architecture

### Namespace

All code uses the `Vulnz_Agent` namespace.

### Public Functions

- `vulnz_agent_get_instance()` - Returns the main plugin instance

### Constants

Key constants defined in `constants.php`:
- `PLUGIN_SORT_ORDER` - Sort plugins by 'title' (default) or 'slug'
- `IS_VULNZ_ENABLED` - Database option name for enabled status
- `VULNZ_API_URL` - Database option name for API URL
- `VULNZ_API_KEY` - Database option name for API key

### Hooks & Actions

- `vulnz_agent` - Hourly cron schedule
- `vulnz_agent_sync_now` - AJAX action for manual sync
- `vulnz_agent_hourly_event` - Cron hook for background sync

## Development

### File Structure

```
vulnz-agent/
├── vulnz-agent.php         # Main plugin file
├── constants.php            # Plugin constants
├── functions.php            # Internal functions
├── functions-public.php     # Public API functions
├── includes/
│   ├── class-plugin.php     # Main plugin class
│   ├── class-admin-hooks.php # Admin UI hooks
│   └── class-api-client.php  # API communication
├── admin-views/
│   ├── settings.php         # Settings page template
│   └── vulnz-overview.php   # Summary page template
└── assets/
    ├── admin.css            # Admin styles
    └── admin.js             # Admin JavaScript
```

### Version History

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

## Migrating from wp-vulnz (1.x)

Version 2.0.0 represents a complete rebrand from `wp-vulnz` to `vulnz-agent`:

- **Plugin folder:** `wp-vulnz/` → `vulnz-agent/`
- **Namespace:** `WP_Vulnz` → `Vulnz_Agent`
- **Functions:** `wp_vulnz_*()` → `vulnz_agent_*()`
- **Menu slugs:** `wp-vulnz-*` → `vulnz-agent-*`

**Database compatibility:** Option names remain unchanged (`wp_vulnz_*`) for backward compatibility. Existing installations will retain their settings.

## Support

For issues, feature requests, or questions:
- **GitHub:** [https://github.com/headwalluk/vulnz-agent](https://github.com/headwalluk/vulnz-agent)
- **Vulnz Platform:** [https://vulnz.net](https://vulnz.net)

## License

GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

**Paul Faulkner** - [Headwall Hosting](https://headwall-hosting.com/)
