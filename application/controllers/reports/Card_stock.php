<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Card_stock extends Admin_Controller
{
    const DIR_VIEW = 'reports/card_stock';

    public function __construct()
    {
        parent::__construct();
        $this->load->model([]);
    }

    public function index()
    {
        $params = [
            'js_resources' => [
                'assets/js/reports/card_stock/index.js',
            ]
        ];
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function print()
    {
        $criterion = [
            'product_id' => $this->input->get('product_id'),
            'start_date' => $this->input->get('start_date'),
            'end_date' => $this->input->get('end_date'),
        ];

        $this->load->model('mutation_stock_model');
        $mutation_stocks = $this->mutation_stock_model->find_all($criterion, $first = 0, $count = 5000, 
            $order_by = 'created_at', $order_direction = 'DESC');

        $product_name = $this->_get_product_name($criterion['product_id']);
        $start_balance = 0;
        $last_balance = 0;
        $view = $this->load->view('reports/stock/print_stock_card', [
            'mutation_stocks' => $mutation_stocks,
            'product_name' => $product_name, 
            'start_balance' => $start_balance,
            'last_balance' => $last_balance,
            'start_date' => $criterion['start_date'],
            'end_date' => $criterion['end_date']
        ], TRUE);
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A5']);
        $mpdf->writeHTML($view);
        $mpdf->output();
    }

    private function _get_product_name($product_id)
    {
        $this->db->select('name');
        $this->db->from('product_description');
        $this->db->where('product_id', $product_id);

        $product = $this->db->get()->row_array();
        if ($product == NULL) {
            return NULL;
        }

        return $product['name'];
    }
}