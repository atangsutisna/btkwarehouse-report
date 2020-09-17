<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_purchase_order_id extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {PRE}retur ADD `purchase_order_id` INT NULL AFTER `supplier_name`");
    }
}