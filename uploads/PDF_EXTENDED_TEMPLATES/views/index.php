<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<link rel="stylesheet" href="<?php echo $child_theme_uri . '/assets/css/styles.css'; ?>" type="text/css"/>
		<link rel="stylesheet" href="<?php echo $child_theme_uri . '/assets/css/bootstrap.min.css'; ?>" type="text/css"/>
		<title>RAS-DS Online</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script>
	</head>
	<body>
	<?php
	foreach ( $lead_ids as $lead_id ) :

		$lead      = RGFormsModel::get_lead( $lead_id );
		$form_data = GFPDFEntryDetail::lead_detail_grid_array( $form, $lead );

		/*
		 * Store your form fields from the $form_data array into variables here
		 * To see your entire $form_data array, view your PDF via the admin area and add &data=1 to the url
		 *
		 * For an example of accessing $form_data fields see https://developer.gravitypdf.com/documentation/custom-templates-introduction/
		 *
		 * Alternatively, as of v3.4.0 you can use merge tags (except {allfields}) in your templates.
		 * Just add merge tags to your HTML and they'll be parsed before generating the PDF.
		 *
		 */

		include( 'header.php' );
		include( 'table-1.php' );
		include( 'table-2.php' );
		include( 'table-3.php' );
		include( 'footer.php' );

	endforeach; ?>

	<script type="text/javascript" src="<?php echo 'https://www.google.com/jsapi?ver=' . GFP_RASDS_CURRENT_VERSION; ?>"></script>
	<script type="text/javascript" src="<?php echo GFP_RASDS_URL . 'gfp-rasds.min.js?ver=' . GFP_RASDS_CURRENT_VERSION; ?>"></script>
	</body>
</html>