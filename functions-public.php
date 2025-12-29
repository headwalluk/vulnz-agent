<?php
/**
 * Public functions for interacting with the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

// Block direct access.
defined( 'ABSPATH' ) || die();

/**
 * Get the plugin instance.
 *
 * @return \Vulnz_Agent\Plugin The plugin instance.
 */
function vulnz_agent_get_instance() {
	global $vulnz_agent_plugin;
	return $vulnz_agent_plugin;
}
