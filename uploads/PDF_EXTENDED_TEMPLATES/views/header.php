<?php if ( isset( $comparison_lead ) ) { ?>
<h2 class="mar-0 mar-b-4 text-9">
	Compare RAS-DS Results: <br/>
	<span class="text-regular c-gray">{test-title:1}</span> vs <span class="text-regular c-gray"><?php echo esc_html( $comparison_lead[1] ); ?></span>
</h2>
<?php } else { ?>
<h2 class="mar-0 mar-b-4 text-9">
	Results: <span class="text-regular c-gray">{test-title:1}</span>
</h2>
<?php }

	$id = absint( isset( $_REQUEST['entry_id'] ) ? $_REQUEST['entry_id'] : 0 );
	$comparison_id = absint( isset( $_REQUEST['comparison_id'] ) ? $_REQUEST['comparison_id'] : 0 );
	echo do_shortcode( '[bn_rasds_chart id=' . $id . ' comparison_id=' . $comparison_id . ']' );
?>
<div class="bg-light-gray pad-y-2 pad-x-2 mar-b-4">
	<b>Key</b>:
	<span class="display-inline-block pad-r-1 pad-l-1"><b>1</b> = Untrue</span>
	<span class="display-inline-block pad-r-1 pad-l-1"><b>2</b> = A Bit True</span>
	<span class="display-inline-block pad-r-1 pad-l-1"><b>3</b> = Mostly True</span>
	<span class="display-inline-block pad-r-1 pad-l-1"><b>4</b> = Completely True</span>
</div>
