<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rfs_operator extends CI_Migration
{
    public function up()
    {
        $this->dbforge->drop_column('return_from_storefront', 'created_by');

        $this->dbforge->add_column('return_from_storefront', [
            'operator_id' => ['type' => 'varchar(100)'],
            'operator_name' => ['type' => 'varchar(100)'],
        ]);
    }
}