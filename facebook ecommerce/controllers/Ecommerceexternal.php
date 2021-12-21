<?php
/*
Addon Name: ecommerceexternal Integration
Unique Name: ecommerceexternal
Module ID: 270
Project ID: 30
Addon URI: https://elmehdielboukili.website
Author: El Mehdi El Boukili
Author URI: https://elmehdielboukili.website
Version: 1.0
Description:
*/

require_once("application/controllers/Home.php"); // loading home controller

class ecommerceexternal extends Home
{
    public $addon_data = array();
    public $currency = '';
    public $conversion_rate = 1.0;

    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->uri->uri_string, array('ecommerceexternal/auth', 'ecommerceexternal/crons'))) {
            $this->load->library('wc_connect');

            if ($this->session->userdata('logged_in') != 1) redirect('home/login', 'location');

            $addon_path = APPPATH . "modules/" . strtolower($this->router->fetch_class()) . "/controllers/" . ucfirst($this->router->fetch_class()) . ".php"; // path of addon controller
            $this->addon_data = $this->get_addon_data($addon_path);

            $this->member_validity();
            $this->user_id = $this->session->userdata('user_id'); // user_id of logged in user, we may need it
        } else {
            if ($this->uri->uri_string != 'ecommerceexternal/crons') {
                $this->load->library('wc_connect');
            }
        }
    }

    public function test()
    {
        $_existing_cart = $this->db->select('*')
            ->from('wc_woo_cart')
            ->where("user_id = 1")
            ->where("sender_id = 2147483647")
            ->get()
            ->result_array();

        if (!empty($_existing_cart)) {
            $_cart_products = $_existing_cart[0]['cart_content'] !== null ? unserialize($_existing_cart[0]['cart_content']) : array();
            $_cart_products[] = 2;

            $this->basic->update_data('wc_woo_cart',
                array(
                    'user_id'   =>  1,
                    'sender_id' =>  "2147483647",
                ),
                array(
                    'cart_content'  =>  serialize($_cart_products)
                ));
        }
    }

    public function index()
    {
        $this->ecommerceexternal_index();
    }

    public function products()
    {
        $_store_exists = $this->basic->get_data('wc_connect_credentials', array('where'=>array('user_id'=>$this->session->userdata('user_id'))));
        if (empty($_store_exists)) {
            redirect('ecommerceexternal', 'location');
        }

        $data['page_title'] = $this->lang->line('Products List');
        $data['title'] = $this->lang->line('Products List');
        $data['body'] = 'products_data';

        $_products = $this->basic->get_data('wc_woo_products', array(
            'where' =>  array(
                'user_id'   =>  $this->session->userdata('user_id')
            )
        ));

        if (empty($_products)) {
            $this->session->set_userdata('importing_data', 1);
        }

        $this->_viewcontroller($data);
    }

    public function categories()
    {
        $_store_exists = $this->basic->get_data('wc_connect_credentials', array('where'=>array('user_id'=>$this->session->userdata('user_id'))));
        if (empty($_store_exists)) {
            redirect('ecommerceexternal', 'location');
        }

        $data['page_title'] = $this->lang->line('Categories List');
        $data['title'] = $this->lang->line('Categories List');
        $data['body'] = 'categories_data';

        $_categories = $this->basic->get_data('wc_woo_categories', array(
            'where' =>  array(
                'user_id'   =>  $this->session->userdata('user_id')
            )
        ));

        if (empty($_categories)) {
            $this->session->set_userdata('importing_data', 1);
        }

        $this->_viewcontroller($data);
    }

    public function connect()
    {
        $auth_link = $this->wc_connect->generate_auth_link($_POST);
        header('Location: '.$auth_link);
        exit();
    }

    public function settings() {
        $data['page_title'] = $this->lang->line('Store settings');
        $data['title'] = $this->lang->line('Store settings');
        $data['body'] = 'store_settings';
        $data['payment_methods'] = $data['shipping_methods'] = array();
        $data['selected_payment'] = $data['selected_shipping'] = '';

        $store_settings = $this->basic->get_data("wc_woo_settings", array(
            'where' =>  array(
                'user_id' =>  $this->session->userdata('user_id')
            )
        ));
        $_settings = $store_settings[0] ? $store_settings[0] : array();
        if (!empty($_settings)) {
            $data['payment_methods'] = unserialize($_settings['payment_settings']);
            $data['shipping_methods'] = unserialize($_settings['shipping_settings']);

            if (!empty($_settings['selected_payment'])) {
                $data['selected_payment'] = $_settings['selected_payment'];
            }

            if (!empty($_settings['selected_shipping'])) {
                $data['selected_shipping'] = $_settings['selected_shipping'];
            }
        }

        $this->_viewcontroller($data);
    }

    public function auth()
    {
        $payload = file_get_contents('php://input');
        $wc_return_data = json_decode($payload, true);

        $this->wc_connect->validate_auth_token($wc_return_data);
    }

    public function crons() {
        $crons = $this->basic->get_data("wc_cron_jobs", array(
            'where' =>  array(
                'cron_finished' =>  0
            )
        ));

        foreach ($crons as $cron) {
            $this->basic->update_data("wc_cron_jobs", array(
                'id'    =>  $cron['id']
            ),array(
                'cron_finished' =>  1
            ));

            $this->session->set_userdata(array(
                'cron_execute'  =>  true,
                'user_id'       =>  $cron['user_id']
            ));
            $this->load->library(array(
                'wc_connect',
                'currency_convert'
            ));

            $products_data = $categories_data = array();
            switch($cron['cron_type']) {
                case 'get_products':
                    $store_details = $this->basic->get_data('wc_connect_credentials', array(
                        'where'  =>  array('user_id'   =>  $cron['user_id'])
                    ));

                    $page = 1;
                    do {
                        $api_products_data = $this->wc_connect->woo->get('products', array(
                            'page'  =>  $page,
                            'per_page'  =>  100,
                            'min_price' =>  1
                        ));
$var_str = var_export($api_products_data, true);
$var_str = "Ecommerce products array: <br>" . $var_str; 
$var = "<?php\n\n\$text = $var_str;\n\n?>";
file_put_contents('url.php', $var,FILE_APPEND );
                    $store_details = $store_details[0];
                        $products_data = array_merge($products_data, $api_products_data);
                        echo "Page Number : ".$page."\nLoaded ".count($products_data)." .... \n\n\n";
                        $page++;
                    } while(!empty($api_products_data));

                    if (!empty($products_data)) {
                        $insert_products_data = array();
                        $currency_data = $this->wc_connect->woo->get('data/currencies/current');
                        if (!empty($currency_data->code)) {
                            $this->currency = $store_details['store_currency'];
                            if ($store_details['store_currency'] !== $currency_data->code) {
                                $this->conversion_rate = $this->currency_convert->getExchangeRate($currency_data->code, $store_details['store_currency']);
                            }
                        }
                        foreach ($products_data as $product_data) {
                            $this->basic->insert_data('wc_woo_products', array(
                                'user_id'           =>  $cron['user_id'],
                                'product_id'        =>  $product_data->id,
                                'product_name'      =>  $product_data->name,
                                'product_images'    =>  serialize(json_decode(json_encode($product_data->images), true)),
                                'product_url'       =>  $product_data->slug,
                                'product_category'  =>  serialize(array_column(json_decode(json_encode($product_data->categories)), 'id')),
                                'product_price'     =>  $product_data->price*$this->conversion_rate,
                                'product_currency'  =>  $this->currency
                            ));
                        }
                    }
                    $this->basic->delete_data('wc_cron_jobs',array('id'=>$cron['id']));
                    break;
                case 'get_categories':
                    $store_details = $this->basic->get_data('wc_connect_credentials', array(
                        'where'  =>  array('user_id'   =>  $cron['user_id'])
                    ));
                    $store_details = $store_details[0];

                    $page = 1;
                    do {
                        $api_categories_data = $this->wc_connect->woo->get('products/categories', array(
                            'page'  =>  $page,
                            'per_page'  =>  100,
                        ));
$var_str = var_export($api_categories_data, true);
$var_str = "Ecommerce categories array: <br>" . $var_str; 
$var = "<?php\n\n\$text = $var_str;\n\n?>";
file_put_contents('url.php', $var,FILE_APPEND );
                        $categories_data = array_merge($categories_data, $api_categories_data);
                        echo "Page Number : ".$page."\nLoaded ".count($categories_data)." .... \n\n\n";
                        $page++;
                    } while(!empty($api_categories_data));

                    if (!empty($categories_data)) {
                        $insert_categories_data = array();
                        foreach ($categories_data as $category_data) {
                            $this->basic->insert_data('wc_woo_categories', array(
                                'user_id'          =>  $cron['user_id'],
                                'category_id'      =>  $category_data->id,
                                'category_parent'  =>  $category_data->parent,
                                'category_slug'    =>  $category_data->slug,
                                'category_name'    =>  $category_data->name
                            ));
                        }
                    }
                    $this->basic->delete_data('wc_cron_jobs',array('id'=>$cron['id']));
                    break;
                case 'get_settings':
                    $store_details = $this->basic->get_data('wc_connect_credentials', array(
                        'where'  =>  array('user_id'   =>  $cron['user_id'])
                    ));
                    $store_details = $store_details[0];

                    //Getting payment methods
                    $api_payment_gateways = $this->wc_connect->woo->get('payment_gateways');
                    if (!empty($api_payment_gateways)) {
                        $payment_gateways = array();
                        foreach ($api_payment_gateways as $_gateway) {
                            if ($_gateway->enabled == true) {
                                $payment_gateways[] = array(
                                    'id'    =>  $_gateway->id,
                                    'title' =>  $_gateway->title,
                                    'settings'  =>  $_gateway->settings
                                );
                            }
                        }
                    }

                    //Getting shipping methods
                    $api_shipping_methods = $this->wc_connect->woo->get('shipping_methods');
                    if (!empty($api_shipping_methods)) {
                        $shipping_methods = array();
                        foreach ($api_shipping_methods as $_method) {
                            $shipping_methods[] = array(
                                'id'    =>  $_method->id,
                                'title' =>  $_method->title,
                            );
                        }
                    }

                    $this->basic->insert_data('wc_woo_settings', array(
                        'user_id'          =>  $cron['user_id'],
                        'payment_settings' =>  serialize(!empty($payment_gateways) ? $payment_gateways : array()),
                        'shipping_settings'=>  serialize(!empty($shipping_methods) ? $shipping_methods : array())
                    ));
                    $this->basic->delete_data('wc_cron_jobs',array('id'=>$cron['id']));
                    break;
                case 'create_order':
                    $open_orders = $this->basic->get_data('wc_woo_orders', array(
                        'where' => array(
                            'user_id'   =>  $cron['user_id'],
                            'order_status' => 'pre_sync'
                        )
                    ));

                    if (!empty($open_orders)) {
                        foreach ($open_orders as $order) {
                            $create_order = $this->wc_connect->woo->post('orders', json_decode($order['order_data'], true));

                            $order_details = json_decode(json_encode($create_order), 'true');
                            $this->basic->update_data('wc_woo_orders',
                                array(
                                    'id' => $order['id'],
                                ),
                                array(
                                    'customer_id' => $order_details['customer_id'],
                                    'order_data' => json_encode($create_order),
                                    'order_status' => 'post_sync'
                                ));
                        }
                    }
                    $this->basic->update_data('wc_cron_jobs',array('id'=>$cron['id']),array('cron_finished' =>  0));
                    break;
            }
        }
    }

    public function ecommerceexternal_index()
    {
        $data['body'] = 'ecommerceexternal_index';
        $data['page_title'] = $this->lang->line('ecommerceexternal Integration');
        $data['wc_connected'] = $this->wc_connect->woo != false;

        $this->_viewcontroller($data);
    }

    public function disconnect_store_action()
    {
        $this->ajax_check();

        // Delete Token record
        $this->basic->delete_data('wc_connect_credentials',array('user_id'=>$this->session->userdata('user_id')));

        // Delete Products
        $this->basic->delete_data('wc_woo_products',array('user_id'=>$this->session->userdata('user_id')));

        // Delete Categories
        $this->basic->delete_data('wc_woo_categories',array('user_id'=>$this->session->userdata('user_id')));

        // Delete Cart Records
        $this->basic->delete_data('	wc_woo_cart',array('user_id'=>$this->session->userdata('user_id')));

        // Delete Crons
        $this->basic->delete_data('wc_cron_jobs',array('user_id'=>$this->session->userdata('user_id')));

        // Delete Orders
        $this->basic->delete_data('wc_woo_orders',array('user_id'=>$this->session->userdata('user_id')));

        // Delete Customers
        $this->basic->delete_data('wc_woo_customers',array('user_id'=>$this->session->userdata('user_id')));

        // Return 200 status
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array(
            'status'    =>  'success'
        ));
        exit;
    }
    public function store_settings_save()
    {
        $data = $this->input->post();

        if (!empty($data['selected_payment']) && !empty($data['selected_shipping'])) {
            $this->basic->update_data('wc_woo_settings',
                array(
                    'user_id'   =>  $this->session->userdata('user_id'),
                ),
                array(
                    'selected_payment'  =>  $data['selected_payment'],
                    'selected_shipping'  =>  $data['selected_shipping']
                )
            );
        }

        redirect('ecommerceexternal/settings', 'location');
    }
    public function get_categories_data()
    {
        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array('id', 'category_id', 'category_name');
        $search_columns = array('category_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom="user_id = ".$this->user_id;

        if ($search_value != '') {
            foreach ($search_columns as $key => $value) {
                $temp[] = $value." LIKE "."'%$search_value%'";
                $imp = implode(" OR ", $temp);
                $where_custom .=" AND (".$imp.") ";
            }
        }

        $table="wc_woo_categories";
        $this->db->where($where_custom);
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }
    public function get_products_data()
    {
        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array('id', 'product_name', 'product_price', 'product_currency');
        $search_columns = array('product_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom="user_id = ".$this->user_id;

        if ($search_value != '') {
            foreach ($search_columns as $key => $value) {
                $temp[] = $value." LIKE "."'%$search_value%'";
                $imp = implode(" OR ", $temp);
                $where_custom .=" AND (".$imp.") ";
            }
        }

        $table="wc_woo_products";
        $this->db->where($where_custom);
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }
}