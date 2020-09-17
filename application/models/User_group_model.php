<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group_model extends MY_Model 
{
    const TBL_REFERENCE = 'groups';
    const PRIMARY_KEY = 'id';

    protected $db;

    function __construct()
    {
        $group_name = $this->config->item('database_group_name', 'ion_auth');
        $this->db = $this->load->database($group_name, TRUE, TRUE);
    }

    public function find_all($term = NULL, $first = 0, $count = 20, $order = 'id', $direction = 'DESC')
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->group_end();
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
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->group_end();
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }


}