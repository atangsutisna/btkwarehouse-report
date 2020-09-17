<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_No_reference_stock_card extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `oc_mutation_stock` ADD `no_reference` VARCHAR(250) NULL AFTER `mutation_date`");
    }
}