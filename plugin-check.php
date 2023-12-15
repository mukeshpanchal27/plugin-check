<?php
/**
 * Plugin Name: Plugin Check
 * Plugin URI: https://github.com/WordPress/plugin-check
 * Description: Plugin Check is a WordPress.org tool which provides checks to help plugins meet the directory requirements and follow various best practices.
 * Requires at least: 6.3
 * Requires PHP: 7.0
 * Version: n.e.x.t
 * Author: WordPress Performance Team
 * Author URI: https://make.wordpress.org/performance/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: plugin-check
 *
 * @package plugin-check
 */

use WordPress\Plugin_Check\Plugin_Main;

define( 'WP_PLUGIN_CHECK_VERSION', 'n.e.x.t' );
define( 'WP_PLUGIN_CHECK_MINIMUM_PHP', '7.0' );
define( 'WP_PLUGIN_CHECK_MAIN_FILE', __FILE__ );
define( 'WP_PLUGIN_CHECK_PLUGIN_DIR_PATH', plugin_dir_path( WP_PLUGIN_CHECK_MAIN_FILE ) );
define( 'WP_PLUGIN_CHECK_PLUGIN_DIR_URL', plugin_dir_url( WP_PLUGIN_CHECK_MAIN_FILE ) );

/**
 * Checks basic requirements and loads the plugin.
 *
 * @since n.e.x.t
 */
function wp_plugin_check_load() {
	// Check for supported PHP version.
	if ( version_compare( phpversion(), WP_PLUGIN_CHECK_MINIMUM_PHP, '<' ) ) {
		add_action( 'admin_notices', 'wp_plugin_check_display_php_version_notice' );
		return;
	}

	// Check Composer autoloader exists.
	if ( ! file_exists( WP_PLUGIN_CHECK_PLUGIN_DIR_PATH . 'vendor/autoload.php' ) ) {
		add_action( 'admin_notices', 'wp_plugin_check_display_composer_autoload_notice' );
		return;
	}

	// Load the Composer autoloader.
	require_once WP_PLUGIN_CHECK_PLUGIN_DIR_PATH . 'vendor/autoload.php';

	// Setup the plugin.
	$instance = new Plugin_Main( WP_PLUGIN_CHECK_MAIN_FILE );
	$instance->add_hooks();
}

/**
 * Displays admin notice about unmet PHP version requirement.
 *
 * @since n.e.x.t
 */
function wp_plugin_check_display_php_version_notice() {
	echo '<div class="notice notice-error"><p>';
	printf(
		/* translators: 1: required version, 2: currently used version */
		esc_html__( 'Plugin Check requires at least PHP version %1$s. Your site is currently running on PHP %2$s.', 'plugin-check' ),
		esc_html( WP_PLUGIN_CHECK_MINIMUM_PHP ),
		esc_html( phpversion() )
	);
	echo '</p></div>';
}

/**
 * Displays admin notice about missing Composer autoload files.
 *
 * @since n.e.x.t
 */
function wp_plugin_check_display_composer_autoload_notice() {
	echo '<div class="notice notice-error"><p>';
	printf(
		/* translators: composer command. */
		esc_html__( 'Your installation of the Plugin Check plugin is incomplete. Please run %s.', 'plugin-check' ),
		'<code>composer install</code>'
	);
	echo '</p></div>';
}

wp_plugin_check_load();

/**
 * Callback function that print plugin activation script.
 *
 * @since n.e.x.t
 */
function perflab_print_plugin_activation_script() {
	$js = <<<JS
( function( $ ) {
	$( document ).ajaxComplete( function( event, xhr, settings ) {
		// Check if this is the 'install-plugin' request.
		if ( settings.data && typeof settings.data === 'string' && settings.data.includes( 'action=install-plugin' ) ) {
			var target_element = $( event.target.activeElement );
			if ( ! target_element ) {
				return;
			}
			/*
			 * WordPress core uses a 1s timeout for updating the activation link,
			 * so we set a 1.5 timeout here to ensure our changes get updated after
			 * the core changes have taken place.
			 */
			setTimeout( function() {
				var plugin_url = target_element.attr( 'href' );
				if ( ! plugin_url ) {
					return;
				}
				var nonce = target_element.attr( 'data-plugin-activation-nonce' );
				var plugin_slug = target_element.attr( 'data-slug' );
				var url = new URL( plugin_url );
				url.searchParams.set( 'action', 'perflab_activate_plugin' );
				url.searchParams.set( '_wpnonce', nonce );
				url.searchParams.set( 'plugin', plugin_slug );
				target_element.attr( 'href', url.href );
			}, 1500 );
		}
	} );
} )( jQuery );
JS;

	wp_print_inline_script_tag( $js );
}
