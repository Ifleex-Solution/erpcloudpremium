<?php
defined('BASEPATH') or exit('No direct script access allowed');

#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Report_model extends CI_Model
{

    public function bdtask_getStock($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (a.product_name like '%" . $searchValue . "%' or a.product_model like '%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchValue != '') {
            $this->db->where($searchQuery);
        }
        $this->db->group_by('a.product_id');
        $records = $this->db->get()->num_rows();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchValue != '') {
            $this->db->where($searchQuery);
        }
        $this->db->group_by('a.product_id');
        $records = $this->db->get()->num_rows();
        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select("a.*,
                a.product_name,
                a.product_id,
                a.product_model
                ");
        $this->db->from('product_information a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->group_by('a.product_id');
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $stockin = $this->db->select('sum(quantity) as totalSalesQnty')->from('invoice_details')->where('product_id', $record->product_id)->get()->row();
            $stockout = $this->db->select('sum(quantity) as totalPurchaseQnty,Avg(rate) as purchaseprice')->from('product_purchase_details')->where('product_id', $record->product_id)->get()->row();


            $sprice = (!empty($record->price) ? $record->price : 0);
            $pprice = (!empty($stockout->purchaseprice) ? sprintf('%0.2f', $stockout->purchaseprice) : 0);
            $stock =  (!empty($stockout->totalPurchaseQnty) ? $stockout->totalPurchaseQnty : 0) - (!empty($stockin->totalSalesQnty) ? $stockin->totalSalesQnty : 0);
            $data[] = array(
                'sl'            =>   $sl,
                'product_name'  =>  $record->product_name,
                'product_model' =>  $record->product_model,
                'sales_price'   =>  sprintf('%0.2f', $sprice),
                'purchase_p'    =>  $pprice,
                'totalPurchaseQnty' => $stockout->totalPurchaseQnty,
                'totalSalesQnty' =>  $stockin->totalSalesQnty,
                'stok_quantity' => sprintf('%0.2f', $stock),

                'total_sale_price' => ($stockout->totalPurchaseQnty - $stockin->totalSalesQnty) * $sprice,
                'purchase_total' => ($stockout->totalPurchaseQnty - $stockin->totalSalesQnty) * $pprice,
            );
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }



    public function totalnumberof_product()
    {

        $this->db->select("a.*,
                a.product_name,
                a.product_id,
                a.product_model,
                c.supplier_price
                ");
        $this->db->from('product_information a');

        $this->db->join('supplier_product c', 'c.product_id = a.product_id', 'left');
        $this->db->group_by('a.product_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }


    public function accounts_closing_data()
    {
        $last_closing_amount = $this->get_last_closing_amount();
        $cash_in = $this->cash_data_receipt();
        $cash_out = $this->cash_data();
        if ($last_closing_amount != null) {
            $last_closing_amount = $last_closing_amount[0]['amount'];
            $cash_in_hand = ($last_closing_amount + $cash_in) - $cash_out;
        } else {
            $last_closing_amount = 0;
            $cash_in_hand = $cash_in - $cash_out;
        }


        return array(
            "last_day_closing" => number_format($last_closing_amount, 2, '.', ','),
            "cash_in"          => number_format($cash_in, 2, '.', ','),
            "cash_out"         => number_format($cash_out, 2, '.', ','),
            "cash_in_hand"     => number_format($cash_in_hand, 2, '.', ',')
        );
    }

    public function get_last_closing_amount()
    {
        $sql = "SELECT amount FROM daily_closing WHERE date = (SELECT MAX(date) FROM daily_closing)";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if ($result) {
            return $result;
        } else {
            return FALSE;
        }
    }

    public function cash_data_receipt()
    {
        date_default_timezone_set('Asia/Colombo');

        //-----------
        $cash = 0;
        $datse = date('Y-m-d');
        $this->db->select('sum(Debit) as amount');
        $this->db->from('acc_transaction');
        $this->db->where('COAID', 111000001);
        $this->db->where('VDate', $datse);
        $result_amount = $this->db->get();
        $amount = $result_amount->result_array();
        $cash += $amount[0]['amount'];
        return $cash;
    }


    public function cash_data()
    {
        date_default_timezone_set('Asia/Colombo');

        //-----------
        $cash = 0;
        $datse = date('Y-m-d');
        $this->db->select('sum(Credit) as amount');
        $this->db->from('acc_transaction');
        $this->db->where('COAID', 111000001);
        $this->db->where('VDate', $datse);
        $result_amount = $this->db->get();
        $amount = $result_amount->result_array();
        $cash += $amount[0]['amount'];
        return $cash;
    }

    //CLOSING ENTRY
    public function daily_closing_entry($data)
    {
        return $this->db->insert('daily_closing', $data);
    }



    public function get_closing_report()
    {
        $this->db->select("* ,(opening_balance + amount_in) - amount_out as 'cash_in_hand'");
        $this->db->from('closing_records');
        $this->db->where('status', 1);
        $this->db->order_by('datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_date_wise_closing_report($from_date, $to_date)
    {
        $dateRange = "DATE(datetime) BETWEEN '$from_date' AND '$to_date'";
        $this->db->select("* ,(opening_balance + amount_in) - amount_out as 'cash_in_hand'");
        $this->db->from('closing_records');
        $this->db->where('status', 1);
        $this->db->where($dateRange, NULL, FALSE);
        $this->db->order_by('datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }


    //Retrieve todays_sales_report
    public function todays_sales_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $usertype = $this->session->userdata('user_level2');

        $type2 = $usertype == 3 ? "B" : "A";
        $encryption_key = Config::$encryption_key;


        $today = date('Y-m-d');
        $this->db->select("a.*,b.customer_id,b.customer_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date', $today);
        $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $type2);

        $this->db->order_by('a.invoice_id', 'desc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function getSalesReportList($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.invoice_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.customer_id,b.customer_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $customer = $record->customer_name;

            $data[] = array(
                'date'                   => $record->date,
                'invoice_id'             => $record->invoice_id,
                'customer_name'          => $customer,
                'due_amount'             => $record->due_amount,
                'total_amount'     => $record->total_amount,
            );
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $data
        );

        return $response;
    }

    //Retrieve all Report
    public function sales_reportinvoicewise($from_date, $to_date, $empid,$branch)
    {
        $encryption_key = Config::$encryption_key;
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

        $this->db->select("a.date,AES_DECRYPT(a.sale_id,'" . $encryption_key . "') as invoiceno, AES_DECRYPT(b.customer_name, '{$encryption_key}') AS customer_name,AES_DECRYPT(a.grandTotal,'" . $encryption_key . "')  as total");
        $this->db->from('sale a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $empid);
        }
        if ($branch) {
            $this->db->where("a.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('a.branch', $branchids);
            }
        }
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve todays_purchase_report
    public function todays_purchase_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_date', $today);
        $this->db->order_by('a.purchase_id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //    ======= its for  todays_customer_receipt ===========
    public function todays_customer_receipt($today = null)
    {
        $this->db->select('a.*,b.HeadName, c.name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
        $this->db->join('acc_subcode c', 'a.subCode=c.id');
        $this->db->where('a.subType', 3);
        $this->db->where('a.Credit >', 0);
        $this->db->where('DATE(a.VDate)', $today);
        $this->db->where('a.IsAppove', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function filter_customer_wise_receipt($custome_id = null, $from_date = null)
    {
        $this->db->select('a.Narration,b.HeadName,a.Credit,b.HeadName, c.name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
        $this->db->join('acc_subcode c', 'a.subCode=c.id');
        $this->db->where('c.referenceNo', $custome_id);
        $this->db->where('a.Credit >', 0);
        $this->db->where('a.subType', 3);
        $this->db->where('DATE(a.VDate)', $from_date);
        $this->db->where('a.IsAppove', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function customerinfo_rpt($customer_id)
    {
        return $this->db->select('*')
            ->from('customer_information')
            ->where('customer_id', $customer_id)
            ->get()
            ->result_array();
    }


    // ======================= user sales report ================
    public function user_sales_report($from_date, $to_date, $user_id, $emp_id, $branch)
    {
        $encryption_key = Config::$encryption_key;

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
        $this->db->select("sum(AES_DECRYPT(a.grandTotal,'" . $encryption_key . "'))  as amount,count(a.id) as toal_invoice,b.first_name,b.last_name");
        $this->db->from('sale a');
        $this->db->join('employee_history b', 'b.id = a.employee_id', 'left');
        if (!empty($user_id)) {
            $this->db->where('a.employee_id', $user_id);
        }
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        if ($emp_id != "All") {
            $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $emp_id);
        }
        $this->db->where('a.date >=', $from_date);

        if ($branch) {
            $this->db->where("a.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('a.branch', $branchids);
            }
        }

        $this->db->group_by('a.employee_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function userList()
    {
        $this->db->select("*");
        $this->db->from('employee_history');
        $this->db->order_by('first_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function retrieve_dateWise_DueReports($from_date, $to_date)
    {
        $this->db->select("a.*,b.*,c.*");
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date BETWEEN "' . $from_date . '" and "' . $to_date . '"');
        $this->db->where('a.due_amount >', 0);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function get_retrieve_dateWise_DueReports($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        // dd($datbetween);

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.invoice_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->distinct();
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->distinct();
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->distinct();
        $this->db->select("a.*,b.customer_id,b.customer_name");
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        // dd($records);
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $customer = $record->customer_name;

            $data[] = array(
                'date'                   => $record->date,
                'invoice_id'             => $record->invoice_id,
                'customer_name'          => $customer,
                'total_amount'           => $record->total_amount,
                'paid_amount'            => $record->paid_amount,
                'due_amount'             => $record->due_amount,
            );
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $data
        );

        return $response;
    }



    // ================= Shipping cost ===========================
    public function retrieve_dateWise_Shippingcost($from_date, $to_date)
    {
        $this->db->select("a.*");
        $this->db->from('invoice a');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }



    //Retrieve todays_purchase_report
    public function bdtask_purchase_report($from_date, $to_date, $empid, $branch)
    {
        $encryption_key = Config::$encryption_key;

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


        date_default_timezone_set('Asia/Colombo');
        $this->db->select("a.date,AES_DECRYPT(a.chalan_no,'" . $encryption_key . "') as invoiceno,AES_DECRYPT(b.supplier_name,'" . $encryption_key . "') as supplier_name,AES_DECRYPT(a.grandTotal,'" . $encryption_key . "')  as total");
        $this->db->from('purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $empid);
        }
        if ($branch) {
            $this->db->where("a.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('a.branch', $branchids);
            }
        }
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function getReportList($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.purchase_date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        // dd($datbetween);

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (a.purchase_date like '%" . $searchValue . "%' or a.purchase_id like '%" . $searchValue . "%' or a.grand_total_amount like'%" . $searchValue . "%' or b.supplier_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $purchase_amount = 0;
        // dd($records);
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $supplier = $record->supplier_name;

            $data[] = array(
                'purchase_date'          => $record->purchase_date,
                'purchase_id'            => $record->purchase_id,
                'supplier_name'          => $supplier,
                'due_amount'             => $record->due_amount,
                'grand_total_amount'     => $record->grand_total_amount,
            );
            $purchase_amount += $record->grand_total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "purchase_amount" => $purchase_amount,
            "aaData" => $data
        );

        return $response;
    }


    public function category_list_product()
    {
        $this->db->select('*');
        $this->db->from('product_category');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_store()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    //    ============= its for purchase_report_category_wise ===============
    public function purchase_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch)
    {
        $encryption_key = Config::$encryption_key;
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

        $this->db->select("b.product_name, b.product_model, 
        sum(IFNULL(AES_DECRYPT(a.quantity,'" . $encryption_key . "'), 0)) as quantity, 
        sum(IFNULL(AES_DECRYPT(a.total_price,'" . $encryption_key . "'), 0)) as total_price, 
        d.date, c.category_name");
        $this->db->from('purchase_details a');
        $this->db->join('product_information b', 'b.id = a.product');
        $this->db->join('product_category c', 'c.category_id = b.category_id');
        $this->db->join('purchase d', 'd.id = a.pid');
        $this->db->where('d.date >=', $from_date);
        $this->db->where('d.date <=', $to_date);

        if ($product) {
            $this->db->where('a.product', $product);
        }
        if ($category) {
            $this->db->where('b.category_id', $category);
        }
        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(d.type2,'" . $encryption_key . "')", $empid);
        }
        if ($branch) {
            $this->db->where("d.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('d.branch', $branchids);
            }
        }
        $this->db->group_by('b.product_id, c.category_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }


    //RETRIEVE DATE WISE SINGE PRODUCT REPORT
    public function retrieve_product_sales_report($from_date, $to_date, $product_id, $empid, $branch)
    {

        $encryption_key = Config::$encryption_key;
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

        $this->db->select("b.product_name,c.date,AES_DECRYPT(c.sale_id,'" . $encryption_key . "') as sale_id,
        AES_DECRYPT(a.product_rate,'" . $encryption_key . "') as product_rate,AES_DECRYPT(a.quantity,'" . $encryption_key . "') as quantity,AES_DECRYPT(a.total_price,'" . $encryption_key . "') as total,AES_DECRYPT(d.customer_name, '{$encryption_key}') AS customer_name");
        $this->db->from('sale_details a');
        $this->db->join('product_information b', 'b.id = a.product');
        $this->db->join('sale c', 'c.id = a.pid');
        $this->db->join('customer_information d', 'd.customer_id = c.customer_id');
        $this->db->where('c.date >=', $from_date);
        $this->db->where('c.date <=', $to_date);
        if ($product_id) {
            $this->db->where('a.product', $product_id);
        }

        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(c.type2,'" . $encryption_key . "')", $empid);
        }
        if ($branch) {
            $this->db->where("c.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('c.branch', $branchids);
            }
        }
        $this->db->order_by('c.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function product_list()
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->where('status', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    //    ============= its for sales_report_category_wise ===============
    public function sales_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch)
    {
        $encryption_key = Config::$encryption_key;
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

        $this->db->select("b.product_name, b.product_model, 
        sum(IFNULL(AES_DECRYPT(a.quantity,'" . $encryption_key . "'), 0)) as quantity, 
        sum(IFNULL(AES_DECRYPT(a.total_price,'" . $encryption_key . "'), 0)) as total_price, 
        d.date, c.category_name");
        $this->db->from('sale_details a');
        $this->db->join('product_information b', 'b.id = a.product');
        $this->db->join('product_category c', 'c.category_id = b.category_id');
        $this->db->join('sale d', 'd.id = a.pid');
        $this->db->where('d.date >=', $from_date);
        $this->db->where('d.date <=', $to_date);
        $this->db->where_not_in('d.incidenttype', [2]);

        if ($product) {
            $this->db->where('a.product', $product);
        }

        if ($branch) {
            $this->db->where("d.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('d.branch', $branchids);
            }
        }

        if ($category) {
            $this->db->where('b.category_id', $category);
        }
        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(d.type2,'" . $encryption_key . "')", $empid);
        }
        $this->db->group_by('b.product_id, c.category_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }



    // sales return data
    public function sales_return_list($start, $end)
    {
        $this->db->select('a.net_total_amount,a.*,b.customer_name');
        $this->db->from('product_return a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('usablity', 1);
        $this->db->where('a.date_return >=', $start);
        $this->db->where('a.date_return <=', $end);
        $this->db->order_by('a.date_return', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    // return supplier
    public function supplier_return($start, $end)
    {
        $this->db->select('a.net_total_amount,a.*,b.supplier_name');
        $this->db->from('product_return a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('usablity', 2);
        $this->db->where('a.date_return >=', $start);
        $this->db->where('a.date_return <=', $end);
        $this->db->order_by('a.date_return', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    // tax report query
    public function retrieve_dateWise_tax($from_date, $to_date)
    {
        $this->db->select("a.*");
        $this->db->from('invoice a');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    //Total profit report
    public function total_profit_report($start_date, $end_date)
    {
        $this->db->select("a.date,a.invoice,b.invoice_id, CAST(sum(total_price) AS DECIMAL(16,2)) as total_sale");
        $this->db->select('CAST(sum(`quantity`*`supplier_rate`) AS DECIMAL(16,2)) as total_supplier_rate', FALSE);
        $this->db->select("CAST(SUM(total_price) - SUM(`quantity`*`supplier_rate`) AS DECIMAL(16,2)) AS total_profit");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.id');
        $this->db->where('a.date >=', $start_date);
        $this->db->where('a.date <=', $end_date);
        $this->db->group_by('b.invoice_id');
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function payment_methods()
    {
        return $data = $this->db->select('*')
            ->from('acc_coa')
            ->where('PHeadName', 'Cash')
            ->or_where('PHeadName', 'Cash at Bank')
            ->get()
            ->result();
    }

    public function received_bypayment_method($seller_id, $headcode)
    {
        $data = $this->db->select('sum(Debit) as total_received')
            ->from('acc_transaction')
            ->where('COAID', $headcode)
            ->where('CreateBy', $seller_id)
            ->where('VDate', date('Y-m-d'))
            ->where('IsAppove', 1)
            ->get()
            ->row();
        return ($data ? $data->total_received : 0);
    }

    public function paid_bypayment_method($seller_id, $headcode)
    {
        $data = $this->db->select('sum(Credit) as total_paid')
            ->from('acc_transaction')
            ->where('COAID', $headcode)
            ->where('CreateBy', $seller_id)
            ->where('VDate', date('Y-m-d'))
            ->where('IsAppove', 1)
            ->get()
            ->row();
        return ($data ? $data->total_paid : 0);
    }

    public function create_opening($data = [])
    {
        return $this->db->insert('closing_records', $data);
    }


    public function store_list()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->where('status', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
}
