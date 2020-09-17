<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Cost_of_goods_sold extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_product` ADD `cost_of_goods_sold` DECIMAL(15,4) NOT NULL AFTER `price_2`");
    }
}

