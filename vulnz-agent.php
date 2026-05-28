<?php
/**
 * Plugin Name:       Vulnz Agent
 * Plugin URI:        https://github.com/headwalluk/vulnz-agent
 * Description:       A companion WordPress plugin for the Vulnz project that syncs site data with the Vulnz API.
 * Version:           2.4.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Paul Faulkner
 * Author URI:        https://headwall-hosting.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vulnz-agent
 * Domain Path:       /languages
 *
 * @package Vulnz_Agent
 */

// Block direct access.
defined( 'ABSPATH' ) || die();

// Plugin-level constants (global scope, prefixed).
define( 'VULNZ_AGENT_PLUGIN_VERSION', '2.4.0' );
define( 'VULNZ_AGENT_PLUGIN_FILE', __FILE__ );
define( 'VULNZ_AGENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VULNZ_AGENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once VULNZ_AGENT_PLUGIN_DIR . 'constants.php';

require_once VULNZ_AGENT_PLUGIN_DIR . 'functions-private.php';
require_once VULNZ_AGENT_PLUGIN_DIR . 'functions.php';
require_once VULNZ_AGENT_PLUGIN_DIR . 'includes/class-plugin.php';
require_once VULNZ_AGENT_PLUGIN_DIR . 'includes/class-admin-hooks.php';
require_once VULNZ_AGENT_PLUGIN_DIR . 'includes/class-api-client.php';
require_once VULNZ_AGENT_PLUGIN_DIR . 'includes/class-github-updater.php';

// GitHub-based auto-updates.
new \Vulnz_Agent\Github_Updater();

// Register activation/deactivation hooks for scheduling the hourly task.
\register_activation_hook( __FILE__, array( '\Vulnz_Agent\Plugin', 'activate' ) );
\register_deactivation_hook( __FILE__, array( '\Vulnz_Agent\Plugin', 'deactivate' ) );

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 */
function vulnz_agent_run() {
	global $vulnz_agent_plugin;
	$vulnz_agent_plugin = new \Vulnz_Agent\Plugin();
}

/**
 * Main entry point.
 */
vulnz_agent_run();
