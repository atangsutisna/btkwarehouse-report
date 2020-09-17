<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Option_model extends MY_Model
{
    const TBL_REFERENCE = 'option';
    const PRIMARY_KEY = 'option_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

    private $sort_data = array(
        'name' => 'option_description.name',
        'sort_order' => 'option.sort_order'
    );

    function find_all($term = NULL, $first = 0, $count = 25, $sort = 'name', $order = 'asc')
    {
        $language_id = (int) $this->get_language_id();
        $this->db->select('*');
        $this->db->from('option');
        $this->db->join('option_description', 'option.option_id = option_description.option_id', 'left');
        $this->db->where('option_description.language_id', $language_id);
        if ($term != NULL || $term != '') {
            $this->db->like('option_description.name', $term);
        }

        $this->db->limit(
            isset($count) ? $count : 20, 
            isset($first) ? $first : 0
        );

        $this->db->order_by($this->sort_data[isset($sort) ? $sort : 'name'], $order);
        if (array_key_exists($sort, $this->sort_data)) {
            $sort = $this->sort_data[$sort];
            $this->db->order_by($sort, $order);
        }
        
		$query_option = $this->db->get();

		return $query_option->result();        
    }

    public function count_all($term = NULL)
    {
        $language_id = (int) $this->get_language_id();
        $this->db->join('option_description', 'option.option_id = option_description.option_id', 'left');
        $this->db->where('option_description.language_id', $language_id);
        if ($term != NULL && $term !== '') {
            $this->db->like('option_description.name', $term);
        }

        $this->db->from(self::TBL_REFERENCE);
        return $this->db->count_all_results();
    }

    public function find_one($option_id)
    {
        $language_id = (int) $this->get_language_id();
        $this->db->join('option_description', 'option.option_id = option_description.option_id');
        $this->db->where('option_description.language_id', $language_id);
        $this->db->where('option_description.option_id', $option_id);
        $query_option = $this->db->get('option');

		return $query_option->row();
    }

    public function get_option_values($option_id)
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

    public function insert($option)
    {
        $this->db->trans_start();
        $this->db->insert('option', [
            'type' => $option['type'],
            'sort_order' => (int) $option['sort_order']
        ]);
		$option_id = $this->db->insert_id();

        $language_id = (int) $this->get_language_id();
        $this->db->insert('option_description', [
            'option_id' => $option_id,
            'language_id' => $language_id,
            'name' => $option['name']
        ]);
        
		if (array_key_exists('option_values', $option)) {
			foreach ($option['option_values'] as $option_value) {
                $this->db->insert('option_value', [
                    'option_id' => $option_id,
                    'sort_order' => $option_value['sort_order']
                ]);

                $option_value_id = $this->db->insert_id();
                $this->db->insert('option_value_description', [
                    'option_value_id' => $option_value_id,
                    'language_id' => $language_id,
                    'option_id' => $option_id,
                    'name' => $option_value['option_value_description'],
                ]);                
			}
        }
        
        $this->db->trans_complete();

		return $option_id;
    }

    public function update($option_id, $option)
    {
        $this->db->trans_start();

        $this->db->where('option_id', $option_id);
        $this->db->update('option', [
            'type' => $option['type'],
            'sort_order' => $option['sort_order'],
        ]);

        $this->db->delete('option_description', ['option_id' => $option_id]);

        $language_id = (int) $this->get_language_id();
        $this->db->insert('option_description', [
            'option_id' => $option_id,
            'language_id' => $language_id,
            'name' => $option['name']
        ]);

        $this->db->delete('option_value', ['option_id' => $option_id]);
        $this->db->delete('option_value_description', ['option_id' => $option_id]);

        if (array_key_exists('option_values', $option)) {
			foreach ($option['option_values'] as $option_value) {
                $this->db->insert('option_value', [
                    'option_id' => $option_id,
                    'sort_order' => $option_value['sort_order']
                ]);

                $option_value_id = $this->db->insert_id();
                $this->db->insert('option_value_description', [
                    'option_value_id' => $option_value_id,
                    'language_id' => $language_id,
                    'option_id' => $option_id,
                    'name' => $option_value['option_value_description'],
                ]);                
			}
        }

        $this->db->trans_complete();
    }

}