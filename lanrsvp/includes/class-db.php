<?php
/**
 * Created by PhpStorm.
 * User: Terje
 * Date: 28.05.2014
 * Time: 15:26
 */

class DB {
    const EVENT_TABLE_NAME = 'lanrsvp_event';
    const USER_TABLE_NAME = 'lanrsvp_user';

    public static function install () {
        /** @var $wpdb WPDB */

        global $wpdb;

        $user_sql = sprintf ("CREATE TABLE %s (
            user_id MEDIUMINT NOT NULL AUTO_INCREMENT,
            email VARCHAR(128) NOT NULL,
            full_name VARCHAR(64) NOT NULL,
            password CHAR(32) NOT NULL,
            PRIMARY KEY  (user_id),
            UNIQUE KEY email (email)
        );", $wpdb->prefix . self::USER_TABLE_NAME);

        $event_sql = sprintf ("CREATE TABLE %s (
            event_id MEDIUMINT NOT NULL AUTO_INCREMENT,
            from_date DATETIME NOT NULL,
            to_date DATETIME NOT NULL,
            min_seats SMALLINT DEFAULT 0 NOT NULL,
            max_seats SMALLINT DEFAULT 0 NOT NULL,
            PRIMARY KEY  (event_id)
        );", $wpdb->prefix . self::EVENT_TABLE_NAME);

        require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta ( $user_sql );
        dbDelta ( $event_sql );
    }

    public static function uninstall() {
        /** @var $wpdb WPDB */
        global $wpdb;
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::EVENT_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::USER_TABLE_NAME));
    }

} 