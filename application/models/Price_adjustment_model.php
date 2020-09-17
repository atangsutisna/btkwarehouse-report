<?php

class Price_adjustment_model  extends MY_Model
{
    const TBL_REFERENCE = 'price_adjustment';
    const PRIMARY_KEY = 'price_adjustment_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'created_at', $direction = 'desc')
    {
        $this->db->select('price_adjustment.price_adjustment_id,
                        price_adjustment.product_id,
                        price_adjustment.product_name,
                        price_adjustment.model AS product_model,
                        price_adjustment.old_price,
                        price_adjustment.price,
                        product.image,
                        unit_measurement.symbol AS qty_unit,
                        price_adjustment.created_at');
        $this->db->from('price_adjustment');
        $this->db->join('product','price_adjustment.product_id = product.product_id');
        $this->db->join('unit_measurement', 'price_adjustment.qty_unit_id = unit_measurement.unit_measurement_id', 'left');

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_all($criterion = [])
    {
        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }
        
        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    function insert($data)
    {
    	$this->db->trans_start();
    	$this->db->insert_batch(self::TBL_REFERENCE, $data);
    	$price_modified = array_map(function($item){
    		return [
    			'product_id' => $item['product_id'],
    			'price' => $item['final_price']
    		];
    	}, $data);
    	$this->db->update_batch('product', $price_modified, 'product_id');
    	$this->db->trans_complete();
    }


}