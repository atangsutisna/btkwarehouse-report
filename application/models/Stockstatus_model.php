<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockstatus_model  extends MY_Model
{
    const TBL_REFERENCE = 'stock_status';
    const PRIMARY_KEY = 'stock_status_id';
    
    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
            self::PRIMARY_KEY);
    }

    public function find_all()
    {
        $language_id = (int) $this->get_language_id();

        $this->db->where('language_id', $language_id);
        $this->db->order_by('stock_status_id', 'asc');
        $query_tax = $this->db->get(self::TBL_REFERENCE);

        return $query_tax->result();
    }
    
}