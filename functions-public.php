<?php
/**
 * Public functions for interacting with the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

declare(strict_types=1);

// Block direct access.
defined( 'ABSPATH' ) || die();

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 *
 * @return \Vulnz_Agent\Plugin The plugin instance.
 */
function vulnz_agent_get_instance() {
	global $vulnz_agent_plugin;
	return $vulnz_agent_plugin;
}
