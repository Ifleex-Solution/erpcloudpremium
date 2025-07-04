<?php
defined('BASEPATH') or exit('No direct script access allowed');

#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Invoice extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $timezone = $this->db->select('timezone')->from('web_setting')->get()->row();
        date_default_timezone_set($timezone->timezone);
        $this->load->model(array(
            'invoice_model',
            'customer/customer_model',
            'account/Accounts_model',
            'product/product_model',
            'service/service_model'

        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }


    function bdtask_invoice_form($id = null)
    {
        $data['title']       = display('add_invoice');
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->product_model->active_store();
        $data['module']      = "invoice";
        $data['page']        = "add_invoice_form";
        $data['id'] = $id;
        if ($this->permission1->method('manage_invoice', 'create')->access()) {
            if ($id != null) {

                $data['title'] = "Edit Sales";
            }
            // echo modules::run('template/layout', $data);
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    function bdtask_new_pos($id = null)
    {
        $data['title']       = display('new_pos');
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->product_model->active_store();
        $data['module']      = "invoice";
        $data['page']        = "new_pos";
        $data['id'] = $id;
        if ($this->permission1->method('manage_invoice', 'create')->access()) {
            if ($id != null) {

                $data['title'] = "Edit Pos Sale";
            }
            // echo modules::run('template/layout', $data);
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    public function customer_list()
    {
        $encryption_key = Config::$encryption_key;

        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select(' customer_id, AES_DECRYPT(customer_name,"' . $encryption_key . '") AS customer_name')
            ->from('customer_information')
            ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function employee_list()
    {
        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select('*')
            ->from('employee_history')
            // ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function pmethod_dropdown()
    {
        $this->db->select('*')
            ->from('payment_type')
            // ->where('PHeadName', 'Cash')
            ->where('status', '1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function active_product()
    {
        $this->db->select('id,product_name');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }



    public function bdtask_invoice_list()
    {
        $data['title']        = display('manage_invoice');
        $data['total_invoice'] = $this->invoice_model->count_invoice();
        $data['module']       = "invoice";
        $data['page']         = "invoice";

        if ($this->permission1->method('manage_invoice', 'read')->access()) {

            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    public function CheckInvoiceList()
    {
        $postData = $this->input->post();
        $data     = $this->invoice_model->getInvoiceList($postData);
        echo json_encode($data);
    }

    public function delivery_note()
    {

        $data['invoice_no']    =  $this->input->post('invoice_no', TRUE);
        $data['delivery_note'] = $this->db->select('delivery_note')
            ->from('invoice')->where('invoice_id', $data['invoice_no'])->get()->row()->delivery_note;

        $this->load->view('invoice/delivery_note', $data);
    }

    public function save_delivery_note($invoice_id)
    {

        $delivery_note = $this->input->post('note', true);
        $data =  array('delivery_note' => $delivery_note);

        if ($this->db->where('invoice_id', $invoice_id)->update('invoice', $data)) {
            #set success message
            $this->session->set_flashdata('message', display('save_successfully'));
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }

        redirect("invoice_list");
    }



    public function bdtask_invoice_details($invoice_id = null)
    {
        $sale = $this->sale($invoice_id);
        $saledetails = $this->saledetails($invoice_id);
        $customer_info    = $this->customer_info($sale[0]['customer_id']);
        $company_info     = $this->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $saledetails,
            'total' => $sale[0]['total'],
            'total_dis' => $sale[0]['discount'] == "" ? "0.0" : $sale[0]['discount'],
            'total_discount_ammount' =>  $sale[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $sale[0]['total_vat_amnt'],
            'grandTotal' =>  $sale[0]['grandTotal'],
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info2'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $sale[0]['date'],
            'details'    => "",
            'invoiceno' => $sale[0]['sale_id'],
            'payment' => ""
        );

        $data['module']     = "invoice";
        $data['page']       = "invoice_html";
        echo modules::run('template/layout', $data);
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

    public function bdtask_delivery_invoice_details($invoice_id = null)
    {
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount  = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }
                if (!empty($invoice_detail[$k]['discount'])) {
                    $is_dis_val = $is_dis_val + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt_per'])) {
                    $vat_amnt_per = $vat_amnt_per + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt'])) {
                    $vat_amnt = $vat_amnt + 1;
                }
            }
        }


        $totalbal      = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword = $totalbal;
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
            'title'             => display('invoice_details'),
            'invoice_id'        => $invoice_detail[0]['invoice_id'],
            'invoice_no'        => $invoice_detail[0]['invoice'],
            'customer_name'     => $invoice_detail[0]['customer_name'],
            'customer_address'  => $invoice_detail[0]['customer_address'],
            'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
            'customer_email'    => $invoice_detail[0]['customer_email'],
            'final_date'        => $invoice_detail[0]['final_date'],
            'email_address'     => $invoice_detail[0]['email_address'],
            'contact'           => $invoice_detail[0]['contact'],
            'invoice_details'   => $invoice_detail[0]['invoice_details'],
            'total_amount'      => number_format($invoice_detail[0]['total_amount'], 2, '.', ','),
            'subTotal_quantity' => $subTotal_quantity,
            'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_discount_cal' => $invoice_detail[0]['total_discount'],
            'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
            'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
            'subTotal_amount_cal' => $subTotal_ammount,
            'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data'  => $invoice_detail,
            'am_inword'         => $amount_inword,
            'is_discount'       => $invoice_detail[0]['total_discount'] - $invoice_detail[0]['invoice_discount'],
            'users_name'        => $users->first_name . ' ' . $users->last_name,
            'tax_regno'         => $txregname,
            'is_desc'           => $descript,
            'is_dis_val'        => $is_dis_val,
            'vat_amnt_per'      => $vat_amnt_per,
            'vat_amnt'          => $vat_amnt,
            'is_discount'       => $is_discount,
            'is_serial'         => $isserial,
            'is_unit'           => $isunit,
        );
        $data['module']     = "invoice";
        $data['page']       = "delivery_invoice_html";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_invoice_pad_print($invoice_id)
    {
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);

        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            $total_discount_amount = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }
                if (!empty($invoice_detail[$k]['discount'])) {
                    $is_dis_val = $is_dis_val + 1;
                    $total_discount_amount = $invoice_detail[$k]['discount'] + $total_discount_amount;
                }
                if (!empty($invoice_detail[$k]['vat_amnt_per'])) {
                    $vat_amnt_per = $vat_amnt_per + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt'])) {
                    $vat_amnt = $vat_amnt + 1;
                }
            }
        }

        $totalbal      = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword = $this->numbertowords->convert_number($totalbal);
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
            'title'            => display('pad_print'),
            'invoice_id'       => $invoice_detail[0]['invoice_id'],
            'invoice_no'       => $invoice_detail[0]['invoice'],
            'customer_name'    => $invoice_detail[0]['customer_name'],
            'customer_address' => $invoice_detail[0]['customer_address'],
            'customer_mobile'  => $invoice_detail[0]['customer_mobile'],
            'customer_email'   => $invoice_detail[0]['customer_email'],
            'final_date'       => $invoice_detail[0]['final_date'],
            'print_setting'    => $this->invoice_model->bdtask_print_settingdata(),
            'invoice_details'  => $invoice_detail[0]['invoice_details'],
            'total_amount'     => number_format($totalbal, 2, '.', ','),
            'subTotal_cartoon' => $subTotal_cartoon,
            'subTotal_quantity' => $subTotal_quantity,
            'total_vat'        => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
            'invoice_discount' => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
            'total_discount'   => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_tax'        => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'      => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'       => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'shipping_cost'   => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data' => $invoice_detail,
            'previous'         => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'am_inword'        => $amount_inword,
            'is_discount'      => $invoice_detail[0]['total_discount'] - $invoice_detail[0]['invoice_discount'],
            'is_dis_val'       => $is_dis_val,
            'vat_amnt_per'     => $vat_amnt_per,
            'vat_amnt'         => $vat_amnt,
            'total_before'            => number_format($invoice_detail[0]['paid_amount'] + $invoice_detail[0]['total_discount'], 2, '.', ','),
            'is_discount'      => $is_discount,
            'users_name'       => $users->first_name . ' ' . $users->last_name,
            'tax_regno'        => $txregname,
            'is_desc'          => $descript,
            'is_serial'        => $isserial,
            'is_unit'          => $isunit,
            'product_discount' => number_format($total_discount_amount, 2),
        );

        $data['module']     = "invoice";
        $data['page']       = "pad_print";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_invoice_pos_print($invoice_id = null)
    {
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }
                if (!empty($invoice_detail[$k]['discount'])) {
                    $is_dis_val = $is_dis_val + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt_per'])) {
                    $vat_amnt_per = $vat_amnt_per + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt'])) {
                    $vat_amnt = $vat_amnt + 1;
                }
            }
        }

        $payment_method_list = $this->invoice_model->invoice_method_wise_balance($invoice_id);
        $terms_list = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result();
        $totalbal = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $user_id  = $invoice_detail[0]['sales_by'];
        $users    = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
            'title'                => display('pos_print'),
            'invoice_id'           => $invoice_detail[0]['invoice_id'],
            'invoice_no'           => $invoice_detail[0]['invoice'],
            'customer_name'        => $invoice_detail[0]['customer_name'],
            'customer_address'     => $invoice_detail[0]['customer_address'],
            'customer_mobile'      => $invoice_detail[0]['customer_mobile'],
            'customer_email'       => $invoice_detail[0]['customer_email'],
            'final_date'           => $invoice_detail[0]['final_date'],
            'invoice_details'      => $invoice_detail[0]['invoice_details'],
            'grand_total'          => $invoice_detail[0]['total_amount'],
            'total_amount'         => number_format($totalbal, 2, '.', ','),
            'subTotal_cartoon'     => $subTotal_cartoon,
            'subTotal_quantity'    => $subTotal_quantity,
            'invoice_discount'     => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
            'total_discount'       => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_vat'            => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
            'total_tax'            => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount'     => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'          => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'           => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'shipping_cost'        => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'total_before'            =>  number_format($purchase_detail[0]['paid_amount'] + $purchase_detail[0]['total_discount'], 2),
            'invoice_all_data'     => $invoice_detail,
            'previous'             => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'is_discount'          => $is_discount,
            'is_dis_val'           => $is_dis_val,
            'vat_amnt_per'         => $vat_amnt_per,
            'vat_amnt'             => $vat_amnt,
            'users_name'           => $users->first_name . ' ' . $users->last_name,
            'tax_regno'            => $txregname,
            'is_desc'              => $descript,
            'is_serial'            => $isserial,
            'is_unit'              => $isunit,
            'all_discount'         => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'p_method_list'        => $payment_method_list,
            'terms_list'           => $terms_list,

        );

        $data['module']     = "invoice";
        $data['page']       = "pos_print";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_pos_print_direct()
    {
        $invoice_id = $this->input->post('invoice_id', true);
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }
            }
        }


        $totalbal = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $user_id  = $invoice_detail[0]['sales_by'];
        $users    = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
            'title'                => display('pos_print'),
            'invoice_id'           => $invoice_detail[0]['invoice_id'],
            'invoice_no'           => $invoice_detail[0]['invoice'],
            'customer_name'        => $invoice_detail[0]['customer_name'],
            'customer_address'     => $invoice_detail[0]['customer_address'],
            'customer_mobile'      => $invoice_detail[0]['customer_mobile'],
            'customer_email'       => $invoice_detail[0]['customer_email'],
            'final_date'           => $invoice_detail[0]['final_date'],
            'invoice_details'      => $invoice_detail[0]['invoice_details'],
            'total_amount'         => number_format($totalbal, 2, '.', ','),
            'subTotal_cartoon'     => $subTotal_cartoon,
            'subTotal_quantity'    => $subTotal_quantity,
            'invoice_discount'     => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
            'total_discount'       => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_tax'            => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount'     => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'          => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'           => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'shipping_cost'        => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data'     => $invoice_detail,
            'previous'             => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'is_discount'         => $is_discount,
            'users_name'           => $users->first_name . ' ' . $users->last_name,
            'tax_regno'            => $txregname,
            'is_desc'              => $descript,
            'is_serial'            => $isserial,
            'is_unit'              => $isunit,
            'url'                  => $this->input->post('url', TRUE),

        );

        $data['module']     = "invoice";
        $data['page']       = "pos_invoice_html_direct";
        echo modules::run('template/layout', $data);
    }




    public function bdtask_download_invoice($invoice_id = null)
    {
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $isunit            = 0;
        $is_discount       = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }
            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
            }
        }

        $currency_details = $this->invoice_model->retrieve_setting_editdata();
        $company_info     = $this->invoice_model->retrieve_company();
        $totalbal         = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword    = $this->numbertowords->convert_number($totalbal);
        $user_id          = $invoice_detail[0]['sales_by'];
        $users            = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
            'title'             => display('invoice_details'),
            'invoice_id'        => $invoice_detail[0]['invoice_id'],
            'customer_info'     => $invoice_detail,
            'invoice_no'        => $invoice_detail[0]['invoice'],
            'customer_name'     => $invoice_detail[0]['customer_name'],
            'customer_address'  => $invoice_detail[0]['customer_address'],
            'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
            'customer_email'    => $invoice_detail[0]['customer_email'],
            'final_date'        => $invoice_detail[0]['final_date'],
            'invoice_details'   => $invoice_detail[0]['invoice_details'],
            'total_amount'      => number_format($invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'], 2, '.', ','),
            'subTotal_quantity' => $subTotal_quantity,
            'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
            'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data'  => $invoice_detail,
            'company_info'      => $company_info,
            'currency'          => $currency_details[0]['currency'],
            'position'          => $currency_details[0]['currency_position'],
            'discount_type'     => $currency_details[0]['discount_type'],
            'currency_details'  => $currency_details,
            'am_inword'         => $amount_inword,
            'is_discount'       => $is_discount,
            'users_name'        => $users->first_name . ' ' . $users->last_name,
            'tax_regno'         => $txregname,
            'is_desc'           => $descript,
            'is_serial'         => $isserial,
            'is_unit'           => $isunit,
        );



        $this->load->library('pdfgenerator');
        $dompdf = new DOMPDF();
        $page = $this->load->view('invoice/invoice_download', $data, true);
        $file_name = time();
        $dompdf->load_html($page, 'UTF-8');
        $dompdf->render();
        $output = $dompdf->output();
        @exec("sudo chmod " . "$file_name 777");
        file_put_contents("assets/data/pdf/invoice/$file_name.pdf", $output);
        $filename = $file_name . '.pdf';
        $file_path = base_url() . 'assets/data/pdf/invoice/' . $filename;

        $this->load->helper('download');
        force_download('./assets/data/pdf/invoice/' . $filename, NULL);
        redirect("invoice_list");
    }

    public function bdtask_manual_sales_insert()
    {
        $this->form_validation->set_rules('customer_id', display('customer_name'), 'required|max_length[15]');
        $this->form_validation->set_rules('product_id[]', display('product'), 'required|max_length[20]');
        $this->form_validation->set_rules('multipaytype[]', display('payment_type'), 'required');
        $this->form_validation->set_rules('product_quantity[]', display('quantity'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_rate[]', display('rate'), 'required|max_length[20]');
        $normal = $this->input->post('is_normal');

        $finyear = $this->input->post('finyear', true);
        if ($finyear <= 0) {
            $data['status'] = false;
            $data['exception'] = 'Please Create Financial Year First From Accounts > Financial Year.';
        } else {
            if ($this->form_validation->run() === true) {
                $incremented_id = $this->number_generator();
                $invoice_id     = $this->invoice_model->invoice_entry($incremented_id);
                if (!empty($invoice_id)) {
                    $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                    if ($setting_data[0]['is_autoapprove_v'] == 1) {

                        $new = $this->autoapprove($invoice_id);
                    }

                    $data['status']     = true;
                    $data['invoice_id'] = $invoice_id;
                    $data['message']    = display('save_successfully');
                    $mailsetting        = $this->db->select('*')->from('email_config')->get()->result_array();

                    if ($mailsetting[0]['isinvoice'] == 1) {
                        $mail  = $this->invoice_pdf_generate($invoice_id);
                        if ($mail == 0) {
                            $data['exception'] = $this->session->set_userdata(array('error_message' => display('please_config_your_mail_setting')));
                        }
                    }
                    if ($normal == 1) {
                        $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($invoice_id);
                        $data['details'] = $this->load->view('invoice/invoice_html_manual', $printdata, true);
                    } else {
                        $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($invoice_id);
                        $data['details'] = $this->load->view('invoice/pos_print', $printdata, true);
                    }
                    $base_url = base_url();

                    echo '<script type="text/javascript">
                    alert("Invoice details saved successfully");
                    window.location.href = "' . $base_url . 'invoice_list";
                   </script>';
                } else {
                    $data['status']    = false;
                    $data['exception'] = 'Please Try Again';
                }
            } else {
                $data['status']    = false;
                $data['exception'] = validation_errors();
            }
        }
    }

    public function bdtask_manual_possales_insert()
    {
        $this->form_validation->set_rules('customer_id', display('customer_name'), 'required|max_length[15]');
        $this->form_validation->set_rules('product_id[]', display('product'), 'required|max_length[20]');
        $this->form_validation->set_rules('multipaytype[]', display('payment_type'), 'required');
        $this->form_validation->set_rules('product_quantity[]', display('quantity'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_rate[]', display('rate'), 'required|max_length[20]');
        $normal = $this->input->post('is_normal');

        $finyear = $this->input->post('finyear', true);
        if ($finyear <= 0) {
            $data['status'] = false;
            $data['exception'] = 'Please Create Financial Year First From Accounts > Financial Year.';
        } else {
            if ($this->form_validation->run() === true) {


                $incremented_id = $this->number_generator();
                $invoice_id     = $this->invoice_model->invoice_posentry($incremented_id);
                if (!empty($invoice_id)) {
                    $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                    if ($setting_data[0]['is_autoapprove_v'] == 1) {

                        $new = $this->autoapprove($invoice_id);
                    }

                    $data['status']     = true;
                    $data['invoice_id'] = $invoice_id;
                    $data['message']    = display('save_successfully');
                    $mailsetting        = $this->db->select('*')->from('email_config')->get()->result_array();

                    if ($mailsetting[0]['isinvoice'] == 1) {
                        $mail  = $this->invoice_pdf_generate($invoice_id);
                        if ($mail == 0) {
                            $data['exception'] = $this->session->set_userdata(array('error_message' => display('please_config_your_mail_setting')));
                        }
                    }
                    if ($normal == 1) {
                        $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($invoice_id);
                        $data['details'] = $this->load->view('invoice/invoice_html_manual', $printdata, true);
                    } else {
                        $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($invoice_id);
                        $data['details'] = $this->load->view('invoice/pos_print', $printdata, true);
                    }
                    $base_url = base_url();

                    echo json_encode($data);
                } else {
                    $data['status']    = false;
                    $data['exception'] = 'Please Try Again';
                }
            } else {
                $data['status']    = false;
                $data['exception'] = validation_errors();
            }
        }
    }


    public function autoapprove($invoice_id)
    {

        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $invoice_id)->where('status', 0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
    }

    public function bdtask_showpaymentmodal($id = null)
    {
        $is_credit =  $this->input->post('is_credit_edit', TRUE);
        $data['is_credit'] = $is_credit;
        $data['id'] = $id;
        if ($is_credit == 1) {
            # code...
            $data['all_pmethod'] = $this->invoice_model->pmethod_dropdown();
        } else {

            $data['all_pmethod'] = $this->invoice_model->pmethod_dropdown_new();
        }

        //$data['banks'] = $this->getAllBanks();

        $this->load->view('invoice/newpaymentveiw', $data);
    }

    public function getAllCustomers()
    {
        $this->db->select('customer_id,customer_name');
        $this->db->from('customer_information');
        $this->db->order_by('customer_name', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        echo json_encode($result);
    }

    public function checkCheque($chequeno = null)
    {
        $this->db->select('*');
        $this->db->from('cheque');
        $this->db->where('cheque_no', $chequeno);
        $query = $this->db->get();
        $result = $query->result_array();
        echo  json_encode($result);
    }


    public function bdtask_showpaymentmodal1($id = null)
    {

        $is_credit =  $this->input->post('is_credit_edit', TRUE);
        $data['is_credit'] = $is_credit;
        $data['id'] = $id;
        if ($is_credit == 1) {
            # code...
            $data['all_pmethod'] = $this->purchase_model->pmethod_dropdown();
        } else {

            $data['all_pmethod'] = $this->purchase_model->pmethod_dropdown_new();
        }
        $this->load->view('purchase/newpaymentveiw', $data);
    }


    public function bdtask_edit_invoice($invoice_id = null)
    {

        $invoice_detail = $this->invoice_model->retrieve_invoice_editdata($invoice_id);
        $vat_tax_info   = $this->invoice_model->vat_tax_setting();
        if ($invoice_detail[0]['is_dynamic'] == 1) {
            if ($invoice_detail[0]['is_dynamic'] != $vat_tax_info->dynamic_tax) {

                $this->session->set_flashdata('exception', 'VAT and TAX are set globally, which is not the same as VAT and TAX on this invoice. (which was configured when the invoice was created). It is not editable.');
                redirect("invoice_list");
            }
        } elseif ($invoice_detail[0]['is_fixed'] == 1) {
            if ($invoice_detail[0]['is_fixed'] != $vat_tax_info->fixed_tax) {

                $this->session->set_flashdata('exception', 'VAT and TAX are set globally, which is not the same as VAT and TAX on this invoice. (which was configured when the invoice was created). It is not editable.');
                redirect("invoice_list");
            }
        }

        $taxinfo        = $this->invoice_model->invoice_taxinfo($invoice_id);
        $taxfield       = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $i = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                $stock = $this->invoice_model->stock_qty_check($invoice_detail[$k]['product_id']);
                $invoice_detail[$k]['stock_qty'] = $stock + $invoice_detail[$k]['quantity'];
            }
        }

        $currency_details = $this->invoice_model->retrieve_setting_editdata();

        $multi_pay_data = $this->db->select('COAID, Debit')
            ->from('acc_vaucher')
            ->where('referenceNo', $invoice_detail[0]['invoice'])
            ->where('Vtype', 'CV')
            ->get()->result();

        $data = array(
            'title'           => display('invoice_edit'),
            'dbinv_id'        => $invoice_detail[0]['dbinv_id'],
            'invoice_id'      => $invoice_detail[0]['invoice_id'],
            'customer_id'     => $invoice_detail[0]['customer_id'],
            'customer_name'   => $invoice_detail[0]['customer_name'],
            'date'            => $invoice_detail[0]['date'],
            'invoice_details' => $invoice_detail[0]['invoice_details'],
            'invoice'         => $invoice_detail[0]['invoice'],
            'total_amount'    => $invoice_detail[0]['total_amount'],
            'paid_amount'     => $invoice_detail[0]['paid_amount'],
            'due_amount'      => $invoice_detail[0]['due_amount'],
            'invoice_discount' => $invoice_detail[0]['invoice_discount'],
            'total_discount'  => $invoice_detail[0]['total_discount'],
            'total_vat_amnt'  => $invoice_detail[0]['total_vat_amnt'],
            'unit'            => $invoice_detail[0]['unit'],
            'tax'             => $invoice_detail[0]['tax'],
            'taxes'           => $taxfield,
            'prev_due'        => $invoice_detail[0]['prevous_due'],
            'net_total'       => $invoice_detail[0]['prevous_due'] + $invoice_detail[0]['total_amount'],
            'shipping_cost'   => $invoice_detail[0]['shipping_cost'],
            'total_tax'       => $invoice_detail[0]['taxs'],
            'invoice_all_data' => $invoice_detail,
            'taxvalu'         => $taxinfo,
            'discount_type'   => $currency_details[0]['discount_type'],
            'bank_id'         => $invoice_detail[0]['bank_id'],
            'multi_paytype'   => $multi_pay_data,
            'is_credit'       => $invoice_detail[0]['is_credit'],
        );
        $data['all_pmethod'] = $this->invoice_model->pmethod_dropdown_new();
        $data['all_pmethodwith_cr'] = $this->invoice_model->pmethod_dropdown();
        $data['module']     = "invoice";
        $vatortax              = $this->invoice_model->vat_tax_setting();
        if ($vatortax->fixed_tax == 1) {

            $data['page']       = "edit_invoice_form";
        }
        if ($vatortax->dynamic_tax == 1) {
            $data['page']          = "edit_invoice_form_dynamic";
        }
        echo modules::run('template/layout', $data);
    }

    public function bdtask_update_invoice()
    {
        $this->form_validation->set_rules('customer_id', display('customer_name'), 'required|max_length[15]');
        $this->form_validation->set_rules('invoice_no', display('invoice_no'), 'required|max_length[20]');
        $this->form_validation->set_rules('multipaytype[]', display('payment_type'), 'required');
        $this->form_validation->set_rules('product_id[]', display('product'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_quantity[]', display('quantity'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_rate[]', display('rate'), 'required|max_length[20]');

        $multipaytype = $this->input->post('multipaytype', TRUE);
        $finyear = $this->input->post('finyear', true);
        if ($finyear <= 0) {
            $data['status'] = false;
            $data['exception'] = 'Please Create Financial Year First From Accounts > Financial Year.';
        } else {

            if ($this->form_validation->run() === true) {
                $invoice_id = $this->invoice_model->update_invoice();
                if (!empty($invoice_id)) {
                    $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                    if ($setting_data[0]['is_autoapprove_v'] == 1) {

                        $new = $this->autoapprove($invoice_id);
                    }
                    $data['status'] = true;
                    $data['invoice_id'] = $invoice_id;
                    $data['message'] = display('update_successfully');
                    $mailsetting = $this->db->select('*')->from('email_config')->get()->result_array();
                    if ($mailsetting[0]['isinvoice'] == 1) {
                        $mail = $this->invoice_pdf_generate($invoice_id);
                        if ($mail == 0) {
                            $data['exception'] = $this->session->set_userdata(array('error_message' => display('please_config_your_mail_setting')));
                        }
                    }
                    $data['details'] = $this->load->view('invoice/invoice_html', $data, true);
                } else {
                    $data['status'] = false;
                    $data['exception'] = 'Please Try Again';
                }
            } else {
                $data['status'] = false;
                $data['exception'] = validation_errors();
            }
        }
        echo json_encode($data);
    }

    public function invoice_pdf_generate($invoice_id = null)
    {
        $id = $invoice_id;
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon = 0;
        $subTotal_discount = 0;
        $subTotal_ammount = 0;
        $descript = 0;
        $isserial = 0;
        $isunit = 0;
        $is_discount = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }

                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
            }
        }

        $currency_details = $this->invoice_model->retrieve_setting_editdata();
        $company_info = $this->invoice_model->retrieve_company();
        $totalbal = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword = $this->numbertowords->convert_number($totalbal);
        $user_id = $invoice_detail[0]['sales_by'];

        $name    = $invoice_detail[0]['customer_name'];
        $email   = $invoice_detail[0]['customer_email'];
        $data = array(
            'title'             => display('invoice_details'),
            'invoice_id'        => $invoice_detail[0]['invoice_id'],
            'customer_info'     => $invoice_detail,
            'invoice_no'        => $invoice_detail[0]['invoice'],
            'customer_name'     => $invoice_detail[0]['customer_name'],
            'customer_address'  => $invoice_detail[0]['customer_address'],
            'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
            'customer_email'    => $invoice_detail[0]['customer_email'],
            'final_date'        => $invoice_detail[0]['final_date'],
            'invoice_details'   => $invoice_detail[0]['invoice_details'],
            'total_amount'      => number_format($invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'], 2, '.', ','),
            'subTotal_quantity' => $subTotal_quantity,
            'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
            'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data'  => $invoice_detail,
            'company_info'      => $company_info,
            'currency'          => $currency_details[0]['currency'],
            'position'          => $currency_details[0]['currency_position'],
            'discount_type'     => $currency_details[0]['discount_type'],
            'currency_details'  => $currency_details,
            'am_inword'         => $amount_inword,
            'is_discount'       => $is_discount,

            'tax_regno'         => $txregname,
            'is_desc'           => $descript,
            'is_serial'         => $isserial,
            'is_unit'           => $isunit,
        );

        $this->load->library('pdfgenerator');
        $html = $this->load->view('invoice/invoice_download', $data, true);
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents('assets/data/pdf/invoice/' . $id . '.pdf', $output);
        $file_path = getcwd() . '/assets/data/pdf/invoice/' . $id . '.pdf';
        $send_email = '';
        if (!empty($email)) {
            $send_email = $this->setmail($email, $file_path, $id, $name);

            if ($send_email) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }



    public function setmail($email, $file_path, $id = null, $name = null)
    {
        $setting_detail = $this->db->select('*')->from('email_config')->get()->row();
        $subject = 'Product Purchase Information';
        $message = strtoupper($name) . '-' . $id;

        $config = array(
            'protocol'  => $setting_detail->protocol,
            'smtp_host' => $setting_detail->smtp_host,
            'smtp_port' => $setting_detail->smtp_port,
            'smtp_user' => $setting_detail->smtp_user,
            'smtp_pass' => $setting_detail->smtp_pass,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        );

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->from($setting_detail->smtp_user);
        $this->email->to($email);

        $config = array(
            'protocol'  => $setting_detail->protocol,
            'smtp_host' => $setting_detail->smtp_host,
            'smtp_port' => $setting_detail->smtp_port,
            'smtp_user' => $setting_detail->smtp_user,
            'smtp_pass' => $setting_detail->smtp_pass,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        );

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->from($setting_detail->smtp_user);
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->attach($file_path);
        $check_email = $this->test_input($email);
        if (filter_var($check_email, FILTER_VALIDATE_EMAIL)) {
            if ($this->email->send()) {
                return true;
            } else {
                $this->session->set_flashdata(array('exception' => display('please_configure_your_mail.')));
                return false;
            }
        } else {

            return false;
        }
    }


    //Email testing for email
    public function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    function bdtask_pos_invoice()
    {
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $tablecolumn   = $this->db->list_fields('tax_collection');
        $num_column    = count($tablecolumn) - 4;
        $walking_customer      = $this->invoice_model->pos_customer_setup();
        $data['customer_name'] = $walking_customer[0]['customer_name'];
        $data['customer_id']   = $walking_customer[0]['customer_id'];
        $data['invoice_no']    = $this->number_generator();
        $data['title']         = display('pos_invoice');
        $data['taxes']         = $this->invoice_model->tax_fileds();
        $data['taxnumber']     = $num_column;
        $data['module']        = "invoice";
        $data['page']          = "add_pos_invoice_form";
        echo modules::run('template/layout', $data);
    }



    public function bdtask_gui_pos()
    {
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $tablecolumn       = $this->db->list_fields('tax_collection');
        $num_column        = count($tablecolumn) - 4;
        $data['title']         = display('gui_pos');
        $saveid                = $this->session->userdata('id');
        $walking_customer      = $this->invoice_model->walking_customer();
        $data['customer_id']   = $walking_customer[0]['customer_id'];
        $data['customer_name'] = $walking_customer[0]['customer_name'];
        $data['categorylist']  = $this->invoice_model->category_list();
        $customer_details      = $this->invoice_model->pos_customer_setup();
        $data['customerlist']  = $this->invoice_model->customer_dropdown();
        $data['customer_name'] = $customer_details[0]['customer_name'];
        $data['customer_id']   = $customer_details[0]['customer_id'];
        $data['itemlist']      = $this->invoice_model->allproduct();
        $data['product_list']  = $this->invoice_model->product_list();
        $data['taxes']         = $taxfield;
        $data['taxnumber']     = $num_column;
        $data['invoice_no']    = $this->number_generator();
        $data['todays_invoice'] = $this->invoice_model->todays_invoice();
        $data['all_pmethod']   = $this->invoice_model->pmethod_dropdown();
        $data['module']        = "invoice";
        $vatortax              = $this->invoice_model->vat_tax_setting();
        if ($vatortax->fixed_tax == 1) {
            $data['page']      = "gui_pos_invoice";
            $data['tax_type']  = "fixed";
        }
        if ($vatortax->dynamic_tax == 1) {
            $data['page']      = "gui_pos_invoice_dynamic";
            $data['tax_type']  = "dynamic";
        }
        echo modules::run('template/layout', $data);
    }


    public function getitemlist()
    {
        $catid       = $this->input->post('category_id', TRUE);
        $category_id = (!empty($catid) ? $catid : '');
        $getproduct  = $this->invoice_model->searchprod($category_id);
        if (!empty($getproduct)) {
            $data['itemlist'] = $getproduct;
            $this->load->view('invoice/getproductlist', $data);
        } else {
            $title['title'] = 'Product Not found';
            $this->load->view('invoice/productnot_found', $title);
        }
    }


    public function getitemlist_byname()
    {
        $product_name     = $this->input->post('product_name', TRUE);
        $getproduct       = $this->invoice_model->searchprod_byname($product_name);
        if (!empty($getproduct)) {
            $data['itemlist'] = $getproduct;
            $this->load->view('invoice/getproductlist', $data);
        } else {
            $title['title']   = 'Product Not found';
            $this->load->view('invoice/productnot_found', $title);
        }
    }



    public function getitemlist_byproductname()
    {
        $prod       = $this->input->post('product_name', TRUE);
        $catid      = $this->input->post('category_id', TRUE);
        $getproduct = $this->invoice_model->searchprod_byname($catid, $prod);
        if (!empty($getproduct)) {
            $data['itemlist'] = $getproduct;
            $this->load->view('invoice/getproductlist', $data);
        } else {
            $title['title'] = 'Product Not found';
            $this->load->view('invoice/productnot_found', $title);
        }
    }

    public function gui_pos_invoice()
    {
        $product_id = $this->input->post('product_id', TRUE);
        $pro_id = $this->input->post('product_id', TRUE);

        $product_details = $this->invoice_model->pos_invoice_setup($product_id);
        $taxfield       = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $prinfo = $this->db->select('*')->from('product_information')->where('product_id', $product_id)->get()->result_array();

        $tr = " ";
        if (!empty($product_details)) {
            $product_id = $this->generator(5);
            $serialdata = explode(',', $product_details->serial_no);
            if ($product_details->total_product > 0) {
                $qty = 1;
            } else {
                $qty = 0;
            }

            $this->db->select('SUM(quantity) as purchase_qty,batch_id,product_id');
            $this->db->from('product_purchase_details');
            $this->db->where('product_id', $product_details->product_id);
            $this->db->group_by('batch_id');
            $pur_product_batch = $this->db->get()->result();


            $html = "";
            if (empty($pur_product_batch)) {
                $html .= "No Serial Found !";
            } else {
                // Select option created for product
                $html .= "<select name=\"serial_no[]\"   class=\"serial_no_1 form-control\" required onchange=\"invoice_product_batch('" . $product_details->product_id . "')\" id=\"serial_no_" . $product_details->product_id . "\">";
                $html .= "<option value=''>" . display('select_one') . "</option>";
                foreach ($pur_product_batch as $p_batch) {


                    $sellt_prod_batch = $this->db->select('SUM(quantity) as sale_qty,batch_id, product_id')->from('invoice_details')->where('product_id', $p_batch->product_id)->where('batch_id', $p_batch->batch_id)->get()->row();
                    $pur_prod = (empty($sellt_prod_batch->sale_qty) ? 0 : $sellt_prod_batch->sale_qty);
                    $available_prod = $p_batch->purchase_qty - $pur_prod;
                    if ($available_prod > 0) {
                        $html .= "<option value=" . $p_batch->batch_id . ">" . $p_batch->batch_id . "</option>";
                    }
                }
                $html .= "</select>";
            }

            $tr .= "<tr id=\"row_" . $product_details->product_id . "\">
                        <td class=\"\" style=\"width:220px\">
                            
                            <input type=\"text\" name=\"product_name\" onkeypress=\"invoice_productList('" . $product_details->product_id . "');\" class=\"form-control productSelection \" value='" . $product_details->product_name . "- (" . $product_details->product_model . ")" . "' placeholder='" . display('product_name') . "' required=\"\"  tabindex=\"\" readonly>

                            <input type=\"hidden\" class=\"form-control autocomplete_hidden_value product_id_" . $product_details->product_id . "\" name=\"product_id[]\" id=\"SchoolHiddenId_" . $product_details->product_id . "\" value = \"$product_details->product_id\"/>
                        </td>
                        <td>" . $html . "</td>
                        <td>
                            <input type=\"text\" name=\"available_quantity[]\" class=\"form-control text-right available_quantity_" . $product_details->product_id . "\" value='' readonly=\"\" id=\"available_quantity_" . $product_details->product_id . "\"/>
                        </td>
                        <td>
                            <input type=\"text\" name=\"product_quantity[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" class=\"total_qntt_" . $product_details->product_id . " form-control text-right\" id=\"total_qntt_" . $product_details->product_id . "\" placeholder=\"0.00\" min=\"0\" value='" . $qty . "' required=\"required\"/>
                        </td>
                        <td style=\"width:85px\">
                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" value='" . $product_details->price . "' id=\"price_item_" . $product_details->product_id . "\" class=\"price_item1 form-control text-right\" required placeholder=\"0.00\" min=\"0\"/>
                        </td>

                        <td class=\"\">
                            <input type=\"text\" name=\"discount[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"discount_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                          
                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"discountvalue[]\"  id=\"discount_value_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\" readonly/>
                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"vatpercent[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"vat_percent_" . $product_details->product_id . "\" value='" . $product_details->product_vat . "' class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"vatvalue[]\"  id=\"vat_value_" . $product_details->product_id . "\" class=\"form-control text-right total_vatamnt\" placeholder=\"0.00\" min=\"0\" readonly/>
                        </td>
                        <td class=\"text-right\" style=\"width:100px\">
                            <input class=\"total_price form-control text-right\" type=\"text\" name=\"total_price[]\" id=\"total_price_" . $product_details->product_id . "\" value='" . $product_details->price . "' tabindex=\"-1\" readonly=\"readonly\"/>
                        </td>

                        <td>";

            $sl = 0;

            $tr .= "<input type=\"hidden\" id=\"total_discount_" . $product_details->product_id . "\" />
                            <input type=\"hidden\" id=\"all_discount_" . $product_details->product_id . "\" class=\"total_discount dppr\"/>
                            <a style=\"text-align: right;\" class=\"btn btn-danger btn-xs\" href=\"#\"  onclick=\"deleteRow(this,'" . $product_details->product_id . "')\">" . '<i class="fa fa-close"></i>' . "</a>
                             <a style=\"text-align: right;\" class=\"btn btn-success btn-xs\" href=\"#\"  onclick=\"detailsmodal('" . $product_details->product_name . "','" . $product_details->total_product . "','" . $product_details->product_model . "','" . $product_details->unit . "','" . $product_details->price . "','" . $product_details->image . "')\">" . '<i class="fa fa-eye"></i>' . "</a>
                        </td>
                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }
    public function gui_pos_invoice_dynamic()
    {
        $product_id = $this->input->post('product_id', TRUE);
        $pro_id = $this->input->post('product_id', TRUE);

        $product_details = $this->invoice_model->pos_invoice_setup($product_id);
        $taxfield       = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $prinfo = $this->db->select('*')->from('product_information')->where('product_id', $product_id)->get()->result_array();

        $tr = " ";
        if (!empty($product_details)) {
            $product_id = $this->generator(5);
            $serialdata = explode(',', $product_details->serial_no);
            if ($product_details->total_product > 0) {
                $qty = 1;
            } else {
                $qty = 0;
            }

            $this->db->select('SUM(quantity) as purchase_qty,batch_id,product_id');
            $this->db->from('product_purchase_details');
            $this->db->where('product_id', $product_details->product_id);
            $this->db->group_by('batch_id');
            $pur_product_batch = $this->db->get()->result();


            $html = "";
            if (empty($pur_product_batch)) {
                $html .= "No Serial Found !";
            } else {
                // Select option created for product
                $html .= "<select name=\"serial_no[]\"   class=\"serial_no_1 form-control\" required onchange=\"invoice_product_batch('" . $product_details->product_id . "')\" id=\"serial_no_" . $product_details->product_id . "\">";
                $html .= "<option value=''>" . display('select_one') . "</option>";
                foreach ($pur_product_batch as $p_batch) {


                    $sellt_prod_batch = $this->db->select('SUM(quantity) as sale_qty,batch_id, product_id')->from('invoice_details')->where('product_id', $p_batch->product_id)->where('batch_id', $p_batch->batch_id)->get()->row();
                    $pur_prod = (empty($sellt_prod_batch->sale_qty) ? 0 : $sellt_prod_batch->sale_qty);
                    $available_prod = $p_batch->purchase_qty - $pur_prod;
                    if ($available_prod > 0) {
                        # code...
                        $html .= "<option value=" . $p_batch->batch_id . ">" . $p_batch->batch_id . "</option>";
                    }
                }
                $html .= "</select>";
            }

            $tr .= "<tr id=\"row_" . $product_details->product_id . "\">
                        <td class=\"\" style=\"width:220px\">
                            
                            <input type=\"text\" name=\"product_name\" onkeypress=\"invoice_productList('" . $product_details->product_id . "');\" class=\"form-control productSelection \" value='" . $product_details->product_name . "- (" . $product_details->product_model . ")" . "' placeholder='" . display('product_name') . "' required=\"\"  tabindex=\"\" readonly>

                            <input type=\"hidden\" class=\"form-control autocomplete_hidden_value product_id_" . $product_details->product_id . "\" name=\"product_id[]\" id=\"SchoolHiddenId_" . $product_details->product_id . "\" value = \"$product_details->product_id\"/>
                        </td>
                        <td>" . $html . "</td>
                        <td>
                            <input type=\"text\" name=\"available_quantity[]\" class=\"form-control text-right available_quantity_" . $product_details->product_id . "\" value='' readonly=\"\" id=\"available_quantity_" . $product_details->product_id . "\"/>
                        </td>
                        <td>
                            <input type=\"text\" name=\"product_quantity[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" class=\"total_qntt_" . $product_details->product_id . " form-control text-right\" id=\"total_qntt_" . $product_details->product_id . "\" placeholder=\"0.00\" min=\"0\" value='" . $qty . "' required=\"required\"/>
                        </td>
                        <td style=\"width:85px\">
                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" value='" . $product_details->price . "' id=\"price_item_" . $product_details->product_id . "\" class=\"price_item1 form-control text-right\" required placeholder=\"0.00\" min=\"0\"/>
                        </td>

                        <td class=\"\">
                            <input type=\"text\" name=\"discount[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"discount_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                          
                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"discountvalue[]\"  id=\"discount_value_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\" readonly/>
                        </td>
                        
                        <td class=\"text-right\" style=\"width:100px\">
                            <input class=\"total_price form-control text-right\" type=\"text\" name=\"total_price[]\" id=\"total_price_" . $product_details->product_id . "\" value='" . $product_details->price . "' tabindex=\"-1\" readonly=\"readonly\"/>
                        </td>

                        <td>";

            $sl = 0;
            foreach ($taxfield as $taxes) {
                $txs = 'tax' . $sl;
                $tr .= "<input type=\"hidden\" id=\"total_tax" . $sl . "_" . $product_details->product_id . "\" class=\"total_tax" . $sl . "_" . $product_details->product_id . "\" value='" . $prinfo[0][$txs] . "'/>
                            <input type=\"hidden\" id=\"all_tax" . $sl . "_" . $product_details->product_id . "\" class=\" total_tax" . $sl . "\" value='" . $prinfo[0][$txs] * $product_details->price . "' name=\"tax[]\"/>";
                $sl++;
            }

            $tr .= "<input type=\"hidden\" id=\"total_discount_" . $product_details->product_id . "\" />
                            <input type=\"hidden\" id=\"all_discount_" . $product_details->product_id . "\" class=\"total_discount dppr\"/>
                            <a style=\"text-align: right;\" class=\"btn btn-danger btn-xs\" href=\"#\"  onclick=\"deleteRow(this,'" . $product_details->product_id . "')\">" . '<i class="fa fa-close"></i>' . "</a>
                             <a style=\"text-align: right;\" class=\"btn btn-success btn-xs\" href=\"#\"  onclick=\"detailsmodal('" . $product_details->product_name . "','" . $product_details->total_product . "','" . $product_details->product_model . "','" . $product_details->unit . "','" . $product_details->price . "','" . $product_details->image . "')\">" . '<i class="fa fa-eye"></i>' . "</a>
                        </td>
                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }


    //Insert pos invoice
    public function insert_pos_invoice()
    {
        $product_id      = $this->input->post('product_id', TRUE);
        $product_details = $this->invoice_model->pos_invoice_setup($product_id);
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $prinfo = $this->db->select('*')->from('product_information')->where('product_id', $product_id)->get()->result_array();
        $tr = " ";
        if (!empty($product_details)) {
            $product_id = $this->generator(5);
            $serialdata = explode(',', $product_details->serial_no);
            if ($product_details->total_product > 0) {
                $qty = 1;
            } else {
                $qty = 1;
            }

            $html = "";
            if (empty($serialdata)) {
                $html .= "No Serial Found !";
            } else {
                // Select option created for product
                $html .= "<select name=\"serial_no[]\"   class=\"serial_no_1 form-control\" id=\"serial_no_" . $product_details->product_id . "\">";
                $html .= "<option value=''>" . display('select_one') . "</option>";
                foreach ($serialdata as $serial) {
                    $html .= "<option value=" . $serial . ">" . $serial . "</option>";
                }
                $html .= "</select>";
            }

            $tr .= "<tr id=\"row_" . $product_details->product_id . "\">
                        <td class=\"\" style=\"width:220px\">
                            
                            <input type=\"text\" name=\"product_name\" onkeypress=\"invoice_productList('" . $product_details->product_id . "');\" class=\"form-control productSelection \" value='" . $product_details->product_name . "- (" . $product_details->product_model . ")" . "' placeholder='" . display('product_name') . "' required=\"\" id=\"product_name_" . $product_details->product_id . "\" tabindex=\"\" readonly>

                            <input type=\"hidden\" class=\"form-control autocomplete_hidden_value product_id_" . $product_details->product_id . "\" name=\"product_id[]\" id=\"SchoolHiddenId_" . $product_details->product_id . "\" value = \"$product_details->product_id\"/>
                            
                        </td>
                         <td>
                             <input type=\"text\" name=\"desc[]\" class=\"form-control text-right \"  />
                                        </td>
                                        <td>" . $html . "</td>
                        <td>
                            <input type=\"text\" name=\"available_quantity[]\" class=\"form-control text-right available_quantity_" . $product_details->product_id . "\" value='" . $product_details->total_product . "' readonly=\"\" id=\"available_quantity_" . $product_details->product_id . "\"/>
                        </td>

                        <td>
                            <input class=\"form-control text-right unit_'" . $product_details->product_id . "' valid\" value=\"$product_details->unit\" readonly=\"\" aria-invalid=\"false\" type=\"text\">
                        </td>
                    
                        <td>
                            <input type=\"text\" name=\"product_quantity[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" class=\"total_qntt_" . $product_details->product_id . " form-control text-right\" id=\"total_qntt_" . $product_details->product_id . "\" placeholder=\"0.00\" min=\"0\" value='" . $qty . "'/>
                        </td>

                        <td style=\"width:85px\">
                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" value='" . $product_details->price . "' id=\"price_item_" . $product_details->product_id . "\" class=\"price_item1 form-control text-right\" required placeholder=\"0.00\" min=\"0\"/>
                        </td>

                        <td class=\"\">
                            <input type=\"text\" name=\"discount[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"discount_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                           
                        </td>

                        <td class=\"text-right\" style=\"width:100px\">
                            <input class=\"total_price form-control text-right\" type=\"text\" name=\"total_price[]\" id=\"total_price_" . $product_details->product_id . "\" value='" . $product_details->price . "' tabindex=\"-1\" readonly=\"readonly\"/>
                        </td>

                        <td>";
            $sl = 0;
            foreach ($taxfield as $taxes) {
                $txs = 'tax' . $sl;
                $tr .= "<input type=\"hidden\" id=\"total_tax" . $sl . "_" . $product_details->product_id . "\" class=\"total_tax" . $sl . "_" . $product_details->product_id . "\" value='" . $prinfo[0][$txs] . "'/>
                            <input type=\"hidden\" id=\"all_tax" . $sl . "_" . $product_details->product_id . "\" class=\" total_tax" . $sl . "\" value='" . $prinfo[0][$txs] * $product_details->price . "' name=\"tax[]\"/>";
                $sl++;
            }

            $tr .= "<input type=\"hidden\" id=\"total_discount_" . $product_details->product_id . "\" />
                            <input type=\"hidden\" id=\"all_discount_" . $product_details->product_id . "\" class=\"total_discount dppr\"/>
                            <button  class=\"btn btn-danger btn-xs text-center\" type=\"button\"  onclick=\"deleteRow(this)\">" . '<i class="fa fa-close"></i>' . "</button>
                        </td>
                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }

    public function invoice_inserted_data_manual()
    {
        $data['title']      = display('invoice_print');
        $invoice_id         = $this->input->post('invoice_id', TRUE);
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
            }
        }


        $payment_method_list = $this->invoice_model->invoice_method_wise_balance($invoice_id);
        $terms_list = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result();
        $totalbal      = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword = $totalbal;
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
            'title'             => display('invoice_details'),
            'invoice_id'        => $invoice_detail[0]['invoice_id'],
            'invoice_no'        => $invoice_detail[0]['invoice'],
            'customer_name'     => $invoice_detail[0]['customer_name'],
            'customer_address'  => $invoice_detail[0]['customer_address'],
            'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
            'customer_email'    => $invoice_detail[0]['customer_email'],
            'final_date'        => $invoice_detail[0]['final_date'],
            'invoice_details'   => $invoice_detail[0]['invoice_details'],
            'total_amount'      => number_format($invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'], 2, '.', ','),
            'grand_total'       => $invoice_detail[0]['total_amount'],
            'subTotal_quantity' => $subTotal_quantity,
            'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data'  => $invoice_detail,
            'am_inword'         => $amount_inword,
            'is_discount'       => $invoice_detail[0]['total_discount'] - $invoice_detail[0]['invoice_discount'],
            'users_name'        => $users->first_name . ' ' . $users->last_name,
            'tax_regno'         => $txregname,
            'is_desc'           => $descript,
            'is_serial'         => $isserial,
            'is_unit'           => $isunit,
            'all_discount'         => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'p_method_list'        => $payment_method_list,
            'terms_list'           => $terms_list,
            'total_vat'            => number_format($invoice_detail[0]['total_vat_amnt'] + $invoice_detail[0]['total_tax'], 2, '.', ','),
        );
        $data['module']     = "invoice";
        $data['page']       = "invoice_html_manual";
        echo modules::run('template/layout', $data);
    }
    /*invoice no generator*/

    public function number_generator($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(sale_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('sale');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 1000000000;
            } else {
                $invoice_no = 3000000000;
            }
        }
        return $invoice_no;
    }

    public function bdtask_customer_autocomplete()
    {
        $customer_id    = $this->input->post('customer_id', TRUE);
        $customer_info  = $this->invoice_model->customer_search($customer_id);

        $list[''] = '';
        foreach ($customer_info as $value) {
            $json_customer[] = array('label' => $value['customer_name'], 'value' => $value['customer_id']);
        }
        echo json_encode($json_customer);
    }

    /*product autocomple search*/
    public function bdtask_autocomplete_product()
    {
        $product_name   = $this->input->post('product_name', TRUE);
        $product_info   = $this->invoice_model->autocompletproductdata($product_name);
        if (!empty($product_info)) {
            $list[''] = '';
            foreach ($product_info as $value) {
                $json_product[] = array('label' => $value['product_name'] . '(' . $value['product_model'] . ')', 'value' => $value['product_id']);
            }
        } else {
            $json_product[] = 'No Product Found';
        }
        echo json_encode($json_product);
    }

    /*after selecting product retrieve product info*/
    public function retrieve_product_data_inv()
    {
        $product_id   = $this->input->post('product_id', TRUE);
        $product_info = $this->invoice_model->get_total_product_invoic($product_id);
        echo json_encode($product_info);
    }
    public function bdtask_batchwise_productprice()
    {
        $product_id   = $this->input->post('prod_id', TRUE);
        $batch_no   = $this->input->post('batch_no', TRUE);

        $this->db->select('sum(quantity) as purchase_qty,batch_id,product_id');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('batch_id', $batch_no);
        $pur_product_batch = $this->db->get()->row();

        $sellt_prod_batch = $this->db->select('sum(quantity) as sale_qty,batch_id, product_id')
            ->from('invoice_details')->where('product_id', $product_id)
            ->where('batch_id', $batch_no)
            ->get()
            ->row();


        $batch_wise_stock =  (!empty($pur_product_batch->purchase_qty) ? $pur_product_batch->purchase_qty : 0) - (!empty($sellt_prod_batch->sale_qty) ? $sellt_prod_batch->sale_qty : 0);
        echo sprintf('%0.2f', $batch_wise_stock);
    }



    /*after select customer retrieve customer previous balance*/
    public function previous()
    {
        $customer_id = $this->input->post('customer_id', TRUE);
        $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
        $this->db->from('customer_information a');
        $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
        $this->db->where('a.customer_id', $customer_id);
        $result = $this->db->get()->result_array();
        $balance = $result[0]['balance'];
        $b = (!empty($balance) ? $balance : 0);
        if ($b) {
            echo  $b;
        } else {
            echo  $b;
        }
    }



    public function instant_customer()
    {

        $data = array(
            'customer_name'    => $this->input->post('customer_name', TRUE),
            'customer_address' => $this->input->post('address', TRUE),
            'customer_mobile'  => $this->input->post('mobile', TRUE),
            'customer_email'   => $this->input->post('email', TRUE),
            'status'           => 1
        );

        $result = $this->db->insert('customer_information', $data);
        if ($result) {

            $customer_id = $this->db->insert_id();

            //Customer  basic information adding.
            $coa = $this->customer_model->headcode();
            if ($coa->HeadCode != NULL) {
                $headcode = $coa->HeadCode + 1;
            } else {
                $headcode = "102030001";
            }
            $c_acc      = $customer_id . '-' . $this->input->post('customer_name', TRUE);
            $createby   = $this->session->userdata('id');
            $createdate = date('Y-m-d H:i:s');

            $customer_coa = [
                'HeadCode'         => $headcode,
                'HeadName'         => $c_acc,
                'PHeadName'        => 'Customer Receivable',
                'HeadLevel'        => '4',
                'IsActive'         => '1',
                'IsTransaction'    => '1',
                'IsGL'             => '0',
                'HeadType'         => 'A',
                'IsBudget'         => '0',
                'IsDepreciation'   => '0',
                'customer_id'      => $customer_id,
                'DepreciationRate' => '0',
                'CreateBy'         => $createby,
                'CreateDate'       => $createdate,
            ];
            //Previous balance adding -> Sending to customer model to adjust the data.
            // $this->db->insert('acc_coa',$customer_coa);

            $sub_acc = [
                'subTypeId'   => 3,
                'name'        => $data['customer_name'],
                'referenceNo' => $customer_id,
                'status'      => 1,
                'created_date' => date("Y-m-d"),

            ];
            $this->db->insert('acc_subcode', $sub_acc);



            $data['status']        = true;
            $data['message']       = display('save_successfully');
            $data['customer_id']   = $customer_id;
            $data['customer_name'] = $data['customer_name'];
        } else {
            $data['status'] = false;
            $data['exception'] = display('please_try_again');
        }
        echo json_encode($data);
    }



    public function bdtask_invoice_details_directprint($invoice_id = null)
    {
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
            }
        }


        $totalbal = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword     = $totalbal;
        $user_id           = $invoice_detail[0]['sales_by'];
        $users             = $this->invoice_model->user_invoice_data($user_id);
        $company_info      = $this->invoice_model->retrieve_company();
        $currency_details  = $this->invoice_model->retrieve_setting_editdata();
        $data = array(
            'title'             => display('invoice_details'),
            'invoice_id'        => $invoice_detail[0]['invoice_id'],
            'invoice_no'        => $invoice_detail[0]['invoice'],
            'customer_name'     => $invoice_detail[0]['customer_name'],
            'customer_address'  => $invoice_detail[0]['customer_address'],
            'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
            'customer_email'    => $invoice_detail[0]['customer_email'],
            'final_date'        => $invoice_detail[0]['final_date'],
            'invoice_details'   => $invoice_detail[0]['invoice_details'],
            'total_amount'      => number_format($invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'], 2, '.', ','),
            'subTotal_quantity' => $subTotal_quantity,
            'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data'  => $invoice_detail,
            'am_inword'         => $amount_inword,
            'is_discount'       => $invoice_detail[0]['total_discount'] - $invoice_detail[0]['invoice_discount'],
            'users_name'        => $users->first_name . ' ' . $users->last_name,
            'tax_regno'         => $txregname,
            'is_desc'           => $descript,
            'is_serial'         => $isserial,
            'is_unit'           => $isunit,
            'discount_type'     => $currency_details[0]['discount_type'],
            'company_info'      => $company_info,
            'logo'              => $currency_details[0]['invoice_logo'],
            'position'          => $currency_details[0]['currency_position'],
            'currency'          => $currency_details[0]['currency'],
        );
        return $data;
    }


    public function generator($lenth)
    {
        $number = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "N", "M", "O", "P", "Q", "R", "S", "U", "V", "T", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 34);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }

    /*invoice no generator*/
    public function number_generator_ajax()
    {
        $this->db->select_max('invoice', 'invoice_no');
        $query      = $this->db->get('invoice');
        $result     = $query->result_array();
        $invoice_no = $result[0]['invoice_no'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1000;
        }
        echo  $invoice_no;
    }

    // category part
    function bdtask_terms_list()
    {
        $data['title']      = display('terms_list');
        $data['module']     = "invoice";
        $data['page']       = "terms_list";
        $data["allterms_list"] = $this->invoice_model->allterms_list();
        echo modules::run('template/layout', $data);
    }


    public function bdtask_terms_form($id = null)
    {
        $data['title'] = display('terms_add');
        #-------------------------------#
        $this->form_validation->set_rules('term_condi', display('term_condi'), 'required');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['single_terms'] = (object)$postData = [
            'id'          => $id,
            'description' => $this->input->post('term_condi', true),
            'status'      => $this->input->post('status', true),
        ];
        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->invoice_model->create_terms($postData)) {
                    #set success message
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }

                redirect("terms_list");
            } else {
                if ($this->invoice_model->update_terms($postData)) {
                    $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                redirect("terms_list");
            }
        } else {
            if (!empty($id)) {
                $data['title']    = display('terms_update');
                $data['single_terms'] = $this->invoice_model->single_terms_data($id);
            }

            $data['module']   = "invoice";
            $data['page']     = "terms_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function bdtask_terms($id = null)
    {
        if ($this->invoice_model->delete_terms($id)) {
            $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }

        redirect("terms_list");
    }


    public function save_sale()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        $num = $this->number_generatorsales($this->input->post('type2', TRUE));
        $lastupdate = date('Y-m-d H:i:s');

        $query = "
    INSERT INTO sale 
    (id,sale_id, date, details, type2, discount, total_discount_ammount, total_vat_amnt, grandTotal, total,customer_id,employee_id,payment_type,lastupdateddate,createddate,userid,
    incidenttype,already,branch) 
    VALUES 
    (0,AES_ENCRYPT('{$num}', '{$encryption_key}') , 
     '{$this->input->post('date', TRUE)}',
     '{$this->input->post('details', TRUE)}',  
     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('customer_id', TRUE)}',
     '{$this->input->post('employee_id', TRUE)}',
      '{$this->input->post('payment_type', TRUE)}',
      '{$lastupdate}',
      '{$lastupdate}','{$this->session->userdata('id')}',
       '{$this->input->post('incidenttype', TRUE)}',
            0,
                   '{$this->input->post('branch', TRUE)}'


    );";




        $this->db->query($query);



        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {

            $qu = -$item['quantity'];
            $query = "
            INSERT INTO stock_details 
            (id,product, store, stock, type, pid,date,conversion_id) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
             AES_ENCRYPT('{$qu}', '{$encryption_key}'),
             'sales',
             '{$inserted_id}','{$this->input->post('date', TRUE)}',
              '{$item['conversion_id']}'
            );
        ";
            $this->db->query($query);



            $store   =   $this->db->select("auto_gdn")->from('store ')->where('id', $item['store'])->get()->row();
            if ($store->auto_gdn == 0) {
                $query = "
                INSERT INTO phystock_details 
                (id,product, store, stock, type, pid,date,conversion_id) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                 AES_ENCRYPT('{$qu}', '{$encryption_key}'),
                 'sales',
                 '{$inserted_id}','{$this->input->post('date', TRUE)}',
                  '{$item['conversion_id']}'

                );
            ";
                $this->db->query($query);
            }

            $query = "
            INSERT INTO sale_details 
            (id, pid, product, store, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,
            type2,conversionratio,unittype,conversion_id) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['product']}', 
              '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
               AES_ENCRYPT('{$item['conversionratio']}', '{$encryption_key}'),
            '{$item['unittype']}', '{$item['conversion_id']}'

            );";

            $this->db->query($query);
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'sale', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        // $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $items,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $num,
            'payment' => $this->input->post('payment', TRUE)
        );

        $data['details'] = $this->load->view('invoice/pos_print',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");      

        echo json_encode($data);
    }

    public function customer_info($customer_id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("a.customer_id as customer_id,
       AES_DECRYPT(a.customer_name, '{$encryption_key}') AS customer_name,
      AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS customer_mobile,
       AES_DECRYPT(a.customer_address, '{$encryption_key}') AS customer_address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS customer_mobile,
       AES_DECRYPT(a.customer_email, '{$encryption_key}') AS customer_email,

       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country")
            ->from('customer_information a')
            ->where('customer_id', $customer_id)
            ->get()
            ->row();
    }


    public function number_generatorsales($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(sale_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('sale');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 1000000000;
            } else {
                $invoice_no = 3000000000;
            }
        }
        return $invoice_no;
    }
    public function checksales()
    {
        $postData = $this->input->post();
        $data = $this->invoice_model->sale($postData, $this->input->post('type2'), $this->input->post('branchid'));
        echo json_encode($data);
    }


    public function getSaleById()
    {

        $encryption_key = Config::$encryption_key;

        $this->db->select("
         po.id, 
         si.customer_id,
         po.date, 
           po.branch, 
         po.details, 
 po.payment_type, 
          po.incidenttype,                    po.employee_id, 
         AES_DECRYPT(po.discount, '" . $encryption_key . "') AS discount, 
         AES_DECRYPT(po.total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount, 
         AES_DECRYPT(po.total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt, 
         AES_DECRYPT(po.grandTotal, '" . $encryption_key . "') AS grandTotal, 
         AES_DECRYPT(po.total, '" . $encryption_key . "') AS total,
         pod.product,
         pod.store,
         pi.unit,
         AES_DECRYPT(pod.quantity, '" . $encryption_key . "') AS quantity,
         AES_DECRYPT(pod.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(pod.discount, '" . $encryption_key . "') AS discount2,
         AES_DECRYPT(pod.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(pod.vat_percent, '" . $encryption_key . "') AS vat_percent,
         AES_DECRYPT(pod.vat_value,'" . $encryption_key . "') AS vat_value,
         AES_DECRYPT(pod.total_price, '" . $encryption_key . "') AS total_price,
         AES_DECRYPT(pod.total_discount, '" . $encryption_key . "') AS total_discount,
         AES_DECRYPT(pod.all_discount,'" . $encryption_key . "') AS all_discount, (SELECT SUM(AES_DECRYPT( c.stock , '" . $encryption_key . "')) AS actualstock 
      FROM stock_details c 
      WHERE pod.product = c.product
        AND pod.store = c.store 
       ) AS avstock
     ");
        $this->db->from('sale po');
        $this->db->join('customer_information si', 'si.customer_id = po.customer_id', 'inner');
        $this->db->join('sale_details pod', 'pod.pid = po.id', 'inner');
        $this->db->join('product_information pi', 'pi.id = pod.product', 'inner');

        $this->db->where('po.id', $this->input->post('id'));

        $query = $this->db->get();


        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function best_of_sale2()
    {
        $encryption_key = Config::$encryption_key;
        $sql = "
    SELECT 
        pc.category_name,
        DATE_FORMAT(s.date, '%Y-%m') AS sale_month,
        SUM(AES_DECRYPT(sd.quantity,'" . $encryption_key . "')) AS product_count
    FROM 
        sale_details sd
    INNER JOIN 
        sale s ON s.id = sd.pid
    INNER JOIN 
        product_information pi ON pi.id = sd.product
     INNER JOIN 
        product_category pc ON pc.category_id = pi.category_id
    WHERE 
        MONTH(s.date) = " . $this->input->post('month') . " AND  
        YEAR(s.date) = " . $this->input->post('year') . "
    GROUP BY 
        pc.category_id
    ORDER BY 
        product_count DESC
    LIMIT 5
";

        $query =  $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode("");
        }
    }


    public function update_sale()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');


        $query = "
    UPDATE sale
    SET 
        date = '{$this->input->post('date', TRUE)}',
        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
        payment_type = '{$this->input->post('payment_type', TRUE)}',
        employee_id = '{$this->input->post('employee_id', TRUE)}',
        details = '{$this->input->post('details', TRUE)}',
        discount = AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
        total_discount_ammount = AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
        total_vat_amnt = AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
        grandTotal = AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
        total = AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
        customer_id = '{$this->input->post('customer_id', TRUE)}',
         lastupdateddate='{$lastupdate}',
         userid='{$this->session->userdata('id')}',
          incidenttype= '{$this->input->post('incidenttype', TRUE)}',
          branch='{$this->input->post('branch', TRUE)}',
         already=0
    WHERE id = '{$this->input->post('id', TRUE)}';
";

        $this->db->query($query);


        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'sales')
            ->delete('stock_details');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'sales')
            ->delete('phystock_details');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->delete('sale_details');

        $this->db->where('voucher_id', $this->input->post('id', TRUE))
            ->where('scenario', 'saleinvoice')
            ->delete('audit_stock');


        foreach ($items as $item) {
            $qu = -$item['quantity'];
            $query = "
            INSERT INTO stock_details 
            (id,product, store, stock, type, pid,date) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
             AES_ENCRYPT('{$qu}', '{$encryption_key}'),
             'sales',
             '{$this->input->post('id', TRUE)}', '{$this->input->post('date', TRUE)}'
            );
        ";
            $this->db->query($query);

            $store   =   $this->db->select("auto_gdn")->from('store ')->where('id', $item['store'])->get()->row();
            if ($store->auto_gdn == 0) {
                $query = "
                INSERT INTO phystock_details 
                (id,product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                 AES_ENCRYPT('{$qu}', '{$encryption_key}'),
                 'sales',
                 '{$this->input->post('id', TRUE)}','{$this->input->post('date', TRUE)}'
                );
            ";
                $this->db->query($query);
            }



            $query = "
            INSERT INTO sale_details 
            (id, pid, product, store, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2) 
            VALUES 
            (0, 
             '{$this->input->post('id', TRUE)}', 
             '{$item['product']}', 
              '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            );";



            $this->db->query($query);
        }

        $query = "
    INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
    VALUES (
        0, 
        'sale', 
        'update', 
        '{$this->input->post('id', TRUE)}', 
        '{$this->session->userdata('id')}',  '{$lastupdate}'
    );
";

        $this->db->query($query);

        $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $items,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $invoiceno[0]['sale_id'],
            'payment' => $this->input->post('payment', TRUE)
        );

        $data['details'] = $this->load->view('invoice/pos_print',  $data, true);


        echo json_encode($data);
    }


    public function invoice_no($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(sale_id, '" . $encryption_key . "') AS sale_id")
            ->from('sale')
            ->where('id', $id)
            ->get()
            ->result_array();
    }


    public function delete_sale($id = null)
    {
        $lastupdate = date('Y-m-d H:i:s');

        $productExists = $this->db->from('gdn_stock')
            ->where('voucherno', $id)
            ->count_all_results();
        $base_url = base_url();


        if ($productExists > 0) {

            echo '<script type="text/javascript">
            alert("Cannot delete this sale detail because this sale detail is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'invoice_list";
           </script>';
        } else {
            $this->db->where('pid', $id)
                ->where('type', 'sales')
                ->delete('stock_details');

            $this->db->where('pid', $id)
                ->where('type', 'sales')
                ->delete('phystock_details');

            $this->db->where('pid', $id)
                ->delete('sale_details');

            $this->db->where('id', $id)
                ->delete('sale');

            $this->db->where('voucher_id',  $id)
                ->where('scenario', 'saleinvoice')
                ->delete('audit_stock');

            $query = "
                INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                VALUES (
                    0, 
                    'sale', 
                    'update', 
                    '{$id}', 
                    '{$this->session->userdata('id')}',  '{$lastupdate}'
                );
            ";

            $this->db->query($query);


            echo '<script type="text/javascript">
   alert("Deleted successfully");
   window.location.href = "' . $base_url . 'invoice_list";
  </script>';
        }
    }

    public function update_salestatus($id = null)
    {
        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE sale
    SET 
        status = 1,
        lastupdateddate='{$lastupdate}'
    WHERE id = '{$id}';
";

        $this->db->query($query);

        $query = "
                INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                VALUES (
                    0, 
                    'sale', 
                    'update', 
                    '{$id}', 
                    '{$this->session->userdata('id')}',  '{$lastupdate}'
                );
            ";

        $this->db->query($query);


        redirect("invoice_list");
    }

    public function update_salestatusredo($id = null)
    {
        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE sale
    SET 
        status = 0,
        lastupdateddate='{$lastupdate}'
    WHERE id = '{$id}';
";

        $this->db->query($query);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'sale', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        redirect("invoice_list");
    }

    public function pos_print()
    {

        $sale = $this->sale($this->input->post('id', TRUE));
        $saledetails = $this->saledetails($this->input->post('id', TRUE));
        $customer_info    = $this->customer_info($sale[0]['customer_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $saledetails,
            'total' => $sale[0]['total'],
            'total_dis' => $sale[0]['discount'] == "" ? "0.0" : $sale[0]['discount'],
            'total_discount_ammount' =>  $sale[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $sale[0]['total_vat_amnt'],
            'grandTotal' =>  $sale[0]['grandTotal'],
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $sale[0]['date'],
            'details'    => "",
            'invoiceno' => $sale[0]['sale_id'],
            'payment' => ""
        );

        $data['details'] = $this->load->view('invoice/pos_print',  $data, true);


        echo json_encode($data);
    }


    public function sale($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("AES_DECRYPT(sale_id, '" . $encryption_key . "') AS sale_id,
         AES_DECRYPT(total, '" . $encryption_key . "') AS total,
         AES_DECRYPT(discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount,
         AES_DECRYPT(total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt,customer_id,
            AES_DECRYPT(grandTotal, '" . $encryption_key . "') AS grandTotal,date ")
            ->from('sale')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function saledetails($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("pi.product_name,AES_DECRYPT(sd.quantity, '" . $encryption_key . "') AS quantity,
         AES_DECRYPT(sd.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(sd.discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(sd.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(sd.vat_percent, '" . $encryption_key . "') AS vat_percent,
            AES_DECRYPT(sd.vat_value, '" . $encryption_key . "') AS vat_value,
             AES_DECRYPT(sd.total_price, '" . $encryption_key . "') AS total_price,
              AES_DECRYPT(sd.total_discount, '" . $encryption_key . "') AS total_discount,
                AES_DECRYPT(sd.all_discount, '" . $encryption_key . "') AS all_discount
            ,sd.store ")
            ->from('sale_details sd')
            ->join('product_information pi', 'pi.id = sd.product', "left")
            ->where('pid', $id)
            ->get()
            ->result_array();
    }
    public function bdtask_dupl_sales()
    {
        $data['title']         = display('dupl_sales');
        $data['module']        = "invoice";
        $data['page']          = "add_invoice_csv";
        if (!$this->permission1->method('dupl_sales', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        $data['pmetods'] = $this->pmethod_dropdown();
        $data['products'] = $this->active_product();
        $data['stores'] = $this->product_model->active_store();
        $data['customers'] = $this->customer_list();
        $data['employees'] = $this->employee_list();
        $data['branches'] = $this->branches();
        $data['invoicesdetails'] = $this->invoices();




        echo modules::run('template/layout', $data);
    }

    public function branches()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("id,AES_DECRYPT(name, '{$encryption_key}') AS name")
            ->from('branch')
            ->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function invoices()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("id,AES_DECRYPT(sale_id, '{$encryption_key}') AS sale_id")
            ->from('sale');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function save_sale2()
    {
        $items_b = $this->input->post('items', TRUE);
        $invoices = $this->input->post('invoice', TRUE);

        $encryption_key = Config::$encryption_key;
        $lastupdate = date('Y-m-d H:i:s');
        $num = $this->number_generatorupload();



        $invoice_ids_string = implode(',', array_column($invoices, 'invoiceId'));

        $query = "
        INSERT INTO bulk_details 
        (id,uploaded_id, date, uploadedby,invoices) 
        VALUES 
        (0,AES_ENCRYPT('{$num}', '{$encryption_key}') , 
         '{$lastupdate}',
         '{$this->session->userdata('id')}',  
         AES_ENCRYPT('{$invoice_ids_string}', '{$encryption_key}')
        );";

        $this->db->query($query);


        foreach ($invoices as $invoice) {
            $query = "
        INSERT INTO sale 
        (id,sale_id, date, details, type2, discount, total_discount_ammount, total_vat_amnt, grandTotal, total,customer_id,employee_id,payment_type,lastupdateddate,createddate,userid,incidenttype,already,branch) 
        VALUES 
        (0,AES_ENCRYPT('{$invoice['invoiceId']}', '{$encryption_key}') , 
         '{$invoice['date']}',
         '{$invoice['details']}',  
         AES_ENCRYPT('{$invoice['type2']}', '{$encryption_key}'), 
         AES_ENCRYPT('{$invoice['discount']}', '{$encryption_key}'), 
         AES_ENCRYPT('{$invoice['total_discount_ammount']}', '{$encryption_key}'), 
         AES_ENCRYPT('{$invoice['total_vat_amnt']}', '{$encryption_key}'), 
         AES_ENCRYPT('{$invoice['grandTotal']}', '{$encryption_key}'), 
         AES_ENCRYPT('{$invoice['total']}', '{$encryption_key}'),
         '{$invoice['customer_id']}',
         '{$invoice['employee_id']}',
          '{$invoice['payment']}',
          '{$lastupdate}',
          '{$lastupdate}','{$this->session->userdata('id')}',
           '{$invoice['incidenttype']}',
            0,
           '{$invoice['branch']}'
        );";

            $this->db->query($query);

            $inserted_id = $this->db->insert_id();

            $items = array_filter($items_b, function ($item) use ($invoice) {
                return $item['invoiceId'] === $invoice['invoiceId'];
            });
            foreach ($items as $item) {

                $qu = -$item['quantity'];
                $query = "
                        INSERT INTO stock_details 
                        (id,product, store, stock, type, pid,date) 
                        VALUES 
                        (0, 
                         '{$item['product']}', 
                         '{$item['store']}', 
                         AES_ENCRYPT('{$qu}', '{$encryption_key}'),
                         'sales',
                         '{$inserted_id}','{$this->input->post('date', TRUE)}'
                        );
                    ";
                $this->db->query($query);



                $store   =   $this->db->select("auto_gdn")->from('store ')->where('id', $item['store'])->get()->row();
                if ($store->auto_gdn == 0) {
                    $query = "
                            INSERT INTO phystock_details 
                            (id,product, store, stock, type, pid,date) 
                            VALUES 
                            (0, 
                             '{$item['product']}', 
                             '{$item['store']}', 
                             AES_ENCRYPT('{$qu}', '{$encryption_key}'),
                             'sales',
                             '{$inserted_id}','{$this->input->post('date', TRUE)}'
                            );
                        ";
                    $this->db->query($query);
                }

                $query = "
                        INSERT INTO sale_details 
                        (id, pid, product, store, quantity, 
                        product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2) 
                        VALUES 
                        (0, 
                         '{$inserted_id}', 
                         '{$item['product']}', 
                          '{$item['store']}', 
                         AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
                         AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
                         AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
                         AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
                         AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
                         AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
                          AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
                          AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
                          AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
                           AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                        );";

                $this->db->query($query);
            }

            $query = "
                    INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                    VALUES (
                        0, 
                        'sale', 
                        'insert', 
                         '{$inserted_id}', 
                        '{$this->session->userdata('id')}',  '{$lastupdate}'
                    );
                ";

            $this->db->query($query);
        }
        echo json_encode("Success");
    }

    public function number_generatorupload()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(uploaded_id,'" . $encryption_key . "')", 'id');
        // $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('bulk_details');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1000000000;
        }
        return $invoice_no;
    }


    public function checkBulkUpload()
    {
        $postData = $this->input->post();
        $data = $this->invoice_model->BulkUpload($postData);
        echo json_encode($data);
    }


    public function download_bulk()
    {

        $sale = $this->download($this->input->post('invoices', TRUE));
        // 

        echo json_encode($sale);
    }


    public function download($invoices)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("
         AES_DECRYPT(sa.sale_id, '" . $encryption_key . "') AS InvoiceId,
         sa.date AS Date,
          AES_DECRYPT(ba.name, '" . $encryption_key . "') AS Branch,
        CASE 
        WHEN sa.incidenttype = 1 THEN 'Sale'
        WHEN sa.incidenttype = 2 THEN 'Wholesale'
        ELSE ''
    END AS IncidentType,
      AES_DECRYPT(ci.customer_name, '" . $encryption_key . "') AS Customer,
      eh.last_name as Employee,
        pi.product_name as Product,s.name as Store,pi.unit as Unit,
        AES_DECRYPT(sd.quantity, '" . $encryption_key . "') AS Qty,
         AES_DECRYPT(sd.product_rate, '" . $encryption_key . "') AS PriceVal,
         AES_DECRYPT(sd.discount, '" . $encryption_key . "') AS Discount,
         AES_DECRYPT(sd.vat_percent, '" . $encryption_key . "') AS VAT,
          AES_DECRYPT(sa.discount, '" . $encryption_key . "') AS SaleDiscount,
         pt.name as PaymentType,sa.details as Details
           ")
            ->from('sale_details sd')
            ->join('product_information pi', 'pi.id = sd.product', "left")
            ->join('store s', 's.id = sd.store', "left")
            ->join('sale sa', 'sa.id = sd.pid', "left")
            ->join('branch ba', 'ba.id = sa.branch', "left")
            ->join('customer_information ci', 'ci.customer_id = sa.customer_id', "left")
            ->join('employee_history eh', 'eh.id = sa.employee_id', "left")
            ->join('payment_type pt', 'pt.id = sa.payment_type', "left")
            ->where_in('AES_DECRYPT(sa.sale_id, "' . $encryption_key . '")', $invoices)
            ->get()
            ->result_array();
    }

    public function delete()
    {

        $invoices = $this->input->post('invoices', TRUE);

        $this->db->where('id',  $this->input->post('id', TRUE))
                ->delete('bulk_details');


        foreach ($invoices as $invoice) {


            $this->delete_sale2($invoice);

        }


        echo json_encode("Success");
    }



    public function delete_sale2($id = null)
    {
        $lastupdate = date('Y-m-d H:i:s');
        $encryption_key = Config::$encryption_key;

        $sale = $this->db->select("id")
            ->from('sale')
            ->where("AES_DECRYPT(sale_id,'" . $encryption_key . "')", $id)
            ->get()
            ->row();
        $productExists = $this->db->from('gdn_stock')
            ->where('voucherno',  $sale->id)
            ->count_all_results();
        $base_url = base_url();


        if ($productExists > 0) {

        //     echo '<script type="text/javascript">
        //     alert("Cannot delete this sale detail because this sale detail is linked to it or something went wrong");
        //     window.location.href = "' . $base_url . 'invoice_list";
        //    </script>';
        } else {
            $this->db->where('pid', $sale->id)
                ->where('type', 'sales')
                ->delete('stock_details');

            $this->db->where('pid', $sale->id)
                ->where('type', 'sales')
                ->delete('phystock_details');

            $this->db->where('pid', $sale->id)
                ->delete('sale_details');

            $this->db->where('id', $sale->id)
                ->delete('sale');

            $this->db->where('voucher_id',  $sale->id)
                ->where('scenario', 'saleinvoice')
                ->delete('audit_stock');

            $query = "
                INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                VALUES (
                    0, 
                    'sale', 
                    'update', 
                    '{$id}', 
                    '{$this->session->userdata('id')}',  '{$lastupdate}'
                );
            ";

            $this->db->query($query);
        }
    }
}
