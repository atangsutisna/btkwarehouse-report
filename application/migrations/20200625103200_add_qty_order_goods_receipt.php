<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_qty_order_goods_receipt extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {PRE}goods_receipt_items ADD `qty_order` INT NOT NULL AFTER `price`");
        $this->db->query("UPDATE {PRE}goods_receipt_items SET qty_order = qty WHERE qty_order = 0");
    }
}