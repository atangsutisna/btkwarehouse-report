<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_product_manager
{
    protected $CI;
    protected $language_id;
    protected $store_id = 0;
    private $defined_columns = [
        'Kode/Barcode',
        'Nama',
        'Deskripsi',
        'Supplier',
        'Kategori',
        'Minimum',
        'Maximum',
        'Minimum Order',
        'Maximum Order',
        'Jenis Produk',
        'Quantity',
        'Rasio',
        'Unit Rasio',
        'Harga Pokok',
        'Harga Offline Satuan',
        'Harga Offline Rasio',
        'Harga Online Satuan',
        'Harga Online Rasio',
        'Gambar'
    ];

	public function __construct($params = []) 
	{
        $this->CI =& get_instance();
        $this->CI->load->library([
            'supplier_manager',
            'weight_class_manager',
            'tax_class_manager',
            'stock_status_manager',
            'product_manager',
            'unit_class_manager',
            'category_manager'
        ]);
        
        /** 
        $stock_status = $this->CI->stock_status_manager->find_or_create('In Stock');
        $stock_statuses['In Stock'] = $stock_status['stock_status_id'];

        $weight_class = $this->CI->weight_class_manager->get_default();
        $weight_classes['default'] = $weight_class['weight_class_id'];

        $tax_class = $this->CI->tax_class_manager->get_default();
        $tax_classes['default'] = $tax_class['tax_class_id'];

        $unit_class = $this->CI->unit_class_manager->get_default();
        $unit_classes['default'] = $unit_class['unit_class_id'];
        **/
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
    protected function _get_language_id()
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

    protected function _read_from_excel($file_path)
    {
        log_message("debug", "Attempting to to read file from : ${file_path}");
        $excel_reader = PHPExcel_IOFactory::createReaderForFile($file_path);
        $excel_product = $excel_reader->load($file_path);
        
        $current_worksheet = $excel_product->getSheet(0);
        $last_row = $current_worksheet->getHighestRow();
        $product = [];
        for ($row = 2; $row <= $last_row; $row++) {
            $product = [
                'model' => $current_worksheet->getCell('A'. $row)->getValue(),
                'name' => $current_worksheet->getCell('B'. $row)->getValue(),
                'description' => $current_worksheet->getCell('C'. $row)->getValue(),
                'supplier' => $current_worksheet->getCell('D'. $row)->getValue(),
                'category' => $current_worksheet->getCell('E'. $row)->getValue(),
                'minimum' => $current_worksheet->getCell('F'. $row)->getValue(),
                'maximum' => $current_worksheet->getCell('G'. $row)->getValue(),
                'minimum_order' => $current_worksheet->getCell('H'. $row)->getValue(),
                'maximum_order' => $current_worksheet->getCell('I'. $row)->getValue(),
                'moving_product_status' => $current_worksheet->getCell('J'. $row)->getValue(),
                'quantity' => $current_worksheet->getCell('K'. $row)->getValue(),
                'rasio' => $current_worksheet->getCell('L'. $row)->getValue(),
                'unit_rasio' => $current_worksheet->getCell('M'. $row)->getValue(),
                'const_of_goods_sold' => $current_worksheet->getCell('N'. $row)->getValue(),
                'offline_price_pcs' => $current_worksheet->getCell('O'. $row)->getValue(),
                'offline_price_rasio' => $current_worksheet->getCell('P'. $row)->getValue(),
                'online_price_pcs' => $current_worksheet->getCell('Q'. $row)->getValue(),
                'online_price_rasio' => $current_worksheet->getCell('R'. $row)->getValue(),
                'image' => $current_worksheet->getCell('S'. $row)->getValue(),
            ];

            $product['meta_title'] = $product['description'] ?? $product['name'];
            $product['meta_description'] = $product['description'] ?? $product['name'];
            $product['meta_keyword'] = $product['description'] ?? $product['name'];

            /** 
            $file_name = $product['model'] .'-'. $product['name'];
            $results = glob($this->app_config['base_image_path'].'/catalog/'.$file_name.'*');  
            if (count($results) > 0) {
                $product['image'] = basename($results[0]);
            }   
            **/       
            if ($product['name'] !== NULL && $product['name'] !== '') {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @return $product
     */
    protected function _transform($raw_product)
    {
        log_message("info", "Find or create stock status In Stock");
        $stock_status = $this->CI->stock_status_manager->find_or_create('In Stock');
        
        log_message("info", "Get default weight class");
        $weight_class = $this->CI->weight_class_manager->get_default();
        
        log_message("info", "Get default tax class");
        $tax_class = $this->CI->tax_class_manager->get_default();

        log_message("info", "Get default unit class");
        $unit_class = $this->CI->unit_class_manager->get_default();

        $product_description['product_variant'] = NULL;
        if (array_key_exists('unit_rasio', $raw_product) 
            && array_key_exists('rasio', $raw_product)
            && $raw_product['unit_rasio'] !== ''
            && $raw_product['unit_rasio'] !== NULL
            && $raw_product['rasio'] !== ''
            && $raw_product['rasio'] !== NULL) {
            
            $unit_rasio = $raw_product['unit_rasio'];
            log_message("info", "Attempting to find or create unit rasio: {$unit_rasio}");
            $unit_rasio_class = $this->CI->unit_class_manager->find_or_create($unit_rasio);
            $product_description['product_variant'] = [
                'model' => $raw_product['model'] . '.1',
                'qty_unit_id' => $unit_rasio_class['unit_measurement_id'],
                'qty_rasio' => $raw_product['rasio'],
                'price' => $raw_product['online_price_rasio'],
                'price_2' => $raw_product['offline_price_rasio'],
                'cost_of_goods_sold' => 0,
                'date_modified' => date('Y-m-d H:i:s')
            ];    
        }

        $base_image_path = 'catalog';
        $online_price_pcs = $raw_product['offline_price_pcs'] + ($raw_product['offline_price_pcs'] * 0.25);
        $product_description['product'] = [
            'model' => remove_whitespace($raw_product['model']), 
            'quantity' => (int) $raw_product['quantity'], 
            'qty_unit_id' => $unit_class['unit_measurement_id'],
            'minimum' => isset($raw_product['minimum']) ? $raw_product['minimum'] : 1, 
            'maximum' => isset($raw_product['maximum']) ? $raw_product['maximum'] : 5, 
            'minimum_order' => isset($raw_product['minimum_order']) ? $raw_product['minimum_order'] : 1, 
            'maximum_order' => isset($raw_product['maximum_order']) ? $raw_product['maximum_order'] : 10, 
            'moving_product_status' => isset($raw_product['moving_product_status']) ? $raw_product['moving_product_status'] : 'normal',
            'subtract' => 0, 
            'stock_status_id' => $stock_status['stock_status_id'], 
            'image' => $raw_product['image'] !== NULL && $raw_product['image'] !== '' ? $base_image_path. '/' .$raw_product['image'] : NULL,
            'shipping' => isset($raw_product['shipping']) ? $raw_product['shipping'] : 1, 
            'price' => $online_price_pcs, 
            'price_2' => $raw_product['offline_price_pcs'] ?? 0, 
            'cost_of_goods_sold' => $raw_product['const_of_goods_sold'] ?? 0,
            'points' => 0, 
            'weight_class_id' => $weight_class['weight_class_id'], 
            'length_class_id' => 0, 
            'status' => 1, 
            'tax_class_id' =>  $tax_class['tax_class_id'], 
            'sort_order' => 0, 
            'multiple_uom' => $product_description['product_variant'] == NULL ? false : true,
            'date_added' => date('Y-m-d H:i:s'), 
            'date_modified' => date('Y-m-d H:i:s')
        ];

        $product_description['description'] = [
            'language_id' => $raw_product['language_id'],
            'name' => $raw_product['name'], 
            'description' => $raw_product['description'] ?? '-',
            'meta_title' => $raw_product['meta_title'] ?? '-', 
            'meta_description' => $raw_product['meta_description'] ?? '-', 
            'meta_keyword' => $raw_product['meta_keyword'] ?? '-'
        ];

        $product_description['supplier_to_product'] = NULL;
        if (array_key_exists('supplier', $raw_product) 
            && $raw_product['supplier'] !== NULL
            && $raw_product['supplier'] !== '') {
            $supplier_name = $raw_product['supplier'];
            log_message("debug", "Attempting to find or create supplier with name {$supplier_name}");
            $product_supplier = $this->CI->supplier_manager->find_or_create($supplier_name);
            $product_description['supplier_to_product'] = [
                'supplier_id' => $product_supplier['supplier_id']
            ];
        }

        $product_description['product_to_category'] = NULL;
        if (array_key_exists('category', $raw_product) 
            && $raw_product['category'] !== NULL
            && $raw_product['category'] !== '') {
            $category_name = $raw_product['category'];
            $category = $this->CI->category_manager->find_or_create($category_name);
            $product_description['product_to_category'] = [
                'category_id' => $category['category_id']
            ];
        }

        log_message("info", "All product has been transformed!");
        return $product_description;
    }
    
    public function copy_from($file_path)
    {
        /** define language id */
        $this->language_id = $this->_get_language_id();
        
        //1. check format file
        log_message("info", "Checking the file format");
        $invalid_columns = $this->check_file_format($file_path);
        if (count($invalid_columns) > 0) {
            throw new Exception("Column invalid");
        }
        
        //2. read product from execel
        log_message("info", "Starting to read file excel");
        $products = $this->_read_from_excel($file_path);
        log_message("info", "Reading success");
        
        //3. set default language id
        array_walk($products, function(&$product) {
            $product['language_id'] = $this->language_id;
        });
        
        //4. transform to product
        log_message("debug", "Starting to transform excel to product");
        $product_descriptions = array_map(function($product) {
            return $this->_transform($product);
        }, $products);
        log_message("info", "Transforming success");

        //5. insert into product
        log_message("debug", "Starting to insert to database");
        $this->CI->product_manager->copy_replace($product_descriptions);
        log_message("debug", "Insert to database success");

        return TRUE;
    }

    public function check_file_format($file_path)
    {
        $excel_reader = PHPExcel_IOFactory::createReaderForFile($file_path);
        $excel_product = $excel_reader->load($file_path);
        $current_worksheet = $excel_product->getSheet(0);
        
        $columns = [];
        array_push($columns, $current_worksheet->getCell('A'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('B'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('C'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('D'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('E'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('F'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('G'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('H'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('I'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('J'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('K'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('L'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('M'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('N'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('O'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('P'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('Q'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('R'. 1)->getValue());
        array_push($columns, $current_worksheet->getCell('S'. 1)->getValue());

        $column_valid = FALSE;
        $column_invalid = [];
        foreach ($this->defined_columns as $idx => $defined_column_name) {
            $input_column_name = $columns[$idx];
            if (strcasecmp($input_column_name, $defined_column_name) > 0 
                || strcasecmp($input_column_name, $defined_column_name) < 0) {
                array_push($column_invalid, $input_column_name);
            }
        }

        return $column_invalid;
    }

    public function get_columns()
    {
        return $this->defined_columns;
    }
}