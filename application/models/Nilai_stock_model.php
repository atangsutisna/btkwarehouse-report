<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nilai_stock_model extends MY_Model
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
        $language_id = (int) $this->get_language_id();
        $sql = "SELECT SUBSTRING(b.name,1,40) as name,
                CASE WHEN ".$param['type']."=2 THEN FORMAT(a.price,2) ELSE FORMAT(a.price_2,2) END as price,
                a.model as code,
                c.last_stock as stock,
                d.name as satuan,
                CASE WHEN ".$param['type']."=2 THEN c.last_stock*a.price ELSE c.last_stock*a.price_2 END as total 
                FROM {PRE}product a 
                LEFT JOIN {PRE}product_description b ON (a.product_id = b.product_id) 
                INNER JOIN {PRE}stock_adjustment c ON a.product_id = c.product_id 
                INNER JOIN {PRE}unit_measurement d ON c.qty_unit_id = d.unit_measurement_id 
                WHERE b.language_id = '{$language_id}'";

        if ($param['category'] != NULL && $param['category'] !== '') {
            $sql .= "";
        }

        $sql .= " GROUP BY a.product_id,c.last_stock,d.name";

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