<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jruns.github.io/
 * @since      1.0.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 * @author     Jason Schramm <jason.runs@proton.me>
 */
class Wp_Utilities {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Utilities_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The status of the HTML buffer.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      bool    $buffer_is_active    The current status of the HTML buffer.
	 */
	protected $buffer_is_active;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_UTILITIES_VERSION' ) ) {
			$this->version = WP_UTILITIES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-utilities';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->load_utilities();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Utilities_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Utilities_i18n. Defines internationalization functionality.
	 * - Wp_Utilities_Admin. Defines all hooks for the admin area.
	 * - Wp_Utilities_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-utilities-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-utilities-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-utilities-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-utilities-public.php';

		$this->loader = new Wp_Utilities_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Utilities_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Utilities_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Utilities_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'registersettings' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Utilities_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	private function utility_is_active( $className ) {
		$constant_name = strtoupper( $className );
		$option_name = strtolower( $className );

		if( defined( $constant_name ) ) {
			if ( constant( $constant_name ) ) {
				return true;
			}
		} else if ( $option_value = get_option( $option_name ) ) {
			return true;
		}

		return false;
	}

	
	/**
	 * Load enabled utilities
	 */
	private function load_utilities() {
		$utilities_dir = dirname( __FILE__ ) . '/utilities/';

		if ( is_dir( $utilities_dir ) ) {
			if ( $dh = opendir( $utilities_dir ) ) {
				while ( ( $file = readdir( $dh ) ) !== false ) {
					if ( $file == '.' || $file == '..' ) {
						continue;
					}

					$className = str_replace( array( 'class-', '-', '.php'), array( '', ' ', ''), $file );
					$className = str_replace( ' ', '_', ucwords( $className ) );

					if ( $this->utility_is_active( $className ) ) {
						include_once( $utilities_dir . $file );

						// Activate output buffer if utility requires it and it has not been activated already
						if( property_exists( $className, 'needs_html_buffer' ) && $className::$needs_html_buffer ) {
							$this->activate_html_buffer();
						}

						// Activate on init so we can access filters
						add_action( 'init', function() use ( $utilities_dir, $file, $className ) {
							$utility = new $className;
							$utility->run();
						} );
					}
				}
				closedir( $dh );
			}
		}
	}

	/**
	 * Activate HTML Buffer if required by an active utility
	 */
	private function activate_html_buffer() {
		if ( ! $this->buffer_is_active ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-wp-utilities-html-buffer.php';
			new Wp_Utilities_Html_Buffer();

			$this->buffer_is_active = true;
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Utilities_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}