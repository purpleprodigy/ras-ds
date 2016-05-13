<?php
/*
Template Name: Home
*/
namespace RASDS;

add_action( 'genesis_before_while', __NAMESPACE__ .  '\add_in_header' );
/**
 * Add home page header.
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_in_header() {
	if ( is_front_page() ) {
		include( CHILD_THEME_DIR . '/lib/views/home-page-header.php' );
	}
}

add_action( 'genesis_before_entry', __NAMESPACE__ . '\render_home_left', 9 );
/**
 * Renders out the home left widget area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function render_home_left() {
	genesis_widget_area( 'home-left', array(
		'before' => '	<div class="row-white pad-y-4">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-9">

					<div class="row">
						<div class="col-sm-6">',
		'after'  => '</div>',
	) );
}

add_action( 'genesis_before_entry', __NAMESPACE__ . '\render_home_right', 9 );
/**
 * Renders out the home right widget area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function render_home_right() {
	genesis_widget_area( 'home-right', array(
		'before' => '<div class="col-sm-6">',
		'after'  => '</div></div>',
	) );
}

add_action( 'genesis_before_entry', __NAMESPACE__ . '\render_home_bottom', 9 );
/**
 * Renders out the home bottom widget area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function render_home_bottom() {
	genesis_widget_area( 'home-bottom', array(
		'before' => '<div class="row pad-t-3"><div class="col-sm-3 pad-b-2"><img class="img-responsive" src="' . get_stylesheet_directory_uri() . '/assets/img/piriws2.jpg" alt=""></div><div class="col-sm-9">',
		'after'  => '</div></div>',
	) );
}

genesis();
