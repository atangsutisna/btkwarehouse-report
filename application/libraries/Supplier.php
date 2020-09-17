<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier 
{
    protected $CI;
    private $supplier_id;
    private $language_id;
    private $commerce_config;

    private $sort_data = array(
        'name' => 'product_description.name',
        'price' => 'product.price',
        'date_modified' => 'product.date_modified',
    );

    private $moving_product_statuses = [
        'fast','slow','normal','bad'
    ];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
        if (!array_key_exists('supplier_id', $params)) {
            throw new Exception("Supplier ID is required");
        }

        if (!array_key_exists('language_id', $params)) {
            throw new Exception("Language ID is required");
        }
        $this->supplier_id = $params['supplier_id'];
        $this->language_id = $params['language_id'];
        
        $this->CI->load->library('image_manager');
		$this->CI->load->config('btkcommerce');
        $this->commerce_config = $this->CI->config->item('commerce');
    }
    
    protected function _get_db()
    {
        return $this->CI->db;
    }

    public function get_products($criterion = [], $first = 0, $count = 25, 
        $sort = 'product.date_modified', $order = 'DESC')
    {
        $this->_get_db()->select('product.product_id,
                        product.model,
                        product.sku,
                        product.upc,
                        product.ean,
                        product.jan,
                        product.isbn,
                        product.mpn,
                        product.image,
                        product_description.name,
                        product.price,
                        product.minimum,
                        product.maximum,
                        product.moving_product_status,
                        inventory_balance.qty,
                        product.qty_unit_id,
                        unit_measurement.symbol AS qty_unit,
                        product.date_modified');
        $this->_get_db()->from('product');
        $this->_get_db()->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->_get_db()->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->_get_db()->join('inventory_balance', 'product.product_id = inventory_balance.product_id', 'left');
        $this->_get_db()->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id');
        $this->_get_db()->where('product_description.language_id', $this->language_id);
        $this->_get_db()->where('product.status', 1);
        $this->_get_db()->where('supplier_to_product.supplier_id', $this->supplier_id);

        if (array_key_exists('product_type', $criterion)) {
            $product_type = $criterion['product_type'];
            if (in_array($product_type, $this->moving_product_statuses)) {
                $this->_get_db()->or_where('moving_product_status', $criterion['product_type']);
            }
        }

        if (array_key_exists('out_of_stock', $criterion) 
            || array_key_exists('stock_minus', $criterion)
            || array_key_exists('available_stock', $criterion)
            || array_key_exists('under_stock_minimum', $criterion)) {
            $this->_get_db()->group_start();

            if (array_key_exists('out_of_stock', $criterion) && $criterion['out_of_stock'] === true) {
                $this->_get_db()->or_where('inventory_balance.qty', 0);
                $this->_get_db()->or_where('inventory_balance.qty', NULL);
            }
    
            if (array_key_exists('stock_minus', $criterion) && $criterion['stock_minus'] === true) {
                $this->_get_db()->or_where('inventory_balance.qty <', 0);
            }
    
            if (array_key_exists('available_stock', $criterion) && $criterion['available_stock'] === true) {
                $this->_get_db()->or_where('inventory_balance.qty >', 0);
            }

            if (array_key_exists('under_stock_minimum', $criterion) && $criterion['under_stock_minimum'] === true) {
                $this->_get_db()->or_where('inventory_balance.qty < product.minimum');
            }
    
            $this->_get_db()->group_end();
        }
        
        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->_get_db()->group_start();
            $this->_get_db()->like('product.model', $criterion['term']);
            $this->_get_db()->or_like('product_description.name', $criterion['term']);
            $this->_get_db()->group_end();
        }

        if (array_key_exists('product_ids', $criterion)) {
            $this->_get_db()->where_in('product.product_id', $criterion['product_ids']);
        }

        $this->_get_db()->limit(
            isset($count) && $count > 0 ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->_get_db()->order_by(array_key_exists($sort, $this->sort_data) ? $this->sort_data[$sort] : 'product.date_modified', $order);
        $query_product = $this->_get_db()->get();

        $supplier_products = $query_product->result();
        array_walk($supplier_products, function(&$product) {
			if ($product->image != null && file_exists($this->commerce_config['base_image_path'] .'/'. $product->image)) {
				$product->image = $this->CI->image_manager->resize($product->image, 40, 40);
			} else if ($product->image != null && !file_exists($this->commerce_config['base_image_path'] .'/'. $product->image)){
				$product->image = $this->CI->image_manager->resize('small-logo.png', 40, 40);
            } else if ($product->image == null) {
                $product->image = $this->CI->image_manager->resize('small-logo.png', 40, 40);
            }
        });

        return $supplier_products;
    }


    public function count_products($criterion = array())
    {
        $this->_get_db()->select('COUNT(DISTINCT {PRE}product.product_id) AS total_rows');
        $this->_get_db()->from('product');
        $this->_get_db()->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->_get_db()->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id');
        $this->_get_db()->where('product_description.language_id', $this->language_id);
        $this->_get_db()->where('product.status', 1);
        $this->_get_db()->where('supplier_to_product.supplier_id', $this->supplier_id);
        
        if (array_key_exists('qty', $criterion)) {
            $this->_get_db()->where('qty', $criterion['qty']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->_get_db()->group_start();
            $this->_get_db()->like('product.model', $criterion['term']);
            $this->_get_db()->or_like('product_description.name', $criterion['term']);
            $this->_get_db()->group_end();
        }

        $query_count = $this->_get_db()->get();
        if ($query_count->row() == NULL) {
            return 0;
        }

        return $query_count->row()->total_rows;
    }

    public function get_profile()
    {
        return $this->_get_db()->get_where('supplier', [
            'supplier_id' => $this->supplier_id
        ])->row();
    }

    /**
     * @param $product_id int
     */
    public function add_product($product_id)
    {
        $product = $this->_get_db()->get_where('supplier_to_product',[
            'product_id' => $product_id,
            'supplier_id' => $this->supplier_id
        ])->row_array();
        if ($product == NULL) {
            $this->_get_db()->insert('supplier_to_product',[
                'product_id' => $product_id,
                'supplier_id' => $this->supplier_id
            ]);
        }
    }

}