<?php

/**
 * @author Atang Sutisna <atang.sutisna.87@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('base_image_url')) 
{
    /**
     * @param string
     */
    function base_image_url($path)
    {
        $CI =& get_instance();
		$CI->load->config('btkcommerce');
        $config = $this->config->item('commerce');
        
        return site_url('catalog/');
    }    
}