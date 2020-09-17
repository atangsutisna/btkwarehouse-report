<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method extends Admin_Controller 
{
    const DIR_VIEW = 'payment-method';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_method_model');
        $this->load->library(['form_validation']);
    }

    public function index()
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'update_price')) {
            redirect('forbidden', 'refresh');
        }

        $params = array(
            'js_resources' => [
                'assets/js/payment-method/index.js',
            ]
        );
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }
    
    public function create()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('name', 'Nama', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run() == TRUE) {
                $payment_method = [
                    'payment_method_name' => $this->input->post('name'),
                    'payment_method_description' => $this->input->post('description'),
                    'status' => $this->input->post('status'),
                ];

                $ref_id = $this->payment_method_model->insert($payment_method);
                $this->session->set_flashdata('info', 'Data sudah disimpan');
                redirect('payment_method/update/'. $ref_id);
            }            

        }

        $params = array(
            'js_resources' => []
        );
        $this->load->template(self::DIR_VIEW. '/create', $params);
    }

    public function update($id)
    {
        $payment_method = $this->payment_method_model->find_one($id);
        if ($payment_method == NULL) {
            show_404();
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('name', 'Nama', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run() == TRUE) {
                $payment_method = [
                    'payment_method_name' => $this->input->post('name'),
                    'payment_method_description' => $this->input->post('description'),
                    'status' => $this->input->post('status'),
                ];

                $this->payment_method_model->modify($id, $payment_method);
                $this->session->set_flashdata('info', 'Data sudah disimpan');
                redirect('payment_method/update/'. $id, 'refresh');
            }            

        }

        $params = array(
            'payment_method' => $payment_method,
            'js_resources' => []
        );
       $this->load->template(self::DIR_VIEW. '/update', $params);
    }

    public function delete($id)
    {
        $this->payment_method_model->modify($id, [
            'status' => 'void'
        ]);
        $this->session->set_flashdata('info', 'Data sudah dihapus');
        redirect('payment_method', 'refresh');
    }

}