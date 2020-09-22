<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_supplier_model extends MY_Model
{
    const TBL_REFERENCE = 'supplier';
    const PRIMARY_KEY = 'supplier_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($param = array(), $first = 0, $count = 25, $sort = 'name', $order = 'asc')
    {
        # code...
        $language_id = (int) $this->get_language_id();
        $sql = "SELECT b.name,
                a.model as code,
                c.last_stock as stock,
                d.name as satuan 
                FROM {PRE}supplier ax 
                INNER JOIN {PRE}supplier_to_product bx ON (ax.supplier_id = bx.supplier_id)
                INNER JOIN {PRE}product a ON (bx.product_id = a.product_id)
                LEFT JOIN {PRE}product_description b ON (a.product_id = b.product_id) 
                INNER JOIN {PRE}stock_adjustment c ON (a.product_id = c.product_id)
                INNER JOIN {PRE}unit_measurement d ON (c.qty_unit_id = d.unit_measurement_id) 
                INNER JOIN {PRE}product_to_category e ON (a.product_id = e.product_id) 
                WHERE b.language_id = '{$language_id}'";

        if ($param['supplier'] != NULL && $param['supplier'] !== '') {
            $sql .= " AND ax.supplier_id='".$param['supplier']."'";
        }

        if ($param['category'] != NULL && $param['category'] !== '') {
            $sql .= " AND e.category_id='".$param['category']."'";
        }

        $sql .= " GROUP BY ax.supplier_id,b.name,a.model,c.last_stock,d.name";

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