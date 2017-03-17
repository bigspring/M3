<?php
/**
 * Collection of functions to handle all hooks required for Monolith
 * @license MIT http://opensource.org/licenses/MIT
 * @package monolith
 */

if ( ! function_exists( 'login_css' ) ) {
	/**
	 * Custom WordPress Login styling, use the login.css to style the WP login page with site logo etc...
	 * @return void
	 */
	function login_css() {
		wp_enqueue_style( 'login_css', get_template_directory_uri() . '/assets/css/login.css' );
	}
	
	add_action( 'login_head', 'login_css' );
}

if ( ! function_exists( 'wpb_imagelink_setup' ) ) {
	/**
	 * Stop WP from automatically linking images to themselves
	 * @return void
	 */
	function wpb_imagelink_setup() {
		$image_set = get_option( 'image_default_link_type' );
		
		if ( $image_set !== 'none' ) {
			update_option( 'image_default_link_type', 'none' );
		}
	}
	
	add_action( 'admin_init', 'wpb_imagelink_setup', 10 );
}

if ( ! function_exists( 'get_address' ) ) {
	/**
	 * Returns the address set in Monolith Contact Settings
	 *
	 * @param array|null $details
	 *
	 * @return array
	 */
	function get_address( $details = null ) {
		if ( ! $details ) {
			$details = array(
				'address_1',
				'address_2',
				'address_3',
				'city',
				'county',
				'country',
				'postcode'
			);
		}
		
		foreach ( $details as &$detail ) {
			if ( get_option( 'monolith_' . $detail ) ) {
				$detail = get_option( 'monolith_' . $detail );
			} else {
				unset( $detail );
			}
		}
		
		return array_values( $details );
	}
}

/**
 * Display excerpt by default
 */
add_filter( 'default_hidden_meta_boxes', function ( $hidden, $screen ) {
	if ( 'post' == $screen->base || 'page' == $screen->base ) {
		$hidden = array(
			'slugdiv',
			'trackbacksdiv',
			'postcustom',
			'commentstatusdiv',
			'commentsdiv',
			'authordiv',
			'revisionsdiv'
		);
	}
	
	// removed 'postcustom',
	return $hidden;
}, 10, 2 );

// create custom gravity forms error message
add_filter( 'gform_validation_message', function ( $message, $form ) {
	return "<div class='validation_error'>" . __( 'Sorry, there was a problem with the form.  Check the fields and try again.', 'monolith' ) . '</div>';
}, 10, 2 );

/**
 * Add browserSync code before closing body tag
 */
add_action( 'foundationpress_before_closing_body', function () {
	
	if ( ! defined( 'ENVIRONMENT' ) || ENVIRONMENT !== 'development' ) {
		return false;
	}
	
	echo "<script id=\"__bs_script__\">//<![CDATA[
		document.write(\"<script async src='http://HOST:3000/browser-sync/browser-sync-client.js?v=2.17.0'><\/script>\".replace(\"HOST\", location.hostname));
		//]]></script>";
}, 99 );

/**
 * Add schema for miain navigation links
 */
add_filter( 'nav_menu_link_attributes', 'add_attribute', 10, 3 );
function add_attribute( $atts, $item, $args )
{
	$atts['itemprop'] = 'url';
	return $atts;
}