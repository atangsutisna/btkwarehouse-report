<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class Purchasing extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchasing_model');
    }

    public function index_get($id = NULL)
    {        
        if ($id != NULL) {
            $response = $this->_find_one($id);
            $this->response($response, REST_Controller::HTTP_OK);
        }

        $response = $this->_find_all();
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function _find_one($id)
    {
        if (!isset($id)) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);            
        }

        $real_id = decrypt_url($id);
        $move_history = $this->purchasing_model->find_one($real_id, TRUE);
        if ($move_history == NULL) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);
        }

        return [
            'code' => REST_Controller::HTTP_OK,
            'data' => $move_history
        ];        
	}

    public function _find_all()
    {
        $draw = $this->input->get('draw');
        $term = $this->input->get('search');
        $first = $this->input->get('start');
        $count = $this->input->get('length');
        $columns = $this->input->get('columns');
        $order_idx = $this->input->get('order')[0]['column'];
        $order = $columns[$order_idx]['data'];
        $direction = $this->input->get('order')[0]['dir'];

        $criterion = [];
        $start_date = $this->get('start_date');
        if (isset($start_date) && $start_date != '') {
            $criterion['start_date'] = $start_date;
        }

        $end_date = $this->get('end_date');
        if (isset($start_date) && $end_date != '') {
            $criterion['end_date'] = $end_date;
        }

        $supplier_id = $this->get('supplier_id');
        if (isset($supplier_id) && $supplier_id != '') {
            $criterion['supplier_id'] = $supplier_id;
        }
        
        $purchases = $this->purchasing_model->find_all($criterion, $first, $count, $order, $direction);
        $total_rows = $this->purchasing_model->count_all($criterion);
        $response = array(
            'draw' => isset($draw) ? $draw : 1,
            'recordsTotal' => $total_rows,
            'recordsFiltered' =>  $total_rows,    
            'data' => $purchases,
            'criterion' => $criterion
        ); 

        return $response;        
    }

    public function index_post() 
    {
        $data = $this->post();
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('goods_receipt_id', 'Goods Receipt ID', 'required');
        $this->form_validation->set_rules('goods_receipt_no', 'Goods Receipt No', 'required');
        $this->form_validation->set_rules('supplier_id', 'Supplier ID', 'required');
        $this->form_validation->set_rules('supplier_name', 'Supplier Name', 'required');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required');
        $this->form_validation->set_rules('invoice_date', 'Invoice Date', 'required');
        $this->form_validation->set_rules('receive_date', 'Receive Date', 'required');
        $this->form_validation->set_rules('subtotal', 'Subtotal', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $purchasing = [
                'goods_receipt_id' => $this->post('goods_receipt_id'),
                'goods_receipt_no' => $this->post('goods_receipt_no'),
                'supplier_id' => $this->post('supplier_id'),
                'supplier_name' => $this->post('supplier_name'),
                'payment_method' => $this->post('payment_method'),
                'due_date' => $this->post('due_date'),
                'invoice_date' => $this->post('invoice_date'),
                'receive_date' => $this->post('receive_date'),
                'subtotal' => $this->post('subtotal'),
                'taxable' => $this->post('taxable'),
                'tax' => $this->post('tax'),
                'discount' => $this->post('discount'),
                'discount_type' => $this->post('discount_type'),
                'total' => $this->post('total'),
                'note' => $this->post('note'),
                'purchasing_items' => [],
                'created_by' => get_identity()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];   

            $purchasing_items = $this->post('purchasing_items');
            foreach ($purchasing_items as $purchasing_item) {
                $purchasing['purchasing_items'][] = [
                    'product_id' => $purchasing_item['product_id'],
                    'product_name' => $purchasing_item['product_name'],
                    'product_model' => $purchasing_item['product_model'],
                    'price' => $purchasing_item['price'],
                    'discount' => $purchasing_item['discount'],
                    'qty' => $purchasing_item['qty'],
                    'qty_unit_id' => $purchasing_item['qty_unit_id'],
                    'qty_rasio' => $purchasing_item['qty_rasio'],
                    'finalprice' => $purchasing_item['finalprice'],
                    'subtotal' => $purchasing_item['subtotal'],
                    /** offline price */
                    'offline_margin' => $purchasing_item['offline_margin'],
                    'offline_price_pcs' => $purchasing_item['offline_price_pcs'],
                    'offline_price_rasio' => $purchasing_item['offline_price_rasio'],
                    /** online price */
                    'online_margin' => $purchasing_item['online_margin'],
                    'online_price_pcs' => $purchasing_item['online_price_pcs'],
                    'online_price_rasio' => $purchasing_item['online_price_rasio'],
                ];
            }

            $this->_do_insert($purchasing);
        } else {
            $errors = [];
            $validation_errors = $this->form_validation->error_array();
            array_walk($validation_errors, function($value, $key) use (&$errors) {
                array_push($errors, $value);
            });

            $response = [
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Data tidak dapat disimpan',
                'errors' => $errors
            ];
            
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);            
        }

    }

    protected function _do_insert($purchasing)
    {
        try {
            $purchasing_id = $this->purchasing_model->insert($purchasing);
            //$purchasing = $this->purchasing_model->find_one($purchasing_id);
            $this->response([
                'code' => REST_Controller::HTTP_CREATED,
                'data' => $purchasing_id
            ], REST_Controller::HTTP_CREATED);
        } catch (Exception $ex) {
            log_message('error', 'Cannot move to Etalase, qty limit exeeced');
            $errors[] = $ex->getMessage();
            $response = [
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Data tidak dapat disimpan',
                'errors' => $errors
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);            
        }
    }


    public function do_print()
    {
        $all_purchasing = $this->purchasing_model->find_all();
    }
}