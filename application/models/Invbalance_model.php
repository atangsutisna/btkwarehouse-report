<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invbalance_model extends MY_Model 
{
    const TBL_REFERENCE = 'inventory_balance';
    const PRIMARY_KEY = 'inventory_balance_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'updated_at', $direction = 'desc')
    {
        $this->db->select('inventory_balance.*, 
                    product.model AS product_model, 
                    product_description.name AS product_name, 
                    product.image AS product_image, 
                    unit_measurement.symbol AS qty_unit');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('unit_measurement', 'unit_measurement.unit_measurement_id = inventory_balance.qty_unit_id');
        $this->db->join('product_description', 'inventory_balance.product_id = product_description.product_id');
        $this->db->join('product', 'inventory_balance.product_id = product.product_id');
        $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id', 'left');

        if (array_key_exists('category_id', $criterion)) {
            $this->db->join('product_to_category', 'inventory_balance.product_id = product_to_category.product_id');
            $this->db->where('product_to_category.category_id', $criterion['category_id']);
        }

        if (array_key_exists('term', $criterion)) {
            $this->db->group_start();
            $this->db->like('product.name', $criterion['term']);
            $this->db->group_end();
        }

        if (array_key_exists('product_name', $criterion)) {
            $this->db->group_start();
            $this->db->like('product_description.name', $criterion['product_name']);
            $this->db->group_end();
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $supplier_id = $criterion['supplier_id'];
            $this->db->where('supplier_to_product.supplier_id', $supplier_id);
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
        $this->db->join('product_description', 'inventory_balance.product_id = product_description.product_id');
        if (array_key_exists('term', $criterion)) {
            $this->db->group_start();
            $this->db->like('product.name', $criterion['term']);
            $this->db->group_end();
        }

        if (array_key_exists('product_name', $criterion)) {
            $this->db->group_start();
            $this->db->like('product_description.name', $criterion['product_name']);
            $this->db->group_end();
        }

        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }

    public function find_by_product_id($product_id) 
    {
        $this->db->select('inventory_balance.*, product_inventory.product_id');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_inventory', 'inventory_balance.inventory_balance_id = product_inventory.inventory_balance_id');
        $this->db->where('product_inventory.product_id', $product_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function find_by_id($inv_bal_id)
    {
        $this->db->select('inventory_balance.*, 
                    product.model AS product_model, 
                    product_description.name AS product_name, 
                    product.image AS product_image, 
                    unit_measurement.symbol AS qty_unit');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('unit_measurement', 'unit_measurement.unit_measurement_id = inventory_balance.qty_unit_id');
        $this->db->join('product_description', 'inventory_balance.product_id = product_description.product_id');
        $this->db->join('product', 'inventory_balance.product_id = product.product_id');
        $this->db->where('inventory_balance_id', $inv_bal_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function find_by_sku($sku)
    {   
        $query = $this->db->get_where('inventory_balance', [
            'sku' => $sku
        ]);

        return $query->row();
    }

    function update($inv_bal_id, $invbalance)
    {
        $this->db->trans_start();
        
        $inventory_balance = $this->db->get_where(self::TBL_REFERENCE,[
            'inventory_balance_id' => $inv_bal_id
        ])->row_array();
        if ($inventory_balance == NULL) {
            throw new Exception("Invalid inventory balance id");
        }

        $storagebin1 = $invbalance['qty'] ;
        $storagebin2 = $inventory_balance['storagebin2'];
        $qty = $storagebin1 + $storagebin2;

        $this->db->where('inventory_balance_id', $inv_bal_id);
        $this->db->update(self::TBL_REFERENCE, [
            'storagebin1' => $storagebin1,
            'storagebin2' => $storagebin2,
            'qty' => $qty,
        ]);

        $this->db->trans_complete();
    }

    function update_all($data)
    {
        $this->load->model('Product_model', 'product_model');
        if (!is_array($data)) {
            throw new Exception("Data must be array");
        }

        $product_inventories = $this->get_product_inventory($data);
        array_walk($data, function($item) use ($product_inventories) {
            $product_invetory_idx = array_search($item['product_id'], 
                array_column($product_inventories, 'product_id'));
            if ($product_invetory_idx >= 0) {
                $product_inventory = $product_inventories[$product_invetory_idx];

                $inventory_balance = $this->get_inventory_balance($product_inventory['inventory_balance_id']);
                if ($product_inventory['unit_measurement_id'] == $inventory_balance->qty_unit_id) {
                    $updated_qty = $inventory_balance->qty + $item['qty'];
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
                    $updated_qty = $inventory_balance->qty + $convertion_result;
                }

                $storagebin1 = $updated_qty - $inventory_balance->storagebin2;
                $this->db->where('inventory_balance_id', $inventory_balance->inventory_balance_id);
                $this->db->update('inventory_balance', [
                    'storagebin1' => $storagebin1,
                    'qty' => $updated_qty
                ]);
                // insert mutation stock
                //qty unit dalam mutation stock menggunakan qty unit target
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
            } else {
                throw new Exception("Missing localSku (SKU Gudang) for product ". 
                    $data['product_name'] . "({$data['product_id']})");                
            }
        });
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

    public function move_storagebin2($data)
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
                $this->db->where('inventory_balance_id', $inventory_balance->inventory_balance_id);
                $this->db->update('inventory_balance', [
                    'storagebin1' => $storagebin1,
                    'storagebin2' => $storagebin2,
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

    public function find_by_skus($skus)
    {   
        $this->db->where_in('sku', $skus);
        $query = $this->db->get('inventory_balance');

        return $query->result_array();
    }

    public function insert_all($invbals)
    {   
        if (!is_array($invbals)) {
            return FALSE;
        }

        if (empty($invbals)) {
            return FALSE;
        }

        array_walk($invbals, function(&$invbal){
            $invbal['created_by'] = 0;
            $invbal['created_at'] = date('Y-m-d H:i:s');
            $invbal['updated_at'] = date('Y-m-d H:i:s');                
        });        
        $this->db->insert_batch('inventory_balance', $invbals);

        return TRUE;
    }

}