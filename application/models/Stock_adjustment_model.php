<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_adjustment_model extends MY_Model
{
    const TBL_REFERENCE = 'stock_adjustment';
    const PRIMARY_KEY = 'stock_adjustment_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
        $this->load->model('storagebin1_model');
    }

    public function find_all($criterion = [], $first = 0, $count = 20, 
        $order = 'created_at', $direction = 'desc')
    {
        $this->db->select('stock_adjustment.*, 
            product_description.name AS product_name,
            product.model AS product_model,
            product.image AS product_image,
            unit_measurement.symbol AS qty_unit');
        $this->db->from('stock_adjustment');
        $this->db->join('product', 'stock_adjustment.product_id = product.product_id');
        $this->db->join('product_description', 'stock_adjustment.product_id = product_description.product_id');
        $this->db->join('unit_measurement', 'stock_adjustment.qty_unit_id = unit_measurement.unit_measurement_id');

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('stock_adjustment.stock_adjustment_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('stock_adjustment.stock_adjustment_date <=', $criterion['end_date']);   
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get();

        return $query->result();
    }

    function find_one($id, $fetch_details = FALSE)
    {
        $query_sa = $this->db->get_where(self::TBL_REFERENCE, [
            'stock_adjustment_id' => $id
        ]);
        
        if ($fetch_details == FALSE) {
            return $query_sa->row();
        }
        
        $stock_adjustment = $query_sa->row();
        if ($stock_adjustment == NULL) {
            return NULL;
        }
        
        $order_items = $this->db->get_where('stock_adjustment_items', [
            'stock_adjustment_id' => $stock_adjustment->stock_adjustment_id
        ])->result();
        
        array_walk($order_items, function(&$item, $key) {
            unset($item->stock_adjustment_id); 
            unset($item->stock_adjustment_item_id);
        });
        $stock_adjustment->order_items = $order_items;

        return $stock_adjustment;
    }

    function count_all($criterion = [])
    {
        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('stock_adjustment.stock_adjustment_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('stock_adjustment.stock_adjustment_date <=', $criterion['end_date']);   
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function insert($data) {
        $this->db->trans_start();

        $this->db->insert(self::TBL_REFERENCE, $data);
        $ref_id = $this->db->insert_id();

        $qty = $data['stock_adjust'];
        // 2 is substraction
        if ($data['status_adjust'] == 2) {
            $qty = -$qty;
        }

        $this->storagebin1_model->insert_or_modify([
            [
                'product_id' => $data['product_id'],
                'qty' => $qty,
                'qty_unit_id' => $data['qty_unit_id']
            ]
        ]);

        $this->db->trans_complete();

        return $ref_id;
    }

}