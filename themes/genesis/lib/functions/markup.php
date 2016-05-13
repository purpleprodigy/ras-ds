<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Markup
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

/**
 * Output markup conditionally.
 *
 * Supported keys for `$args` are:
 *
 *  - `html5` (`sprintf()` pattern markup),
 *  - `xhtml` (XHTML markup),
 *  - `context` (name of context),
 *  - `echo` (default is true).
 *
 * If the child theme supports HTML5, then this function will output the `html5` value, with a call to `genesis_attr()`
 * with the same context added in. Otherwise, it will output the `xhtml` value.
 *
 * Applies a `genesis_markup_{context}` filter early to allow shortcutting the function.
 *
 * Applies a `genesis_markup_{context}_output` filter at the end.
 *
 * @since 1.9.0
 *
 * @uses genesis_html5() Check for HTML5 support.
 * @uses genesis_attr()  Contextual attributes.
 *
 * @param array $args Array of arguments.
 *
 * @return string Markup.
 */
function genesis_markup( $args = array() ) {

	$defaults = array(
		'html5'   => '',
		'xhtml'   => '',
		'context' => '',
		'echo'    => true,
	);

	$args = wp_parse_args( $args, $defaults );

	//* Short circuit filter
	$pre = apply_filters( "genesis_markup_{$args['context']}", false, $args );
	if ( false !== $pre ) {
		return $pre;
	}

	if ( ! $args['html5'] || ! $args['xhtml'] ) {
		return '';
	}

	//* If HTML5, return HTML5 tag. Maybe add attributes. Else XHTML.
	if ( genesis_html5() ) {
		$tag = $args['context'] ? sprintf( $args['html5'], genesis_attr( $args['context'] ) ) : $args['html5'];
	} else {
		$tag = $args['xhtml'];
	}

	//* Contextual filter
	$tag = $args['context'] ? apply_filters( "genesis_markup_{$args['context']}_output", $tag, $args ) : $tag;

	if ( $args['echo'] ) {
		echo $tag;
	} else {
		return $tag;
	}

}

/**
 * Merge array of attributes with defaults, and apply contextual filter on array.
 *
 * The contextual filter is of the form `genesis_attr_{context}`.
 *
 * @since 2.0.0
 *
 * @param  string $context The context, to build filter name.
 * @param  array $attributes Optional. Extra attributes to merge with defaults.
 *
 * @return array Merged and filtered attributes.
 */
function genesis_parse_attr( $context, $attributes = array() ) {

	$defaults = array(
		'class' => sanitize_html_class( $context ),
	);

	$attributes = wp_parse_args( $attributes, $defaults );

	//* Contextual filter
	return apply_filters( "genesis_attr_{$context}", $attributes, $context );

}

/**
 * Build list of attributes into a string and apply contextual filter on string.
 *
 * The contextual filter is of the form `genesis_attr_{context}_output`.
 *
 * @since 2.0.0
 *
 * @uses genesis_parse_attr() Merge array of attributes with defaults, and apply contextual filter on array.
 *
 * @param  string $context The context, to build filter name.
 * @param  array $attributes Optional. Extra attributes to merge with defaults.
 *
 * @return string String of HTML attributes and values.
 */
function genesis_attr( $context, $attributes = array() ) {

	$attributes = genesis_parse_attr( $context, $attributes );
	$output = '';

	//* Cycle through attributes, build tag attribute string
	foreach ( $attributes as $attribute => $value ) {

		if ( ! $value ) {
			continue;
		}

		if ( true === $value ) {
			$output .= esc_html( $attribute ) . ' ';

		} else {
			$output .= sprintf( '%s="%s" ', esc_html( $attribute ), esc_attr( $value ) );
		}

	}

	$output = apply_filters( "genesis_attr_{$context}_output", $output, $attributes, $context );
	return trim( $output );
}

