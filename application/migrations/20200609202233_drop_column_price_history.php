<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Drop_column_price_history extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {PRE}price_adjustment DROP FOREIGN KEY `fk_goods_receipt_price_adjustment`");
        $this->dbforge->drop_column('price_adjustment', 'goods_receipt_id');
    }
}