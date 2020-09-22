<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_supplier extends Admin_Controller
{
    const DIR_VIEW = 'reports/stock_supplier';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Stock_supplier_model','category_model','supplier_model']);
    }

    public function index()
    {
        $supplier = $this->supplier_model->find_all($term = NULL, $first = 0, $count = 5000);
        $categories = $this->category_model->find_all($term = NULL, $first = 0, $count = 5000);
        $params = [
            'supplier' => $supplier,
            'categories' => $categories,
            'js_resources' => [
                'assets/js/reports/stock_supplier/index.js',
            ]
        ];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function print()
    {
        $title = "Laporan Stock Produk Supplier ";
        $cat_id = $this->input->get('cat_id');
        $sup_id = $this->input->get('sup_id');
        $cat_name = $this->_get_category_name($cat_id);
        $sup_name = $this->_get_supplier_name($sup_id);
        $params = array(
            'supplier' => $sup_id,
            'category'=> $cat_id
        );
        $stock = $this->Stock_supplier_model->find_all($params);
        $view = $this->load->view('reports/stock_supplier/print', [
            'title' => $title,
            'stock' => $stock,
            'cat_name' => $cat_name,
            'supplier'=>$sup_name
        ], TRUE);

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5']);
        $mpdf->AddPageByArray([
            'margin-left' => 7,
            'margin-right' => 7,
            'margin-top' => 7,
            'margin-bottom' => 7,
        ]);
        $mpdf->setFooter('Halaman {PAGENO} dari {nbpg}');
        $mpdf->writeHTML($view);
        $mpdf->output();
    }

    protected function _get_category_name($cat_id)
    {
        $category = $this->category_model->find_one($cat_id);
        if ($category == NULL) {
            return NULL;
        }

        return $category->name;
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