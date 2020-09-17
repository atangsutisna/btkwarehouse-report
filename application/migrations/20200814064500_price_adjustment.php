<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Price_adjustment extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_price_adjustment` ADD `old_price` DOUBLE(15,4) NOT NULL AFTER `model`");
        $this->db->query("ALTER TABLE `oc_price_adjustment` ADD `old_price_2` DOUBLE(15,4) NOT NULL DEFAULT '0' AFTER `price_2`");
        $this->db->query("ALTER TABLE `oc_price_adjustment` CHANGE `price_2` `price_2` DECIMAL(15,4) NOT NULL DEFAULT '0'");
    }
}