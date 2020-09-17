<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_order_model extends MY_Model
{
    const TBL_REFERENCE = 'purchase_order';
    const PRIMARY_KEY = 'purchase_order_id';

    private $purchase_order_status = ['draft','ordered','void','complete','partial'];
    private $search_criteria = ['purchase_order_no', 'supplier_id', 'status'];

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'created_at', $direction = 'desc')
    {
        if (array_key_exists('purchase_order_no', $criterion)) {
            $this->db->like('purchase_order_no', $criterion['purchase_order_no']);
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);
        }

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }

        if (array_key_exists('status', $criterion)) {
            $criteria_status = $criterion['status'];
            if (is_array($criteria_status)) {
                $this->db->where_in('status', $criterion['status']);
            } else {
                $this->db->where('status', $criterion['status']);
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
    
    public function count_all($criterion = [])
    {
        if (array_key_exists('purchase_order_no', $criterion)) {
            $this->db->like('purchase_order_no', $criterion['purchase_order_no']);
        }

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);
        }

        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }

        if (array_key_exists('status', $criterion)) {
            $criteria_status = $criterion['status'];
            if (is_array($criteria_status)) {
                $this->db->where_in('status', $criterion['status']);
            } else {
                $this->db->where('status', $criterion['status']);
            }
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    function insert($purchase_order)
    {
        $this->db->trans_start();
        $order_items = $purchase_order['order_items'];
        unset($purchase_order['order_items']);

        $this->db->insert(self::TBL_REFERENCE, $purchase_order);
        $purchase_order_id = $this->db->insert_id();
        array_walk($order_items, function(&$value, $key) use ($purchase_order_id) {
            $value['purchase_order_id'] = $purchase_order_id;
        });
        $this->db->insert_batch('purchase_order_items', $order_items);

        $this->db->trans_complete();

        return $purchase_order_id;
    }

    function update($purchase_order_id, $purchase_order)
    {
        $this->db->trans_start();
        $order_items = $purchase_order['order_items'];
        unset($purchase_order['order_items']);
        //update purchase order
        $this->db->where('purchase_order_id', $purchase_order_id);
        $this->db->update(self::TBL_REFERENCE, $purchase_order);
        // update purchase order items
        $order_items = array_map(function($order_item){
            return [
                'purchase_order_item_id' => $order_item['purchase_order_item_id'],
                'qty' => $order_item['qty'],
                'note' => $order_item['note'],
            ];
        }, $order_items);
        $this->db->update_batch('purchase_order_items', $order_items, 'purchase_order_item_id');

        $this->db->trans_complete();
    }

    function find_one($id, $fetch_details = FALSE)
    {
        $query_po = $this->db->get_where(self::TBL_REFERENCE, [
            'purchase_order_id' => $id
        ]);
        
        if ($fetch_details == FALSE) {
            return $query_po->row();
        }
        
        $purchase_order = $query_po->row();
        if ($purchase_order == NULL) {
            return NULL;
        }
        
        $this->db->select('purchase_order_items.*, product.model as product_model, product.image AS product_image, unit_measurement.symbol AS qty_unit');
        $this->db->from('purchase_order_items');
        $this->db->join('product', 'purchase_order_items.product_id = product.product_id');
        $this->db->join('unit_measurement', 'purchase_order_items.qty_unit_id = unit_measurement.unit_measurement_id');
        $this->db->where('purchase_order_id', $purchase_order->purchase_order_id);
        $order_items = $this->db->get()->result();
        
        array_walk($order_items, function(&$item, $key) {
            unset($item->purchase_order_id); 
        });
        $purchase_order->order_items = $order_items;

        return $purchase_order;
    }

    function get_supplier($purchase_order_id)
    {
        $this->db->select('supplier_id, supplier_name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->where('purchase_order_id', $purchase_order_id);
        $query = $this->db->get();

        return $query->row();
    }

    function update_status($purchase_order_id, $status)
    {
        if (!in_array($status, $this->purchase_order_status)) {
            throw new Exception('Invalid purchase-order status. The valid are draft, void and complete');
        }

        $this->db->where('purchase_order_id', $purchase_order_id);
        $this->db->update(self::TBL_REFERENCE, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    function get_next_id()
    {
        $query = $this->db->query("SELECT GET_NEXT_ID('purchase_order_no') AS next_id");
        return $query->row()->next_id;        
    }

    public function sum_qty_order($purchase_order_id)
    {
        $this->db->select('product_id, SUM(qty) AS qty_order');
        $this->db->from('purchase_order_items');
        $this->db->where('purchase_order_id', $purchase_order_id);
        $this->db->group_by('product_id');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function update_qty_order_balance($purchase_order_id, $data)
    {
        foreach ($data as $raw) {
            $this->db->where('purchase_order_id', $purchase_order_id);
            $this->db->where('product_id', $raw['product_id']);
            $this->db->update('purchase_order_items', [
                'qty_receipt' => $raw['qty_receipt'],
                'qty_balance' => $raw['qty_balance']
            ]);    
        }
    }

    public function insert_or_update($purchase_order)
    {
        if ($this->_get_draft_by_supplier($purchase_order['supplier_id']) > 0) {
            $draft_items = $this->_get_draft_items($purchase_order['supplier_id']);
            $purchase_order_id = $draft_items[0]['purchase_order_id'];

            $incoming_items = $purchase_order['order_items'];
            foreach ($incoming_items as &$incoming_item) {
                $product_id = $incoming_item['product_id'];
                
                $product_ids = array_column($draft_items, 'product_id');
                $draft_item_key = array_search($product_id, $product_ids);
                if (false !== $draft_item_key && array_key_exists($draft_item_key, $draft_items)) {
                    $draft_item = $draft_items[$draft_item_key];
                    $incoming_item['qty'] = $draft_item['qty'];
                    
                    unset($draft_items[$draft_item_key]);
                }
            }

            foreach ($draft_items as $draft_item) {
                $purchase_order_item = [
                    'product_id' => $draft_item['product_id'],
                    'product_name' => $draft_item['product_name'],
                    'price' => $draft_item['price'],
                    'qty' => $draft_item['qty'],
                    'qty_unit_id' => $draft_item['qty_unit_id'],
                    'qty_rasio' => $draft_item['qty_rasio'],
                    'note' => $draft_item['note'],
                    'subtotal' => 0,
                ];         
                array_push($purchase_order['order_items'], $purchase_order_item);
            }

            $this->purchase_order->update($purchase_order_id, $purchase_order);
            return $purchase_order_id;
        } else {
            $purchase_order['purchase_order_no'] = $this->get_next_id();
            $purchase_order_id = $this->insert($purchase_order);

            return $purchase_order_id;
        }
    }

    private function _get_draft_by_supplier($supplier_id)
    {
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('status', 'draft');
        $this->db->from('purchase_order');

        return $this->db->count_all_results();
    }

    private function _get_draft_items($supplier_id)
    {
        $this->db->select('purchase_order.purchase_order_id, 
                        purchase_order_items.product_id,
                        purchase_order_items.product_name,
                        purchase_order_items.qty,
                        purchase_order_items.price,
                        unit_measurement.symbol AS qty_unit,
                        purchase_order_items.qty_unit_id,
                        ');
        $this->db->from('purchase_order');
        $this->db->join('purchase_order_items', 'purchase_order.purchase_order_id = purchase_order_items.purchase_order_id');
        $this->db->join('unit_measurement', 'purchase_order_items.qty_unit_id = unit_measurement.unit_measurement_id');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('purchase_order.status', 'draft');

        $query = $this->db->get();
        return $query->result_array();
    }

    private function _delete_items($purchase_order_id)
    {
        $this->db->where('purchase_order_id', $purchase_order_id);
        $this->db->delete('purchase_order_items');
    }

}