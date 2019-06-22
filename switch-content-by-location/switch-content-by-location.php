<?php
/**
 *
 * @link              https://github.com/mrHiker/WP-Switch-Content-by-Location
 * @since             0.1
 * @package           WP-Switch-Content-By-Location
 *
 * Plugin Name:       WP Switch Content By Location
 * Plugin URI:        
 * Description:       
 * Version:           0.1
 * Author:            Danil Davletov
 * Author URI:        https://github.com/mrHiker/WP-Switch-Content-by-Location
 * License:           MIT
 * License URI:       
 * Text Domain:       switch-content-by-location
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if(!defined('ABSPATH')) { exit; }

/**
 * Current version and constants.
 */
define( 'WPSCBL_VERSION', '0.1' );
define( 'WPSCBL', 'WP Switch Content By Location' );


/**
 * Include ReduxFramework
 */
if ( !isset( $wsb_redux ) && file_exists( dirname( __FILE__ ) . '/includes/redux-config.php' ) ) {
    require_once( dirname( __FILE__ ) . '/includes/redux-config.php' );
}

/**
 * Enable shortcodes for ACF simple text fields
 */
if ($wpscbl_options['enable-shortcodes-in-acf']){
	add_filter('acf/format_value/type=textarea', 'do_shortcode');
}

/**
 * Register Shortcode to define replacable content
 */
function wpscbl_shortcode( $atts, $content = null ) {

	//plugin options from redux-config;
	global $wpscbl_options;
	$opt = $wpscbl_options;

	//filter default shortcode attributes
	$a = shortcode_atts( array(	'name' => ''	), $atts );
	if ( $a['name'] == '' ) return $content;

	// get the get parameter from url
	$the_get_val = $_GET[ $opt['get-param'] ];
	if ( !$the_get_val ) return $content;

	//get the current get value index from param values list
	// we'll use that index to select the right version of content
	$the_get_index = array_search( $the_get_val, $opt['get-param-values'] );
	if ( $the_get_index === FALSE ) return $content;

	/*
	 find the block by name and choose the right version of content
	*/
	$out = $content;
	$i = 0;
	foreach ($opt['content_blocks']['content-block-name'] as $name) {

		//check if we have options for the content block with current name
		if ( $name == $a['name'] && $opt['content_blocks']['content-block-values'][$i][$the_get_index] ){

			//store the corresponding version of content
			$out = $opt['content_blocks']['content-block-values'][$i][$the_get_index];

		}
		$i++;
	
	}

	return $out;

}
add_shortcode( 'wpscbl_content', 'wpscbl_shortcode' );