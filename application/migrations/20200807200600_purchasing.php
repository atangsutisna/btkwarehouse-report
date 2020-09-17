<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Purchasing extends CI_Migration
{
    public function up()
    {
        /** table purchasing */
        if ($this->db->table_exists('purchasing')) {
          $this->db->query("DROP TABLE `oc_purchasing`");
        }

        $this->db->query("CREATE TABLE `oc_purchasing` (
            `purchasing_id` int(11) NOT NULL,
            `purchasing_no` varchar(250) NOT NULL,
            `goods_receipt_id` int(11) NOT NULL,
            `goods_receipt_no` varchar(250) NOT NULL,
            `supplier_id` int(11) NOT NULL,
            `supplier_name` varchar(250) NOT NULL,
            `payment_method` int(11) NOT NULL,
            `due_date` date NOT NULL,
            `invoice_date` date NOT NULL,
            `receive_date` date NOT NULL,
            `subtotal` decimal(15,4) NOT NULL,
            `taxable` tinyint(1) NOT NULL DEFAULT '0',
            `tax` decimal(15,4) NOT NULL,
            `discount` decimal(15,4) NOT NULL,
            `discount_type` enum('discount_amount','discount_percentage') NOT NULL,
            `total` decimal(15,4) NOT NULL,
            `note` text NOT NULL,
            `created_by` int(11) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        $this->db->query("ALTER TABLE `oc_purchasing` ADD PRIMARY KEY (`purchasing_id`)");
        $this->db->query("ALTER TABLE `oc_purchasing` MODIFY `purchasing_id` int(11) NOT NULL AUTO_INCREMENT");

        /** table purchasing items*/
        if ($this->db->table_exists('oc_purchasing_items')) {
          $this->db->query("DROP TABLE `oc_purchasing_items`");
        }

        $this->db->query("CREATE TABLE `oc_purchasing_items` (
            `puchasing_item_id` int(11) NOT NULL,
            `purchasing_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `product_name` varchar(200) NOT NULL,
            `product_model` varchar(200) NOT NULL,
            `price` decimal(15,4) NOT NULL,
            `qty` int(11) NOT NULL,
            `qty_unit_id` int(11) NOT NULL,
            `discount` decimal(15,4) NOT NULL,
            `finalprice` decimal(15,4) NOT NULL,
            `subtotal` decimal(15,4) NOT NULL,
            `offline_margin` decimal(15,4) NOT NULL,
            `offline_price_pcs` decimal(15,4) NOT NULL,
            `offline_price_rasio` decimal(15,4) NOT NULL,
            `online_margin` decimal(15,4) NOT NULL,
            `online_price_pcs` decimal(15,4) NOT NULL,
            `online_price_rasio` decimal(15,4) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        $this->db->query("ALTER TABLE `oc_purchasing_items` ADD PRIMARY KEY (`puchasing_item_id`)");
        $this->db->query("ALTER TABLE `oc_purchasing_items` MODIFY `puchasing_item_id` int(11) NOT NULL AUTO_INCREMENT");
        $this->db->query("INSERT INTO _sequence (seq_name,seq_group,seq_val) VALUES('purchasing_no','P',1)");
    }
}