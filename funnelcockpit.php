<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://funnelcockpit.com
 * @since             1.0.0
 * @package           funnelcockpit
 *
 * @wordpress-plugin
 * Plugin Name:       FunnelCockpit
 * Plugin URI:        https://funnelcockpit.com
 * Description:       Die All-In-One Lösung für den Aufbau von Funnels, Seiten und Conversion-Optimierung via Splittests, Maustracking und mehr.
 * Version:           1.3.3
 * Author:            FunnelCockpit
 * Author URI:        https://funnelcockpit.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       funnelcockpit
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-funnelcockpit-activator.php
 */
function activate_funnelcockpit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-funnelcockpit-activator.php';
	FunnelCockpit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-funnelcockpit-deactivator.php
 */
function deactivate_funnelcockpit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-funnelcockpit-deactivator.php';
	FunnelCockpit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_funnelcockpit' );
register_deactivation_hook( __FILE__, 'deactivate_funnelcockpit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-funnelcockpit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_funnelcockpit() {

	$plugin = new FunnelCockpit();
	$plugin->run();

}
run_funnelcockpit();
