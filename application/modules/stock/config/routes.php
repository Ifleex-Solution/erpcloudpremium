<?php
defined('BASEPATH') OR exit('No direct script access allowed');






//stock adjustment part
$route['newstockadjustment_form']         = "stock/stock/bdtask_newstockadjustment_form";
$route['newstockadjustment_form/(:any)']  = "stock/stock/bdtask_newstockadjustment_form/$1";
$route['manage_stock_adjustment']         = "stock/stock/bdtask_manage_stock_adjustment";

//GRN
$route['new_grn']         = "stock/stock/bdtask_newgrn_form";
$route['new_grn/(:any)']  = "stock/stock/bdtask_newgrn_form/$1";
$route['manage_grn']         = "stock/stock/bdtask_manage_grn";


//GDN
$route['new_gdn']         = "stock/stock/bdtask_newgdn_form";
$route['new_gdn/(:any)']  = "stock/stock/bdtask_newgdn_form/$1";
$route['manage_gdn']         = "stock/stock/bdtask_manage_gdn";






