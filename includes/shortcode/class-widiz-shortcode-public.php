<?php
/**
 * Widiz
 *
 * @package   Widiz_Shortcode_Public
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
 * Handle Plugin Shortcode Public Side Features
 */
class Widiz_Shortcode_Public
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
     * Initialize the class
     *
     * @since     1.0.0
     */
    private function __construct()
    {
        /**
         * Call $plugin_slug from public plugin class.
         */
        $plugin               = Widiz::get_instance();
        $this->plugin_slug    = $plugin->get_plugin_slug();
        $this->plugin_version = $plugin->get_plugin_version();
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
     * Render Shortcode [widiz-form]
     *
     * @since     1.0.0
     */
    public function render_widiz_form($atts, $content = "")
    {
        $parameters = shortcode_atts(array(
            'id' => '',
            'submit-text' => 'Send',
            'submit-class' => 'widiz-form-submit',
        ), $atts);

        $id = (int) $parameters['id'];

        if (!$id) {
            return '';
        }

        $html =  '<form id="widizForm' . $id . '" class="widiz-form" method="POST">';

        $html .= '<input type="hidden" name="form_id" value="'.$id.'">';

        $fields = get_post_meta($id, 'widiz_forms_custom_form_fields', true);

        foreach ($fields as $field) {
            $fieldType = $field['field_type'];
            $fieldNameAttr = 'data['.$field['widiz_field_id'].']';

            if($fieldType == "hidden"){
                continue;
            }

            $labelHtml = '<label>' . $field['field_name'] . '</label>';

            if($fieldType == 'text'){
                $inputHtml = '<input type="' . $fieldType . '" data-field="'.$field['widiz_field_id'].'" name="'.$fieldNameAttr.'">';
            }
            else if($fieldType == 'textarea'){
                $inputHtml = '<textarea type="' . $fieldType . '" data-field="'.$field['widiz_field_id'].'" name="'.$fieldNameAttr.'"></textarea>';
            }

            $formGroup = '<p>' . $labelHtml . $inputHtml . '</p>';
            $html .= $formGroup;
        }

        $submitButton = '<p class="widiz-submit-wrapper"><input type="submit" value="' . $parameters['submit-text'] . '" class="' . $parameters['submit-class'] . '"></p>';
        $html .= $submitButton;
        $html .= '<div class="widiz-response"></div></form>';
        return $html;
    }
}

add_shortcode('widiz-form', array(Widiz_Shortcode_Public::get_instance(), 'render_widiz_form'));
