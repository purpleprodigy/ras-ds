<?php
/*
Template Name: Login
*/

Namespace RASDS;

//* Remove the standard entry title
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
//* Remove the standard entry content
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

add_action( 'genesis_before_while', __NAMESPACE__ . '\add_custom_login_page_contents' );
/**
 * Add in the custom title and contents that are put into the editor for the login page.
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_custom_login_page_contents() {
	$page = get_post( get_option( 'page_for_posts' ) );

	if ( ! $page ) {
		return;
	}

	$content = wp_kses_post( $page->post_content );
	include( CHILD_THEME_DIR . '/lib/views/login.php' );
}

add_action( 'genesis_entry_content', __NAMESPACE__ . '\pp_login_form' );
/**
 * Add customised login form
 *
 * @since 1.0.0
 *
 * @return void
 */
function pp_login_form() {
	if ( ! is_user_logged_in() ) {
		$args = array(
			'redirect'       => '/start-new-ras-ds/dashboard',
			'form_id'        => 'loginform-custom',
			'label_username' => __( 'Email or Username' ),
			'label_password' => __( 'Password' ),
			'label_log_in'   => __( 'Log In' ),
			'remember'       => false
		);
		wp_login_form( $args );
		echo '<a href="' . get_bloginfo( 'url' ) . "/wp-login.php?action=lostpassword" . '">Forgotten your password?</a><br>';
		echo 'Don’t have an account? <a href="' . get_bloginfo( 'url' ) . "/register" . '">Register now</a>';
	} else {
		echo 'You are already logged in.<br><br> <a class="btn btn-primary mar-r-1" href="/dashboard">Back to My Tracker</a><br><br>';
	}
}

genesis();