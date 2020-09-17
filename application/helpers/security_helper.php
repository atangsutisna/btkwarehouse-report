<?php

/**
 * @author Atang Sutisna <atang.sutisna.87@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('generate_token')) 
{
	function generate_token($length = 32) {
		// Create random token
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		$max = strlen($string) - 1;
		
		$token = '';
		
		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}	
		
		return $token;
	}

}

if (! function_exists('encrypt_url')) 
{
	function encrypt_url($url) {
		$output = false;
	    /*

	    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code

	    */        
	    $security       = parse_ini_file("security.ini");
	    $secret_key     = $security["encryption_key"];
	    $secret_iv      = $security["iv"];
	    $encrypt_method = $security["encryption_mechanism"];

	    // hash
	    $key    = hash("sha256", $secret_key);

	    // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
	    $iv     = substr(hash("sha256", $secret_iv), 0, 16);

	    //do the encryption given text/string/number
	    $result = openssl_encrypt($url, $encrypt_method, $key, 0, $iv);
	    $output = base64_encode($result);
	    return $output;
	}
}

if (! function_exists('decrypt_url')) 
{
	function decrypt_url($url) {
		$output = false;
	    /*
	    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
	    */

	    $security       = parse_ini_file("security.ini");
	    $secret_key     = $security["encryption_key"];
	    $secret_iv      = $security["iv"];
	    $encrypt_method = $security["encryption_mechanism"];

	    // hash
	    $key    = hash("sha256", $secret_key);

	    // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
	    $iv = substr(hash("sha256", $secret_iv), 0, 16);

	    //do the decryption given text/string/number

	    $output = openssl_decrypt(base64_decode($url), $encrypt_method, $key, 0, $iv);
	    return $output;
	}
}

if (! function_exists('get_identity')) 
{
    /**
     * @param string $danger_message
     */
    function get_identity()
    {
        $CI = & get_instance();
        return $CI->ion_auth->user()->row();
    }
    
}

if (! function_exists('get_logged_role_name')) 
{
    /**
     * @param string $danger_message
     */
    function get_logged_role_name()
    {
        $CI = & get_instance();
        return $CI->session->userdata('level');
    }
    
}

if (! function_exists('is_user')) 
{
    function is_user()
    {
        $CI = & get_instance();
        return $CI->session->userdata('level') == 'user';
    }
    
}

if (! function_exists('is_admin')) 
{
    function is_admin()
    {
        $CI = & get_instance();
        return $CI->session->userdata('level') == 'admin';
    }
    
}