<?php
/**
 * Header
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS;

remove_action( 'wp_head', 'genesis_load_favicon' );
add_action( 'wp_head', __NAMESPACE__ . '\add_in_favicons' );
/**
 * Add favicons.
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_in_favicons() {
	include( CHILD_THEME_DIR . '/lib/views/favicons.php' );
}

add_action( 'genesis_before_header', __NAMESPACE__ . '\show_login_widget' );
/**
 * Shows login widget area for logged in users.
 *
 * @since 1.0.0
 *
 * @return void
 */
function show_login_widget() {
	genesis_widget_area( 'login', array(
		'before' => '<div class="row-login"><div class="container-fluid"><div class="login text-1 text-right">',
		'after'  => '</div></div></div>',
	) );
}

// Filter the header with a custom function
add_filter('genesis_seo_title', __NAMESPACE__ . '\custom_header' );

function custom_header() {
	include( CHILD_THEME_DIR . '/lib/views/header.php' );
}
