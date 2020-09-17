<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends CI_Model 
{
    const TBL_REFERENCE = 'permissions';
    protected $db;

    function __construct()
    {
        $group_name = $this->config->item('database_group_name', 'ion_auth');
        $this->db = $this->load->database($group_name, TRUE, TRUE);
    }

    public function find_all($term = NULL, $first = 0, $count = 20, 
        $order = 'ID', $direction = 'ASC')
    {
        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->where('Title !=', 'root');
        $order = $order == 'id' ? 'ID' : 'ID';
        $this->db->order_by($order, $direction);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result();
    }
}