<?php

/**
 * HTML Buffer for modifying html before its output
 *
 * @link       https://jruns.github.io/
 * @since      0.1.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 */

class Wp_Utilities_Html_Buffer {

	private $buffer;

	public function __construct() {
		add_action( 'init', array( $this, 'start_buffer' ) );
		add_action( 'shutdown', array( $this, 'end_buffer' ), 9 );
	}

	static function filter_buffer( $buffer ) {
		$buffer = apply_filters( 'wp_utilities_modify_final_output', $buffer );
		return $buffer;
	}

	public function start_buffer() {
		ob_start( array( 'self', 'filter_buffer' ) );
	}

	public function end_buffer() {
		$this->buffer = ob_get_clean();
	}

	/**
	 * Process buffer tag replacements
	 *
	 * @since    0.3.0
	 */
	public static function process_buffer_replacements( $buffer, $args ) {
		extract( $args );

		$match_strings = array();
		// Expand match strings into an OR statement
		foreach ( $match_types as $type ) {
			$match_array = array_map( 
				function( $value ) { 
					return is_array( $value ) ? join( "|", $value ) : $value; 
				},
				array_column( $match_settings, $type ) 
			);
			$match_strings[ $type ] = addcslashes( join( "|", $match_array ), '/()[]{}.,+*^$' );
		}

		if ( ! empty( $exclusions ) ) {
			$exclusions = join( '|', $exclusions );
		} else {
			$exclusions = null;
		}

		$moves_queue = array();

		$buffer = preg_replace_callback( 
			$tag_regex, 
			function( $matches ) use( $operation, $match_settings, $match_types, $match_strings, $exclusions, &$moves_queue )  {
				$tag_contents = $matches[0];

				foreach ( $match_types as $type ) {
					if ( 'code' === $type ) {
						$regex_string = '/(' . $match_strings[ $type ] . ')/im';
					} else {
						$regex_string = '/' . $type . '=[\\\'\"][^\\\'\"]*(' . $match_strings[ $type ] . ')[^\\\'\"]*[\\\'\"]/i';
					}

					if ( ! empty( $match_strings[ $type ] ) && preg_match( $regex_string, $tag_contents ) ) {
						if ( $operation === 'delay' ) {
							if ( empty( $exclusions ) || 1 !== preg_match( '/<script[^>]*' . $exclusions . '[^>]*>/im', $tag_contents ) ) {
								if ( 0 === preg_match( '/<script[^>]* defer[^>]*>/im', $tag_contents ) ) {
									$tag_contents = str_replace( '<script', '<script defer', $tag_contents );
								}
							}
						} else {
							if ( $operation === 'move_to_footer' ) {
								$moves_queue[] = $tag_contents;
							}

							// Remove existing tag
							$tag_contents = '';
						}

						// Stop checking match types because we found a match
						break;
					}
				}
				
				return $tag_contents;
			},
			$buffer
		);

		// Add tags queued for movement to the footer.
		foreach( $moves_queue as $tag_to_move ) {
			$buffer = str_replace( '</body>', $tag_to_move . '</body>', $buffer );
		}

		return $buffer;
	}
}
