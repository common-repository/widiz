<?php
/**
 * Widiz.
 *
 * @package   Widiz_Settings
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
 * Plugin API
 */
class Widiz_Api
{

    protected static $instance = null;

    private $debug = true;
    private $baseurl = 'https://app.widiz.com/plugins/integrations';

    private function __construct()
    {

    }

    private function getAuthorizationData(){
        return Widiz_Options::read('widiz_api_key') . ':' . Widiz_Options::read('widiz_api_token');
    }

    private function remoteRequest($url, $method = "GET", $data = []){
        $abs_url = $this->baseurl . $url;

        if($method == "GET"){
            if(!empty($data)){
                $abs_url .= '?' . http_build_query($data);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $abs_url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getAuthorizationData());

        if($this->debug){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response);
        return json_last_error() == JSON_ERROR_NONE ? $json : false;
    }

    private function getRequest($url, $data = []){
        return $this->remoteRequest($url, 'GET', $data);
    }

    private function postRequest($url, $data = []){
        return $this->remoteRequest($url, 'POST', $data);
    }

    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function cacheMethod($transient, $callback){
        $cache = get_transient($transient);
        if ($cache === false) {
            $new_cache = $callback();
            set_transient($transient, $new_cache, 3600);
            return $new_cache;
        }
        return $cache;
    }

    public function clearCache(){
        global $wpdb;
        $sql = "
            delete from {$wpdb->options}
            where option_name like '_transient_widiz_cache%' or option_name like '_transient_timeout_widiz_cache%'
        ";
        return $wpdb->query($sql);
    }

    public function isConfigured(){
        return Widiz_Options::read('widiz_api_key') && Widiz_Options::read('widiz_api_token');
    }

    public function isValid(){
        $response = $this->getRequest('/api/check');
        if($response && $response->success){
            return true;
        }
        return false;
    }

    public function isValidCached(){
        return $this->cacheMethod('widiz_cache_api_status', function(){
            $response = $this->getRequest('/api/check');
            if($response && $response->success){
                return true;
            }
            return false;
        });
    }

    public function get_crm_fields(){
        $fieldsResponse = $this->getRequest('fields');

        if($fieldsResponse && $fieldsResponse->success){
            return array_map(function($field) {
                return [
                    'id' => $field->id,
                    'name' => $field->name
                ];
            }, $fieldsResponse->fields);
        }

        return [];
    }

    public function get_leads_manager_lists(){
        return $this->cacheMethod('widiz_cache_lists', function() {
            $listsResponse = $this->getRequest('/leads/lists');
            if($listsResponse && $listsResponse->success){
                return $listsResponse->lists;
            }
            return [];
        });
    }

    public function get_leads_manager_fields($listId){
        return $this->cacheMethod('widiz_cache_fields_lid_' . $listId, function() use ($listId){
            $fieldsResponse = $this->getRequest('/leads/fields', ['list' => $listId]);
            if($fieldsResponse && $fieldsResponse->success){
                return $fieldsResponse->fields;
            }
            return [];
        });
    }

    public function create_leads_manager_lead($listId, $leadData = null){
        $leadResponse = $this->postRequest('/leads/create', [
            'list' => $listId,
            'data' => $leadData
        ]);

        if($leadResponse && $leadResponse->success){
            return true;
        }

        return false;
    }

}
