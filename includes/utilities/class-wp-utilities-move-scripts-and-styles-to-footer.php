<?php

class Wp_Utilities_Move_Scripts_And_Styles_To_Footer {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
		$this->settings = array(
			'scripts'	=> array(),
			'styles'	=> array()
		);

		$this->settings = apply_filters( 'wp_utilities_scripts_and_styles_to_move_to_footer', $this->settings );
	}

	public function process_moves( $buffer ) {
		// Filter out moves that are not valid for the current page, based on conditional matches
		$this->settings['scripts'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['scripts'] );
		$this->settings['styles'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['styles'] );

		// Process moves
		if ( ! empty( $this->settings['scripts'] ) ) {
			// Process all script tags
			$match_args = array(
				'tag_regex'			=> '/<script[^>]*>[\s\S]*?<\/[^>]*script[^>]*>\n?/im',
				'match_settings'	=> $this->settings['scripts'],
				'match_types'		=> array( 'id', 'src', 'code' )
			);
			$buffer = $this->process_buffer_moves( $buffer, $match_args );
		}

		if ( ! empty( $this->settings['styles'] ) ) {
			// Process all stylesheet link and style tags
			$match_args = array(
				'tag_regex'			=> '/<link[^>]*rel=[\\\'\"]stylesheet[\\\'\"][^>]*>\n?|<style[^>]*>[\s\S]*?<\/[^>]*style[^>]*>\n?/im',
				'match_settings'	=> $this->settings['styles'],
				'match_types'		=> array( 'id', 'href', 'code' )
			);
			$buffer = $this->process_buffer_moves( $buffer, $match_args );
		}

		return $buffer;
	}

	public function process_buffer_moves( $buffer, $args ) {
		extract( $args );

		$match_strings = array();
		foreach ( $match_types as $type ) {
			$match_strings[ $type ] = addcslashes( join( "|", array_column( $match_settings, $type ) ), '/' );
		}

		$moves_queue = array();

		$buffer = preg_replace_callback( 
			$tag_regex, 
			function( $matches ) use( $match_settings, $match_types, $match_strings, &$moves_queue )  {
				foreach ( $match_types as $type ) {
					switch( $type ) {
						case 'id':
						case 'src':
						case 'href':
							if ( ! empty( $match_strings[ $type ] ) && preg_match( '/' . $type . '=[\\\'\"][^\\\'\"]*(' . $match_strings[ $type ] . ')[^\\\'\"]*[\\\'\"]/i', $matches[0] ) ) {
								$moves_queue[] = $matches[0];
								return '';
							}
							break;
						case 'code':
							if ( ! empty( $match_strings[ $type ] ) && preg_match( '/(' . $match_strings[ $type ] . ')/im', $matches[0] ) ) {
								$moves_queue[] = $matches[0];
								return '';
							}
							break;
						default:
							break;
					}
				}
				
				return $matches[0];
			},
			$buffer
		);

		// Add tags queued for movement to the footer.
		foreach( $moves_queue as $tag_to_move ) {
			$buffer = str_replace( '</body>', $tag_to_move . '</body>', $buffer );
		}

		return $buffer;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.3.0
	 */
	public function run() {
		// Iterate over scripts and styles to move to the footer
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_moves' ), 6 );
	}
}
