<span id="gfp-rasds-<?php esc_attr_e( $chart_object_info['chart_type'] ); ?>_chart_<?php esc_attr_e( $chart_object_info['id'] ); ?>" class="gfp-rasds-<?php esc_attr_e( $chart_object_info['chart_type'] ); ?>_chart"></span>
<script type="text/javascript">
	jQuery(document).on( "gfp_rasds_object_declared", function(){
		gfp_rasds_chart_js.charts.push(<?php echo wp_json_encode( $formatted_chart_object_info ); ?>);
	});
</script>
<input type="hidden" id="gravityview-entry-id" value="<?php echo intval( $entry['id'] ); ?>" />
<input type="hidden" id="ajaxurl" value="<?php echo esc_url( $ajaxurl ); ?>" />