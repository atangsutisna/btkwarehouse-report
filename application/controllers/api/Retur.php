<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class Retur extends REST_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Retur_model', 'retur');
        $this->load->model('Sequence_model', 'sequence_model');
    }

    function index_get($id = NULL)
    {        
        if ($id != NULL) {
            $response = $this->_find_one($id);
            $this->response($response, REST_Controller::HTTP_OK);
        }

        $response = $this->_find_all();
        $this->response($response, REST_Controller::HTTP_OK);
    }

    function index_post() 
    {
        $data = $this->post();
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('retur_date', 'Tanggal', 'required');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('supplier_name', 'Supplier', 'required');

        if ($this->form_validation->run() == TRUE) {
            $retur = [
                'supplier_id' => $this->post('supplier_id'),
                'supplier_name' => $this->post('supplier_name'),
                'purchase_order_id' => $this->post('purchase_order_id'),
                'retur_date' => $this->post('retur_date'),
                'created_at' => date('Y-m-d H:i:s'),
                'update_at' => date('Y-m-d H:i:s'),
                'return_items' => [],
                'status' => 'draft'
            ];

            $return_items = $this->post('return_items');
            foreach ($return_items as $return_item) {
                $retur['return_items'][] = [
                    'product_id' => $return_item['product_id'],
                    'product_model' => $return_item['product_model'],
                    'product_name' => $return_item['product_name'],
                    'qty' => $return_item['qty'],
                    'qty_unit_id' => $return_item['qty_unit_id'],
                    'note' =>  $return_item['note'],
                ];
            }
            
            try {
                $retur_id = $this->retur->insert($retur);
                $added_retur = $this->retur->find_one($retur_id);
                $this->response([
                    'code' => REST_Controller::HTTP_CREATED,
                    'data' => $added_retur,
                    'retur_id' => $retur_id
                ], REST_Controller::HTTP_CREATED);
            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
                $response = [
                    'code' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'Retur item not valid',
                    'errors' => $errors,
                ];                
                $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
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

    function index_put() 
    {
        $data = $this->put();
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('created_at', 'Tanggal', 'required');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('supplier_name', 'Supplier', 'required');

        if ($this->form_validation->run() == TRUE) {
            $retur_id = $this->put('retur_id');
            $retur = [
                'retur_date' => $this->put('created_at'),
                'purchase_order_id' => $this->put('purchase_order_id'),
                'status' => $this->put('status'),
                'updated_at' => date('Y-m-d H:i:s'),
                'return_items' => [],
                'total' => $this->put('total')
            ];

            $return_items = $this->put('order_details');
            foreach ($return_items as $order_item) {
                $item = [
                    'retur_id' => $retur_id,
                    'product_id' => $order_item['product_id'],
                    'product_sku' => $order_item['product_sku'],
                    'product_name' => $order_item['product_name'],
                    'price' => $order_item['price'],
                    'qty' => $order_item['qty'],
                    'qty_unit' => $order_item['qty_unit'],
                    'subtotal' => $order_item['subtotal'],
                ];

                $options = [];
                if (array_key_exists('options', $order_item)) {
                    $options = $order_item['options'];
                }
                
                for ($idx = 0; $idx < 5; $idx++) {
                    if (array_key_exists($idx, $options)) {
                        $option = $options[$idx];
                        $option_numb = $idx + 1;
                        $item['option'. $option_numb] = $option['name'];
                        $item['option_value'. $option_numb] = $option['value'];
                    } else {
                        $option_numb = $idx + 1;
                        $item['option'. $option_numb] = NULL;
                        $item['option_value'. $option_numb] = NULL;
                    }    
                }

                array_push($retur['return_items'], $item);
            }
            //var_dump($retur['return_items']);
            $this->retur->update($retur_id, $retur);
            $added_retur = $this->retur->find_one($retur_id);

            $retur['retur_id'] = $retur_id;
            $retur['retur_no'] = $added_retur->retur_no;
            $this->response([
                'code' => REST_Controller::HTTP_OK,
                'data' => $retur
            ], REST_Controller::HTTP_OK);
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

    function _find_one($id)
    {
        $retur = $this->retur->find_one($id, TRUE);
        if ($retur == NULL) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);
        }

        $order_details = [];
        foreach ($retur->return_items as $order_item) {
            /*$options = [];
            for ($option_numb = 1; $option_numb <= 5; $option_numb++) {
                $option = "option".$option_numb;
                $option_value = "option_value".$option_numb;
                if ($order_item->$option != NULL) {
                    array_push($options, [
                        'name' => $order_item->$option,
                        'value' => $order_item->$option_value,
                    ]);
                }    
            }*/
            array_push($order_details, [
                'product_id' => $order_item->product_id,
                'product_sku' => $order_item->product_sku,
                'product_name' => $order_item->product_name,
                'price' => $order_item->price,
                'qty' => $order_item->qty,
                'qty_unit' => $order_item->qty_unit,
                'subtotal' => $order_item->subtotal/*,
                'options' => $options*/
            ]);
        }

        return [
            'code' => REST_Controller::HTTP_OK,
            'data' => [
                'retur_id' => $retur->retur_id,
                'retur_no' => $retur->retur_no,
                'retur_date' => $retur->retur_date,
                'supplier_id' => $retur->supplier_id,
                'supplier_name' => $retur->supplier_name,
                'order_details' => $order_details
            ]
        ];
    }

    function _find_all()
    {
        $draw = $this->input->get('draw');
        $term = $this->input->get('search');
        $first = $this->input->get('start');
        $count = $this->input->get('length');
        $columns = $this->input->get('columns');
        $order_idx = $this->input->get('order')[0]['column'];
        $order = $columns[$order_idx]['data'];
        $direction = $this->input->get('order')[0]['dir'];

        $and_criterion = [];
        $retur_no = $this->get('retur_no');
        if (isset($retur_no) && $retur_no != NULL && !empty($retur_no)) {
            $and_criterion['retur_no'] = $retur_no;
        }

        $supplier_id = $this->get('supplier_id');
        if (isset($supplier_id) && $supplier_id != NULL) {
            $and_criterion['supplier_id'] = $supplier_id;
        }

        $retur_orders = $this->retur->find_all($and_criterion, $first, $count, $order, $direction);
        $total_rows = $this->retur->count_all($and_criterion);
        $response = array(
            'draw' => isset($draw) ? $draw : 1,
            'recordsTotal' => $total_rows,
            'recordsFiltered' =>  $total_rows,    
            'and_criterion' => $and_criterion,
            'data' => $retur_orders
        ); 

        return $response;
    }

    function index_delete($id)
    {
        if (!isset($id)) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);
        }

        $retur = $this->retur->find_one($id, FALSE);
        if ($retur == NULL) {
            $this->response([
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'Cannot find purchase-order with id '. $id
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if ($retur->status == 'complete') {
            $this->response([
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Purchase-order with id '. $id . ' can be modified. The status is complete'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->retur->update_status($id, 'void');
        $this->response(REST_Controller::HTTP_OK);
    }

}