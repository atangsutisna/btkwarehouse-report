<?php

class Customer_model  extends MY_Model
{
    const TBL_REFERENCE = 'customer';
    const PRIMARY_KEY = 'customer_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($term = NULL, $first = 0, $count = 25, $sort = 'date_added', $order = 'asc') 
    {
        $language_id = (int) $this->get_language_id();

        $this->db->select('customer_id, customer.customer_group_id, customer_group_description.description AS customer_grup_name, 
            firstname, lastname, email, telephone, date_added');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('customer_group_description', '{PRE}customer.customer_group_id = {PRE}customer_group_description.customer_group_id');
        $this->db->where('customer_group_description.language_id', $language_id);

        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('first_name', $term);
            $this->db->or_like('last_name', $term); 
            $this->db->group_end();
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );

        $sort = $sort ?? 'date_added';
        $direction = $direction ?? 'desc';
        $this->db->order_by($sort, $direction);
        $query = $this->db->get();
       
        return $query->result();
    }

    public function count_all($term = NULL)
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('first_name', $term);
            $this->db->or_like('last_name', $term); 
            $this->db->group_end();
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }
}