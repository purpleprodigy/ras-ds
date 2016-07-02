<input type="hidden" id="gravityview-entry-id" value="<?php echo intval( $entry['id'] ); ?>" />
<?php
	$chart_data = new \BrightNucleus\RASDS_Charts\ChartData();
	if ( $chart_data->is_comparison() ) {
		?>
<input type="hidden" id="gravityview-comparison-id" value="<?php echo $chart_data->get_comparison_id(); ?>" />
		<?php
	}
?>
<input type="hidden" id="ajaxurl" value="<?php echo esc_url( $ajaxurl ); ?>" />
<?php echo do_shortcode('[bn_rasds_chart]');
