<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $route['reports/(:num)'] = 'report/report/bdtask_purchase_edit_form/$1';
// $route['closing_form']   = "report/report/bdtask_cash_closing";
// $route['closing_report'] = "report/report/bdtask_closing_report";
// $route['closing_report_search'] = "report/report/bdtask_closing_report_search";
// $route['todays_report']  = "report/report/bdtask_todays_report";
// $route['todays_customer_received']  = "report/report/bdtask_todays_customer_received";
// $route['todays_customerwise_received']  = "report/report/bdtask_customerwise_received";
// $route['datewise_sales_report']  = "report/report/bdtask_datewise_sales_report";
// $route['invoice_wise_due_report']= "report/report/bdtask_invoice_wise_due_report";
// $route['shipping_cost_report']= "report/report/bdtask_shippingcost_report";
// $route['sales_return']         = "report/report/bdtask_sales_return";
// $route['supplier_returns']      = "report/report/bdtask_supplier_return";
// $route['tax_report']           = "report/report/bdtask_tax_report";
// $route['profit_report']        = "report/report/bdtask_profit_report";

$route['sales_report']  = "report/report/bdtask_todays_sales_report";
$route['userwise_sales_report']  = "report/report/bdtask_userwise_sales_report";
$route['product_wise_sales_report']= "report/report/bdtask_sale_report_productwise";
$route['category_sales_report']= "report/report/bdtask_categorywise_sales_report";
$route['purchase_report']     = "report/report/bdtask_purchase_report";
$route['purchase_report_categorywise']= "report/report/bdtask_purchase_report_category_wise";
$route['stock']          = "report/report/bdtask_stock_report";
$route['live_stock_report']          = "report/report/bdtask_livestock_report";
$route['stock_audit_report']     = "report/report/bdtask_stock_audit_report";



$route['generate_salesreportinvoice']  = "report/report/generate_salesreportinvoice";
$route['generate_salesreportproduct']  = "report/report/generate_salesreportproduct";
$route['generate_salesreportcategory']  = "report/report/generate_salesreportcategory";
$route['generate_purchasereportinvoice']  = "report/report/generate_purchasereportinvoice";
$route['generate_purchasereportcategory']  = "report/report/generate_purchasereportcategory";
$route['generate_salesreportemployee']  = "report/report/generate_salesreportemployee";
$route['generate_stockreport']  = "report/report/generate_stockreport";
$route['generate_cashbook']  = "report/report/generate_cashbook";
$route['generate_livestockreport']  = "report/report/generate_livestockreport";















