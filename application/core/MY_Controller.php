<?php


class Admin_Controller extends CI_Controller {
    
    protected $rbac;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('ion_auth');
        $this->load->library('Rbac_manager', 'rbac_manager');
        if (!$this->ion_auth->logged_in()) {
            $this->session->set_flashdata('warn', 'Silahkan login terlebih dahulu');
            return redirect('auth');             
        }
    }

}