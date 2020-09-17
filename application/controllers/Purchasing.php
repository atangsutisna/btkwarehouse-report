<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasing extends Admin_Controller 
{
    const DIR_VIEW = 'purchasing';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['payment_method_model','purchasing_model']);
    }

    public function index()
    {
        $params = array(
            'js_resources' => [
                'assets/js/purchasing/index.js',
            ]
        );
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function new_form()
    {
        $this->load->model('Sequence_model', 'sequence');
        $params = array(
            'js_resources' => ['assets/js/purchasing/form.js'],
        );
        $this->load->template(self::DIR_VIEW. '/_form', $params);
    }

    public function view($id)
    {
        if (!isset($id)) {
            show_404();
        }

        $purchasing = $this->purchasing_model->find_one($id);
        if ($purchasing == NULL) {
            show_404();
        }
        
        $params = array(
            'purchasing' => $purchasing,
            'js_resources' => [],
        );
        $this->load->template(self::DIR_VIEW. '/view', $params);        
    }

    public function create()
    {
        $params = array(
            'payment_methods' => $this->payment_method_model->find_all([], 0, 100),
            'js_resources' => ['assets/js/purchasing/form.js'],
        );
        $this->load->template(self::DIR_VIEW. '/create', $params);
    }

}