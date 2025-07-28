<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://jruns.github.io/
 * @since      0.1.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 * @author     Jason Schramm <jason.runs@proton.me>
 */
class Wp_Utilities_Html_Buffer {

	private $buffer;

	public function __construct() {
		add_action( 'init', array( $this, 'start_buffer' ) );
		add_action( 'shutdown', array( $this, 'end_buffer' ), 9 );
	}

	static function process_buffer( $buffer ) {
		$buffer = apply_filters( 'wp_utilities_modify_final_output', $buffer );
		return $buffer;
	}

	public function start_buffer() {
		ob_start( array( 'self', 'process_buffer' ) );
	}

	public function end_buffer() {
		$this->buffer = ob_get_clean();
	}

}
