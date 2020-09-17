<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Application_permissions extends CI_Migration
{
    public function up()
    {
        $this->load->library('Rbac_manager', 'rbac_manager');
        /** create user */
        $this->rbac_manager->add_permission('read_user', 'Read User');
        $this->rbac_manager->add_permission('create_user', 'Create User');
        $this->rbac_manager->add_permission('update_user', 'Update User');
        $this->rbac_manager->add_permission('delete_user', 'Delete User');

        /** role */
        $this->rbac_manager->add_permission('read_role', 'Read Role');

        /** create user */
        $this->rbac_manager->add_permission('read_code', 'Read Pengkodean');
        $this->rbac_manager->add_permission('update_code', 'Update Pengkodean');

        /** update price */
        $this->rbac_manager->add_permission('update_price', 'Update Price Product');

        /** product rasio or unit convertion */
        $this->rbac_manager->add_permission('read_unit_convertion', 'Read Return Supplier');
        $this->rbac_manager->add_permission('create_unit_convertion', 'Create Unit Convertion');
        $this->rbac_manager->add_permission('update_unit_convertion', 'Update Unit Convertion');
        $this->rbac_manager->add_permission('delete_unit_convertion', 'Delete Unit Convertion');

        /** return supplier */
        $this->rbac_manager->add_permission('read_return_supplier', 'Read Return Supplier');
        $this->rbac_manager->add_permission('create_return_supplier', 'Create Return Supplier');
        $this->rbac_manager->add_permission('update_return_supplier', 'Update Return Supplier');
        $this->rbac_manager->add_permission('delete_return_supplier', 'Delete Return Supplier');

        /** purchase order */
        $this->rbac_manager->add_permission('read_purchase_order', 'Read Purchase Order');
        $this->rbac_manager->add_permission('create_purchase_order', 'Create Purchase Order');
        $this->rbac_manager->add_permission('update_purchase_order', 'Update Purchase Order');
        $this->rbac_manager->add_permission('delete_purchase_order', 'Delete Purchase Order');

        /** goods receipt */
        $this->rbac_manager->add_permission('read_goods_receipt', 'Read Goods Receipt');
        $this->rbac_manager->add_permission('create_goods_receipt', 'Create Goods Receipt');
        $this->rbac_manager->add_permission('update_goods_receipt', 'Update Goods Receipt');
        $this->rbac_manager->add_permission('delete_goods_receipt', 'Delete Goods Receipt');

        /** 
        $this->rbac_manager->add_permission('read_stock_storefront', 'Read Stock Store Frotn');
        **/

        /** stock opname */
        $this->rbac_manager->add_permission('read_stock_opname', 'Read Stock Opname');
        $this->rbac_manager->add_permission('create_stock_opname', 'Create Stock Opname');

        /** inventory balance */
        $this->rbac_manager->add_permission('read_inventory_balance', 'Read Inventory Balance');
    }
}