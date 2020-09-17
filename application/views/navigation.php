<?php
$indentity = get_identity();
?>
<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <!-- The user image in the navbar-->
                    <img src="<?= base_url("assets/img/avatar5.png") ?>" class="user-image" alt="User Image">
                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                    <span class="hidden-xs">
                        <?= $indentity->first_name. ' '.$indentity->last_name ?>
                    </span>
                </a>
                <ul class="dropdown-menu">
                    <!-- The user image in the menu -->
                    <li class="user-header" style="height: 110px;">
                        <img src="<?= base_url("assets/img/avatar5.png") ?>" class="user-image" alt="User Image"><br/>
                        <p>
                        <?= $indentity->first_name. ' '.$indentity->last_name ?>
                        <small>Operator/Admin</small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-right">
                            <a href="<?= site_url('profile') ?>" class="btn btn-default btn-flat">Profile</a>
                            <a href="<?= site_url('auth/logout') ?>" class="btn btn-default btn-flat">Sign out</a>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
        </ul>
    </div>
</nav>