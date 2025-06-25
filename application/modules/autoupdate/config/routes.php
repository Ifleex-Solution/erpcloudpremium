<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['bank_form']               = "bank/bank/bdtask_bank_form";
$route['bank_form/(:num)']        = "bank/bank/bdtask_bank_form/$1";
$route['bank_list']               = "bank/bank/bdtask_bank_list";
$route['bank_transaction']        = "bank/bank/bdtask_bank_transaction";
$route['bank_ledger']             = "bank/bank/bank_ledger";
$route['3rdpartybanks']             = "bank/bank/bdtask_3rdPartybanks";
$route['3rdpartybranches']             = "bank/bank/bdtask_3rdPartybranches";





