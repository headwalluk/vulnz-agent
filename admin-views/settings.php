<?php
/**
 * Settings page for the Vulnz Agent plugin.
 *
 * @package Vulnz_Agent
 */

// Block direct access.
defined( 'ABSPATH' ) || die();

echo '<div class="wrap">';
printf( '<h1>%s</h1>', esc_html( get_admin_page_title() ) );

printf( '<p>%s</p>', esc_html__( 'You can get your API key by logging in to your Vulnz account and generating a new key in the dashboard.', 'vulnz-agent' ) );

printf( '<p>Default API URL: %s</p>', esc_url( 'https://api.vulnz.net/api' ) );

printf( '<form action="%s" method="post">', esc_url( admin_url( 'options.php' ) ) );
\settings_fields( Vulnz_Agent\SETTINGS_GROUP );
\do_settings_sections( 'vulnz-agent' );

echo '<table class="form-table">';

echo '<tr valign="top">';
printf( '<th scope="row">%s</th>', esc_html__( 'Enable Connection to Vulnz', 'vulnz-agent' ) );
printf( '<td><input type="checkbox" name="%s" value="1" %s /></td>', esc_attr( Vulnz_Agent\IS_VULNZ_ENABLED ), checked( 1, get_option( Vulnz_Agent\IS_VULNZ_ENABLED ), false ) );
echo '</tr>';

echo '<tr valign="top">';
printf( '<th scope="row">%s</th>', esc_html__( 'API URL', 'vulnz-agent' ) );
printf( '<td><input type="text" name="%s" value="%s" size="50" /></td>', esc_attr( Vulnz_Agent\VULNZ_API_URL ), esc_attr( get_option( Vulnz_Agent\VULNZ_API_URL ) ) );
echo '</tr>'; // .form-table row.

echo '<tr valign="top">';
printf( '<th scope="row">%s</th>', esc_html__( 'API Key', 'vulnz-agent' ) );
printf( '<td><input type="password" name="%s" value="%s" size="50" autocomplete="off" /></td>', esc_attr( Vulnz_Agent\VULNZ_API_KEY ), esc_attr( get_option( Vulnz_Agent\VULNZ_API_KEY ) ) );
echo '</tr>'; // .form-table row.

echo '</table>'; // .form-table.

\submit_button();

echo '</form>'; // options.php.
echo '</div>'; // .wrap.
