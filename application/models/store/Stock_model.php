<?php

class Stock_model  extends CI_Model
{

	public function __construct() 
	{
		$this->load->model('Storagebin2_model', 'storagebin2');
		$this->load->model('Mutation_stock_model', 'mutation_stock');
	}

	/**
	* array of [['transaction_id', 'transaction_type', 'product_id', 'product_name', 'qty']]
	* Update qty (-)
	**/
	public function update($data)
	{
		$this->db->trans_start();
		
		//1. update (substract) stock product
		$product = array_map(function($item){
			return [
				'product_id' => $item['product_id'],
				'product_name' => $item['product_name'],
				'qty' => $item['qty'],
			];
		}, $data);	
		$this->storagebin2->substract($product);

		//2. create mutation stock
		$this->mutation_stock->insert_all($data);

		$this->db->trans_complete();
	}


}