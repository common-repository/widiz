<?php
/**
 * @package   Widiz
 * @author    Widiz
 * @license   GPL-2.0+
 * @link      https://widiz.com
 * @copyright 2017 Widiz
 *
 * @wordpress-plugin
 * Plugin Name:       Widiz
 * Description:       Simple way to integrate Widiz plugins with Wordpress
 * Version:           0.0.1
 * Author:            Widiz
 * Author URI:        https://widiz.com
 * Text Domain:       widiz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");

define('WIDIZ_BASEFILE', __FILE__);
/*-----------------------------------------*/

/*----------------------------------------------------------------------------*
 * Plugin Libraries
 *----------------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . 'includes/custom-wp-meta/metaboxes/meta_box.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-widiz-options.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-widiz-api.php';

/*----------------------------------------------------------------------------*
 * Plugin Settings
 *----------------------------------------------------------------------------*/

/* ----- Plugin Module: Settings ----- */
require_once plugin_dir_path(__FILE__) . 'includes/class-widiz-settings.php';

register_activation_hook(__FILE__, array('Widiz_Settings', 'activate'));
add_action('plugins_loaded', array('Widiz_Settings', 'get_instance'));
/* ----- Module End: Settings ----- */

/*----------------------------------------------------------------------------*
 * Include extensions and CPT
 *----------------------------------------------------------------------------*/

/* ----- Plugin Module: CPT ----- */
require_once plugin_dir_path(__FILE__) . 'includes/cpt/class-widiz-cpt.php';
add_action('plugins_loaded', array('Widiz_CPT', 'get_instance'));
/* ----- Module End: CPT ----- */


/* ----- Plugin Module: FormsHooks ----- */
require_once plugin_dir_path(__FILE__) . 'includes/class-widiz-forms-hooks.php';
add_action('plugins_loaded', array('Widiz_Forms_Hooks', 'get_instance'));
/* ----- Module End: FormsHooks ----- */

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once plugin_dir_path(__FILE__) . 'includes/class-widiz.php';

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, array('Widiz', 'activate'));
register_deactivation_hook(__FILE__, array('Widiz', 'deactivate'));

add_action('plugins_loaded', array('Widiz', 'get_instance'));

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-widiz-admin.php';
    add_action('plugins_loaded', array('Widiz_Admin', 'get_instance'));

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-widiz-admin-pages.php';
    add_action('plugins_loaded', array('Widiz_Admin_Pages', 'get_instance'));

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-widiz-admin-pages-settings.php';
    add_action('plugins_loaded', array('Widiz_Admin_Pages_Settings', 'get_instance'));

}

/*----------------------------------------------------------------------------*
 * Register Plugin Shortcode
 *----------------------------------------------------------------------------*/

/* ----- Plugin Module: Shortcode ----- */
// Admin Side
require_once plugin_dir_path(__FILE__) . 'includes/shortcode/class-widiz-shortcode-admin.php';
add_action('plugins_loaded', array('Widiz_Shortcode_Admin', 'get_instance'));

// Public Side
require_once plugin_dir_path(__FILE__) . 'includes/shortcode/class-widiz-shortcode-public.php';
add_action('plugins_loaded', array('Widiz_Shortcode_Public', 'get_instance'));
/* ----- Module End: Shortcode ----- */

/*----------------------------------------------------------------------------*
 * Handle AJAX Calls
 *----------------------------------------------------------------------------*/

/* ----- Plugin Module: AJAX ----- */
require_once plugin_dir_path(__FILE__) . 'includes/class-widiz-ajax.php';
add_action('plugins_loaded', array('Widiz_AJAX', 'get_instance'));
/* ----- Module End: AJAX ----- */
