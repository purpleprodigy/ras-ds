<?php
/**
 * Widget Areas
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS;

/**
 * Register the widget areas.
 *
 * @since  1.0.0
 *
 * @return void
 */

function register_widget_areas() {

	$widget_areas = array(
		array(
			'id'          => 'home-left',
			'name'        => __( 'Home Left', 'ras-ds' ),
			'description' => __( 'This is the widget area for the middle left of the homepage.', 'ras-ds' ),
			'before_title' => '<h3 class="home-subheadings">',
			'after_title' => '</h3>',
		),
		array(
			'id'          => 'home-right',
			'name'        => __( 'Home Right', 'ras-ds' ),
			'description' => __( 'This is the widget area for the middle right of the homepage.', 'ras-ds' ),
			'before_title' => '<h3 class="home-subheadings">',
			'after_title' => '</h3>',
		),
		array(
			'id'          => 'home-bottom',
			'name'        => __( 'Home Bottom', 'ras-ds' ),
			'description' => __( 'This is the widget area for the bottom of the homepage.', 'ras-ds' ),
		),
		array(
			'id'          => 'login',
			'name'        => __( 'Logged in area', 'ras-ds' ),
			'description' => __( 'This is the widget area to show if a user is logged in.', 'ras-ds' ),
		),
		array(
			'id'          => 'contact',
			'name'        => __( 'Contact page', 'ras-ds' ),
			'description' => __( 'This is the widget area to show on the right of the contact page.', 'ras-ds' ),
			'before_title' => '<h3 class="text-6 mar-0 mar-b-2">',
			'after_title' => '</h3>',
		),
		array(
			'id'          => 'register',
			'name'        => __( 'Register page', 'ras-ds' ),
			'description' => __( 'This is the widget area to show on the right of the register page.', 'ras-ds' ),
		),
	);

	foreach ( $widget_areas as $widget_area ) {
		genesis_register_sidebar( $widget_area );
	}
}
