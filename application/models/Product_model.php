<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends MY_Model
{
    const TBL_REFERENCE = 'product';
    const PRIMARY_KEY = 'product_id';

    private $sort_data = array(
        'name' => 'product_description.name',
        'price' => 'product.price',
        'date_modified' => 'product.date_modified',
    );

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    function find_all($criterion = array(), $first = 0, $count = 25, $sort = 'product.date_modified', $order = 'DESC')
    {
        $language_id = (int) $this->get_language_id();
        $this->db->select('product.*, product_description.name, unit_measurement.symbol AS qty_unit');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where('product.status', 1);

        if (array_key_exists('model', $criterion) && !empty($criterion['model'])) {
            $this->db->where('model', $criterion['model']);
        }

        if (array_key_exists('quantity', $criterion) && !empty($criterion['quantity'])) {
            $this->db->where('quantity', $criterion['quantity']);
        }

        if (array_key_exists('price', $criterion) && !empty($criterion['price'])) {
            $this->db->where('price', $criterion['price']);
        }

        if (array_key_exists('minimum', $criterion) && !empty($criterion['minimum'])) {
            $this->db->where('minimum', $criterion['minimum']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term'], 'after');
            $this->db->group_end();
        }

        $this->db->group_by('product.product_id');
        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by(array_key_exists($sort, $this->sort_data) ? $this->sort_data[$sort] : 'product.date_modified', $order);
        $query_product = $this->db->get();

        $products = $query_product->result();
        //query product images
        $product_ids = array_map(function($product){
            return $product->product_id;
        }, $products);

        return $products;
    }

    public function count_all($criterion = array())
    {
        $language_id = (int) $this->get_language_id();
        $this->db->select('COUNT(DISTINCT {PRE}product.product_id) AS total_rows');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where('product.status', 1);
        if (array_key_exists('model', $criterion) && !empty($criterion['model'])) {
            $this->db->where('model', $criterion['model']);
        }

        if (array_key_exists('quantity', $criterion) && !empty($criterion['quantity'])) {
            $this->db->where('quantity', $criterion['quantity']);
        }

        if (array_key_exists('price', $criterion) && !empty($criterion['price'])) {
            $this->db->where('price', $criterion['price']);
        }

        if (array_key_exists('minimum', $criterion) && !empty($criterion['minimum'])) {
            $this->db->where('minimum', $criterion['minimum']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term']);
            $this->db->group_end();
        }

        $query_count = $this->db->get();
        if ($query_count->row() == NULL) {
            return 0;
        }

        return $query_count->row()->total_rows;
    }

    public function insert($product)
    {
        $this->db->trans_start();
        $date_available = date('Y-m-d');

        if (!array_key_exists('qty_unit_id', $product)) {
            throw new Exception('Qty unit id is required');
        }

        $this->db->insert('product', [
            'sku' => remove_whitespace($product['sku']),
            'model' => $product['model'], 
            'quantity' => (int) $product['quantity'], 
            'minimum' => (int) $product['minimum'], 
            'maximum' => (int) $product['maximum'], 
            'minimum_order' => (int) $product['minimum_order'], 
            'maximum_order' => (int) $product['maximum_order'], 
            'moving_product_status' => $product['moving_product_status'],
            'subtract' => (int) $product['subtract'], 
            'stock_status_id' => (int) $product['stock_status_id'], 
            'date_available' => $product['date_available'] == NULL ? $date_available : $product['date_available'], 
            'manufacturer_id' => (int) $product['manufacturer_id'], 
            'shipping' => (int) $product['shipping'], 
            'points' => (int) $product['points'], 
            'weight' => (float) $product['weight'], 
            'weight_class_id' => (int) $product['weight_class_id'], 
            'length' => (float) $product['length'], 
            'width' => (float) $product['width'], 
            'height' => (float) $product['height'], 
            'length_class_id' => (int) $product['length_class_id'], 
            'status' => (int) $product['status'], 
            'tax_class_id' => (int) $product['tax_class_id'], 
            'sort_order' => (int) $product['sort_order'], 
            'date_added' => date('Y-m-d H:i:s'), 
            'date_modified' => date('Y-m-d H:i:s'),
            'qty_unit_id' => $product['qty_unit_id']
        ]);    
        
        $product_id = $this->db->insert_id();
		if (array_key_exists('image', $product)) {
            $this->db->where('product_id', $product_id);
            $this->db->update('product', [
                'image' => $product['image']
            ]);
        }
                
        $language_id = (int) $this->get_language_id();
        $this->db->insert('product_description', [
            'product_id' => $product_id,
            'language_id' => $language_id, 
            'name' => $product['name'], 
            'description' => $product['description'],
            'meta_title' => $product['meta_title'], 
            'meta_description' => $product['meta_description'], 
            'meta_keyword' => $product['meta_keyword']
        ]);

		if (array_key_exists('product_option', $product)) {
			foreach ($product['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
                        $this->db->insert('product_option', [
                            'product_id' => (int) $product_id, 
                            'option_id' => (int) $product_option['option_id'], 
                            'required' => true
                        ]);

						$product_option_id = $this->db->insert_id();

						foreach ($product_option['product_option_value'] as $product_option_value) {
                            if ($product_option_value['checked'] == true) {
                                $this->db->insert('product_option_value', [
                                    'product_option_id' => (int) $product_option_id, 
                                    'product_id' => (int) $product_id, 
                                    'option_id' => (int) $product_option['option_id'], 
                                    'option_value_id' => (int) $product_option_value['option_value_id'],     
                                ]);
                            }
						}
					}
				} else {
                    $this->db->insert('product_option', [
                        'product_id' => $product_id,
                        'option_id' => (int) $product_option['option_id'],
                        'value' => $product_option['value'],
                        'required' => (int) $product_option['required']
                    ]);
				}
			}
		} //end product option

        //insert product image
		if (array_key_exists('product_image', $product) && count($product['product_image'])) {
            array_walk($product['product_image'], function(&$product_image) use ($product_id) {
                $product_image['product_id'] = $product_id;
            });
            
            $this->db->insert_batch('product_image', $product['product_image']);
		}

		if (array_key_exists('product_category', $product)) {
			foreach ($product['product_category'] as $category_id) {
                $this->db->insert('product_to_category', [
                    'product_id' => (int) $product_id, 
                    'category_id' => (int) $category_id
                ]);
			}
		}

		if (array_key_exists('product_related', $product)) {
			foreach ($product['product_related'] as $related_id) {
                $product_id = (int) $product_id;
                $related_id = (int) $related_id;
				$this->db->query("DELETE FROM {PRE}product_related WHERE product_id = '{$product_id}' AND related_id = '{$related_id}'");
				$this->db->query("INSERT INTO {PRE}product_related SET product_id = '{$product_id}', related_id = '{$related_id}'");
				$this->db->query("DELETE FROM {PRE}product_related WHERE product_id = '{$related_id}' AND related_id = '{$product_id}'");
				$this->db->query("INSERT INTO {PRE}product_related SET product_id = '{$related_id}', related_id = '{$product_id}'");
			}
        }
        
        /**
         * handling inventory balance
         * fix me
        if (!array_key_exists('invbalance_id', $product)) {
            throw new Exception('Invbalance id is required');
        }
        **/

        /** 
        $query_invbalance = $this->db->get_where('inventory_balance', [
            'inventory_balance_id' => $product['invbalance_id']
        ]);
        if ($query_invbalance->row() == NULL) {
            throw new Exception('Illegal inventory balance id');
        }

        $this->db->insert('product_inventory', [
            'product_id' => (int) $product_id,
            'inventory_balance_id' => (int) $product['invbalance_id'],
            'unit_measurement_id' => (int) $product['qty_unit_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 0,
            'status' => true
        ]); **/
        // end of inventory
        if (array_key_exists('supplier_id', $product)) {
            $this->db->insert('supplier_to_product',[
                'supplier_id' => $product['supplier_id'],
                'product_id' => $product_id,
            ]);
        }

        $this->db->insert('product_to_store', [
            'product_id' => $product_id,
            'store_id' => 0
        ]);

        $this->db->trans_complete();

        return $product_id;
    }

    public function find_one($product_id)
    {
        $language_id = (int) $this->get_language_id();
        $query_product = $this->db->query("SELECT DISTINCT * FROM {PRE}product p 
        LEFT JOIN {PRE}product_description pd 
        ON (p.product_id = pd.product_id) WHERE p.product_id = '{$product_id}' 
        AND pd.language_id = '{$language_id}'");        

        $product = $query_product->row();
        if ($product == NULL) {
            return NULL;
        }

        $query_category = $this->db->get_where('product_to_category', [
            'product_id' => $product_id
        ]);
        if ($query_category != NULL) {
            $product_category = $query_category->result();
            $product->product_category = array_map(function($category){
                return $category->category_id;
            }, $product_category);
        }

        $this->db->select('product_related.related_id, product_description.name');
        $this->db->from('product_related');
        $this->db->join('product_description', 'product_related.related_id = product_description.product_id');
        $this->db->where('product_related.product_id', $product_id);
        $this->db->order_by('product_related.related_id');
        $query_product_related = $this->db->get();
        if ($query_product_related != null) {
            $product->product_related = $query_product_related->result();
        }

        //query supplier
        $query_supplier = $this->db->get_where('supplier_to_product', [
            'product_id' => $product_id
        ]);
        if ($query_supplier->row() != null) {
            $product->supplier_id = $query_supplier->row()->supplier_id;
        }

        //query product option
        $product_option = $this->db->get_where('product_option', [
            'product_id' => $product_id
        ])->result();
        if ($product_option != NULL) {
            $option_ids = [];
            foreach ($product_option as $product_opt) {
                $option_ids[] = $product_opt->option_id;
            }

            $this->db->join('option_description', 'option.option_id = option_description.option_id');
            $this->db->where('option_description.language_id', $language_id);
            $this->db->where_in('option_description.option_id', $option_ids);
            $query_option = $this->db->get('option');  
            
            $product->product_option = [];
            if ($query_option->result() != NULL) {
                foreach ($query_option->result() as $option) {
                    $option->values = $this->get_product_option_values($option->option_id);
                    $db = $this->db;
                    array_walk($option->values, function(&$option_value, $key) use ($db, $product_id) {
                        $option_value['checked'] = false;
                        $product_option_value = $db->get_where('product_option_value', [
                            'option_value_id' => $option_value['option_value_id'],
                            'product_id' => $product_id
                        ])->row();
                        if ($product_option_value != NULL) {
                            $option_value['checked'] = true;
                        } 
                    });
                    $product->product_option[] = $option;
                }
            }
            
        } else {
            $product->product_option = [];
        }
        
        //query product images
        $product->product_images = [];
        $product_images = $this->db->get_where('product_image', [
            'product_id' => $product_id
        ])->result();
        if ($product_images != NULL) {
            $product->product_images = $product_images;
        }
        
        return $product;
    }

    public function update($product_id, $product) {
        if (!array_key_exists('qty_unit_id', $product)) {
            throw new Exception('Qty unit id is required');
        }

        $this->db->trans_start();
        //update data
        $date_available = date('Y-m-d');
        $this->db->where('product_id', $product_id);
        $this->db->update('product', [
            'sku' => remove_whitespace($product['sku']),
            'isbn' => remove_whitespace($product['isbn']),
            'model' => $product['model'], 
            'quantity' => (int) $product['quantity'], 
            'minimum' => (int) $product['minimum'], 
            'maximum' => (int) $product['maximum'], 
            'minimum_order' => (int) $product['minimum_order'], 
            'maximum_order' => (int) $product['maximum_order'], 
            'moving_product_status' => $product['moving_product_status'],
            'subtract' => $product['subtract'], 
            'stock_status_id' => (int) $product['stock_status_id'], 
            'date_available' => $product['date_available'] == NULL ? $date_available : $product['date_available'], 
            'manufacturer_id' => (int) $product['manufacturer_id'], 
            'shipping' => (int) $product['shipping'], 
            'price' => (float) $product['price'], 
            'points' => (int) $product['points'], 
            'weight' => (float) $product['weight'], 
            'weight_class_id' => (int) $product['weight_class_id'], 
            'length' => (float) $product['length'], 
            'width' => (float) $product['width'], 
            'height' => (float) $product['height'], 
            'length_class_id' => (int) $product['length_class_id'], 
            'status' => (int) $product['status'], 
            'tax_class_id' => (int) $product['tax_class_id'], 
            'sort_order' => (int) $product['sort_order'], 
            'date_modified' => date('Y-m-d H:i:s'),
            'qty_unit_id' => $product['qty_unit_id'],
            'multiple_uom' => $product['multiple_uom'],
        ]);    

		if (array_key_exists('image', $product)) {
            $this->db->where('product_id', $product_id);
            $this->db->update('product', [
                'image' => $product['image']
            ]);
        }

        $language_id = (int) $this->get_language_id();
        $this->db->where('product_id', $product_id);
        $this->db->where('language_id', $language_id);
        $this->db->update('product_description', [
            'product_id' => $product_id,
            'language_id' => $language_id, 
            'name' => $product['name'], 
            'description' => $product['description'],
            'meta_title' => $product['meta_title'], 
            'meta_description' => $product['meta_description'], 
            'meta_keyword' => $product['meta_keyword']
        ]);
        
        //ini gak perlu, karena wh gak memanage store atau toko
        //$this->db->query("DELETE FROM {PRE}product_to_store WHERE product_id = '{$product_id}'");
		$this->db->query("DELETE FROM {PRE}product_option WHERE product_id = '{$product_id}'");
		$this->db->query("DELETE FROM {PRE}product_option_value WHERE product_id = '{$product_id}'");
		if (array_key_exists('product_option', $product)) {
			foreach ($product['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
                        $this->db->insert('product_option', [
                            'product_id' => (int) $product_id, 
                            'option_id' => (int) $product_option['option_id'], 
                            'required' => true
                        ]);

						$product_option_id = $this->db->insert_id();

						foreach ($product_option['product_option_value'] as $product_option_value) {
                            if ($product_option_value['checked'] == true) {
                                $this->db->insert('product_option_value', [
                                    'product_option_id' => (int) $product_option_id, 
                                    'product_id' => (int) $product_id, 
                                    'option_id' => (int) $product_option['option_id'], 
                                    'option_value_id' => (int) $product_option_value['option_value_id'],     
                                ]);    
                            }
						}
					}
				} else {
                    $this->db->insert('product_option', [
                        'product_id' => $product_id,
                        'option_id' => (int) $product_option['option_id'],
                        'value' => $product_option['value'],
                        'required' => (int) $product_option['required']
                    ]);
				}
			}
		} //end product option

        //delete product discount
        $this->db->where('product_id', $product_id);
        $this->db->delete('product_discount');
		if (array_key_exists('product_discount', $product)) {
			foreach ($product['product_discount'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount 
                SET product_id = '" . (int)$product_id . "', 
                customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', 
                quantity = '" . (int)$product_discount['quantity'] . "', 
                priority = '" . (int)$product_discount['priority'] . "', 
                price = '" . (float)$product_discount['price'] . "', 
                date_start = '" . $this->db->escape($product_discount['date_start']) . "', 
                date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
                $this->db->insert('product_discount', [
                    'product_id' =>  (int) $product_id, 
                    'customer_group_id' => (int) $product_discount['customer_group_id'], 
                    'quantity' => (int) $product_discount['quantity'], 
                    'priority' => (int) $product_discount['priority'], 
                    'price' => (float) $product_discount['price'], 
                    'date_start' => $product_discount['date_start'], 
                    'date_end' => $product_discount['date_end']    
                ]);
			}
        }
        
        //update image
        $this->db->where('product_id', $product_id);
        $this->db->delete('product_image');    
		if (array_key_exists('product_image', $product) && count($product['product_image'])) {
            $this->db->insert_batch('product_image', $product['product_image']);
		}
        //update product to categories
        $this->db->where('product_id', (int) $product_id);
        $this->db->delete('product_to_category');    
		if (array_key_exists('product_category', $product)) {
			foreach ($product['product_category'] as $category_id) {
                $this->db->insert('product_to_category', [
                    'product_id' => (int) $product_id, 
                    'category_id' => (int) $category_id
                ]);
			}
		}

        // update product related
        $this->db->where('product_id', (int) $product_id);
        $this->db->delete('product_related');    
        
        $this->db->where('related_id', (int) $product_id);
        $this->db->delete('product_related');    
		if (array_key_exists('product_related', $product)) {
			foreach ($product['product_related'] as $related_id) {
                $product_id = (int) $product_id;
                $related_id = (int) $related_id;
                
                $this->db->where('product_id', (int) $product_id);
                $this->db->where('related_id', (int) $related_id);
                $this->db->delete('product_related');    
                        
                $this->db->insert('product_related', [
                    'product_id' => (int) $product_id,
                    'related_id' => (int) $related_id,
                ]);

                $this->db->where('product_id', (int) $related_id);
                $this->db->where('related_id', (int) $product_id);
                $this->db->delete('product_related');    
                
                $this->db->insert('product_related', [
                    'product_id' => (int) $related_id,
                    'related_id' => (int) $product_id,
                ]);
                
			}
        }

        if (array_key_exists('supplier_id', $product)) {
            // delete first
            $this->db->where('product_id', (int) $product_id);
            $this->db->delete('supplier_to_product');    
            // insert then
            $this->db->insert('supplier_to_product', [
                'supplier_id' => (int) $product['supplier_id'],
                'product_id' => (int) $product_id
            ]);
        }

        //handling inventory balance
        /** 
        if (!array_key_exists('invbalance_id', $product)) {
            throw new Exception('Invbalance id is required');
        }

        $query_invbalance = $this->db->get_where('inventory_balance', [
            'inventory_balance_id' => $product['invbalance_id']
        ]);
        if ($query_invbalance->row() == NULL) {
            throw new Exception('Illegal inventory balance id');
        }

        $query_prodinventory = $this->db->get_where('product_inventory', [
            'product_id' => (int) $product_id
        ]);
        if ($query_prodinventory->row() == NULL) {
            //TODO: fix created by, not hard code
            // check apakah target qty unit-nya sama
            $this->db->insert('product_inventory', [
                'product_id' => (int) $product_id,
                'inventory_balance_id' => (int) $product['invbalance_id'],
                'unit_measurement_id' => (int) $product['qty_unit_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 0,
                'status' => true
            ]);
        } else {            
            //TODO: before update, check it has inventory balance?
            //dan juga targe qty unit -nya sama
            $this->db->where('product_id', $product_id);
            $this->db->update('product_inventory', [
                'inventory_balance_id' => $product['invbalance_id'],
                'unit_measurement_id' => (int) $product['qty_unit_id'],
            ]);
        } **/
        // update product variant
        $this->db->where('product_id', (int) $product_id);
        $this->db->delete('product_variant');    
		if ($product['multiple_uom'] && array_key_exists('product_variant', $product)) {
            //var_dump($product['product_variant']);
            foreach ($product['product_variant'] as $product_variant) {
                $this->db->insert('product_variant', [
                    'product_id' => (int) $product_id,
                    'model' => $product_variant['model'],
                    'qty_unit_id' => $product_variant['qty_unit_id'],
                    'qty_rasio' => $product_variant['qty_rasio'],   
                ]);    
            }
        }        

        $product_store = $this->db->get_where('product_to_store', [
            'product_id' => (int) $product_id
        ])->row_array();
        
        if ($product_store == NULL) {
            log_message("info", "Attempting to insert product ". $product_id . " to store id 0");
            $this->db->insert('product_to_store', [
                'product_id' => $product_id,
                'store_id' => 0
            ]);
        } 

        $this->db->trans_complete();
    }

    public function disable($product_id) 
    {
        $this->db->where('product_id', $product_id);
        $this->db->update('product', [
            'status' => 0
        ]);
    }

    public function find_by_sku($sku)
    {
        $query_product = $this->db->get_where('product', [
            'sku' => $sku
        ]);

        return $query_product->row();
    }

    public function find_by_isbn($isbn)
    {
        $query_product = $this->db->get_where('product', [
            'isbn' => $isbn
        ]);

        return $query_product->row();
    }

    public function get_product_option_values($option_id)
    {
        $option_id = (int) $option_id;
        $language_id = (int) $this->get_language_id();
		$option_value_data = array();

        $option_value_query = $this->db->query("SELECT * FROM {PRE}option_value 
        WHERE option_id = '" . $option_id . "' ORDER BY sort_order");

		foreach ($option_value_query->result_array() as $option_value) {
			$option_value_description_data = array();

            $option_value_description_query = $this->db->query("SELECT * FROM {PRE}option_value_description 
            WHERE option_value_id = '" . (int) $option_value['option_value_id'] . "'
            AND language_id = '". $language_id ."'");

			$option_value_data[] = array(
				'option_value_id'          => $option_value['option_value_id'],
				'option_value_description' => $option_value_description_query->row_array()['name'],
				'image'                    => $option_value['image'],
				'sort_order'               => $option_value['sort_order']
			);
		}

		return $option_value_data;
    }

    function find_by_supplier($supplier_id, 
        $criterion = array(),
        $first = 0, $count = 25, 
        $sort = 'product.date_modified', $order = 'DESC')
    {
        if (!isset($supplier_id)) {
            throw new Exception('Supplier must not null');
        }

        $language_id = (int) $this->get_language_id();
        $this->db->select('product.*, product_description.name, unit_measurement.symbol AS qty_unit');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('unit_measurement', 'product.qty_unit_id = unit_measurement.unit_measurement_id', 'left');
        $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where('product.status', 1);
        $this->db->where('supplier_to_product.supplier_id', $supplier_id);

        if (array_key_exists('quantity', $criterion)) {
            $this->db->where('quantity', $criterion['quantity']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term']);
            $this->db->group_end();
        }

        $this->db->group_by('product.product_id');
        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by(array_key_exists($sort, $this->sort_data) ? $this->sort_data[$sort] : 'product.date_modified', $order);
        $query_product = $this->db->get();

        return $query_product->result();
    }    

    
    public function find_by_model($model)
    {
        $query_product = $this->db->get_where('product', [
            'model' => $model
        ]);

        return $query_product->row();
    }

    public function count_by_supplier($supplier_id, $criterion = array())
    {
        $language_id = (int) $this->get_language_id();
        $this->db->select('COUNT(DISTINCT {PRE}product.product_id) AS total_rows');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where('product.status', 1);
        $this->db->where('supplier_to_product.supplier_id', $supplier_id);
        
        if (array_key_exists('quantity', $criterion)) {
            $this->db->where('quantity', $criterion['quantity']);
        }

        if (array_key_exists('term', $criterion) && !empty($criterion['term'])) {
            $this->db->group_start();
            $this->db->like('product.model', $criterion['term']);
            $this->db->or_like('product_description.name', $criterion['term']);
            $this->db->group_end();
        }

        $query_count = $this->db->get();
        if ($query_count->row() == NULL) {
            return 0;
        }

        return $query_count->row()->total_rows;
    }

    public function find_images($product_ids)
    {
        if (!is_array($product_ids) || count($product_ids) == 0) {
            return [];
        }

        $this->db->where('sort_order', 0);
        $this->db->where_in('product_id', $product_ids);
        
        return $this->db->get('product_image')->result();
    }


    public function update_quantity($product_id, $quantity)
    {
        $this->db->where('product_id', $product_id);
        $this->db->update('product', [
            'quantity' => $quantity
        ]);
    }

    public function find_by_models($models)
    {
        if (!is_array($models)) {
            throw new Exception("Model or barcode must be array", 1);
        }

        $this->db->select('product_id, model');
        $this->db->from('product');
        $this->db->where_in('model', $models);
        $query_product = $this->db->get();

        return $query_product->result_array();
    }

    function find_by_ids($product_ids, $sort = 'product.date_modified', $order = 'DESC')
    {
        $language_id = (int) $this->get_language_id();
        $this->db->select('product.*, product_description.name');
        $this->db->from(self::TBL_REFERENCE);
        $this->db->join('product_description', 'product.product_id = product_description.product_id', 'left');
        $this->db->join('supplier_to_product', 'product.product_id = supplier_to_product.product_id');
        $this->db->where('product_description.language_id', $language_id);
        $this->db->where_in('product.product_id', $product_ids);

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );
        $this->db->order_by(array_key_exists($sort, $this->sort_data) ? $this->sort_data[$sort] : 'product.date_modified', $order);
        $query_product = $this->db->get();

        return $query_product->result();
    }    

    public function get_supplier($product_id)
    {
        $this->db->select('supplier.supplier_id, supplier.name');
        $this->db->from('supplier');
        $this->db->join('supplier_to_product', 'supplier.supplier_id = supplier_to_product.supplier_id');
        $this->db->where('supplier_to_product.product_id', $product_id);
        $query = $this->db->get();

        return $query->row();
    }
    
}