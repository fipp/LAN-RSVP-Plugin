<?php
/*  Copyright 2014 Terje Ness Andersen (email : terje.andersen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Plugin Name: LAN
 */

global $lanrsvp_db_version;
$lanrsvp_db_version = '1.0';

function lanrsvp_db_install () {
  global $wpdb;

  $user_sql = sprintf ("CREATE TABLE %s (
    user_id MEDIUMINT NOT NULL AUTO_INCREMENT,
    email VARCHAR(128) NOT NULL,
    full_name VARCHAR(64) NOT NULL,
    password CHAR(32) NOT NULL,
    PRIMARY KEY  (user_id),
    UNIQUE KEY email (email)
  );", $wpdb->prefix . 'lanrsvp_user');

  $event_sql = sprintf ("CREATE TABLE %s (
    event_id MEDIUMINT NOT NULL AUTO_INCREMENT,
    from_date DATETIME NOT NULL,
    to_date DATETIME NOT NULL,
    min_seats SMALLINT DEFAULT 0 NOT NULL,
    max_seats SMALLINT DEFAULT 0 NOT NULL,
    PRIMARY KEY  (event_id)
  );", $wpdb->prefix . 'lanrsvp_event');

  require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta ( $user_sql );
  dbDelta ( $event_sql );
}


/*
  $wpdb->insert ( 
    $wpdb->prefix . 'lanrsvp_user',
    array(
      'email' => 'terje.andersen@gmail.com',
      'full_name' => 'Terje Ness Andersen',
      'password' => wp_hash_password ( 'foobar' )
    )
  );

  $wpdb->insert ( 
    $wpdb->prefix . 'lanrsvp_event',
    array(
      'from_date' => '2014-05-30 18:00:00',
      'to_date' => '2014-06-01 18:00:00'
    )
  );
function save_error () {
  replace_option('plugin_error',  ob_get_contents());
}
add_action('activated_plugin', 'save_error');
echo get_option('plugin_error');
*/

register_activation_hook( __FILE__, 'lanrsvp_db_install' );

?>
