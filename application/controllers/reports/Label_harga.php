<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Label_harga extends Admin_Controller
{
    const DIR_VIEW = 'reports/label_harga';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['label_harga_model','category_model']);
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
    	# code...
    	$params = array(
    				'category'=>$cat_id = $this->input->get('cat_id')
    			);
        $css = file_get_contents(base_url("assets/vendor/bootstrap/bootstrap.min.css"));
        
        $product = $this->label_harga_model->find_all($params);
        $view = $this->load->view('reports/label_harga/print', [
            'product' => $product
        ], TRUE);

    
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5','orientation' => 'L']);
        $mpdf->AddPageByArray([
		    'margin-left' => 7,
		    'margin-right' => 7,
		    'margin-top' => 7,
		    'margin-bottom' => 7,
		]);
        $mpdf->writeHTML($view);
        $mpdf->output();
    }
}