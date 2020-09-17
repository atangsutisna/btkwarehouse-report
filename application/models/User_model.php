<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model 
{
    const TBL_REFERENCE = 'users';
    const PRIMARY_KEY = 'id';
    const GROUP_ADMIN = 1;

    protected $db;

    function __construct()
    {
        $group_name = $this->config->item('database_group_name', 'ion_auth');
        $this->db = $this->load->database($group_name, TRUE, TRUE);
    }

    public function find_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->row();
    }

    public function find_all($term = NULL, $first = 0, $count = 20, $order = 'id', $direction = 'DESC')
    {
        $this->db->select('users.*, groups.id AS group_id, groups.name AS group_name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('users_groups', 'users.id = users_groups.user_id');
        $this->db->join('groups', 'users_groups.group_id = groups.id');
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('username', $term);
            $this->db->or_like('users.first_name', $term); 
            $this->db->or_like('users.last_name', $term); 
            $this->db->group_end();
        }

        $this->db->where('groups.id !=', self::GROUP_ADMIN);
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
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('users.username', $term);
            $this->db->or_like('users.first_name', $term); 
            $this->db->or_like('users.last_name', $term); 
            $this->db->group_end();
        }
        $this->db->join('users_groups', 'users.id = users_groups.user_id');
        $this->db->join('groups', 'users_groups.group_id = groups.id');
        $this->db->where('groups.id !=', self::GROUP_ADMIN);
        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function modify($user_id, $user)
    {
        if ($user_id == NULL) {
            throw new  Exception("Error Processing Request");
        }
        
        $this->db->where('user_id', $user_id);
        $this->db->update(self::TBL_REFERENCE, $user);
    }

    public function insert($user)
    {
        $this->db->insert(self::TBL_REFERENCE, $user);
        $last_insert_id = $this->db->insert_id();

        return $last_insert_id;
    }

    /**
     * uid is unique id
     * can be uid, username or email
     */
    public function find_by_uniqueid($uid)
    {
        $this->db->group_start();
        $this->db->where('id', $uid);
        $this->db->or_where('email', $uid);
        $this->db->or_where('username', $uid);
        $this->db->group_end();

        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->row();
    }

    public function find_one($id)
    {
        $this->db->select('users.*, users_groups.group_id AS group_id, groups.name AS group_name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('users_groups', 'users.id = users_groups.user_id');
        $this->db->join('groups', 'users_groups.group_id = groups.id');
        $this->db->where('users.id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    function login($username, $password) 
    {
        $query = $this->db->query("SELECT {PRE}user.*, {PRE}user_group.name as group_name FROM {PRE}user 
            INNER JOIN {PRE}user_group 
            ON {PRE}user.user_group_id = {PRE}user_group.user_group_id
            WHERE username = '{$username}' AND password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('{$password}'))))) AND status = '1'");
        //$this->db->select('user.*, user_group.name as group_name');
        //$this->db->join('user_group', 'user.user_group_id = user_group.user_group_id');
        return $query->row();
    }

    public function find_by_username($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->row();
    }

}