<?php
/**
 * Summary page for the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

// Block direct access.
defined( 'ABSPATH' ) || die();

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template file variables are local scope.

// Verify user has permission to access this page.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'vulnz-agent' ) );
}

$api_client = Vulnz_Agent\get_plugin()->get_api_client();
$site_url   = \site_url();
$our_domain = \wp_parse_url( $site_url, PHP_URL_HOST );

$website_data = $api_client->get_website( $our_domain );
$last_sync    = \get_option( Vulnz_Agent\LAST_CRON_RUN, '' );

echo '<div class="wrap">';
printf( '<h1>%s</h1>', esc_html( get_admin_page_title() ) );

// Action bar: Sync button, last synced, and dashboard links.
echo '<p>';
printf(
	'<button id="vulnz-agent-sync-now" type="button" class="button button-primary" data-label="%s" data-syncing="%s">%s</button> ',
	esc_attr__( 'Sync Now', 'vulnz-agent' ),
	esc_attr__( 'Syncing...', 'vulnz-agent' ),
	esc_html__( 'Sync Now', 'vulnz-agent' )
);

if ( ! empty( $last_sync ) ) {
	printf(
		'<span class="description">%s %s</span> ',
		esc_html__( 'Last synced:', 'vulnz-agent' ),
		esc_html( $last_sync )
	);
}
echo '</p>';

// Dashboard links.
echo '<p>';
printf(
	'<a href="%s" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-external" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span> %s</a>',
	esc_url( Vulnz_Agent\VULNZ_DASHBOARD_URL . '/' ),
	esc_html__( 'Vulnz Dashboard', 'vulnz-agent' )
);
echo ' &nbsp;|&nbsp; ';
printf(
	'<a href="%s" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-external" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span> %s</a>',
	esc_url( Vulnz_Agent\VULNZ_DASHBOARD_URL . '/' . $our_domain . '/' ),
	/* translators: %s: the site's domain name */
	sprintf( esc_html__( 'View %s on Vulnz', 'vulnz-agent' ), esc_html( $our_domain ) )
);
echo '</p>';

if ( empty( $website_data ) ) {
	echo '<p>' . esc_html__( 'No data available for this website. Please click "Sync Now" to retrieve data from the API.', 'vulnz-agent' ) . '</p>';
} elseif ( ! array_key_exists( 'wordpress-plugins', $website_data ) ) {
	echo '<p>' . esc_html__( 'No plugin data available for this website.', 'vulnz-agent' ) . '</p>';
} else {
	printf( '<h2>%s</h2>', esc_html__( 'Installed Plugins', 'vulnz-agent' ) );
	echo '<table class="wp-list-table widefat fixed striped">';
	echo '<thead>';
	echo '<tr>';
	printf( '<th scope="col">%s</th>', esc_html__( 'Plugin', 'vulnz-agent' ) );
	printf( '<th scope="col">%s</th>', esc_html__( 'Version', 'vulnz-agent' ) );
	printf( '<th scope="col">%s</th>', esc_html__( 'Vulnerabilities', 'vulnz-agent' ) );
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach ( $website_data['wordpress-plugins'] as $this_plugin ) {
		echo '<tr>';
		printf( '<td>%s</td>', esc_html( $this_plugin['title'] ) );
		printf( '<td>%s</td>', esc_html( $this_plugin['version'] ) );
		echo '<td>';
		if ( ! empty( $this_plugin['vulnerabilities'] ) ) {
			$index = 1;
			foreach ( $this_plugin['vulnerabilities'] as $vulnerability ) {
				// Extract hostname from URL.
				$parsed_url = wp_parse_url( $vulnerability );
				$hostname   = isset( $parsed_url['host'] ) ? $parsed_url['host'] : $vulnerability;
				// Remove 'www.' prefix if present.
				$hostname = preg_replace( '/^www\./', '', $hostname );

				printf(
					'<a href="%s" target="_blank" rel="noopener noreferrer" style="display: block; margin-bottom: 4px;"><span class="dashicons dashicons-external" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span> %d. %s</a>',
					esc_url( $vulnerability ),
					esc_html( (string) $index ),
					esc_html( $hostname )
				);
				++$index;
			}
		} else {
			printf( '<span style="color: #2c7e2e;">%s</span>', esc_html__( 'No known vulnerabilities', 'vulnz-agent' ) );
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>'; // .wp-list-table
}

echo '</div>'; // .wrap
