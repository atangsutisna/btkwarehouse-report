<?php

class Return_model  extends MY_Model
{
    const TBL_REFERENCE = 'return_from_storefront';
    const PRIMARY_KEY = 'return_from_storefront';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
        $this->load->model('Storagebin1_model', 'storagebin1');
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'purchase_order_no', $direction = 'desc')
    {
        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('created_at >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('created_at <=', $criterion['end_date']);   
        }

        if (array_key_exists('receiver_name', $criterion)) {
            $this->db->like('receiver_name', $criterion['receiver_name']);   
        }

        if (array_key_exists('sender_name', $criterion)) {
            $this->db->like('sender_name', $criterion['sender_name']);   
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

        if (array_key_exists('receiver_name', $criterion)) {
            $this->db->like('receiver_name', $criterion['receiver_name']);   
        }
        
        if (array_key_exists('sender_name', $criterion)) {
            $this->db->like('sender_name', $criterion['sender_name']);   
        }
        
        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function get_next_no() 
    {
        $result = $this->db->query("SELECT GET_NEXT_ID('return_from_storefront') AS next_id");
        return $result->row()->next_id;
    }

    public function insert($data)
    {
        $this->db->trans_start();
        $return_from_storefront_items = $data['return_from_storefront_items'];
        unset($data['return_from_storefront_items']);

        $this->db->insert(self::TBL_REFERENCE, $data);
        $return_from_storefront_id = $this->db->insert_id();
        array_walk($return_from_storefront_items, function(&$value, $key) use ($return_from_storefront_id) {
            $value['return_from_storefront_id'] = $return_from_storefront_id;
        });
        $this->db->insert_batch('return_from_storefront_items', $return_from_storefront_items);

        $moved_data = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => $item['qty']
            ];
        }, $return_from_storefront_items);
        $this->storagebin1->add($moved_data);

        $this->db->trans_complete();

        return $return_from_storefront_id;
    }

    public function find_one($id)
    {
        $query1 = $this->db->get_where(self::TBL_REFERENCE, [
            'return_from_storefront_id' => $id
        ]);

        $rfs = $query1->row();
        
        $query2 = $this->db->get_where('return_from_storefront_items', [
            'return_from_storefront_id' => $id
        ]);
        $rfs->items = $query2->result();
        
        return $rfs;
    }

}