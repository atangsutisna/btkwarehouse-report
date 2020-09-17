<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_inventory_model extends MY_Model 
{
    const TBL_REFERENCE = 'product_inventory';
    const PRIMARY_KEY = 'product_inventory_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_by_product_id($product_id) 
    {
        $this->db->select('product_inventory.*, unit_measurement.name, unit_measurement.symbol');
        $this->db->from('product_inventory');
        $this->db->join('unit_measurement', 'product_inventory.unit_measurement_id = unit_measurement.unit_measurement_id');
        $this->db->where('product_id', $product_id);
        $query = $this->db->get();

        return $query->row(); 
    }

    public function find_by_product_ids($product_ids)
    {
        $this->db->where_in('product_id', $product_ids);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result_array();
    }
}