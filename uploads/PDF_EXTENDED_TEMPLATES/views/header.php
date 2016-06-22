<h2 class="mar-0 mar-b-4 text-9">
	Results: <span class="text-regular c-gray">{test-title:1}</span>
</h2>

<?php
	render_bar_chart( $form_data );
?>

<div class="bg-light-gray pad-y-2 pad-x-2 mar-b-4">
	<b>Key</b>:
	<span class="display-inline-block pad-r-1 pad-l-1"><b>1</b> = Untrue</span>
	<span class="display-inline-block pad-r-1 pad-l-1"><b>2</b> = A Bit True</span>
	<span class="display-inline-block pad-r-1 pad-l-1"><b>3</b> = Mostly True</span>
	<span class="display-inline-block pad-r-1 pad-l-1"><b>4</b> = Completely True</span>
</div>