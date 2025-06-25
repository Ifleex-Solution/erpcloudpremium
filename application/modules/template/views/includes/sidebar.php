<div class="sidebar">
    <!-- Sidebar user panel -->

    <div class="user-panel text-center">
        <div class="image">
            <?php $image = $this->session->userdata('image') ?>
            <img src="<?php echo base_url((!empty($image) ? $image : 'assets/img/icons/default.jpg')) ?>" class="img-circle" alt="User Image">
        </div>
        <div class="info">
            <p><?php echo $this->session->userdata('fullname') ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i>
                <?php echo $this->session->userdata('user_level') ?></a>
        </div>
    </div>




    <!-- sidebar menu -->
    <ul class="sidebar-menu">

        <?php if ($this->session->userdata('screen') == 1) { ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "home") ? "active" : null) ?>">
                <a href="<?php echo base_url('home') ?>"> <i class="ti-dashboard"></i>
                    <span style="margin-left: 6px;"><?php echo display('dashboard') ?></span>
                </a>
            </li>
        <?php } ?>



        <!-- Store start -->
        <?php if (
            $this->permission1->method('add_store', 'create')->access()
            || $this->permission1->method('storelist', 'read')->access()
        ) { ?>

            <li class="treeview <?php
                                if (

                                    $this->uri->segment('1') == ("storelist") || $this->uri->segment('1') == ("store_form")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="ti-truck"></i><span style="margin-left: 10px;"><?php echo display('store') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                   

                    <?php if ($this->permission1->method('add_store', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "store_form") ? "active" : '') ?>">
                            <a href="<?php echo base_url('store_form') ?>"> <?php echo display('add_store') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('storelist', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("storelist")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('storelist') ?>"><?php echo display('storelist') ?></a></li>
                    <?php } ?>


                </ul>
            </li>
        <?php } ?>





        <?php if (
            $this->permission1->method('add_branch', 'create')->access()
            ||$this->permission1->method('branch_list', 'read')->access()
           
        ) { ?>

            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("branch_form") || $this->uri->segment('1') == ("branch_list")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="ti-home" ></i><span style="margin-left: 10px;"><?php echo display('branch') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <?php if ($this->permission1->method('add_branch', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "branch_form") ? "active" : '') ?>">
                            <a href="<?php echo base_url('branch_form') ?>"> <?php echo display('add_branch') ?>

                            </a>

                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('branch_list', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("branch_list")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('branch_list') ?>"><?php echo display('branch_list') ?></a></li>
                    <?php } ?>

                   

                </ul>
            </li>
        <?php } ?>


        <!-- service menu start -->
        <?php if ($this->permission1->method('create_service', 'create')->access() || $this->permission1->method('manage_service', 'read')->access()) { ?>

            <li class="treeview <?php
                                if ($this->uri->segment('1') == ("add_service") || $this->uri->segment('1') == ("manage_service") || $this->uri->segment('1') == ("edit_service")) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="fa fa-asl-interpreting"></i><span style="margin-left: 6px;"><?php echo display('service') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('create_service', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("add_service")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('add_service') ?>"><?php echo display('add_service') ?></a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_service', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("manage_service")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('manage_service') ?>"><?php echo display('manage_service') ?></a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>



        <!-- product menu part -->
        <?php if ($this->permission1->method('create_product', 'create')->access() || $this->permission1->method('add_product_csv', 'create')->access() || $this->permission1->method('manage_product', 'read')->access()) { ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "category_form" || $this->uri->segment(1) == "category_list" || $this->uri->segment(1) == "unit_form" || $this->uri->segment(1) == "unit_list" || $this->uri->segment(1) == "product_form" || $this->uri->segment(1) == "product_list" || $this->uri->segment(1) == "barcode" || $this->uri->segment(1) == "qrcode" || $this->uri->segment(1) == "bulk_products" || $this->uri->segment(1) == "product_details") ? "active" : '') ?>">

                <a href="javascript:void(0)">

                    <i class="metismenu-icon fa fa-cubes"></i> <span style="margin-left: 2px;"><?php echo display('product') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">

                    <?php if ($this->permission1->method('category', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "category_form") ? "active" : '') ?>">
                            <a href="<?php echo base_url('category_form') ?>"> <?php echo display('add_category') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_category', 'read')->access() || $this->permission1->method('manage_category', 'update')->access() || $this->permission1->method('manage_category', 'delete')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "category_list") ? "active" : '') ?>">
                            <a href="<?php echo base_url('category_list') ?>">

                                <?php echo display('category_list') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('unit', 'create')->access() || $this->permission1->method('unit', 'update')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "unit_form") ? "active" : '') ?>">
                            <a href="<?php echo base_url('unit_form') ?>"> <?php echo display('add_unit') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_unit', 'create')->access() || $this->permission1->method('manage_unit', 'read')->access() || $this->permission1->method('manage_unit', 'delete')->access() || $this->permission1->method('manage_unit', 'update')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "unit_list") ? "active" : '') ?>">
                            <a href="<?php echo base_url('unit_list') ?>">

                                <?php echo display('unit_list') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('create_product', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "product_form") ? "active" : '') ?>">
                            <a href="<?php echo base_url('product_form') ?>">

                                <?php echo display('add_product') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('add_product_csv', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "bulk_products") ? "active" : '') ?>">
                            <a href="<?php echo base_url('bulk_products') ?>">

                                <?php echo display('add_product_csv') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_product', 'read')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "product_list") ? "active" : '') ?>">
                            <a href="<?php echo base_url('product_list') ?>">

                                <?php echo display('manage_product') ?>

                            </a>

                        </li>
                    <?php } ?>

                </ul>

            </li>
        <?php } ?>


        <!-- customer menu start-->

        <?php
        $path = 'application/modules/';
        $map  = directory_map($path);
        $HmvcMenu   = array();
        if (is_array($map) && sizeof($map) > 0)
            foreach ($map as $key => $value) {
                $menu = str_replace("\\", '/', $path . $key . 'config/menu.php');
                if (file_exists($menu)) {

                    if (file_exists(APPPATH . 'modules/' . $key . '/assets/data/env')) {
                        @include($menu);
                    }
                }
            }
        ?>
        <?php if ($this->permission1->method('add_customer', 'create')->access() || $this->permission1->method('manage_customer', 'read')->access() || $this->permission1->method('credit_customer', 'read')->access() || $this->permission1->method('paid_customer', 'read')->access() || $this->permission1->method('customer_ledger', 'read')->access() || $this->permission1->method('customer_advance', 'create')->access()) { ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "add_customer" || $this->uri->segment(1) == "customer_list" || $this->uri->segment(1) == "credit_customer" || $this->uri->segment(1) == "paid_customer" || $this->uri->segment(1) == "edit_customer" || $this->uri->segment(1) == "customer_ledgerdata" || $this->uri->segment(1) == "customer_ledger" || $this->uri->segment(1) == "advance_receipt" || $this->uri->segment(1) == "customer_advance") ? "active" : '') ?>">

                <a href="javascript:void(0)">

                    <i class="metismenu-icon pe-7s-user"></i> <span style="margin-left: 6px;"><?php echo display('customer') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">

                    <?php if ($this->permission1->method('add_customer', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "add_customer") ? "active" : '') ?>">
                            <a href="<?php echo base_url('add_customer') ?>" class="<?php echo (($this->uri->segment(1) == "add_customer") ? "active" : null) ?>">
                                <?php echo display('add_customer') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_customer', 'read')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "customer_list") ? "active" : '') ?>">
                            <a href="<?php echo base_url('customer_list') ?>">

                                <?php echo display('customer_list') ?>

                            </a>

                        </li>
                    <?php } ?>


                </ul>

            </li>
        <?php } ?>


        <!-- customer menu end -->

        <!-- supplier menu part -->
        <?php if ($this->permission1->method('add_supplier', 'create')->access() || $this->permission1->method('manage_supplier', 'read')->access() || $this->permission1->method('supplier_ledger', 'read')->access() || $this->permission1->method('supplier_advance', 'create')->access()) { ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "add_supplier" || $this->uri->segment(1) == "supplier_list" || $this->uri->segment(1) == "edit_supplier" || $this->uri->segment(1) == "supplier_ledgerdata" || $this->uri->segment(1) == "supplier_ledger" || $this->uri->segment(1) == "supplier_advance") ? "active" : '') ?>">

                <a href="javascript:void(0)">

                    <i class="metismenu-icon fa fa-user-secret"></i> <span style="margin-left: 3px;"><?php echo display('supplier') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">

                    <?php if ($this->permission1->method('add_supplier', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "add_supplier") ? "active" : '') ?>">
                            <a href="<?php echo base_url('add_supplier') ?>" class="<?php echo (($this->uri->segment(1) == "add_supplier") ? "active" : null) ?>">
                                <?php echo display('add_supplier') ?>

                            </a>

                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_supplier', 'read')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "supplier_list") ? "active" : '') ?>">
                            <a href="<?php echo base_url('supplier_list') ?>">

                                <?php echo display('supplier_list') ?>

                            </a>

                        </li>
                    <?php } ?>



                </ul>

            </li>
        <?php } ?>

        <!-- Payment Method Menu Start -->
        <?php if ($this->permission1->method('add_payment_method', 'create')->access() || $this->permission1->method('payment_method_list', 'read')->access()) { ?>
            <li class="treeview <?php
                                if ($this->uri->segment('1') == ("add_payment_method") || $this->uri->segment('1') == ("payment_method_list")) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="fa fa-book"></i><span style="margin-left: 5px;"><?php echo display('payment_method') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('add_payment_method', 'create')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("add_payment_method")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('add_payment_method') ?>"><?php echo display('add_payment_method'); ?></a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('payment_method_list', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("payment_method_list")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('payment_method_list') ?>"><?php echo display('payment_method_list'); ?></a>
                        </li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>
        <!-- Payment Menu end -->




        <!-- Stock menu part -->
        <?php if (
            $this->permission1->method('new_stock_adjustment', 'create')->access()
            ||  $this->permission1->method('new_grn', 'create')->access()
            || $this->permission1->method('manage_stock_adjustment', 'read')->access()
            || $this->permission1->method('manage_grn', 'read')->access()

        ) { ?>
            <li class="treeview <?php echo (
                                    $this->uri->segment(1) == "newstockadjustment_form"
                                    || $this->uri->segment(1) == "new_grn"
                                    || $this->uri->segment(1) == "new_gdn"
                                    ||  $this->uri->segment(1) == "manage_stock_adjustment"
                                    || $this->uri->segment(1) == "manage_grn"
                                    || $this->uri->segment(1) == "manage_gdn"

                                    ? "active" : '') ?>">

                <a href="javascript:void(0)">

                    <i class="ti-harddrives"></i> <span style="margin-left: 6px;"><?php echo display('stock') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('new_stock_adjustment', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "newstockadjustment_form") ? "active" : '') ?>">
                            <a href="<?php echo base_url('newstockadjustment_form') ?>"> <?php echo display('new_stock_adjustment') ?> </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('manage_stock_adjustment', 'read')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "manage_stock_adjustment") ? "active" : '') ?>">
                            <a href="<?php echo base_url('manage_stock_adjustment') ?>"> <?php echo display('manage_stock_adjustment') ?> </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('new_grn', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "new_grn") ? "active" : '') ?>">
                            <a href="<?php echo base_url('new_grn') ?>"> <?php echo display('new_grn') ?> </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('manage_grn', 'read')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "manage_grn") ? "active" : '') ?>">
                            <a href="<?php echo base_url('manage_grn') ?>"> <?php echo display('manage_grn') ?> </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('new_gdn', 'create')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "new_gdn") ? "active" : '') ?>">
                            <a href="<?php echo base_url('new_gdn') ?>"> <?php echo display('new_gdn') ?> </a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('manage_gdn', 'read')->access()) { ?>
                        <li class="<?php echo (($this->uri->segment(1) == "manage_gdn") ? "active" : '') ?>">
                            <a href="<?php echo base_url('manage_gdn') ?>"> <?php echo display('manage_gdn') ?> </a>
                        </li>
                    <?php } ?>

                </ul>

            </li>
        <?php } ?>







        <!-- Purchase menu start -->
        <?php if (
            $this->permission1->method('add_purchase', 'create')->access()
            || $this->permission1->method('manage_purchase', 'read')->access()
            || $this->permission1->method('new_purchase_order', 'create')->access()
            || $this->permission1->method('manage_purchase_order', 'read')->access()
        ) { ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("add_purchase")
                                    || $this->uri->segment('1') == ("purchase_edit")
                                    || $this->uri->segment('1') == ("purchase_list")
                                    || $this->uri->segment('1') == ("purchase_details")
                                    || $this->uri->segment('1') == ("new_purchase_order")
                                    || $this->uri->segment('1') == ("manage_purchase_order")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="ti-shopping-cart"></i><span style="margin-left: 8px;"><?php echo display('purchase') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('add_purchase', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("add_purchase")) {
                                                echo "active";
                                            } else {
                                                echo "";
                                            }
                                            ?>"><a href="<?php echo base_url('add_purchase') ?>"><?php echo display('add_purchase') ?></a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_purchase', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("purchase_list")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('purchase_list') ?>"><?php echo display('manage_purchase') ?></a></li>
                    <?php } ?>


                </ul>
            </li>
        <?php } ?>
        <!-- Purchase menu end -->








        <!-- Invoice menu start -->
        <?php if ($this->permission1->method('new_invoice', 'create')->access()||
        $this->permission1->method('new_pos', 'create')->access() 
        || $this->permission1->method('manage_invoice', 'read')->access()) { ?>
            <li class="treeview <?php
                                if ($this->uri->segment('1') == ("add_invoice")||$this->uri->segment('1') == ("new_pos") || $this->uri->segment('1') == ("invoice_list")  || $this->uri->segment('1') == ("invoice_details") || $this->uri->segment('1') == ("invoice_pad_print") || $this->uri->segment('1') == ("pos_print") || $this->uri->segment('1') == ("invoice_edit")) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="fa fa-balance-scale"></i><span style="margin-left: 4px;"><?php echo display('invoice') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('new_invoice', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("add_invoice")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('add_invoice') ?>"><?php echo display('new_invoice') ?></a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('new_pos', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("new_pos")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('new_pos') ?>"><?php echo display('new_pos') ?></a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_invoice', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("invoice_list")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('invoice_list') ?>"><?php echo display('manage_invoice') ?></a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>




        <!-- service menu start -->
        <?php if ($this->permission1->method('service_invoice', 'create')->access() || $this->permission1->method('manage_service_invoice', 'read')->access()) { ?>

            <li class="treeview <?php
                                if ($this->uri->segment('1') == ("add_service_invoice") || $this->uri->segment('1') == ("service_details") || $this->uri->segment('1') == ("manage_service_invoice") || $this->uri->segment('1') == ("edit_service_invoice")) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="fa fa-asl-interpreting"></i><span style="margin-left: 4px;"><?php echo display('sales_of_service') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('service_invoice', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("add_service_invoice")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('add_service_invoice') ?>"><?php echo display('service_invoice') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_service_invoice', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("manage_service_invoice")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a href="<?php echo base_url('manage_service_invoice') ?>"><?php echo display('manage_service_invoice') ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>













        <!-- human resource management menu start -->
        <?php if (
            $this->permission1->method('add_designation', 'create')->access() || $this->permission1->method('manage_designation', 'read')->access()
            || $this->permission1->method('add_employee', 'create')->access() || $this->permission1->method('manage_employee', 'read')->access()
        ) { ?>
            <!-- Supplier menu start -->
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("designation_form") || $this->uri->segment('1') == ("designation_list")
                                    || $this->uri->segment('1') == ("employee_form") || $this->uri->segment('1') == ("employee_list")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="fa fa-users"></i><span style="margin-left: 4px;"><?php echo display('hrm_management') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('add_designation', 'create')->access() || $this->permission1->method('manage_designation', 'read')->access()  || $this->permission1->method('add_employee', 'create')->access() || $this->permission1->method('manage_employee', 'read')->access()) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("designation_form") || $this->uri->segment('1') == ("designation_list") ||  $this->uri->segment('1') == ("employee_form") || $this->uri->segment('1') == ("employee_list")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="fa fa-users"></i><span><?php echo display('hrm') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('add_designation', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("designation_form")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('designation_form') ?>"><?php echo display('add_designation') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('manage_designation', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("designation_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('designation_list') ?>"><?php echo display('manage_designation') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('add_employee', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("employee_form")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('employee_form') ?>"><?php echo display('add_employee') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('manage_employee', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("employee_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('employee_list') ?>"><?php echo display('manage_employee') ?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>



                    <!-- =============================== Payroll menu end =================== -->

                    <!--  Personal loan start -->

                    <!-- loan end -->
                </ul>
            </li>
        <?php } ?>
        <!-- Human resource management menu end -->
        <!-- Report menu start -->


        <!-- service menu start -->
        <?php if (
            $this->permission1->method('closing_report', 'read')->access() || $this->permission1->method('todays_report', 'read')->access()
            || $this->permission1->method('income_statement_form', 'read')->access()
            || $this->permission1->method('expenditure_statement', 'read')->access()
            || $this->permission1->method('receipt_payment', 'read')->access()
            || $this->permission1->method('balance_sheet', 'read')->access() || $this->permission1->method('product_wise_sales_report', 'read')->access()
            || $this->permission1->method('fixedasset_schedule', 'read')->access()
            || $this->permission1->method('bank_reconciliation_report', 'read')->access()
            || $this->permission1->method('cheque_flow__report', 'create')->access() || $this->permission1->method('stock', 'read')->access() || $this->permission1->method('stock_report', 'read')->access()
            || $this->permission1->method('todays_customer_receipt', 'read')->access() || $this->permission1->method('todays_sales_report', 'read')->access() || $this->permission1->method('due_report', 'read')->access() || $this->permission1->method('todays_purchase_report', 'read')->access() || $this->permission1->method('purchase_report_category_wise', 'read')->access() || $this->permission1->method('product_sales_reports_date_wise', 'read')->access() || $this->permission1->method('sales_report_category_wise', 'read')->access()
            || $this->permission1->method('shipping_cost_report', 'read')->access()  || $this->permission1->method('live_stock_report', 'read')->access()
            || $this->permission1->method('stock_audit_report', 'read')->access()

        ) { ?>

            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("cash_book") || $this->uri->segment('1') == ("cash_book_report")
                                    || $this->uri->segment('1') == ("day_book") || $this->uri->segment('1') == ("day_book_report")
                                    || $this->uri->segment('1') == ("bank_book") || $this->uri->segment('1') == ("bank_book_report") ||
                                    $this->uri->segment('1') == ("general_ledger") || $this->uri->segment('1') == ("sub_ledger") || $this->uri->segment('1') == ("sub_ledger_report") || $this->uri->segment('1') == ("trial_balance") || $this->uri->segment('1') == ("coa_print")
                                    || $this->uri->segment('1') == ("trial_balance_report") || $this->uri->segment('1') == ("accounts_report_search") ||
                                    $this->uri->segment('1') == ("income_statement_form") || $this->uri->segment('1') == ("income_statement")
                                    ||  $this->uri->segment('1') == ("expenditure_statement") ||  $this->uri->segment('1') == ("expenditure_statement_report")
                                    || $this->uri->segment('1') == ("receipt_payment") || $this->uri->segment('1') == ("receipt_payment_report")
                                    || $this->uri->segment('1') == ("profit_loss_report_search") || $this->uri->segment('1') == ("profit_loss_report")
                                    || $this->uri->segment('1') == ("balance_sheet") ||  $this->uri->segment('1') == ("fixedasset_schedule")
                                    || $this->uri->segment('1') == ("bank_reconciliation_report") ||
                                    $this->uri->segment('1') == ("chequeflowreport") || $this->uri->segment('1') == ("stock") ||
                                    $this->uri->segment('1') == ("closing_report") || $this->uri->segment('1') == ("closing_report_search")  || $this->uri->segment('1') == ("live_stock_report") || $this->uri->segment('1') == ("todays_report") || $this->uri->segment('1') == ("todays_customer_received") || $this->uri->segment('1') == ("todays_customerwise_received") || $this->uri->segment('1') == ("sales_report") || $this->uri->segment('1') == ("datewise_sales_report") || $this->uri->segment('1') == ("userwise_sales_report") || $this->uri->segment('1') == ("invoice_wise_due_report") || $this->uri->segment('1') == ("shipping_cost_report") || $this->uri->segment('1') == ("purchase_report") || $this->uri->segment('1') == ("purchase_report_categorywise") || $this->uri->segment('1') == ("product_wise_sales_report") || $this->uri->segment('1') == ("category_sales_report") || $this->uri->segment('1') == ("sales_return") || $this->uri->segment('1') == ("supplier_returns") || $this->uri->segment('1') == ("tax_report") || $this->uri->segment('1') == ("profit_report")
                                    || $this->uri->segment('1') == ("stock_audit_report")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="fa fa-asl-interpreting"></i><span style="margin-left: 6px;"><?php echo display('report') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <?php if ($this->permission1->method('cash_book', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("cash_book")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('cash_book') ?>"><?php echo display('cash_book'); ?></a></li>
                    <?php } ?>

                    <?php if ($this->permission1->method('todays_sales_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("sales_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('sales_report') ?>"><?php echo display('sales_report') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('user_wise_sales_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("userwise_sales_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('userwise_sales_report') ?>"><?php echo display('user_wise_sales_report') ?></a>
                        </li>
                    <?php } ?>

                    <?php if ($this->permission1->method('product_wise_sales_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("product_wise_sales_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('product_wise_sales_report') ?>"><?php echo display('product_wise_sales_report') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('sales_report_category_wise', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("category_sales_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('category_sales_report') ?>"><?php echo display('sales_report_category_wise') ?></a>
                        </li>
                    <?php } ?>



                    <?php if ($this->permission1->method('todays_purchase_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('purchase_report') ?>"><?php echo display('purchase_report') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('purchase_report_category_wise', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_report_categorywise")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('purchase_report_categorywise') ?>"><?php echo display('purchase_report_category_wise') ?></a>
                        </li>
                    <?php } ?>




                    <?php if ($this->permission1->method('stock_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("stock")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('stock') ?>"><?php echo display('stock_report') ?></a></li>
                    <?php } ?>

                    <?php if ($this->permission1->method('live_stock_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("live_stock_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('live_stock_report') ?>"><?php echo display('live_stock_report') ?></a></li>
                    <?php } ?>

                    <?php if ($this->permission1->method('stock_audit_report', 'read')->access()) { ?>
                        <li class="treeview <?php if ($this->uri->segment('1') == ("stock_audit_report")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            } ?>"><a href="<?php echo base_url('stock_audit_report') ?>"><?php echo display('stock_audit_report') ?></a></li>
                    <?php } ?>




                </ul>
            </li>
        <?php } ?>



        <!-- Report menu end -->








        <!-- Comission end -->





        <!-- Software Settings menu start -->
        <?php if (
            $this->permission1->method('manage_company', 'read')->access() || $this->permission1->method('manage_company', 'create')->access()
            || $this->permission1->method('add_user', 'create')->access() || $this->permission1->method('add_user', 'read')->access() || $this->permission1->method('add_language', 'create')->access() || $this->permission1->method('add_currency', 'create')->access() || $this->permission1->method('soft_setting', 'create')->access() || $this->permission1->method('add_role', 'create')->access() || $this->permission1->method('role_list', 'read')->access() || $this->permission1->method('user_assign', 'create')->access()
            || $this->permission1->method('sms_configure', 'create')->access() || $this->permission1->method('add_incometax', 'create')->access() || $this->permission1->method('manage_income_tax', 'read')->access() || $this->permission1->method('tax_settings', 'create')->access()
            || $this->permission1->method('tax_report', 'read')->access() || $this->permission1->method('invoice_wise_tax_report', 'read')->access() || $this->permission1->method('tax_settings', 'read')->access()
            || $this->permission1->method('vat_tax_setting', 'read')->access() || $this->permission1->method('add_incometax', 'create')->access() || $this->permission1->method('manage_income_tax', 'read')->access() || $this->permission1->method('tax_settings', 'create')->access() || $this->permission1->method('tax_report', 'read')->access() || $this->permission1->method('invoice_wise_tax_report', 'read')->access() || $this->permission1->method('tax_settings', 'read')->access() || $this->permission1->method('vat_tax_setting', 'read')->access()
            || $this->permission1->method('user_assign_branch', 'read')->access()   || $this->permission1->method('user_assign_store', 'read')->access()
        ) { ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("company_list") || $this->uri->segment('1') == ("edit_company")
                                    || $this->uri->segment('1') == ("add_user") || $this->uri->segment('1') == ("user_list") || $this->uri->segment('1') == ("language")
                                    || $this->uri->segment('1') == ("currency_form") || $this->uri->segment('1') == ("currency_list") || $this->uri->segment('1') == ("settings")
                                    || $this->uri->segment('1') == ("mail_setting") || $this->uri->segment('1') == ("app_setting") || $this->uri->segment('1') == ("add_role") || $this->uri->segment('1') == ("role_list")
                                    || $this->uri->segment('1') == ("edit_role") || $this->uri->segment('1') == ("assign_role") || $this->uri->segment('1') == ("sms_setting") || $this->uri->segment('1') == ("restore")
                                    || $this->uri->segment('1') == ("db_import") || $this->uri->segment('1') == ("editPhrase") || $this->uri->segment('1') == ("phrases") || $this->uri->segment('1') == ("invoice_wise_tax_report") || $this->uri->segment('1') == ("tax_setting")
                                    || $this->uri->segment('1') == ("income_tax") || $this->uri->segment('1') == ("manage_income_tax")
                                    || $this->uri->segment('1') == ("tax_reports") || $this->uri->segment('1') == ("update_tax_setting")
                                    || $this->uri->segment('1') == ("vat_tax_setting")||$this->uri->segment('1') == ("user_assign_branch")
                                    ||$this->uri->segment('1') == ("user_assign_store") 
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="#">
                    <i class="ti-settings"></i><span style="margin-left: 10px;"><?php echo display('settings_preferences') ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <!-- Software Settings menu start -->
                    <?php if (
                        $this->permission1->method('manage_company', 'read')->access() || $this->permission1->method('manage_company', 'create')->access()
                        || $this->permission1->method('add_language', 'create')->access() || $this->permission1->method('add_currency', 'create')->access() || $this->permission1->method('soft_setting', 'create')->access() || $this->permission1->method('back_up', 'create')->access() || $this->permission1->method('back_up', 'read')->access() || $this->permission1->method('restore', 'create')->access() || $this->permission1->method('sql_import', 'create')->access()
                    ) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("company_list") || $this->uri->segment('1') == ("edit_company")  || $this->uri->segment('1') == ("language") || $this->uri->segment('1') == ("currency_form") || $this->uri->segment('1') == ("currency_list") || $this->uri->segment('1') == ("settings") || $this->uri->segment('1') == ("mail_setting") || $this->uri->segment('1') == ("app_setting") || $this->uri->segment('1') == ("editPhrase") || $this->uri->segment('1') == ("phrases")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="ti-settings"></i> <span><?php echo display('web_settings') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('manage_company', 'read')->access() || $this->permission1->method('manage_company', 'update')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("company_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('company_list') ?>"><?php echo display('manage_company') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('add_language', 'create')->access() || $this->permission1->method('add_language', 'update')->access()) { ?>
                                    <li class="<?php echo (($this->uri->segment(1) == "language" || $this->uri->segment('1') == ("editPhrase") || $this->uri->segment('1') == ("phrases")) ? "active" : '') ?>">
                                        <a href="<?php echo base_url('language') ?>">

                                            <?php echo display('language') ?>

                                        </a>

                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('soft_setting', 'create')->access() || $this->permission1->method('soft_setting', 'update')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("settings")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>">
                                        <a href="<?php echo base_url('settings') ?>" class="<?php echo (($this->uri->segment(1) == "settings") ? "active" : null) ?>">

                                            <?php echo display('settings') ?>

                                        </a>

                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('mail_setting', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("mail_setting")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('mail_setting') ?>"><?php echo display('mail_setting') ?> </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>


                    <!-- Tax permission start -->
                    <?php if ($this->permission1->method('add_incometax', 'create')->access() || $this->permission1->method('manage_income_tax', 'read')->access() || $this->permission1->method('tax_settings', 'create')->access() || $this->permission1->method('tax_report', 'read')->access() || $this->permission1->method('invoice_wise_tax_report', 'read')->access() || $this->permission1->method('tax_settings', 'read')->access() || $this->permission1->method('vat_tax_setting', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if (($this->uri->segment('1') == ("invoice_wise_tax_report") || $this->uri->segment('1') == ("tax_setting")
                                                || $this->uri->segment('1') == ("income_tax") || $this->uri->segment('1') == ("manage_income_tax")
                                                || $this->uri->segment('1') == ("tax_reports") || $this->uri->segment('1') == ("update_tax_setting")
                                                || $this->uri->segment('1') == ("vat_tax_setting"))) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="fa fa-money"></i> <span><?php echo display('tax') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('tax_settings', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("vat_tax_setting")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('vat_tax_setting') ?>"><?php echo display('vat_tax_setting') ?></a></li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>
                    <!-- Tax permission End -->


                    <!-- Role permission start -->
                    <?php if (
                        $this->permission1->method('add_role', 'create')->access()
                        || $this->permission1->method('role_list', 'read')->access() || $this->permission1->method('add_user', 'create')->access() || $this->permission1->method('manage_user', 'read')->access()
                        || $this->permission1->method('edit_role', 'create')->access() || $this->permission1->method('assign_role', 'create')->access()
                        || $this->permission1->method('user_assign_branch', 'read')->access() || $this->permission1->method('user_assign_store', 'read')->access()
                    ) { ?>
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("add_role") || $this->uri->segment('1') == ("role_list") || $this->uri->segment('1') == ("edit_role") || $this->uri->segment('1') == ("assign_role")
                                                || $this->uri->segment('1') == ("add_user") || $this->uri->segment('1') == ("user_list")|| $this->uri->segment('1') == ("user_assign_branch")||$this->uri->segment('1') == ("user_assign_store")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="ti-key"></i> <span><?php echo display('area_responsibility') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('add_user', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("add_user")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('add_user') ?>"><?php echo display('add_user') ?></a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('manage_user', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("user_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('user_list') ?>"><?php echo display('manage_users') ?> </a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('add_role', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("add_role")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('add_role') ?>"><?php echo display('add_role') ?></a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('role_list', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("role_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('role_list') ?>"><?php echo display('role_list') ?></a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('user_assign', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("assign_role")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('assign_role') ?>"><?php echo display('user_assign_role') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('user_assign_store', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("user_assign_store")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('user_assign_store') ?>"><?php echo display('user_assign_store') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('user_assign_branch', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("user_assign_branch")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('user_assign_branch') ?>"><?php echo display('user_assign_branch') ?></a>
                                    </li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>
                    <!-- Role permission End -->

                    <!-- Synchronizer setting start -->
                    <?php if ($this->permission1->method('restore', 'create')->access() || $this->permission1->method('sql_import', 'create')->access() || $this->permission1->method('sql_import', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("restore") || $this->uri->segment('1') == ("db_import")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="ti-reload"></i> <span><?php echo display('data_synchronizer') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">



                                <li class="treeview <?php if ($this->uri->segment('2') == ("backup_create")) {
                                                        echo "active";
                                                    } else {
                                                        echo " ";
                                                    } ?>"><a href="<?php echo base_url('dashboard/backup_restore/download_backup') ?>"><?php echo display('backup') ?></a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    <!-- Synchronizer setting end -->

                </ul>
            </li>
        <?php } ?>
        <!-- Software Settings menu end -->





    </ul>
</div> <!-- /.sidebar -->