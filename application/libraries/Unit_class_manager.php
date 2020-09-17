<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use DusanKasan\Knapsack\Collection;

class Unit_class_manager
{
    protected $CI;
    private $unit_classes = [];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
        $default_unit_class = $this->get_default();
        $this->unit_classes[] = $default_unit_class;
    }

    /**
     * @return dbObject
     */
    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @param $symbol 'string'
     */
    public function find_or_create($symbol)
    {
        $unit_measurement = NULL;
        log_message("info", "Attempting to find unit with symbol {$symbol} in memory");
        $unit_measurement = Collection::from($this->unit_classes)->find(function($value) use ($symbol){
            return $value['symbol'] == $symbol;
        });
        
        if ($unit_measurement !== NULL) {
            log_message('info', "Got unit with symbol {$symbol} in memory");
            return $unit_measurement; 
        }

        log_message("info", "Failed to find unit with symbol {$symbol} in memory, attempting to find in database");
        $unit_measurement = $this->_get_db()->get_where('unit_measurement',[
            'symbol' => $symbol
        ])->row_array();

        if ($unit_measurement != NULL) {
            log_message("info", "Got unit with symbol {$symbol} in database");
            return $unit_measurement;
        }

        $unit_measurement = [
            'name' => $symbol,
            'description' => '',
            'symbol' => $symbol,
            'status' => 1,
        ];
        return $this->create($unit_measurement);
    }

    public function create($unit_measurement)
    {
        $this->_get_db()->insert('unit_measurement', $unit_measurement);
        $unit_measurement['unit_measurement_id'] = $this->_get_db()->insert_id();

        return $unit_measurement;
    }

    /**
     * @return unit_measurement
     */
    public function get_default()
    {
        try {
            log_message('info', 'Attempting to find default unit class in memory');
            $default_unit_class = Collection::from($this->unit_classes)->first();
            log_message('info', 'Got default unit class in memory');

            return $default_unit_class;
        } catch (Exception $ex) {
            log_message("error", "Cannot find default unit class in memory, attempting to find in database");
            $default_unit_class = $this->_get_db()->get_where('unit_measurement',[
                'symbol' => 'Pcs'
            ])->row_array();

            return $default_unit_class;
        }
    }

}