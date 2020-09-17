<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends MY_Model
{
    const TBL_REFERENCE = 'supplier';
    const PRIMARY_KEY = 'supplier_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    function find_one($supplier_id)
    {
        $query = $this->db->get_where(self::TBL_REFERENCE, [
            'supplier_id' => $supplier_id
        ]);

        return $query->row();
    }

    function find_all($term, $first = 0, $count = 25, $order = 'name', $direction = 'asc')
    {
        if ($term != NULL && $term !== '') {
            $this->db->like('name', $term);
        }

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
            $this->db->like('name', $term);
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function insert($supplier)
    {
        $this->db->insert(self::TBL_REFERENCE, $supplier);
        return $this->db->insert_id();
    }

    public function update($supplier_id, $supplier)
    {
        $this->db->where('supplier_id', $supplier_id);
        $this->db->update(self::TBL_REFERENCE, $supplier);
    }

    public function find_by_names($names)
    {
        if (!is_array($names)) {
            throw new Exception("Names must be an array", 1);
        }

        $this->db->select('supplier_id, name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->where_in('name', $names);
        $query = $this->db->get();

        return $query->result_array();
    }

}