<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Stock_adjustment extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('stock_adjustment')) {
            $this->db->query("DROP TABLE {PRE}stock_adjustment");
        }
  
        $this->db->query("CREATE TABLE `oc_stock_adjustment` (
            `stock_adjustment_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `stock_adjustment_date` date NOT NULL,
            `original_stock` int(11) NOT NULL,
            `stock_adjust` int(11) NOT NULL,
            `last_stock` int(11) NOT NULL,
            `status_adjust` int(11) NOT NULL,
            `created_at` datetime NOT NULL,
            `qty_unit_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $this->db->query("ALTER TABLE `oc_stock_adjustment` ADD PRIMARY KEY (`stock_adjustment_id`)");
        $this->db->query("ALTER TABLE `oc_stock_adjustment` MODIFY `stock_adjustment_id` int(11) NOT NULL AUTO_INCREMENT");
    }
}

