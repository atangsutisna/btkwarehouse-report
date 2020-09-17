<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storagebin1_model extends Storagebin_model 
{
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * data is array of [['product_id' => 0, 'product_name' => 'string', 'qty' => 'int', 'qty_unit_id' => 'int']]
     */
    public function insert($data)
    {
        //var_dump($data);
        $this->load->model('Product_model', 'product_model');
        if (!is_array($data)) {
            throw new Exception("Data must be array");
        }

        $product_ids = array_map(function($item_data) {
            return $item_data['product_id'];
        }, $data);
        $product_inventories = $this->get_product_inventories($product_ids);

        foreach ($data as $item_data) {
            log_message('info', 'Attempting to find product inventory for product id '. $item_data['product_id']);
            $product_inventory_idx = array_search($item_data['product_id'], 
                array_column($product_inventories, 'product_id'));

            if (false !== $product_inventory_idx && array_key_exists($product_inventory_idx, $product_inventories)) {
                $product_inventory = $product_inventories[$product_inventory_idx];

                $added_qty = $item_data['qty'] * $item_data['qty_rasio'];
                /** 
                if ($product_inventory['qty_unit_id'] == $item_data['qty_unit_id']) {
                    $added_qty = $item_data['qty'];
                } else {
                    log_message('info', 'Attempting to convert from qty_unit_id '. 
                        $product_inventory['qty_unit_id']. ' to '. $item_data['qty_unit_id'] . ' for product_id '. $item_data['product_id']);
                    throw new Exception("Not implemented Yet");
                } **/

                $storagebin1 = $product_inventory['storagebin1'] + $added_qty;
                $storagebin2 = $product_inventory['storagebin2'];
                $qty = $storagebin1 + $storagebin2;

                $this->_update_qty($item_data['product_id'], $qty);

                // insert mutation stock
                //qty unit dalam mutation stock menggunakan qty unit target
                $mutation_stock = [
                    'product_id' => $item_data['product_id'],
                    'original_qty' => $product_inventory['qty'],
                    'qty' => $added_qty,
                    'last_qty' => $qty,
                    'mutation_date' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creation_time' => date('Y-m-d H:i:s')
                ]; 
                $this->db->insert('mutation_stock', $mutation_stock);
            } else {
                throw new Exception("Cannot find product inventory with product id ". $item_data['product_id']);                
            }
        }
    }
    
    protected function _update_qty($product_id, $qty)
    {
        $this->db->where('product_id', $product_id);
        $this->db->update('inventory_balance', [
            'storagebin1' => $qty,
            'qty' => $qty
        ]);
        
    }

    /**
     * data is array of [['product_id' => 0, 'product_name' => 'string', 'qty' => 'int', 'qty_unit_id' => 'int']]
     */
    public function insert_or_modify($data)
    {
        $this->insert($data);
    }
}