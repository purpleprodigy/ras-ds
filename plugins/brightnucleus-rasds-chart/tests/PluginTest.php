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

use Brain\Monkey;
use Brain\Monkey\WP\Actions;
use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Dependency\DependencyManager;
use BrightNucleus\Shortcode\ShortcodeManager;
use Mockery;

/**
 * Class PluginTest.
 *
 * @since  1.0.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class PluginTest extends \PHPUnit_Framework_TestCase {

	/** @var Plugin */
	protected $plugin;

	/**
	 * Test whether the class can be instantiated.
	 *
	 * @since 1.0.0
	 */
	public function testClassInstantiation() {
		$this->assertInstanceOf(
			'BrightNucleus\RASDS_Charts\Plugin',
			$this->plugin
		);
	}

	/**
	 * Test whether the run method triggers shortcode initialization.
	 *
	 * @since 1.0.0
	 */
	public function testRunTriggersInitShortcodes() {
		$this->plugin->run();
		$this->assertTrue( has_action(
			'init',
			[ $this->plugin, 'init_shortcodes' ],
			20
		) );
	}

	/**
	 * Set up test case.
	 *
	 * @since 1.0.0
	 */
	protected function setUp() {
		parent::setUp();
		Monkey::setUpWP();
		$this->plugin = new Plugin();
	}

	/**
	 * Tear down test case.
	 *
	 * @since 1.0.0
	 */
	protected function tearDown() {
		Monkey::tearDownWP();
		parent::tearDown();
	}
}
