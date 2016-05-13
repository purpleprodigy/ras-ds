<div class="row-white pad-y-5">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<h2 class="mar-0 mar-b-4 text-9">My RAS-DS Tracker: <span class="text-regular"><?php echo do_shortcode('[gravityform action="user" key="nickname"]'); ?></span></h2>
				<div class="row">
					<div class="col-sm-5 pad-b-3">
						<div class="mar-b-2">
							<a href="<?php echo home_url(); ?>/ras-ds/start-new-ras-ds" class="btn btn-primary btn-block text-6"><b>Start a new RAS-DS</b></a>
						</div>
						<div class="mar-b-2">
							<a href="#" class="btn btn-primary btn-block">Compare Last Two Completed RAS-DS results</a>
						</div>
						<div class="mar-b-2">
							<a href="#" class="btn btn-primary btn-block">Compare Other Completed RAS-DS results</a>
						</div>
					</div>
					<div class="col-sm-6 col-sm-offset-1">
						<h3 class="mar-0 mar-b-2">Saved (Incomplete) RAS-DS</h3>
						<ul class="list-icon fa-ul mar-b-4">
							<li class="mar-b-1"><i class="fa-li fa fa-file-text-o c-mid-gray"></i><?php echo do_shortcode('[gravityview id=\'227\']'); ?></li>
						</ul>
						<h3 class="mar-0 mar-b-2">Completed RAS-DS</h3>
						<ul class="list-icon fa-ul mar-b-4">
							<li class="mar-b-1"><i class="fa-li fa fa-file-text-o c-mid-gray"></i><?php echo do_shortcode('[gravityview id=\'91\']'); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

