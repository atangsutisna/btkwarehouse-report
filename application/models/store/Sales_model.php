<?php

class Sales_model extends MY_Model
{
    const TBL_REFERENCE = 'sales';
    const PRIMARY_KEY = 'sales_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
        self::PRIMARY_KEY);
    }

    public function insert($sales)
    {
        $this->db->trans_start();
        if (!array_key_exists('created_at', $sales)) {
            $sales['created_at'] = date('Y-m-d');
        }
        
        $sales_items = $sales['sales_items'];
        unset($sales['sales_items']);
        $this->db->insert(self::TBL_REFERENCE, $sales);

        $sales_id = $this->db->insert_id();
        array_walk($sales_items, function(&$sales_item) use ($sales_id) {
            $sales_item['sales_id'] = $sales_id;
        });

        //TODO: update stock etalase
        $this->db->insert_batch('sales_item', $sales_items);
        $this->db->trans_complete();
    }
}
