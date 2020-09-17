<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Payment_methods extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE `oc_payment_method` (
            `payment_method_id` int(11) NOT NULL,
            `payment_method_name` varchar(100) NOT NULL,
            `payment_method_description` text NOT NULL,
            `status` enum('active','nonactive','void') NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
        $this->db->query("INSERT INTO `oc_payment_method` 
            (`payment_method_id`, `payment_method_name`, `payment_method_description`, `status`)
            VALUES 
                (1, 'Kredit-14 hari', 'testing 14 hari', 'active'),
                (2, 'Kredit-7 hari', '', 'active'),
                (3, 'Kredit-30 Hari', '', 'active'),
                (4, 'Cash', 'Testing desc', 'active'),
                (5, 'Debit', '', 'active'),
                (6, 'Testing', 'hallo', 'void')");
        $this->db->query("ALTER TABLE `oc_payment_method` ADD PRIMARY KEY (`payment_method_id`)");
        $this->db->query("ALTER TABLE `oc_payment_method` MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7");
    }
}