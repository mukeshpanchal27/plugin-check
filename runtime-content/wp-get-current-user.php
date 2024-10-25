<?php
/**
 * Pluggable function override for `wp_get_current_user()`.
 *
 * @package plugin-check
 */

/**
 * Gets the current user.
 *
 * Pluggable function override specifically for early execution, to avoid trying to determine the current user too
 * early when it is not possible yet.
 *
 * @return WP_User The current user.
 */
function wp_get_current_user() {
	if ( ! did_action( 'muplugins_loaded' ) ) {
		return new WP_User();
	}

	// Original Core implementation.
	return _wp_get_current_user();
}
