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
                registered_ip_remote_addr CHAR(45) NOT NULL,
                registered_ip_x_forwarded_for CHAR(45),
                comment TEXT,
                PRIMARY KEY  (user_id)
            );",
            $wpdb->prefix . self::USER_TABLE_NAME
        );

        $event_sql = sprintf("CREATE TABLE %s (
                event_id MEDIUMINT NOT NULL AUTO_INCREMENT,
                is_active ENUM('0','1') NOT NULL DEFAULT '0',
                event_title VARCHAR(64) NOT NULL,
                start_date DATETIME NOT NULL,
                description TEXT,
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
                registered_ip_remote_addr CHAR(45) NOT NULL,
                registered_ip_x_forwarded_for CHAR(45),
                comment TEXT,
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
                FOREIGN KEY (event_id, user_id) REFERENCES %s (event_id, user_id)
            );",
            $wpdb->prefix . self::SEAT_TABLE_NAME,
            $wpdb->prefix . self::ATTENDEE_TABLE_NAME
        );

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($user_sql);
        dbDelta($event_sql);
        dbDelta($attendee_sql);
        dbDelta($seat_sql);

        if (self::DEBUG) {
            $wpdb->query("INSERT INTO wp_lanrsvp_event (is_active,event_title,start_date,end_date,min_attendees,max_attendees,has_seatmap) VALUES ('1','Rindal LAN Oktober 2014','2014-10-10 18:00:00','2014-10-12 16:00:00','0','0','1')");
            $wpdb->query("INSERT INTO wp_lanrsvp_event (is_active,event_title,start_date,min_attendees,max_attendees,has_seatmap) VALUES ('1','Lonely Compo','2014-10-11 12:00:00','0','1','0')");
            $wpdb->query("INSERT INTO wp_lanrsvp_event (is_active,event_title,start_date,min_attendees,max_attendees,has_seatmap) VALUES ('1','CS Compo','2014-10-11 16:00:00','4','16','0')");
            $wpdb->query("INSERT INTO wp_lanrsvp_event (is_active,event_title,start_date,min_attendees,max_attendees,has_seatmap) VALUES ('0','FIFA Compo','2014-10-12 12:00:00','2','20','1')");
        }
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
            'activation_code' => $user['activation_code'],
            'registered_ip_remote_addr' => $_SERVER['REMOTE_ADDR']
        ];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $data['registered_ip_x_forwarded_for'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $wpdb->insert($wpdb->prefix . self::USER_TABLE_NAME, $data, array('%s', '%s', '%s', '%s', '%s'));

        if (!is_numeric($wpdb->insert_id)) {
            $errorMessage = "A new user could not be created! Please contact the system administrator.";
            throw new Exception($errorMessage);
        }
    }

    public static function create_attendee($event_id, $user_id, $seat_row = null, $seat_col = null) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $event = DB::get_event($event_id);
        $has_seatmap = ($event['has_seatmap'] == '1' ? true : false);
        if ($has_seatmap && (is_null($seat_row) || is_null($seat_col))) {
            throw new Exception("Event $event_id has a seat map, but no seat was given during sign up.");
        }

        try {
            $wpdb->query('START TRANSACTION');

            $data = [
                'event_id' => $event_id,
                'user_id' => $user_id,
                'registered_ip_remote_addr' => $_SERVER['REMOTE_ADDR']
            ];

            $format = ['%d','%d','%s'];

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $data['registered_ip_x_forwarded_for'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
                array_push( $format, '%s');
            }


            $rowsUpdated = $wpdb->insert(
                $wpdb->prefix . self::ATTENDEE_TABLE_NAME,
                $data,
                $format
            );

            if (!$rowsUpdated) {
                throw new Exception("System error - could not sign up user. Please contact the system administrator.");
            }

            if ($has_seatmap) {
                $wpdb->update(
                    $rowsUpdated = $wpdb->prefix . self::SEAT_TABLE_NAME,
                    array(
                        'user_id' => $user_id
                    ),
                    array(
                        'event_id' => $event_id,
                        'seat_row' => $seat_row,
                        'seat_column' => $seat_col
                    ),
                    array('%s'),
                    array('%s','%s','%s')
                );

                if (!$rowsUpdated) {
                    throw new Exception("System error - could not assign seat. Please contact the system administrator.");
                }
            }
            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            throw $e;
        }
    }

    public static function delete_attendee($event_id, $user_id) {
        /** @var $wpdb WPDB */
        global $wpdb;
        try {
            $wpdb->query('START TRANSACTION');

            $event = self::get_event($event_id);
            if ($event['has_seatmap']) {
                $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;
                $rowsUpdated = $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE $seat_table_name SET user_id = NULL WHERE event_id = %s AND user_id = %s",
                        $event_id,
                        $user_id
                    ));

                if (!$rowsUpdated) {
                    throw new Exception("System error - could not remove attendee seat. Please contact the system administrator.");
                }
            }

            $rowsUpdated = $wpdb->delete(
                $wpdb->prefix . self::ATTENDEE_TABLE_NAME,
                array(
                    'event_id' => $event_id,
                    'user_id' => $user_id
                ),
                array('%s','%s')
            );

            if (!$rowsUpdated) {
                throw new Exception("System error - could not remove attendee. Please contact the system administrator.");
            }

            $wpdb->query('COMMIT');
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            throw $e;
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
        /** @var $wpdb WPDB */
        global $wpdb;

        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;
        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT
              a.user_id,
              b.first_name,
              b.last_name,
              b.email,
              c.seat_row,
              c.seat_column,
              a.comment,
              a.registration_date
             FROM
              $attendee_table_name a
              JOIN $user_table_name b ON (a.user_id = b.user_id)
              LEFT JOIN $seat_table_name c ON (a.event_id = c.event_id AND a.user_id = c.user_id)
             WHERE a.event_id = %d",
            $event_id
        ), ARRAY_A);
    }

    public static function get_attendee($event_id, $user_id) {
        /** @var $wpdb WPDB */
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT a.user_id, b.first_name, b.last_name, b.email, a.event_id, a.registration_date, c.seat_row, c.seat_column
              FROM wp_lanrsvp_attendee a
                JOIN wp_lanrsvp_user b ON a.user_id = b.user_id
                LEFT JOIN wp_lanrsvp_seat c ON a.event_id = c.event_id AND a.user_id = c.user_id
              WHERE a.event_id = %d AND a.user_id = %d",
            $event_id,
            $user_id
        ), ARRAY_A);
    }

    public static function get_event($event_id) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $event_table_name = $wpdb->prefix . self::EVENT_TABLE_NAME;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $event_table_name WHERE event_id = %d",
            $event_id
        ), ARRAY_A);

    }

    public static function get_event_seatmap($event_id) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;
        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT a.*, b.user_id, b.first_name, b.last_name
            FROM $seat_table_name a LEFT JOIN $user_table_name b ON a.user_id = b.user_id
            WHERE event_id = %d",
            $event_id
        ), ARRAY_A);
    }

    public static function get_events() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $event_table_name = $wpdb->prefix . self::EVENT_TABLE_NAME;
        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;
        $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;

        $res = $wpdb->get_results(
            "SELECT
              a.*,
              (SELECT COUNT(*) FROM $attendee_table_name WHERE event_id = a.event_id) AS 'attendees_registered',
              (SELECT COUNT(*) FROM $seat_table_name WHERE event_id = a.event_id) AS 'total_seats'
            FROM
              $event_table_name a;",
            ARRAY_A
        );

        return $res;
    }

    public static function get_users() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $user_table_name = $wpdb->prefix . self::USER_TABLE_NAME;
        return $wpdb->get_results("SELECT user_id, is_activated, email, first_name, last_name, registration_date, comment FROM $user_table_name", ARRAY_A);
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

            $format = array('%s', '%s', '%s', '%s', '%d');
            $data = array(
                'event_title' => $e['lanrsvp-event-title'],
                'start_date' => $e['lanrsvp-event-startdate'],
                'is_active' => ($e['lanrsvp-event-status'] == 'open' ? '1' :'0'),
                'has_seatmap' => ($type == 'seatmap' ? '1' : '0'),
                'min_attendees' => intval($e['lanrsvp-event-minattendees'])
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

    public static function get_attendees_count($event_id) {
        /** @var $wpdb WPDB */
        global $wpdb;
        $attendee_table_name = $wpdb->prefix . self::ATTENDEE_TABLE_NAME;
        $res = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $attendee_table_name WHERE event_id = %s",
                array('event_id' => $event_id)
            ),
            ARRAY_A
        );
        return $res['COUNT(*)'];
    }

    public static function get_seats_count($event_id) {
        /** @var $wpdb WPDB */
        global $wpdb;
        $seat_table_name = $wpdb->prefix . self::SEAT_TABLE_NAME;
        $res = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $seat_table_name WHERE event_id = %s",
                array('event_id' => $event_id)
            ),
            ARRAY_A
        );
        return $res['COUNT(*)'];
    }

    public static function get_max_attendees($event_id) {
        /** @var $wpdb WPDB */
        global $wpdb;
        $event_table_name = $wpdb->prefix . self::EVENT_TABLE_NAME;
        $res = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT max_attendees FROM $event_table_name WHERE event_id = %s",
                array('event_id' => $event_id)
            ),
            ARRAY_A
        );
        return $res['max_attendees'];
    }
}