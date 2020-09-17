<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @deprecated
 * use import_product_manager instead
 */
class Product_bulk_model extends CI_Model
{
	protected $store_id = 0;
    protected $app_config;

    function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model', 'category_model');
        
        $this->load->config('btkcommerce');
        $this->app_config = $this->config->item('commerce');
    }

    public function get_language_id()
    {
        $this->db->select('store_id, key, value as setting_value');
        $this->db->from('setting');
        $this->db->where('store_id', $this->store_id);
        $this->db->where('key', 'config_language');
        $query_setting = $this->db->get();
        if ($query_setting->row() == NULL) {
            return NULL;
        }

        $this->db->select('language_id');
        $this->db->from('language');
        $this->db->where('code', $query_setting->row()->setting_value);
        $query_language = $this->db->get();
        if ($query_language->row() == NULL) {
            return NULL;
        }

        return $query_language->row()->language_id;
    }    

    protected function insert_categories($language_id, $raw_categories)
    {
    	//check all by names
        $this->load->model('Category_model', 'category_model');
    	$candidate_categories = array_unique($raw_categories);
        $categories = $this->category_model->find_by_names($language_id, $candidate_categories);
        $new_categories = array_filter($candidate_categories, function($candidate_category) use ($categories) {
            $category_key = array_search($candidate_category, array_column($categories, 'name'));
            if (gettype($category_key) == 'integer') {
                return FALSE;
            }

            return TRUE;
        });

        $total_categories = count($new_categories);
        if ($total_categories > 0) {
            log_message("debug", "Attempting to insert {$total_categories} new categories");
            foreach ($new_categories as $item) {
                // name is required
                // description is required
                if ($item != NULL) {
                    $this->db->insert('category', [
                        'parent_id' => 0,
                        'top' => 0,
                        'column' => 1,
                        'sort_order' => 0,
                        'status' => 1,
                        'date_added' => date('Y-m-d H:i:s'),
                        'date_modified' => date('Y-m-d H:i:s'),
                    ]);

                    $category_id = $this->db->insert_id();
                    $this->db->insert('category_description', [
                        'category_id' => $category_id,
                        'language_id' => $language_id,
                        'name' => $item,
                        'description' => $item,
                        'meta_title' => '',
                        'meta_description' => '',
                        'meta_keyword' => '',
                    ]);
                    $this->db->insert('category_path', [
                        'category_id' => $category_id,
                        'path_id' => $category_id,
                        'level' => 0
                    ]);        
                }
                
            }            
        }

        $added_categories = $this->category_model->find_by_names($language_id, $candidate_categories);
    	return $added_categories;
    }

    /**
    * Just insert if the names doesnt exist on database
    **/
    protected function insert_suppliers($raw_suppliers)
    {
        $this->load->model('Supplier_model', 'supplier_model');
    	$candidate_suppliers = array_unique($raw_suppliers);
        $suppliers = $this->supplier_model->find_by_names($candidate_suppliers);
        $new_suppliers = array_filter($candidate_suppliers, function($candidate_supplier) use ($suppliers) {
            $supplier_key = array_search($candidate_supplier, array_column($suppliers, 'name'));
            if (gettype($supplier_key) == 'integer') {
                return FALSE;
            }

            return TRUE;
        });
        $total_new_suppliers = count($new_suppliers);
        if ($total_new_suppliers > 0) {
            log_message("debug", "Attempting to insert {$total_new_suppliers} new suppliers");
            $suppliers = array_map(function($supplier_name){
                return [
                    'name' => $supplier_name,
                    'description' => $supplier_name,
                    'sort_order' => 0
                ];
            }, $new_suppliers);
            $this->db->insert_batch('supplier', $suppliers);
        }
    	
        $added_suppliers = $this->supplier_model->find_by_names($candidate_suppliers);
    	return $added_suppliers;
    }

    function insert_products($language_id, $raw_products)
    {
        $this->load->model('Product_model', 'product_model');
        $candidate_models = array_map(function($product){
            return $product['model'];
        }, $raw_products);
        $registered_models = $this->product_model->find_by_models($candidate_models);
        $new_products = array_filter($raw_products, function($product) use ($registered_models) {
            $model_key = array_search($product['model'], array_column($registered_models, 'model'));
            if (gettype($model_key) == 'integer') {
                return FALSE;
            }

            return TRUE;
        });

        $total_new_products = count($new_products);
        log_message("debug", "Attempting to insert {$total_new_products} new products");
        if ($total_new_products == 0) {
            log_message("info", "Empty new products");
            return FALSE;
        }

    	//asumsikan tax sama weight sudah ada
    	$raw_taxes = array_map(function($product){
    		return $product['tax'];
    	}, $raw_products);
    	$filter_taxes = array_unique($raw_taxes);
    	$this->db->select('tax_class_id, title');
    	$this->db->from('tax_class');
    	$this->db->where_in('title', $filter_taxes);
    	$tax_classes = $this->db->get()->result_array();

    	$raw_weights = array_map(function($product){
    		return $product['weight'];
    	}, $raw_products);
    	$filter_weights = array_unique($raw_weights);
    	$this->db->select('weight_class_id, title');
    	$this->db->from('weight_class_description');
    	$this->db->where_in('title', $filter_weights);
    	$this->db->where('language_id', $language_id);
    	$weight_classes = $this->db->get()->result_array();

    	//query id taxes
    	$added_products = [];
    	foreach ($new_products as $item) {
            $tax_class_key = array_search($item['tax'], array_column($tax_classes, 'title'));
            $tax_class = NULL;
            if ($tax_class_key !== FALSE && array_key_exists($tax_class_key, $tax_classes)) {
                $tax_class = $tax_classes[$tax_class_key];
            }
    		
            $weight_class_key = array_search($item['weight'], array_column($weight_classes, 'title'));
            $weight_class = NULL;
            if ($weight_class_key !== FALSE && array_key_exists($weight_class_key, $weight_classes)) {
                $weight_class = $weight_classes[$weight_class_key];
            } 
    		
	        $this->db->insert('product', [
	            'klu' => remove_whitespace($item['klu']),
	            'model' => remove_whitespace($item['model']), 
	            'quantity' => (int) $item['quantity'], 
	            'minimum' => isset($item['minimum']) ? $item['minimum'] : 1, 
	            'maximum' => isset($item['maximum']) ? $item['maximum'] : 5, 
	            'minimum_order' => isset($item['minimum_order']) ? $item['minimum_order'] : 1, 
	            'maximum_order' => isset($item['maximum_order']) ? $item['maximum_order'] : 10, 
	            'moving_product_status' => isset($item['moving_product_status']) ? $item['moving_product_status'] : 'normal',
	            'subtract' => 0, 
	            'stock_status_id' => 0, 
	            'shipping' => isset($item['shipping']) ? $item['shipping'] : 1, 
	            'price' => isset($item['price']) ? $item['price'] : 0, 
	            'points' => 0, 
	            'weight' => 0, 
	            'weight_class_id' => $weight_class != NULL ? $weight_class['weight_class_id'] : 0, 
	            'length' => 0, 
	            'width' => 0, 
	            'height' => 0, 
	            'length_class_id' => 0, 
	            'status' => 1, 
	            'tax_class_id' =>  $tax_class != NULL ? $tax_class['tax_class_id'] : 0, 
	            'sort_order' => 0, 
	            'date_added' => date('Y-m-d H:i:s'), 
	            'date_modified' => date('Y-m-d H:i:s')
	        ]);    
	        $product_id = $this->db->insert_id();
	        $this->db->insert('product_description', [
	            'product_id' => $product_id,
	            'language_id' => $language_id, 
	            'name' => $item['name'], 
	            'description' => $item['description'],
	            'meta_title' => $item['meta_title'], 
	            'meta_description' => $item['meta_description'], 
	            'meta_keyword' => $item['meta_keyword']
	        ]);

	        array_push($added_products, [
	        	'product_id' => $product_id,
                'sku' => $item['sku'],
                'sku_description' => $item['sku_description'],
                'model' => remove_whitespace($item['model']),
	        	'name' => $item['name'],
	        	'category' => $item['category'],
	        	'supplier' => $item['supplier'],
                'unit' => $item['unit'],
                'rasio' => $item['rasio'],
                'target_unit' => $item['target_unit'],
                'image' => $item['image']
	        ]);
    	}

    	return $added_products;
    }

    function insert_product_to_category($added_products, $added_categories)
    {
        if (!is_array($added_products) || !is_array($added_categories)) {
            throw new Exception("Product or Category must be array", 1);
        }

        if (count($added_products) == 0) {
            return FALSE;
        }

    	$product_to_categories = [];
    	foreach ($added_products as $product) {
            $cat_key = array_search($product['category'], array_column($added_categories, 'name'));
            $category = NULL;
            if ($cat_key !== FALSE && array_key_exists($cat_key, $added_categories)) {
                $category = $added_categories[$cat_key];
                array_push($product_to_categories, [
                    'product_id' => $product['product_id'], 
                    'category_id' => $category['category_id']
                ]);    
            }
    	}

        if (count($product_to_categories) > 0) {
            $this->db->insert_batch('product_to_category', $product_to_categories);
        }
    }

    function insert_supplier_to_product($added_products, $added_suppliers)
    {
        if (!is_array($added_products) || !is_array($added_suppliers)) {
            throw new Exception("Products or Suppliers must be array", 1);
        }

        if (count($added_products) == 0) {
            return FALSE;
        }

    	$supplier_to_products = [];
    	foreach ($added_products as $product) {
            $supplier_key = array_search($product['supplier'], array_column($added_suppliers, 'name'));
            if ($supplier_key !== FALSE && array_key_exists($supplier_key, $added_suppliers)) {
                $supplier = $added_suppliers[$supplier_key];
                array_push($supplier_to_products, [
                    'supplier_id' => (int) $supplier['supplier_id'],
                    'product_id' => (int) $product['product_id']
                ]);    
            }
    	}

        if (count($supplier_to_products) > 0) {
            $this->db->insert_batch('supplier_to_product', $supplier_to_products);
        }
    	
    }

    function insert_unit_measurement($raw_unit_measurements)
    {
        $this->load->model('Unitmeasurement_model', 'unit_measurement');
        $candidate_measurements = array_unique($raw_unit_measurements);
        $unit_measurements = $this->unit_measurement->find_by_names($candidate_measurements);
        $new_unit_measurements = array_filter($candidate_measurements, function($candidate_measurement) use ($unit_measurements) {
            $unit_measurement_key = array_search($candidate_measurement, array_column($unit_measurements, 'name'));
            if (gettype($unit_measurement_key) == 'integer') {
                return FALSE;
            }

            return TRUE;
        });        

        if (count($new_unit_measurements) > 0) {
            $this->db->insert_batch('unit_measurement', array_map(function($new_unit_measurment){
                return [
                    'name' => $new_unit_measurment,
                    'symbol' => $new_unit_measurment,
                    'status' => 1
                ];
            }, $new_unit_measurements));            
        } 

        return $this->unit_measurement->find_by_names($candidate_measurements);
    }

    function insert_product_inventory($product_invbalances)
    {
        if (!is_array($product_invbalances)) {
            return FALSE;
        }

        if (count($product_invbalances) == 0) {
            return FALSE;
        }
        //check should have one inventory balance
        array_walk($product_invbalances, function($prod_invbal){
            if (!array_key_exists('inventory_balance_id', $prod_invbal)) {
                show_error('Missing inventory balance id', 400, 'Bad Request');
            }
        });

        $this->load->model('Unitmeasurement_model', 'unit_measurement');
        $unit_names = array_map(function($product){
            return $product['unit'];
        }, $product_invbalances);
        log_message('debug', "Attempting to find unit measurements with names: ". implode(",", $unit_names));  
        $unit_measurements = $this->unit_measurement->find_by_names($unit_names);
        $total_unit_measurement = count($unit_measurements);
        log_message('debug', "Found {$total_unit_measurement} items");  
        $product_inventories = array_map(function($product) use ($unit_measurements) {
            $product_inventory = [
                'product_id' => $product['product_id'],
                'inventory_balance_id' => $product['inventory_balance_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 0,
                'status' => 1
            ];

            $unit_key = array_search($product['unit'], array_column($unit_measurements, 'name'));
            if (gettype($unit_key) == 'integer') {
                $unit_measurement = $unit_measurements[$unit_key];

                log_message('debug', "Set measurement to product id: ". $product['product_id']);  
                $product_inventory['unit_measurement_id'] = $unit_measurement['unit_measurement_id'];
            } else {
                log_message('debug', "Unit measurement for product ". $product['product_id'] . " not found");  
            }

            return $product_inventory;
        }, $product_invbalances);
        //ignore kalo belum punya unit id
        $total_product_inventory = count($product_inventories);
        log_message('debug', "Attempting to create {$total_unit_measurement} item new product inventory");  
        $product_inventories = array_filter($product_inventories, function($product_inventory){
            return array_key_exists('unit_measurement_id', $product_inventory);
        });

        if (count($product_inventories) == 0) {
            log_message('error', "No product inventory to be insert");  
            return FALSE;  
        }

        $this->db->insert_batch('product_inventory', $product_inventories);

        //insert product converter if empty converter
        $unit_convertions = array_map(function($prod_inv) use ($product_invbalances) {
            $prod_invbal_key = array_search($prod_inv['product_id'], array_column($product_invbalances, 'product_id'));
            if (gettype($prod_invbal_key) == 'integer') {
                $product_invbalance = $product_invbalances[$prod_invbal_key];

                log_message('info', "Set measurement converter");  
                $unit_convertion = [
                    'base_unit_measurement_id' => $prod_inv['unit_measurement_id'], 
                    'to_unit_measurement_id' => $product_invbalance['qty_unit_id'],
                    'status' => 1,
                    'multiply_rate' => $product_invbalance['rasio']
                ];       

                return $unit_convertion;         
            }   

            return FALSE;         
        }, $product_inventories);
        $total_unit_convertion = count($unit_convertions);
        log_message('debug', "Attempting to create {$total_unit_convertion} item unit convertions");  
        $this->db->insert_batch('unit_measurement_convertion', $unit_convertions);        
    }

    function insert_inventory_balance($added_products)
    {
        $added_products = array_filter($added_products, function($product){
            return $product['sku'] != NULL && $product['sku_description'] != NULL;
        });

        $total_products = count($added_products);
        log_message('info', "Attempting to create new inventory balance for {$total_products} new products");
        if (count($added_products) == 0) {
            log_message('info', "{$total_products} new products, no inserting new inventory balance");
            return FALSE;
        }

        $this->load->model('Unitmeasurement_model', 'unit_measurement');
        $unit_names = array_map(function($product){
            return $product['target_unit'];
        }, $added_products);
        $unit_measurements = $this->unit_measurement->find_by_names($unit_names); 
        //check for not duplicate 
        $new_invbalances = array_map(function($product) use ($unit_measurements) {
            $invbalance = [
                'sku' => $product['sku'],
                'description' => $product['sku_description'],
                'product_type' => 'simple',
                'storagebin1' => 0,
                'storagebin2' => 0,
                'qty' => 0,
                'qty_unit_id' => 0,
                'status' => 1,
            ];

            $unit_key = array_search($product['target_unit'], array_column($unit_measurements, 'name'));
            if (gettype($unit_key) == 'integer') {
                $unit_measurement = $unit_measurements[$unit_key];
                $invbalance['qty_unit_id'] = $unit_measurement['unit_measurement_id'];
            }

            return $invbalance;
        }, $added_products);

        //check kalo sku uda ada, ignore
        /**
        $this->load->model('Invbalance_model', 'invbalance_model');
        $total_new_invbalance = count($new_invbalances);
        if (!empty($new_invbalances)) {
            log_message('info', "Attempting to insert {$total_new_invbalance} items new inventory balances");
            $new_invbalances = array_unique($new_invbalances, SORT_REGULAR);
            array_walk($new_invbalances, function(&$new_invbalance){
                $inventory_balance['created_by'] = 0;
                $inventory_balance['created_at'] = date('Y-m-d H:i:s');
                $inventory_balance['updated_at'] = date('Y-m-d H:i:s');                
            });
            $this->db->insert_batch('inventory_balance', $new_invbalances);
        }
        
        $skus = array_map(function($product){
            return $product['sku'];
        }, $added_products);
        log_message('info', 'Get inventory balance by sku');
        //log_message('debug', $skus);
        $invbalances = $this->invbalance_model->find_by_skus($skus); **/
        $this->load->library('Invbal_bulk', 'invbal_bulk');
        $invbalances = $this->invbal_bulk->insert($new_invbalances);
        $product_invbalances = [];
        foreach ($added_products as $product) {
            $product_invbalance = [
                'product_id' => $product['product_id'],
                'unit' => $product['unit'],
                'rasio' => $product['rasio'],
            ];

            $product_key = array_search($product['sku'], array_column($invbalances, 'sku'));
            if (gettype($product_key) == 'integer') {
                $invbalance = $invbalances[$product_key];

                $product_invbalance['inventory_balance_id'] = $invbalance['inventory_balance_id'];
                $product_invbalance['qty_unit_id'] = $invbalance['qty_unit_id'];
            } else {
                log_message('error', "Product with id {$product['product_id']} doesnt have inventory id");    
            }
            array_push($product_invbalances, $product_invbalance);
        }

        return $product_invbalances;
    }

    function insert_product_images($added_products)
    {
        if (!is_array($added_products)) {
            log_message('error', "Invalid paramter products");    
            return FALSE;
        }

        $product_with_images = array_filter($added_products, function($product){
            return array_key_exists('image', $product) && $product['image'] != NULL;
        });

        $product_images = array_map(function($product){
            return [
                'product_id' => $product['product_id'],
                'image' => 'catalog/'. $product['image']
            ];
        }, $product_with_images);
        if (count($product_images) > 0) {
            $total_product_image = count($product_images);
            log_message('info', "attempting to insert {$total_product_image} items product image");    
            $this->db->insert_batch('product_image', $product_images);
        }
        
    }

    function insert($products = [])
    {
    	$this->db->trans_start();
    	$language_id = $this->get_language_id();
    	//insert into categories and the descriptions
    	$raw_cats = array_map(function($product){
    		return $product['category'];
    	}, $products);
    	$added_categories = $this->insert_categories($language_id, $raw_cats);

    	//insert into suppliers
    	$raw_suppliers = array_map(function($product) {
    		return $product['supplier'];
    	}, $products);
    	$added_suppliers = $this->insert_suppliers($raw_suppliers);
        //echo $this->db->query_errors();
    	//insert into product
    	$raw_products = array_map(function($product){
    		return $product;
    	}, $products); 
    	$added_products = $this->insert_products($language_id, $raw_products);
        if ($added_products) {
            //insert image product
            $this->insert_product_images($added_products);
            //insert into product to category
            $this->insert_product_to_category($added_products, $added_categories);
            //insert into supplier to product
            $this->insert_supplier_to_product($added_products, $added_suppliers);

            //insert into inventory balance
            //inventory balance gak usah di input disini, karena ketika tambah stock sudah ditangani
            //$product_invbalances = $this->insert_inventory_balance($added_products);

            $total_products = count($added_products);
            log_message('info', "Success creating {$total_products} items new products ");            
        }

    	$this->db->trans_complete();

        return $added_products;
    }

}
