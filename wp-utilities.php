<?php

/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jruns.github.io/
 * @since             0.1.0
 * @package           Wp_Utilities
 *
 * @wordpress-plugin
 * Plugin Name:       WP Performance Utilities 
 * Plugin URI:        https://github.com/jruns/wp-utilities
 * Description:       Utilities to improve the performance of your WordPress site.
 * Version:           0.1.0
 * Author:            Jason Schramm
 * Author URI:        https://jruns.github.io//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-utilities
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_UTILITIES_VERSION', '0.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-utilities-activator.php
 */
function activate_wp_utilities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-utilities-activator.php';
	Wp_Utilities_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-utilities-deactivator.php
 */
function deactivate_wp_utilities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-utilities-deactivator.php';
	Wp_Utilities_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_utilities' );
register_deactivation_hook( __FILE__, 'deactivate_wp_utilities' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-utilities.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_wp_utilities() {

	$plugin = new Wp_Utilities();
	$plugin->run();

}
run_wp_utilities();
