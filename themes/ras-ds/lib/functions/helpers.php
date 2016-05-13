<?php
/**
 * Additional functionality for GF
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS;

// Automatically login user when they register.
add_action("gform_user_registered", "autologin", 10, 4);
function autologin($user_id, $config, $entry, $password) {
	wp_set_auth_cookie($user_id, false, '');
}

// Enable users to login with email or username
function enable_login_with_email( $user, $email_or_username, $password ) {
	if ( ! is_email( $email_or_username ) ) {
		return $user;
	}

	$user = get_user_by( 'email', $email_or_username );
	if ( is_wp_error( $user ) || ! $user instanceof \WP_User ) {
		return $user;
	}

	return wp_authenticate_username_password( null, $user->user_login, $password );
}
