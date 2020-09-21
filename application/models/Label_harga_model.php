<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Label_harga_model extends MY_Model
{
    const TBL_REFERENCE = 'product';
    const PRIMARY_KEY = 'product_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($param = array(), $first = 0, $count = 25, $sort = 'sort_order', $order = 'asc')
    {
        # code...
        $term="";
        for ($i=0;$i<count($param);$i++) {
            # code...
            if ($i==0) {
                # code...
                $term .= "'".$param[$i]."'";
            }else{
                $term .= ",'".$param[$i]."'";
            }
        }
        $language_id = (int) $this->get_language_id();
        $sql = "SELECT SUBSTRING(b.name,1,40) as name,a.model,FORMAT(a.price_2,2) as price,
                coalesce(DATE_FORMAT(a.expiry_date,'%d-%m-%Y'),'N/A') as expired 
                FROM {PRE}product a 
                LEFT JOIN {PRE}product_description b ON (a.product_id = b.product_id) 
                WHERE b.language_id = '{$language_id}'";

        if ($term != NULL && $term !== '') {
            $sql .= " AND a.product_id IN(" . $term . ")";
        }

        $sql .= " GROUP BY a.product_id";

        $sort_data = array(
            'name',
            'sort_order'
        );

        if ($sort != NULL && in_array($sort, $sort_data)) {
            $sql .= " ORDER BY " . $sort;
        } else {
            $sql .= " ORDER BY sort_order";
        }

        if ($order != NULL && ($order == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        $first = isset($first) && $first != 0 ? $first : 0;
        $count = isset($count) && $count != 0 ? $count : 20;
        $sql .= " LIMIT " . (int) $first . "," . (int) $count;
        $query = $this->db->query($sql);
        
        return $query->result();        
    }
}