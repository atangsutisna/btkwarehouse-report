<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model
{
	protected $http_client;

    function __construct()
    {
        parent::__construct();
        $this->http_client = new GuzzleHttp\Client(['base_uri' => 'http://157.245.207.150/api/']);
    }

	function find_all()
	{
		$response = $this->http_client->request('POST', 'employee/get', ['auth' => ['dannoshaprilm@gmail.com', '101010Messi']]);
		if ($response->getStatusCode() == 200) {
			return json_decode($response->getBody());
		}

		throw new Exception('Exception occur');
	}
}