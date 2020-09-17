<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Po_qty_rasio extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_purchase_order_items` ADD `qty_rasio` INT NOT NULL AFTER `qty_unit`");
        $this->db->query("ALTER TABLE `oc_purchase_order_items` ADD `note` TEXT NOT NULL AFTER `qty_rasio`");
        $this->db->query("ALTER TABLE `oc_purchase_order_items` DROP COLUMN `product_sku`");
    }
}

