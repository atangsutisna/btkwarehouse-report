<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_qty_unit_id_goods_receipt extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {PRE}goods_receipt_items ADD `qty_unit_id` INT NOT NULL AFTER `qty`");
    }
}