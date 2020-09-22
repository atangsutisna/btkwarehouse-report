<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_pembelian extends Admin_Controller
{
    const DIR_VIEW = 'reports/retur_pembelian';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Retur_pembelian_model','supplier_model']);
    }

    public function index()
    {
        $supplier = $this->supplier_model->find_all($term = NULL, $first = 0, $count = 5000);
        $params = [
            'supplier' => $supplier,
            'js_resources' => [
                'assets/js/reports/stock_supplier/index.js',
            ]
        ];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

     public function print()
    {
        $title = "Laporan Retur Pembelian";
        $sup_id = $this->input->get('sup_id');
        $sup_name = $this->_get_supplier_name($sup_id);
        $params = array(
            'supplier' => $sup_id
        );
        if($sup_name==''){
            $sup_name="Semua Supplier";
        }
        $stock = $this->Retur_pembelian_model->find_all($params);
        $view = $this->load->view('reports/retur_pembelian/print', [
            'title' => $title,
            'stock' => $stock,
            'supplier'=>$sup_name
        ], TRUE);

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5']);
        $mpdf->AddPageByArray([
            'margin-left' => 7,
            'margin-right' => 7,
            'margin-top' => 20,
            'margin-bottom' => 15,
            'suppress' => 'off'
        ]);
         $mpdf->setFooter('Halaman {PAGENO} dari {nbpg}');
        $mpdf->writeHTML($view);
        $mpdf->output();
    }

    protected function _get_supplier_name($cat_id)
    {
        $supplier = $this->supplier_model->find_one($cat_id);
        if ($supplier == NULL) {
            return NULL;
        }

        return $supplier->name;
    }
}