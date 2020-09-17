<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_price_model extends MY_Model 
{
    const TBL_REFERENCE = 'product_prices';
    const PRIMARY_KEY = 'product_price_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_by_product_id($product_id) 
    {
        $query = $this->db->get_where(self::TBL_REFERENCE, [
            'product_id' => $product_id
        ]);

        return $query->result();
    }
}