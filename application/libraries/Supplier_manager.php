<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use DusanKasan\Knapsack\Collection;

class Supplier_manager
{
    protected $CI;
    protected $suppliers = [];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
    }

    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @input
     * supplier is ['name' => 'string','description' => 'string', 'sort_order' => int]
     */
    protected function create($supplier)
    {
        if (!array_key_exists('name', $supplier)) {
            throw new Exception('Supplier name is required');
        }

        if (!array_key_exists('description', $supplier)) {
            $supplier['description'] = '';
        }

        if (!array_key_exists('sort_order', $supplier)) {
            $supplier['sort_order'] = 0;
        }

        log_message('info', 'Attempting to create a new supplier');
        $this->_get_db()->insert('supplier', $supplier);
        $supplier['supplier_id'] = $this->_get_db()->insert_id();
        log_message('info', 'New supplier was created with id '+ $supplier['supplier_id']);

        array_push($this->suppliers, $supplier);

        return $supplier;
    }

    /**
     * @return
     * supplier is ['name' => 'string','description' => 'string', 'sort_order' => int]
     */
    public function find_or_create($supplier_name)
    {
        $supplier = NULL;
        log_message("info", "Attempting to find supplier with name {$supplier_name} in memory");
        $supplier = Collection::from($this->suppliers)->find(function($value) use ($supplier_name){
            return $value['name'] == $supplier_name;
        });
        
        if ($supplier !== NULL) {
            log_message('info', "Got supplier with name {$supplier_name} in memory");
            return $supplier; 
        }

        log_message("info", "Failed to find supplier with name {$supplier_name} in memory, attempting to find in database");
        $this->_get_db()->where('name', $supplier_name);
        $supplier = $this->_get_db()->get('supplier')->row_array();
        if ($supplier != NULL) {
            log_message("info", "Got supplier with name {$supplier_name} in database");
            array_push($this->suppliers, $supplier);

            return $supplier;
        }
        
        $supplier = [
            'name' => $supplier_name,
            'description' => $supplier_name,
            'sort_order' => 0
        ];

        return $this->create($supplier);
    }



}