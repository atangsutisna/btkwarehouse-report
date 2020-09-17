<?php

class Forbidden extends CI_Controller
{
    public function index() 
    {
        $params = [];
        $this->load->template('errors/forbidden', $params);    	
    }
}