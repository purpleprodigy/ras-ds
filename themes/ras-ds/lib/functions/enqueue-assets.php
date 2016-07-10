<?php
/**
 * Enqueue assets
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */

namespace RASDS;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );
/**
 * Enqueue theme assets.
 *
 * @since 1.0.0
 */
function enqueue_assets() {
	wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css' );
	wp_enqueue_style( 'styles', get_stylesheet_directory_uri() . '/assets/css/styles.css' );
	wp_enqueue_style( 'print', get_stylesheet_directory_uri() . '/assets/css/print.css', '', '', 'print' );
	wp_enqueue_style( 'font-awesome', get_stylesheet_directory_uri() . '/assets/css/font-awesome.min.css' );
	wp_enqueue_script( 'bootstrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.js', array() );
//	wp_enqueue_script( 'npm', get_stylesheet_directory_uri() . '/assets/js/npm.js', array() );
//	wp_enqueue_script( 'responsive-nav', get_stylesheet_directory_uri() . '/assets/js/responsive-nav.js', array( 'jquery' ) );
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:400,700,800,800italic,400italic', array(), CHILD_THEME_VERSION );
}


