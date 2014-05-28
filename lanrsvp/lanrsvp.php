<?php
/**
 * LAN RSVP Plugin
 *
 * A RSVP Plugin for LAN parties.
 *
 * @package   lanrsvp
 * @author    Terje Ness Andersen <terje.andersen@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Terje Ness Andersen
 *
 * @wordpress-plugin
 * Plugin Name:       LAN RSVP Plugin
 * Description:       A RSVP Plugin for LAN parties.
 * Version:           1.0.0
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
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-lanrsvp-admin.php' );
	add_action( 'plugins_loaded', array( 'LanRsvpAdmin', 'get_instance' ) );

}