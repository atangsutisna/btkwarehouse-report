<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class Payment_method extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['payment_method_model']);
    }

    public function index_get($id = NULL)
    {
        if ($id !== NULL) {
            $response = $this->_find_one($id);
            $this->response($response, REST_Controller::HTTP_OK);
        }

        $response = $this->_find_all();
        $this->response($response, REST_Controller::HTTP_OK);
    }


    protected function _find_all()
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
        $payment_methods = $this->payment_method_model->find_all($criterion, $first, $count, $order, $direction);
        $total_rows = $this->payment_method_model->count_all($criterion);

        $current_uid = $this->session->userdata('uid');
        $response = array(
            'draw' => isset($draw) ? $draw : 1,
            'recordsTotal' => $total_rows,
            'recordsFiltered' =>  $total_rows,
            'data' => $payment_methods
        );

        return $response;
    }

    protected function _find_one($id)
    {
        $payment_method = $this->payment_method_model->find_one($id);
        if ($payment_method == NULL) {
            $response = [
                'code' => 404,
                'message' => 'Cannot find payment method with id '. $id
            ];

            return $response;
        }

        return ['code' => 200, 'data' => $payment_methods];
    }

    public function index_delete($id = NULL)
    {
        if ($id == NULL) {
            $response = [
                'code' => 404,
                'message' => 'Cannot find payment method with id '. $id
            ];
            
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->payment_method_model->modify($id, [
            'status' => 'void'
        ]);
        $this->response([
            'code' => 200,
            'message' => 'Data sudah dihapus'
        ], REST_Controller::HTTP_OK);
    }


}