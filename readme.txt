=== Vulnz Agent ===
Contributors: headwalluk
Tags: security, vulnerabilities, api, monitoring, vulnz
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 2.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin companion for the Vulnz vulnerability management platform.

== Description ==

Vulnz Agent is a companion WordPress plugin for the [Vulnz](https://vulnz.net) SaaS platform. It automatically syncs your WordPress site's plugin inventory to the Vulnz API for vulnerability tracking and security monitoring.

**Website:** [https://vulnz.net](https://vulnz.net)  
**Self-Hosted Option:** Technical users can host their own Vulnz instance - see [github.com/headwalluk/vulnz](https://github.com/headwalluk/vulnz)

**Key Features:**

* Automated hourly sync of installed plugins to Vulnz API
* Manual "Sync Now" for immediate updates
* Admin dashboard showing plugins with known vulnerabilities
* Configurable API endpoint and authentication
* Privacy-focused: only sends plugin metadata, no personal data

**How It Works:**

The plugin registers a background task that runs hourly, sending your site's plugin list to the Vulnz API. The API checks each plugin against known vulnerability databases and returns security information. You can view this in your WordPress admin under Vulnz Agent → Summary.

**External Service:**

This plugin communicates with the Vulnz API at `https://api.vulnz.net`. You will need a Vulnz account and API key to use this plugin. Learn more at [https://vulnz.net](https://vulnz.net).

**Data Transmitted:**

* Site title and URL
* WordPress version
* SSL status
* Admin login URL
* List of installed plugins (slug and version only)

No personal data, user information, or site content is transmitted.

== Installation ==

1. Upload the `vulnz-agent` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Vulnz Agent → Settings
4. Enable the connection
5. Enter your API URL (default: `https://api.vulnz.net` - base URL)
6. Enter your API Key from your Vulnz account
7. Click "Sync Now" to test the connection

== Frequently Asked Questions ==

= What is Vulnz? =

Vulnz is a vulnerability management platform for WordPress sites. It tracks known security issues in WordPress plugins and helps you keep your sites secure. Visit [https://vulnz.net](https://vulnz.net) to learn more.

= Do I need a Vulnz account? =

Yes. You need to sign up at [https://vulnz.net](https://vulnz.net) and generate an API key to use this plugin.

= Does this plugin require an external service? =

Yes. The plugin sends data to the Vulnz API at `https://api.vulnz.net`. This is required for vulnerability checking.

= What data does the plugin send? =

The plugin sends:
* Your site's URL and title
* WordPress version
* Whether SSL is enabled
* Your admin login URL
* A list of installed plugins (name and version only)

No personal data, user credentials, or site content is transmitted.

= Can I disable the automatic syncing? =

Yes. Go to Vulnz Agent → Settings and uncheck "Enable Connection to Vulnz". This will stop the hourly background sync.

= How often does it sync? =

By default, the plugin syncs once per hour using WordPress's built-in cron system (WP-Cron). You can also trigger a manual sync using the "Sync Now" button.

= Is my data secure? =

Yes. The plugin requires HTTPS for API communication and validates your API key with each request. Your API key is stored securely in the WordPress database.

= Can I use this with multiple sites? =

Yes. Each site needs the plugin installed and configured with its own settings. You can manage multiple sites from your Vulnz account dashboard.

== Screenshots ==

1. Summary page showing installed plugins with vulnerability status
2. Settings page for API configuration

== Changelog ==

= 2.1.2 (2025-12-30) =
* Added: GitHub Actions release workflow publishes both a stable asset (`vulnz-agent.zip`) and a versioned asset for each release
* Docs: README updated with a stable latest download URL

= 2.1.1 (2025-12-29) =
* Fixed: get_option_or_constant() now properly checks namespaced constants
* Fixed: Settings page displays database values correctly
* Fixed: API key field no longer erased when saving other settings
* Fixed: API URL now uses base domain (https://api.vulnz.net) without /api path
* Fixed: API client strips trailing slashes from URL
* Changed: API key masking uses DUMMY_API_KEY constant instead of bullet points
* Changed: Vulnerability links show clean hostname with external icon
* Changed: Vulnerability display with numbered index and improved layout
* Changed: "No known vulnerabilities" shown in green text

= 2.1.0 (2025-12-29) =
* Added: wp-config.php constant support (VULNZ_AGENT_ENABLED, VULNZ_AGENT_API_URL, VULNZ_AGENT_API_KEY)
* Added: Settings page shows notice and disables fields when constants are defined
* Added: uninstall.php for proper cleanup when plugin is deleted
* Added: Admin UI configuration constants (input size, menu position, icon, page hook)
* Security: API key masking in settings form (shows bullets instead of plain text)
* Security: Enhanced capability checks in admin templates (defense-in-depth)
* Security: Production-safe error logging (only when WP_DEBUG enabled)
* Security: Sanitized error messages to prevent information leakage
* Changed: API endpoint migrated to api.vulnz.net
* Changed: Error log messages use clean [Vulnz Agent] prefix

= 2.0.0 (2025-12-29) =
**Major Update - Breaking Changes**

* **BREAKING:** Renamed plugin from "wp-vulnz" to "vulnz-agent"
* **BREAKING:** Changed namespace from `WP_Vulnz` to `Vulnz_Agent`
* **BREAKING:** Renamed functions: `wp_vulnz_*()` → `vulnz_agent_*()`
* **BREAKING:** Updated admin menu slugs: `wp-vulnz-*` → `vulnz-agent-*`
* **BREAKING:** Changed text domain from `wp-vulnz` to `vulnz-agent`
* **BREAKING:** Updated hook names and AJAX actions to use `vulnz_agent` prefix
* **BREAKING:** Raised minimum PHP version to 8.3 (8.0 supported, 8.4 preferred)
* Added: Plugin list sorting (configurable by title or slug)
* Added: Multi-level sort (vulnerabilities first, then alphabetically)
* Added: wp-config.php constant support (VULNZ_AGENT_ENABLED, VULNZ_AGENT_API_URL, VULNZ_AGENT_API_KEY)
* Added: Settings page shows when constants are defined and disables those fields
* Security: API key masking in settings form
* Security: Capability checks in admin templates (defense-in-depth)
* Security: Production-safe error logging (only when WP_DEBUG enabled)
* Improved: Code organization with constants for all option names
* Improved: Removed magic strings throughout codebase
* Changed: Updated Plugin URI to GitHub repository
* Changed: API endpoint migrated to api.vulnz.net
* Note: Database option names unchanged for backward compatibility

= 1.0.2 (Previous) =
* Tidying up bits-and-bobs in the codebase
* Added settings link from wp-admin/plugins.php page
* Moved PLUGIN_VERSION constant for proper updates

= 1.0.0 (Previous) =
* First stable release
* Admin Summary and Settings pages
* Configurable API URL and API Key
* Hourly background sync and on-demand "Sync Now"
* Documentation updates

= 0.1.0 (Previous) =
* Initial release

== Upgrade Notice ==

= 2.0.0 =
Major update with breaking changes. Plugin renamed from "wp-vulnz" to "vulnz-agent". If you have custom code integrating with this plugin, you'll need to update function names and hooks. Database settings are preserved.
