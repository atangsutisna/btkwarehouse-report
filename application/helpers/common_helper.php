<?php

/**
 * @author Atang Sutisna <atang.sutisna.87@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('show_bootstrap_warn_alert')) 
{
    /**
     * @param string $warn_message
     */
    function show_bootstrap_warn_alert($warn_message)
    {
        echo "<div class=\"alert alert-warning alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-info\"></i> Alert!</h4>
                {$warn_message}
            </div>";
    }    
}

if (! function_exists('show_bootstrap_info_alert')) 
{
    /**
     * @param string $info_message
     */
    function show_bootstrap_info_alert($info_message)
    {
        echo "<div class=\"alert alert-info alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-info\"></i> Informasi!</h4>
                {$info_message}
            </div>";
    }    
}

if (! function_exists('show_bootstrap_danger_alert')) 
{
    /**
     * @param string $danger_message
     */
    function show_bootstrap_danger_alert($danger_message)
    {
        echo "<div class=\"alert alert-danger\" role=\"alert\">{$danger_message}</div>";
    }
    
}

if (! function_exists('show_boostrap_alert')) 
{
    function show_bootstrap_alert()
    {
        $CI = & get_instance();
        
        $warn_message = $CI->session->flashdata('warn');
        if (isset($warn_message)) {
            show_bootstrap_warn_alert($warn_message);
        }
        
        $info_message = $CI->session->flashdata('info');
        if (isset($info_message)) {
            show_bootstrap_info_alert($info_message);
        }
        
        $danger_message = $CI->session->flashdata('danger');
        if (isset($danger_message)) {
            show_bootstrap_danger_alert($danger_message);
        }
        
    }
    
}

if (! function_exists('to_map')) 
{
    /**
     * @param string $danger_message
     */
    function to_map($raw_array, $key, $val)
    {
        $map = [NULL => '--select one--'];
        foreach ($raw_array as $raw) {
            $map[$raw->$key] = $raw->$val;
        }

        return $map;
    }
    
}

if (! function_exists('format_date')) 
{
    /**
     * @param string $danger_message
     */
    function format_date($date)
    {
        return date("d/m/Y", strtotime($date));
    }
    
}

if (! function_exists('currency_format')) 
{
    function currency_format($number, $currency = 'Rp')
    {
	   return $currency . number_format($number,0,',','.');       
    }
}

if (! function_exists('to_key_value')) 
{
    /**
     * @param string $danger_message
     */
    function to_key_value($raw_array, $key, $val)
    {
        $arr_map = [];
        foreach ($raw_array as $raw) {
            array_push($arr_map, [
                'id' => $raw->$key,
                'value' => $raw->$val,
            ]);
        }

        return $arr_map;
    }
    
}

/**
 * @author Atang Sutisna <atang.sutisna.87@gmail.com>
*/

if (! function_exists('format_rupiah')) 
{
    /**
     * @param string $warn_message
     */
    function format_rupiah($price)
    {
        $money = number_format($price,'0',',','.');
        return "Rp{$money}";
    }    
}

if (! function_exists('format_date_to')) 
{
    /**
     * 
     * FUNGSI TERBILANG OLEH : MALASNGODING.COM
     * WEBSITE : WWW.MALASNGODING.COM
     * AUTHOR : https://www.malasngoding.com/author/admin    
     */
    function format_date_to($input_date, $format = "d F Y") 
    {
        $date = date_create($input_date);
        return date_format($date, $format); 
    }
    
}

if (! function_exists('set_active')) 
{
    /**
     * @param
     */
    function set_active($menu_name)
    {
        $CI =& get_instance();
        $active_controller = $CI->router->fetch_class();
        $is_equal = strcasecmp($active_controller, $menu_name);
        if ($is_equal == 0) {
            return "class=\"active\"";
        }

        return FALSE;
    }    
}

if (! function_exists('set_group_active')) 
{
    /**
     * @param
     */
    function set_group_active($group_name)
    {
        $CI =& get_instance();
        $group_segment = $CI->uri->segment(1);
        $is_equal = strcasecmp($group_segment, $group_name);
        if ($is_equal == 0) {
            return "active";
        }

        return '';
    }    
}

if (! function_exists('set_group_member_active')) 
{
    /**
     * @param
     */
    function set_group_member_active($group_member_name)
    {
        $CI =& get_instance();
        $group_member_segment = $CI->uri->segment(2);
        $is_equal = strcasecmp($group_member_segment, $group_member_name);
        if ($is_equal == 0) {
            return "class=\"active\"";
        }

        return '';
    }    
}

if (! function_exists('selected')) 
{
    /**
     * @param
     */
    function selected($option_value, $object_value = NULL)
    {
        if ($object_value == NULL) {
            return "";
        }
        
        return $option_value == $object_value ? "selected=\"selected\"" : "";
    }    
}

if (! function_exists('to_dropdown_values')) 
{
    /**
     * @param string $danger_message
     */
    function to_dropdown_values($array_objects, $key, $val)
    {
        $dropdown_values = [];
        foreach ($array_objects as $object) {
            $dropdown_values[$object->$key] = $object->$val;
        }

        return $dropdown_values;
    }
    
}

if (! function_exists('remove_whitespace')) 
{
    function remove_whitespace($sentence)
    {
        return preg_replace('/\s/', '', $sentence);
    }
}

if (! function_exists('get_operator')) 
{
    function get_operator($document)
    {
        if ((isset($document->operator_id) && $document->operator_id != '') || 
                (isset($document->operator_name) && $document->operator_name != '')) {
            return $document->operator_name.' '.'('.$document->operator_id.')';
        }

        return 'Not Set';
    }
}
