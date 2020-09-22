<?php
$indentity = get_identity();
?>
<section class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
        <div class="pull-left image">
            <img src="<?= base_url("assets/img/avatar5.png") ?>" class="user-image" alt="User Image"><br/>
        </div>
        <div class="pull-left info">
          <p><?= $indentity->first_name. ' '.$indentity->last_name ?></p>
        </div>
    </div>
      
    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <!-- Optionally, you can add icons to the links -->
        <li <?=set_active('home')?>>
          <a href="<?= site_url('home') ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        <li class="<?= set_group_active('po-module') ?> treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i> <span>Transaksi</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li>
                <a href="<?= site_url('payment_method') ?>">
                    <i class="fa fa-circle-o"></i><span>Metode Pembayaran</span>
                </a>
            </li>               
            <li>
                <a href="<?= site_url('purchase_order') ?>">
                    <i class="fa fa-circle-o"></i><span>Purchase Order</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('goods_receipt') ?>">
                    <i class="fa fa-circle-o"></i><span>Penerimaan Barang</span>
                </a>
            </li>                     
            <li>
                <a href="<?= site_url('purchasing') ?>">
                    <i class="fa fa-circle-o"></i><span>Pembelian</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('retur') ?>">
                    <i class="fa fa-circle-o"></i><span>Retur Pembelian</span>
                </a>
            </li>
          </ul>
        </li>
        <li class="<?= set_group_active('reports') ?> treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i> <span>Laporan</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li>
                <a href="<?= site_url('reports/purchasing') ?>">
                    <i class="fa fa-circle-o"></i><span>Pembelian</span>
                </a>
            </li>               
            <li>
                <a href="<?= site_url('reports/return_purchasing') ?>">
                    <i class="fa fa-circle-o"></i><span>Retur Pembelian</span>
                </a>
            </li>   
            <li>
                <a href="<?= site_url('reports/product_stock') ?>">
                    <i class="fa fa-circle-o"></i><span>Daftar Stok Produk</span>
                </a>
            </li>                           
            <li>
                <a href="<?= site_url('reports/card_stock') ?>">
                    <i class="fa fa-circle-o"></i><span>Kartu Stok</span>
                </a>
            </li>
            <li>
                <a href="<?= site_url('reports/stock/stock_product') ?>">
                    <i class="fa fa-circle-o"></i><span>Stok Produk Supplier</span>
                </a>
            </li> 
            <li>
                <a href="<?= site_url('reports/stock/card') ?>">
                    <i class="fa fa-circle-o"></i><span>Stok Opname</span>
                </a>
            </li> 
            <li>
                <a href="<?= site_url('reports/stock/card') ?>">
                    <i class="fa fa-circle-o"></i><span>Laba-rugi Penjualan</span>
                </a>
            </li>  
            <li>
                <a href="<?= site_url('reports/label_harga') ?>">
                    <i class="fa fa-circle-o"></i><span>Label Harga (Freelancer)</span>
                </a>
            </li>   
            <li>
                <a href="<?= site_url('reports/nilai_stock') ?>">
                    <i class="fa fa-circle-o"></i><span>Nilai Stock (Freelancer)</span>
                </a>
            </li>   
            <li>
                <a href="<?= site_url('reports/stock_supplier') ?>">
                    <i class="fa fa-circle-o"></i><span>Stock Produk Supplier (Freelancer)</span>
                </a>
            </li>    
            <li>
                <a href="<?= site_url('reports/stock_with_gambar') ?>">
                    <i class="fa fa-circle-o"></i><span>Stock With Gambar (Freelancer)</span>
                </a>
            </li> 
            <li>
                <a href="<?= site_url('reports/retur_pembelian') ?>">
                    <i class="fa fa-circle-o"></i><span>Retur Pembelian (Freelancer)</span>
                </a>
            </li>                                                                               
          </ul>
        </li>    
    </ul>
    <!-- /.sidebar-menu -->
</section>