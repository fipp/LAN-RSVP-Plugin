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
        add_action( 'wp_ajax_update_event', array( $this, 'update_event' ) );
        add_action( 'wp_ajax_delete_event', array( $this, 'delete_event' ) );
        add_action( 'wp_ajax_delete_attendee', array( $this, 'delete_attendee' ) );

        add_action( 'wp_ajax_save_user_comments', array( $this, 'save_user_comments' ) );
        add_action( 'wp_ajax_save_attendees', array( $this, 'save_attendees' ) );

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
                plugins_url( '../assets/css/lanrsvp-seatmap.css', __FILE__ ),
                array(),
                LanRsvpAdmin::VERSION
            );

            wp_enqueue_style(
                $this->plugin_slug .'-admin-styles',
                plugins_url( 'assets/css/lanrsvp-admin.css', __FILE__ ),
                array(),
                LanRsvpAdmin::VERSION
            );

            wp_enqueue_style(
                $this->plugin_slug .'-fontawesome',
                '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
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

            if (isset($_REQUEST['event_id'])) {
                wp_localize_script(
                    $this->plugin_slug . '-admin-script',
                    'LanRsvpAdmin',
                    array('event_id' => $_REQUEST['event_id'])
                );
            }

            if (substr( $screen->id, -strlen( $this->plugin_slug . '_event' ) ) == $this->plugin_slug . '_event') {
                wp_enqueue_script(
                    $this->plugin_slug . '-seatmap-script',
                    plugins_url( '../assets/js/lanrsvp-seatmap.js', __FILE__ ),
                    array( 'jquery' ),
                    LanRsvpAdmin::VERSION
                );

                $seatmap_data = [
                    'seatmap' => null,
                    'isAdmin' => true,
                    'ajaxurl' => admin_url('admin-ajax.php')
                ];
                if (isset($_REQUEST['event_id'])) {
                    $seatmap_data['seats'] = DB::get_event_seatmap($_REQUEST['event_id']);
                    $seatmap_data['event_id'] = $_REQUEST['event_id'];
                }
                wp_localize_script(
                    $this->plugin_slug . '-seatmap-script',
                    'seatmap_data',
                    $seatmap_data
                );
            }
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
            __( 'LAN Party Events', $this->plugin_slug ),    // The title to be displayed in the browser window for this page.
            __( 'LAN Party Events', $this->plugin_slug ),    // The text to be displayed for this menu item
            'manage_options',                               // Which type of users can see this menu item
            $this->plugin_slug,                             // The unique ID - that is, the slug - for this menu item
            array( $this, 'display_plugin_events_page' )     // The name of the function to call when rendering this menu's page
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // Events
        $hookname = add_submenu_page(
            $this->plugin_slug,                                     // The ID of the top-level menu page to which this submenu item belongs
            __( 'LAN Party Events - Events', $this->plugin_slug ),   // The value used to populate the browser's title bar when the menu page is active
            __( 'Events', $this->plugin_slug ),                     // The label of this submenu item displayed in the menu
            'manage_options',                                       // What roles are able to access this submenu item
            $this->plugin_slug,                                     // The ID used to represent this submenu item
            array( $this, 'display_plugin_events_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // Settings
        /*
        $hookname = add_submenu_page(
            $this->plugin_slug,                                     // The ID of the top-level menu page to which this submenu item belongs
            __( 'LAN Party Events Plugin - Settings', $this->plugin_slug ), // The value used to populate the browser's title bar when the menu page is active
            __( 'Settings', $this->plugin_slug ),                   // The label of this submenu item displayed in the menu
            'manage_options',                                       // What roles are able to access this submenu item
            $this->plugin_slug . '_settings',                       // The ID used to represent this submenu item
            array( $this, 'display_plugin_settings_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;
        */

        // Create/Edit Event Page
        $hookname = add_submenu_page(
            null, // Parent slug == null so that it won't show up in the menu
            __( 'LAN Party Events - Create Event', $this->plugin_slug ),
            __( 'Create Event', $this->plugin_slug ),
            'manage_options',
            $this->plugin_slug . '_event',
            array( $this, 'display_plugin_create_event_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // Users
        $hookname = add_submenu_page(
            $this->plugin_slug,
            __( 'LAN Party Events - Users', $this->plugin_slug ),
            __( 'Users', $this->plugin_slug ),
            'manage_options',
            $this->plugin_slug . '_users',
            array( $this, 'display_plugin_users_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // Attendees
        $hookname = add_submenu_page(
            null,
            __( 'LAN Party Events - Attendees', $this->plugin_slug ),
            __( 'Attendees', $this->plugin_slug ),
            'manage_options',
            $this->plugin_slug . '_attendees',
            array( $this, 'display_plugin_attendees_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

        // User event history
        $hookname = add_submenu_page(
            null,
            __( 'LAN Party Events - User', $this->plugin_slug ),
            __( 'User', $this->plugin_slug ),
            'manage_options',
            $this->plugin_slug . '_user',
            array( $this, 'display_plugin_user_page' )
        );
        $this->plugin_screen_hook_suffix[$hookname] = 0;

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
     * Render the create event page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_create_event_page() {
        include_once('views/event.php');
    }

    /**
     * Render the list events page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_events_page() {
        include_once('views/events.php');
    }

    /**
     * Render the list users page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_users_page() {
        include_once('views/users.php');
    }

    /**
     * Render the list attendees page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_attendees_page() {
        if (isset($_REQUEST['event_id']) && is_numeric($_REQUEST['event_id'])) {
            $event_id = $_REQUEST['event_id'];
            $event = DB::get_event($event_id);
            if (is_array($event)) {
                $has_seatmap = $event['has_seatmap'] == '1' ? true : false;
                $attendees = DB::get_attendees($event_id);
                $attendeesTable = new Attendees_Table($attendees, $is_admin = true, $has_seatmap);
                include_once('views/attendees.php');
            }
        }
    }

    /**
     * Render the user page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_user_page() {
        if (isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id'])) {
            $user_id = $_REQUEST['user_id'];
            $eventHistoryTable = new Event_History_table($user_id);
            include_once('views/user.php');
        }
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
        echo DB::create_event($_REQUEST);
        die();
    }

    public function update_event() {
        echo DB::create_event($_REQUEST);
        die();
    }

    public function delete_event() {
        echo DB::delete_event($_REQUEST['event_id']);
        die();
    }

    public function delete_attendee() {
        try {
            $_REQUEST = LanRsvp::checkAndTrimParams(['event_id', 'user_id'], $_REQUEST);
            DB::delete_attendee($_REQUEST['event_id'],$_REQUEST['user_id']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die();
    }

    public function save_attendees() {
        try {
            if (isset($_REQUEST['event_id']) && is_numeric($_REQUEST['event_id'])) {
                $event_id = $_REQUEST['event_id'];
                if (isset($_REQUEST['users']) && is_array($_REQUEST['users'])) {
                    foreach ($_REQUEST['users'] as $user_id => $data) {
                        LanRsvp::_log($user_id);
                        $comment = (isset($data['comment']) ? $data['comment'] : null);
                        $has_paid = (isset($data['has_paid']) ? $data['has_paid'] : null);
                        DB::update_attendee($event_id, $user_id, $comment, $has_paid);
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        die();
    }

    public function save_user_comments() {
        try {
            if (isset($_REQUEST['user_comments']) && is_array($_REQUEST['user_comments'])) {
                foreach ($_REQUEST['user_comments'] as $user_id => $comment) {
                    DB::set_user_comment($user_id, $comment);
                }
            }
        } catch (Exception $e) {
            $e->getMessage();
        }

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
                //'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>',
                //'delete' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Delete', $this->plugin_slug ) . '</a>'
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
}