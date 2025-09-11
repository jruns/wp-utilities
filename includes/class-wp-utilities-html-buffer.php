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

}
