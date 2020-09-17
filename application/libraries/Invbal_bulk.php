<?php

class Invbal_bulk
{
	protected $CI;

	public function __construct() 
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Invbalance_model', 'inventory_balance');
	}

	public function insert($candidate_invbals)
	{
		$total_candidate = count($candidate_invbals);
		if ($total_candidate == 0) {
			return FALSE;
		}

        array_walk($candidate_invbals, function($invbal){
            if (!array_key_exists('sku', $invbal)) {
                throw new Exception("Cannot insert inventory balance, sku is required", 1);
            }
        });

        $new_invbals = $this->_filter_unique($candidate_invbals);
        $total_invbal = count($new_invbals);
        if ($total_invbal > 0) {
	        log_message('info', "Attempting to insert {$total_invbal} items new inventory balances");
	        if (!$this->CI->inventory_balance->insert_all($new_invbals)) {
	        	log_message('error', "Failed to insert {$total_invbal} items new inventory balances");
	        }       	
        }

        $skus = $this->_get_skus($candidate_invbals);
        log_message('info', "Find inventory balance with skus: ". implode(",",  $skus));
        $inventory_balances = $this->CI->inventory_balance->find_by_skus($skus);

        return $inventory_balances;
	}

	protected function _filter_unique($candidate_invbals)
	{
		$skus = $this->_get_skus($candidate_invbals);
        $inventory_balances = $this->CI->inventory_balance->find_by_skus($skus);
        //ensure not duplicate in database
        $filter_results1 = array_filter($candidate_invbals, function($invbal) use ($inventory_balances) {
            $invbal_key = array_search($invbal['sku'], array_column($inventory_balances, 'sku'));
            if (gettype($invbal_key) == 'integer') {
                return FALSE;
            }

            return TRUE;
        });

        //ensure not duplicate in array it self
        $final_results = [];
        foreach ($filter_results1 as $invbal) {
        	$invbal_key = array_search($invbal['sku'], array_column($final_results, 'sku'));
            if (gettype($invbal_key) !== 'integer') {
            	array_push($final_results, $invbal);
            }
        }

        return $final_results;		
	}

	protected function _get_skus($invbals)
	{
       	$skus = array_map(function($inv_bal){
            return $inv_bal['sku'];
        }, $invbals);

        return array_unique($skus);		
	}
}