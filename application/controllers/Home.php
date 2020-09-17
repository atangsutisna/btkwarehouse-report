<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Admin_Controller 
{

    const DIR_VIEW = 'home';

	public function index()
	{
        $params = [];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }
}