/**
 * Helper function for use as a filter for when you want to prevent a class from being automatically
 * generated and output on an element that is passed through the markup API.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_empty_class( $attributes ) {

	$attributes['class'] = '';

	return $attributes;

}

/**
 * Helper function for use as a filter for when you want to add screen-reader-text class to an element.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_screen_reader_class( $attributes ) {

	$attributes['class'] .= ' screen-reader-text';

	return $attributes;

}

add_filter( 'genesis_attr_head', 'genesis_attributes_head' );
/**
 * Add attributes for head element.
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_head( $attributes ) {

	$attributes['class'] = '';

	if ( ! is_front_page() ) {
		return $attributes;
	}

	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/WebSite';

	return $attributes;

}

add_filter( 'genesis_attr_body', 'genesis_attributes_body' );
/**
 * Add attributes for body element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_body( $attributes ) {

	$attributes['class']     = join( ' ', get_body_class() );
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/WebPage';

	//* Search results pages
	if ( is_search() ) {
		$attributes['itemtype'] = 'http://schema.org/SearchResultsPage';
	}

	return $attributes;

}

add_filter( 'genesis_attr_site-header', 'genesis_attributes_header' );
/**
 * Add attributes for site header element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_header( $attributes ) {

	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/WPHeader';

	return $attributes;

}

add_filter( 'genesis_attr_site-title', 'genesis_attributes_site_title' );
/**
 * Add attributes for site title element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_site_title( $attributes ) {

	$attributes['itemprop'] = 'headline';

	return $attributes;

}

add_filter( 'genesis_attr_site-description', 'genesis_attributes_site_description' );
/**
 * Add attributes for site description element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_site_description( $attributes ) {

	$attributes['itemprop'] = 'description';

	return $attributes;

}

add_filter( 'genesis_attr_header-widget-area', 'genesis_attributes_header_widget_area' );
/**
 * Add attributes for header widget area element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_header_widget_area( $attributes ) {

	$attributes['class'] = 'widget-area header-widget-area';

	return $attributes;

}

add_filter( 'genesis_attr_breadcrumb', 'genesis_attributes_breadcrumb' );
/**
 * Add attributes for breadcrumb wrapper.
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Ammended attributes
 */
function genesis_attributes_breadcrumb( $attributes ) {

	$attributes['itemprop']  = 'breadcrumb';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/BreadcrumbList';

	//* Breadcrumb itemprop not valid on blog
	if ( is_singular( 'post' ) || is_archive() || is_home() || is_page_template( 'page_blog.php' ) ) {
		unset( $attributes['itemprop'] );
	}

	return $attributes;

}

add_filter( 'genesis_attr_breadcrumb-link-wrap', 'genesis_attributes_breadcrumb_link_wrap' );
/**
 * Add attributes for breadcrumb wrapper.
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Ammended attributes
 */
function genesis_attributes_breadcrumb_link_wrap( $attributes ) {

	$attributes['itemprop']  = 'itemListElement';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/ListItem';

	return $attributes;

}

add_filter( 'genesis_attr_search-form', 'genesis_attributes_search_form' );
/**
 * Add attributes for search form.
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_search_form( $attributes ) {

	$attributes['itemprop']  = 'potentialAction';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/SearchAction';
	$attributes['method']    = 'get';
	$attributes['action']    = home_url( '/' );
	$attributes['role']      = 'search';

	return $attributes;

}

add_filter( 'genesis_attr_nav-primary', 'genesis_attributes_nav' );
add_filter( 'genesis_attr_nav-secondary', 'genesis_attributes_nav' );
add_filter( 'genesis_attr_nav-header', 'genesis_attributes_nav' );
/**
 * Add typical attributes for navigation elements.
 *
 * Used for primary navigation, secondary navigation, and custom menu widgets in the header right widget area.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_nav( $attributes ) {

	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/SiteNavigationElement';

	return $attributes;

}

add_filter( 'genesis_attr_nav-link-wrap', 'genesis_attributes_nav_link_wrap' );
/**
 * Add attributes for the span wrap around navigation item links.
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_nav_link_wrap( $attributes ) {

	$attributes['class']    = '';
	$attributes['itemprop'] = 'name';

	return $attributes;

}

add_filter( 'genesis_attr_nav-link', 'genesis_attributes_nav_link' );
/**
 * Add attributes for the navigation item links.
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_nav_link( $attributes ) {

	/**
	 * Since we're utilizing a filter that plugins might also want to filter, don't overwrite class here.
	 *
	 * @link https://github.com/copyblogger/genesis/issues/1226
	 */
	$class = str_replace( 'nav-link', '', $attributes['class'] );

	$attributes['class']    = $class;
	$attributes['itemprop'] = 'url';

	return $attributes;

}

