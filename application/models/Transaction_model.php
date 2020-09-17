<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends MY_Model 
{
    const TBL_REFERENCE = 'tbl_transaksi';
    const PRIMARY_KEY = 'id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }


    public function find_all($term = NULL, $first = 0, $count = 20, $order = 'modification_time', $direction = 'DESC')
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('judul', $term);
            $this->db->or_like('nim', $term); 
            $this->db->or_like('nama', $term); 
            $this->db->group_end();
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function count_all($term = NULL)
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('judul', $term);
            $this->db->or_like('nim', $term); 
            $this->db->or_like('nama', $term); 
            $this->db->group_end();
        }

        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }
    
}