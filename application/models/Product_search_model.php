<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_search_model extends MY_Model
{
    const TBL_REFERENCE = 'product';
    const PRIMARY_KEY = 'product_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    function find_by_ids($product_ids)
    {
        $language_id = $this->get_language_id();
        $this->db->select('product.*, product_description.name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where_in('product.product_id', $product_ids);

        $query = $this->db->get();
        return $query->result();
    }

}