add_filter( 'genesis_attr_structural-wrap', 'genesis_attributes_structural_wrap' );
/**
 * Add attributes for structural wrap element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_structural_wrap( $attributes ) {

	$attributes['class'] = 'wrap';

	return $attributes;

}

add_filter( 'genesis_attr_content', 'genesis_attributes_content' );
/**
 * Add attributes for main content element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_content( $attributes ) {

	return $attributes;

}

add_filter( 'genesis_attr_taxonomy-archive-description', 'genesis_attributes_taxonomy_archive_description' );
/**
 * Add attributes for taxonomy description.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_taxonomy_archive_description( $attributes ) {

	$attributes['class'] = 'archive-description taxonomy-archive-description taxonomy-description';

	return $attributes;

}

add_filter( 'genesis_attr_author-archive-description', 'genesis_attributes_author_archive_description' );
/**
 * Add attributes for author description.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_author_archive_description( $attributes ) {

	$attributes['class'] = 'archive-description author-archive-description author-description';

	return $attributes;

}

add_filter( 'genesis_attr_cpt-archive-description', 'genesis_attributes_cpt_archive_description' );
/**
 * Add attributes for CPT archive description.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_cpt_archive_description( $attributes ) {

	$attributes['class'] = 'archive-description cpt-archive-description';

	return $attributes;

}

add_filter( 'genesis_attr_date-archive-description', 'genesis_attributes_date_archive_description' );
/**
 * Add attributes for date archive description.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_date_archive_description( $attributes ) {

	$attributes['class'] = 'archive-description date-archive-description archive-date';

	return $attributes;

}

add_filter( 'genesis_attr_blog-template-description', 'genesis_attributes_blog_template_description' );
/**
 * Add attributes for blog template description.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_blog_template_description( $attributes ) {

	$attributes['class'] = 'archive-description blog-template-description';

	return $attributes;

}

add_filter( 'genesis_attr_posts-page-description', 'genesis_attributes_posts_page_description' );
/**
 * Add attributes for posts page description.
 *
 * @since 2.2.1
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_posts_page_description( $attributes ) {

	$attributes['class'] = 'archive-description posts-page-description';

	return $attributes;

}

add_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
/**
 * Add attributes for entry element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry( $attributes ) {

	$attributes['class'] = join( ' ', get_post_class() );

	if ( ! is_main_query() && ! genesis_is_blog_template() ) {
		return $attributes;
	}

	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/CreativeWork';

	return $attributes;

}

add_filter( 'genesis_attr_entry-image', 'genesis_attributes_entry_image' );
/**
 * Add attributes for entry image element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_image( $attributes ) {

	$attributes['class']    = genesis_get_option( 'image_alignment' ) . ' post-image entry-image';
	$attributes['itemprop'] = 'image';

	return $attributes;

}

add_filter( 'genesis_attr_entry-image-widget', 'genesis_attributes_entry_image_widget' );
/**
 * Add attributes for entry image element shown in a widget.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_image_widget( $attributes ) {

	$attributes['class']    = 'entry-image attachment-' . get_post_type();
	$attributes['itemprop'] = 'image';

	return $attributes;

}

add_filter( 'genesis_attr_entry-image-grid-loop', 'genesis_attributes_entry_image_grid_loop' );
/**
 * Add attributes for entry image element shown in a grid loop.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_image_grid_loop( $attributes ) {

	$attributes['itemprop'] = 'image';

	return $attributes;

}

add_filter( 'genesis_attr_entry-author', 'genesis_attributes_entry_author' );
/**
 * Add attributes for author element for an entry.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_author( $attributes ) {

	$attributes['itemprop']  = 'author';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/Person';

	return $attributes;

}

add_filter( 'genesis_attr_entry-author-link', 'genesis_attributes_entry_author_link' );
/**
 * Add attributes for entry author link element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_author_link( $attributes ) {

	$attributes['itemprop'] = 'url';
	$attributes['rel']      = 'author';

	return $attributes;

}

add_filter( 'genesis_attr_entry-author-name', 'genesis_attributes_entry_author_name' );
/**
 * Add attributes for entry author name element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_author_name( $attributes ) {

	$attributes['itemprop'] = 'name';

	return $attributes;

}

add_filter( 'genesis_attr_entry-time', 'genesis_attributes_entry_time' );
/**
 * Add attributes for time element for an entry.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_time( $attributes ) {

	$attributes['itemprop'] = 'datePublished';
	$attributes['datetime'] = get_the_time( 'c' );

	return $attributes;

}

add_filter( 'genesis_attr_entry-modified-time', 'genesis_attributes_entry_modified_time' );
/**
 * Add attributes for modified time element for an entry.
 *
 * @since 2.1.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_modified_time( $attributes ) {

	$attributes['itemprop'] = 'dateModified';
	$attributes['datetime'] = get_the_modified_time( 'c' );

	return $attributes;

}

add_filter( 'genesis_attr_entry-title', 'genesis_attributes_entry_title' );
/**
 * Add attributes for entry title element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_title( $attributes ) {

	$attributes['itemprop'] = 'headline';

	return $attributes;

}

add_filter( 'genesis_attr_entry-content', 'genesis_attributes_entry_content' );
/**
 * Add attributes for entry content element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_content( $attributes ) {

	$attributes['itemprop'] = 'text';

	return $attributes;

}

add_filter( 'genesis_attr_entry-meta-before-content', 'genesis_attributes_entry_meta' );
add_filter( 'genesis_attr_entry-meta-after-content', 'genesis_attributes_entry_meta' );
/**
 * Add attributes for entry meta elements.
 *
 * @since 2.1.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_meta( $attributes ) {

	$attributes['class'] = 'entry-meta';

	return $attributes;

}

add_filter( 'genesis_attr_archive-pagination', 'genesis_attributes_pagination' );
add_filter( 'genesis_attr_entry-pagination', 'genesis_attributes_pagination' );
add_filter( 'genesis_attr_adjacent-entry-pagination', 'genesis_attributes_pagination' );
add_filter( 'genesis_attr_comments-pagination', 'genesis_attributes_pagination' );
/**
 * Add attributes for pagination.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_pagination( $attributes ) {

	$attributes['class'] .= ' pagination';

	return $attributes;

}

add_filter( 'genesis_attr_entry-comments', 'genesis_attributes_entry_comments' );
/**
 * Add attributes for entry comments element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_entry_comments( $attributes ) {

	$attributes['id'] = 'comments';

	return $attributes;

}

add_filter( 'genesis_attr_comment', 'genesis_attributes_comment' );
/**
 * Add attributes for single comment element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_comment( $attributes ) {

	$attributes['class']     = '';
	$attributes['itemprop']  = 'comment';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/Comment';

	return $attributes;

}

add_filter( 'genesis_attr_comment-author', 'genesis_attributes_comment_author' );
/**
 * Add attributes for comment author element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_comment_author( $attributes ) {

	$attributes['itemprop']  = 'author';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/Person';

	return $attributes;

}

add_filter( 'genesis_attr_comment-author-link', 'genesis_attributes_comment_author_link' );
/**
 * Add attributes for comment author link element.
 *
 * @since 2.1.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_comment_author_link( $attributes ) {

	$attributes['rel']      = 'external nofollow';
	$attributes['itemprop'] = 'url';

	return $attributes;

}

add_filter( 'genesis_attr_comment-time', 'genesis_attributes_comment_time' );
/**
 * Add attributes for comment time element.
 *
 * @since 2.1.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_comment_time( $attributes ) {

	$attributes['datetime'] = esc_attr( get_comment_time( 'c' ) );
	$attributes['itemprop'] = 'datePublished';

	return $attributes;

}

add_filter( 'genesis_attr_comment-time-link', 'genesis_attributes_comment_time_link' );
/**
 * Add attributes for comment time link element.
 *
 * @since 2.1.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_comment_time_link( $attributes ) {

	$attributes['itemprop'] = 'url';

	return $attributes;

}

add_filter( 'genesis_attr_comment-content', 'genesis_attributes_comment_content' );
/**
 * Add attributes for comment content container.
 *
 * @since 2.1.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_comment_content( $attributes ) {

	$attributes['itemprop'] = 'text';

	return $attributes;

}

add_filter( 'genesis_attr_author-box', 'genesis_attributes_author_box' );
/**
 * Add attributes for author box element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_author_box( $attributes ) {

	$attributes['itemprop']  = 'author';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/Person';

	return $attributes;

}

add_filter( 'genesis_attr_sidebar-primary', 'genesis_attributes_sidebar_primary' );
/**
 * Add attributes for primary sidebar element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_sidebar_primary( $attributes ) {

	$attributes['class']      = 'sidebar sidebar-primary widget-area';
	$attributes['role']       = 'complementary';
	$attributes['aria-label'] = __( 'Primary Sidebar', 'genesis' );
	$attributes['itemscope']  = true;
	$attributes['itemtype']   = 'http://schema.org/WPSideBar';

	return $attributes;

}

add_filter( 'genesis_attr_sidebar-secondary', 'genesis_attributes_sidebar_secondary' );
/**
 * Add attributes for secondary sidebar element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_sidebar_secondary( $attributes ) {

	$attributes['class']      = 'sidebar sidebar-secondary widget-area';
	$attributes['role']       = 'complementary';
	$attributes['aria-label'] = __( 'Secondary Sidebar', 'genesis' );
	$attributes['itemscope']  = true;
	$attributes['itemtype']   = 'http://schema.org/WPSideBar';

	return $attributes;

}

add_filter( 'genesis_attr_site-footer', 'genesis_attributes_site_footer' );
/**
 * Add attributes for site footer element.
 *
 * @since 2.0.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Amended attributes.
 */
