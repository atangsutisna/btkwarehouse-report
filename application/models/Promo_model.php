<?php

class Promo_model  extends MY_Model
{
    const TBL_REFERENCE = 'promo';
    const PRIMARY_KEY = 'id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    function find_all($criterion = [], $first = 0, $count = 20, 
        $order = 'updated_at', $direction = 'desc')
    {
        if (array_key_exists('status', $criterion)) {
            $this->db->where('status', $criterion['status']);
        }

        if (array_key_exists('name', $criterion)) {
            $this->db->like('name', $criterion['name']);
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result();
    }
    
    function count_all($criterion = [])
    {
        if (array_key_exists('status', $criterion)) {
            $this->db->where('status', $criterion['status']);
        }
        
        if (array_key_exists('name', $criterion)) {
            $this->db->like('name', $criterion['name']);
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    function modify($id, $data) 
    {
        $this->db->trans_begin();
        if (array_key_exists('promo_product', $data)) {
            $this->db->where('promo_id', $id);
            $this->db->delete('promo_product');

            $promo_product = array_map(function($prod_id) use ($id) {
                return [
                    'promo_id' => $id,
                    'product_id' => $prod_id
                ];
            }, $data['promo_product']);
            $this->db->insert_batch('promo_product', $promo_product);

            unset($data['promo_product']);
        }

        $this->db->where('id', $id);
        $this->db->update('promo', $data);

        $this->db->trans_complete();
    }

    function find_one($id)
    {
        $this->db->where('id', $id);
        $product = $this->db->get(self::TBL_REFERENCE)->row();

        if ($product != NULL) {
            $this->db->where('promo_id', $id);
            $product->promo_product = $this->db->get('promo_product')->result();
        }

        return $product;
    }

    function insert($data) 
    {
        $this->db->trans_begin();

        $promo_product = [];
        if (array_key_exists('promo_product', $data)) {
            $promo_product = $data['promo_product'];
            unset($data['promo_product']);            
        }

        //var_dump($data);
        $this->db->insert('promo', $data);
        $id = $this->db->insert_id();

        if (count($promo_product) > 0) {
            $promo_product = array_map(function($prod_id) use ($id) {
                return [
                    'promo_id' => $id,
                    'product_id' => $prod_id
                ];
            }, $promo_product);
            $this->db->insert_batch('promo_product', $promo_product);
        }

        $this->db->trans_complete();

        return $id;
    }

}