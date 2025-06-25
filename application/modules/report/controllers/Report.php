<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");
require_once(__DIR__ . '/TCPDF-main/tcpdf.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class SalesReportInvoicewise extends TCPDF
{
    // Override the Header() method to remove the line
    public $pageTotal = 0;

    public function Header()
    {
        $this->pageTotal = 0;
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-22);
        $this->SetFont('', 'B', 12);
        $this->Cell(50, 10, "Page Total:", 'TB', 0, 'L');
        $this->Cell(100, 10, "", 'TB', 0, 'L');
        $this->Cell(35, 10, number_format($this->pageTotal, 2), 'TB', 1, 'R');
        $this->Ln(10);
    }

    public function updatePageTotal($amount)
    {
        $this->pageTotal += $amount;
    }
}

class StockReport extends TCPDF
{
    // Override the Header() method to remove the line
    public $pageTotal = 0;

    public function Header()
    {
        $this->pageTotal = 0;
    }

    public function updatePageTotal($amount)
    {
        $this->pageTotal += $amount;
    }
}

class Report extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'report_model',
            'service/service_model'

        ));
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
    }

    /*product stock part*/
    function bdtask_livestock_report()
    {
        $data['title']      = display('live_stock_report');
        $data['product_list']  = $this->report_model->product_list();
        $data['category_list']  = $this->report_model->category_list_product();
        $data['store_list'] = $this->report_model->active_store();


        $data['module']     = "report";
        $data['page']       = "live_stock_report";
        echo modules::run('template/layout', $data);
    }

    function bdtask_stock_report()
    {
        $data['title']      = display('stock_report');
        $data['product_list']  = $this->report_model->product_list();
        $data['category_list']  = $this->report_model->category_list_product();
        $data['store_list'] = $this->report_model->active_store();


        $data['module']     = "report";
        $data['page']       = "stock_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_checkStocklist()
    {
        // GET data
        $postData = $this->input->post();
        $data = $this->report_model->bdtask_getStock($postData);
        echo json_encode($data);
    }


    public function bdtask_cash_closing()
    {
        $data['title']      = "Reports | Daily Closing";
        $data['info']       = $this->report_model->accounts_closing_data();
        $data['pay_methods'] = $this->report_model->payment_methods();
        $data['module']     = "report";
        $data['page']       = "closing_form";
        echo modules::run('template/layout', $data);
    }

    public function add_daily_closing()
    {

        $closedata = $this->db->select('*')->from('daily_closing')->where('date', date('Y-m-d'))->get()->num_rows();
        if ($closedata > 0) {
            $this->session->set_flashdata(array('exception' => 'Already Closed Today'));
            redirect(base_url('closing_form'));
        }
        $todays_date = date("Y-m-d");
        $data = array(
            'last_day_closing'  =>  str_replace(',', '', $this->input->post('last_day_closing', TRUE)),
            'cash_in'           =>  str_replace(',', '', $this->input->post('cash_in', TRUE)),
            'cash_out'          =>  str_replace(',', '', $this->input->post('cash_out', TRUE)),
            'date'              =>  $todays_date,
            'amount'            =>  str_replace(',', '', $this->input->post('cash_in_hand', TRUE)),
            'status'            =>      1
        );
        $invoice_id = $this->report_model->daily_closing_entry($data);


        $this->session->set_flashdata(array('message' => display('successfully_added')));
        redirect(base_url('closing_report'));
    }


    public function bdtask_closing_report()
    {
        $daily_closing_data = $this->report_model->get_closing_report();
        $i = 0;

        if (!empty($daily_closing_data)) {
            foreach ($daily_closing_data as $k => $v) {
                $daily_closing_data[$k]['final_date'] = $this->occational->dateConvert(date("Y-m-d", strtotime($daily_closing_data[$k]['datetime'])));
            }
        }
        $data = array(
            'title'              => display('closing_report'),
            'daily_closing_data' => $daily_closing_data,
        );
        $data['module']   = "report";
        $data['page']     = "closing_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_closing_report_search()
    {
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        $daily_closing_data = $this->report_model->get_date_wise_closing_report($from_date, $to_date);

        $i = 0;
        if (!empty($daily_closing_data)) {
            foreach ($daily_closing_data as $k => $v) {
                $daily_closing_data[$k]['final_date'] = $this->occational->dateConvert(date("Y-m-d", strtotime($daily_closing_data[$k]['datetime'])));
            }
            foreach ($daily_closing_data as $k => $v) {
                $i++;
                $daily_closing_data[$k]['sl'] = $i;
            }
        }

        $data = array(
            'title'              => display('closing_report'),
            'daily_closing_data' => $daily_closing_data,
            'from_date'          => $from_date,
            'to_date'            => $to_date,

        );

        $data['module']   = "report";
        $data['page']     = "closing_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_todays_report()
    {
        $sales_report = $this->report_model->todays_sales_report();
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {
                $i++;
                $sales_report[$k]['sl'] = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
            }
        }

        $purchase_report = $this->report_model->todays_purchase_report();
        $purchase_amount = 0;
        if (!empty($purchase_report)) {
            $i = 0;
            foreach ($purchase_report as $k => $v) {
                $i++;
                $purchase_report[$k]['sl'] = $i;
                $purchase_report[$k]['prchse_date'] = $this->occational->dateConvert($purchase_report[$k]['purchase_date']);
                $purchase_amount = $purchase_amount + $purchase_report[$k]['grand_total_amount'];
            }
        }

        $data = array(
            'title'           => display('todays_report'),
            'sales_report'    => $sales_report,
            'sales_amount'    => number_format($sales_amount, 2, '.', ','),
            'purchase_amount' => number_format($purchase_amount, 2, '.', ','),
            'purchase_report' => $purchase_report,
            'date'            => $today = date('Y-m-d'),
        );

        $data['module']   = "report";
        $data['page']     = "todays_report";
        echo modules::run('template/layout', $data);
    }


    //    ============ its for todays_customer_receipt =============
    public function bdtask_todays_customer_received()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $all_customer = $this->db->select('*')->from('customer_information')->get()->result();
        $todays_customer_receipt = $this->report_model->todays_customer_receipt($today);
        $data = array(
            'title'                   => "Received From Customer",
            'all_customer'            => $all_customer,
            'todays_customer_receipt' => $todays_customer_receipt,
            'today'                   => $today,
            'customer_id'             => '',
        );
        $data['module']   = "report";
        $data['page']     = "todays_customer_receipt";
        echo modules::run('template/layout', $data);
    }


    //    ============ its for todays_customer_receipt =============
    public function bdtask_customerwise_received()
    {
        date_default_timezone_set('Asia/Colombo');

        $customer_id = $this->input->post('customer_id', TRUE);
        $from_date   = $this->input->post('from_date', TRUE);
        $today       = date('Y-m-d');
        $all_customer = $this->db->select('*')->from('customer_information')->get()->result();
        $filter_customer_wise_receipt = $this->report_model->filter_customer_wise_receipt($customer_id, $from_date);
        $data = array(
            'title'                   => "Received From Customer",
            'all_customer'            => $all_customer,
            'todays_customer_receipt' => $filter_customer_wise_receipt,
            'today'                   => $from_date,
            'customer_info'           => $this->report_model->customerinfo_rpt($customer_id),
            'customer_id'            => $customer_id,
        );

        $data['module']   = "report";
        $data['page']     = "todays_customer_receipt";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_todays_sales_report()
    {
        // $sales_report = $this->report_model->todays_sales_report();
        $sales_amount = 0;
        if (!$this->permission1->method('todays_sales_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'        => display('sales_report'),
            'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['module']   = "report";
        $data['page']     = "sales_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_datewise_sales_report()
    {
        $from_date = $this->input->get('from_date');
        $to_date  = $this->input->get('to_date');
        $sales_report = $this->report_model->retrieve_dateWise_SalesReports($from_date, $to_date);
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {
                $i++;
                $sales_report[$k]['sl'] = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
            }
        }
        $data = array(
            'title'        => display('sales_report'),
            'sales_amount' => $sales_amount,
            'sales_report' => $sales_report,
            'from_date'    => $from_date,
            'to_date'      => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "sales_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_userwise_sales_report()
    {
        $user_id = (!empty($this->input->get('user_id')) ? $this->input->get('user_id') : '');
        $star_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $end_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        if (!$this->permission1->method('user_wise_sales_report', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        // $sales_report = $this->report_model->user_sales_report($star_date, $end_date, $user_id);
        // $sales_amount = 0;
        // if (!empty($sales_report)) {
        //     $i = 0;
        //     foreach ($sales_report as $k => $v) {
        //         $i++;
        //         $sales_report[$k]['sl'] = $i;

        //         $sales_amount += $sales_report[$k]['amount'];
        //     }
        // }
        $user_list = $this->report_model->userList();
        $data = array(
            'title'         => display('user_wise_sales_report'),
            // 'sales_amount'  => number_format($sales_amount, 2, '.', ','),
            // 'sales_report'  => $sales_report,
            'from'          => $this->occational->dateConvert($star_date),
            'to'            => $this->occational->dateConvert($end_date),
            'user_list'     => $user_list,
            'user_id'       => $user_id,
        );
        $data['module']   = "report";
        $data['page']     = "user_wise_sales_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_invoice_wise_due_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));

        $data = array(
            'title'        => display('due_report'),
            'from_date'    => $from_date,
            'to_date'      => $to_date,

        );

        $data['module']   = "report";
        $data['page']     = "due_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_shippingcost_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $sales_report = $this->report_model->retrieve_dateWise_Shippingcost($from_date, $to_date);
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {
                $i++;
                $sales_report[$k]['sl'] = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
            }
        }
        $data = array(
            'title'        => display('shipping_cost_report'),
            'sales_amount' => $sales_amount,
            'sales_report' => $sales_report,
            'from_date'    => $from_date,
            'to_date'      => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "shippincost_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_purchase_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));

        if (!$this->permission1->method('todays_purchase_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']   = display('purchase_report');
        $data['from']   = $from_date;
        $data['to']   = $to_date;
        $data['module']   = "report";
        $data['page']     = "purchase_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_purchase_report_category_wise()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date   = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $category  = (!empty($this->input->get('category')) ? $this->input->get('category') : '');
        $category_list = $this->report_model->category_list_product();
        $product_list = $this->report_model->product_list();

        // $purchase_report_category_wise = $this->report_model->purchase_report_category_wise($from_date, $to_date, $category);
        $data = array(
            'title'         => "Purchase Report (Category Wise)",
            'category_list' => $category_list,
            'from'          => $from_date,
            'to'            => $to_date,
            'category_id'   => $category,
            'product_list'   => $product_list,

            // 'purchase_report_category_wise' => $purchase_report_category_wise,
        );
        if (!$this->permission1->method('purchase_report_category_wise', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['module']   = "report";
        $data['page']     = "purchase_report_category_wise";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_sale_report_productwise()
    {
        // $from_date      = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        // $to_date        = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        // $product_id     = (!empty($this->input->get('product_id')) ? $this->input->get('product_id') : '');

        // $product_report = $this->report_model->retrieve_product_sales_report($from_date, $to_date, $product_id);
        if (!$this->permission1->method('product_wise_sales_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $product_list = $this->report_model->product_list();
        // if (!empty($product_report)) {
        //     $i = 0;
        //     foreach ($product_report as $k => $v) {
        //         $i++;
        //         $product_report[$k]['sl'] = $i;
        //     }
        // }
        // $sub_total = 0;
        // if (!empty($product_report)) {
        //     foreach ($product_report as $k => $v) {
        //         $product_report[$k]['sales_date'] = $this->occational->dateConvert($product_report[$k]['date']);
        //         $sub_total = $sub_total + $product_report[$k]['total_amount'];
        //     }
        // }
        $data = array(
            'title'          => display('sales_report_product_wise'),
            // 'sub_total'      => number_format($sub_total, 2, '.', ','),
            // 'product_report' => $product_report,
            'product_list'   => $product_list,
            // 'product_id'     => $product_id,
            // 'from'           => $from_date,
            // 'to'             => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "product_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_categorywise_sales_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $category = (!empty($this->input->get('category')) ? $this->input->get('category') : '');
        $category_list = $this->report_model->category_list_product();
        $product_list = $this->report_model->product_list();
        if (!$this->permission1->method('sales_report_category_wise', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        // $sales_report_category_wise = $this->report_model->sales_report_category_wise($from_date, $to_date, $category);
        $data = array(
            'title'                      => display('sales_report_category_wise'),
            'category_list'              => $category_list,
            'product_list'   => $product_list,
            // 'sales_report_category_wise' => $sales_report_category_wise,
            'from'                       => $from_date,
            'to'                         => $to_date,
            'category_id'                => $category,
        );
        $data['module']   = "report";
        $data['page']     = "sales_report_category_wise";
        echo modules::run('template/layout', $data);
    }



    public function bdtask_sales_return()
    {
        $from_date = $this->input->post('from_date', TRUE);
        $to_date   = $this->input->post('to_date', TRUE);
        $start     = (!empty($from_date) ? $from_date : date('Y-m-d'));
        $end       = (!empty($to_date) ? $to_date : date('Y-m-d'));
        $return_list = $this->report_model->sales_return_list($start, $end);
        if (!empty($return_list)) {
            foreach ($return_list as $k => $v) {
                $return_list[$k]['final_date'] = $this->occational->dateConvert($return_list[$k]['date_return']);
            }
        }

        $data = array(
            'title'      => display('invoice_return'),
            'return_list' => $return_list,
            'from_date'  => $start,
            'to_date'    => $end,
        );

        $data['module']   = "report";
        $data['page']     = "sales_return";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_supplier_return()
    {
        $from_date = $this->input->post('from_date', TRUE);
        $to_date   = $this->input->post('to_date', TRUE);
        $start     = (!empty($from_date) ? $from_date : date('Y-m-d'));
        $end       = (!empty($to_date) ? $to_date : date('Y-m-d'));
        $return_list = $this->report_model->supplier_return($start, $end);
        if (!empty($return_list)) {
            foreach ($return_list as $k => $v) {
                $return_list[$k]['final_date'] = $this->occational->dateConvert($return_list[$k]['date_return']);
            }
        }

        $data = array(
            'title'       => display('supplier_return'),
            'return_list' => $return_list,
            'start_date'  => $start,
            'end_date'    => $end,
        );

        $data['module']   = "report";
        $data['page']     = "supplier_return";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_tax_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $sales_report = $this->report_model->retrieve_dateWise_tax($from_date, $to_date);
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {

                $sales_report[$k]['sl']         = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
                $i++;
            }
        }
        $data = array(
            'title'        => display('tax_report'),
            'sales_amount' => $sales_amount,
            'sales_report' => $sales_report,
            'from_date'    => $from_date,
            'to_date'      => $to_date,
        );

        $data['module']   = "report";
        $data['page']     = "tax_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_profit_report()
    {
        $start_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $end_date   = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $total_profit_report = $this->report_model->total_profit_report($start_date, $end_date);
        $profit_ammount   = 0;
        $SubTotalSupAmnt  = 0;
        $SubTotalSaleAmnt = 0;
        if (!empty($total_profit_report)) {
            $i = 0;
            foreach ($total_profit_report as $k => $v) {
                $total_profit_report[$k]['sl'] = $i;
                $total_profit_report[$k]['prchse_date'] = $this->occational->dateConvert($total_profit_report[$k]['date']);
                $profit_ammount = $profit_ammount + $total_profit_report[$k]['total_profit'];
                $SubTotalSupAmnt = $SubTotalSupAmnt + $total_profit_report[$k]['total_supplier_rate'];
                $SubTotalSaleAmnt = $SubTotalSaleAmnt + $total_profit_report[$k]['total_sale'];
            }
        }

        $data = array(
            'title'               => display('profit_report'),
            'profit_ammount'      => number_format($profit_ammount, 2, '.', ','),
            'total_profit_report' => $total_profit_report,
            'from'                => $start_date,
            'to'                  => $end_date,
            'SubTotalSupAmnt'     => number_format($SubTotalSupAmnt, 2, '.', ','),
            'SubTotalSaleAmnt'    => number_format($SubTotalSaleAmnt, 2, '.', ','),
        );
        $data['module']   = "report";
        $data['page']     = "profit_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_add_closing()
    {

        $this->form_validation->set_rules('opening_bal', display('opening_balance'), 'max_length[100]|required');
        if ($this->form_validation->run()) {
            $createby    = $this->session->userdata('id');
            $check_exist = $this->db->select('')->from('closing_records')->where('user_id', $createby)->where('DATE(datetime)', date('Y-m-d'))->where('head_code', $this->input->post('head_code', true))->get()->num_rows();
            if ($check_exist > 0) {
                $data['status'] = 0;
                $data['message'] = 'Already Closed Today';
                echo json_encode($data);
                exit;
            }
            $createdate = date('Y-m-d H:i:s');
            $postData = array(
                'head_code'       => $this->input->post('head_code', true),
                'opening_balance' => $this->input->post('opening_bal', true),
                'amount_in'       => $this->input->post('total_received', true),
                'amount_out'      => $this->input->post('total_paid', true),
                'closign_balance' => $this->input->post('closing', true),
                'user_id'         => $createby,
                'status'          => 1
            );
            if ($this->report_model->create_opening($postData)) {
                $data['status'] = 1;
                $data['message'] = display('successfully_saved');
            } else {
                $data['status'] = 0;
                $data['message'] = display('please_try_again');
            }
        } else {
            $data['status'] = 0;
            $data['message'] = validation_errors();
        }
        echo json_encode($data);
        exit;
    }

    public function CheckReportList()
    {
        // echo "bb";
        // exit;
        $postData = $this->input->post();
        $data = $this->report_model->getReportList($postData);
        // dd($data);
        // exit;
        echo json_encode($data);
    }
    public function getSalesReportList()
    {
        // echo "bb";
        // exit;
        $postData = $this->input->post();
        $data = $this->report_model->getSalesReportList($postData);
        // dd($data);
        // exit;
        echo json_encode($data);
    }
    public function get_retrieve_dateWise_DueReports()
    {
        // echo "bb";
        // exit;
        $postData = $this->input->post();
        $data = $this->report_model->get_retrieve_dateWise_DueReports($postData);
        // dd($data);
        // exit;
        echo json_encode($data);
    }

    public function sales_reportinvoicewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->sales_reportinvoicewise($from_date, $to_date, $empid, $branch);
        $_SESSION['sale_reportsri'] =  $report_data;
        $_SESSION['sri_istype'] =   $this->input->post('istype');
        $_SESSION['srifrom_date'] = $from_date;
        $_SESSION['srito_date'] =  $to_date;


        echo json_encode($_SESSION['sale_reportsri']);
    }

    public function purchase_reportinvoicewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->bdtask_purchase_report($from_date, $to_date, $empid, $branch);
        $_SESSION['purchase_reportpri'] =  $report_data;
        $_SESSION['pri_istype'] =   $this->input->post('istype');
        $_SESSION['prifrom_date'] = $from_date;
        $_SESSION['prito_date'] =  $to_date;


        echo json_encode($_SESSION['purchase_reportpri']);
    }

    public function sales_reportproductwise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $productid = $this->input->post('productid');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->retrieve_product_sales_report($from_date, $to_date, $productid, $empid, $branch);
        $_SESSION['sale_reportsrp'] =  $report_data;
        $_SESSION['srp_istype'] =   $this->input->post('istype');
        $_SESSION['srpfrom_date'] = $from_date;
        $_SESSION['srpto_date'] =  $to_date;
        echo json_encode($_SESSION['sale_reportsrp']);
    }



    public function sales_reportcategorywise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $category = $this->input->post('category');
        $product = $this->input->post('product');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->sales_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch);
        $_SESSION['sale_reportsrc'] =  $report_data;
        $_SESSION['src_istype'] =   $this->input->post('istype');
        $_SESSION['srcfrom_date'] = $from_date;
        $_SESSION['srcto_date'] =  $to_date;
        echo json_encode($_SESSION['sale_reportsrc']);
    }

    public function sales_reportemployeewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $employee = $this->input->post('employee');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->user_sales_report($from_date, $to_date, $employee, $empid, $branch);
        $_SESSION['sale_reportsre'] =  $report_data;
        $_SESSION['sre_istype'] =   $this->input->post('istype');
        $_SESSION['srefrom_date'] = $from_date;
        $_SESSION['sreto_date'] =  $to_date;


        echo json_encode($_SESSION['sale_reportsre']);
    }


    public function purchase_reportcategorywise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $category = $this->input->post('category');
        $product = $this->input->post('product');
        $branch = $this->input->post('branch');


        $report_data = $this->report_model->purchase_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch);
        $_SESSION['purchase_reportprc'] =  $report_data;
        $_SESSION['prc_istype'] =   $this->input->post('istype');
        $_SESSION['prcfrom_date'] = $from_date;
        $_SESSION['prcto_date'] =  $to_date;


        echo json_encode($_SESSION['purchase_reportprc']);
    }


    public function generate_salesreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Invoice Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Invoice Wise)", $_SESSION['sri_istype'], $_SESSION['srifrom_date'], $_SESSION['srito_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(45, 10, 'Sale Date', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(40, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(60, 10, 'Customer Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(40, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
        $pdf->Ln(10);

        $data = isset($_SESSION['sale_reportsri']) ? $_SESSION['sale_reportsri'] : [];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Invoice Wise)", $_SESSION['sri_istype'], $_SESSION['srifrom_date'], $_SESSION['srito_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(45, 10, 'Sale Date', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(40, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(60, 10, 'Customer Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(40, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            $total = $total + $row['total'];
            $patotal = $patotal + $row['total'];

            $pdf->SetFont('', '', 10);
            $pdf->Cell(45, 8, $row['date'], 0, 0, 'L');
            $pdf->Cell(40, 8,  $row['invoiceno'], 0, 0, 'L');
            $pdf->Cell(60, 8,  $row['customer_name'], 0, 0, 'L');
            $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');
        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Sales Report(Invoice Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_purchasereportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Report(Invoice Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Report (Invoice Wise)", $_SESSION['pri_istype'], $_SESSION['prifrom_date'], $_SESSION['prito_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(45, 10, 'Purchase Date', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(40, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(60, 10, 'Supplier Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(40, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
        $pdf->Ln(10);

        $data = isset($_SESSION['purchase_reportpri']) ? $_SESSION['purchase_reportpri'] : [];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Invoice Wise)", $_SESSION['pri_istype'], $_SESSION['prifrom_date'], $_SESSION['prito_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(45, 10, 'Purchase Date', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(40, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(60, 10, 'Supplier Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(40, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            $patotal = $patotal + $row['total'];
            $total = $total + $row['total'];

            $pdf->SetFont('', '', 10);
            $pdf->Cell(45, 8, $row['date'], 0, 0, 'L');
            $pdf->Cell(40, 8,  $row['invoiceno'], 0, 0, 'L');
            $pdf->Cell(60, 8,  $row['supplier_name'], 0, 0, 'L');
            $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');
        $pdf->updatePageTotal($patotal);




        $date = date('Y-m-d');
        $filename = "Purchase Report (Invoice Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_salesreportproduct()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Product Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Product Wise)", $_SESSION['srp_istype'], $_SESSION['srpfrom_date'], $_SESSION['srpto_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(20, 10, 'Sale Date', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(45, 10, 'Product Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(23, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(33, 10, 'Customer Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(27, 10, 'Rate', 'TB', 0, 'R', 0, '', 1);
        $pdf->Cell(10, 10, 'Qty', 'TB', 0, 'R', 0, '', 1);
        $pdf->Cell(27, 10, 'Total', 'TB', 0, 'R', 0, '', 1);


        $pdf->Ln(10);

        $data = isset($_SESSION['sale_reportsrp']) ? $_SESSION['sale_reportsrp'] : [];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Product Wise)", $_SESSION['srp_istype'], $_SESSION['srpfrom_date'], $_SESSION['srpto_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(20, 10, 'Sale Date', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(45, 10, 'Product Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(23, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(33, 10, 'Customer Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(27, 10, 'Rate', 'TB', 0, 'R', 0, '', 1);
                $pdf->Cell(10, 10, 'Qty', 'TB', 0, 'R', 0, '', 1);
                $pdf->Cell(27, 10, 'Total', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            $patotal = $patotal + $row['total'];
            $total = $total + $row['total'];

            $pdf->SetFont('', '', 10);
            $pdf->Cell(20, 8, $row['date'], 0, 0, 'L');
            $pdf->Cell(45, 8,  $row['product_name'], 0, 0, 'L');
            $pdf->Cell(23, 8,  $row['sale_id'], 0, 0, 'L');
            $pdf->Cell(33, 8,  $row['customer_name'], 0, 0, 'L');
            $pdf->Cell(27, 8, number_format($row['product_rate'], 2), 0, 0, 'R');
            $pdf->Cell(10, 8,  $row['quantity'], 0, 0, 'R');
            $pdf->Cell(27, 8, number_format($row['total'], 2), 0, 0, 'R');
            $pdf->Ln(8);



            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');

        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Sales Report (Product Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_salesreportcategory()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Category Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Category Wise)", $_SESSION['src_istype'], $_SESSION['srcfrom_date'], $_SESSION['srcto_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(75, 10, 'Category Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(60, 10, 'Product Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(20, 10, 'Qty', 'TB', 0, 'C', 0, '', 1);
        $pdf->Cell(30, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);




        $pdf->Ln(10);

        $data =  $_SESSION['sale_reportsrc'];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {



            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Category Wise)", $_SESSION['src_istype'], $_SESSION['srcfrom_date'], $_SESSION['srcto_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(75, 10, 'Category Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(60, 10, 'Product Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(20, 10, 'Qty', 'TB', 0, 'C', 0, '', 1);
                $pdf->Cell(30, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            $patotal = $patotal + $row['total_price'];
            $total = $total + $row['total_price'];

            // $pdf->SetFont('', '', 10);
            // $pdf->Cell(45, 8, $row['category_name'], 0, 0, 'L');
            // $pdf->Cell(50, 8,  $row['product_name'], 0, 0, 'L');
            // $pdf->Cell(30, 8,  $row['date'], 0, 0, 'L');
            // $pdf->Cell(20, 8,  $row['quantity'], 0, 0, 'R');
            // $pdf->Cell(40, 8, number_format($row['total_price'], 2), 0, 0, 'R');

            $pdf->SetFont('', '', 10);
            $pdf->MultiCell(75, 8, $row['category_name'], '', 'L', 0, 0);
            $pdf->MultiCell(60, 8, $row['product_name'], '', 'L', 0, 0);
            $pdf->MultiCell(20, 8, $row['quantity'], '', 'C', 0, 0);
            $pdf->MultiCell(30, 8,  number_format($row['total_price'], 2), '', 'R', 0, 0);
            $pdf->Ln(10);



            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');

        $pdf->updatePageTotal($patotal);



        $date = date('Y-m-d');
        $filename = "Sales Report (Category Wise)_$date.pdf";
        ob_end_clean();

        $pdf->Output($filename, 'I');
    }


    public function generate_salesreportemployee()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Employee Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Employee Wise)", $_SESSION['sre_istype'], $_SESSION['srefrom_date'], $_SESSION['sreto_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Employee ID', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(50, 10, 'Employee Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(40, 10, 'Total Sale', 'TB', 0, 'C', 0, '', 1);
        $pdf->Cell(45, 10, 'Total Amount', 'TB', 0, 'R', 0, '', 1);




        $pdf->Ln(10);

        $data =  $_SESSION['sale_reportsre'];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {



            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Employee Wise)", $_SESSION['sre_istype'], $_SESSION['srefrom_date'], $_SESSION['sreto_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(50, 10, 'First Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(50, 10, 'Last Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(40, 10, 'Total Sale', 'TB', 0, 'C', 0, '', 1);
                $pdf->Cell(45, 10, 'Total Amount', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            $patotal = $patotal + $row['amount'];
            $total = $total + $row['amount'];

            $pdf->SetFont('', '', 10);
            $pdf->Cell(50, 8, $row['first_name'], 0, 0, 'L');
            $pdf->Cell(50, 8,  $row['last_name'], 0, 0, 'L');
            $pdf->Cell(40, 8,  $row['toal_invoice'], 0, 0, 'C');
            $pdf->Cell(45, 8, number_format($row['amount'], 2), 0, 0, 'R');
            $pdf->Ln(8);



            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');

        $pdf->updatePageTotal($patotal);




        $date = date('Y-m-d');
        $filename = "Sales Report (Employee Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_purchasereportcategory()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Report(Category Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Report (Category Wise)", $_SESSION['prc_istype'], $_SESSION['prcfrom_date'], $_SESSION['prcto_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(75, 10, 'Category Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(60, 10, 'Product Name', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(20, 10, 'Qty', 'TB', 0, 'C', 0, '', 1);
        $pdf->Cell(30, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);




        $pdf->Ln(10);

        $data =  $_SESSION['purchase_reportprc'];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {



            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Purchase Report (Category Wise)", $_SESSION['src_istype'], $_SESSION['srcfrom_date'], $_SESSION['srcto_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(75, 10, 'Category Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(60, 10, 'Product Name', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(20, 10, 'Qty', 'TB', 0, 'C', 0, '', 1);
                $pdf->Cell(30, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            $patotal = $patotal + $row['total_price'];
            $total = $total + $row['total_price'];



            $pdf->SetFont('', '', 10);
            $pdf->MultiCell(75, 8, $row['category_name'], '', 'L', 0, 0);
            $pdf->MultiCell(60, 8, $row['product_name'], '', 'L', 0, 0);
            $pdf->MultiCell(20, 8, $row['quantity'], '', 'C', 0, 0);
            $pdf->MultiCell(30, 8,  number_format($row['total_price'], 2), '', 'R', 0, 0);

            // $pdf->Cell(40, 8, number_format($row['total_price'], 2), 0, 0, 'R');






            // $pdf->Cell(45, 8, $row['category_name'], 0, 0, 'L');
            // $pdf->Cell(50, 8,  $row['product_name'], 0, 0, 'L');
            // $pdf->Cell(30, 8,  $row['date'], 0, 0, 'L');
            // $pdf->Cell(20, 8,  $row['quantity'], 0, 0, 'R');
            // $pdf->Cell(40, 8, number_format($row['total_price'], 2), 0, 0, 'R');
            $pdf->Ln(10);



            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');

        $pdf->updatePageTotal($patotal);



        $date = date('Y-m-d');
        $filename = "Sales Report (Category Wise)_$date.pdf";
        ob_end_clean();

        $pdf->Output($filename, 'I');
    }


    public function header($pdf, $page, $head, $type, $from, $to)
    {
        $pdf->Ln(5);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $pdf->SetFont('helvetica', 'B', 100);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Image(base_url() . $currency_details[0]['invoice_logo'], $x, 13, 50, 20, '', '', '', true, 300, '', false, false, 0, false, false, false);
        $pdf->SetX($x + 65);
        $pdf->SetFont('helvetica', 'B', 15);
        $pageWidth = $pdf->GetPageWidth() + 10;
        $pdf->SetFont('helvetica', 'B', 20);
        $company =  $company_info[0]['company_name'];
        $companyWidth = $pdf->GetStringWidth($company);
        $pdf->SetX(($pageWidth - $companyWidth) / 2);
        $pdf->Cell($companyWidth, 10, $company, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $address = $company_info[0]['address'];
        $addressWidth = $pdf->GetStringWidth($address);
        $pdf->SetX(($pageWidth - $addressWidth) / 2);
        $pdf->Cell($addressWidth, 5, $address, 0, 1, 'C');
        $email = $company_info[0]['email'];
        $emailWidth = $pdf->GetStringWidth($email);
        $pdf->SetX(($pageWidth - $emailWidth) / 2);
        $pdf->Cell($emailWidth, 5, $email, 0, 1, 'C');
        $mobile = $company_info[0]['mobile'];
        $mobileWidth = $pdf->GetStringWidth($mobile);
        $pdf->SetX(($pageWidth - $mobileWidth) / 2);
        $pdf->Cell($mobileWidth, 5, $mobile, 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 12);
        $headWidth = $pdf->GetStringWidth($head);
        $pdf->SetX(($pageWidth - $headWidth) / 2);
        $pdf->Cell($headWidth, 5, $head, 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 11);
        $dateWidth = $pdf->GetStringWidth($mobile);
        $pdf->SetX(($pageWidth - $dateWidth) / 2);

        if (isset($type) && $type  === "false") {
            $pdf->Cell($dateWidth, 10, "From: " . $from . "  " . "To: " . $to, 0, 1, 'C');
        } else if ($from != "") {
            $pdf->Cell($dateWidth, 10, "Date: " . $from, 0, 1, 'C');
        }

        $margins = $pdf->getMargins();
        $pageWidth = $pdf->GetPageWidth();
        $rightMargin = $margins['right'];
        $pdf->SetFont('helvetica', 'B', 60);
        $rightText =  $page;
        $pdf->SetXY($pageWidth - $rightMargin - 60, 13);
        $pdf->Cell(50, 10, "", 0, 0, 'R');
        $pdf->Ln(40);
    }


    public function stock_reportdata()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');

        $product = $this->input->post('product');
        $category = $this->input->post('category');
        $store = $this->input->post('store');
        $stocktype = $this->input->post('stocktype');

        $storeResult = $this->db->select("store.id")
            ->from('sec_store')
            ->join('store', 'store.id=sec_store.storeid')
            ->where('sec_store.userid', $this->session->userdata('id'))
            ->group_by('sec_store.storeid')
            ->get()
            ->result();

        $storeids = [];

        if (isset($storeResult)) {
            $storeids = array_column($storeResult, 'id');
        }

        $sqljoin = "";
        if ($category) {
            $sqljoin .= " And pi.category_id=" . $category;
        }


        if ($product) {
            $sqljoin .= " And pi.id=" . $product;
        }

        if ($store) {
            $sqljoin .= " And sd.store=" . $store;
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
                $inClause = implode(',', array_map('intval', $storeids));
                $sqljoin .= " AND sd.store IN ($inClause) ";
            }
        }


        $encryption_key = Config::$encryption_key;


        if ($stocktype == "all" || $stocktype == "") {
            $sql = "SELECT  id,product_name,
    unit,
    category_name,
    SUM(inqty) AS inqty,
    SUM(outqty) AS outqty,
    SUM(avqty) AS avqty,
    SUM(pinqty) AS pinqty,
    SUM(poutqty) AS poutqty,
    SUM(pavqty) AS pavqty,purchase_price, sale_price from (SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as avqty, NULL AS pinqty,NULL AS poutqty,NULL AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN stock_details sd on sd.product=pi.id
WHERE sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
GROUP By pi.id
UNION 
SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
 NULL AS inqty,
    NULL AS outqty,
    NULL AS avqty,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN phystock_details sd on sd.product=pi.id
WHERE sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
GROUP By pi.id) AS stock_data
GROUP BY id;";

            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "actualstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as avqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN stock_details sd on sd.product=pi.id
WHERE sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "physicalstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN phystock_details sd on sd.product=pi.id
WHERE sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        $empid = $this->input->post('empid');
        $_SESSION['stock_report'] =  $data;
        $_SESSION['sr_istype'] =   $this->input->post('istype');
        $_SESSION['srfrom_date'] = $from_date;
        $_SESSION['srto_date'] =  $to_date;
        $_SESSION['sr_istype2'] =  $stocktype;
        $_SESSION['header'] = "Comprehensive Stock Report";




        echo json_encode($data);
    }


    public function livestock_reportdata()
    {

        $product = $this->input->post('product');
        $category = $this->input->post('category');
        $store = $this->input->post('store');
        $stocktype = $this->input->post('stocktype');

        $storeResult = $this->db->select("store.id")
            ->from('sec_store')
            ->join('store', 'store.id=sec_store.storeid')
            ->where('sec_store.userid', $this->session->userdata('id'))
            ->group_by('sec_store.storeid')
            ->get()
            ->result();

        $storeids = [];

        if (isset($storeResult)) {
            $storeids = array_column($storeResult, 'id');
        }


        $sqljoin = "";
        if ($category) {
            $sqljoin .= " And pi.category_id=" . $category;
        }


        if ($product) {
            $sqljoin .= " And pi.id=" . $product;
        }

        if ($store) {
            $sqljoin .= " And sd.store=" . $store;
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
                $inClause = implode(',', array_map('intval', $storeids));
                $sqljoin .= " AND sd.store IN ($inClause) ";
            }
        }


        $encryption_key = Config::$encryption_key;


        if ($stocktype == "all" || $stocktype == "") {
            $sql = "SELECT  id,product_name,
    unit,
    category_name,
    SUM(inqty) AS inqty,
    SUM(outqty) AS outqty,
    SUM(avqty) AS avqty,
    SUM(pinqty) AS pinqty,
    SUM(poutqty) AS poutqty,
    SUM(pavqty) AS pavqty,purchase_price, sale_price from (SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as avqty, NULL AS pinqty,NULL AS poutqty,NULL AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN stock_details sd on sd.product=pi.id
WHERE sd.date " . $sqljoin . "
GROUP By pi.id
UNION 
SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
 NULL AS inqty,
    NULL AS outqty,
    NULL AS avqty,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN phystock_details sd on sd.product=pi.id
WHERE pi.status=1 " . $sqljoin . "
GROUP By pi.id) AS stock_data
GROUP BY id;";

            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "actualstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as avqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN stock_details sd on sd.product=pi.id
WHERE pi.status=1  " . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "physicalstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
SUM(AES_DECRYPT(sd.stock,'" . $encryption_key . "')) as pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN phystock_details sd on sd.product=pi.id
WHERE pi.status=1  " . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        $empid = $this->input->post('empid');
        $_SESSION['stock_report'] =  $data;
        $_SESSION['sr_istype'] =   $this->input->post('istype');
        $_SESSION['srfrom_date'] = '';
        $_SESSION['srto_date'] =  '';
        $_SESSION['sr_istype2'] =  $stocktype;
        $_SESSION['header'] = "Live Stock Report";





        echo json_encode($data);
    }



    public function cashbook_reportdata()
    {
        $encryption_key = Config::$encryption_key;

        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $payment = $this->input->post('payment');
        $branch = $this->input->post('branch');

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }




        $sqljoin = "";

        if ($empid != "All") {
            $sqljoin .= " And type2= '" . $empid . "'";
        }

        if ($payment) {
            $sqljoin .= " And payment_type= " . $payment . "";
        }

        // if ($branch) {
        //     $this->db->where("a.branch", $branch);
        // } else {
        //     if ($this->session->userdata('user_level2') != 1) {

        //         $this->db->where_in('a.branch', $branchids);
        //     }
        // }

        if ($branch) {
            $sqljoin .= " AND branch = " . (int)$branch . " ";
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
                // Convert branch ID array to comma-separated string
                $inClause = implode(',', array_map('intval', $branchids));
                $sqljoin .= " AND branch IN ($inClause) ";
            }
        }



        $sql = "SELECT date,incidenttype,payment_type,payment_method,invoice_no,grandTotal,type2,createddate,branch
FROM (
    SELECT 
        s.date, 
        'Sale' AS incidenttype,
        s.payment_type,
        pt.name AS payment_method,
        AES_DECRYPT(s.sale_id,  '" . $encryption_key . "') AS invoice_no,
        AES_DECRYPT(s.grandTotal,  '" . $encryption_key . "') AS grandTotal,
         AES_DECRYPT(s.type2,  '" . $encryption_key . "') AS type2,
         s.createddate,s.branch
    FROM sale s
    INNER JOIN payment_type pt ON pt.id = s.payment_type
    UNION
    SELECT 
        p.date, 
        'Purchase' AS incidenttype,
        p.payment_type,
        pt.name AS payment_method,
        AES_DECRYPT(p.chalan_no,  '" . $encryption_key . "') AS invoice_no,
        AES_DECRYPT(p.grandTotal,  '" . $encryption_key . "') AS grandTotal,
         AES_DECRYPT(p.type2,  '" . $encryption_key . "') AS type2,
       p.createddate,p.branch
    FROM purchase p
    INNER JOIN payment_type pt ON pt.id = p.payment_type
) AS cashbook
 WHERE date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
ORDER by createddate desc;";
        $query = $this->db->query($sql);
        $data  = $query->result_array();

        $_SESSION['cashbook'] =  $data;
        $_SESSION['cb_istype'] =   $this->input->post('istype');
        $_SESSION['cbfrom_date'] = $from_date;
        $_SESSION['cbto_date'] =  $to_date;
        // $_SESSION['cb_istype2'] =  $stocktype;




        echo json_encode($data);
    }

    public function generate_stockreport()
    {
        $page = 1;
        $pdf = new StockReport('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle($_SESSION['header']);
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page,  $_SESSION['header'], $_SESSION['sr_istype'], $_SESSION['srfrom_date'], $_SESSION['srto_date']);

        if ($_SESSION['sr_istype2'] == "actualstock") {
            $pdf->Cell(20, 10, '', '', 0, 'C', 0, '', 1);
        }

        if ($_SESSION['sr_istype2'] == "physicalstock") {
            $pdf->Cell(40, 10, '', '', 0, 'C', 0, '', 1);
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(35, 10, '', 'TL', 0, 'L', 0, '', 1);
        $pdf->Cell(35, 10, 'Product Infomation', 'T', 0, 'L', 0, '', 1);
        $pdf->Cell(10, 10, '', 'T', 0, 'L', 0, '', 1);
        $pdf->Cell(15, 10, '', 'T', 0, 'R', 0, '', 1);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->Cell(48, 10, 'Actual Stock', 'TL', 0, 'C', 0, '', 1);
        }

        if ($_SESSION['sr_istype2'] != "actualstock") {
            $pdf->Cell(48, 10, 'Physical Stock', 'TL', 0, 'C', 0, '', 1);
        }

        $pdf->Cell(12, 10, '', 'TL', 0, 'L', 0, '', 1);

        $pdf->Cell(30, 10, 'Unit Price', 'TR', 0, 'L', 0, '', 1);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->Cell(12, 10, '', 'TL', 0, 'L', 0, '', 1);

            $pdf->Cell(30, 10, 'Stock Value', 'TR', 0, 'L', 0, '', 1);
        }


        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 8);

        if ($_SESSION['sr_istype2'] == "actualstock") {
            $pdf->Cell(20, 10, '', '', 0, 'C', 0, '', 1);
        }

        if ($_SESSION['sr_istype2'] == "physicalstock") {
            $pdf->Cell(40, 10, '', '', 0, 'C', 0, '', 1);
        }

        $pdf->MultiCell(15, 10, 'Sl', 'TLB', 'C', 0, 0);
        $pdf->MultiCell(35, 10, 'Product Name', 'TLB', 'C', 0, 0);
        $pdf->MultiCell(30, 10, 'Category', 'TLB', 'C', 0, 0);
        $pdf->MultiCell(15, 10, 'Unit', 'TLB', 'C', 0, 0);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->MultiCell(16, 10, "In.Qty", 'TLB', 'C', 0, 0);
            $pdf->MultiCell(16, 10, "Out.Qty", 'TLB', 'C', 0, 0);
            $pdf->MultiCell(16, 10, "Avl.Qty", 'TLB', 'C', 0, 0);
        }

        if ($_SESSION['sr_istype2'] != "actualstock") {
            $pdf->MultiCell(16, 10, "In.Qty", 'TLB', 'C', 0, 0);
            $pdf->MultiCell(16, 10, "Out.Qty", 'TLB', 'C', 0, 0);
            $pdf->MultiCell(16, 10, "Avl.Qty", 'TLB', 'C', 0, 0);
        }

        $pdf->MultiCell(21, 10, "Purchase Price", 'TLB', 'C', 0, 0);
        $pdf->MultiCell(21, 10, "Sale Price", 'TLBR', 'C', 0, 0);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->MultiCell(21, 10, "Act.Stock Purchase Val", 'TLB', 'C', 0, 0);
            $pdf->MultiCell(21, 10, "Act.Stock Sale Val", 'TLBR', 'C', 0, 0);
        }

        $data =  $_SESSION['stock_report'];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;
        $pdf->SetFont('helvetica', '', 8);
        $i = 1;

        foreach ($data as $row) {
            $pdf->Ln(10);

            if ($_SESSION['sr_istype2'] == "actualstock") {
                $pdf->Cell(20, 10, '', '', 0, 'C', 0, '', 1);
            }

            if ($_SESSION['sr_istype2'] == "physicalstock") {
                $pdf->Cell(40, 10, '', '', 0, 'C', 0, '', 1);
            }

            $pdf->Cell(15, 10, $i, 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(35, 10, $row['product_name'], 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(30, 10, $row['category_name'], 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(15, 10, $row['unit'], 'TLB', 0, 'C', 0, '', 1);



            if ($_SESSION['sr_istype2'] != "physicalstock") {
                $pdf->Cell(16, 10, $row['inqty'], 'TLB', 0, 'C', 0, '', 1);
                $pdf->Cell(16, 10, abs($row['outqty']), 'TLB', 0, 'C', 0, '', 1);
                $pdf->Cell(16, 10, $row['avqty'], 'TLB', 0, 'C', 0, '', 1);
            }

            if ($_SESSION['sr_istype2'] != "actualstock") {
                $pdf->Cell(16, 10, $row['pinqty'], 'TLB', 0, 'C', 0, '', 1);
                $pdf->Cell(16, 10, abs($row['poutqty']), 'TLB', 0, 'C', 0, '', 1);
                $pdf->Cell(16, 10, $row['pavqty'], 'TLB', 0, 'C', 0, '', 1);
            }




            $pdf->Cell(21, 10, number_format($row['purchase_price'], 2), 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(21, 10, number_format($row['sale_price'], 2), 'TLBR', 0, 'C', 0, '', 1);
            $total_purchase =  $row['purchase_price'] * $row['avqty'];
            $total_sale =  $row['sale_price'] * $row['avqty'];

            if ($_SESSION['sr_istype2'] != "physicalstock") {


                $pdf->Cell(21, 10, number_format($total_purchase, 2), 'TLB', 0, 'C', 0, '', 1);
                $pdf->Cell(21, 10, number_format($total_sale, 2), 'TLBR', 0, 'C', 0, '', 1);
            }
            $i = $i + 1;
        }




        $date = date('Y-m-d');
        $filename =  $_SESSION['header'] . "_$date.pdf";
        $pdf->Output($filename, 'I');
    }



    public function generate_livestockreport()
    {
        $page = 1;
        $pdf = new StockReport('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle($_SESSION['header']);
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page,  $_SESSION['header'], $_SESSION['sr_istype'], $_SESSION['srfrom_date'], $_SESSION['srto_date']);

        if ($_SESSION['sr_istype2'] == "actualstock") {
            $pdf->Cell(20, 10, '', '', 0, 'C', 0, '', 1);
        }

        if ($_SESSION['sr_istype2'] == "physicalstock") {
            $pdf->Cell(40, 10, '', '', 0, 'C', 0, '', 1);
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(50, 10, '', 'TL', 0, 'L', 0, '', 1);
        $pdf->Cell(35, 10, 'Product Infomation', 'T', 0, 'L', 0, '', 1);
        $pdf->Cell(20, 10, '', 'T', 0, 'L', 0, '', 1);
        $pdf->Cell(15, 10, '', 'T', 0, 'R', 0, '', 1);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->Cell(24, 10, 'Actual Stock', 'TL', 0, 'C', 0, '', 1);
        }

        if ($_SESSION['sr_istype2'] != "actualstock") {
            $pdf->Cell(24, 10, 'Physical Stock', 'TL', 0, 'C', 0, '', 1);
        }


        $pdf->Cell(15, 10, '', 'TL', 0, 'L', 0, '', 1);

        $pdf->Cell(35, 10, 'Unit Price', 'TR', 0, 'L', 0, '', 1);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->Cell(15, 10, '', 'TL', 0, 'L', 0, '', 1);

            $pdf->Cell(35, 10, 'Stock Value', 'TR', 0, 'L', 0, '', 1);
        }



        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 8);

        if ($_SESSION['sr_istype2'] == "actualstock") {
            $pdf->Cell(20, 10, '', '', 0, 'C', 0, '', 1);
        }

        if ($_SESSION['sr_istype2'] == "physicalstock") {
            $pdf->Cell(40, 10, '', '', 0, 'C', 0, '', 1);
        }

        $pdf->MultiCell(15, 10, 'Sl', 'TLB', 'C', 0, 0);
        $pdf->MultiCell(50, 10, 'Product Name', 'TLB', 'C', 0, 0);
        $pdf->MultiCell(40, 10, 'Category', 'TLB', 'C', 0, 0);
        $pdf->MultiCell(15, 10, 'Unit', 'TLB', 'C', 0, 0);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->MultiCell(24, 10, "Avl.Qty", 'TLB', 'C', 0, 0);
        }

        if ($_SESSION['sr_istype2'] != "actualstock") {
            $pdf->MultiCell(24, 10, "Avl.Qty", 'TLB', 'C', 0, 0);
        }

        $pdf->MultiCell(25, 10, "Purchase Price", 'TLB', 'C', 0, 0);
        $pdf->MultiCell(25, 10, "Sale Price", 'TLBR', 'C', 0, 0);

        if ($_SESSION['sr_istype2'] != "physicalstock") {
            $pdf->MultiCell(25, 10, "Act.Stock Purchase Val", 'TLB', 'C', 0, 0);
            $pdf->MultiCell(25, 10, "Act.Stock Sale Val", 'TLBR', 'C', 0, 0);
        }

        $data =  $_SESSION['stock_report'];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;
        $pdf->SetFont('helvetica', '', 8);
        $i = 1;

        foreach ($data as $row) {
            $pdf->Ln(10);

            if ($_SESSION['sr_istype2'] == "actualstock") {
                $pdf->Cell(20, 10, '', '', 0, 'C', 0, '', 1);
            }

            if ($_SESSION['sr_istype2'] == "physicalstock") {
                $pdf->Cell(40, 10, '', '', 0, 'C', 0, '', 1);
            }

            $pdf->Cell(15, 10, $i, 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(50, 10, $row['product_name'], 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(40, 10, $row['category_name'], 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(15, 10, $row['unit'], 'TLB', 0, 'C', 0, '', 1);



            if ($_SESSION['sr_istype2'] != "physicalstock") {
                $pdf->Cell(24, 10, $row['avqty'], 'TLB', 0, 'C', 0, '', 1);
            }

            if ($_SESSION['sr_istype2'] != "actualstock") {
                $pdf->Cell(24, 10, $row['pavqty'], 'TLB', 0, 'C', 0, '', 1);
            }




            $pdf->Cell(25, 10, number_format($row['purchase_price'], 2), 'TLB', 0, 'C', 0, '', 1);
            $pdf->Cell(25, 10, number_format($row['sale_price'], 2), 'TLBR', 0, 'C', 0, '', 1);
            $total_purchase =  $row['purchase_price'] * $row['avqty'];
            $total_sale =  $row['sale_price'] * $row['avqty'];

            if ($_SESSION['sr_istype2'] != "physicalstock") {


                $pdf->Cell(25, 10, number_format($total_purchase, 2), 'TLB', 0, 'C', 0, '', 1);
                $pdf->Cell(25, 10, number_format($total_sale, 2), 'TLBR', 0, 'C', 0, '', 1);
            }
            $i = $i + 1;
        }




        $date = date('Y-m-d');
        $filename =  $_SESSION['header'] . "_$date.pdf";
        $pdf->Output($filename, 'I');
    }








    public function generate_cashbook()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Cash Book');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Cash Book", $_SESSION['cb_istype'], $_SESSION['cbfrom_date'], $_SESSION['cbto_date']);


        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(37, 10, 'Date', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(37, 10, 'Incident', 'TB', 0, 'L', 0, '', 1);
        $pdf->Cell(37, 10, 'Payment Method', 'TB', 0, 'C', 0, '', 1);
        $pdf->Cell(37, 10, 'Invoice No', 'TB', 0, 'C', 0, '', 1);
        $pdf->Cell(37, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);




        $pdf->Ln(10);

        $data =  $_SESSION['cashbook'];
        $lineHeight = 10;
        $maxY = 270;



        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {



            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Cash Book", $_SESSION['cb_istype'], $_SESSION['cbfrom_date'], $_SESSION['cbto_date']);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(37, 10, 'Date', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(37, 10, 'Incident', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(37, 10, 'Payment Method', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(37, 10, 'Invoice No', 'TB', 0, 'L', 0, '', 1);
                $pdf->Cell(37, 10, 'Amount', 'TB', 0, 'R', 0, '', 1);
                $pdf->Ln(10);
            }
            if ($row['incidenttype'] == "Purchase") {
                $patotal = $patotal - $row['grandTotal'];
                $total = $total - $row['grandTotal'];
            } else {
                $patotal = $patotal + $row['grandTotal'];
                $total = $total + $row['grandTotal'];
            }


            $pdf->SetFont('', '', 10);
            $pdf->Cell(37, 8, $row['date'], 0, 0, 'L');
            $pdf->Cell(37, 8,  $row['incidenttype'], 0, 0, 'L');
            $pdf->Cell(37, 8,  $row['payment_method'], 0, 0, 'C');
            $pdf->Cell(37, 8,  $row['invoice_no'], 0, 0, 'C');

            $grandtotal = number_format($row['grandTotal'], 2);

            if ($row['incidenttype'] == "Purchase") {
                $pdf->Cell(37, 8, "-" . $grandtotal, 0, 0, 'R');
            } else {
                $pdf->Cell(37, 8,  $grandtotal, 0, 0, 'R');
            }
            $pdf->Ln(8);



            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(50, 10, "Total Amount:", 'TB', 0, 'L');
        $pdf->Cell(100, 10, "", 'TB', 0, 'L');
        $pdf->Cell(35, 10, number_format($total, 2), 'TB', 1, 'R');

        $pdf->updatePageTotal($patotal);



        $date = date('Y-m-d');
        $filename = "Cash Book_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function bdtask_stock_audit_report()
    {
        $encryption_key = Config::$encryption_key;

        if (!$this->permission1->method('stock_audit_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $product_list = $this->report_model->product_list();
        $store_list = $this->report_model->store_list();


        $data = array(
            'title'          => display('stock_audit_report'),
            'product_list'   => $product_list,
            'store_list'     => $store_list,


        );
        $data['module']   = "report";
        $data['page']     = "stock_audit_report";
        echo modules::run('template/layout', $data);
    }


    public function audit_stock_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $productid = $this->input->post('productid');
        $encryption_key = Config::$encryption_key;
        $storeid  = $this->input->post('storeid');
        $incident = $this->input->post('incident');
        $scenario = $this->input->post('scenario');

        $query = $this->db->query("CALL sp_get_stock_audit(?, ?, ?, ?, ?, ?, ?)", [
            $encryption_key,
            $productid,
            $from_date,
            $to_date,
            $scenario,
            $incident,
            $storeid
        ]);

        if ($query->num_rows() > 0) {
            $report_data = $query->result_array();
        } else {
            $report_data = [];
        }

        echo json_encode($report_data);
    }

    public function audit_stock_report_sync()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->query("CALL sp_insert_stock_audit(?)", [
            $encryption_key
        ]);
        echo json_encode("Success");
    }

    public function generate_auditreport()
    {
        $data = json_decode($_POST['aulist'], true);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Stock Audit Report  ' . $_POST['fdate'] . " To " . $_POST['tdate']);
        $sheet->mergeCells("A1:H1");
        $sheet->getStyle("A1:H1")->getFont()->setBold(true);
        $sheet->getStyle("A1:H1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:H1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);





        // === HEADER ROWS ===
        $headers = ['Date', 'Product', 'Scenario', 'Incident', 'Voucher No', 'Store', 'Actual Stock', 'Physical Stock'];
        $columnWidths = [17, 35, 20, 20, 17, 14, 14, 14];

        //header
        $sheet->fromArray($headers, NULL, 'A2');

        foreach ($headers as $index => $header) {
            $colLetter = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->getColumnDimension($colLetter)->setWidth($columnWidths[$index]);
        }

        $startRow = 3;
        $range = "A$2:H$2";
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getFont()->setBold(true);


        $scenario = '';
        $voucher_id = '';
        $stockktotal = 0;
        $phystockktotal = 0;



        foreach ($data as $row) {
            if ($scenario == $row['scenario'] && $voucher_id == $row['voucher_id']) {
                $startRow = $startRow - 1;

                $storecell = $sheet->getCell('F' . $startRow)->getValue() . "\n" . $row['storename'];
                $stockcell = $sheet->getCell('G' . $startRow)->getValue() . "\n" . $row['stock'];
                $phystockcell = $sheet->getCell('H' . $startRow)->getValue() . "\n" . $row['phystock'];

                $sheet->setCellValue('F' . $startRow, $storecell);
                $sheet->setCellValue('G' . $startRow, $stockcell);
                $sheet->setCellValue('H' . $startRow, $phystockcell);

                $sheet->getStyle('F' . $startRow)->getAlignment()->setWrapText(true);
            } else {
                $sheet->setCellValue('A' . $startRow, $row['date']);
                $sheet->setCellValue('B' . $startRow, $row['product_name']);

                if ($row['scenario'] == "purchaseinvoice") {
                    $sheet->setCellValue('C' . $startRow, "Purchase Invoice");
                }

                if ($row['scenario'] == "saleinvoice") {
                    $sheet->setCellValue('C' . $startRow, "Sale Invoice");
                }

                if ($row['scenario'] == "stock") {
                    $sheet->setCellValue('C' . $startRow, "Stock");
                }

                if ($row['scenario'] == "GRN") {
                    $sheet->setCellValue('C' . $startRow, "GRN");
                }

                if ($row['scenario'] == "GDN") {
                    $sheet->setCellValue('C' . $startRow, "GDN");
                }
                if ($row['incident'] == "localpurchase") {
                    $sheet->setCellValue('D' . $startRow, "Local Purchase");
                }

                if ($row['incident'] == "internationalpurchase") {
                    $sheet->setCellValue('D' . $startRow, "International Purchase");
                }

                if ($row['incident'] == "sale") {
                    $sheet->setCellValue('D' . $startRow, "Sale");
                }

                if ($row['incident'] == "wholesale") {
                    $sheet->setCellValue('D' . $startRow, "Whole Sale");
                }

                if ($row['incident'] == "openingstock") {
                    $sheet->setCellValue('D' . $startRow, "Opening Stock");
                }

                if ($row['incident'] == "opening_stock") {
                    $sheet->setCellValue('D' . $startRow, "Opening Stock");
                }

                if ($row['incident'] == "storetransfer") {
                    $sheet->setCellValue('D' . $startRow, "Store Transfer");
                }

                if ($row['incident'] == "stockdisposal") {
                    $sheet->setCellValue('D' . $startRow, "Stock Disposal");
                }
                if ($row['incident'] == "stockadjustment") {
                    $sheet->setCellValue('D' . $startRow, "Stock Adjustment");
                }

                if ($row['incident'] == "purchase") {
                    $sheet->setCellValue('D' . $startRow, "Purchase");
                }

                if ($row['incident'] == "salesreturn") {
                    $sheet->setCellValue('D' . $startRow, "Sales Return");
                }

                if ($row['incident'] == "storetransfer") {
                    $sheet->setCellValue('D' . $startRow, "Store Transfer");
                }
                if ($row['incident'] == "purchasereturn") {
                    $sheet->setCellValue('D' . $startRow, "Purchase Return");
                }

                if ($row['incident'] == "stockdisposal") {
                    $sheet->setCellValue('D' . $startRow, "Stock Disposal");
                }

                $sheet->setCellValue('E' . $startRow, $row['voucherno']);
                $sheet->setCellValue('F' . $startRow, $row['storename']);
                $sheet->setCellValue('G' . $startRow, $row['stock']);
                $sheet->setCellValue('H' . $startRow, $row['phystock']);
            }
            $sheet->getStyle('G' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            $sheet->getStyle('H' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);

            $range = "A$startRow:H$startRow";
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $scenario = $row['scenario'];
            $voucher_id = $row['voucher_id'];

            $phystockktotal = $row['phystock'] + $phystockktotal;
            $stockktotal = $row['stock'] + $stockktotal;

            $startRow++;
        }


        $sheet->setCellValue('A' . $startRow, 'Available Quantity');
        $startCol = 1;
        $endCol = 6;
        $row = $startRow;
        $startLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
        $endLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);
        $mergeRange = "{$startLetter}{$row}:{$endLetter}{$row}";
        $sheet->mergeCells($mergeRange);

        $sheet->setCellValue('G' . $startRow, $stockktotal);
        $sheet->setCellValue('H' . $startRow, $phystockktotal);
        $sheet->getStyle('G' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        $sheet->getStyle('H' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);

        $range = "A$startRow:H$startRow";
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getFont()->setBold(true);


        // $sheet->getStyle($mergeRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);



        // === OUTPUT DOWNLOAD ===
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="OT_Reportcheck.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
