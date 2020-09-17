<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_role_permission_model extends CI_Model 
{
    const TBL_REFERENCE = 'rolepermissions';
    protected $db;

    function __construct()
    {
        $group_name = $this->config->item('database_group_name', 'ion_auth');
        $this->db = $this->load->database($group_name, TRUE, TRUE);
    }

    public function find_by_role_id($user_role_id, $order = 'RoleID', $direction = 'ASC')
    {
        $this->db->select('rolepermissions.*, permissions.Title, permissions.Description');
        $this->db->join('permissions', 'rolepermissions.PermissionID = permissions.ID');
        $this->db->from('rolepermissions');
        $this->db->where('RoleID', $user_role_id);
        $this->db->order_by($order, $direction);
        $query = $this->db->get();

        return $query->result();
    }

    public function is_exists($user_role_id, $permission_id)
    {
        $query = $this->db->get_where(self::TBL_REFERENCE, [
            'RoleID' => $user_role_id,
            'PermissionID' => $permission_id
        ]);

        return $query->row() != NULL ? TRUE : FALSE;
    }

    public function insert($data)
    {
        $this->db->insert(self::TBL_REFERENCE, $data);
    }

    public function remove($role_id, $permission_id)
    {
        $this->db->where('RoleID', $role_id);
        $this->db->where('PermissionID', $role_id);
        $this->db->delete(self::TBL_REFERENCE);
    }

    
}