<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Retur_pembelian_items extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('retur_items')) {
            $this->db->query("DROP TABLE {PRE}retur_items");
        }

        $this->db->query("CREATE TABLE `oc_retur_items` (
            `retur_item_id` int(11) NOT NULL,
            `retur_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `product_model` varchar(255) NOT NULL,
            `product_name` varchar(250) NOT NULL,
            `qty` int(11) NOT NULL,
            `qty_unit_id` int(11) NOT NULL,
            `note` text
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->query("ALTER TABLE `oc_retur_items` ADD PRIMARY KEY (`retur_item_id`), ADD KEY `fk_retur_id` (`retur_id`)");
        $this->db->query("ALTER TABLE `oc_retur_items` MODIFY `retur_item_id` int(11) NOT NULL AUTO_INCREMENT");
        $this->db->query("ALTER TABLE `oc_retur_items` ADD CONSTRAINT `fk_retur_id` FOREIGN KEY (`retur_id`) REFERENCES `oc_retur` (`retur_id`) ON DELETE CASCADE ON UPDATE NO ACTION");
    }
}

