<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_pembelian_model extends MY_Model
{
    const TBL_REFERENCE = 'supplier';
    const PRIMARY_KEY = 'supplier_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    public function find_all($param = array(), $first = 1, $count = 25, $sort = 'name', $order = 'asc')
    {
        # code...
        $language_id = (int) $this->get_language_id();
        $sql = "SELECT ax.name,ax.supplier_id 
                FROM {PRE}supplier ax";

        if ($param['supplier'] != NULL && $param['supplier'] !== '') {
            $sql .= " WHERE ax.supplier_id='".$param['supplier']."'";
        }

        $sql .= " GROUP BY ax.name,ax.supplier_id";

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