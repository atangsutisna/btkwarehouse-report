<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Retur_pembelian extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('retur') && $this->db->table_exists('retur_items')) {
            $this->db->query("DROP TABLE {PRE}retur_items");
            $this->db->query("DROP TABLE {PRE}retur");
        }

        if ($this->db->table_exists('retur') && !$this->db->table_exists('retur_items')) {
            $this->db->query("DROP TABLE {PRE}retur");
        }

        $this->db->query("CREATE TABLE `oc_retur` (
            `retur_id` int(11) NOT NULL,
            `supplier_id` int(11) NOT NULL,
            `supplier_name` varchar(250) NOT NULL,
            `purchase_order_id` int(11) DEFAULT NULL,
            `retur_no` varchar(250) NOT NULL,
            `retur_date` date NOT NULL,
            `status` enum('draft','complete','void') NOT NULL,
            `created_at` datetime NOT NULL,
            `update_at` datetime NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $this->db->query("ALTER TABLE `oc_retur` ADD PRIMARY KEY (`retur_id`)");
        $this->db->query("ALTER TABLE `oc_retur` MODIFY `retur_id` int(11) NOT NULL AUTO_INCREMENT");
    }
}

