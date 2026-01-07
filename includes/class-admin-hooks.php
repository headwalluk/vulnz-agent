<?php
/**
 * Admin hooks for the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

declare(strict_types=1);

namespace Vulnz_Agent;

use Error;

// Block direct access.
defined( 'ABSPATH' ) || die();

/**
 * Class Admin_Hooks
 *
 * @since 1.0.0
 */
class Admin_Hooks {

	/**
	 * Enqueue admin assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_assets( string $hook ): void {
		if ( ADMIN_PAGE_HOOK_SUMMARY !== $hook ) {
			return;
		}

		\wp_enqueue_style( 'vulnz-agent-admin', PLUGIN_URL . 'assets/admin.css', array(), PLUGIN_VERSION );

		\wp_enqueue_script( 'vulnz-agent-admin', PLUGIN_URL . 'assets/admin.js', array( 'jquery' ), PLUGIN_VERSION, true );

		\wp_localize_script(
			'vulnz-agent-admin',
			'vulnz_agent',
			array(
				'nonce' => \wp_create_nonce( SYNC_NOW_ACTION_NONCE ),
			)
		);
	}

	/**
	 * Render the plugin summary page.
	 *
	 * @since 1.0.0
	 */
	public function render_summary_page() {
		include_once PLUGIN_DIR . 'admin-views/vulnz-overview.php';
	}

	/**
	 * Render the plugin settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		include_once PLUGIN_DIR . 'admin-views/settings.php';
	}

	/**
	 * Display an admin notice if the plugin is not enabled.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice() {
		if ( ! (bool) filter_var( get_option_or_constant( IS_VULNZ_ENABLED, 'VULNZ_AGENT_ENABLED', false ), FILTER_VALIDATE_BOOLEAN ) ) {
			printf(
				'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'Vulnz Agent API is not enabled. ', 'vulnz-agent' ),
				esc_url( get_our_settings_url() ),
				esc_html__( 'Click here to enable', 'vulnz-agent' )
			);
		}
	}

	/**
	 * Add a settings link to the plugin's entry in the plugins list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links An array of plugin action links.
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			get_our_settings_url(),
			\__( 'Settings', 'vulnz-agent' )
		);
		\array_unshift( $links, $settings_link );
		return $links;
	}
}
