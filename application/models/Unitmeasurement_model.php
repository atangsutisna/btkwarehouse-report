<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unitmeasurement_model extends MY_Model 
{
    const TBL_REFERENCE = 'unit_measurement';
    const PRIMARY_KEY = 'unit_measurement_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($term = NULL, $first = 0, $count = 20, $order = 'name', $direction = 'desc')
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->group_end();
        }

        $this->db->where("status",1);
        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result();
    }

    public function count_all($term = NULL)
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->group_end();
        }

        $this->db->from($this->table_name);
        $this->db->where("status", 1);

        return $this->db->count_all_results();
    }

    public function find_by_target_id($target_measurement_id)
    {
        $this->db->select('uom_conv.unit_convertion_id, uom_conv.base_unit_measurement_id, uom1.name AS base_uom, uom1.symbol AS base_uom_symbol, 
            uom_conv.to_unit_measurement_id, uom2.name AS to_uom, uom2.symbol to_uom_symbol, 
            uom_conv.multiply_rate, uom_conv.divide_rate');
        $this->db->from('unit_measurement_convertion AS uom_conv');
        $this->db->join('unit_measurement AS uom1', 'uom1.unit_measurement_id = uom_conv.base_unit_measurement_id');
        $this->db->join('unit_measurement AS uom2', 'uom2.unit_measurement_id = uom_conv.to_unit_measurement_id');
        $this->db->where('uom_conv.to_unit_measurement_id', $target_measurement_id);
        $query_one = $this->db->get();
        
        return $query_one->result();        
    }

    public function find_by_names($names)
    {
        $this->db->select('unit_measurement_id, name');
        $this->db->where_in('name', $names);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result_array();
    }

    public function disable($id)
    {
        $this->db->where('unit_measurement_id', $id);
        $this->db->update('unit_measurement', [
            'status' => 0
        ]);
    }

}