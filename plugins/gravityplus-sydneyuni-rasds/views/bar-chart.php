<input type="hidden" id="gravityview-entry-id" value="<?php echo intval( $entry['id'] ); ?>" />
<input type="hidden" id="ajaxurl" value="<?php echo esc_url( $ajaxurl ); ?>" />

<?php echo do_shortcode('[bn_rasds_chart]');
