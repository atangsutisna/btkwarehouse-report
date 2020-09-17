<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasing extends Admin_Controller
{
    const DIR_VIEW = 'reports/purchasing';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['purchasing_model','supplier_model']);
    }

    public function index()
    {
        $params = [
            'suppliers' => $this->supplier_model->find_all($term = NULL, 0, 5000),
            'js_resources' => [
                'assets/js/reports/purchasing/index.js',
            ]
        ];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function generate()
    {
        $supplier_id = $this->input->post('supplier_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $this->load->helper('purchasing');

        $criterion = [];
        if (isset($supplier_id)) {
            $criterion['supplier_id'] = $supplier_id;
        }
        $suppliers = $this->_get_suppliers($criterion);
        $view = $this->load->view(self::DIR_VIEW. '/print', [
            'suppliers' => $suppliers,
            'start_date' => $start_date ?? NULL,
            'end_date' => $end_date ?? NULL
        ], TRUE);
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->AddPage('L');
        $mpdf->writeHTML($view);
        $mpdf->output();
    }

    private function _get_suppliers($criterion = [])
    {
        $this->db->select('supplier_id, supplier_name');
        $this->db->from('purchasing');
        $this->db->distinct();

        if (array_key_exists('supplier_id', $criterion)) {
            $this->db->where('supplier_id', $criterion['supplier_id']);
        }
        
        return $this->db->get()->result();
    }
}