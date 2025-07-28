<?php

/**
 * Helper functions
 *
 * @link       https://jruns.github.io/
 * @since      0.1.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 */

/**
 * Helper functions
 *
 * @since      0.1.0
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 * @author     Jason Schramm <jason.runs@proton.me>
 */
class Wp_Utilities_Helper {

	private static $allowed_conditionals = array(
		'is_home', 'is_front_page', 'is_single', 'is_page', 'is_category', 'is_tag', 'has_tag', 'is_tax', 'has_term', 'is_author', 'is_date', 'is_year', 'is_month', 'is_day', 'is_time', 'is_new_day', 'is_archive', 'is_search', 'is_404', 'is_paged', 'is_attachment', 'is_singular', 'has_excerpt', 'is_multisite', 'is_main_site', 'is_user_logged_in' 
	);

	public function __construct() {
	}

	public function is_matched( $params ) {
		if ( is_string( $params ) ) {
			$params = array( $params );
		}
	}

}
