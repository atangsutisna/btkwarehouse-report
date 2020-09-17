<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mutation_stock_model extends MY_Model 
{
    const TBL_REFERENCE = 'mutation_stock';
    const PRIMARY_KEY = 'mutation_stock_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'updated_at', $direction = 'desc')
    {
        if (array_key_exists('product_id', $criterion)) {
            $this->db->where('product_id', $criterion['product_id']);
        }

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('mutation_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('mutation_date <=', $criterion['end_date']);
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result();
    }

    public function count_all($term = NULL)
    {
        if ($term != NULL && $term !== '') {
            $this->db->group_start();
            $this->db->like('description', $term);
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
        $this->db->select('inventory_balance.*, unit_measurement.name AS unit_name, unit_measurement.symbol AS unit_symbol,');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('unit_measurement', 'inventory_balance.qty_unit_id = unit_measurement.unit_measurement_id');
        $this->db->where('inventory_balance_id', $inv_bal_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function insert($data)
    {
        $this->db->insert(self::TBL_REFERENCE, $data);
        return $this->db->insert_id();
    }
    
    public function update($inv_bal_id, $ivbalance)
    {
        $this->db->trans_start();
        $ivbalance_item = $ivbalance['ivbalance_item'];
        unset($ivbalance['ivbalance_item']);
        
        $this->db->where('inventory_balance_id', $inv_bal_id);
        $this->db->update(self::TBL_REFERENCE, $ivbalance);
        
        /*$this->db->delete('purchase_order_items', [
            'purchase_order_id' => $purchase_order_id
        ]);
            
        $this->db->insert_batch('purchase_order_items', $order_items);*/

        $this->db->trans_complete();
    }

    protected function validate($data)
    {
        array_walk($data, function($item){
            if (!array_key_exists('transaction_id', $item)) {
                throw new Exeption('Missing transaction ID');
            }

            if (!array_key_exists('transaction_type', $item)) {
                throw new Exeption('Missing transaction Type');
            }

            if (!array_key_exists('product_id', $item)) {
                throw new Exeption('Missing product ID');
            }

            if (!array_key_exists('qty', $item)) {
                throw new Exeption('Missing Qty');
            }

        });

        return TRUE;
    }

    public function insert_all($data)
    {
        $this->validate($data);
        $this->db->insert_batch(self::TBL_REFERENCE, $data);
    }
}