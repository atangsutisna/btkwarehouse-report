<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use DusanKasan\Knapsack\Collection;

class Stock_status_manager
{
    protected $CI;
    protected $store_id = 0;

    protected $stock_statuses = [];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();

        $stock_status = $this->find_or_create('In Stock');
        $this->stock_statuses[] = $stock_status;
    }

    /**
     * @return dbObject
     */
    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @return lanuage_id int
     */
    public function _get_language_id()
    {
        $this->_get_db()->select('store_id, key, value as setting_value');
        $this->_get_db()->from('setting');
        $this->_get_db()->where('store_id', $this->store_id);
        $this->_get_db()->where('key', 'config_language');
        $query_setting = $this->_get_db()->get();
        if ($query_setting->row() == NULL) {
            return NULL;
        }

        $this->_get_db()->select('language_id');
        $this->_get_db()->from('language');
        $this->_get_db()->where('code', $query_setting->row()->setting_value);
        $query_language = $this->_get_db()->get();
        if ($query_language->row() == NULL) {
            return NULL;
        }

        return $query_language->row()->language_id;
    }    

    /**
     * @return stock_status
     */
    public function find_by_name($name)
    {
        $language_id = $this->_get_language_id();   

        log_message("info", "Attempting to find stock status with name {$name} in memory");
        $stock_status = NULL;
        $stock_status = Collection::from($this->stock_statuses)->find(function($value) use ($name, $language_id){
            return $value['name'] == $name && $value['language_id'] == $language_id;
        });

        if ($stock_status !== NULL) {
            log_message('info', "Got stock status with name {$name} in memory");   
            return $stock_status;
        }

        log_message("info", "Failed to find stock status {$name} in memory, attempting to find in database");
        $stock_status = $this->_get_db()->get_where('stock_status', [
            'language_id' => $language_id,
            'name' => $name
        ])->row_array();

        if ($stock_status !== NULL) {
            log_message("info", "Got store stock status with name {$name} in database");
            array_push($this->stock_statuses, $stock_status);
        }

        return $stock_status;
    }

    //In Stock
    /**
     * @return stock_status
     */
    public function find_or_create($name)
    {
        $language_id = $this->_get_language_id();        
        $stock_status = $this->find_by_name($name);
        
        if ($stock_status != NULL) {
            return $stock_status;
        }

        $stock_status = [
            'language_id' => $language_id,
            'name' => $name
        ];
        $this->_get_db()->insert('stock_status', $stock_status);
        $stock_status_id = $this->_get_db()->insert_id();
        $stock_status['stock_status_id'] = $stock_status_id;

        return $stock_status;
    }

}