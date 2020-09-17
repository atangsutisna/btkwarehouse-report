<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_product_model extends CI_Model
{
    private $language_id;
    protected $store_id = 0;

    public function __construct()
    {
        parent::__construct();  
        $this->language_id = $this->_get_default_language_id();
    }

    private function _get_default_language_id()
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


    public function find_all($criterion = array(), $first = 0, $count = 25, 
        $sort = 'product.date_modified', $order = 'DESC')
    {
        $this->db->select('product.product_id,
                        product.model,
                        product.image,
                        product_description.name,
                        product.price AS online_price,
                        product.price_2 AS offline_price,
                        product.cost_of_goods_sold,
                        product.minimum,
                        product.maximum,
                        product.moving_product_status,
                        product.qty_unit_id,
                        unit_measurement.symbol AS qty_unit,
                        product.status,
                        product.date_modified,
                        inventory_balance.qty AS qty_on_hand');
        $this->db->from('product');
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->join('inventory_balance', 'product.product_id = inventory_balance.product_id', 'left');

        if (array_key_exists('supplier_id', $criterion) && !empty($criterion['supplier_id']) && $criterion['supplier_id'] != '') {
            $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id', 'left');
            $this->db->where('supplier_to_product.supplier_id', $criterion['supplier_id']);
        }

        if (array_key_exists('zero_price', $criterion) && $criterion['zero_price'] == true) {
            $this->db->group_start();
            $this->db->where('price', '0.0000');
            $this->db->or_where('price_2', '0.0000');
            $this->db->group_end();
        }

        if (array_key_exists('product_ids', $criterion) && is_array($criterion['product_ids'])) {
            $this->db->where_in('product.product_id', $criterion['product_ids']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term']);
            $this->db->group_end();
        }

        $this->db->where('product_description.language_id', $this->language_id);
        $this->db->where('product.status', 1);

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($sort, $order);
        $query = $this->db->get();

        return $query->result();
    } 

    public function count_all($criterion = array())
    {
        $this->db->from('product');
        $this->db->join('product_description', 'product.product_id = product_description.product_id');

        if (array_key_exists('supplier_id', $criterion) && !empty($criterion['supplier_id']) && $criterion['supplier_id'] != '') {
            $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id', 'left');
            $this->db->where('supplier_to_product.supplier_id', $criterion['supplier_id']);
        }

        if (array_key_exists('zero_price', $criterion) && $criterion['zero_price'] == true) {
            $this->db->group_start();
            $this->db->where('price', '0.0000');
            $this->db->or_where('price_2', '0.0000');
            $this->db->group_end();
        }

        if (array_key_exists('product_ids', $criterion) && is_array($criterion['product_ids'])) {
            $this->db->where_in('product.product_id', $criterion['product_ids']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function get_variant($product_id)
    {
        $this->db->select('product_variant.product_variant_id, 
                        product_variant.model,
                        product_variant.qty_unit_id,
                        product_variant.qty_rasio,
                        product_variant.price AS online_price,
                        product_variant.price_2 AS offline_price,
                        unit_measurement.symbol AS qty_unit');
        $this->db->from('product_variant');
        $this->db->join('unit_measurement', 'product_variant.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where('product_id', $product_id);
        $query = $this->db->get();

        return $query->result();
    }

    public function find_by_model($model)
    {
        $this->db->select('product.product_id,
                        product.model,
                        product.image,
                        product_description.name,
                        product.price,
                        product.minimum,
                        product.maximum,
                        product.moving_product_status,
                        unit_measurement.symbol AS qty_unit,
                        product.status,
                        product.date_modified');
        $this->db->from('product');
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where('product_description.language_id', $this->language_id);
        $this->db->where('product.status', 1);
        $this->db->where('product.model', $model);
        $parent_product_query = $this->db->get()->row();
        if ($parent_product_query != NULL) {
            return $parent_product_query;
        }

        $this->db->select('product_variant.product_id,
                        product_variant.model,
                        product.image,
                        product_description.name,
                        product_variant.price,
                        product.minimum,
                        product.maximum,
                        product.moving_product_status,
                        unit_measurement.symbol AS qty_unit,
                        product.date_modified');
        $this->db->from('product_variant');
        $this->db->join('product_description', 'product_variant.product_id = product_description.product_id');
        $this->db->join('unit_measurement', 'product_variant.qty_unit_id = unit_measurement.unit_measurement_id');
        $this->db->join('product', 'product_variant.product_id = product.product_id');

        $this->db->where('product_description.language_id', $this->language_id);
        $this->db->where('product.status', 1);
        $this->db->where('product_variant.model', $model);
        $child_product_query = $this->db->get()->row();

        return $child_product_query;
    }

    public function find_one($product_id)
    {
        $this->db->select('product.product_id,
                        product.model,
                        product.image,
                        product_description.name,
                        product.price AS online_price,
                        product.price_2 AS offline_price,
                        product.minimum,
                        product.maximum,
                        product.moving_product_status,
                        unit_measurement.symbol AS qty_unit,
                        product.status,
                        product.date_modified');
        $this->db->from('product');
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where('product_description.language_id', $this->language_id);
        $this->db->where('product.status', 1);
        $this->db->where('product.product_id', $product_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function get_variants($product_ids)
    {
        $this->db->select('product_variant.product_variant_id, 
                        product_variant.product_id,
                        product_variant.model,
                        product_variant.qty_unit_id,
                        product_variant.qty_rasio,
                        product_variant.price AS online_price,
                        product_variant.price_2 AS offline_price,
                        unit_measurement.symbol AS qty_unit');
        $this->db->from('product_variant');
        $this->db->join('unit_measurement', 'product_variant.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where_in('product_id', $product_ids);
        $query = $this->db->get();

        return $query->result();
    }
   
}