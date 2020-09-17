<?php
use DusanKasan\Knapsack\Collection;

class Purchasing_model  extends MY_Model
{
    const TBL_REFERENCE = 'purchasing';
    const PRIMARY_KEY = 'purchasing_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
        $this->load->model(['storagebin1_model','product_model','search_product_model']);
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'created_at', $direction = 'desc')
    {
        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);   
        }

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
        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);   
        }
        
        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function insert($purchasing)
    {
        $this->db->trans_start();
        $purchasing_items = $purchasing['purchasing_items'];
        unset($purchasing['purchasing_items']);

        $purchasing['purchasing_no'] = $this->get_next_no();
        if ($purchasing['note'] == NULL || $purchasing['note'] == '') {
            $purchasing['note'] = '';
        }
        
        $this->db->insert(self::TBL_REFERENCE, $purchasing);
        $purchasing_id = $this->db->insert_id();
        array_walk($purchasing_items, function(&$value, $key) use ($purchasing_id) {
            $value['purchasing_id'] = $purchasing_id;
        });
        $this->db->insert_batch('purchasing_items', $purchasing_items);
        // update prices
        $this->_update_prices($purchasing_items);
        // update inventory
        $inv_data = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => $item['qty'],
                'qty_unit_id' => $item['qty_unit_id'],
                'qty_rasio' => $item['qty_rasio'],
            ];
        }, $purchasing_items);
        $this->_update_inventory($inv_data);
        $this->_update_goods_receipt_to_be_complete($purchasing['goods_receipt_id']);

        $this->db->trans_complete();

        return $purchasing_id;
    }

    public function get_next_no() 
    {
        $result = $this->db->query("SELECT GET_NEXT_ID('purchasing_no') AS next_id");
        return $result->row()->next_id;
    }

    protected function _update_inventory($inv_data)
    {
        $this->storagebin1_model->insert($inv_data);
    }

    protected function _update_price($product_id, $product_prices)
    {
        $this->db->where('product_id', $product_id);
        $this->db->update('product', [
            'price' => $product_prices['online_price_pcs'] ?? 0, 
            'price_2' => $product_prices['offline_price_pcs'] ?? 0, 
            //'cost_of_goods_sold' => $product_prices['cost_of_goods_sold'] ?? 0, 
            'date_modified' => date('Y-m-d H:i:s')
        ]);

        if (array_key_exists('online_price_rasio', $product_prices) 
            && array_key_exists('offline_price_rasio', $product_prices)) {
            $this->db->where('product_id', $product_id);
            $this->db->update('product_variant', [
                'price' => $product_prices['online_price_rasio'],
                'price_2' => $product_prices['offline_price_rasio'],
            ]);    
        }

        $this->_insert_history($product_id, $product_prices);

        //get product by product_id and qty_unit_id
        $product = $this->db->get_where('product',[
            'product_id' => $product_id,
            'qty_unit_id' => $product_prices['qty_unit_id']
        ])->row();
        if ($product !== NULL) {
            // update cost of goods sold master product
            $this->db->where('product_id', $product_id);
            $this->db->update('product', [
                'cost_of_goods_sold' => $product_prices['cost_of_goods_sold'] ?? 0, 
            ]);                
        }
        
        //get product_variant by product_id and qty_unit_id
        $product_variant = $this->db->get_where('product_variant',[
            'product_id' => $product_id,
            'qty_unit_id' => $product_prices['qty_unit_id']
        ])->row();
        if ($product_variant !== NULL) {
            // update cost of goods sold product variant
            $this->db->where('product_id', $product_id);
            $this->db->update('product_variant', [
                'cost_of_goods_sold' => $product_prices['cost_of_goods_sold'] ?? 0, 
            ]);                
        }
    }
    
    protected function _update_prices($purchasing_items)
    {
        foreach ($purchasing_items as $purchasing_item) {
            $product_id = $purchasing_item['product_id'];
            $old_price = 0;

            $this->_update_price($product_id, [
                'product_id' => $purchasing_item['product_id'],
                'qty_unit_id' => $purchasing_item['qty_unit_id'],
                'cost_of_goods_sold' => $purchasing_item['price'],
                'offline_price_pcs' => $purchasing_item['offline_price_pcs'],
                'online_price_pcs' => $purchasing_item['online_price_pcs'],
                'offline_price_rasio' => $purchasing_item['offline_price_rasio'],
                'online_price_rasio' => $purchasing_item['online_price_rasio'],  
            ]);
        }
    }

    /**
     * product_prices is array of product_prices ['offline_price_pcs' => 0, 'online_price_pcs' => 0, 'offline_price_rasio' => 0, 'online_price_rasio' => 0]
     */
    protected function _insert_history($product_id, $product_prices)
    {
        $product = $this->product_model->find_one($product_id);
        $identity = get_identity();

        $this->db->select('price_2 AS offline_price_pcs');
        $this->db->from('product');
        $this->db->where_in('product_id', $product_id);
        $old_product_price = $this->db->get()->row_array();

        $this->db->insert('price_adjustment', [
            'product_id' => $product->product_id,
            'product_name' => $product->name,
            'model' => $product->model,
            'qty_unit_id' => $product->qty_unit_id,
            'price' => $product_prices['online_price_pcs'] ?? 0,
            'price_2' => $product_prices['offline_price_pcs'] ?? 0,
            'old_price_2' => $old_product_price['offline_price_pcs'] ?? 0,
            'created_by' => $identity->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $product_variant = $this->search_product_model->get_variant($product_id);
        if ($product_variant != NULL) {
            $this->db->insert('price_adjustment', [
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'model' => $product->model,
                'qty_unit_id' => $product_variant[0]->qty_unit_id,
                'price' => $product_prices['online_price_rasio'],
                'price_2' => $product_prices['offline_price_rasio'],
                'old_price_2' => $product_variant[0]->offline_price,
                'created_by' => $identity->id,
                'created_at' => date('Y-m-d H:i:s')
            ]);    
        }

    }

    protected function _update_goods_receipt_to_be_complete($goods_receipt_id)
    {
        $result = $this->db->get_where('goods_receipt', [
            'goods_receipt_id' => $goods_receipt_id
        ])->row();
        if ($result == NULL) {
            throw new Exception("Invalid goods receipt id");
        }

        $this->db->where('goods_receipt_id', $goods_receipt_id);
        $this->db->update('goods_receipt', ['status' => 'complete']);
    }

    public function find_one($id)
    {
        $this->db->select('purchasing.*, payment_method.payment_method_name AS payment_method_name');
        $this->db->from('purchasing');
        $this->db->join('payment_method', 'purchasing.payment_method = payment_method.payment_method_id');
        $this->db->where('purchasing_id', $id);
        $purchasing = $this->db->get()->row();

        if ($purchasing == NULL) {
            return NULL;
        }

        $this->db->select('purchasing_items.*, unit_measurement.symbol AS qty_unit');
        $this->db->from('purchasing_items');
        $this->db->join('unit_measurement', 'purchasing_items.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where('purchasing_id', $id);
        $purchasing->purchasing_items = $this->db->get()->result();

        return $purchasing;
    }

}