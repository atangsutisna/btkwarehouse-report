<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_manager
{
    protected $CI;
    protected $store_id = 0;

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
    }

    /**
     * @return dbObject
     */
    protected function _get_db()
    {
        return $this->CI->db;
    }

    /**
     * @return lanuage_id int
     */
    public function _get_language_id()
    {
        $this->_get_db()->select('store_id, key, value as setting_value');
        $this->_get_db()->from('setting');
        $this->_get_db()->where('store_id', $this->store_id);
        $this->_get_db()->where('key', 'config_language');
        $query_setting = $this->_get_db()->get();
        if ($query_setting->row() == NULL) {
            return NULL;
        }

        $this->_get_db()->select('language_id');
        $this->_get_db()->from('language');
        $this->_get_db()->where('code', $query_setting->row()->setting_value);
        $query_language = $this->_get_db()->get();
        if ($query_language->row() == NULL) {
            return NULL;
        }

        return $query_language->row()->language_id;
    }    

    /**
     * @param array of category
     * for example ['name' => 'string','description' => 'string', 'meta_title' => 'string', 'meta_keyword' => 'string', 'meta_description' => 'string']
     */
    protected function create($category)
    {
        if (!array_key_exists('name', $category)) {
            throw new Exception('category name is required');
        }

        if (!array_key_exists('description', $category)) {
            $category['description'] = $category['name'];
        }

        $this->_get_db()->insert('category', [
            'parent_id' => 0,
            'top' => 0,
            'column' => 1,
            'sort_order' => 0,
            'status' => 1,
            'date_added' => date('Y-m-d H:i:s'),
            'date_modified' => date('Y-m-d H:i:s'),
        ]);

        $category_id = $this->_get_db()->insert_id();
        $category['category_id'] = $category_id;

        $language_id = $this->_get_language_id();
        $this->_get_db()->insert('category_description', [
            'category_id' => $category_id,
            'language_id' => $language_id,
            'name' => $category['name'],
            'description' => $category['description'],
            'meta_title' => '',
            'meta_description' => '',
            'meta_keyword' => '',
        ]);

        $this->_get_db()->insert('category_path', [
            'category_id' => $category_id,
            'path_id' => $category_id,
            'level' => 0
        ]);        

        $category = $this->_get_db()->get_where('category_description', [
            'category_id' => $category_id,
            'language_id' => $language_id,
        ])->row_array();

        return $category;
    }

    /**
     * @param $category_name
     * @return array of category ['name' => 'string','description' => 'string', 'sort_order' => int]
     */
    public function find_or_create($category_name)
    {
        $language_id = $this->_get_language_id();

        $this->_get_db()->where('name', $category_name);
        $this->_get_db()->where('language_id', $language_id);
        $category = $this->_get_db()->get('category_description')->row_array();
        if ($category != NULL) {
            return $category;
        }
        
        $category = [
            'name' => $category_name,
            'description' => $category_name,
        ];
        return $this->create($category);
    }

    /**
     * @param array of ['product_id' => int, 'category_id' => int]
     */
    public function add_product($product_to_category)
    {
        $product_category = $this->_get_db()->get_where('product_to_category',[
            'product_id' => $product_to_category['product_id'],
            'category_id' => $product_to_category['category_id']
        ])->row_array();
        if ($product_category == NULL) {
            $this->_get_db()->insert('product_to_category',[
                'product_id' => $product_to_category['product_id'],
                'category_id' => $product_to_category['category_id']
            ]);
        }

    }



}