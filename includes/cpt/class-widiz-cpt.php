<?php
/**
 * Widiz.
 *
 * @package   Widiz_AJAX
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

/**
 * Register custom post types and taxonomies
 */
class Widiz_CPT
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
     * List of all Custom Post Types to be registered
     *
     * @since    1.0.0
     *
     * @var      array
     */
    private static $cpt_list = array();

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct()
    {
        self::load_cpt();
        add_action('init', array($this, 'register_cpt_and_taxonomies'));
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

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Assign Custom Post Types to class variable.
     *
     * @since     1.0.0
     */
    private static function load_cpt()
    {

        $plugin               = Widiz::get_instance();

        $cpt = array(
            'widizforms' => array(
                'labels'             => array(
                    'name'               => _x('Forms', 'post type general name', 'widiz'),
                    'singular_name'      => _x('Form', 'post type singular name', 'widiz'),
                    'menu_name'          => _x('Forms', 'admin menu', 'widiz'),
                    'name_admin_bar'     => _x('Forms', 'add new on admin bar', 'widiz'),
                    'add_new'            => _x('Add New', 'entry', 'widiz'),
                    'add_new_item'       => __('Add New Form', 'widiz'),
                    'new_item'           => __('New Form', 'widiz'),
                    'edit_item'          => __('Edit Form', 'widiz'),
                    'view_item'          => __('View Form', 'widiz'),
                    'all_items'          => __('All Forms', 'widiz'),
                    'search_items'       => __('Search Form', 'widiz'),
                    'parent_item_colon'  => __('Parent Forms:', 'widiz'),
                    'not_found'          => __('No Forms found.', 'widiz'),
                    'not_found_in_trash' => __('No Forms found in Trash.', 'widiz'),
                ),
                'description'        => __('Manage your forms', 'widiz'),
                'public'             => true,
                'show_ui'            => true,
                'rewrite'            => array('slug' => 'widizforms'),
                'capability_type'    => 'post',
                'menu_icon'          => 'dashicons-layout',
                'supports'           => array('title'),
                'show_in_menu'       => $plugin->get_plugin_slug() . '-main-page'
            ),
        );

        $prefix = 'widiz_forms_';

        $widizApi = Widiz_Api::get_instance();
        $widizLists = $widizApi->get_leads_manager_lists();

        $widizListsOptions = array_map(function($list){
            return ['label' => $list->name, 'value' => $list->id];
        }, $widizLists);

        $fields = array(
            array(
                'label' => 'Form type',
                'desc'  => 'Choose the type of form you will use for this integration',
                'id'    => $prefix.'mode',
                'type'  => 'radio',
                'options' => array (
                    'one' => array (
                        'label' => 'Custom Form 7',
                        'value' => 'cf7'
                    ),
                    'two' => array (
                        'label' => 'Create new form',
                        'value' => 'custom'
                    )
                )
            ),
            array(
                'label' => 'CF7 form',
                'id'    =>  $prefix.'cf7_form_id',
                'type'  => 'post_select',
                'default' => 'Select the CF7 form you will use',
                'post_type' => 'wpcf7_contact_form'
            ),
            array(
                'label' => 'Leads Manager List',
                'id'    => $prefix.'cf7_list_id',
                'type'  => 'select',
                'default' => 'Select the list you will use',
                'options' => $widizListsOptions,
            ),
            array(
                'label' => 'Link fields',
                'desc'  => 'Link fields between CF7 form and Widiz.',
                'id'    => $prefix . 'cf7_linked_fields',
                'type'  => 'repeatable',
                'sanitizer' => array(
                    'featured' => 'meta_box_santitize_boolean',
                    'title' => 'sanitize_text_field',
                    'desc' => 'wp_kses_data'
                ),
                'repeatable_prefix' => 'cf7',
                'repeatable_fields' => array (
                    array(
                        'label' => 'CF7 Field',
                        'id'    => 'cf7_field',
                        'type'  => 'select',
                        'options' => [],
                    ),
                    array(
                        'label' => 'Widiz Field',
                        'id'    => 'widiz_field_id',
                        'type'  => 'select',
                        'options' => $widizListsOptions
                    ),
                    array(
                        'label' => 'Default Value',
                        'id'    => 'default_value',
                        'type'  => 'text'
                    )
                ),
            ),
            array(
                'label' => 'Leads Manager List',
                'id'    => $prefix.'custom_list_id',
                'type'  => 'select',
                'default' => 'Select the list you will use',
                'options' => $widizListsOptions,
            ),
            array(
                'label' => 'Form fields',
                'desc'  => 'Add fields you want to show in your new form',
                'id'    => $prefix . 'custom_form_fields',
                'type'  => 'repeatable',
                'sanitizer' => array(
                    'featured' => 'meta_box_santitize_boolean',
                    'title' => 'sanitize_text_field',
                    'desc' => 'wp_kses_data'
                ),
                'repeatable_prefix' => 'custom',
                'repeatable_fields' => array (
                    array(
                        'label' => 'Field name',
                        'id'    => 'field_name',
                        'type'  => 'text'
                    ),
                    array(
                        'label' => 'Field Type',
                        'id'    => 'field_type',
                        'type'  => 'select',
                        'options' => [
                            ['value' => 'text', 'label' => 'Text'],
                            ['value' => 'textarea', 'label' => 'Textarea'],
                            ['value' => 'hidden', 'label' => 'Hidden (only send default data to Widiz)'],
                        ]
                    ),
                    array(
                        'label' => 'Widiz Field',
                        'id'    => 'widiz_field_id',
                        'type'  => 'select',
                        'options' => []
                    ),
                    array(
                        'label' => 'Default Value',
                        'id'    => 'default_value',
                        'type'  => 'text'
                    )
                ),
            )
        );

        $sample_box = new custom_add_meta_box( 'widizforms_options', 'Options', $fields, 'widizforms', true );
        self::$cpt_list = $cpt;


        // Add the custom columns to the book post type:
        add_filter( 'manage_widizforms_posts_columns', 'set_custom_edit_book_columns' );
        function set_custom_edit_book_columns($columns) {
            unset( $columns['author'] );
            $columns['shortcode'] = __( 'Shortcode', 'your_text_domain' );

            return $columns;
        }

        // Add the data to the custom columns for the book post type:
        add_action( 'manage_widizforms_posts_custom_column' , 'custom_book_column', 10, 2 );
        function custom_book_column( $column, $post_id ) {
            switch ( $column ) {
                case 'shortcode' :
                    $formMode = get_post_meta($post_id, 'widiz_forms_mode', true);
                    if($formMode == "cf7"){
                        $cf7FormId = get_post_meta($post_id, 'widiz_forms_cf7_form_id', true);
                        if(!is_array($cf7FormId) || !count($cf7FormId)){
                            continue;
                        }
                        $wpcf7 = wpcf7_contact_form($cf7FormId[0]);
                        if($wpcf7){
                            echo '<code>' . $wpcf7->shortcode() . '</code>';
                        }
                    }
                    else if($formMode == "custom"){
                        echo '<code>[widiz-form id="' . $post_id . '"]</code>';
                    }
                    break;
            }
        }

        function your_columns_head($defaults) {
            $new = array();
            $shortcode = $defaults['shortcode'];
            unset($defaults['shortcode']);

            foreach($defaults as $key=>$value) {
                if($key=='date') {
                   $new['shortcode'] = $shortcode;
                }
                $new[$key]=$value;
            }

            return $new;
        }

        add_filter('manage_widizforms_posts_columns', 'your_columns_head');
    }

    /**
     * Register all Custom Post Types and Taxonomies.
     *
     * @since     1.0.0
     */
    public function register_cpt_and_taxonomies()
    {
        // Register CPT
        foreach (self::$cpt_list as $slug => $args) {
            register_post_type($slug, $args);
        }
    }

}
