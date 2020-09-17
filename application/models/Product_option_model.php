<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_option_model extends MY_Model
{

    function __construct()
    {
        parent::__construct(NULL, NULL);
    }

    function find_by_ids($product_ids)
    {
        $language_id = (int) $this->get_language_id();
        $this->db->distinct('*');
        $this->db->from('product');
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where_in('product.product_id', $product_ids);
        $products = $this->db->get()->result();

        foreach ($products as $product) {
            if ($this->_is_product_varied($product->product_id)) {
                $product_options = $this->_find_product_options($product->product_id, $language_id);
                $product->product_options = $product_options;    
            }
        }

        return $products;
    }

    protected function _is_product_varied($product_id)
    {
        $this->db->select('1');
        $this->db->from('product_option');
        $this->db->where('product_id', $product_id);
        $result = $this->db->get()->row();

        return $result == NULL ? FALSE : TRUE;
    }

    protected function _find_product_options($product_id, $language_id = 0)
    {
        $product_options = $this->db->get_where('product_option', [
            'product_id' => $product_id
        ])->result();
        if ($product_options == NULL) {
            return [];
        }

        $product_option_ids = array_map(function($product_option){
            return $product_option->option_id;
        }, $product_options);
        $product_option_descriptions = $this->_get_option_descriptions($product_option_ids, $language_id);

        $results = [];
        foreach ($product_option_descriptions as $product_option_description) {
            $option_values = $this->_get_option_values($product_option_description->option_id, $language_id);
            foreach ($option_values as $option_value) {
                $product_option_value = $this->_get_product_option_value($product_id, $option_value['option_value_id']);
                if ($product_option_value != NULL) {
                    $product_option_description->values[] = $option_value;
                }
            }

            array_push($results, $product_option_description);
        }

        return $results;
    }

    protected function _get_option_descriptions($option_ids, $language_id = 0)
    {
        $this->db->join('option_description', 'option.option_id = option_description.option_id');
        $this->db->where('option_description.language_id', $language_id);
        $this->db->where_in('option_description.option_id', $option_ids);
        
        return $this->db->get('option')->result();
    }

    protected function _get_option_values($option_id, $language_id = 0)
    {
        $option_id = (int) $option_id;
        $option_value_data = array();

        $option_value_query = $this->db->query("SELECT * FROM {PRE}option_value 
        WHERE option_id = '" . $option_id . "' ORDER BY sort_order");

        foreach ($option_value_query->result_array() as $option_value) {
            $option_value_description_data = array();

            $option_value_description_query = $this->db->query("SELECT * FROM {PRE}option_value_description 
            WHERE option_value_id = '" . (int) $option_value['option_value_id'] . "'
            AND language_id = '". $language_id ."'");

            $option_value_data[] = array(
                'option_value_id'          => $option_value['option_value_id'],
                'option_value_description' => $option_value_description_query->row_array()['name'],
                'image'                    => $option_value['image'],
                'sort_order'               => $option_value['sort_order']
            );
        }

        return $option_value_data;        
    }

    protected function _get_product_option_value($product_id, $option_value_id)
    {
        return $this->db->get_where('product_option_value', [
            'option_value_id' => $option_value_id,
            'product_id' => $product_id
        ])->row();
    }

    function split_product_by_options($product)
    {

    }

    function collect_product_with_option()
    {

    }
}