<?php
/**
 * Re-implementation of the WordPress Core pluggable function `get_user_by()`, to have it available early.
 *
 * @package plugin-check
 */

/**
 * Retrieves user info by a given field.
 *
 * @global WP_User $current_user The current user object which holds the user data.
 *
 * @param string     $field The field to retrieve the user with. id | ID | slug | email | login.
 * @param int|string $value A value for $field. A user ID, slug, email address, or login name.
 * @return WP_User|false WP_User object on success, false on failure.
 */
function get_user_by( $field, $value ) {
	$userdata = WP_User::get_data_by( $field, $value );

	if ( ! $userdata ) {
		return false;
	}

	$user = new WP_User();
	$user->init( $userdata );

	return $user;
}
