<?php
defined('BASEPATH') OR exit('No direct script access allowed');



//branch part
$route['branch_form']         = "store/store/bdtask_branch_form";
$route['branch_form/(:any)']  = "store/store/bdtask_branch_form/$1";
$route['branch_list']         = "store/store/bdtask_branchlist";


// //store type part
// $route['storetype_form']         = "store/store/bdtask_storetype_form";
// $route['storetype_form/(:any)']  = "store/store/bdtask_storetype_form/$1";
// $route['storetypelist']         = "store/store/bdtask_storetypelist";



// //floor part
// $route['floor_form']         = "store/store/bdtask_floor_form";
// $route['floor_form/(:any)']  = "store/store/bdtask_floor_form/$1";
// $route['floorlist']         = "store/store/bdtask_floorlist";



//store part
$route['store_form']         = "store/store/bdtask_store_form";
$route['store_form/(:any)']  = "store/store/bdtask_store_form/$1";
$route['storelist']         = "store/store/bdtask_storelist";