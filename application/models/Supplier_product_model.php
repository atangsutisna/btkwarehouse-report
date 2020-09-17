<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_product_model extends CI_Model
{
    public function find_all($supplier_id, 
        $criterion = array(),
        $first = 0, $count = 25, 
        $sort = 'product.date_modified', $order = 'DESC')
    {
        if (!isset($supplier_id)) {
            throw new Exception('Supplier must not null');
        }

        $language_id = (int) $this->get_language_id();
        $this->db->select('product.*, product_description.name, unit_measurement.symbol AS qty_unit');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where('product.status', 1);
        $this->db->where('supplier_to_product.supplier_id', $supplier_id);

        if (array_key_exists('quantity', $criterion)) {
            $this->db->where('quantity', $criterion['quantity']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term']);
            $this->db->group_end();
        }

        $this->db->group_by('product.product_id');
        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by(array_key_exists($sort, $this->sort_data) ? $this->sort_data[$sort] : 'product.date_modified', $order);
        $query_product = $this->db->get();

        return $query_product->result();
    }    
}