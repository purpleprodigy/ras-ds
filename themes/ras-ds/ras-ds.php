<?php
/*
Template Name: RAD-DS Online
*/

Namespace RASDS;

//* Remove the standard entry title
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
//* Remove the standard entry content
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

add_action( 'genesis_before_while', __NAMESPACE__ . '\add_ras_ds_online_content' );
/**
 * Add in the custom title and contents that are put into the editor for the main ras-ds-online page.
 * (/rasds)
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_ras_ds_online_content() {
	$page = get_post( get_option( 'page_for_posts' ) );
	if ( ! is_user_logged_in() ) {

		$content = wp_kses_post( $page->post_content );

		include( CHILD_THEME_DIR . '/lib/views/ras-ds-online.php' );

	} else { // if logged in:
		echo 'You are already logged in. Go to your <a href="' . get_bloginfo( 'url' ) . "/start-new-ras-ds/dashboard" . '\">Dashboard</a><br>';
//		$location = get_bloginfo( 'url' ) .' "/start-new-ras-ds/dashboard';
//		wp_redirect( $location );
//		exit;
	}
}

genesis();