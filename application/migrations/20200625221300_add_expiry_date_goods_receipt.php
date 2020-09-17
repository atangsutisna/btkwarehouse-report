<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_expiry_date_goods_receipt extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {PRE}goods_receipt_items ADD `expiry_date` DATE NULL AFTER `note`");
        $this->db->query("ALTER TABLE {PRE}product ADD `expiry_date` DATE NULL DEFAULT NULL AFTER `moving_product_status`;");
    }
}