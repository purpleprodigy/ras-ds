/**
 * scripts.js - Builds the distribution JavaScript and jQuery files
 *
 * @package     KnowTheCodeGulp
 * @since       1.0.0
 * @author      hellofromTonya <hellofromtonya@knowthecode.io>
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

'use strict';

module.exports = function ( gulp, plugins, config ) {

	var handleErrors = require( config.gulpDir + 'utils/handleErrors.js' );

	/**
	 * The tasks are synchronous to ensure the order is maintained and
	 * avoid any potential conflicts with the promises.
	 *
	 * @since 1.0.0
	 */
	return function () {
		clean();
	};

	/**
	 * Delete the .css before we minify and optimize
	 */
	function clean() {
		var settings = config.script.clean;

		plugins.del( settings.src ).then( function () {
			plugins.util.log( plugins.util.colors.bgGreen( 'Scripts are now clean....[clean()]' ) );
			concat();
		} );
	};

	/**
	 * Compile Sass and run stylesheet through PostCSS.
	 *
	 * @since 1.0.0
	 */
	function concat() {
		var settings = config.scripts.concat;

		return gulp.src( settings.src )

		           // Deal with errors.
		           .pipe( plugins.plumber( {errorHandler: handleErrors} ) )

		           .pipe( plugins.sourcemaps.init() )
		           .pipe( concat( settings.concatSrc ) )
		           .pipe( plugins.sourcemaps.write() )

		           .pipe( gulp.dest( settings.dest ) ).on( 'end', function () {
						plugins.util.log( plugins.util.colors.bgGreen( 'Scripts concat is now done....[concat()]' ) );
						uglify();
					} )
		           .pipe( plugins.browserSync.stream() );
		/**
		 * Minify and optimize style.css.
		 *
		 * @since 1.0.0
		 */
		function uglify() {
			var settings = config.styles.uglify;

			return gulp.src( settings.src )
			           // Deal with errors.
			           .pipe( plugins.plumber( {errorHandler: handleErrors} ) )

			           .pipe( plugins.rename( {suffix: '.min'} ) )
			           .pipe( plugins.uglify( {
				           mangle: false
			           } ) )
			           .pipe( gulp.dest( settings.dest ) ).on( 'end', function () {
							plugins.util.log( plugins.util.colors.bgGreen( 'Scripts are now minified....[uglify()]' ) );
						} )
			           .pipe( plugins.notify( {message: 'Scripts are built.'} ) );
		};

	};

};