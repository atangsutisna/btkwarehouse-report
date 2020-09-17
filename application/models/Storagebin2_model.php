<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storagebin2_model extends CI_Model 
{
    
    function __construct()
    {
        parent::__construct();
    }

    protected function get_product_inventory($data)
    {
        $this->load->model('Product_inventory_model', 'product_inventory');
        $product_ids = array_map(function($item){
            return $item['product_id'];
        }, $data);

        return $this->product_inventory->find_by_product_ids($product_ids);
    }

    protected function get_inventory_balance($invbal_id)
    {
        $invbal = $this->db->get_where('inventory_balance', [
            'inventory_balance_id' => $invbal_id
        ])->row();

        return $invbal;
    }

    public function add($data)
    {
        $product_inventories = $this->get_product_inventory($data);
        array_walk($data, function($item) use ($product_inventories) {
            $product_invetory_idx = array_search($item['product_id'], 
                array_column($product_inventories, 'product_id'));
            if ($product_invetory_idx >= 0) {
                $product_inventory = $product_inventories[$product_invetory_idx];

                $inventory_balance = $this->get_inventory_balance($product_inventory['inventory_balance_id']);
                if ($product_inventory['unit_measurement_id'] == $inventory_balance->qty_unit_id) {
                    $storagebin2 = $inventory_balance->storagebin2 + $item['qty'];
                } else {
                    //convert to qty unit target (inv balance)
                    $unit_convertion = $this->db->get_where('unit_measurement_convertion', [
                        'base_unit_measurement_id' => $product_inventory['unit_measurement_id'],
                        'to_unit_measurement_id' => $inventory_balance->qty_unit_id
                    ])->row();
                    if ($unit_convertion == NULL) {
                        throw new Exception("Missing unit convertion from {$product_inventory->unit_measurement_id} to {$inventory_balance->qty_unit_id}");
                    }

                    $convertion_result = $item['qty'] * $unit_convertion->multiply_rate;
                    $storagebin2 = $inventory_balance->storagebin2 + $convertion_result;
                }

                $storagebin1 = $inventory_balance->qty - $storagebin2;
                if ($storagebin1 < 0) {
                    throw new Exception("Product ".$item['product_name']. " tidak dapat disimpan. Qty melebihi batas");
                }
                $this->db->where('inventory_balance_id', $inventory_balance->inventory_balance_id);
                $this->db->update('inventory_balance', [
                    'storagebin1' => $storagebin1,
                    'storagebin2' => $storagebin2,
                ]);
                
                $this->update_qty_product([
                    'product_id' => $item['product_id'],
                    'qty' => $storagebin2
                ]);
                // insert mutation stock
                //qty unit dalam mutation stock menggunakan qty unit target
                /**
                $mutation_stock = [
                    'product_id' => $item['product_id'],
                    'original_qty' => $inventory_balance->qty,
                    'qty' => isset($convertion_result) ? $convertion_result : $item['qty'],
                    'last_qty' => $updated_qty,
                    'mutation_date' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'creation_time' => date('Y-m-d H:i:s')
                ]; 
                $this->db->insert('mutation_stock', $mutation_stock);
                **/
            } else {
                throw new Exception("Missing localSku (SKU Gudang) for product ". 
                    $data['product_name'] . "({$data['product_id']})");                
            }
        });
    }

    /**
     * ini digunakan untuk ketika ada perpindahan stok toko ke gudang
     */
    public function subtract($data)
    {
        $this->db->trans_start();

        $product_inventories = $this->get_product_inventory($data);
        array_walk($data, function($item) use ($product_inventories) {
            $product_invetory_idx = array_search($item['product_id'], 
                array_column($product_inventories, 'product_id'));
            if ($product_invetory_idx >= 0) {
                $product_inventory = $product_inventories[$product_invetory_idx];

                $inventory_balance = $this->get_inventory_balance($product_inventory['inventory_balance_id']);
                if ($product_inventory['unit_measurement_id'] !== $inventory_balance->qty_unit_id) {
                    //convert to qty unit target (inv balance)
                    $unit_convertion = $this->db->get_where('unit_measurement_convertion', [
                        'base_unit_measurement_id' => $product_inventory['unit_measurement_id'],
                        'to_unit_measurement_id' => $inventory_balance->qty_unit_id
                    ])->row();

                    if ($unit_convertion == NULL) {
                        throw new Exception("Missing unit convertion from {$product_inventory->unit_measurement_id} to {$inventory_balance->qty_unit_id}");
                    }

                    $real_qty = $item['qty'] * $unit_convertion->multiply_rate;
                    $item['qty'] = $real_qty;
                } 

                $storagebin2 = $inventory_balance->storagebin2 - $item['qty'];
                $total_qty = $inventory_balance->qty - $storagebin2;
                
                log_message('info','Update invetory balance for id '.$item['product_id'].', qty: '.$item['qty'].', etalase:'.$storagebin2.', total qty:'.$total_qty);
                $this->update_inventory_balance(
                    $inventory_balance->inventory_balance_id,
                    [
                        'storagebin2' => $storagebin2,
                        'qty' => $total_qty,
                    ]
                );
            } else {
                log_message('error','Missing localSku (SKU Gudang) for product '. $item['product_id']);
                throw new Exception("Missing localSku (SKU Gudang) for product ". 
                    $data['product_name'] . "({$data['product_id']})");                
            }
        });       
        
        $this->db->trans_complete();
    }

    protected function update_inventory_balance($invid, $invbalance)
    {

    }

    protected function update_qty_product($product)
    {
        $this->db->where('product_id', $product['product_id']);
        $this->db->update('product',[
            'quantity' => $product['qty']
        ]);
    }

    public function update($product)
    {
        $this->db->trans_start();
        
        $this->load->model('invbalance_model');
        $inventory_balance = $this->invbalance_model->find_by_product_id($product['product_id']);

        $storagebin1 = $inventory_balance->storagebin1;
        $storagebin2 = $inventory_balance->storagebin2 - $product['qty'];
        $total_qty = $storagebin1 + $storagebin2;
        $this->db->where('inventory_balance_id', $inventory_balance->inventory_balance_id);
        $this->db->update('inventory_balance',[
            'storagebin1' => $storagebin1,
            'storagebin2' => $storagebin2,
            'qty' => $total_qty,
        ]);

        $this->update_qty_product([
            'product_id' => $product['product_id'],
            'qty' => $storagebin2
        ]);

        $this->db->trans_complete();
    }

}