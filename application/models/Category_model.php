<?php

class Category_model  extends MY_Model
{
    const TBL_REFERENCE = 'category';
    const PRIMARY_KEY = 'category_id';

    function __construct()
    {
        parent::__construct(self::TBL_REFERENCE, self::PRIMARY_KEY);
    }

	function find_one($category_id)
	{
		$language_id = (int) $this->get_language_id();
		$category_id = (int) $category_id;
		$query = $this->db->query("SELECT DISTINCT *, 
		(SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') 
		FROM {PRE}category_path 
		cp LEFT JOIN {PRE}category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) 
		WHERE cp.category_id = c.category_id AND cd1.language_id = '{$language_id}' 
		GROUP BY cp.category_id) AS path FROM {PRE}category c 
		LEFT JOIN {PRE}category_description cd2 ON (c.category_id = cd2.category_id) 
		WHERE c.category_id = '{$category_id}' AND cd2.language_id = '{$language_id}'");

		return $query->row();
	}

    function find_all($term, $first = 0, $count = 25, $sort = 'sort_order', $order = 'asc') 
    {   
        $language_id = (int) $this->get_language_id();
		$sql = "SELECT cp.category_id AS category_id, 
        GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, 
        c1.parent_id, c1.sort_order FROM {PRE}category_path cp 
        LEFT JOIN {PRE}category c1 ON (cp.category_id = c1.category_id) 
        LEFT JOIN {PRE}category c2 ON (cp.path_id = c2.category_id) 
        LEFT JOIN {PRE}category_description cd1 ON (cp.path_id = cd1.category_id) 
        LEFT JOIN {PRE}category_description cd2 ON (cp.category_id = cd2.category_id) 
        WHERE cd1.language_id = '{$language_id}' AND cd2.language_id = '{$language_id}'";

		if ($term != NULL && $term !== '') {
			$sql .= " AND cd2.name LIKE '%" . $term . "%'";
		}

		$sql .= " GROUP BY cp.category_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if ($sort != NULL && in_array($sort, $sort_data)) {
			$sql .= " ORDER BY " . $sort;
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if ($order != NULL && ($order == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		$first = isset($first) && $first != 0 ? $first : 0;
		$count = isset($count) && $count != 0 ? $count : 20;
		$sql .= " LIMIT " . (int) $first . "," . (int) $count;
		$query = $this->db->query($sql);
		
		return $query->result();        
	}
	
    public function count_all($term = NULL)
    {
		$query = $this->db->query("SELECT COUNT(*) AS total_rows FROM (
			SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '  >  ') AS name FROM oc_category_path cp 
			LEFT JOIN oc_category c1 ON (cp.category_id = c1.category_id) 
			LEFT JOIN oc_category c2 ON (cp.path_id = c2.category_id) 
			LEFT JOIN oc_category_description cd1 ON (cp.path_id = cd1.category_id) 
			LEFT JOIN oc_category_description cd2 ON (cp.category_id = cd2.category_id) 
			WHERE cd1.language_id = '2' AND cd2.language_id = '2'
			GROUP BY cp.category_id ORDER BY name ASC) 
			parent_child_category");
		$query_result = $query->row_array();
		if ($query_result != NULL) {
			return $query_result['total_rows'];
		}

		return 0;
	}
	
    public function update($category_id, $category)
    {
		$category_id = (int) $category_id;
		$language_id = (int) $this->get_language_id();
		$parent_id = (int) $category['parent_id'];
		$top = (isset($data['top']) ? (int)$data['top'] : 0);
		//update category		
        $this->db->where('category_id', $category_id);
        $this->db->update(self::TBL_REFERENCE, [
			'parent_id' => $parent_id,
			'top' => $top,
			'column' => 1,
			'sort_order' => $category['sort_order'],
			'status' => $category['status'],
			'date_modified' => date('Y-m-d H:i:s'),
		]);
		//update image category
		if (array_key_exists('image', $category)) {
			$this->db->where('category_id', $category_id);
			$this->db->update(self::TBL_REFERENCE, [
				'image' => $category['image'],
			]);	
		}

		$this->db->query("DELETE FROM {PRE}category_description WHERE category_id = '{$category_id}'");
		$this->db->insert('category_description', [
			'category_id' => $category_id,
			'language_id' => $language_id,
			'name' => $category['name'],
			'description'=> $category['description'],
			'meta_title'=> $category['meta_title'],
			'meta_description'=> $category['meta_description'],
			'meta_keyword'=> $category['meta_keyword'],
		]);

		// MySQL Hierarchical Data Closure Table Pattern
		$this->db->where('path_id', (int) $category_id);
		$this->db->order_by('level', 'ASC');
		$query_cat_path = $this->db->get('category_path');
		if (count($query_cat_path->result()) > 0) {
			foreach ($query_cat_path->result_array() as $category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `{PRE}category_path` 
				WHERE category_id = '" . (int) $category_path['category_id'] . "' AND level < '" . (int) $category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `{PRE}category_path` 
				WHERE category_id = '" . (int) $category['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->result_array() as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `{PRE}category_path` 
				WHERE category_id = '" . (int) $category_path['category_id'] . "' ORDER BY level ASC");

				foreach ($query->result_array() as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `{PRE}category_path` 
					SET category_id = '" . (int) $category_path['category_id'] . "', `path_id` = '" . (int) $path_id . "', level = '" . (int) $level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `{PRE}category_path` WHERE category_id = '{$category_id}'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `{PRE}category_path` WHERE category_id = '{$parent_id}' ORDER BY level ASC");

			foreach ($query->result_array() as $result) {
				$path_id = (int) $result['path_id'];
				$level = (int) $level;
				$this->db->query("INSERT INTO `{PRE}category_path` 
				SET category_id = '{$category_id}', `path_id` = '{$path_id}', level = '{$level}'");

				$level++;
			}

			$this->db->query("REPLACE INTO `{PRE}category_path` 
			SET category_id = '{$category_id}', `path_id` = '{$category_id}', level = '{$level}'");
		}

		$this->db->query("DELETE FROM {PRE}category_filter WHERE category_id = '{$category_id}'");
		if (array_key_exists('category_filter', $category)) {
			foreach ($category['category_filter'] as $filter_id) {
				$filter_id = (int) $filter_id;
				$this->db->query("INSERT INTO {PRE}category_filter 
				SET category_id = '{$category_id}', filter_id = '{$filter_id}'");
			}
		}

		$this->db->query("DELETE FROM {PRE}category_to_store WHERE category_id = '{$category_id}'");		
		if (array_key_exists('category_store', $category)) {
			foreach ($category['category_store'] as $store_id) {
				$store_id = (int) $store_id;
				$this->db->query("INSERT INTO {PRE}category_to_store 
					SET category_id = '{$category_id}', store_id = '{$store_id}'");
			}
		}

		$this->db->query("DELETE FROM `{PRE}seo_url` WHERE query = 'category_id=" . (int)$category_id . "'");

		if (array_key_exists('category_seo_url', $category)) {
			foreach ($category['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO {PRE}seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" . (int) $category_id . "', keyword = '" . $keyword . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM {PRE}category_to_layout WHERE category_id = '" . (int) $category_id . "'");
		/** 
		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
		**/
	}
	
	public function insert($category)
	{
		$language_id = (int) $this->get_language_id();
		//begin transaction
		$this->db->trans_start();
		$this->db->insert(self::TBL_REFERENCE, [
			'parent_id' => $category['parent_id'],
			'top' => array_key_exists('top', $category) ? (int) $category['top'] : 0,
			'column' => array_key_exists('column', $category) ? $category['column'] : '',
			'sort_order' => array_key_exists('sort_order', $category) && $category['sort_order'] != NULL ? $category['sort_order'] : 0,
			'status' => (int) $category['status'],
			'date_modified' => date('Y-m-d H:i:s'),
			'date_added' => date('Y-m-d H:i:s')
		]);
		$category_id = $this->db->insert_id();

		if (array_key_exists('image', $category)) {
			$this->db->where('category_id', (int) $category_id);
			$this->db->update(self::TBL_REFERENCE, [
				'image' => $category['image']
			]);
		}

		$this->db->insert('category_description', [
			'category_id' => $category_id,
			'language_id' => $language_id,
			'name' => $category['name'],
			'description' => $category['description'],
			'meta_title' => $category['meta_title'],
			'meta_description' => $category['meta_description'],
			'meta_keyword' => $category['meta_keyword'],
		]);

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;
		$this->db->where('category_id', (int) $category['parent_id']);
		$this->db->order_by('level', 'ASC');
		$query_category_path = $this->db->get('category_path');
		foreach ($query_category_path->result() as $result) {
			$this->db->insert('category_path', [
				'category_id' => (int) $category_id,
				'path_id' => (int) $result->path_id,
				'level' => (int) $level,
			]);

			$level++;
		}

		$this->db->insert('category_path', [
			'category_id' => (int) $category_id,
			'path_id' => (int) $category_id,
			'level' => (int) $level
		]);
		
		//FIXME: don't hardcode the store id
		$this->db->insert('category_to_store', [
			'category_id' => (int) $category_id,
			'store_id' => 0
		]);
		$this->db->trans_complete();

		return $category_id;		
	}

	function find_by_ids($cat_ids)
	{
		$language_id = (int) $this->get_language_id();
		$cat_ids = implode(',', $cat_ids);
		$query = $this->db->query("SELECT DISTINCT *, 
		(SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') 
		FROM {PRE}category_path 
		cp LEFT JOIN {PRE}category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) 
		WHERE cp.category_id = c.category_id AND cd1.language_id = '{$language_id}' 
		GROUP BY cp.category_id) AS path FROM {PRE}category c 
		LEFT JOIN {PRE}category_description cd2 ON (c.category_id = cd2.category_id) 
		WHERE c.category_id IN ({$cat_ids}) AND cd2.language_id = '{$language_id}'");

		return $query->result();
	}

	function find_by_names($language_id, $names)
	{
		$this->db->where('language_id', $language_id);
		$this->db->where_in('name', $names);
		$query = $this->db->get('category_description');

		return $query->result_array();
	}


	public function delete($id)
	{
		$this->db->trans_start();

		//delete from oc_category
		$this->db->where('category_id', $id);
		$this->db->delete('category');
		
		//delete from oc_category
		$this->db->where('parent_id', $id);
		$this->db->delete('category');

		//delete from category_description
		$this->db->where('category_id', $id);
		$this->db->delete('category_description');
		
		//delete from category_filter
		$this->db->where('category_id', $id);
		$this->db->delete('category_filter');

		//delete from category_path
		$this->db->where('category_id', $id);
		$this->db->delete('category_path');
		
		//delete from category_path
		$this->db->where('path_id', $id);
		$this->db->delete('category_path');

		$this->db->where('category_id', $id);
		$this->db->delete('product_to_category');

		$this->db->trans_complete();
	}
}

