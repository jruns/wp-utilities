<?php

class Wp_Utilities_Delay_Scripts {

	private $settings;

	public static $needs_html_buffer = true;

	public const EXCLUSIONS = array( 'nodelay', 'nowprocket', 'data-pagespeed-no-defer' );

	public function __construct() {
		$this->settings = array(
			'scripts'	=> array()
		);

		$this->settings = apply_filters( 'wp_utilities_scripts_to_delay', $this->settings );
	}

	public function process_delays( $buffer ) {
		// Filter out delays that are not valid for the current page, based on conditional matches
		$this->settings['scripts'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['scripts'] );

		// Process delays
		if ( ! empty( $this->settings['scripts'] ) ) {
			// Process all script tags
			$match_args = array(
				'tag_type'			=> 'script',
				'match_settings'	=> $this->settings['scripts'],
				'match_types'		=> array( 'id', 'src', 'code' ),
				'operation'			=> 'delay',
				'exclusions'		=> self::EXCLUSIONS
			);

			$buffer = Wp_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );
		}

		return $buffer;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.4.0
	 */
	public function run() {
		// Iterate over scripts to delay
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_delays' ), 9 );
	}
}
