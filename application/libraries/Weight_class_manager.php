<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use DusanKasan\Knapsack\Collection;

class Weight_class_manager
{
    protected $CI;
    protected $store_id = 0;

    protected $weight_classes = [];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
        $default_weight_class = $this->get_default();
        $this->weight_classes[] = $default_weight_class;
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
     * @param weight_class
     * @return weight_class_description
     */
    protected function create($weight_class)
    {
        if (!array_key_exists('title', $weight_class)) {
            throw new Exception('Weight class title is required');
        }

        $this->_get_db()->insert('weight_class', [
            'value' => 0,
            'default' => 0
        ]);
        $weight_class_id = $this->_get_db()->insert_id();

        $weight_class['weight_class_id'] = $weight_class_id;
        $this->_get_db()->insert('weight_class_description', $weight_class);


        return $weight_class;
    }

    /**
     * @param $weight_name string
     */
    public function find_or_create($weight_name)
    {
        $language_id = $this->_get_language_id();

        $this->_get_db()->where('title', $weight_name);
        $this->_get_db()->where('language_id', $language_id);
        $weight_class = $this->_get_db()->get('weight_class_description')->row_array();
        if ($weight_class != NULL) {
            return $weight_class;
        }
        
        $weight_class = [
            'language_id' => $language_id,
            'title' => $weight_name,
            'unit' => ''
        ];
        return $this->create($weight_class);
    }

    /**
     * @return weight_class_description
     */
    public function get_default()
    {
        try {
            log_message('info', 'Attempting to find default weight class in memory');
            $default_weight_class = Collection::from($this->weight_classes)->first();
            log_message('info', 'Got default weight class in memory');

            return $default_weight_class;
        } catch (Exception $ex) {
            log_message("error", "Cannot find default weight class in memory, attempting to find in database");
            
            $this->_get_db()->select('weight_class.*, weight_class_description.title, weight_class_description.unit');
            $this->_get_db()->from('weight_class');
            $this->_get_db()->join('weight_class_description','weight_class.weight_class_id = weight_class_description.weight_class_id');
            $this->_get_db()->where('default', 1);
    
            return $this->_get_db()->get()->row_array();
        }
    }
    
}