<?php
/**
 * LAN RSVP Plugin
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

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        /* Define custom functionality.
         * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        // add_action( '@TODO', array( $this, 'action_method_name' ) );
        // add_filter( '@TODO', array( $this, 'filter_method_name' ) );
        add_shortcode( 'lanrsvp', array( $this, 'shortcode_handler_lanrsvp' ) );

        add_shortcode( 'lanrsvp', array( $this, 'shortcode_handler_lanrsvp' ) );

        // AJAX login
        add_action('wp_ajax_login', array( $this, 'login' ));
        add_action('wp_ajax_nopriv_login', array( $this, 'login' ));
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
     * Register and enqueue public-facing style sheet.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/lanrsvp.css', __FILE__ ), array(), self::VERSION );
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/lanrsvp.js', __FILE__ ), array( 'jquery' ), self::VERSION );
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
        $ajax_url = admin_url('admin-ajax.php');

        return <<<HTML
<div class="lanrsvp">
    <script type="text/javascript">
        var ajaxUrl = "{$ajax_url}";
    </script>
    <form class="lanrsvp-login">
        <table>
            <tr><td>E-mail:</td><td><input type="email" required value="test@test.com" /></td></tr>
            <tr><td>Password:</td><td><input type="password" required value="testpassword" /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Log in" /></td></tr>
        </table>
    </form>
</div>
HTML;
    }

    function login() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $email = $_REQUEST['email'];
        $password_plain = $_REQUEST['password'];

        $sql =  $wpdb->prepare(
            "SELECT password FROM wp_lanrsvp_user WHERE email = %s",
            $email
        );

        $res = $wpdb->get_results($sql);

        if (isset( $res[0]->{'password'} )) {
            $password_hash = $res[0]->{'password'};
        }

        $this->_log($password_plain);
        $this->_log($password_hash);

        $wp_hasher = new PasswordHash(8, TRUE);
        if ( $wp_hasher->CheckPassword( $password_plain, $password_hash )) {
            $this->_log("match");
        } else {
            $this->_log("no match");
        }

        echo "";

        die();
    }

    function _log( $message ) {
        if( WP_DEBUG === true ){
            if( is_array( $message ) || is_object( $message ) ){
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }

}