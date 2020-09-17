<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_goods_receipt_note extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {PRE}goods_receipt_items ADD `note` TEXT NULL AFTER `qty_unit`");
    }
}