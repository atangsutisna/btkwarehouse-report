<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Taxclass_model  extends MY_Model
{
    const TBL_REFERENCE = 'tax_class';
    const PRIMARY_KEY = 'tax_class_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
            self::PRIMARY_KEY);
    }
    
    public function find_all()
    {
        $this->db->order_by('tax_class_id', 'asc');
        $query_tax = $this->db->get(self::TBL_REFERENCE);

        return $query_tax->result();
    }

    public function get_default()
    {
        $this->db->select('tax_class_id, title, description');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->where('default', 1);
        $query = $this->db->get();

        return $query->row();
    }
    
}