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
			// Process all script tags
			$match_args = array(
				'tag_regex'			=> '/<script[^>]*>[\s\S]*?<\/[^>]*script[^>]*>\n?/im',
				'match_settings'	=> $this->settings['scripts'],
				'match_types'		=> array( 'id', 'src', 'code' ),
				'operation'			=> 'remove'
			);
			$buffer = Wp_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );
		}

		if ( ! empty( $this->settings['styles'] ) ) {
			// Process all stylesheet link and style tags
			$match_args = array(
				'tag_regex'			=> '/<link[^>]*rel=[\\\'\"]stylesheet[\\\'\"][^>]*>\n?|<style[^>]*>[\s\S]*?<\/[^>]*style[^>]*>\n?/im',
				'match_settings'	=> $this->settings['styles'],
				'match_types'		=> array( 'id', 'href', 'code' ),
				'operation'			=> 'remove'
			);
			$buffer = Wp_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );
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
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_removals' ), 15 );
	}
}
