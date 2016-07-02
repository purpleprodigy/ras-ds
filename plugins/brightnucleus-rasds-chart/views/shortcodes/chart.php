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

use BrightNucleus\Config\ConfigFactory;
use SVGGraph;

// Fetch chart settings from Config file.
$config = ConfigFactory::create( BN_RASDS_CHARTS_DIR . '/config/defaults.php' )
                       ->getSubConfig( CONFIG_PREFIX, 'Chart' );

$options = $config->getKey( 'options' );
$data    = ( new ChartData() )->get_data();
$options = array_merge( $options, $data['options'] );
$values  = $data['values'];
$type    = $data['type'];
$colours = $data['colours'];

$graph          = new SVGGraph(
	$config->getKey( 'width' ),
	$config->getKey( 'height' ),
	$options
);
$graph->colours = $colours;
$graph->Values( $values );
echo $graph->Fetch( $type );
