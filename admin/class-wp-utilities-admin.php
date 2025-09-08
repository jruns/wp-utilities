<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jruns.github.io/
 * @since      0.1.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/admin
 * @author     Jason Schramm <jason.runs@proton.me>
 */
class Wp_Utilities_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function add_options_page() {
		add_options_page(
			'WP Performance Utilities',
			'WP Performance Utilities',
			'manage_options',
			'wp-utilities',
			array( $this, 'render_options_page' )
		);
	}
	
    public function registersettings() {
        register_setting( 'wp-utilities', 'wp_utilities_disable_jquery_migrate');
    }

	public function render_options_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wp-utilities-admin-options-display.php' );
	}

	public function add_plugin_action_links( array $links ) {
		$settings_url = menu_page_url( 'wp-utilities', false );
		return array_merge( array(
			'settings' => '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'wp-utilities' ) . '</a>',
		), $links );
	}
}
