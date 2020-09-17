<?php

class Search_sales_model extends MY_Model
{
    const TBL_REFERENCE = 'sales';
    const PRIMARY_KEY = 'sales_id';

    public function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, 
        self::PRIMARY_KEY);
    }

}