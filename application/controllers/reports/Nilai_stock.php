<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nilai_stock extends Admin_Controller
{
    const DIR_VIEW = 'reports/nilai_stock';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['category_model','Nilai_stock_model']);
    }

    public function index()
    {
        $categories = $this->category_model->find_all($term = NULL, $first = 0, $count = 5000);
        $params = [
            'categories' => $categories,
            'js_resources' => [
                'assets/js/reports/product_stock/index.js',
            ]
        ];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function print()
    {
        $title = "Laporan Nilai Stock ";
        $cat_id = $this->input->get('cat_id');
        $cat_name = $this->_get_category_name($cat_id);
        $type = $this->input->get('type_data');
        if ($type==1) {
            $type="Harga Jual";
        }elseif ($type==2) {
            # code...
            $type="Harga Pokok";
        }
        $title .= "Per ".$type;
        $param = array(
            'category' => $cat_id, 
            'type' => $this->input->get('type_data') 
        );
        $stock = $this->Nilai_stock_model->find_all($param);

        $view = $this->load->view('reports/nilai_stock/print', [
            'title' => $title,
            'cat_name' => $cat_name, 
            'type'=> $type,
            'stock'=>$stock
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

    protected function _get_category_name($cat_id)
    {
        $category = $this->category_model->find_one($cat_id);
        if ($category == NULL) {
            return NULL;
        }

        return $category->name;
    }
}