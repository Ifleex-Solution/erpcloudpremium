<?php
defined('BASEPATH') OR exit('No direct script access allowed');



$route['add_purchase']         = "purchase/purchase/bdtask_purchase_form";
$route['purchase_list']        = "purchase/purchase/bdtask_purchase_list";
$route['purchase_details/(:num)'] = 'purchase/purchase/bdtask_purchase_details/$1';
$route['purchase_edit/(:any)'] = 'purchase/purchase/bdtask_purchase_form/$1';



//purchase order part
// $route['new_purchase_order']         = "purchase/purchase/bdtask_new_purchase_order";
// $route['new_purchase_order/(:any)']  = "purchase/purchase/bdtask_new_purchase_order/$1";
// $route['manage_purchase_order']         = "purchase/purchase/bdtask_manage_purchase_order";






