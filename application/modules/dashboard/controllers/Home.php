<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Home extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SESSION sql_mode = ""');
        $this->load->model('home_model');

        if (! $this->session->userdata('isLogIn'))
            redirect('login');
    }

    function index()
    {
        // $best_sales_product  = $this->home_model->best_sales_products();
        // $sales_report        = $this->home_model->todays_total_sales_report();
        // $salesamount         = $this->home_model->todays_total_sales_amount();
        // $todays_sale_product = $this->home_model->todays_sale_product();
        // $purchase_report     = $this->home_model->todays_total_purchase_report();

        if ($this->session->userdata('screen') != 1) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        $tlvmonth            = '';
        $month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        for ($i = 0; $i <= 11; $i++) {
            $tlvmonth .=  $month[$i] . ',';
        }
        $currentyearsale = '';
        for ($i = 1; $i <= 12; $i++) {
            $sold = $this->home_model->yearly_invoice_report($i);
            if (!empty($sold)) {
                $currentyearsale .= $sold->total_sale . ",";
            } else {
                $currentyearsale .= ",";
            }
        }

        $currentyearpurchase = '';
        for ($i = 1; $i <= 12; $i++) {
            $purchase = $this->home_model->yearly_purchase_report($i);
            if (!empty($purchase)) {
                $currentyearpurchase .= $purchase->total_purchase . ",";
            } else {
                $currentyearpurchase .= ",";
            }
        }
        $best_sales_product = $this->best_of_sale();


        $chart_label = $chart_data = '';

        if ($best_sales_product->num_rows() > 0) {
            foreach ($best_sales_product->result_array() as $row) {
                $chart_label .= (!empty($row["product_name"]) ?  $row["product_name"] . ', ' : null);
                $chart_data .= (!empty($row["product_count"]) ? $row["product_count"] . ', ' : null);
            }
        }


        $data['title']         = display('home');
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
        $encryption_key = Config::$encryption_key;

        if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            // Convert branch ID array to comma-separated string
            $inClause = implode(',', array_map('intval', $branchids));

            $query = $this->db->query(
                "
                SELECT COUNT(*) as count 
                FROM sale 
                WHERE DATE(date) = CURDATE() 
                AND AES_DECRYPT(type2, ?) = ? AND branch IN ($inClause)  ",
                array($encryption_key, $type2)
            );
        }else{
            $query = $this->db->query(
                "
                SELECT COUNT(*) as count 
                FROM sale 
                WHERE DATE(date) = CURDATE() 
                AND AES_DECRYPT(type2, ?) = ? ",
                array($encryption_key, $type2)
            );
        }
       
        $salecount = $query->row()->count;
        // $count = $query->row_array()['COUNT(*)'];
        $data = array(
            'title'                => display('dashboard'),
            'total_customer'       => $this->db->count_all('customer_information'),
            'total_product'        => $this->db->count_all('product_information'),
            'total_suppliers'      => $this->db->count_all('supplier_information'),
            'tlvmonthsale'         => $currentyearsale,
            'tlvmonthpurchase'     => $currentyearpurchase,
            'month'                => $tlvmonth,

            'total_sales'          =>   $salecount,
            'todays_sales_report'  => $this->home_model->todays_sales_report(),
            // 'todays_sales_due'     => $this->home_model->todays_sales_due(),
            // 'todays_purchase_due'  => $this->home_model->todays_purchase_due(),
            'chart_label'          => $chart_label,
            'chart_data'           => $chart_data,
            // 'sales_amount'         => number_format($salesamount[0]['total_amount'], 2, '.', ','),
            // 'todays_sale_product'  =>  $todays_sale_product,
            // 'todays_total_purchase' => number_format($purchase_report[0]['ttl_purchase_amount'], 2, '.', ','),
        );
        $data['module']      = "dashboard";
        $data['page']        = "home/home";

        echo Modules::run('template/layout', $data);
    }

    public function best_of_sale()
    {
        $encryption_key = Config::$encryption_key;
        $sql = "
    SELECT 
        pi.product_name,
        sd.product,
        ROUND(
            SUM(AES_DECRYPT(sd.quantity, '" . $encryption_key . "')) /max_qty.max_quantity * 100, 
            2
        ) AS product_count
    FROM 
        sale_details sd
    INNER JOIN 
        sale s ON s.id = sd.pid
    INNER JOIN 
        product_information pi ON pi.id = sd.product
    CROSS JOIN (
        SELECT 
            MAX(product_total) AS max_quantity
        FROM (
            SELECT 
                SUM(AES_DECRYPT(sd.quantity, '" . $encryption_key . "')) AS product_total
            FROM 
                sale_details sd
            INNER JOIN sale s ON s.id = sd.pid
            WHERE s.date >= CURDATE() - INTERVAL 365 DAY
        ) AS sub
    ) AS max_qty
    WHERE 
        s.date >= CURDATE() - INTERVAL 365 DAY
    GROUP BY 
        sd.product
    ORDER BY 
        product_count DESC
    LIMIT 10";

        return $this->db->query($sql);
    }




    public function see_all_best_sales()
    {
        $data['title']                   = display('dashboard');
        $data['best_saler_product_list'] = $this->home_model->best_saler_product_list();
        $data['module']                  = "dashboard";
        $data['page']                    = "home/best_saler_product_list";

        $data['company_info2']    = $this->company_info();


        echo Modules::run('template/layout', $data);
    }

    public function company_info()
    {
        $encryption_key = Config::$encryption_key;

        $data = $this->db->select("
		 company_id,
		 AES_DECRYPT(company_name, '{$encryption_key}') AS company_name,
		 AES_DECRYPT(email, '{$encryption_key}') AS email,
		 AES_DECRYPT(address, '{$encryption_key}') AS address,
		 AES_DECRYPT(mobile, '{$encryption_key}') AS mobile,
		AES_DECRYPT(website, '{$encryption_key}') AS website,
		 AES_DECRYPT(vat_no, '{$encryption_key}') AS vat_no,
		 AES_DECRYPT(cr_no, '{$encryption_key}') AS cr_no,
		 status
	 ")
            ->from('company_information')
            ->get()
            ->result_array();
        return $data;
    }


    public function out_of_stock()
    {
        $data['title']        = display('out_of_stock');
        $data['out_of_stock'] = $this->home_model->out_of_stock();
        $data['module']       = "dashboard";
        $data['page']         = "home/out_of_stock";
        echo Modules::run('template/layout', $data);
    }

    public function notification()
    {
        $data['title']        = "notification";
        $data['notification'] = $this->home_model->notification();
        $data['module']       = "dashboard";
        $data['page']         = "home/notification";
        echo Modules::run('template/layout', $data);
    }

    public function bdtask_updatenotification($id = null)
    {
        $query = "
    UPDATE notification 
    SET 
        status = '1'
    WHERE id = '{$id}';
";
        $this->db->query($query);
        redirect("notification");
    }

    public function setting()
    {
        $data['title']    = "Profile Setting";
        $id = $this->session->userdata('id');
        /*-----------------------------------*/
        $this->form_validation->set_rules('firstname', 'First Name', 'required|max_length[50]');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|max_length[50]');
        #------------------------#
        $this->form_validation->set_rules('email', 'Email Address', "required|valid_email|max_length[100]");
        $this->form_validation->set_rules('password', 'Password', 'max_length[32]|md5');
        $this->form_validation->set_rules('about', 'About', 'max_length[1000]');
        /*-----------------------------------*/
        $config['upload_path']          = './assets/img/user/';
        $config['allowed_types']        = 'gif|jpg|png';

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();
            $image = $config['upload_path'] . $data['file_name'];

            $config['image_library']  = 'gd2';
            $config['source_image']   = $image;
            $config['create_thumb']   = false;
            $config['maintain_ratio'] = TRUE;
            $config['width']          = 115;
            $config['height']         = 90;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $this->session->set_flashdata('message', "Image Upload Successfully!");
        }



        /*-----------------------------------*/
        $data['user'] = (object)$userData = array(
            'id'               => $this->input->post('id'),
            'firstname'   => $this->input->post('firstname'),
            'lastname'       => $this->input->post('lastname'),
            'email'         => $this->input->post('email'),
            'password'       => (!empty($this->input->post('password')) ? md5($this->input->post('password')) : $this->input->post('oldpassword')),
            'about'         => $this->input->post('about', true),
            'image'         => (!empty($image) ? $image : $this->input->post('old_image'))
        );

        /*-----------------------------------*/
        if ($this->form_validation->run()) {

            if ($image === false) {
                $this->session->set_flashdata('exception', display('invalid_image'));
            }


            if ($this->home_model->setting($userData)) {

                $this->session->set_userdata(array(
                    'fullname'   => $this->input->post('firstname') . ' ' . $this->input->post('lastname'),
                    'email'        => $this->input->post('email'),
                    'image'        => (!empty($image) ? $image : $this->input->post('old_image'))
                ));


                $this->session->set_flashdata('message', display('update_successfully'));
            } else {
                $this->session->set_flashdata('exception',  display('please_try_again'));
            }
            redirect("dashboard/home/setting");
        } else {
            $data['module'] = "dashboard";
            $data['page']   = "home/profile_setting";
            if (!empty($id))
                $data['user']   = $this->home_model->profile($id);
            echo Modules::run('template/layout', $data);
        }
    }


    public function profile()
    {

        $edit_data = $this->home_model->profile_edit_data();
        $data = array(
            'title'      => display('update_profile'),
            'first_name' => $edit_data[0]['first_name'],
            'last_name'  => $edit_data[0]['last_name'],
            'user_name'  => $edit_data[0]['username'],
            'logo'       => $edit_data[0]['logo']
        );
        $data['title']  = "Profile";
        $data['module'] = "dashboard";
        $data['page']   = "home/edit_profile";
        echo Modules::run('template/layout', $data);
    }


    public function update_profile()
    {
        $this->form_validation->set_rules('first_name', display('first_name'), 'required|max_length[50]');
        $this->form_validation->set_rules('last_name', display('last_name'), 'required|max_length[50]');
        $this->form_validation->set_rules('user_name', display('email'), 'required|max_length[50]');
        if ($this->form_validation->run()) {
            $this->home_model->profile_update();
            $this->session->set_flashdata(array('message' => display('successfully_updated')));
        } else {
            $this->session->set_flashdata(array('exception' => validation_errors()));
        }
        redirect(base_url('edit_profile'));
    }

    #=============Change Password=========# 

    public function change_password_form()
    {
        $data['title']  = "Change Password";
        $data['module'] = "dashboard";
        $data['page']   = "home/change_password";
        echo Modules::run('template/layout', $data);
    }


    public function change_password()
    {
        $error        = '';
        $email        = $this->input->post('email', TRUE);
        $old_password = $this->input->post('old_password', TRUE);
        $new_password = $this->input->post('password', TRUE);
        $repassword   = $this->input->post('repassword', TRUE);

        if ($email == '' || $old_password == '' || $new_password == '') {
            $error = display('blank_field_does_not_accept');
        } else if ($email != $this->session->userdata('email')) {
            $error = display('you_put_wrong_email_address');
        } else if (strlen($new_password) < 6) {
            $error = display('new_password_at_least_six_character');
        } else if ($new_password != $repassword) {
            $error = display('password_and_repassword_does_not_match');
        } else if ($this->home_model->change_password($email, $old_password, $new_password) === FALSE) {
            $error = display('you_are_not_authorised_person');
        }

        if ($error != '') {
            $this->session->set_flashdata(array('exception' => $error));
            redirect(base_url('change_password'));
        } else {
            $this->session->set_flashdata(array('message' => display('successfully_changed_password')));
            redirect(base_url('change_password'));
        }
    }
}
