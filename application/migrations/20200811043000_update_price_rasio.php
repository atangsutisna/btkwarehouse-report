<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_price_rasio extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_product_variant` ADD `cost_of_goods_sold` DECIMAL(15,4) NOT NULL AFTER `price_2`, ADD `date_modified` TIMESTAMP NOT NULL AFTER `cost_of_goods_sold`");
        $this->db->query("ALTER TABLE `oc_purchasing_items` ADD `qty_rasio` INT NOT NULL AFTER `qty_unit_id`");
        $this->db->query("ALTER TABLE `oc_goods_receipt_items` ADD `qty_rasio` INT NOT NULL AFTER `qty_unit_id`");
    }
}

