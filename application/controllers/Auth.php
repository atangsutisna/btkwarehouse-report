<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    const DIR_VIEW = 'auth';

    private $username = 'admin@btk.co.id';
    private $password = 'admin12345';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');

		$this->load->database('btkguard');
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
    }

	public function index()
	{
		if ($this->ion_auth->logged_in()) {
            redirect('home', 'refresh');
        } 
        
        $params = [];
        $this->load->view(self::DIR_VIEW.'/index', $params);
    }
    
    public function login()
    {
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
        if ($this->form_validation->run() === TRUE) {
            $identity = $this->input->post('identity');
            $password = $this->input->post('password');

			if ($this->ion_auth->login($identity, $password, $remember_me = FALSE)) {
				$this->session->set_flashdata('info', $this->ion_auth->messages());
				redirect('/', 'refresh');
			}

            $this->session->set_flashdata('danger', $this->ion_auth->errors());
            redirect('auth', 'refresh');
        }

        $params = [
            'message' => (validation_errors()) ? validation_errors() : $this->session->flashdata('message')
        ];
        $this->load->view(self::DIR_VIEW.'/index', $params);
    }

    public function logout()
    {
		$this->data['title'] = "Logout";

		// log the user out
		$this->ion_auth->logout();
		redirect('auth', 'refresh');
    }
	
}
