<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unitconvertion_model extends MY_Model 
{
    const TBL_REFERENCE = 'unit_measurement_convertion';
    const PRIMARY_KEY = 'unit_convertion_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($term = NULL, $first = 0, $count = 20, $order = 'name', $direction = 'desc')
    {
        $this->db->select('uom_conv.unit_convertion_id, uom_conv.base_unit_measurement_id, uom1.name AS base_uom, 
            uom_conv.to_unit_measurement_id, uom2.name AS to_uom, uom_conv.multiply_rate, uom_conv.divide_rate');
        $this->db->from('unit_measurement_convertion AS uom_conv');
        $this->db->join('unit_measurement AS uom1', 'uom1.unit_measurement_id = uom_conv.base_unit_measurement_id');
        $this->db->join('unit_measurement AS uom2', 'uom2.unit_measurement_id = uom_conv.to_unit_measurement_id');
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('uom1.name', $term);
            $this->db->or_like('uom2.name', $term);
            $this->db->group_end();
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get();

        return $query->result();
    }

    public function count_all($term = NULL)
    {
        $this->db->join('unit_measurement AS uom1', 'uom1.unit_measurement_id = unit_measurement_convertion.base_unit_measurement_id');
        $this->db->join('unit_measurement AS uom2', 'uom2.unit_measurement_id = unit_measurement_convertion.to_unit_measurement_id');
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('uom1.name', $term);
            $this->db->or_like('uom2.name', $term);
            $this->db->group_end();
        }

        $this->db->from('unit_measurement_convertion');
        return $this->db->count_all_results();
    } 
    
    public function find_by_id($id)
    {
        $this->db->select('uom_conv.unit_convertion_id, uom_conv.base_unit_measurement_id, uom1.name AS base_uom, 
            uom_conv.to_unit_measurement_id, uom2.name AS to_uom, uom_conv.multiply_rate, uom_conv.divide_rate');
        $this->db->from('unit_measurement_convertion AS uom_conv');
        $this->db->join('unit_measurement AS uom1', 'uom1.unit_measurement_id = uom_conv.base_unit_measurement_id');
        $this->db->join('unit_measurement AS uom2', 'uom2.unit_measurement_id = uom_conv.to_unit_measurement_id');
        $this->db->where('uom_conv.unit_convertion_id', $id);
        $query_one = $this->db->get();
        
        return $query_one->row();
    }

    public function find_by_base_unit_measurement_id($id)
    {
        $this->db->select('uom_conv.unit_convertion_id, uom_conv.base_unit_measurement_id, uom1.name AS base_uom, 
            uom_conv.to_unit_measurement_id, uom2.name AS to_uom, uom_conv.multiply_rate, uom_conv.divide_rate');
        $this->db->from('unit_measurement_convertion AS uom_conv');
        $this->db->join('unit_measurement AS uom1', 'uom1.unit_measurement_id = uom_conv.base_unit_measurement_id');
        $this->db->join('unit_measurement AS uom2', 'uom2.unit_measurement_id = uom_conv.to_unit_measurement_id');
        $this->db->where('uom_conv.base_unit_measurement_id', $id);
        $query_one = $this->db->get();
        
        return $query_one->row();
    }

    public function find_rasio($base_unit_measurement_id)
    {
        $this->db->select('uom_conv.unit_convertion_id, uom_conv.base_unit_measurement_id, uom1.name AS base_uom, 
            uom_conv.to_unit_measurement_id, uom2.name AS to_uom, uom_conv.multiply_rate, uom_conv.divide_rate');
        $this->db->from('unit_measurement_convertion AS uom_conv');
        $this->db->join('unit_measurement AS uom1', 'uom1.unit_measurement_id = uom_conv.base_unit_measurement_id');
        $this->db->join('unit_measurement AS uom2', 'uom2.unit_measurement_id = uom_conv.to_unit_measurement_id');
        $this->db->where('uom_conv.base_unit_measurement_id', $base_unit_measurement_id);
        $query_one = $this->db->get();
        
        return $query_one->result();
    }

}