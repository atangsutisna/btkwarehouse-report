<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_inventory_balance extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE `oc_inventory_balance` (
            `inventory_balance_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `storagebin1` int(11) NOT NULL COMMENT 'storage bin utama (gudang)',
            `storagebin2` int(11) NOT NULL COMMENT 'secondary (etalase)',
            `qty` double NOT NULL,
            `qty_unit_id` int(11) NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT '1',
            `created_by` int(11) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        $this->db->query("ALTER TABLE `oc_inventory_balance` ADD PRIMARY KEY (`inventory_balance_id`)");
        $this->db->query("ALTER TABLE `oc_inventory_balance` MODIFY `inventory_balance_id` int(11) NOT NULL AUTO_INCREMENT");
    }
}