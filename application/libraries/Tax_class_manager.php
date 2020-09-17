<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use DusanKasan\Knapsack\Collection;

class Tax_class_manager
{
    protected $CI;
    private $tax_classes = [];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
        $default_tax_class = $this->get_default();
        $this->tax_classes[] = $default_tax_class;
    }

    /**
     * @return dbObject
     */
    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @param $tax_name string
     */
    public function find_or_create($title)
    {
        log_message("info", "Attempting to find a tax with title {$name} in memory");
        $tax_class = NULL;
        $tax_class = Collection::from($this->tax_classes)->find(function($value) use ($title){
            return $value['title'] == $title; 
        });

        if ($tax_class !== NULL) {
            log_message('info', "Got tax class with title {$title} in memory");
            return $tax_class;
        }

        log_message("info", "Failed to find tax class with title {$title} in memory, attempting to find in database");
        $tax_class = $this->_get_db()->get_where('tax_class',[
            'title' => $title
        ])->row_array();

        if ($tax_class != NULL) {
            log_message("info", "Got tax class with title {$title} in database");
            array_push($this->tax_classes, $tax_class);

            return $tax_class;
        }

        $tax_class = [
            'title' => $title,
            'description' => $title,
            'date_added' => date('Y-m-d H:i:s'),
            'date_modified' => date('Y-m-d H:i:s'),
            'default' => 0
        ];
        return $this->create($tax_class);
    }

    public function create($tax_class)
    {
        $this->_get_db()->insert('tax_class', $tax_class);
        $tax_class['tax_class_id'] = $this->_get_db()->insert_id();

        return $tax_class;
    }

    /**
     * @return tax_class
     */
    public function get_default()
    {
        try {
            log_message('info', 'Attempting to find default tax class in memory');
            $default_tax_class = Collection::from($this->tax_classes)->first();
            log_message('info', 'Got default tax class in memory');
            
            return $default_tax_class;
        } catch (Exception $ex) {
            log_message("error", "Cannot find default tax class in memory, attempting to find in database");
            $default_tax_class = $this->_get_db()->get_where('tax_class',[
                'default' => 1
            ])->row_array();
            return $default_tax_class;
        }
    }

}