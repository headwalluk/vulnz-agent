# Vulnz Agent

[![Release](https://img.shields.io/github/v/release/headwalluk/vulnz-agent?label=release)](https://github.com/headwalluk/vulnz-agent/releases)
[![Download latest](https://img.shields.io/badge/download-latest%20zip-brightgreen?logo=github)](https://github.com/headwalluk/vulnz-agent/releases/latest/download/vulnz-agent.zip)
[![License](https://img.shields.io/badge/license-GPLv2%2B-blue)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777bb3?logo=php)](https://www.php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b?logo=wordpress)](https://wordpress.org/)

A WordPress plugin that syncs your site's plugin inventory to the [Vulnz](https://vulnz.net) vulnerability management platform for security monitoring.

## Features

- **Automated Sync** — hourly background sync of installed plugins via WP-Cron
- **On-Demand Sync** — manual "Sync Now" button for immediate updates
- **Vulnerability Dashboard** — view plugins with known vulnerabilities in the WordPress admin
- **Flexible Configuration** — admin UI settings or `wp-config.php` constants for mu-plugin deployments
- **Privacy-Focused** — only plugin metadata is transmitted; no personal data

## Quick Start

1. [Download the latest release](https://github.com/headwalluk/vulnz-agent/releases/latest/download/vulnz-agent.zip) and install in WordPress
2. Activate and go to **Vulnz Agent > Settings**
3. Enter your API key from [vulnz.net](https://vulnz.net) and enable the connection

## Documentation

- **[Installation](docs/installation.md)** — regular plugin and mu-plugin deployment
- **[Configuration](docs/configuration.md)** — admin settings and wp-config.php constants
- **[API Integration](docs/api-integration.md)** — sync behaviour, data transmitted, and security
- **[GitHub Auto-Updates](docs/github-auto-updates.md)** — how auto-updates work, caching, and how to disable

## External Service & Privacy

This plugin communicates with the Vulnz API at `https://api.vulnz.net` to sync your site's data for vulnerability monitoring. An API key from [vulnz.net](https://vulnz.net) is required.

**Data transmitted:** site title, domain, WordPress version, PHP version, database server type/version, SSL status, admin login URL, and installed plugins (slug and version only).

**Not transmitted:** no personal data, user accounts, site content, or credentials.

See [API Integration](docs/api-integration.md) for full details.

## Links

- **Vulnz Platform:** [vulnz.net](https://vulnz.net)
- **Self-Hosted:** [github.com/headwalluk/vulnz](https://github.com/headwalluk/vulnz)
- **Issues:** [github.com/headwalluk/vulnz-agent/issues](https://github.com/headwalluk/vulnz-agent/issues)
- **Changelog:** [CHANGELOG.md](CHANGELOG.md)

## License

GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

**Paul Faulkner** — [Headwall Hosting](https://headwall-hosting.com/)
