<?php defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Home_model extends CI_Model
{

    public function total_sales_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(total_amount) as totalsales");
        $this->db->from('invoice');
        $this->db->where('date >=', $days['start_date']);
        $this->db->where('date <=', $days['end_date']);
        $query = $this->db->get()->row();
        return (!empty($query->totalsales) ? $query->totalsales : 1);
    }
    public function yearmonthval($date)
    {
        list($month, $year) = explode(' ', $date);
        switch ($month) {
            case "January":
                $month = '01';
                break;
            case "February":
                $month = '02';
                break;
            case "March":
                $month = '03';
                break;
            case "April":
                $month = '04';
                break;
            case "May":
                $month = '05';
                break;
            case "June":
                $month = '06';
                break;
            case "July":
                $month = '07';
                break;
            case "August":
                $month = '08';
                break;
            case "September":
                $month = '09';
                break;
            case "October":
                $month = '10';
                break;
            case "November":
                $month = '11';
                break;
            case "December":
                $month = '12';
                break;
        }
        $fdate = $year . '-' . $month . '-' . '01';
        $lastday = date('t', strtotime($fdate));
        $edate = $year . '-' . $month . '-' . $lastday;
        $startd    = $fdate;
        $data['start_date'] = $startd;
        $data['end_date'] = $edate;
        return $data;
    }


    public function total_purchase_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(grand_total_amount) as totalpurchase");
        $this->db->from('product_purchase');
        $this->db->where('purchase_date >=', $days['start_date']);
        $this->db->where('purchase_date <=', $days['end_date']);
        $query = $this->db->get();
        if (!empty($query->row()->totalpurchase)) {
            return $query->row()->totalpurchase;
        } else {
            return 1;
        }
    }

    public function total_expense_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("*");
        $this->db->where('PHeadName', 'Expence');
        $this->db->from('acc_coa');
        $query = $this->db->get();
        $result =  $query->result_array();
        $totalamount = 0;
        foreach ($result as $expense) {
            $amount = $this->db->select('ifnull(sum(Debit),0) as amount')->from('acc_transaction')->where('VDate >=', $days['start_date'])->where('VDate <=', $days['end_date'])->where('COAID', $expense['HeadCode'])->get()->row();
            $totalamount = $totalamount + $amount->amount;
        }

        return (!empty($totalamount) ? $totalamount : 1);
    }


    // Total Employee Salary
    public function total_employee_salary($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(total_salary) as totalsalary");
        $this->db->from('employee_salary_payment');
        $this->db->where('payment_date >=', $days['start_date']);
        $this->db->where('payment_date <=', $days['end_date']);
        $query = $this->db->get();
        if (!empty($query->row()->totalsalary)) {
            return $query->row()->totalsalary;
        } else {
            return 1;
        }
    }

    public function total_service_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(total_amount) as totalservice");
        $this->db->from('service_invoice');
        $this->db->where('date >=', $days['start_date']);
        $this->db->where('date <=', $days['end_date']);
        $query = $this->db->get();
        if (!empty($query->row()->totalservice)) {
            return $query->row()->totalservice;
        } else {
            return 1;
        }
    }


    public function yearly_invoice_report($month = null)
    {
        $encryption_key = Config::$encryption_key;

        $usertype = $this->session->userdata('user_level2');

        $type2 = $usertype == 3 ? "B" : "A";

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

        if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            // Convert branch ID array to comma-separated string
            $inClause = implode(',', array_map('intval', $branchids));

            $query = $this->db->query(
                "
        SELECT SUM(AES_DECRYPT(grandTotal, ?)) AS total_sale 
        FROM sale
        WHERE MONTH(date) = ? 
            AND CAST(AES_DECRYPT(type2, ?) AS CHAR) = ? AND branch IN ($inClause) 
            AND YEAR(date) = YEAR(CURRENT_TIMESTAMP)",
                array($encryption_key, $month, $encryption_key, $type2)
            );
        } else {
            $query = $this->db->query(
                "
        SELECT SUM(AES_DECRYPT(grandTotal, ?)) AS total_sale 
        FROM sale
        WHERE MONTH(date) = ? 
            AND CAST(AES_DECRYPT(type2, ?) AS CHAR) = ? 
            AND YEAR(date) = YEAR(CURRENT_TIMESTAMP)",
                array($encryption_key, $month, $encryption_key, $type2)
            );
        }



        return $query->row();
    }

    public function yearly_purchase_report($month = null)
    {

        $encryption_key = Config::$encryption_key;
        $usertype = $this->session->userdata('user_level2');

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

        $type2 = $usertype == 3 ? "B" : "A";

       

        if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            // Convert branch ID array to comma-separated string
            $inClause = implode(',', array_map('intval', $branchids));

            $query = $this->db->query(
                "
            SELECT SUM(AES_DECRYPT(grandTotal, ?)) AS total_purchase 
            FROM purchase
            WHERE MONTH(date) = ? AND branch IN ($inClause) 
                AND AES_DECRYPT(type2, ?) = ? 
                AND YEAR(date) = YEAR(CURRENT_TIMESTAMP)",
                array($encryption_key, $month, $encryption_key, $type2)
            );
        } else {
            $query = $this->db->query(
                "
            SELECT SUM(AES_DECRYPT(grandTotal, ?)) AS total_purchase 
            FROM purchase
            WHERE MONTH(date) = ? 
                AND AES_DECRYPT(type2, ?) = ? 
                AND YEAR(date) = YEAR(CURRENT_TIMESTAMP)",
                array($encryption_key, $month, $encryption_key, $type2)
            );
        }

        return $query->row();
    }


    //    ======= its for  best_sales_products ===========
    public function best_sales_products()
    {
        $this->db->select('b.product_id, b.product_name, sum(a.quantity) as quantity');
        $this->db->from('invoice_details a');
        $this->db->join('product_information b', 'b.product_id = a.product_id');
        $this->db->group_by('b.product_id');
        $this->db->order_by('quantity', 'desc');
        $query = $this->db->get();
        return $query->result();
    }


    //Count todays_sales_report
    public function todays_sales_report()
    {
        date_default_timezone_set('Asia/Colombo');
        $encryption_key = Config::$encryption_key;

        $usertype = $this->session->userdata('user_level2');

        $type2 = $usertype == 3 ? "B" : "A";
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


        $today = date('Y-m-d');
        $this->db->select("a.id as invoice_id1,a.*, AES_DECRYPT(b.customer_name, '" . $encryption_key . "') as customer_name, b.customer_id, AES_DECRYPT(a.sale_id, '" . $encryption_key . "') AS sale_id, AES_DECRYPT(a.grandTotal, '" . $encryption_key . "') AS grandTotal,c.name as pay");
        $this->db->from('sale a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('payment_type c', 'c.id = a.payment_type');

        $this->db->where('a.date', $today);
        $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $type2);

        if ($this->session->userdata('user_level2') != 1) {
            $this->db->where_in('a.branch', $branchids);
        }

        $this->db->limit(50);
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }
    //Count todays_sales_due_report
    public function todays_sales_due()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select('a.*,b.customer_name, b.customer_id, a.invoice_id,a.invoice');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date', $today);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit(10);
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }
    //Count todays_purchase_due_report
    public function todays_purchase_due()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select('a.*,b.supplier_name, b.supplier_id, a.purchase_id,a.chalan_no');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_date', $today);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit(10);
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }


    //Retrieve todays_total_sales_report
    public function todays_total_sales_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("a.date,a.invoice,b.invoice_id, sum(a.total_amount) as total_amt, sum(b.total_price) as total_sale,sum(`quantity`*`supplier_rate`) as total_supplier_rate,(SUM(total_price) - SUM(`quantity`*`supplier_rate`)) AS total_profit");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.invoice_id');
        $this->db->where('a.date', $today);
        $this->db->order_by('a.invoice_id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function todays_total_sales_amount()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("sum(total_amount) as total_amount");
        $this->db->from('invoice');
        $this->db->where('date', $today);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    // todays sales product
    public function todays_sale_product()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("c.product_name,c.price");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.invoice_id');
        $this->db->join('product_information c', 'c.product_id = b.product_id');
        $this->db->order_by('a.date', 'desc');
        $this->db->where('a.date', $today);
        $this->db->limit('3');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve todays_total_sales_report
    public function todays_total_purchase_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("sum(grand_total_amount) as ttl_purchase_amount");
        $this->db->from('product_purchase ');
        $this->db->where('purchase_date', $today);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function best_saler_product_list()
    {
        $encryption_key = Config::$encryption_key;

        // $this->db->select("b.product_id, b.product_name, SUM(AES_DECRYPT(a.quantity,'" . $encryption_key . "')) AS quantity");
        // $this->db->from('sale_details a');
        // $this->db->join('sale s', 's.id = a.pid');
        // $this->db->join('product_information b', 'b.id = a.product');
        // $this->db->where('s.date >=', 'CURDATE() - INTERVAL 365 DAY', FALSE);
        // $this->db->group_by(['b.product_id', 'b.product_name']);
        // $this->db->order_by('quantity', 'DESC');
        // $query = $this->db->get();
        // return $query->result();


        $maxQuery = "
    SELECT MAX(product_total) AS max_quantity
    FROM (
        SELECT SUM(AES_DECRYPT(sd.quantity, '" . $encryption_key . "')) AS product_total
        FROM sale_details sd
        INNER JOIN sale s ON s.id = sd.pid
        WHERE s.date >= CURDATE() - INTERVAL 365 DAY
    ) AS sub
";

        $maxResult = $this->db->query($maxQuery)->row();
        $max_quantity = $maxResult->max_quantity ?? 1; // avoid divide-by-zero

        $this->db->select("
    b.product_id,
    b.product_name,
    ROUND(SUM(AES_DECRYPT(a.quantity,'" . $encryption_key . "')) / $max_quantity * 100, 2) AS quantity
", FALSE);

        $this->db->from('sale_details a');
        $this->db->join('sale s', 's.id = a.pid');
        $this->db->join('product_information b', 'b.id = a.product');
        $this->db->where('s.date >=', 'CURDATE() - INTERVAL 365 DAY', FALSE);
        $this->db->group_by(['b.product_id', 'b.product_name']);
        $this->db->order_by('quantity', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function out_of_stock()
    {

        // $this->db->select("a.unit,a.product_name,a.product_id,a.price,a.product_model,(select sum(quantity) from invoice_details where product_id= `a`.`product_id`) as 'totalSalesQnty',(select sum(quantity) from product_purchase_details where product_id= `a`.`product_id`) as 'totalBuyQnty'");
        // $this->db->from('product_information a');
        // $this->db->where(array('a.status' => 1));
        // $this->db->group_by('a.product_id');
        $encryption_key = Config::$encryption_key;

        $this->db->select("
        pi.product_id,
        pi.product_name,
        pi.unit,
        pc.category_name,
        (SELECT SUM(CAST(AES_DECRYPT(sd.stock, '" . $encryption_key . "') AS SIGNED)) 
         FROM stock_details sd 
         WHERE sd.product = pi.product_id) AS av_stock
    ");
        $this->db->from('product_information pi');
        $this->db->join('product_category pc', 'pc.category_id = pi.category_id', 'left');
        $this->db->having('av_stock <=', 21);
        $query = $this->db->get();
        $result = $query->result_array();
        $stock = [];
        $i = 0;
        foreach ($result as $stockproduct) {
            // $stokqty = $stockproduct['totalBuyQnty'] - $stockproduct['totalSalesQnty'];
            // if ($stokqty < 10) {

            $stock[$i]['stock']         = $stockproduct['av_stock'];
            $stock[$i]['product_id']    = $stockproduct['product_id'];
            $stock[$i]['product_name']  = $stockproduct['product_name'];
            $stock[$i]['category_name'] = $stockproduct['category_name'];
            $stock[$i]['unit']          = $stockproduct['unit'];
            // }
            $i++;
        }
        return $stock;
    }

    public function notification()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
        id,
    type,
    AES_DECRYPT(invoiceno, '" . $encryption_key . "') AS invoiceno,
    AES_DECRYPT(customername, '" . $encryption_key . "') AS customername,
    AES_DECRYPT(suppliername, '" . $encryption_key . "') AS suppliername,
    date,
    status,
    store,
    AES_DECRYPT(type2, '" . $encryption_key . "') AS type2
");
        $this->db->from('notification');
        $this->db->where('status', '0');
        $this->db->where('store', $this->session->userdata('email'));
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();
        $result = $query->result_array();

        return  $result;
    }


    public function profile_edit_data()
    {
        $user_id = $this->session->userdata('id');
        $this->db->select('a.*,b.username,a.logo');
        $this->db->from('users a');
        $this->db->join('user_login b', 'b.user_id = a.user_id');
        $this->db->where('a.user_id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function profile_update()
    {
        $logo = $this->fileupload->do_upload(
            './assets/img/user/',
            'logo'

        );

        $old_logo = $this->input->post('old_logo', true);
        $user_id = $this->session->userdata('id');
        $first_name = $this->input->post('first_name', true);
        $last_name = $this->input->post('last_name', true);
        $user_name = $this->input->post('user_name', true);
        $new_logo = (!empty($logo) ? $logo : $old_logo);

        return $this->db->query("UPDATE `users` AS `a`,`user_login` AS `b` SET `a`.`first_name` = '$first_name', `a`.`last_name` = '$last_name', `b`.`username` = '$user_name',`a`.`logo` = '$new_logo' WHERE `a`.`user_id` = '$user_id' AND `a`.`user_id` = `b`.`user_id`");
    }

    public function change_password($email, $old_password, $new_password)
    {
        $user_name = md5("gef" . $new_password);
        $password = md5("gef" . $old_password);
        $this->db->where(array('username' => $email, 'password' => $password, 'status' => 1));
        $query = $this->db->get('user_login');
        $result = $query->result_array();

        if (count($result) == 1) {
            $this->db->set('password', $user_name);
            $this->db->where('password', $password);
            $this->db->where('username', $email);
            $this->db->update('user_login');

            return true;
        }
        return false;
    }
}
