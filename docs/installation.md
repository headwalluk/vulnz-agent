# Installation

## Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 8.0+ (8.3+ recommended, 8.4 preferred)
- **Vulnz API Key:** Required — sign up at [vulnz.net](https://vulnz.net)

## As a Regular Plugin

1. Download the [latest release](https://github.com/headwalluk/vulnz-agent/releases/latest/download/vulnz-agent.zip), or clone the repository:

   ```bash
   cd wp-content/plugins/
   git clone git@github.com:headwalluk/vulnz-agent.git
   ```

2. Activate the plugin in **WordPress Admin > Plugins**.

3. Configure settings at **Vulnz Agent > Settings** (see [Configuration](configuration.md)).

## As a Must-Use Plugin

For hosting providers or managed WordPress installations where the plugin should always be active:

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
    * Description: Must-use plugin loader for Vulnz Agent.
    */
   require_once WPMU_PLUGIN_DIR . '/vulnz-agent/vulnz-agent.php';
   ```

3. Configure via `wp-config.php` constants (recommended for mu-plugins):

   ```php
   define( 'VULNZ_AGENT_ENABLED', true );
   define( 'VULNZ_AGENT_API_URL', 'https://api.vulnz.net' );
   define( 'VULNZ_AGENT_API_KEY', 'your-api-key-here' );
   ```

   See [Configuration](configuration.md) for details on all configuration methods.

## Uninstall

When deleted through the WordPress admin, the plugin automatically cleans up:

- All database options (`wp_vulnz_*`)
- Scheduled cron events
- Cached transient data
