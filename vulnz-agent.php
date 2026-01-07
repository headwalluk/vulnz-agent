<?php
/**
 * Plugin Name:       Vulnz Agent
 * Plugin URI:        https://github.com/headwalluk/vulnz-agent
 * Description:       A companion WordPress plugin for the Vulnz project that syncs site data with the Vulnz API.
 * Version:           2.2.1
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

declare(strict_types=1);

// Block direct access.
defined( 'ABSPATH' ) || die();

// Define the plugin version here for easy bumping during releases.
define( 'Vulnz_Agent\PLUGIN_VERSION', '2.2.1' );
define( 'Vulnz_Agent\PLUGIN_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'constants.php';

require_once Vulnz_Agent\PLUGIN_DIR . 'functions.php';
require_once Vulnz_Agent\PLUGIN_DIR . 'functions-public.php';
require_once Vulnz_Agent\PLUGIN_DIR . 'includes/class-plugin.php';
require_once Vulnz_Agent\PLUGIN_DIR . 'includes/class-admin-hooks.php';
require_once Vulnz_Agent\PLUGIN_DIR . 'includes/class-api-client.php';

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
