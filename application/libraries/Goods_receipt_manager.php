<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_receipt_manager
{
    private $CI;

	public function __construct() 
	{
        $this->CI =& get_instance();
        $this->CI->load->library(['purchase_order_manager']);
        $this->CI->load->model(['goods_receipt_model','storagebin1_model']);
    }
    
    public function receipt($goods_receipt)
    {
        $this->CI->db->trans_start();

        $goods_receipt_id = $this->_save($goods_receipt);

        $goods_receipt_items = $goods_receipt['goods_receipt_items'];
        /**
         * TODO: move to purchasing 
         * 
        $inv_data = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => $item['qty'],
                'qty_unit_id' => $item['qty_unit_id']
            ];
        }, $goods_receipt_items);
        $this->_update_storagebin1($inv_data);
        **/

        $purchase_order_id = $goods_receipt['purchase_order_id'];
        $this->CI->purchase_order_manager->confirm($purchase_order_id);

        $product_expiries = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'expiry_date' => $item['expiry_date']
            ];
        }, $goods_receipt_items);        
        $this->_update_expiry_date($product_expiries);

        $this->CI->db->trans_complete();

        return $goods_receipt_id;
    }

    protected function _update_storagebin1($inv_data)
    {
        $this->CI->storagebin1_model->insert($inv_data);
    }

    protected function _save($goods_receipt)
    {
        $goods_receipt_id = $this->CI->goods_receipt_model->insert($goods_receipt);
        return $goods_receipt_id;
    }

    protected function _update_expiry_date($product_expiries)
    {
        if (is_array($product_expiries) && count($product_expiries) > 0) {
            $this->CI->db->update_batch('product',$product_expiries,'product_id');
        }
    }

}