<?php

class User_manager {
	protected $CI;

	public function __construct() 
	{
		$this->CI =& get_instance();
	}

	function isGuest() 
	{
        $id = $this->CI->session->userdata('id');
        $name = $this->CI->session->userdata('name');
        $level = $this->CI->session->userdata('level');

        if (!isset($id) && !isset($name) && !isset($level)) {
        	return TRUE;
        }

        return FALSE;
	}

	function getIdentity()
	{
		return [
	        'id' => $this->CI->session->userdata('id'),
	        'name' => $this->CI->session->userdata('name'),
	        'level' => $this->CI->session->userdata('level')
		];
	}

}