<?php

/**
 * @author Atang Sutisna <atang.sutisna.87@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('catalog_url')) 
{
    /**
     * @param string
     */
    function catalog_url($path)
    {
        return site_url('catalog/'. $path);
    }    
}
