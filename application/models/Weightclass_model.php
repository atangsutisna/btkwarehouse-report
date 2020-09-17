<?php
/**
 * TODO: tambahkan default true ke table weightclass opencart
 */
class Weightclass_model  extends MY_Model
{
    const TBL_REFERENCE = 'weight_class_description';
    const PRIMARY_KEY = 'weight_class_id';
    
    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
            self::PRIMARY_KEY);
    }

    public function find_all()
    {
        $language_id = (int) $this->get_language_id();

        $this->db->select('weight_class_id, title, unit');
        $this->db->where('language_id', $language_id);
        $query_weight_desc = $this->db->get(self::TBL_REFERENCE);

        return $query_weight_desc->result();
    }

    public function get_default()
    {
        $language_id = (int) $this->get_language_id();

        $this->db->select('weight_class.weight_class_id, title, unit');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('weight_class', 'weight_class_description.weight_class_id = weight_class.weight_class_id');
        $this->db->where('language_id', $language_id);
        $this->db->where('weight_class.default', true);
        $query = $this->db->get();

        return $query->row();
    }
    
}