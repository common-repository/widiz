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
 * Handle AJAX calls
 */
class Widiz_AJAX
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

        // Frontend AJAX calls
        add_action('wp_ajax_widiz_fields', [$this, 'ajaxGetFields']);
        add_action('wp_ajax_widiz_form_submit', [$this, 'ajaxFormSubmit']);
        add_action('wp_ajax_nopriv_widiz_form_submit', [$this, 'ajaxFormSubmit']);
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

    private function getCF7Fields($form_id){
        $contactForm = wpcf7_contact_form($form_id);

        if(!$contactForm) return [];

        $_fields = $contactForm->scan_form_tags();
        $fields = [];
        $allowedTypes = ['text', 'email', 'phone', 'tel', 'textarea'];
        foreach ($_fields as $field) {
            if(in_array($field['basetype'], $allowedTypes) !== false){
                $fields[] = ['label' => $field['name'], 'id' => $field['name'], 'type' => $field['basetype']];
            }
        }

        return $fields;
    }

    private function getListFields($list_id){
        $widizApi = Widiz_Api::get_instance();
        $fields = [];

        $_fields = $widizApi->get_leads_manager_fields($list_id);
        $allowedTypes = ['text', 'textarea'];

        foreach ($_fields as $field) {
            if(in_array($field->type, $allowedTypes) !== false){
                $fields[] = [
                    'label' => $field->name,
                    'id' => $field->id,
                    'type' => $field->type
                ];
            }
        }

        return $fields;
    }

    public function ajaxGetFields()
    {

        $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : null;
        $list_id = isset($_POST['list_id']) ? $_POST['list_id'] : null;

        if(!$list_id){
            wp_send_json_error();
            exit;
        }

        $response = [];

        if($form_id){
            $response['cf7'] = $this->getCF7Fields($form_id);
        }

        $response['list'] = $this->getListFields($list_id);

        wp_send_json_success($response);

        die();
    }

    public function ajaxFormSubmit()
    {
        $form_id = isset($_POST['form_id']) ? $_POST['form_id'] : null;

        if(!$form_id){
            return wp_send_json_error(['message' => "Invalid form"]);
        }

        $form = get_post($form_id);
        if(!$form){
            return wp_send_json_error(['success' => "Invalid form"]);
        }

        $data = isset($_POST['data']) && is_array($_POST['data']) ? $_POST['data'] : [];

        $formFields = get_post_meta($form_id, 'widiz_forms_custom_form_fields', true);
        $listId = get_post_meta($form_id, 'widiz_forms_custom_list_id', true);
        $errors = [];
        $isValidForm = true;
        $widizData = [];

        foreach ($formFields as $field) {
            $fieldName = $field['field_name'];
            $fieldDefault = $field['default_value'];
            $fieldType = $field['field_type'];
            $fieldListId = $field['widiz_field_id'];
            $userFieldValue = isset($data[$fieldListId]) ? $data[$fieldListId] : '';
            $fieldValue = !$userFieldValue && $fieldDefault ? $fieldDefault : $userFieldValue;
            $widizData[$fieldListId] = $fieldValue;

            if($fieldListId == "first_name" && !$userFieldValue){
                $errors[] = ['field' => $fieldListId, 'message' => 'This field is required'];
                $isValidForm= false;
                continue;
            }

            if($fieldListId == "email"){
                if(!$userFieldValue){
                    $errors[] = ['field' => $fieldListId, 'message' => 'This field is required'];
                    $isValidForm= false;
                    continue;
                }
                else if (!filter_var($userFieldValue, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = ['field' => $fieldListId, 'message' => 'Invalid email address'];
                    $isValidForm= false;
                    continue;
                }
            }
        }

        if(!$isValidForm){
           return wp_send_json_error(['errors' => $errors, 'message' => 'One or more fields have an error. Please check and try again.']);
        }

        $widizApi = Widiz_Api::get_instance();
        $response = $widizApi->create_leads_manager_lead($listId, $widizData);

        if(!$response){
            return wp_send_json_error(['message' => "Internal server error. Please try again."]);

        }

        wp_send_json_success(['message' => 'The form was submitted']);
    }
}
