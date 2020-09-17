<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_model extends MY_Model
{
    const TBL_REFERENCE = 'retur';
    const PRIMARY_KEY = 'retur_id';

    private $search_criteria = ['retur_no', 'supplier_id'];

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
        $this->load->model('Product_model', 'product_model');
        $this->load->model('Invbalance_model', 'invbalance_model');
    }

    public function find_all($and_criterion = [], $first = 0, $count = 20, $order = 'retur_no', $direction = 'desc')
    {
        foreach ($and_criterion as $key => $value) {
            if (in_array($key, $this->search_criteria)) {
                $this->db->where($key, $value);
            }
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by($order, $direction);
        $query = $this->db->get(self::TBL_REFERENCE);

        return $query->result();
    }
    
    public function count_all($and_criterion = [])
    {
        foreach ($and_criterion as $key => $value) {
            if (in_array($key, $this->search_criteria)) {
                $this->db->where($key, $value);
            }
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }
    
    public function insert($retur)
    {
        $this->db->trans_start();
        $return_items = $retur['return_items'];
        unset($retur['return_items']);

        //insert into retur
        $retur['retur_no'] = $this->get_next_id();
        $this->db->insert('retur', $retur);
        $retur_id = $this->db->insert_id();

        //set return_id into return items
        array_walk($return_items, function(&$return_item) use ($retur_id){
            $return_item['retur_id'] = $retur_id;
        });
        $this->db->insert_batch('retur_items', $return_items);

        //update inventory balance
        /** 
        $inv_data = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => -abs($item['qty'])
            ];
        }, $return_items);
        
        $this->invbalance_model->update_all($inv_data);
        **/
        $this->db->trans_complete();
        return $retur_id;
    }

    function update($retur_id, $retur_order)
    {
        $this->db->trans_start();
        $return_items = $retur_order['return_items'];
        unset($retur_order['return_items']);
        //update purchase order
        $this->db->where('retur_id', $retur_id);
        $this->db->update(self::TBL_REFERENCE, $retur_order);
        //delete all items
        $this->db->delete('retur_items', [
            'retur_id' => $retur_id
        ]);
        //reinsert    
        $this->db->insert_batch('retur_items', $return_items);

        $this->db->trans_complete();
    }

    public function find_one($id, $fetch_details = FALSE)
    {
        $query_ret = $this->db->get_where(self::TBL_REFERENCE, [
            'retur_id' => $id
        ]);
        
        if ($fetch_details == FALSE) {
            return $query_ret->row();
        }
        
        $retur = $query_ret->row();
        if ($retur == NULL) {
            return NULL;
        }
        
        $this->db->select('retur_items.*, unit_measurement.symbol AS qty_unit');
        $this->db->from('retur_items');
        $this->db->join('unit_measurement', 'retur_items.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where('retur_id', $id);
        
        $return_items = $this->db->get()->result();
        array_walk($return_items, function(&$item, $key) {
            unset($item->retur_id); 
            unset($item->retur_item_id);
        });
        $retur->return_items = $return_items;

        return $retur;
    }

    function get_supplier($retur_id)
    {
        $this->db->select('supplier_id, supplier_name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->where('retur_id', $retur_id);
        $query = $this->db->get();

        return $query->row();
    }

    function get_next_id()
    {
        $query = $this->db->query("SELECT GET_NEXT_ID('return_to_vendor') AS next_id");
        return $query->row()->next_id;
    }


    
}