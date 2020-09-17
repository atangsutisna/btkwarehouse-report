<?php

class Lengthclass_model  extends MY_Model
{
    const TBL_REFERENCE = 'length_class_description';
    const PRIMARY_KEY = 'length_class_id';
    
    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
            self::PRIMARY_KEY);
    }

    public function find_all()
    {
        $language_id = (int) $this->get_language_id();

        $this->db->select('length_class_id, title, unit');
        $this->db->where('language_id', $language_id);
        $query_weight_desc = $this->db->get(self::TBL_REFERENCE);

        return $query_weight_desc->result();
    }
    
}