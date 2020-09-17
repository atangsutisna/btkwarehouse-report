<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use DusanKasan\Knapsack\Collection;

class Purchase_order extends Admin_Controller 
{
    const DIR_VIEW = 'purchase_order';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('image_manager');

		$this->load->config('btkcommerce');
        $this->commerce_config = $this->config->item('commerce');
        
        $this->load->helper('utf8');

        $this->load->model('Purchase_order_model', 'purchase_order');
        $this->load->model('search_product_model');
    }

	public function index()
	{
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'read_purchase_order')) {
            redirect('forbidden', 'refresh');
        }

        $params = array(
            'js_resources' => ['assets/js/purchase_order/index.js']
        );
        $this->load->template(self::DIR_VIEW.'/index', $params);
    }

	public function view($id)
	{
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'read_purchase_order')) {
            redirect('forbidden', 'refresh');
        }

        if (!isset($id)) {
            show_404();
        }

        $this->load->model('Purchase_order_model', 'purchase_order');
        $params = array(
            'purchase_order_id' => $id,
            'supplier' => $this->purchase_order->get_supplier($id),
            'js_resources' => ['assets/js/purchase_order/form.js']
        );
        $this->load->template(self::DIR_VIEW.'/view', $params);
    }

    public function new_form() 
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'create_purchase_order')) {
            redirect('forbidden', 'refresh');
        }

        $params = array(
            'js_resources' => ['assets/js/purchase_order/form.js']
        );
        $this->load->template(self::DIR_VIEW.'/_form', $params);
    }

    public function update($id)
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'update_purchase_order')) {
            redirect('forbidden', 'refresh');
        }

        if (!isset($id)) {
            show_404();
        }

        $this->load->model('Purchase_order_model', 'purchase_order');
        $params = array(
            'purchase_order_id' => $id,
            'supplier' => $this->purchase_order->get_supplier($id),
            'js_resources' => ['assets/js/purchase_order/form.js']
        );
        $this->load->template(self::DIR_VIEW.'/_form', $params);
    }

    public function print($id = NULL)
    {
        if ($id == NULL) {
            show_404();
        }

        $this->load->model('Purchase_order_model', 'purchase_order');
        $purchase_order = $this->purchase_order->find_one($id, TRUE);
        if ($purchase_order == NULL) {
            show_404();
        }
        $purchase_order_items = [];
        foreach ($purchase_order->order_items as $order_item) {
            $options = [];
            for ($option_numb = 1; $option_numb <= 5; $option_numb++) {
                $option = "option".$option_numb;
                $option_value = "option_value".$option_numb;
                if ($order_item->$option != NULL) {
                    array_push($options, [
                        'name' => $order_item->$option,
                        'value' => $order_item->$option_value,
                    ]);
                }    
            }

            $product_image = $this->image_manager->get_image_uri($order_item->product_image) ?? $this->image_manager->get_default_image_uri();
            array_push($purchase_order_items, [
                'product_id' => $order_item->product_id,
                'product_name' => $order_item->product_name,
                'product_model' => $order_item->product_model,
                'product_image' => $product_image,
                'price' => $order_item->price,
                'qty' => $order_item->qty,
                'qty_unit' => $order_item->qty_unit,
                'subtotal' => $order_item->subtotal,
                'options' => $options
            ]);
        }

        $view = $this->load->view(self::DIR_VIEW. '/print', [
            'purchase_order' => $purchase_order,
            'purchase_order_items' => $purchase_order_items
        ], TRUE);
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5']);
        $mpdf->writeHTML($view);
        $mpdf->output();
    }


    public function confirm($purchase_order_id)
    {
        $this->load->library('purchase_order_manager');
        
        $this->purchase_order_manager->confirm($purchase_order_id);
    }

    public function create() 
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'create_purchase_order')) {
            redirect('forbidden', 'refresh');
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $supplier_id = $this->input->post('supplier_id');
            $product_ids = $this->input->post('product_qtys');
            $qty_unit_ids = $this->input->post('qty_unit_ids');

            $product_ids = array_filter($product_ids, function($qty){
                return $qty > 0;
            });
            
            $this->load->model('language_model');
            $supplier_product_params = [
                'language_id' => $this->language_model->get_default_language_id(),
                'supplier_id' => $supplier_id
            ];
            $this->load->library('supplier', $supplier_product_params);
            $criterion = [
                'product_ids' => array_keys($product_ids)
            ];
            $products = $this->supplier->get_products($criterion, $first = 0, $count = 500);
            $supplier = $this->supplier->get_profile();

            $purchase_order = [
                'supplier_id' => $supplier->supplier_id,
                'supplier_name' => $supplier->name,
                'purchase_order_date' => date('Y-m-d H:i:s'),
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'order_items' => [],
                'total' => 0
            ];

            foreach ($products as $product) {
                $qty = $product_ids[$product->product_id];
                log_message("debug", "Find qty unit for product id {$product->product_id}");
                $qty_unit_id = $qty_unit_ids[$product->product_id];
                
                $purchase_order_item = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                    'qty_unit_id' => $qty_unit_id,
                    'qty_rasio' => 1,
                    'subtotal' => 0,
                ];

                if ($product->qty_unit_id !== $qty_unit_id) {
                    //get product variant rasio
                    $product_variants = $this->search_product_model->get_variant($product->product_id);
                    $purchase_order_item['qty_rasio'] = $product_variants[0]->qty_rasio;
                }
                
                $purchase_order['order_items'][] = $purchase_order_item;
            }

            $purchase_order_id = $this->purchase_order->insert_or_update($purchase_order);
            redirect('purchase_order/update/'. $purchase_order_id);
        }

        $supplier_id = $this->input->get('supplier_id');
        $supplier_products = [];
        $product_variants = [];
        $supplier = NULL;
        if (isset($supplier_id)) {
            $this->load->model('language_model');
            $this->load->library('supplier', [
                'language_id' => $this->language_model->get_default_language_id(),
                'supplier_id' => $supplier_id,
            ]);
    
            $criterion = [];
            $supplier_products = $this->supplier->get_products($criterion, $first = 0, $count = 500);
            array_walk($supplier_products, function(&$product) {
                if ($product->image != null && file_exists($this->commerce_config['base_image_path'] .'/'. $product->image)) {
                    $product->image = $this->image_manager->resize($product->image, 40, 40);
                } else if ($product->image != null && !file_exists($this->commerce_config['base_image_path'] .'/'. $product->image)){
                    $product->image = $this->image_manager->resize('no_image.png', 40, 40);
                } else if ($product->image == null) {
                    $product->image = $this->image_manager->resize('no_image.png', 40, 40);
                }
            });    
            $supplier = $this->supplier->get_profile();

            $product_ids = array_map(function($supplier_product){
                return $supplier_product->product_id;
            }, $supplier_products);
    
            $product_variants = $this->search_product_model->get_variants($product_ids);    
        }

        $params = [
            'supplier_products' => $supplier_products,
            'product_variants' => $product_variants,
            'supplier' => $supplier,
            'js_resources' => ['assets/js/purchase_order/create.js']            
        ];

        if (isset($supplier_id)) {
            $params['supplier_id'] = $supplier_id;
            $params['js_resources'] = ['assets/js/purchase_order/update.js'];        
        }


        $this->load->template(self::DIR_VIEW.'/create', $params);
    }

    public function load_supplier_product()
    {
        $this->load->model(['language_model','search_product_model']);
        $supplier_id = $this->input->get('supplier_id');
        $this->load->library('supplier', [
            'language_id' => $this->language_model->get_default_language_id(),
            'supplier_id' => $supplier_id,
        ]);

        $product_type = $this->input->get('product_type');
        $under_stock_minimum = $this->input->get('under_stock_minimum');
        $stock_minus = $this->input->get('stock_minus');
        $available_stock = $this->input->get('available_stock');
        $out_of_stock = $this->input->get('out_of_stock');
        
        $criterion = [];
        if (isset($product_type)) {
            $criterion['product_type'] = $product_type;
        }

        if (isset($under_stock_minimum)) {
            $criterion['under_stock_minimum'] = filter_var($under_stock_minimum, FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($stock_minus)) {
            $criterion['stock_minus'] = $filter_var($stock_minus, FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($available_stock)) {
            $criterion['available_stock'] = filter_var($available_stock, FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($out_of_stock)) {
            $criterion['out_of_stock'] = filter_var($out_of_stock, FILTER_VALIDATE_BOOLEAN);
        }

        $supplier_products = $this->supplier->get_products($criterion, $first = 0, $count = 5000);
        $product_ids = array_map(function($supplier_product){
            return $supplier_product->product_id;
        }, $supplier_products);

        $product_variants = [];
        if (count($product_ids) > 0) {
            $product_variants = $this->search_product_model->get_variants($product_ids);
        }
        
        $params = [
            'supplier_products' => $supplier_products,
            'product_variants' => $product_variants,
            'supplier_id' => $supplier_id
        ];
        echo $this->load->view(self::DIR_VIEW.'/supplier_product', $params, TRUE);
    }

    function test_coll()
    {
        var_dump($finded);
    }

}