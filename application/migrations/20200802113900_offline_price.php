<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Offline_price extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_product` ADD `price_2` DOUBLE(15,4) NOT NULL COMMENT 'price offline' AFTER `multiple_uom`");
        $this->db->query("ALTER TABLE `oc_product_variant` ADD `price_2` DECIMAL(15,4) NOT NULL AFTER `price`");
        $this->db->query("ALTER TABLE `oc_product_variant` CHANGE `price_2` `price_2` DECIMAL(15,4) NOT NULL COMMENT 'price offline'");
        $this->db->query("ALTER TABLE `oc_price_adjustment` DROP `qty_unit`, DROP `original_price`, DROP `tax`, DROP `margin`, DROP `final_price`");
        $this->db->query("ALTER TABLE `oc_price_adjustment` ADD `price` DECIMAL(15,4) NOT NULL AFTER `product_name`, ADD `price_2` DECIMAL(15,4) NOT NULL AFTER `price`");
        $this->db->query("ALTER TABLE `oc_price_adjustment` ADD `qty_unit_id` INT NOT NULL AFTER `price_2`");
        $this->db->query("ALTER TABLE `oc_price_adjustment` ADD `model` VARCHAR(100) NOT NULL AFTER `product_name`");
    }
}