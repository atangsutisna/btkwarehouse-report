<?php

/**
 * @author Atang Sutisna <atang.sutisna.87@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('get_purchasing_by_supplier')) 
{
    /**
     * @param string
     */
    function get_purchasing_by_supplier($supplier_id, $external_criterion = [])
    {
        $CI =& get_instance();
        $CI->load->model('purchasing_model');

        $criterion = [
            'supplier_id' => $supplier_id
        ];
        
        if (array_key_exists('start_date', $external_criterion)) {
            $criterion['start_date'] = $external_criterion['start_date'];
        }

        if (array_key_exists('end_date', $external_criterion)) {
            $criterion['end_date'] = $external_criterion['end_date'];
        }

        return $CI->purchasing_model->find_all($criterion, $first = 0, $count = 5000);
    }    
}
