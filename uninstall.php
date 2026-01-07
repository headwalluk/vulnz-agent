<?php
/**
 * Uninstall script for Vulnz Agent plugin.
 *
 * Fired when the plugin is uninstalled via WordPress admin.
 * Cleans up all options and transients created by the plugin.
 *
 * @package Vulnz_Agent
 */

declare(strict_types=1);

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all plugin options.
delete_option( 'wp_vulnz_enabled' );
delete_option( 'wp_vulnz_api_url' );
delete_option( 'wp_vulnz_api_key' );
delete_option( 'wp_vulnz_last_cron_run' );

// Clear any scheduled cron events.
wp_clear_scheduled_hook( 'vulnz_agent' );

// Delete all website cache transients.
// Pattern: wp_vulnz_website_*.
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wp_vulnz_website_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wp_vulnz_website_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_wp_vulnz_website_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
