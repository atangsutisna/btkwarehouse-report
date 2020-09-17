<?php

class Move_storefront_model  extends MY_Model
{
    const TBL_REFERENCE = 'move_to_storefront';
    const PRIMARY_KEY = 'move_to_storefront_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
        $this->load->model('Storagebin2_model', 'storagebin2');
    }

    public function find_all($criterion = [], $first = 0, $count = 20, $order = 'created_at', $direction = 'desc')
    {
        if (array_key_exists('start_date', $criterion)) {
            $this->db->where('moved_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('moved_date <=', $criterion['end_date']);   
        }

        if (array_key_exists('receiver_name', $criterion)) {
            $this->db->like('receiver_name', $criterion['receiver_name']);   
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
            $this->db->where('moved_date >=', $criterion['start_date']);
        }

        if (array_key_exists('end_date', $criterion)) {
            $this->db->where('moved_date <=', $criterion['end_date']);   
        }

        if (array_key_exists('receiver_name', $criterion)) {
            $this->db->like('receiver_name', $criterion['receiver_name']);   
        }
        
        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function insert($data)
    {
        $this->db->trans_start();
        $move_to_storefront_items = $data['move_to_storefront_items'];
        unset($data['move_to_storefront_items']);

        $this->db->insert(self::TBL_REFERENCE, $data);
        $move_to_storefront_id = $this->db->insert_id();
        array_walk($move_to_storefront_items, function(&$value, $key) use ($move_to_storefront_id) {
            $value['move_to_storefront_id'] = $move_to_storefront_id;
        });
        $this->db->insert_batch('move_to_storefront_items', $move_to_storefront_items);

        //insert into inventory balance
        $moved_data = array_map(function($item){
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => $item['qty']
            ];
        }, $move_to_storefront_items);
        $this->storagebin2->add($moved_data);

        $this->db->trans_complete();

        return $move_to_storefront_id;
    }

    public function get_next_no() 
    {
        $result = $this->db->query("SELECT GET_NEXT_ID('move_storefront') AS next_id");
        return $result->row()->next_id;
    }

    public function find_items($id)
    {
        $query = $this->db->get_where('move_to_storefront_items', [
            'move_to_storefront_id' => $id
        ]);

        return $query->result();
    }

}