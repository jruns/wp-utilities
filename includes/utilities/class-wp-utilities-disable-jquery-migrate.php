<?php

class Wp_Utilities_Disable_Jquery_Migrate {

	public function __construct() {
	}

	public function remove_jquery_migrate( $scripts ) {
		if ( ! is_admin() && isset( $scripts->registered[ 'jquery' ] ) ) {
			$script = $scripts->registered[ 'jquery' ];

			if ( $script->deps ) {
				$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
			}
		}
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    1.0.0
	 */
	public function run() {
		add_action( 'wp_default_scripts', array( $this, 'remove_jquery_migrate' ) );
	}
}
