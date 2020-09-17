<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sequence_model extends CI_Model 
{
    private $table_name = '_sequence';

    public function find_all()
    {
        $query = $this->db->query('SELECT * FROM _sequence');
        return $query->result();
    }

    public function count_all()
    {
        $query = $this->db->query('SELECT COUNT(*) AS total_rows FROM _sequence');
        if ($query->row() == NULL) {
            return 0;
        }

        return $query->row()->total_rows;
    }

    public function find_one($seq_name)
    {
        $query = $this->db->query("SELECT * FROM _sequence WHERE seq_name = ?", [$seq_name]);
        return $query->row();
    }

    public function get_next() 
    {
        $result = $this->db->query("SELECT GET_NEXT_ID('order_no') AS next_id");
        return $result->row()->next_id;
    }

    public function get_next_goods_receipt() 
    {
        $result = $this->db->query("SELECT GET_NEXT_ID('goods_receipt_no') AS next_id");
        return $result->row()->next_id;
    }

    public function get_next_purchase_order() 
    {
        $result = $this->db->query("SELECT GET_NEXT_ID('purchase_order_no') AS next_id");
        return $result->row()->next_id;
    }

    public function update($seq_name, $seq_group)
    {
        $this->db->query("UPDATE _sequence SET seq_group = ? WHERE seq_name = ?", [$seq_group, $seq_name]);
        echo $this->db->last_query();
    }   

}