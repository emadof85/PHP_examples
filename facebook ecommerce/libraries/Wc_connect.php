<?php
include("WooCommerce/autoload.php");

use Automattic\WooCommerce\Client;

class Wc_connect {
    public $woo;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('my_helper');
        $this->CI->load->library('session');

        if ($this->CI->session->userdata('logged_in') || $this->CI->session->userdata('cron_execute')) {
            $wc_app = $this->CI->basic->get_data("wc_connect_credentials",array("where"=>array("user_id"=>$this->CI->session->userdata("user_id"))));
            if (!empty($wc_app)) {
                $wc_data = $wc_app[0];

                $this->woo = new Client(
                    $wc_data['store_url'],
                    $wc_data['consumer_key'],
                    $wc_data['consumer_secret'],
                    array(
                        'version' => 'wc/v3'
                    )
                );
            } else {
                $this->woo = false;
            }
        } else {
            $this->woo = false;
        }
    }

    public function generate_auth_link($params = array())
    {
        extract($params);
        $endpoint = '/wc-auth/v1/authorize';

        $params = array(
            'app_name' => 'Test App',
            'scope' => 'read_write',
            'user_id' => $this->CI->session->userdata('user_id').'_'.base64_encode(serialize(array(
                    $store_url,
                    $store_name,
                    $store_currency
            ))),
            'return_url' => base_url('ecommerceexternal'),
            'callback_url' => base_url('ecommerceexternal/auth')
        );
        $query_string = http_build_query( $params );

        return $store_url . $endpoint . '?' . $query_string;
    }

    public function validate_auth_token($params = array())
    {
        $cron_types = array(
            'get_products',
            'get_categories',
            'get_settings',
            'create_order'
        );
        $user_inputs = explode("_", $params['user_id']);
        $user_id = $user_inputs[0];
        list($store_url, $store_name, $store_currency) = unserialize(base64_decode($user_inputs[1]));

        $this->CI->basic->insert_data('wc_connect_credentials', array(
            'user_id'           =>  $user_id,
            'consumer_key'      =>  $params['consumer_key'],
            'consumer_secret'   =>  $params['consumer_secret'],
            'store_url'         =>  $store_url,
            'store_name'        =>  $store_name,
            'store_currency'    =>  $store_currency
        ));

        foreach ($cron_types as $cron_type) {
            $this->CI->basic->insert_data('wc_cron_jobs', array(
                'user_id' => $user_id,
                'cron_type' => $cron_type
            ));
        }
    }
}