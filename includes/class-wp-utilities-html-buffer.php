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
			$matches = array_map( 
				function( $value ) { 
					return is_array( $value ) ? join( "|", $value ) : $value; 
				},
				array_column( $match_settings, $type ) 
			);
			$match_strings[ $type ] = addcslashes( join( "|", $matches ), '/' );
		}

		$moves_queue = array();

		$buffer = preg_replace_callback( 
			$tag_regex, 
			function( $matches ) use( $operation, $match_settings, $match_types, $match_strings, &$moves_queue )  {
				foreach ( $match_types as $type ) {
					switch( $type ) {
						case 'id':
						case 'src':
						case 'href':
							if ( ! empty( $match_strings[ $type ] ) && preg_match( '/' . $type . '=[\\\'\"][^\\\'\"]*(' . $match_strings[ $type ] . ')[^\\\'\"]*[\\\'\"]/i', $matches[0] ) ) {
								if ( $operation === 'move_to_footer' ) {
									$moves_queue[] = $matches[0];
								}
								return '';
							}
							break;
						case 'code':
							if ( ! empty( $match_strings[ $type ] ) && preg_match( '/(' . $match_strings[ $type ] . ')/im', $matches[0] ) ) {
								if ( $operation === 'move_to_footer' ) {
									$moves_queue[] = $matches[0];
								}
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
}
