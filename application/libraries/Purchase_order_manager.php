<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_order_manager
{
    private $CI;

	public function __construct() 
	{
        $this->CI =& get_instance();
        $this->CI->load->model(['purchase_order_model','goods_receipt_model']);
    }

    public function confirm($purchase_order_id)
    {
        $purchase_order = $this->_get($purchase_order_id);
        if ($purchase_order == NULL) {
            throw new Exception("Invalid purchase order");
        }
        
        $qty_orders = $this->_get_total_qty_order($purchase_order_id);
        $qty_receipts = $this->_get_total_qty_receipt($purchase_order_id);
        $audit_results = $this->_audit($qty_orders, $qty_receipts);
        
        $this->_update_status($purchase_order_id, $audit_results);
        $this->_update_qty_order_balance($purchase_order_id, $audit_results);
    }

    protected function _get($purchase_order_id)
    {
        return $this->CI->purchase_order_model->find_one($purchase_order_id);
    }

    protected function _audit($qty_orders, $qty_receipts)
    {
        $audit_results = [];
        foreach ($qty_orders as $qty_order) {
            $product_id = $qty_order['product_id'];

            $qty_receipt_key = array_search($product_id, array_column($qty_receipts, 'product_id'));
            $qty_receipt = $qty_receipts[$qty_receipt_key];

            $audit_result['product_id'] = $product_id;
            $audit_result['qty_order'] = $qty_order['qty_order'];
            $audit_result['qty_receipt'] = $qty_receipt['qty_receipt'];
            $audit_result['status'] = FALSE;

            $qty_balance = $qty_order['qty_order'] - $qty_receipt['qty_receipt'];
            $audit_result['qty_balance'] = $qty_balance;
            if ($qty_balance <= 0) {
                $audit_result['status'] = TRUE;
            }

            array_push($audit_results, $audit_result);
        }

        return $audit_results;
    }

    protected function _get_total_qty_order($purchase_order_id)
    {
        return $this->CI->purchase_order_model->sum_qty_order($purchase_order_id);
    }

    protected function _get_total_qty_receipt($purchase_order_id)
    {
        return $this->CI->goods_receipt_model->sum_qty_receipt($purchase_order_id);
    }

    protected function _update_status($purchase_order_id, $audit_results)
    {
        $order_status = NULL;
        foreach ($audit_results as $audit_result) {
            if ($order_status == NULL) {
                $order_status = $audit_result['status'];
                continue;
            }

            $order_status = $order_status && $audit_result['status'];
        }

        $status = $order_status == TRUE ? 'complete' : 'partial';
        $this->CI->purchase_order_model->update_status($purchase_order_id, $status);
    }

    protected function _update_qty_order_balance($purchase_order_id, $audit_results)
    {
        array_walk($audit_results, function(&$audit_result){
            unset($audit_result['status']);
        });
        $this->CI->purchase_order_model->update_qty_order_balance($purchase_order_id, $audit_results);   
    }

}