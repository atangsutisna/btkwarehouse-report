<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_manager
{
    protected $CI;
    protected $store_id = 0;

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
        $this->CI->load->library([
            'product_variant_manager',
            'category_manager'
        ]);
    }

    /**
     * @return dbObject
     */
    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @param $product
     * @return $product_id string
     */
    public function create($product_description)
    {
        //insert into master product
        $this->_get_db()->insert('product', $product_description['product']);
        $product_id = $this->_get_db()->insert_id();
        
        //insert into product description
        $this->_get_db()->insert('product_description', [
            'product_id' => $product_id,
            'language_id' => $product_description['description']['language_id'], 
            'name' => $product_description['description']['name'], 
            'description' => $product_description['description']['description'],
            'meta_title' => $product_description['description']['meta_title'], 
            'meta_description' => $product_description['description']['meta_description'], 
            'meta_keyword' => $product_description['description']['meta_keyword']
        ]);

        //insert into product variant
        if (array_key_exists('product_variant', $product_description)
            && $product_description['product_variant'] !== NULL) {
            $product_variant = $product_description['product_variant'];
            $product_variant['product_id'] = $product_id;
            $this->CI->product_variant_manager->create($product_variant);            
        }
        
        //insert into supplier to product
        if (array_key_exists('supplier_to_product', $product_description)
            && $product_description['supplier_to_product'] !== NULL) {
            $supplier_id = $product_description['supplier_to_product']['supplier_id'];
            $this->product_to_supplier($product_id, $supplier_id);
        }
        
        //insert into product to category
        if (array_key_exists('product_to_category', $product_description)
            && $product_description['product_to_category'] !== NULL) {
            $category_id = $product_description['product_to_category']['category_id'];
            $product_category = [
                'category_id' => $category_id,
                'product_id' => $product_id
            ];
            $this->CI->category_manager->add_product($product_category);
        }

        $this->product_to_store($product_id);
        $this->product_image($product_id);

        return $product_id;
    }

    /**
     * @param $product_description
     * @return void
     */
    public function create_or_modify($product_description)
    {
        $this->_get_db()->trans_start();

        $product = $this->get_product($product_description['product']['model']);
        if ($product != NULL) {
            log_message("info", "Attempting to modify product with id: ". $product['product_id']);
            $this->modify($product['product_id'], $product_description);
        } else {
            log_message("info", "Attempting to create a new product with name ". $product_description['description']['name']);
            $this->create($product_description);
        }

        $this->_get_db()->trans_complete();
    }
    
    /**
     * @param $product_code string
     * @return $product
     */
    public function get_product($product_code)
    {
        $this->_get_db()->where('model', $product_code);
        return $this->_get_db()->get('product')->row_array();
    }

    /**
     * @param $product_id int
     * @param $product_description 
     * @return void
     */
    public function modify($product_id, $product_description)
    {
        /** update product */
        $this->_get_db()->where('product_id', $product_id);
        $this->_get_db()->update('product', $product_description['product']);

        $this->_get_db()->where('product_id', $product_id);
        $this->_get_db()->update('product_description', [
            'language_id' => $product_description['description']['language_id'], 
            'name' => $product_description['description']['name'], 
            'description' => $product_description['description']['description'],
            'meta_title' => $product_description['description']['meta_title'], 
            'meta_description' => $product_description['description']['meta_description'], 
            'meta_keyword' => $product_description['description']['meta_keyword']
        ]);

        if (array_key_exists('product_variant', $product_description)
            && $product_description['product_variant'] !== NULL) {
            $product_variant = $product_description['product_variant'];
            $product_variant['product_id'] = $product_id;
            $this->CI->product_variant_manager->create_or_modify($product_variant);
        }

        if (array_key_exists('supplier_to_product', $product_description)
            && $product_description['supplier_to_product'] !== NULL) {
            $supplier_id = $product_description['supplier_to_product']['supplier_id'];
            $this->product_to_supplier($product_id, $supplier_id);
        }

        if (array_key_exists('product_to_category', $product_description)
            && $product_description['product_to_category'] !== NULL) {
            $category_id = $product_description['product_to_category']['category_id'];
            $product_category = [
                'category_id' => $category_id,
                'product_id' => $product_id
            ];
            $this->CI->category_manager->add_product($product_category);
        }

        $this->product_to_store($product_id);
        $this->product_image($product_id);
    }

    /**
     * @param $product['product'] = 'product'
     * @param $product['description'] = 'product_description'
     * @return $total_product int
     */
    public function copy_replace($product_descriptions)
    {
        $total_product = 0;
        foreach ($product_descriptions as $product_description) {
            $this->create_or_modify($product_description);
            $total_product++;
        }

        return $total_product;
    }

    /**
     * @param $product_id
     * @param $supplier_id
     * @return void
     */
    public function product_to_supplier($product_id, $supplier_id)
    {
        $product = $this->_get_db()->get_where('supplier_to_product',[
            'product_id' => $product_id,
            'supplier_id' => $supplier_id
        ])->row_array();

        if ($product == NULL) {
            $this->_get_db()->insert('supplier_to_product',[
                'product_id' => $product_id,
                'supplier_id' => $supplier_id
            ]);
        }
    }

    public function product_to_store($product_id)
    {
        $product_store = $this->_get_db()->get_where('product_to_store', [
            'product_id' => $product_id
        ])->row_array();
        
        if ($product_store == NULL) {
            log_message("info", "Attempting to insert product ". $product_id . " to store id 0");
            $this->_get_db()->insert('product_to_store', [
                'product_id' => $product_id,
                'store_id' => 0
            ]);
        } 
    }

    public function product_image($product_id)
    {
        $this->_get_db()->select('image');
        $this->_get_db()->from('product');
        $this->_get_db()->where('product_id', $product_id);
        $product = $this->_get_db()->get()->row_array();

        if ($product !== NULL && $product['image'] !== NULL && $product['image'] !== '') {
            log_message("info", "Attempting to delete product_image with ID ". $product_id);
            $this->_get_db()->where('product_id', $product_id);
            $this->_get_db()->delete('product_image');

            log_message("info", "Attempting to reinsert image ". $product['image'] . " to product id with ID ". $product_id);
            $this->_get_db()->insert('product_image', [
                'product_id' => $product_id,
                'image' => $product['image']
            ]);
        }
    }
}