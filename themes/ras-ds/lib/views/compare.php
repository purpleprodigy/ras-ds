<div class="row-white pad-y-5">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="mar-b-3 overflow-hidden"><a class="float-left btn btn-default mar-r-2" href="/rasds/dashboard">Back to My Tracker</a></div>

				<h2 class="mar-0 mar-b-4 text-9">Compare Other Completed RAS-DS</h2>

				<div class="row">
					<div class="col-sm-7">
						<?php

						$entries_list = GFP_SydneyUni_RASDS::get_entries_list_for_comparisons();

						?>
						<form id="rasds-comparison-form">
							<div class="error"></div>
							<div class="form-group">
								<label for="rasds-compare-test1">RAS-DS 1:</label>
								<select id="rasds-compare-test1" class="form-control">
									<option value="">Choose RAS-DS</option>
									<?php foreach( $entries_list as $entry ) { ?>
										<option value="<?php echo $entry['id']?>"><?php echo $entry['label']?></option>
									<?php } unset( $entry );?>
								</select>
							</div>
							<div class="form-group">
								<label for="rasds-compare-test2">RAS-DS 2:</label>
								<select id="rasds-compare-test2" class="form-control">
									<option value="">Choose RAS-DS</option>
									<?php foreach( $entries_list as $entry ) { ?>
										<option value="<?php echo $entry['id']?>"><?php echo $entry['label']?></option>
									<?php } unset( $entry );?>
								</select>
							</div>
							<input type="submit" value="Compare" class="btn btn-primary" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


