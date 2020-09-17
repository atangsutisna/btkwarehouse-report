<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur extends Admin_Controller 
{
    const DIR_VIEW = 'retur';

    public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'read_return_supplier')) {
            redirect('forbidden', 'refresh');
        }

        $params = array(
            'js_resources' => ['assets/js/retur/index.js']
        );
        $this->load->template(self::DIR_VIEW.'/index', $params);
    }

	public function view($id)
	{
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'read_return_supplier')) {
            redirect('forbidden', 'refresh');
        }

        if (!isset($id)) {
            show_404();
        }

        $this->load->model('Retur_model', 'retur_model');
        $retur_data = $this->retur_model->find_one($id, TRUE);

        $params = array(
            'retur_data' => $retur_data
        );
        $this->load->template(self::DIR_VIEW.'/view', $params);
    }

    public function create() 
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'create_return_supplier')) {
            redirect('forbidden', 'refresh');
        }

        $params = array(
            'js_resources' => ['assets/js/retur/form.js']
        );
        $this->load->template(self::DIR_VIEW.'/_form', $params);
    }

    public function edit_form($id)
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'update_return_supplier')) {
            redirect('forbidden', 'refresh');
        }

        if (!isset($id)) {
            show_404();
        }

        $this->load->model('Retur_model', 'retur');
        $params = array(
            'retur_id' => $id,
            'supplier' => $this->retur->get_supplier($id),
            'js_resources' => ['assets/js/retur/form.js']
        );
        $this->load->template(self::DIR_VIEW.'/_form', $params);
    }

}