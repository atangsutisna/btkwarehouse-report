<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_purchase_order_partial extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_purchase_items` ADD `quantity_receive` INT NULL DEFAULT '0' COMMENT 'Quantity receive (goods receipt)' AFTER `quantity`");
        $this->db->query("ALTER TABLE `oc_purchase_order` CHANGE `status` `status` ENUM('draft','ordered','void','complete','partial') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
        $this->db->query("ALTER TABLE `oc_purchase_order_items` ADD `qty_receipt` INT NULL DEFAULT '0' COMMENT 'qty_receipt' AFTER `qty`");
        $this->db->query("ALTER TABLE `oc_purchase_order_items` ADD `qty_balance` INT NULL DEFAULT '0' AFTER `qty_receipt`");
    }
}