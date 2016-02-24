<?php
/**
 * LAN Party Events Plugin
 *
 * @package   lanrsvp
 * @author    Terje Ness Andersen <terje.andersen@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Terje Ness Andersen
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `admin/class-lanrsvp-admin.php`
 *
 *
 * @package lanrsvp
 * @author  Terje Ness Andersen <terje.andersen@gmail.com>
 */

class LanRsvp {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    const VERSION = '1.0.0';

    /**
     * Unique identifier for your plugin.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    1.0.0*
     * @var      string
     */
    protected $plugin_slug = 'lanrsvp';

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct() {

        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
        add_action( 'init', array( $this, 'startSession' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Register (but not enqueue) public-facing style sheet and JavaScript.
        // The enqueue will be in the shortcode.
        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles') );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts') );

        add_shortcode( 'lanrsvp', array( $this, 'shortcode_handler_lanrsvp' ) );

        // AJAX login, register, activate, and forgot password
        add_action('wp_ajax_login', array( $this, 'login'));
        add_action('wp_ajax_nopriv_login', array( $this, 'login'));

        add_action('wp_ajax_logout', array( $this, 'logout'));
        add_action('wp_ajax_nopriv_logout', array( $this, 'logout'));

        add_action('wp_ajax_reset_password', array( $this, 'reset_password'));
        add_action('wp_ajax_nopriv_reset_password', array( $this, 'reset_password'));

        add_action('wp_ajax_register', array( $this, 'register'));
        add_action('wp_ajax_nopriv_register', array( $this, 'register'));

        add_action('wp_ajax_activate_user', array( $this, 'activate_user'));
        add_action('wp_ajax_nopriv_activate_user', array( $this, 'activate_user'));

        //add_action('wp_ajax_get_authenticated', array( $this, 'ajax_get_authenticated' ) );
        //add_action('wp_ajax_nopriv_get_authenticated', array( $this, 'ajax_get_authenticated' ) );

        add_action('wp_ajax_sign_up', array( $this, 'sign_up' ) );
        add_action('wp_ajax_nopriv_sign_up', array( $this, 'sign_up' ) );

        add_action('wp_ajax_unsubscribe', array( $this, 'unsubscribe' ) );
        add_action('wp_ajax_nopriv_unsubscribe', array( $this, 'unsubscribe' ) );

        add_action('wp_ajax_get_seatmap', array( $this, 'get_seatmap' ) );
        add_action('wp_ajax_nopriv_get_seatmap', array( $this, 'get_seatmap' ) );



    }

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    LanRsvp slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide  ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_activate();
                }

                restore_current_blog();

            } else {
                self::single_activate();
            }

        } else {
            self::single_activate();
        }

    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_deactivate();

                }

                restore_current_blog();

            } else {
                self::single_deactivate();
            }

        } else {
            self::single_deactivate();
        }

    }

    public static function uninstall( ) {
        DB::uninstall();
    }


        /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    1.0.0
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site( $blog_id ) {

        if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
            return;
        }

        switch_to_blog( $blog_id );
        self::single_activate();
        restore_current_blog();

    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    1.0.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {
        /** @var $wpdb WPDB */

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
        WHERE archived = '0' AND spam = '0'
        AND deleted = '0'";

        return $wpdb->get_col( $sql );

    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    1.0.0
     */
    private static function single_activate() {
        DB::install();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    1.0.0
     */
    private static function single_deactivate() {
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        $domain = $this->plugin_slug;
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

    }

    /**
     * Starts a session
     *
     * @since    1.0.0
     */
    public function startSession() {

        if (!session_id()) {
            session_start();
        }

    }

    /**
     * Register public-facing style sheets.
     *
     * @since    1.0.0
     */
    public function register_styles() {
        wp_register_style(
            $this->plugin_slug .'-fontawesome',
            '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
            array(),
            self::VERSION
        );

	    wp_register_style(
		    $this->plugin_slug .'-public-styles',
		    plugins_url( 'assets/css/lanrsvp.css', __FILE__ ),
		    array(),
		    self::VERSION
	    );

        wp_register_style(
            $this->plugin_slug .'-seatmap-styles',
            plugins_url( '../assets/css/lanrsvp-seatmap.css', __FILE__ ),
            array(),
            self::VERSION
        );
    }

    /**
     * Register public-facing JavaScript files.
     *
     * @since    1.0.0
     */
    public function register_scripts() {

        wp_register_script(
            $this->plugin_slug . '-public-script',
            plugins_url( 'assets/js/lanrsvp.js', __FILE__ ),
            array( 'jquery' ),
            self::VERSION
        );

        wp_register_script(
            $this->plugin_slug . '-seatmap-script',
            plugins_url( '../assets/js/lanrsvp-seatmap.js', __FILE__ ),
            array( 'jquery' ),
            self::VERSION
        );

    }

    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    1.0.0
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:  Filters are points of execution in which WordPress modifies data
     *        before saving it or sending it to the browser.
     *
     *        Filters: http://codex.wordpress.org/Plugin_API#Filters
     *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    1.0.0
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }

    function shortcode_handler_lanrsvp ( $attrs ) {

        echo "<h1>LAN Party Events Plugin</h1>";
        if ( isset($attrs['event_id']) && is_numeric($attrs['event_id'])) {
            $event_id = $attrs['event_id'];
            $event = DB::get_event($event_id);
            if (is_array($event)) {
                $event_is_active = $event['is_active'];
                $has_seatmap = ($event['has_seatmap'] == 0 ? false : true);

                wp_enqueue_script($this->plugin_slug . '-public-script');
                wp_localize_script(
                    $this->plugin_slug . '-public-script',
                    'LanRsvp',
                    array(
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        'event_id' => $event_id,
                        'has_seatmap' => $has_seatmap
                    )
                );
                wp_enqueue_style($this->plugin_slug .'-public-styles');

                $is_authenticated = session_id() && isset($_SESSION['lanrsvp-userid']);
                $can_sign_up = false;
                if ($event_is_active) {
                    $is_signed_up = false;
                    $attendee = false;
                    if ($is_authenticated) {
                        $attendee_raw = DB::get_attendee($event_id, $_SESSION['lanrsvp-userid']);
                        if (is_array($attendee_raw)) {
                            $attendee = $attendee_raw;
                            $is_signed_up = true;
                        }
                    }
                }


                $attendees = DB::get_attendees($event_id);
                $attendees_count = count($attendees);

                $attendeesTable = new Attendees_Table($attendees, $is_admin = false, $has_seatmap);

                $seats = null;
                $seats_count = null;
                $places_left = null;

                if ($has_seatmap) {
                    wp_enqueue_script($this->plugin_slug . '-seatmap-script');
	                wp_enqueue_style($this->plugin_slug .'-seatmap-styles');
                    wp_enqueue_style($this->plugin_slug .'-fontawesome');

                    $seats = DB::get_event_seatmap($event_id);
                    $seats_count = count($seats);
                    $places_left = $seats_count - $attendees_count;

                    // If the current requester is authenticated, not signed up, and there are still free
                    // seats, flag that he can sign up
                    if ($event_is_active && $is_authenticated && !$is_signed_up && $places_left > 0) {
                        $can_sign_up = true;
                    }

                    $seatmap_data = [
                        'ajaxurl'         => admin_url('admin-ajax.php'),
                        'event_id'        => $event_id,
                        'isAdmin'         => false,
	                    'canSignUp'       => $can_sign_up,
                        'seats'           => $seats
                    ];

                    wp_localize_script(
                        $this->plugin_slug . '-seatmap-script',
                        'seatmap_data',
                        $seatmap_data
                    );
                }


                if ($event['max_attendees'] > 0) {
                    $places_left = $event['max_attendees'] - $attendees_count;
                }

                if ($event_is_active && $is_authenticated && !$is_signed_up) {
                    if (is_null($places_left) || $places_left > 0) {
                        $can_sign_up = true;
                    }
                }

                echo '<div id="lanrsvp">';
                include_once('views/event-details.php');
                if ($event_is_active) {
                    if ($is_authenticated) {
                        include_once('views/authenticated.php');
                    } else {
                        include_once('views/authenticate.php');
                    }
                }
                if ($has_seatmap) {
                    include_once('views/seatmap.php');
                }
                //self::get_authenticated($event_id, $has_seatmap, $is_authenticated, $is_signed_up, $can_sign_up, $attendee);
   	            include_once('views/attendees.php');
	            echo '</div>';

                return;
            } else {
                return "<p>Specified event id $event_id is not valid.</p>";
            }
        } else {
            return '<p>Could not recognize shortcode. Valid example: [lanrsvp event_id="12"].</p>';
        }
    }

    public static function sign_up() {
        try {
            $user_id = null;
            if (isset($_SESSION['lanrsvp-userid'])) {
                $user_id = $_SESSION['lanrsvp-userid'];
            } else {
                throw new Exception("You are not logged in! Try again, or contact system administrator.");
            }

            $user = DB::get_user($user_id);
            if (!is_array($user) || $user['is_activated'] == '0') {
                throw new Exception("User does not exist, or the user is not activated.");
            }

            $_REQUEST = self::checkAndTrimParams(['event_id','seat_row','seat_col'], $_REQUEST);
            $event_id = $_REQUEST['event_id'];
            $seat_row = $_REQUEST['seat_row'];
            if (!is_numeric($seat_row)) {
                $seat_row = null;
            }
            $seat_col = $_REQUEST['seat_col'];
            if (!is_numeric($seat_col)) {
                $seat_col = null;
            }

            DB::create_attendee($event_id, $user_id, $seat_row, $seat_col);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die();
    }

    public static function unsubscribe() {
        try {
            $user_id = null;
            if (isset($_SESSION['lanrsvp-userid'])) {
                $user_id = $_SESSION['lanrsvp-userid'];
            } else {
                throw new Exception("You are not logged in! Try again, or contact system administrator.");
            }

            $_REQUEST = self::checkAndTrimParams(['event_id'], $_REQUEST);
            $event_id = $_REQUEST['event_id'];
            DB::delete_attendee($event_id, $user_id);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die();
    }

    public static function get_seatmap() {
        try {
            $_REQUEST = LanRsvp::checkAndTrimParams(['event_id'], $_REQUEST);
            $seats = DB::get_event_seatmap($_REQUEST['event_id']);
            echo json_encode($seats);
        } catch (Exception $e) {}
        die();
    }

    function login() {
        try {
            $_REQUEST = self::checkAndTrimParams(['email', 'password'], $_REQUEST);

            $email = $_REQUEST['email'];
            $password_plain = $_REQUEST['password'];

            $password_hash = null;

            $user = DB::get_user(null,$email);
            if (!is_array($user)) {
                throw new Exception("User account '$email' not found. Did you enter the correct email address?");
            }

            if ($user['is_activated'] == '0') {
                throw new Exception("User account '$email' not activated. Please follow the activation instructions sent
                to your email address.'");
            }

            $password_hash = $user['password'];

            $wp_hasher = new PasswordHash(8, TRUE);
            if ( !$wp_hasher->CheckPassword( $password_plain, $password_hash )) {
                throw new Exception("Wrong password for user account '$email'.");
            }

            $_SESSION['lanrsvp-userid'] = $user['user_id'];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die();
    }

    function logout() {
        if (session_id()) {
            unset($_SESSION['lanrsvp-userid']);
            if (!is_array($_SESSION) || count($_SESSION) == 0) {
                session_destroy();
            }
        }
        echo "";
        die();
    }

    function reset_password() {
        try {
            $_REQUEST = self::checkAndTrimParams(['email'], $_REQUEST);

            $email = $_REQUEST['email'];
            $user = DB::get_user(null, $email);
            if (!is_array($user)) {
                throw new Exception("The user account '$email' could not be found. Please provide an existing account.");
            }

            if ($user['is_activated'] != '1') {
                throw new Exception("The user account '$email' is not activated. Please activate your account first.");
            }

            $firstName = $user['first_name'];
            $lastName = $user['last_name'];

            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $newPassword = substr(str_shuffle($chars),0,8);

            if (DB::set_password(null, $_REQUEST['email'], $newPassword) == 1) {
                $subject = "LAN Party Events Plugin - Your new password";
                $site_url = site_url();
                $message = <<<HTML
Dear $firstName $lastName!

Someone (hopefully you - $email) requested a new password for this LAN RSVP system account on $site_url.

The new password is: $newPassword

Please note that this password is encrypted in our database, and not stored as clear text.

Best Regards,
The LAN Party Events Plugin, on behalf of $site_url.
HTML;
                wp_mail( $email, $subject, $message );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die();
    }

    public function register() {
        try {
            $_REQUEST = self::checkAndTrimParams(
                ['firstName', 'lastName', 'email', 'emailConfirm', 'password', 'passwordConfirm'],
                $_REQUEST
            );

            if ($_REQUEST['email'] != $_REQUEST['emailConfirm']) {
                throw new Exception('The email addresses do not match! Please try again.');
            }

            if ($_REQUEST['password'] != $_REQUEST['passwordConfirm']) {
                throw new Exception('The passwords do not match! Please try again.');
            }

            $firstName = $_REQUEST['firstName'];
            $lastName = $_REQUEST['lastName'];
            $email = $_REQUEST['email'];

            $activation_code = md5($email . time());
            $_REQUEST['activation_code'] = $activation_code;
            DB::create_user($_REQUEST);

            $subject = "LAN Party Events Plugin - Your activation code";
            $site_url = site_url();
            $message = <<<HTML
Dear $firstName $lastName!

Your email address ($email) has been used to register a new account for the LAN RSVP system on $site_url.

If this was you, please activate your account by entering the following code at the page where you registered:

$activation_code

If this was not you, you can safely disregard this message, the account will not be usable.

Best Regards,
The LAN Party Events Plugin, on behalf of $site_url.
HTML;
            wp_mail( $email, $subject, $message );
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        die();
    }

    public function activate_user() {
        try {
            $_REQUEST = self::checkAndTrimParams(['email', 'activationCode'], $_REQUEST);
            DB::activate_user($_REQUEST);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        die();
    }

    public static function checkAndTrimParams($parameterList, $source) {
        foreach ($parameterList as $param) {
            if (!isset($source[$param])) {
                $errorMsg = "Parameter $param is not given! Try again or contact the system administrator";
                throw new Exception($errorMsg);
            }

            $source[$param] = trim($source[$param]);

            if (strlen($source[$param]) == 0) {
                $errorMsg = "Parameter $param is empty! Try again or contact the system administrator";
                throw new Exception($errorMsg);
            }
        }

        return $source;
    }

    public static function _log ( $message ) {
        if ( WP_DEBUG === true ) {
            if( is_array( $message ) || is_object( $message ) ){
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }

}