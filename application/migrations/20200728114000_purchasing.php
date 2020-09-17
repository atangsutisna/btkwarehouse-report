<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Purchasing extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE `oc_purchasing` (
            `purchasing_id` int(11) NOT NULL,
            `purchasing_no` varchar(250) NOT NULL,
            `goods_receipt_id` int(11) NOT NULL,
            `goods_receipt_no` varchar(250) NOT NULL,
            `supplier_id` int(11) NOT NULL,
            `supplier_name` varchar(250) NOT NULL,
            `total_amount` decimal(10,8) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        $this->db->query("ALTER TABLE `oc_purchasing` ADD PRIMARY KEY (`purchasing_id`)");
        $this->db->query("ALTER TABLE `oc_purchasing` MODIFY `purchasing_id` int(11) NOT NULL AUTO_INCREMENT");
    }
}