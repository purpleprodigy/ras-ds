<?php
/**
 * Bright Nucleus RAS-DS Charts.
 *
 * Render SVG charts from Gravity Forms data.
 *
 * @package   BrightNucleus\RASDS_Charts
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      https://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\RASDS_Charts;

/*
 * Chart rendering variables.
 */
$chartFont     = '"Helvetica Neue", Helvetica, Arial, sans-serif';
$chartFontSize = 13;

/*
 * Chart rendering settings.
 */
$chartOptions = [
	'auto_fit'              => true,
	'axis_colour'           => '#333',
	'axis_font'             => $chartFont,
	'axis_font_size'        => $chartFontSize,
	'axis_max_v'            => 100,
	'axis_min_v'            => 0,
	'axis_overlap'          => 0,
	'axis_stroke_width'     => 1,
	'axis_text_space'       => 10,
	'back_colour'           => 'none',
	'back_stroke_colour'    => '#eee',
	'back_stroke_width'     => 0,
	'data_label_colour'     => '#fff',
	'data_label_font'       => $chartFont,
	'data_label_font_size'  => $chartFontSize,
	'division_style'        => 'none',
	'grid_colour'           => '#ccc',
	'grid_division_v'       => 25,
	'label_colour'          => '#000',
	'legend_back_colour'    => 'none',
	'legend_entry_height'   => $chartFontSize,
	'legend_font'           => $chartFont,
	'legend_font_size'      => $chartFontSize,
	'legend_position'       => 'inner top 0 -35',
	'legend_shadow_opacity' => 0,
	'legend_stroke_width'   => 0,
	'minimum_grid_spacing'  => 20,
	'pad_left'              => 20,
	'pad_right'             => 20,
	'pad_top'               => 20,
	'shape'                 => [
		[
			'line',
			'x1'           => 'g4',
			'y1'           => 'g0',
			'x2'           => 'g4',
			'y2'           => 'g100',
			'stroke-width' => 1,
			'stroke'       => '#000',
		],
		[
			'line',
			'x1'           => 'g0',
			'y1'           => 'g100',
			'x2'           => 'g4',
			'y2'           => 'g100',
			'stroke-width' => 1,
			'stroke'       => '#000',
		],
	],
	'show_data_labels'      => true,
	'show_grid_v'           => false,
	'stroke_colour'         => '#000',
	'stroke_width'          => 0,
	'structure'             => [
		'key'   => 0,
		'value' => [ 1, 2 ],
	],
	'structured_data'       => true,
	'units_label'           => '%',
	'units_y'               => '%',
];

$chart = [
	'width'   => 600,
	'height'  => 380,
	'options' => $chartOptions,
	'colors'  => [ '#CE3D20', '#8EB304' ],
];

/*
 * ShortcodeManager settings.
 */
$shortcodeManager = [
	'bn_rasds_chart' => [
		'view' => BN_RASDS_CHARTS_DIR . '/views/shortcodes/chart.php',
		'atts' => [
			'id'        => [
				'validate' => function ( $att ) {
					return absint( $att );
				},
				'default'  => 0,
			],
			'compareTo' => [
				'validate' => function ( $att ) {
					return absint( $att );
				},
				'default'  => 0,
			],
		],
	],
];

/*
 * DependencyManager settings.
 */
$dependencyManager = [ ];

/*
 * PDF Filter settings.
 */
$pdfFilter = [
	// Remove the XML version tag preceding the SVG chart.
	// This tag gets visibly rendered in the PDF document, so we need to remove
	// it completely.
	'removeXMLVersion' => [
		'search'  => '/<\?xml version(.*)\?>/',
		'replace' => '',
	],

	// Fix the axis labels at the bottom of the chart.
	// These use relative positioning, which the PDF renderer does not seem to
	// handle correctly, so we change them into absolute positioning.
	'fixAxisLabels'    => [
		'search'    => '/<text\s+y="(.*?)"\s+x="(.*?)">\s*'
		               . '<tspan\s+x="(.*?)"\s+dy="(.*?)">(.*?)<\/tspan>\s*'
		               . '<tspan\s+x="(.*?)"\s+dy="(.*?)">(.*?)<\/tspan>\s*'
		               . '<\/text>/',
		'replace'   => '<text y="%d" x="%d">'
		               . '<tspan x="%d" y="%d">%s</tspan>'
		               . '<tspan x="%d" y="%d">%s</tspan>'
		               . '</text>',
		'arguments' => function ( $matches ) {
			return [
				$matches[1],
				$matches[2],
				$matches[3],
				$matches[1] + $matches[4],
				$matches[5],
				$matches[6],
				$matches[1] + $matches[7],
				$matches[8],
			];
		},
	],
];

/*
 * Configuration for Bright Nucleus RAS-DS Charts.
 */
$configuration = [
	'Chart'             => $chart,
	'DependencyManager' => $dependencyManager,
	'ShortcodeManager'  => $shortcodeManager,
	'PDFFilter'         => $pdfFilter,
];

/*
 * Return the configuration with a vendor/package prefix.
 */
return [
	'BrightNucleus' => [
		'RASDS_Charts' => $configuration,
	],
];
