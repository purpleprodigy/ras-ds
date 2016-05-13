/**
 * style.js - Builds the distribution stylesheets
 *
 * Tasks include:
 *      1. Linting
 *      2. Compiles the Sass files into CSS and stores them into the Distribution location
 *      3. Minifies the CSS in the Distribution location
 *      4. Moves the style.css file from the Distribution location to the root of your theme
 *
 * @package     KnowTheCodeGulp
 * @since       1.0.0
 * @author      hellofromTonya <hellofromtonya@knowthecode.io>
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

'use strict';

module.exports = function ( gulp, plugins, config ) {

	var handleErrors = require( config.gulpDir + 'utils/handleErrors.js' ),
		bourbon = require( 'bourbon' ).includePaths,
		neat = require( 'bourbon-neat' ).includePaths,
		mqpacker = require( 'css-mqpacker' );

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
		var settings = config.styles.clean;
		
		plugins.del( settings.src ).then(function(){
			plugins.util.log( plugins.util.colors.bgGreen( 'Styles are now clean....[cleanStyles()]' ) );
			postcss();
		});
	};

	/**
	 * Compile Sass and run stylesheet through PostCSS.
	 *
	 * @since 1.0.0
	 */
	function postcss() {
		var settings = config.styles.postcss;

		return gulp.src( settings.src )

		           // Deal with errors.
		           .pipe( plugins.plumber( {errorHandler: handleErrors} ) )

		           // Wrap tasks in a sourcemap.
		           .pipe( plugins.sourcemaps.init() )

		           // Compile Sass using LibSass.
		           .pipe( plugins.sass( {
			           includePaths: [].concat( bourbon, neat ),
			           errLogToConsole: true,
			           outputStyle: 'expanded' // Options: nested, expanded, compact, compressed
		           } ) )

		           // Parse with PostCSS plugins.
		           .pipe( plugins.postcss( [
			           plugins.autoprefixer( settings.autoprefixer ),
			           mqpacker(),
		           ] ) )

		           // Create sourcemap.
		           .pipe( plugins.sourcemaps.write() )

		           // Create *.css.
		           .pipe( gulp.dest( settings.dest ) ).on( 'end', function () {
						plugins.util.log( plugins.util.colors.bgGreen( 'postCSS is now done....[postCSS()]' ) );
						cssnano();
					} )
		           .pipe( plugins.browserSync.stream() );
		/**
		 * Minify and optimize style.css.
		 *
		 * @since 1.0.0
		 */
		function cssnano() {
			var settings = config.styles.cssnano;

			return gulp.src( settings.src, function( cb ){
				
				plugins.util.log( plugins.util.colors.bgGreen( 'cssnano is now done....[cssnano()]' ) );
			    cssfinalize();
			} )
			           // Deal with errors.
			           .pipe( plugins.plumber( {errorHandler: handleErrors} ) )

			           .pipe( plugins.cssnano( {
				           safe: true
			           } ) )
			           .pipe( plugins.rename( function ( path ) {
				           path.basename += ".min";
			           } ) )
			           .pipe( gulp.dest( settings.dest ) )
			           .pipe( plugins.browserSync.stream() );
		};

	};

	/**
	 * Move the style.css file to the theme's root
	 *
	 * @since 1.0.0
	 */
	function cssfinalize() {
		var settings = config.styles.cssfinalize;

		gulp.src( settings.src, function( cb ){
			    plugins.util.log( plugins.util.colors.bgGreen( 'Styles are all done....[cssfinalize()]' ) );
		} )
		    // Deal with errors.
		    .pipe( plugins.plumber( {errorHandler: handleErrors} ) )
		    .pipe( gulp.dest( settings.dest ) )
// 		    .pipe( gulp.dest( settings.rootDest ) )

			.pipe( plugins.notify( {title: "WooHoo, Task Done", message: 'Heya, styles are gulified.'} ) );
		
// 		plugins.del( settings.src );
	};

	/**
	 * Sass linting.
	 *
	 * @since 1.0.0
	 */
	function sassLint() {
		gulp.src( [
			    'assets/sass/**/*.scss',
			    '!assets/sass/base/_normalize.scss',
			    '!assets/sass/utilities/animate/**/*.*',
			    '!assets/sass/base/_sprites.scss'
		    ] )
		    .pipe( plugins.sassLint() )
		    .pipe( plugins.sassLint.format() )
		    .pipe( plugins.sassLint.failOnError() );
	};
};