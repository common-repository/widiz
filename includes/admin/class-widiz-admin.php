<?php
/**
 * Widiz.
 *
 * @package   Widiz_Admin
 * @author    Widiz
 * @license   GPL-2.0+
 * @link      https://widiz.com
 * @copyright 2017 Widiz
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");
/*-----------------------------------------*/

class Widiz_Admin
{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = array();

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
        return;
        } */

        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin               = Widiz::get_instance();
        $this->plugin_slug    = $plugin->get_plugin_slug();
        $this->plugin_version = $plugin->get_plugin_version();

        // Load admin style sheet and JavaScript.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css_js' ) );

        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(dirname(__FILE__)))) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'add_action_links'));

        add_action('admin_notices', array($this, 'admin_notices'));

    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
        return;
        } */

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific CSS and JS.
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_css_js()
    {
        /* Admin Styles */
        wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), $this->plugin_version);

        // Main Admin JS Script
        wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__), array(), $this->plugin_version);

        wp_enqueue_script($this->plugin_slug . '-admin-script');

    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links)
    {
        return array_merge(
            array(
                'settings' => '<a href="' . admin_url('admin.php?page=' . $this->plugin_slug . '-settings') . '">' . __('Settings', 'widiz') . '</a>',
            ),
            $links
        );
    }

    function admin_notices() {
        $widizApi = Widiz_Api::get_instance();
        if($widizApi){
            $screen = get_current_screen();
            if(!$widizApi->isConfigured()){
                if((isset($screen->base) && $screen->base !== 'widiz_page_widiz-settings') || !isset($screen->base)){
                    ?>
                        <div class="notice notice-error is-dismissible">
                            <p>To start using Widiz plugin features, please go <a href="<?php echo admin_url("admin.php?page=widiz-settings#api_setup"); ?>">here</a> and complete API Credentials with our own!</p>
                        </div>
                    <?php
                }
            }
            else{
                if(!$widizApi->isValidCached()){
                    ?>
                        <div class="notice notice-error is-dismissible">
                            <p>Your Widiz API Credentials are invalid, to continue using of plugin please go <a href="<?php echo admin_url("admin.php?page=widiz-settings#api_setup"); ?>">here</a> and correct them!</p>
                        </div>
                    <?php
                }
            }
        }
    }
}
