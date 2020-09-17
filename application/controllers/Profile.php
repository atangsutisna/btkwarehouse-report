<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Admin_Controller {
    const DIR_VIEW = 'profile';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

	public function index()
	{
        $identity = get_identity();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
            $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
            if ($this->form_validation->run() === TRUE) {
                $this->ion_auth->update($identity->id,[
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                ]);
                $this->session->set_flashdata('info', $this->ion_auth->messages());
                redirect('profile', 'refresh');    
            }
        }

        $params = [
            'user' => get_identity()
        ];
        $this->load->template(self::DIR_VIEW.'/index', $params);
    }

    public function update_passwd()
    {
        $identity = get_identity();
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
        if ($this->form_validation->run() == TRUE) {
            $this->ion_auth->update($identity->id,[
                'password' => $this->input->post('password'),
            ]);
            $this->session->set_flashdata('info', $this->ion_auth->messages());
            redirect('profile', 'refresh');
        }
        
        $params = array(
            'user' => $identity,
            'js_resources' => []
        );

        $this->load->template(self::DIR_VIEW. '/index', $params);
    }


}