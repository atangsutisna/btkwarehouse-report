<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_receipt_model extends MY_Model
{
    const TBL_REFERENCE = 'goods_receipt';
    const PRIMARY_KEY = 'goods_receipt_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
        $this->load->model('Product_model', 'product_model');
        $this->load->model('Invbalance_model', 'invbalance_model');
    }

    function find_all($criterion = [], $first = 0, $count = 20, 
        $order = 'updated_at', $direction = 'desc')
    {
        $this->db->select('goods_receipt.*, purchase_order.purchase_order_no');
        $this->db->from('goods_receipt');
        $this->db->join('purchase_order', 'goods_receipt.purchase_order_id = purchase_order.purchase_order_id');
        if (array_key_exists('goods_receipt_no', $criterion)) {
            $goods_receipt_no = preg_replace('/\s/', '', $criterion['goods_receipt_no']);
            $this->db->where('goods_receipt_no', $goods_receipt_no);
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);
        }

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('received_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('received_date <=', $criterion['end_date']);   
        }

        if (array_key_exists('status', $criterion)) {
            $this->db->where('goods_receipt.status', $criterion['status']);
        }

        if (array_key_exists('term', $criterion)) {
            $term = preg_replace('/\s/', '', $criterion['term']);
            
            $this->db->group_start();
            $this->db->like('goods_receipt_no', $term);
            $this->db->or_like('purchase_order.purchase_order_no', $term);
            $this->db->or_like('goods_receipt.supplier_name', $term);
            $this->db->group_end();
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get();

        return $query->result();
    }
    
    function count_all($criterion = [])
    {
        if (array_key_exists('goods_receipt_no', $criterion)) {
            $goods_receipt_no = preg_replace('/\s/', '', $criterion['goods_receipt_no']);
            $this->db->where('goods_receipt_no', $goods_receipt_no);
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);
        }

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('received_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('received_date <=', $criterion['end_date']);   
        }

        if (array_key_exists('status', $criterion)) {
            $this->db->where('goods_receipt.status', $criterion['status']);   
        }

        if (array_key_exists('term', $criterion)) {
            $term = preg_replace('/\s/', '', $criterion['term']);

            $this->db->group_start();
            $this->db->like('goods_receipt_no', $term);
            $this->db->or_like('purchase_order.purchase_order_no', $term);
            $this->db->or_like('goods_receipt.supplier_name', $term);
            $this->db->group_end();
        }

        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('purchase_order', 'goods_receipt.purchase_order_id = purchase_order.purchase_order_id');
        return $this->db->count_all_results();
    }

    function get_next_id()
    {
        $query = $this->db->query("SELECT GET_NEXT_ID('goods_receipt_no') AS next_id");
        return $query->row()->next_id;
    }

    public function insert($goods_receipt)
    {
        //$this->db->trans_start();
        $goods_receipt_items = $goods_receipt['goods_receipt_items'];
        unset($goods_receipt['goods_receipt_items']);
        
        //insert into goods_receipt
        $goods_receipt['goods_receipt_no'] = $this->get_next_id();
        $this->db->insert('goods_receipt', $goods_receipt);
        $goods_receipt_id = $this->db->insert_id();

        //insert into goods_receipt item
        $total_qty = 0;
        $total_amount = 0;
        array_walk($goods_receipt_items, function(&$goods_receipt_item) use ($goods_receipt_id, &$total_qty, &$total_amount){
            $total_qty += $goods_receipt_item['qty'];
            $subtotal = $goods_receipt_item['price'] * $goods_receipt_item['qty'];
            $total_amount += $subtotal;

            $goods_receipt_item['goods_receipt_id'] = $goods_receipt_id;
            $goods_receipt_item['subtotal'] = $subtotal;
        });
        $this->db->insert_batch('goods_receipt_items', $goods_receipt_items);

        //update total qty and total amount and status
        $this->db->where('goods_receipt_id', $goods_receipt_id);
        $this->db->update('goods_receipt', [
            'total_qty' => $total_qty,
            'total_amount' => $total_amount
        ]);

        //update inventory balance
        /**$inv_data = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => $item['qty']
            ];
        }, $goods_receipt_items);
        $this->load->model('storagebin1_model');
        $this->storagebin1_model->insert($inv_data); **/

        //update purchase order to complete
        /**$this->db->where('purchase_order_id', $goods_receipt['purchase_order_id']);
        $this->db->update('purchase_order', ['status' => 'complete']);**/

        //update expiry date product
        /**$products = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'expiry_date' => $item['expiry_date']
            ];
        }, $goods_receipt_items);
        if (count($products) > 0) {
            $total_items = count($products);
            log_message('info','Update '.$total_items.' expiry date product');
            $this->db->update_batch('product',$products,'product_id');
        } **/

        //$this->db->trans_complete();

        return $goods_receipt_id;
    }

    function get_product_inventory($product_ids)
    {
        $this->db->where_in('product_id', $product_ids);
        $query = $this->db->get('product_inventory');

        return $query->result_array();
    }

    function update($goods_receipt_id, $goods_receipt)
    {
        //update only for status draft
        //convert goods_receipt_item into qty target if status is final
        //insert or update inventory balance
        //if product variant, insert into inventory balance item
    }

    function find_one($id, $fetch_details = FALSE)
    {
        if (!isset($id)) {
            throw new Exception("Goods receipt ID is required", 1);
        }

        $this->db->select('goods_receipt.*, purchase_order.purchase_order_date, purchase_order.purchase_order_no');
        $this->db->from('goods_receipt');
        $this->db->join('purchase_order', 'goods_receipt.purchase_order_id = purchase_order.purchase_order_id');
        $this->db->where('goods_receipt_id', $id);
        $query = $this->db->get();

        $goods_receipt = $query->row();
        if ($goods_receipt == NULL) {
            return NULL;
        }

        if ($fetch_details) {
            $this->db->select('goods_receipt_items.*, product.model AS product_model, product.cost_of_goods_sold');
            $this->db->from('goods_receipt_items');
            $this->db->join('product', 'goods_receipt_items.product_id = product.product_id');
            $this->db->where('goods_receipt_id', $id);
            $goods_receipt_items = $this->db->get()->result();

            array_walk($goods_receipt_items, function(&$item, $key) {
                unset($item->goods_receipt_id); 
                unset($item->goods_receipt_item_id);
            });
            $goods_receipt->goods_receipt_items = $goods_receipt_items;
        }

        return $goods_receipt;

    }

    function find_items($goods_receipt_id)
    {
        $query = $this->db->get_where('goods_receipt_items', [
            'goods_receipt_id' => $goods_receipt_id
        ]);

        return $query->result();
    }

    public function sum_qty_receipt($purchase_order_id)
    {
        // get goods_receipt_ids based on purchase_order_id
        $this->db->select('goods_receipt_id');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->where('purchase_order_id', $purchase_order_id);
        $goods_receipt_ids = $this->db->get()->result_array();
        $goods_receipt_ids = array_map(function($raw){
            return $raw['goods_receipt_id'];
        }, $goods_receipt_ids);

        // sum total based on goods_receipt_ids
        $this->db->select('product_id, SUM(qty) AS qty_receipt');
        $this->db->where_in('goods_receipt_id', $goods_receipt_ids);
        $this->db->group_by('product_id');
        $query = $this->db->get('goods_receipt_items');

        return $query->result_array();
    }

}