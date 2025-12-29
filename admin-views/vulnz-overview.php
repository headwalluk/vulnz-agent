<?php
/**
 * Summary page for the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

// Block direct access.
defined( 'ABSPATH' ) || die();

$api_client = Vulnz_Agent\get_plugin()->get_api_client();
$site_url   = \site_url();
$our_domain = \wp_parse_url( $site_url, PHP_URL_HOST );

$website_data = $api_client->get_website( $our_domain );

echo '<div class="wrap">';
printf( '<h1>%s</h1>', esc_html( get_admin_page_title() ) );
printf( '<button id="vulnz-agent-sync-now" class="button button-primary">%s</button>', esc_html__( 'Sync Now', 'vulnz-agent' ) );


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
			echo '<ul>';
			foreach ( $this_plugin['vulnerabilities'] as $vulnerability ) {
				printf( '<li><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>', esc_url( $vulnerability ), esc_html( $vulnerability ) );
			}
			echo '</ul>';
		} else {
			printf( '<p>%s</p>', esc_html__( 'No known vulnerabilities', 'vulnz-agent' ) );
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>'; // .wp-list-table
}

echo '</div>'; // .wrap
