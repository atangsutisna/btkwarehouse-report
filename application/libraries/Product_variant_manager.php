<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_variant_manager
{
    protected $CI;

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
    }

    /**
     * @return dbObject
     */
    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @param $product_id string
     * @return $product_variant
     */
    protected function _get_variant($product_id)
    {
        $this->_get_db()->where('product_id', $product_id);
        return $this->_get_db()->get('product_variant')->row_array();
    }

    /**
     * @param $product_id string
     * @return boolean
     */
    protected function is_present($product_id) {
        $product_variant = $this->_get_variant($product_id);
        if ($product_variant === NULL) {
            return FALSE;
        }

        return TRUE;
    }

    public function create($product_variant)
    {
        if (!array_key_exists('product_id', $product_variant)) {
            throw new Exception("Product id is required");
        }

        $this->_get_db()->insert('product_variant',[
            'product_id' => $product_variant['product_id'],
            'model' => $product_variant['model'],
            'qty_unit_id' => $product_variant['qty_unit_id'],
            'qty_rasio' => $product_variant['qty_rasio'],
            'price' => $product_variant['price'],
            'price_2' => $product_variant['price_2'],
            'cost_of_goods_sold' => $product_variant['cost_of_goods_sold'],
            'date_modified' => $product_variant['date_modified']
        ]);
    }

    public function modify($product_variant)
    {
        $this->_get_db()->where('product_id', $product_variant['product_id']);
        $this->_get_db()->update('product_variant',[
            'model' => $product_variant['model'],
            'qty_unit_id' => $product_variant['qty_unit_id'],
            'qty_rasio' => $product_variant['qty_rasio'],
            'price' => $product_variant['price'],
            'price_2' => $product_variant['price_2'],
            'cost_of_goods_sold' => $product_variant['cost_of_goods_sold'],
            'date_modified' => $product_variant['date_modified']
        ]);
    }

    public function create_or_modify($product_variant)
    {
        if (array_key_exists('product_id', $product_variant)
            && $product_variant['product_id'] !== ''
            && $this->is_present($product_variant['product_id'])) {
            $this->modify($product_variant);
        } else {
            $this->create($product_variant);
        }
    }

}