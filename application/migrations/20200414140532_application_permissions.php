<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Application_permissions extends CI_Migration
{
    public function up()
    {
        $this->load->library('Rbac_manager', 'rbac_manager');
        $this->rbac_manager->add_permission('read_promo', 'Read Promo');

        /** supplier */
        $this->rbac_manager->add_permission('read_supplier', 'Read Supplier');
        $this->rbac_manager->add_permission('create_supplier', 'Create Supplier');
        $this->rbac_manager->add_permission('update_supplier', 'Update Supplier');
        $this->rbac_manager->add_permission('delete_supplier', 'Delete Supplier');

        /** category */
        $this->rbac_manager->add_permission('read_category', 'Read Category');
        $this->rbac_manager->add_permission('create_category', 'Create Category');
        $this->rbac_manager->add_permission('update_category', 'Update Category');
        $this->rbac_manager->add_permission('delete_category', 'Delete Category');

        /** Option */
        $this->rbac_manager->add_permission('read_option', 'Read Option');
        $this->rbac_manager->add_permission('create_option', 'Create Option');
        $this->rbac_manager->add_permission('update_option', 'Update Option');
        $this->rbac_manager->add_permission('delete_option', 'Delete Option');

        /** product */
        $this->rbac_manager->add_permission('read_product', 'Read Product');
        $this->rbac_manager->add_permission('create_product', 'Create Product');
        $this->rbac_manager->add_permission('update_product', 'Update Product');
        $this->rbac_manager->add_permission('delete_product', 'Delete Prdouct');

        /** unit measurement */
        $this->rbac_manager->add_permission('read_unit_measurement', 'Read Unit Measurement');
        $this->rbac_manager->add_permission('create_unit_measurement', 'Create Unit Measurement');
        $this->rbac_manager->add_permission('update_unit_measurement', 'Update Unit Measurement');
        $this->rbac_manager->add_permission('delete_unit_measurement', 'Delete Unit Measurement');
    }
}