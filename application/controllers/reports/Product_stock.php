<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_stock extends Admin_Controller
{
    const DIR_VIEW = 'reports/product_stock';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['category_model','invbalance_model']);
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
        $cat_id = $this->input->get('cat_id');
        $cat_name = $this->_get_category_name($cat_id);
        
        $product_stocks = $this->invbalance_model->find_all([
            'category_id' => $cat_id
        ], $first = 0, $count = 5000);
        $view = $this->load->view('reports/product_stock/print', [
            'product_stocks' => $product_stocks,
            'cat_name' => $cat_name, 
        ], TRUE);

    
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5']);
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

}