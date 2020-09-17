<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_multiple_uom extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_product` ADD `qty_unit_id` INT NOT NULL AFTER `expiry_date`");
        $this->db->query("ALTER TABLE `oc_product` ADD `multple_uom` BOOLEAN NOT NULL DEFAULT FALSE AFTER `qty_unit_id`");

        $this->db->query("CREATE TABLE `oc_product_variant` (
            `product_variant_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `model` varchar(64) NOT NULL,
            `qty_unit_id` int(11) NOT NULL,
            `qty_rasio` double(10,2) NOT NULL,
            `price` decimal(15,4) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        $this->db->query("ALTER TABLE `oc_product_variant` ADD PRIMARY KEY (`product_variant_id`)");
        $this->db->query("ALTER TABLE `oc_product_variant` MODIFY `product_variant_id` int(11) NOT NULL AUTO_INCREMENT");
    }
}