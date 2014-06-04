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
    const ATTENDEE_TABLE_NAME = 'lanrsvp_attendee';
    const DEBUG = true;

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
            );",
            $wpdb->prefix . self::USER_TABLE_NAME
        );

        $event_sql = sprintf ("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL AUTO_INCREMENT,
                event_title VARCHAR(64) NOT NULL,
                from_date DATETIME NOT NULL,
                to_date DATETIME NOT NULL,
                min_seats SMALLINT DEFAULT 0 NOT NULL,
                max_seats SMALLINT DEFAULT 0 NOT NULL,
                PRIMARY KEY  (event_id)
            );",
            $wpdb->prefix . self::EVENT_TABLE_NAME
        );

        $attendee_sql = sprintf ("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL,
                user_id MEDIUMINT NOT NULL,
                seat MEDIUMINT,
                registration_date TIMESTAMP DEFAULT NOW(),
                PRIMARY KEY  (event_id, user_id),
                FOREIGN KEY (event_id) REFERENCES %s (event_id),
                FOREIGN KEY (user_id) REFERENCES %s (user_id)
            );",
            $wpdb->prefix . self::ATTENDEE_TABLE_NAME,
            $wpdb->prefix . self::EVENT_TABLE_NAME,
            $wpdb->prefix . self::USER_TABLE_NAME
        );

        require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta ( $user_sql );
        dbDelta ( $event_sql );
        dbDelta ( $attendee_sql );

        if (self::DEBUG) {
            self::populate_sample_data();
        }
    }

    public static function uninstall() {
        /** @var $wpdb WPDB */
        global $wpdb;
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::ATTENDEE_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::EVENT_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::USER_TABLE_NAME));
    }

    public static function populate_sample_data() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $sqlList = array(
            sprintf("INSERT INTO %s (email, full_name, password) VALUES('test1@example.com', 'Test 1', '%s');",
                $wpdb->prefix . self::USER_TABLE_NAME, wp_hash_password('test2password')),
            sprintf("INSERT INTO %s (email, full_name, password) VALUES('test2@example.com', 'Test 2', '%s');",
                $wpdb->prefix . self::USER_TABLE_NAME, wp_hash_password('test3password')),
            sprintf("INSERT INTO %s (email, full_name, password) VALUES('test3@example.com', 'Test 3', '%s');",
                $wpdb->prefix . self::USER_TABLE_NAME, wp_hash_password('test4password')),
            sprintf("INSERT INTO %s (email, full_name, password) VALUES('test4@example.com', 'Test 4', '%s');",
                $wpdb->prefix . self::USER_TABLE_NAME, wp_hash_password('test5password')),

            sprintf("INSERT INTO %s (event_title, from_date, to_date) VALUES('Meldal-LAN 2014', '2014-06-06 18:00:00', '2014-06-08 18:00:00');",
                $wpdb->prefix . self::EVENT_TABLE_NAME),

            sprintf("INSERT INTO %s (event_id, user_id) VALUES(1, 2);",
                $wpdb->prefix . self::ATTENDEE_TABLE_NAME),
            sprintf("INSERT INTO %s (event_id, user_id, seat) VALUES(1, 4, 52);",
                $wpdb->prefix . self::ATTENDEE_TABLE_NAME)

        );

        foreach ($sqlList as $sql) {
            $wpdb->query($sql);
        }
    }

    public static function get_attendees ($event_id) {
        if (!isset($event_id)) {
            return null;
        }

        /** @var $wpdb WPDB */
        global $wpdb;

        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;
        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT a.user_id AS 'User ID', b.full_name AS 'Full name', a.seat AS 'Chosen seat', a.registration_date AS 'Registration date'
             FROM $attendee_table_name a JOIN $user_table_name b ON (a.user_id = b.user_id)
             WHERE event_id = %s",
            $event_id
        ));
    }

    public static function get_event($event_id) {
        if (!isset($event_id)) {
            return null;
        }

        /** @var $wpdb WPDB */
        global $wpdb;

        $event_table_name = $wpdb->prefix . self::EVENT_TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $event_table_name WHERE event_id = %s",
            $event_id
        ));
    }

    public static function get_events() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $event_table_name = $wpdb->prefix . self::EVENT_TABLE_NAME;
        return $wpdb->get_results("SELECT * FROM $event_table_name");
    }

    public static function get_users() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        return $wpdb->get_results("SELECT user_id, email, full_name FROM $user_table_name");
    }

    public static function getPasswordHash ($user_id = null, $email = null) {
        if (!isset($user_id) && !isset($email)) {
            return null;
        }

        if (isset($user_id) && isset($email)) {
            return null;
        }

        /** @var $wpdb WPDB */
        global $wpdb;

        $sql = '';
        $table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        if (isset($user_id)) {
            $sql =  $wpdb->prepare(
                "SELECT password FROM $table_name WHERE user_id = %s",
                $user_id
            );
        } elseif (isset($email)) {
            $sql =  $wpdb->prepare(
                "SELECT password FROM $table_name WHERE email = %s",
                $email
            );
        }

        return $wpdb->get_results($sql);
    }

} 