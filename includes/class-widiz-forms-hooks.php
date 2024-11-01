<?php
/**
 * Widiz.
 *
 * @package   Widiz_DB
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
 * Setup custom DB tables
 */
class Widiz_Forms_Hooks
{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;


    private function __construct()
    {
        add_action( 'wpcf7_submit', [$this, 'submited_cf7'], 10, 2 );

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
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function submited_cf7($instance, $result){
        $formId = $instance->id();

        if(!isset($result['status']) || $result['status'] == "validation_failed"){
            return false;
        }

        $query = [0 => (string)$formId];
        $queryVal = serialize($query);

        $queryArgs = [
           'post_type' => 'widizforms',
           'meta_query' => [
                [
                    'key' => 'widiz_forms_mode',
                    'value' => 'cf7'
                ],
                [
                    'key' => 'widiz_forms_cf7_form_id',
                    'value' => $queryVal
                ]
            ]
        ];

        $widizForms = get_posts($queryArgs);

        foreach ($widizForms as $widizForm) {
            $listId = get_post_meta($widizForm->ID, 'widiz_forms_cf7_list_id', true);
            $linkedFields = get_post_meta($widizForm->ID, 'widiz_forms_cf7_linked_fields', true);

            $crmData = [];

            foreach ($linkedFields as $linkedField) {
                $fieldKey = $linkedField['cf7_field'];
                $fieldListId = $linkedField['widiz_field_id'];
                $fieldDefault = $linkedField['default_value'];

                if(!$fieldListId){
                    continue;
                }

                $fieldValue = !isset($_POST[$fieldKey]) || !$_POST[$fieldKey] ? $fieldDefault : $_POST[$fieldKey];
                $crmData[$fieldListId] = $fieldValue;
            }

            $widizApi = Widiz_Api::get_instance();
            $response = $widizApi->create_leads_manager_lead($listId, $crmData);
        }
    }
}
