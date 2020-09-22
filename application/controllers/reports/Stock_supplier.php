<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_supplier extends Admin_Controller
{
    const DIR_VIEW = 'reports/stock_supplier';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Stock_supplier_model']);
    }

    public function index()
    {
        //$categories = $this->category_model->find_all($term = NULL, $first = 0, $count = 5000);
        $params = [
            //'categories' => $categories,
            'js_resources' => [
                'assets/js/reports/product_stock/index.js',
            ]
        ];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

     public function print()
    {
        $title = "Laporan Stock Produk Supplier ";
        $params = array(
            'supplier' => '29' 
        );
        $stock = $this->Stock_supplier_model->find_all($params);
        $view = $this->load->view('reports/stock_supplier/print', [
            'title' => $title,
            'stock' => $stock 
        ], TRUE);

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5']);
        $mpdf->AddPageByArray([
            'margin-left' => 7,
            'margin-right' => 7,
            'margin-top' => 7,
            'margin-bottom' => 7,
        ]);
        $mpdf->writeHTML($view);
        $mpdf->setFooter('Halaman {PAGENO} dari {nbpg}');
        $mpdf->output();
    }
}