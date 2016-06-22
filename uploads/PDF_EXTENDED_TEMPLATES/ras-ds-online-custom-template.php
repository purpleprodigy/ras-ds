<?php

/**
 * Don't give direct access to the template
 */
if ( ! class_exists( 'RGForms' ) ) {
	return;
}

$form = RGFormsModel::get_form_meta( $form_id );

/**
 * Render the bar chart.
 *
 * @since 1.0.0
 *
 * @param array $form_data
 *
 * @return void
 */
function render_bar_chart( array $form_data ) {
	global $gfp_sydneyuni_rasds;

	$entry_id = (int) $form_data['entry_id'];

	$entry = GFAPI::get_entry( $entry_id );

	$bar_chart_data = $gfp_sydneyuni_rasds->get_formatted_data_for_bar_chart( $entry );

	$gfp_sydneyuni_rasds->get_chart_html_markup( $bar_chart_data, $entry, false );
}

$child_theme_uri = get_stylesheet_directory_uri();

require( 'views/index.php' );

/**
 * So if you comment this out, you'll see that it does not render the chart into the
 * <embed> element.  The chart is generated in JavaScript obviously.
 */
die();
