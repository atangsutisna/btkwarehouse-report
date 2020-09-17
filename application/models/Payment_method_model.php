<?php

class Payment_method_model  extends MY_Model
{
    const TBL_REFERENCE = 'payment_method';
    const PRIMARY_KEY = 'payment_method_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($criterion = [], $first = 0, $count = 20, 
        $order = 'payment_method_id', $direction = 'desc')
    {
        if (array_key_exists('status', $criterion)) {
            $this->db->where('status', $criterion['status']);
        }
        $this->db->where_in('status', ['active','nonactive']);
        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result();
    }

    public function count_all($criterion = [])
    {
        if (array_key_exists('status', $criterion)) {
            $this->db->where('status', $criterion['status']);
        }

        $this->db->from(self::TBL_REFERENCE);
        $this->db->where_in('status', ['active','nonactive']);
        return $this->db->count_all_results();
    }


}