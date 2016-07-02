<?php

/**
 * Don't give direct access to the template
 */
if ( ! class_exists( 'RGForms' ) ) {
	return;
}

$form = RGFormsModel::get_form_meta( $form_id );

$child_theme_uri = get_stylesheet_directory_uri();

require( 'views/index.php' );

/**
 * So if you comment this out, you'll see that it does not render the chart into the
 * <embed> element.  The chart is generated in JavaScript obviously.
 */
// die();
