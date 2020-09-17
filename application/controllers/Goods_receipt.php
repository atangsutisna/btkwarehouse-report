<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_receipt extends Admin_Controller 
{
    const DIR_VIEW = 'goods_receipt';

    public function index()
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'read_goods_receipt')) {
            redirect('forbidden', 'refresh');
        }

        $params = array(
            'js_resources' => [
                'assets/js/goods_receipt/index.js',
            ]
        );
        $this->load->template(self::DIR_VIEW. '/index', $params);
    }

    public function new_form()
    {
        $identity = get_identity(); 
        $user_id = $identity->id;
        if (!$this->rbac_manager->has_permission($user_id, 'create_goods_receipt')) {
            redirect('forbidden', 'refresh');
        }

        $this->load->model('Sequence_model', 'sequence');
        $params = array(
            'identity' => $identity,
            'js_resources' => ['assets/js/goods_receipt/form.js'],
        );
        $this->load->template(self::DIR_VIEW. '/_form', $params);
    }

    public function view($id)
    {
        $user_id = get_identity()->id;
        if (!$this->rbac_manager->has_permission($user_id, 'update_goods_receipt')) {
            redirect('forbidden', 'refresh');
        }

        if (!isset($id)) {
            show_404();
        }

        $this->load->model('Goods_receipt_model', 'goods_receipt');
        $goods_receipt = $this->goods_receipt->find_one(decrypt_url($id));
        if ($goods_receipt == NULL) {
            show_404();
        }
        
        $goods_receipt_items = $this->goods_receipt->find_items(decrypt_url($id));
        $params = array(
            'goods_receipt' => $goods_receipt,
            'goods_receipt_items' => $goods_receipt_items,
            'js_resources' => [],
        );
        $this->load->template(self::DIR_VIEW. '/view', $params);        
    }

}