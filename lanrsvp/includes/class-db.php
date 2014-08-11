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
    const SEAT_TABLE_NAME = 'lanrsvp_seat';

    const DEBUG = true;

    public static function install () {
        /** @var $wpdb WPDB */

        global $wpdb;

        $user_sql = sprintf ("CREATE TABLE %s (
                user_id MEDIUMINT NOT NULL AUTO_INCREMENT,
                email VARCHAR(128) NOT NULL,
                full_name VARCHAR(64) NOT NULL,
                password CHAR(32) NOT NULL,
                registration_date TIMESTAMP DEFAULT NOW(),
                PRIMARY KEY  (user_id),
                UNIQUE KEY email (email)
            );",
            $wpdb->prefix . self::USER_TABLE_NAME
        );

        $event_sql = sprintf ("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL AUTO_INCREMENT,
                is_active TINYINT(1) NOT NULL,
                event_title VARCHAR(64) NOT NULL,
                start_date DATETIME NOT NULL,
                end_date DATETIME,
                min_attendees SMALLINT NOT NULL,
                max_attendees SMALLINT NOT NULL,
                has_seatmap TINYINT(1) NOT NULL,
                PRIMARY KEY  (event_id)
            );",
            $wpdb->prefix . self::EVENT_TABLE_NAME
        );

        $seat_sql = sprintf ("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL,
                seat_row SMALLINT NOT NULL,
                seat_column SMALLINT NOT NULL,
                PRIMARY KEY  (event_id, seat_row, seat_column),
                FOREIGN KEY (event_id) REFERENCES %s (event_id)
            );",
            $wpdb->prefix . self::SEAT_TABLE_NAME,
            $wpdb->prefix . self::EVENT_TABLE_NAME
        );

        $attendee_sql = sprintf ("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL,
                user_id MEDIUMINT NOT NULL,
                seat_row SMALLINT,
                seat_column SMALLINT,
                registration_date TIMESTAMP DEFAULT NOW(),
                PRIMARY KEY (event_id, user_id),
                FOREIGN KEY (event_id) REFERENCES %s (event_id),
                FOREIGN KEY (user_id) REFERENCES %s (user_id),
                FOREIGN KEY (event_id, seat_row, seat_column) REFERENCES %s (event_id, seat_row, seat_column)
            );",
            $wpdb->prefix . self::ATTENDEE_TABLE_NAME,
            $wpdb->prefix . self::EVENT_TABLE_NAME,
            $wpdb->prefix . self::USER_TABLE_NAME,
            $wpdb->prefix . self::SEAT_TABLE_NAME
        );

        require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta ( $user_sql );
        dbDelta ( $event_sql );
        dbDelta ( $seat_sql );
        dbDelta ( $attendee_sql );
    }

    public static function uninstall() {
        /** @var $wpdb WPDB */
        global $wpdb;
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::ATTENDEE_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::SEAT_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::EVENT_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::USER_TABLE_NAME));
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
            "SELECT
              a.user_id AS 'User ID',
              b.full_name AS 'Full name',
              a.seat_row AS 'Row',
              a.seat_column AS 'Column',
              a.registration_date AS 'Registration date'
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
        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;
        return $wpdb->get_results(
            "SELECT a.*,COUNT(*) AS 'attendees_registered'
             FROM $event_table_name a JOIN $attendee_table_name b ON a.event_id = b.event_id
             GROUP BY a.event_id;"
        );
    }

    public static function get_users() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        return $wpdb->get_results("SELECT user_id, email, full_name, registration_date FROM $user_table_name");
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

    /**
     * @param $event
     */
    public static function createEvent($event) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $type = isset ($event['type']);

        $wpdb->query('START TRANSACTION');

        try {

            $data = array(
                'event_title' => $event['title'],
                'start_date' => $event['start_date'],
                'type' => $event['type']
            );
            $format = array('%s', '%s', '%s');

            if ($type == 'general') {
                $data['min_attendees'] = intval($event['min_attendees']);
                $data['max_attendees'] = intval($event['max_attendees']);
                array_push($format, '%d');
                array_push($format, '%d');
            }

            if ( isset ( $event['end_date'] ) ) {
                $data['end_date'] = $event['end_date'];
                array_push($format, '%s');
            }

            $wpdb->insert(
                $wpdb->prefix . self::EVENT_TABLE_NAME,
                $data,
                $format
            );

            $event_id = $wpdb->insert_id;
            if (is_int($event_id)) {
                if ($type == 'seatmap') {
                    foreach ($event['seatmap'] as $row => $cols) {
                        if (is_array($cols)) {
                            foreach ($cols as $col => $cell) {
                                if (is_array($cell)) {
                                    $status = $cell['status'];
                                    $wpdb->insert(
                                        $wpdb->prefix . self::SEAT_TABLE_NAME,
                                        array(
                                            'event_id' => $event_id,
                                            'seat_row' => $row,
                                            'seat_column' => $col,
                                            'start_status' => $status
                                        ),
                                        array('%d', '%d', '%d', '%s')
                                    );
                                }
                            }
                        }
                    }
                }
            } else {
                $wpdb->query('ROLLBACK');
                return "There was an error creating the event. Contact plugin author.";
            }
            $wpdb->query('COMMIT');
            return $event_id;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return "SQL error: $e";
        }
    }
} 