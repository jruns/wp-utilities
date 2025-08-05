<?php

class Wp_Utilities_Remove_Scripts_And_Styles {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
		$this->settings = array(
			'scripts'	=> array(),
			'styles'	=> array()
		);

		$this->settings = apply_filters( 'wp_utilities_scripts_and_styles_to_remove', $this->settings );
	}

	public function process_removals( $buffer ) {
		// Filter out removals that are not valid for the current page, based on conditional matches
		$this->settings['scripts'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['scripts'] );
		$this->settings['styles'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['styles'] );

		// Process removals
		if ( ! empty( $this->settings['scripts'] ) ) {
			$match_ids = join( "|", array_column( $this->settings['scripts'], 'id' ) );
			$match_sources = addcslashes( join( "|", array_column( $this->settings['scripts'], 'src' ) ), '/' );
			$match_code = addcslashes( join( "|", array_column( $this->settings['scripts'], 'code' ) ), '/' );

			// Process all script tags
			$buffer = preg_replace_callback( 
				'/<script[^>]*>[\s\S]*?<\/[^>]*script[^>]*>/im', 
				function( $matches ) use( $match_ids, $match_sources, $match_code )  {

					// Remove matching ids
					if ( ! empty( $match_ids ) && preg_match( '/id=[\\\'\"][^\\\'\"]*(' . $match_ids . ')[^\\\'\"]*[\\\'\"]/i', $matches[0] ) ) {
						return '';
					}

					// Remove matching src attributes
					if ( ! empty( $match_sources ) && preg_match( '/src=[\\\'\"][^\\\'\"]*(' . $match_sources . ')[^\\\'\"]*[\\\'\"]/i', $matches[0] ) ) {
						return '';
					}

					// Remove matching inline javascript code
					if ( ! empty( $match_code ) && preg_match( '/(' . $match_code . ')/im', $matches[0] ) ) {
						return '';
					}
					
					return $matches[0];
				},
				$buffer
			);
		}

		if ( ! empty( $this->settings['styles'] ) ) {
			$match_ids = join( "|", array_column( $this->settings['styles'], 'id' ) );
			$match_sources = addcslashes( join( "|", array_column( $this->settings['styles'], 'href' ) ), '/' );
			$match_code = addcslashes( join( "|", array_column( $this->settings['styles'], 'code' ) ), '/' );

			// Process all stylesheet link tags
			$buffer = preg_replace_callback( 
				'/<link[^>]*rel=[\\\'\"]stylesheet[\\\'\"][^>]*>/i', 
				function( $matches ) use( $match_ids, $match_sources )  {

					// Remove matching ids
					if ( ! empty( $match_ids ) && preg_match( '/id=[\\\'\"][^\\\'\"]*(' . $match_ids . ')[^\\\'\"]*[\\\'\"]/i', $matches[0] ) ) {
						return '';
					}

					// Remove matching hrefs
					if ( ! empty( $match_sources ) && preg_match( '/href=[\\\'\"][^\\\'\"]*(' . $match_sources . ')[^\\\'\"]*[\\\'\"]/i', $matches[0] ) ) {
						return '';
					}
					
					return $matches[0];
				},
				$buffer
			);

			// Process all stylesheet style tags
			$buffer = preg_replace_callback( 
				'/<style[^>]*>[\s\S]*?<\/[^>]*style[^>]*>/im', 
				function( $matches ) use( $match_ids, $match_code )  {
					// Remove matching ids
					if ( ! empty( $match_ids ) && preg_match( '/id=[\\\'\"][^\\\'\"]*(' . $match_ids . ')[^\\\'\"]*[\\\'\"]/im', $matches[0] ) ) {
						return '';
					}

					// Remove matching inline stylesheet code
					if ( ! empty( $match_code ) && preg_match( '/(' . addcslashes( $match_code, '.-' ) . ')/im', $matches[0] ) ) {
						return '';
					}
					
					return $matches[0];
				},
				$buffer
			);

		}

		return $buffer;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.1.0
	 */
	public function run() {
		// Iterate over scripts and styles to remove
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_removals' ) );
	}
}
