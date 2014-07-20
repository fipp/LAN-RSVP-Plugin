<?php
/**
 * Plugin Name.
 *
 * @package   lanrsvp_admin
 * @author    Terje Ness Andersen <terje.andersen@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Terje Ness Andersen
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-lanrsvp.php`
 *
 *
 * @package lanrsvp_admin
 * @author  Terje Ness Andersen <terje.andersen@gmail.com>
 */
class LanRsvpAdmin {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    const VERSION = '1.0.0';


    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slugs of the plugin screens.
     *
     * @since    1.0.0
     *
     * @var      array
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct() {
        /*
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
            return;
        } */

        /*
         * Call $plugin_slug from public plugin class.
         *
         */
        $plugin = LanRsvp::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Load admin style sheet and JavaScript.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Add the options page and menu item with submenus
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

        // Add wordpress settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );



        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
        add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

        /*
         * Define custom functionality.
         *
         * Read more about actions and filters:
         * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        // add_action( '@TODO', array( $this, 'action_method_name' ) );
        // add_filter( '@TODO', array( $this, 'filter_method_name' ) );
        // Add AJAX handler for event registration
        add_action( 'wp_ajax_create_event', array( $this, 'create_event' ) );

    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        /*
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
            return;
        } */

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles() {

        if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( isset($this->plugin_screen_hook_suffix[$screen->id]) ) {

            wp_enqueue_style(
                $this->plugin_slug .'-common-styles',
                plugins_url( '../assets/css/lanrsvp.css', __FILE__ ),
                array(),
                LanRsvpAdmin::VERSION
            );

            wp_enqueue_style(
                $this->plugin_slug .'-admin-styles',
                plugins_url( 'assets/css/lanrsvp-admin.css', __FILE__ ),
                array(),
                LanRsvpAdmin::VERSION
            );

        }

    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts() {

        if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( isset($this->plugin_screen_hook_suffix[$screen->id]) ) {

            wp_enqueue_script(
                $this->plugin_slug . '-admin-script',
                plugins_url( 'assets/js/lanrsvp-admin.js', __FILE__ ),
                array( 'jquery' ),
                LanRsvpAdmin::VERSION
            );

            wp_enqueue_script(
                $this->plugin_slug . '-seatmap-script',
                plugins_url( 'assets/js/lanrsvp-seatmap.js', __FILE__ ),
                array( 'jquery' ),
                LanRsvpAdmin::VERSION
            );
        }

    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        /*
         * Administration Menus: http://codex.wordpress.org/Administration_Menus
         * - Change 'manage_options' to the capability you see fit
         *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
         */

        // Main menu
        $hookname = add_menu_page(
            __( 'LAN RSVP Plugin', $this->plugin_slug ),    // The title to be displayed in the browser window for this page.
            __( 'LAN RSVP Plugin', $this->plugin_slug ),    // The text to be displayed for this menu item
            'manage_options',                               // Which type of users can see this menu item
            $this->plugin_slug,                             // The unique ID - that is, the slug - for this menu item
            array( $this, 'display_plugin_admin_page' )     // The name of the function to call when rendering this menu's page
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // Settings
        $hookname = add_submenu_page(
            $this->plugin_slug,                                     // The ID of the top-level menu page to which this submenu item belongs
            __( 'LAN RSVP Plugin - Settings', $this->plugin_slug ), // The value used to populate the browser's title bar when the menu page is active
            __( 'Settings', $this->plugin_slug ),                   // The label of this submenu item displayed in the menu
            'manage_options',                                       // What roles are able to access this submenu item
            $this->plugin_slug . '_settings',                       // The ID used to represent this submenu item
            array( $this, 'display_plugin_settings_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // Handle event
        $hookname = add_submenu_page(
            $this->plugin_slug,
            __( 'LAN RSVP Plugin - Create Event', $this->plugin_slug ),
            __( 'Create Event', $this->plugin_slug ),
            'manage_options',
            $this->plugin_slug . '_event',
            array( $this, 'display_plugin_create_event_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

    }

    /**
     * Render the overview page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        include_once( 'views/overview.php' );
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_settings_page() {
        include_once( 'views/settings.php' );
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_create_event_page() {
        include_once( 'views/create-event.php' );
    }



    public function register_settings() {

        register_setting( $this->plugin_slug . '_settings', 'logip' );

        /*
        add_settings_section(
            $this->plugin_slug . '_settings_section',       // ID used to identify this section and with which to register options
            __( 'Logging Settings', $this->plugin_slug ),   // Title to be displayed on the administration page
            '',
            $this->plugin_slug . '_settings'                // Page on which to add this section of options
        );

        add_settings_field(
            $this->plugin_slug . '_checkbox_logip',     // ID used to identify the field throughout the plug-in
            __( 'Log IPs', $this->plugin_slug ),        // The label to the left of the option interface
            array( $this, 'settings_checkbox_element' ),   // The function responsible for rendering the option interface
            $this->plugin_slug . '_settings' ,          // The page on which this option will be displayed
            $this->plugin_slug . '_settings_section',   // The name of the section to which this field belongs
            array(
                'type' => 'checkbox'
            )
        );

        register_setting(
            $this->plugin_slug . '_settings',
            $this->plugin_slug . '_settings'
        );
        */
    }

    public function create_event() {

        // Validate that the expected GET or POST parameters are set
        if ( !isset($_REQUEST['title'], $_REQUEST['start_date'], $_REQUEST['end_date']) &&
            (isset($_REQUEST['seatmap']) || isset($_REQUEST['min_attendees']) || isset($_REQUEST['max_attendees']))) {
            echo "Not sufficient data sent in. Contact plugin author for fix.";
            die();
        }

        $event = array(
            'title' => $_REQUEST['title'],
            'start_date' => $_REQUEST['start_date'],
            'end_date' => $_REQUEST['end_date'],
            'seatmap' => isset($_REQUEST['seatmap']) ? $_REQUEST['seatmap'] : '',
            'min_attendees' => isset($_REQUEST['min_attendees']) ? isset($_REQUEST['min_attendees']) : '',
            'max_attendees' => isset($_REQUEST['max_attendees']) ? isset($_REQUEST['max_attendees']) : ''
        );

        // Validate title
        if (strlen($event['title']) < 2 && strlen($event['title']) > 64) {
            echo "Title is either under 2 characters, or over 64.";
            die();
        }

        // Validate start date
        if (DateTime::createFromFormat('Y-m-d H:i:s', $event['start_date']) == false) {
            echo "Start date is not valid.";
            die();
        }

        // Validate end date
        if (strlen($event['end_date']) != 0) {
            if (DateTime::createFromFormat('Y-m-d H:i:s', $event['end_date']) == false) {
                echo "End date is defined, but not valid.";
                die();
            }
        } else {
            unset($event['end_date']);
        }

        if ( !is_array($event['seatmap']) && !is_numeric($event['min_attendees']) &&
            !is_numeric($event['max_attendees'])) {
            echo "Neither a seat map, or a setting for number of attendees could be found. Contact plugin author.";
            die();
        } elseif (is_array($event['seatmap'])) {
            unset($event['min_attendees']);
            unset($event['max_attendees']);
        } else {
            unset($event['seatmap']);
        }

        echo DB::createEvent($event);
        die();
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links( $links ) {

        return array_merge(
            array(
                'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
            ),
            $links
        );

    }

    /**
     * NOTE:     Actions are points in the execution of a page or process
     *           lifecycle that WordPress fires.
     *
     *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    1.0.0
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:     Filters are points of execution in which WordPress modifies data
     *           before saving it or sending it to the browser.
     *
     *           Filters: http://codex.wordpress.org/Plugin_API#Filters
     *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    1.0.0
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }

    public static function getEventsTable() {
        $html = '<table>';
        $html .= sprintf(
            "<tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr>",
            'Event ID',
            'Title',
            'From date',
            'To date'
        );

        $events = DB::get_events();
        if (isset( $events )) {
            foreach ($events as $event) {
                $html .= sprintf(
                    "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                    $event->{'event_id'},
                    $event->{'event_title'},
                    $event->{'from_date'},
                    $event->{'to_date'}
                );
            }
        } else {
            $html .= '<tr><td colspan="4">No events found.</td></tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public static function getUsersTable() {
        $html = '<table>';
        $html .= sprintf(
            "<tr><th>%s</th><th>%s</th><th>%s</th></tr>",
            'User ID',
            'Email',
            'Full name'
        );

        $users = DB::get_users();
        if (isset( $users )) {
            foreach ($users as $user) {
                $html .= sprintf(
                    "<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
                    $user->{'user_id'},
                    $user->{'email'},
                    $user->{'full_name'}
                );
            }
        } else {
            $html .= '<tr><td colspan="4">No users found.</td></tr>';
        }

        $html .= '</table>';

        return $html;
    }
}