<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language_model extends CI_Model
{
    protected $store_id = 0;

    public function get_default_language_id()
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