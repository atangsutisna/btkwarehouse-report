<?php

class Sales_item_model extends CI_Model
{
    const TBL_REFERENCE = 'sales_item';
    const PRIMARY_KEY = 'sales_item_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
        self::PRIMARY_KEY);
    }

}
