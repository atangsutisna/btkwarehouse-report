<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_stock extends Admin_Controller 
{
    const DIR_VIEW = 'store/return';

    public function index()
    {
        $params = array(
            'js_resources' => [
                'assets/js/store/return/index.js',
            ]
        );
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function create()
    {
        $params = array(
            'js_resources' => ['assets/js/store/return/form.js'],
        );
        $this->load->template(self::DIR_VIEW. '/_form', $params);        
    }

    public function view($id)
    {
        $this->load->model('store/return_model');
        $return_stock = $this->return_model->find_one(decrypt_url($id));
        if ($return_stock == NULL) {
            show_404();
        }
        
        $params = array(
            'return_stock' => $return_stock,
            'js_resources' => [],
        );
        $this->load->template(self::DIR_VIEW. '/view', $params);        
    }

}