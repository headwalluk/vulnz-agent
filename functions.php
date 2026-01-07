<?php
/**
 * Private/Plugin-scoped functions.
 *
 * @package Vulnz_Agent
 */

declare(strict_types=1);

namespace Vulnz_Agent;

// Block direct access.
defined( 'ABSPATH' ) || die();

/**
 * Get the main plugin instance.
 *
 * @since 1.0.0
 *
 * @return Plugin The plugin instance.
 */
function get_plugin(): Plugin {
	global $vulnz_agent_plugin;
	return $vulnz_agent_plugin;
}

/**
 * Get an option value with wp-config.php constant override support.
 *
 * Allows configuration via constants in wp-config.php, which is useful for
 * mu-plugins installations where database options aren't easily configurable.
 *
 * @since 1.0.0
 *
 * @param string $option_name The option name.
 * @param string $constant_name The constant name to check for override.
 * @param mixed  $default The default value if neither option nor constant exists.
 *
 * @return mixed The option value, constant value, or default.
 */
function get_option_or_constant( string $option_name, string $constant_name, $default = false ) {
	// Check if constant is defined in the current namespace first.
	$namespaced_constant = __NAMESPACE__ . '\\' . $constant_name;
	if ( defined( $namespaced_constant ) ) {
		return constant( $namespaced_constant );
	}

	// Check if constant is defined in global namespace (wp-config.php).
	if ( defined( $constant_name ) ) {
		return constant( $constant_name );
	}

	// Fall back to database option.
	return \get_option( $option_name, $default );
}


/**
 * Get the API client instance.
 *
 * @since 1.0.0
 *
 * @return Api_Client The API client instance.
 */
function get_api_client(): Api_Client {
	global $vulnz_agent_plugin;
	return $vulnz_agent_plugin->get_api_client();
}

/**
 * Sanitize the API key.
 *
 * @since 1.0.0
 *
 * @param string $api_key The API key to sanitize.
 *
 * @return string The sanitized API key.
 */
function sanitize_api_key( string $api_key ): string {
	$sanitised = preg_replace( '/[^a-zA-Z0-9]/', '', $api_key );
	if ( ! is_string( $sanitised ) ) {
		$sanitised = '';
	}

	return $sanitised;
}

/**
 * Sanitize the API key field, handling masked input.
 *
 * @since 1.0.0
 *
 * @param string $api_key The API key input value.
 *
 * @return string The sanitized API key or existing key if input was masked.
 */
function sanitize_api_key_field( string $api_key ): string {
	// If the input matches our dummy key, keep existing key (don't update).
	if ( DUMMY_API_KEY === $api_key ) {
		return get_option( VULNZ_API_KEY, '' );
	}

	// Otherwise sanitize the new key.
	return sanitize_api_key( $api_key );
}


/**
 * Get the URL to our settings page.
 *
 * @since 1.0.0
 *
 * @return string The settings page URL.
 */
function get_our_settings_url(): string {
	return \admin_url( 'admin.php?page=vulnz-agent-settings' );
}


/**
 * Get the cache key for storing website data.
 *
 * @since 1.0.0
 *
 * @param string $domain The domain name.
 *
 * @return string|null The cache key, or null if the domain is empty.
 */
function get_website_cache_key( string $domain ): ?string {
	$cache_key = null;

	if ( ! empty( $domain ) ) {
		$cache_key = WEBSITE_CACHE_KEY_PREFIX . md5( $domain );
	}

	return $cache_key;
}


/**
 * Get the list of installed plugins with their slugs and versions.
 *
 * @since 1.0.0
 *
 * @return array List of installed plugins with 'slug' and 'version' keys.
 */
function get_installed_plugins(): array {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = array();
	foreach ( \get_plugins() as $plugin_file => $plugin_data ) {
		$slug = \dirname( $plugin_file );
		if ( '.' === $slug ) {
			$slug = \basename( $plugin_file, '.php' );
		}
		$plugins[] = array(
			'slug'    => $slug,
			'version' => $plugin_data['Version'],
		);
	}

	return $plugins;
}