function genesis_attributes_site_footer( $attributes ) {

	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/WPFooter';

	return $attributes;

}

/**
 * Add ID markup to the elements to jump to
 *
 * @since 2.2.0
 *
 * @link https://gist.github.com/salcode/7164690
 * @link genesis_markup() http://docs.garyjones.co.uk/genesis/2.0.0/source-function-genesis_parse_attr.html#77-100
 *
 */
function genesis_skiplinks_markup() {

	add_filter( 'genesis_attr_nav-primary', 'genesis_skiplinks_attr_nav_primary' );
	add_filter( 'genesis_attr_content', 'genesis_skiplinks_attr_content' );
	add_filter( 'genesis_attr_sidebar-primary', 'genesis_skiplinks_attr_sidebar_primary' );
	add_filter( 'genesis_attr_sidebar-secondary', 'genesis_skiplinks_attr_sidebar_secondary' );
	add_filter( 'genesis_attr_footer-widgets', 'genesis_skiplinks_attr_footer_widgets' );

}

/**
 * Add ID markup to primary navigation
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return $attributes plus id and aria-label
 *
 */
function genesis_skiplinks_attr_nav_primary( $attributes ) {
	$attributes['id']         = 'genesis-nav-primary';
	$attributes['aria-label'] = __( 'Main navigation', 'genesis' );

	return $attributes;
}

/**
 * Add ID markup to content area
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return $attributes plus id
 *
 */
function genesis_skiplinks_attr_content( $attributes ) {
	$attributes['id'] = 'genesis-content';

	return $attributes;
}

/**
 * Add ID markup to primary sidebar
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return $attributes plus id
 *
 */
function genesis_skiplinks_attr_sidebar_primary( $attributes ) {
	$attributes['id'] = 'genesis-sidebar-primary';

	return $attributes;
}

/**
 * Add ID markup to secondary sidebar
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return $attributes plus id
 *
 */
function genesis_skiplinks_attr_sidebar_secondary( $attributes ) {
	$attributes['id'] = 'genesis-sidebar-secondary';

	return $attributes;
}

/**
 * Add ID markup to footer widget area
 *
 * @since 2.2.0
 *
 * @param array $attributes Existing attributes.
 *
 * @return $attributes plus id
 *
 */
function genesis_skiplinks_attr_footer_widgets( $attributes ) {
	$attributes['id'] = 'genesis-footer-widgets';

	return $attributes;
}

