<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class Goods_receipt extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Goods_receipt_model', 'goods_receipt');
        $this->load->model('Sequence_model', 'sequence_model');
        $this->load->model('Product_inventory_model', 'Product_inventory');
        $this->load->model('Invbalance_model', 'Invbalance');
        $this->load->model('Mutation_stock_model', 'Mutation_stock');
        $this->load->model('Purchase_order_model', 'purchase_order');

        $this->load->library(['goods_receipt_manager']);
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

    public function index_post()
    {
        $data = $this->post();
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('received_date', 'Tanggal Terima', 'required');
        $this->form_validation->set_rules('receiver_name', 'Penerima', 'required');
        $this->form_validation->set_rules('sales_person_name', 'Pramuniaga', 'required');

        if ($this->form_validation->run() == TRUE) {
            $receiver_id = $this->post('receiver_id');
            $goods_receipt = [
                'purchase_order_id' => $this->post('purchase_order_id'),
                'supplier_id' => $this->post('supplier_id'),
                'supplier_name' => $this->post('supplier_name'),
                'receiver_id' => $receiver_id ?? 0,
                'receiver_name' => $this->post('receiver_name'),
                'received_date' => $this->post('received_date'),
                'sales_person_name' => $this->post('sales_person_name'),
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'goods_receipt_items' => []
            ];

            $raw_goods_receipt_items = $this->post('goods_receipt_items');
            $goods_receipt_item_statuses = [];
            foreach ($raw_goods_receipt_items as $good_receipt_item) {
                $qty_receipt = $good_receipt_item['qty_receipt'];
                $new_qty_receipt = $good_receipt_item['received_qty'];
                $total_qty_receipt = $qty_receipt + $new_qty_receipt;
                $qty_order = $good_receipt_item['qty'];

                if ($total_qty_receipt > $qty_order) {
                    $goods_receipt_item_statuses[] = [
                        'product_id' => $good_receipt_item['product_id'],
                        'status' => 'Melebihin qty order'
                    ];
                }

                $new_item = [
                    'product_id' => $good_receipt_item['product_id'],
                    'product_sku' => '',
                    'product_name' => $good_receipt_item['product_name'],
                    'price' => $good_receipt_item['price'],
                    'qty_order' => $good_receipt_item['qty'],
                    'qty' => $good_receipt_item['received_qty'],
                    'qty_unit_id' => $good_receipt_item['qty_unit_id'],
                    'qty_unit' => $good_receipt_item['qty_unit'],
                    'qty_rasio' => $good_receipt_item['qty_rasio'],
                    'note' => $good_receipt_item['note'],
                    'expiry_date' => $good_receipt_item['expiry_date'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $goods_receipt['goods_receipt_items'][] = $new_item;
                $new_goods_receipt['goods_receipt_items'][] = $new_item;
            }

            if (count($goods_receipt_item_statuses) > 0) {
                $response = [
                    'code' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'Goods receipt item not valid',
                    'errors' => $goods_receipt_item_statuses,
                ];                
                $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            }

            try {
                $goods_receipt_id = $this->goods_receipt_manager->receipt($goods_receipt);
                $added_goods_receipt = $this->goods_receipt->find_one($goods_receipt_id);
                $this->response([
                    'code' => REST_Controller::HTTP_CREATED,
                    'data' => $added_goods_receipt,
                    'goods_receipt_id' => $goods_receipt_id
                ], REST_Controller::HTTP_CREATED);
            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
                $response = [
                    'code' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'Goods receipt item not valid',
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

    public function index_put()
    {
        $data = $this->put();
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('goods_receipt_no', 'Goods receipt no', 'required');
        $this->form_validation->set_rules('received_date', 'Goods receipt date', 'required');
        if ($this->form_validation->run() == TRUE) {
            $goods_receipt_id = $this->put('goods_receipt_id');
            $new_goods_receipt = [
                'supplier_id' => $this->put('supplier_id'),
                'goods_receipt_no' => $this->put('goods_receipt_no'),
                'received_date' => $this->put('received_date'),
                'status' => $this->put('status'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'goods_receipt_items' => []
            ];
            $goods_receipt_items = $this->put('goods_receipt_items');
            foreach ($goods_receipt_items as $good_receipt_item) {
                $new_item = [
                    'product_id' => $good_receipt_item['product_id'],
                    'product_sku' => $good_receipt_item['product_sku'],
                    'product_name' => $good_receipt_item['product_name'],
                    'price' => $good_receipt_item['price'],
                    'qty_order' => $good_receipt_item['qty'],
                    'qty' => $good_receipt_item['received_qty'],
                    'qty_unit' => $good_receipt_item['qty_unit'],
                    'note' => $good_receipt_item['note'],
                    'expiry_date' => $good_receipt_item['expiry_date'],
                    'subtotal' => $good_receipt_item['subtotal'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                for ($idx = 0; $idx < 5; $idx++) {
                    if (array_key_exists($idx, $options)) {
                        $option = $options[$idx];
                        $option_numb = $idx + 1;
                        $new_item['option'. $option_numb] = $option['name'];
                        $new_item['option_value'. $option_numb] = $option['value'];
                    } else {
                        $option_numb = $idx + 1;
                        $new_item['option'. $option_numb] = NULL;
                        $new_item['option_value'. $option_numb] = NULL;
                    }    
                }

                $new_goods_receipt['goods_receipt_items'][] = $new_item;
            }

            $this->goods_receipt->update($goods_receipt_id, 
                $new_goods_receipt);
            $added_goods_receipt = $this->goods_receipt->find_one($goods_receipt_id);
            $this->response([
                'code' => REST_Controller::HTTP_CREATED,
                'data' => $added_goods_receipt
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
    
    public function _find_one($id)
    {
        if (!isset($id)) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);            
        }
        $real_id = decrypt_url($id);
        $goods_receipt = $this->goods_receipt->find_one($real_id, TRUE);
        
        if ($goods_receipt == NULL) {
            $this->response(REST_Controller::HTTP_NOT_FOUND);
        }
        
        $goods_receipt_items = [];
        foreach ($goods_receipt->goods_receipt_items as $goods_receipt_item) {
            $options = [];
            for ($option_numb = 1; $option_numb <= 5; $option_numb++) {
                $option = "option".$option_numb;
                $option_value = "option_value".$option_numb;
                if ($goods_receipt_item->$option != NULL) {
                    array_push($options, [
                        'name' => $goods_receipt_item->$option,
                        'value' => $goods_receipt_item->$option_value,
                    ]);
                }    
            }
            array_push($goods_receipt_items, [
                'product_id' => $goods_receipt_item->product_id,
                'product_sku' => $goods_receipt_item->product_sku,
                'product_name' => $goods_receipt_item->product_name,
                'product_model' => $goods_receipt_item->product_model,
                'cost_of_goods_sold' => $goods_receipt_item->cost_of_goods_sold,
                'price' => $goods_receipt_item->price,
                'qty' => $goods_receipt_item->qty,
                'qty_unit_id' => $goods_receipt_item->qty_unit_id,
                'qty_unit' => $goods_receipt_item->qty_unit,
                'qty_rasio' => $goods_receipt_item->qty_rasio,
                'subtotal' => $goods_receipt_item->subtotal,
                'options' => $options
            ]);
        }

        return [
            'code' => REST_Controller::HTTP_OK,
            'real_id' => $real_id,
            'data' => [
                'goods_receipt_id' => $goods_receipt->goods_receipt_id,
                'goods_receipt_no' => $goods_receipt->goods_receipt_no,
                'received_date' => $goods_receipt->received_date,
                'receiver_name' => $goods_receipt->receiver_name,
                'sales_person_name' => $goods_receipt->sales_person_name,
                'status' => $goods_receipt->status,
                'supplier_id' => $goods_receipt->supplier_id,
                'supplier_name' => $goods_receipt->supplier_name,
                'goods_receipt_items' => $goods_receipt_items
            ]
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
        if (isset($term) && array_key_exists('value', $term) && $term['value'] != '') {
            $criterion['term'] = $term['value'];
        }
        
        $goods_receipt_no = $this->get('goods_receipt_no');
        if (isset($goods_receipt_no) && $goods_receipt_no != NULL && !empty($goods_receipt_no)) {
            $criterion['goods_receipt_no'] = $goods_receipt_no;
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
        if (isset($status) && $status != '') {
            $criterion['status'] = $status;
        }

        $goods_receipts = $this->goods_receipt->find_all($criterion, $first, $count, $order, $direction);
        array_walk($goods_receipts, function(&$goods_receipt){
            $goods_receipt->goods_receipt_id = encrypt_url($goods_receipt->goods_receipt_id);
            $goods_receipt->purchase_order_id = encrypt_url($goods_receipt->purchase_order_id);
        });
        $total_rows = $this->goods_receipt->count_all($criterion);
        $response = array(
            'draw' => isset($draw) ? $draw : 1,
            'recordsTotal' => $total_rows,
            'recordsFiltered' =>  $total_rows,    
            'data' => $goods_receipts,
            'criterion' => $criterion
        ); 

        return $response;        
    }

}