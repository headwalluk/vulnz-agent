<?php
/**
 * Settings page for the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

// Block direct access.
defined( 'ABSPATH' ) || die();

// Verify user has permission to access this page.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'vulnz-agent' ) );
}

echo '<div class="wrap">';
printf( '<h1>%s</h1>', esc_html( get_admin_page_title() ) );

printf( '<p>%s</p>', esc_html__( 'You can get your API key by logging in to your Vulnz account and generating a new key in the dashboard.', 'vulnz-agent' ) );

printf( '<p>Default API URL: %s</p>', esc_url( Vulnz_Agent\DEFAULT_VULNZ_API_URL ) );

// Check if settings are defined via constants.
$has_constant_config = defined( 'VULNZ_AGENT_ENABLED' ) || defined( 'VULNZ_AGENT_API_URL' ) || defined( 'VULNZ_AGENT_API_KEY' );
if ( $has_constant_config ) {
	echo '<div class="notice notice-info"><p>';
	printf(
		esc_html__( 'Note: Some settings are configured via constants in %s and cannot be changed here.', 'vulnz-agent' ),
		'<code>wp-config.php</code>'
	);
	echo '</p></div>';
}

printf( '<form action="%s" method="post">', esc_url( admin_url( 'options.php' ) ) );
\settings_fields( Vulnz_Agent\SETTINGS_GROUP );
\do_settings_sections( 'vulnz-agent' );

echo '<table class="form-table">';

echo '<tr valign="top">';
printf( '<th scope="row">%s</th>', esc_html__( 'Enable Connection to Vulnz', 'vulnz-agent' ) );
$is_enabled_via_constant = defined( 'VULNZ_AGENT_ENABLED' );
$enabled_value = Vulnz_Agent\get_option_or_constant( Vulnz_Agent\IS_VULNZ_ENABLED, 'VULNZ_AGENT_ENABLED', false );
printf(
	'<td><input type="checkbox" name="%s" value="1" %s %s />%s</td>',
	esc_attr( Vulnz_Agent\IS_VULNZ_ENABLED ),
	checked( 1, $enabled_value, false ),
	$is_enabled_via_constant ? 'disabled' : '',
	$is_enabled_via_constant ? ' <em>' . esc_html__( '(Set via wp-config.php)', 'vulnz-agent' ) . '</em>' : ''
);
echo '</tr>';

echo '<tr valign="top">';
printf( '<th scope="row">%s</th>', esc_html__( 'API URL', 'vulnz-agent' ) );
$is_url_via_constant = defined( 'VULNZ_AGENT_API_URL' );
$url_value = Vulnz_Agent\get_option_or_constant( Vulnz_Agent\VULNZ_API_URL, 'VULNZ_AGENT_API_URL', '' );
printf(
	'<td><input type="text" name="%s" value="%s" size="%d" %s />%s</td>',
	esc_attr( Vulnz_Agent\VULNZ_API_URL ),
	esc_attr( $url_value ),
	Vulnz_Agent\ADMIN_INPUT_FIELD_SIZE,
	$is_url_via_constant ? 'disabled' : '',
	$is_url_via_constant ? ' <em>' . esc_html__( '(Set via wp-config.php)', 'vulnz-agent' ) . '</em>' : ''
);
echo '</tr>'; // .form-table row.

echo '<tr valign="top">';
printf( '<th scope="row">%s</th>', esc_html__( 'API Key', 'vulnz-agent' ) );
$is_key_via_constant = defined( 'VULNZ_AGENT_API_KEY' );
if ( $is_key_via_constant ) {
	// Show that key is set via constant.
	printf(
		'<td><input type="password" value="%s" size="%d" disabled /> <em>%s</em></td>',
		esc_attr( str_repeat( '•', 32 ) ),
		Vulnz_Agent\ADMIN_INPUT_FIELD_SIZE,
		esc_html__( '(Set via wp-config.php)', 'vulnz-agent' )
	);
} else {
	// Normal masking behavior for database options.
	$existing_key = get_option( Vulnz_Agent\VULNZ_API_KEY );
	$display_value = ! empty( $existing_key ) ? str_repeat( '•', 32 ) : '';
	printf(
		'<td><input type="password" name="%s" value="%s" size="%d" autocomplete="off" placeholder="%s" /></td>',
		esc_attr( Vulnz_Agent\VULNZ_API_KEY ),
		esc_attr( $display_value ),
		Vulnz_Agent\ADMIN_INPUT_FIELD_SIZE,
		esc_attr__( 'Enter new API key to update', 'vulnz-agent' )
	);
}
echo '</tr>'; // .form-table row.

echo '</table>'; // .form-table.

\submit_button();

echo '</form>'; // options.php.
echo '</div>'; // .wrap.
