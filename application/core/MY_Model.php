<?php
class MY_Model extends CI_Model
{
    protected $primary_key;
    protected $table_name;
    protected $store_id = 0;
    
    public function __construct($table_name, $primary_key)
    {
        parent::__construct();
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
    }

    public function insert($entity)
    {
        $this->db->insert($this->table_name, $entity);
        $last_insert_id = $this->db->insert_id();

        return $last_insert_id;
    }

    public function modify($id, $entity)
    {
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table_name, $entity);
    }

    public function remove($id)
    {
        $this->db->where($primary_key, $id);
        $this->db->delete($this->table_name);
    }

    public function find_one($id)
    {   
        $query = $this->db->get_where($this->table_name, [
            $this->primary_key => $id
        ]);

        return $query->row();
    }

    public function get_language_id()
    {
        $this->db->select('store_id, key, value as setting_value');
        $this->db->from('setting');
        $this->db->where('store_id', $this->store_id);
        $this->db->where('key', 'config_language');
        $query_setting = $this->db->get();
        if ($query_setting->row() == NULL) {
            return NULL;
        }

        $this->db->select('language_id');
        $this->db->from('language');
        $this->db->where('code', $query_setting->row()->setting_value);
        $query_language = $this->db->get();
        if ($query_language->row() == NULL) {
            return NULL;
        }

        return $query_language->row()->language_id;
    }    
}

//FIXME: deprecated
class Commerce_model extends CI_Model {
    protected $db;
    

    public function __construct() 
    {
        $this->db = $this->load->database('btk_commerce', TRUE);
    }

}

class Storagebin_model extends CI_Model {

    protected function get_inventory_balance($invbal_id)
    {
        $invbal = $this->db->get_where('inventory_balance', [
            'inventory_balance_id' => $invbal_id
        ])->row();

        return $invbal;
    }

    protected function get_or_create_product_inventory($product_id)
    {
        $product_inventory = $this->get_product_inventory($product_id);
        if ($product_inventory != NULL) {
            return $product_inventory;
        }

        $this->create_product_inventory($product_id);
        return $this->get_product_inventory($product_id);
    }

    protected function get_product_inventory($product_id)
    {
        return $this->db->get_where('inventory_balance', [
            'product_id' => $product_id
        ])->row_array();
    }

    protected function create_product_inventory($product_id)
    {
        $this->db->select('qty_unit_id');
        $this->db->from('product');
        $this->db->where('product_id', $product_id);
        $query_product = $this->db->get()->row();

        if ($query_product == NULL) {
            throw new Exception("Invalid product");
        }

        $identity = $this->ion_auth->user()->row();
        $product_inventory = [
            'product_id' => $product_id,
            'storagebin1' => 0,
            'storagebin2' => 0,
            'qty' => 0,
            'qty_unit_id' => $query_product->qty_unit_id,
            'status' => 1,
            'created_by' => $identity->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('inventory_balance', $product_inventory);
    }

    protected function get_product_inventories($product_ids)
    {
        if (!is_array($product_ids)) {
            throw new Exception("Product ids must be an array");
        }

        $product_inventories = [];
        foreach ($product_ids as $product_id) {
            $product_inventory = $this->get_or_create_product_inventory($product_id);
            array_push($product_inventories, $product_inventory);
        }

        return $product_inventories;
    }

}