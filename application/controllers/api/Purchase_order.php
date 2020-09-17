<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class Purchase_order extends REST_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Purchase_order_model', 'purchase_order');
        $this->load->model('Sequence_model', 'sequence_model');

        $this->load->config('btkcommerce');
        $this->commerce_config = $this->config->item('commerce');
        $this->load->helper('utf8');
        $this->load->library('image_manager');
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

        $this->form_validation->set_rules('created_at', 'Tanggal', 'required');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('supplier_name', 'Supplier', 'required');

        if ($this->form_validation->run() == TRUE) {
            $purchase_order = [
                'supplier_id' => $this->post('supplier_id'),
                'supplier_name' => $this->post('supplier_name'),
                'purchase_order_no' => $this->purchase_order->get_next_id(),
                'purchase_order_date' => $this->post('created_at'),
                'status' => $this->post('status'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'order_items' => [],
                'total' => $this->post('total')
            ];

            $order_items = $this->post('order_details');
            foreach ($order_items as $order_item) {
                $options = $order_item['options'];
                $item = [
                    'product_id' => $order_item['product_id'],
                    'product_name' => $order_item['product_name'],
                    'price' => $order_item['price'],
                    'qty' => $order_item['qty'],
                    'qty_unit' => $order_item['qty_unit'],
                    'subtotal' => $order_item['subtotal'],
                ];
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

                $purchase_order['order_items'][] = $item;
            }

            $purchase_order_id = $this->purchase_order->insert($purchase_order);
            $added_purchase_order = $this->purchase_order->find_one($purchase_order_id);

            $purchase_order['purchase_order_id'] = $purchase_order_id;
            $purchase_order['purchase_order_no'] = $added_purchase_order->purchase_order_no;
            $this->response([
                'code' => REST_Controller::HTTP_CREATED,
                'data' => $purchase_order
            ], REST_Controller::HTTP_CREATED);
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
            $purchase_order_id = $this->put('purchase_order_id');
            $purchase_order = [
                'purchase_order_date' => $this->put('created_at'),
                'status' => $this->put('status'),
                'updated_at' => date('Y-m-d H:i:s'),
                'order_items' => [],
                'total' => $this->put('total')
            ];

            $order_items = $this->put('order_details');
            foreach ($order_items as $order_item) {
                $item = [
                    'purchase_order_item_id' => $order_item['purchase_order_item_id'],
                    'purchase_order_id' => $purchase_order_id,
                    'product_id' => $order_item['product_id'],
                    'product_name' => $order_item['product_name'],
                    'price' => $order_item['price'],
                    'qty' => $order_item['qty'],
                    'qty_unit_id' => $order_item['qty_unit_id'],
                    'note' => $order_item['note'],
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

                array_push($purchase_order['order_items'], $item);
            }
            //var_dump($purchase_order['order_items']);
            $this->purchase_order->update($purchase_order_id, $purchase_order);
            $added_purchase_order = $this->purchase_order->find_one($purchase_order_id);

            $purchase_order['purchase_order_id'] = $purchase_order_id;
            $purchase_order['purchase_order_no'] = $added_purchase_order->purchase_order_no;
            $this->response([
                'code' => REST_Controller::HTTP_OK,
                'data' => $purchase_order
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
        $purchase_order = $this->purchase_order->find_one($id, TRUE);
        if ($purchase_order == NULL) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);
        }

        $order_details = [];
        foreach ($purchase_order->order_items as $order_item) {
            $options = [];
            for ($option_numb = 1; $option_numb <= 5; $option_numb++) {
                $option = "option".$option_numb;
                $option_value = "option_value".$option_numb;
                if ($order_item->$option != NULL) {
                    array_push($options, [
                        'name' => $order_item->$option,
                        'value' => $order_item->$option_value,
                    ]);
                }    
            }
            array_push($order_details, [
                'purchase_order_item_id' => $order_item->purchase_order_item_id,
                'product_id' => $order_item->product_id,
                'product_name' => $order_item->product_name,
                'product_model' => $order_item->product_model,
                'product_image' => $this->image_manager->get_image_uri($order_item->product_image) ?? $this->image_manager->get_default_image_uri(),
                'price' => $order_item->price,
                'qty' => $order_item->qty,
                'qty_receipt' => $order_item->qty_receipt,
                'qty_balance' => $order_item->qty_balance,
                'qty_unit_id' => $order_item->qty_unit_id,
                'qty_unit' => $order_item->qty_unit,
                'qty_rasio' => $order_item->qty_rasio,
                'note' => $order_item->note,
                'subtotal' => $order_item->subtotal,
                'options' => $options
            ]);
        }

        return [
            'code' => REST_Controller::HTTP_OK,
            'data' => [
                'purchase_order_id' => $purchase_order->purchase_order_id,
                'purchase_order_no' => $purchase_order->purchase_order_no,
                'purchase_order_date' => $purchase_order->purchase_order_date,
                'updated_at' => $purchase_order->updated_at,
                'status' => $purchase_order->status,
                'supplier_id' => $purchase_order->supplier_id,
                'supplier_name' => $purchase_order->supplier_name,
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

        $criterion = [];
        $purchase_order_no = $this->get('purchase_order_no');
        if (isset($purchase_order_no) && $purchase_order_no != NULL && !empty($purchase_order_no)) {
            $criterion['purchase_order_no'] = $purchase_order_no;
        }

        $supplier_id = $this->get('supplier_id');
        if (isset($supplier_id) && $supplier_id != NULL) {
            $criterion['supplier_id'] = $supplier_id;
        }

        $start_date = $this->get('start_date');
        if (isset($start_date) && $start_date != '') {
            $criterion['start_date'] = $start_date;
        }

        $end_date = $this->get('end_date');
        if (isset($start_date) && $end_date != '') {
            $criterion['end_date'] = $end_date;
        }

        $status = $this->get('status');
        if (isset($status) && $status != NULL) {
            $statuses = explode(",", $status);
            $criterion['status'] = $statuses;
        }        

        $purchase_orders = $this->purchase_order->find_all($criterion, $first, $count, $order, $direction);
        $total_rows = $this->purchase_order->count_all($criterion);
        $response = array(
            'draw' => isset($draw) ? $draw : 1,
            'recordsTotal' => $total_rows,
            'recordsFiltered' =>  $total_rows,    
            'criterion' => $criterion,
            'data' => $purchase_orders,
        ); 

        return $response;
    }

    function index_delete($id)
    {
        if (!isset($id)) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);
        }

        $purchase_order = $this->purchase_order->find_one($id, FALSE);
        if ($purchase_order == NULL) {
            $this->response([
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'Cannot find purchase-order with id '. $id
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if ($purchase_order->status == 'complete') {
            $this->response([
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Purchase-order with id '. $id . ' can be modified. The status is complete'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->purchase_order->update_status($id, 'void');
        $this->response(REST_Controller::HTTP_OK);
    }

}