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

    public static function install() {
        /** @var $wpdb WPDB */

        global $wpdb;

        $user_sql = sprintf("CREATE TABLE %s (
                user_id MEDIUMINT NOT NULL AUTO_INCREMENT,
                first_name VARCHAR(32) NOT NULL,
                last_name VARCHAR(32) NOT NULL,
                email VARCHAR(128) NOT NULL UNIQUE,
                password CHAR(48) NOT NULL,
                registration_date TIMESTAMP DEFAULT NOW(),
                activation_code CHAR(32) NOT NULL UNIQUE,
                is_activated ENUM('0','1') NOT NULL DEFAULT '0',
                PRIMARY KEY  (user_id)
            );",
            $wpdb->prefix . self::USER_TABLE_NAME
        );

        $event_sql = sprintf("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL AUTO_INCREMENT,
                is_active TINYINT(1) NOT NULL,
                event_title VARCHAR(64) NOT NULL,
                start_date DATETIME NOT NULL,
                end_date DATETIME,
                min_attendees SMALLINT NOT NULL,
                max_attendees SMALLINT NOT NULL,
                has_seatmap ENUM('0','1') NOT NULL,
                PRIMARY KEY  (event_id)
            );",
            $wpdb->prefix . self::EVENT_TABLE_NAME
        );

        $attendee_sql = sprintf("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL,
                user_id MEDIUMINT NOT NULL,
                registration_date TIMESTAMP DEFAULT NOW(),
                PRIMARY KEY (event_id, user_id),
                FOREIGN KEY (event_id) REFERENCES %s (event_id),
                FOREIGN KEY (user_id) REFERENCES %s (user_id)
            );",
            $wpdb->prefix . self::ATTENDEE_TABLE_NAME,
            $wpdb->prefix . self::EVENT_TABLE_NAME,
            $wpdb->prefix . self::USER_TABLE_NAME
        );

        $seat_sql = sprintf("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL,
                user_id MEDIUMINT,
                seat_row SMALLINT NOT NULL,
                seat_column SMALLINT NOT NULL,
                PRIMARY KEY (event_id, seat_row, seat_column),
                FOREIGN KEY (event_id) REFERENCES %s (event_id),
                FOREIGN KEY (user_id) REFERENCES %s (user_id)

            );",
            $wpdb->prefix . self::SEAT_TABLE_NAME,
            $wpdb->prefix . self::EVENT_TABLE_NAME,
            $wpdb->prefix . self::ATTENDEE_TABLE_NAME
        );

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($user_sql);
        dbDelta($event_sql);
        dbDelta($attendee_sql);
        dbDelta($seat_sql);
    }

    public static function uninstall() {
        /** @var $wpdb WPDB */
        global $wpdb;
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::SEAT_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::ATTENDEE_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::EVENT_TABLE_NAME));
        $wpdb->query(sprintf("DROP TABLE IF EXISTS %s", $wpdb->prefix . self::USER_TABLE_NAME));
    }

    public static function create_user ($user) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM wp_lanrsvp_user WHERE email = %s",
                $user['email']),
            ARRAY_A
        );

        if ($existing['COUNT(*)'] > 0) {
            return "The user already exists.";
        }

        $data = [
            'first_name' => $user['firstName'],
            'last_name' =>  $user['lastName'],
            'email' =>  $user['email'],
            'password' => wp_hash_password($user['password']),
            'activation_code' => $user['activation_code']
        ];

        $wpdb->insert($wpdb->prefix . self::USER_TABLE_NAME, $data, array('%s', '%s', '%s', '%s', '%s'));

        if (!is_numeric($wpdb->insert_id)) {
            $errorMessage = "A new user could not be created! Please contact the system administrator.";
            throw new Exception($errorMessage);
        }
    }

    public static function activate_user($user) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $res = $wpdb->update(
            $wpdb->prefix . self::USER_TABLE_NAME,
            array('is_activated' => '1'),
            array(
                'email' => $user['email'],
                'activation_code' => $user['activationCode']
            ),
            array('%s'),
            array('%s', '%s')
        );

        if ($res == false || $res == 0) {
            $errorMessage = "Activation failed. Did you enter the correct code? If so, please contact the system administrator.";
            throw new Exception($errorMessage);
        }

    }

    public static function get_attendees($event_id) {
        if (!isset($event_id)) {
            return null;
        }

        /** @var $wpdb WPDB */
        global $wpdb;

        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;
        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT
              a.user_id,
              CONCAT(b.first_name, ' ', b.last_name) AS full_name,
              b.email,
              c.seat_row,
              c.seat_column,
              a.registration_date
             FROM
              $attendee_table_name a
              JOIN $user_table_name b ON (a.user_id = b.user_id)
              JOIN $seat_table_name c ON (a.event_id = c.event_id AND a.user_id = c.user_id)
             WHERE a.event_id = %d",
            $event_id
        ));
    }

    public static function get_attendee($event_id, $user_id) {
        if (!isset($event_id) || !isset($user_id)) {
            return null;
        }

        /** @var $wpdb WPDB */
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT *
              FROM wp_lanrsvp_attendee a JOIN wp_lanrsvp_user b ON a.user_id = b.user_id
              WHERE a.event_id = %d AND a.user_id = %d",
            $event_id,
            $user_id
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
            "SELECT * FROM $event_table_name WHERE event_id = %d",
            $event_id
        ));
    }

    public static function get_event_seatmap($event_id) {
        if (!isset($event_id)) {
            return null;
        }

        /** @var $wpdb WPDB */
        global $wpdb;

        $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $seat_table_name WHERE event_id = %d",
            $event_id
        ));
    }

    public static function get_events() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $event_table_name = $wpdb->prefix . self::EVENT_TABLE_NAME;
        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;

        $res = $wpdb->get_results(
            "SELECT
              a.*,
              (SELECT COUNT(*) FROM $attendee_table_name WHERE event_id = a.event_id) AS 'attendees_registered'
            FROM
              $event_table_name a;"
        );

        return $res;
    }

    public static function get_users() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        return $wpdb->get_results("SELECT user_id, email, full_name, registration_date FROM $user_table_name");
    }

    public static function get_user($user_id = null, $email = null) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $user_table = $wpdb->prefix . self::USER_TABLE_NAME;
        if (isset($user_id)) {
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $user_table WHERE user_id = %d",
                $user_id
            ), ARRAY_A);
        } elseif (isset($email)) {
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $user_table WHERE email = %s",
                $email
            ), ARRAY_A);
        }
    }

    public static function get_password_hash($user_id = null, $email = null) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $sql = '';
        $table_name = $wpdb->prefix . self::USER_TABLE_NAME;

        if (isset($user_id)) {
            $sql = $wpdb->prepare(
                "SELECT password FROM $table_name WHERE user_id = %d AND is_activated = '1'",
                $user_id
            );
        } elseif (isset($email)) {
            $sql = $wpdb->prepare(
                "SELECT password FROM $table_name WHERE email = %s AND is_activated = '1'",
                $email
            );
        }

        return $wpdb->get_results($sql);
    }

    public static function set_password ($user_id = null, $email = null, $newPassword) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $res = false;
        if (isset($user_id)) {
            $res = $wpdb->update(
                $wpdb->prefix . self::USER_TABLE_NAME,
                array('password' => wp_hash_password($newPassword)),
                array('user_id' => $user_id),
                array('%s'),
                array('%d')
            );
        } elseif (isset($email)) {
            $res = $wpdb->update(
                $wpdb->prefix . self::USER_TABLE_NAME,
                array('password' => wp_hash_password($newPassword)),
                array('email' => $email),
                array('%s'),
                array('%s')
            );
        }

        if ($res == false || $res == 0) {
            $errorMessage = "Reset password request failed. Please contact the system administrator.";
            throw new Exception($errorMessage);
        }

        return $res;
    }

    public static function create_event($event) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $e = $event;

        $wpdb->query('START TRANSACTION');

        try {
            $type = $e['lanrsvp-event-type'];

            $format = array('%s', '%s', '%s', '%d');
            $data = array(
                'event_title' => $e['lanrsvp-event-title'],
                'start_date' => $e['lanrsvp-event-startdate'],
                'has_seatmap' => ($type == 'seatmap' ? '1' : '0'),
                'min_attendees' => intval($e['lanrsvp-event-minattendees']),
            );

            if ($type == 'general') {
                array_push($format, '%d');
                $data['max_attendees'] = intval($e['lanrsvp-event-maxattendees']);
            }

            if (strlen($e['lanrsvp-event-enddate']) > 0) {
                $data['end_date'] = $e['lanrsvp-event-enddate'];
                array_push($format, '%s');
            }

            $event_id = '';
            if (isset($e['lanrsvp-event-id']) && is_numeric($e['lanrsvp-event-id'])) {
                $event_id = intval($e['lanrsvp-event-id']);
                $wpdb->update(
                    $wpdb->prefix . self::EVENT_TABLE_NAME,
                    $data,
                    array('event_id' => $e['lanrsvp-event-id']),
                    $format,
                    array('%d')
                );
            } else {
                $wpdb->insert(
                    $wpdb->prefix . self::EVENT_TABLE_NAME,
                    $data,
                    $format
                );
                $event_id = $wpdb->insert_id;
            }

            if (is_numeric($event_id)) {
                if ($type == 'seatmap') {
                    if (is_array($e['lanrsvp-event-seatmap'])) {
                        self::delete_seatmap($event_id);

                        foreach ($e['lanrsvp-event-seatmap'] as $row => $cols) {
                            if (is_array($cols)) {
                                foreach ($cols as $col => $cell) {
                                    if (is_array($cell) && isset($cell['status'])) {
                                        $seat_data = array(
                                            'event_id' => $event_id,
                                            'seat_row' => $row,
                                            'seat_column' => $col
                                        );
                                        $seat_data_format = array('%d', '%d', '%d');

                                        if (isset($cell['user_id']) && is_numeric($cell['user_id'])) {
                                            $seat_data['user_id'] = $cell['user_id'];
                                            array_push($seat_data_format, '%d');
                                        }

                                        $wpdb->insert(
                                            $wpdb->prefix . self::SEAT_TABLE_NAME,
                                            $seat_data,
                                            $seat_data_format
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $wpdb->query('ROLLBACK');
                return "The event could not be created. Contact plugin author.";
            }
            $wpdb->query('COMMIT');
            return ''; // success !
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return "The event could not be created: $e. Contact plugin author.";
        }
    }

    public static function delete_event($event_id) {
        if (isset($event_id) && is_numeric($event_id)) {
            /** @var $wpdb WPDB */
            global $wpdb;
            $wpdb->delete($wpdb->prefix . self::SEAT_TABLE_NAME, array('event_id' => $event_id), array('%d'));
            $wpdb->delete($wpdb->prefix . self::ATTENDEE_TABLE_NAME, array('event_id' => $event_id), array('%d'));
            $wpdb->delete($wpdb->prefix . self::EVENT_TABLE_NAME, array('event_id' => $event_id), array('%d'));
        }
    }

    private static function delete_seatmap($event_id) {
        if (isset($event_id) && is_numeric($event_id)) {
            LanRsvp::_log("deleting seatmap $event_id ...");
            /** @var $wpdb WPDB */
            global $wpdb;
            $wpdb->delete($wpdb->prefix . self::SEAT_TABLE_NAME, array('event_id' => $event_id), array('%d'));
        }
    }
}