<?php
/**
 * RASDS Display a single entry when using a table template
 *
 * @package GravityView
 * @subpackage GravityView/templates
 *
 * @global GravityView_View $this
 */

use BrightNucleus\RASDS_Charts\ChartData;

$chart_data = new ChartData();
$comparison = $chart_data->is_comparison() ? '&rasdscompare=' . $chart_data->get_comparison_id() : '';

?>
<?php gravityview_before(); ?>

	<div class="gv-table-view gv-container gv-table-single-container">

		<?php $entry = $this->getCurrentEntry(); ?>

		<div class="mar-b-4">
			<a class="btn btn-primary mar-r-1" href="javascript:void()" onclick="window.print()">Print</a>
			<a class="btn btn-primary mar-r-1"
			   href="/?gf_pdf=1&fid=1&lid=<?php echo $entry['id']; ?>&template=ras-ds-online-custom-template.php<?php echo $comparison; ?>" target="_blank">PDF</a>
			<a class="btn btn-primary mar-r-1" id="rasds-email-entry"
			   href="/rasds/email?gvid=<?php echo $this->getViewId(); ?>&rasdsgventry=<?php echo $entry['id']; ?><?php echo $comparison; ?>">Email</a>
			<a class="btn btn-primary mar-r-1" href="/dashboard">Back to My Tracker</a>
			<a class="btn btn-primary mar-r-1" href="/workbook">Go to the workbook</a>
		</div>



		<?php

		$fields = $this->getFields( 'single_table-columns' );

		if ( ! empty( $fields ) ) {

			$content = GFP_SydneyUni_RASDS::get_results_page_content( $entry, $fields );

			?>

			<?php if ( $content['comparison'] ) { ?>
				<h2 class="mar-0 mar-b-4 text-9">Compare RAS-DS Results</h2>
			<?php } else { ?>
				<?php the_title( '<h2 class="mar-0 mar-b-4 text-9">', '</h2>' ); ?>

			<?php } ?>

			<div class="mar-b-5 <?php echo $content['bar_chart']['class'] ?>">
				<?php echo $content['bar_chart']['output'] ?>
			</div>

			<div id="gv-field-1-custom" class="<?php echo $content['key']['class']; ?> gv-field-1-custom">
				<?php echo $content['key']['output']; ?>
			</div>

			<?php foreach ( $content['tables'] as $key => $table ) { ?>

				<h3 class="mar-t-0 mar-b-3 <?php echo $table['section_header']['class']; ?>">
					<?php echo $table['section_header']['label']; ?>
				</h3>

				<table class="table table-border gv-table-view-content">

					<thead>

					<?php if ( $content['comparison'] ) { ?>

						<tr>
							<th class="col-sm-7"></th>
							<th class="col-sm-4" colspan="2"><?php echo $content['test1'] ?></th>
							<th class="col-sm-4" colspan="2"><?php echo $content['test2'] ?></th>
						</tr>
						<tr>
							<th class="col-sm-7 compare-results-question">Question</th>
							<th class="col-sm-1 compare-results-score">Score</th>
							<th class="col-sm-4 compare-results-comment">Comment</th>
							<th class="col-sm-1 compare-results-score">Score</th>
							<th class="col-sm-4 compare-results-comment">Comment</th>
						</tr>

					<?php } else { ?>

						<tr>
							<th class="col-sm-7 single-results-question">Question</th>
							<th class="col-sm-1 single-results-score">Score</th>
							<th class="col-sm-4 single-results-comment">Comment</th>
						</tr>

					<?php } ?>

					</thead>

					<tbody>

					<?php foreach ( $table['rows'] as $row ) { ?>

						<?php if ( $content['comparison'] ) { ?>

							<tr>
								<td><?php echo $row['question'] ?></td>
								<td><?php echo $row['score'] ?></td>
								<td class="results-comment"><?php echo $row['comment'] ?></td>
								<td><?php echo $row['comparison_score'] ?></td>
								<td class="results-comment"><?php echo $row['comparison_comment'] ?></td>
							</tr>

						<?php } else { ?>

							<tr>
								<td><?php echo $row['question'] ?></td>
								<td><?php echo $row['score'] ?></td>
								<td class="results-comment"><?php echo $row['comment'] ?></td>
							</tr>

						<?php } ?>

					<?php } ?>

					<tr class="<?php echo $table['section_score']['class'] ?>">
						<td><?php echo $table['section_score']['label'] ?></td>
						<td><?php echo $table['section_score']['value'] ?></td>
						<td>&nbsp;</td>

						<?php if ( $content['comparison'] ) { ?>

							<td><?php echo $table['section_score']['comparison'] ?></td>
							<td>&nbsp;</td>

						<?php } ?>

					</tr>

					<tr class="<?php echo $table['section_percentage']['class'] ?>">
						<td><?php echo $table['section_percentage']['label'] ?></td>
						<td><?php echo $table['section_percentage']['value'] ?>%</td>
						<td>&nbsp;</td>

						<?php if ( $content['comparison'] ) { ?>

							<td><?php echo $table['section_percentage']['comparison'] ?>%</td>
							<td>&nbsp;</td>

						<?php } ?>

					</tr>

					<?php if ( ( count( $content['tables'] ) - 1 ) == $key ) { ?>

						<tr class="<?php echo $content['total_score']['class'] ?>">
							<td><?php echo $content['total_score']['label'] ?></td>
							<td><?php echo $content['total_score']['value'] ?></td>
							<td>&nbsp;</td>

							<?php if ( $content['comparison'] ) { ?>

								<td><?php echo $content['total_score']['comparison'] ?></td>
								<td>&nbsp;</td>

							<?php } ?>

						</tr>

						<tr class="<?php echo $content['total_percentage']['class'] ?>">
							<td><?php echo $content['total_percentage']['label'] ?></td>
							<td><?php echo $content['total_percentage']['value'] ?>%</td>
							<td>&nbsp;</td>

							<?php if ( $content['comparison'] ) { ?>

								<td><?php echo $content['total_percentage']['comparison'] ?>%</td>
								<td>&nbsp;</td>

							<?php } ?>

						</tr>

					<?php } ?>

					</tbody>

				</table>

			<?php } ?>
			<div class="mar-b-4">
				<a class="btn btn-primary mar-r-1" href="javascript:void()" onclick="window.print()">Print</a>
				<a class="btn btn-primary mar-r-1"
				   href="/?gf_pdf=1&fid=1&lid=<?php echo $entry['id']; ?>&template=ras-ds-online-custom-template.php<?php echo $comparison; ?>" target="_blank">PDF</a>
				<a class="btn btn-primary mar-r-1" id="rasds-email-entry"
				   href="/rasds/email?gvid=<?php echo $this->getViewId(); ?>&rasdsgventry=<?php echo $entry['id']; ?><?php echo $comparison; ?>">Email</a>
				<a class="btn btn-primary mar-r-1" href="/dashboard">Back to My Tracker</a>
				<a class="btn btn-primary mar-r-1" href="/workbook">Go to the workbook</a>
			</div>
			<?php echo $content['understanding']['output']; ?>

		<?php } ?>

	</div>

<?php gravityview_after(); ?>
