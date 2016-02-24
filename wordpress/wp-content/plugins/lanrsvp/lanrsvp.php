<?php
/**
 * LAN Party Events Plugin
 *
 * An event registration system for LAN Parties.
 *
 * @package   lanrsvp
 * @author    Terje Ness Andersen <terje.andersen@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Terje Ness Andersen
 *
 * @wordpress-plugin
 * Plugin Name:       LAN Party Events Plugin
 * Description:       An event registration system for LAN Parties.
 * Version:           0.1
 * Author:            Terje Ness Andersen
 * Author URI:        http://it.terjeandersen.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/fipp/LAN-RSVP-Plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Common classes
 *----------------------------------------------------------------------------*/
require_once( ABSPATH . 'wp-includes/class-phpass.php');
if (!is_admin()) {
    // Required to use wp-list-table outside admin context
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	if ( ! class_exists('WP_Screen') ) {
		require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	}
	require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
}


require_once( plugin_dir_path( __FILE__ ) . 'includes/class-db.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/attendees-table.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/events-table.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/users-table.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/event-history-table.php' );


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-lanrsvp.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'LanRsvp', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'LanRsvp', 'deactivate' ) );
// register_uninstall_hook( __FILE__, array( 'LanRsvp', 'uninstall' ) );

add_action( 'plugins_loaded', array( 'LanRsvp', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
// if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-lanrsvp-admin.php' );
	add_action( 'plugins_loaded', array( 'LanRsvpAdmin', 'get_instance' ) );

}