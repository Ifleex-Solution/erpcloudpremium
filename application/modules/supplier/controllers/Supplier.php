<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Supplier extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'supplier_model'
        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }

    function index()
    {
        $data['title']      = display('supplier_list');
        $data['module']     = "supplier";
        $data['page']       = "supplier_list";
        $data["supplier_dropdown"] = $this->supplier_model->supplier_dropdown();
        $data['all_supplier'] = $this->supplier_model->allsupplier();
        if (!$this->permission1->method('manage_supplier', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    function loadmanagecheque()
    {
        $data['title']      = "Manage Cheque";
        $data['module']     = "supplier";
        $data['page']       = "managecheque";
        echo modules::run('template/layout', $data);
    }

    function chequeflowreport()
    {

        $data['title']      = "Manage Cheque";
        $data['module']     = "supplier";
        $data['page']       = "chequeflow";
        echo modules::run('template/layout', $data);
    }

    public function getallcheques()
    {
        $this->db->query("SET @rownum := 0");
        $query = $this->db->query("
            SELECT 
                @rownum := @rownum + 1 AS seq,
                cq.id,
                cq.cheque_no,
                cq.type,
                cq.effectivedate,
                CASE 
                    WHEN cq.receiptvoucher = 2 THEN receive2.name 
                    WHEN cq.receiptvoucher = 1 THEN receive1.HeadName 
                    ELSE ci.customer_name 
                END AS customer_name,
                CASE 
                    WHEN cq.paymentvoucher = 2 THEN paid2.name 
                    WHEN cq.paymentvoucher = 1 THEN paid1.HeadName 
                    ELSE si.supplier_name 
                END AS supplier_name,
                cq.status AS chequestatus,
                ac1.HeadName AS bank2,
                FORMAT(cq.amount, 0) AS amount,
                cq.ismanual
            FROM 
                cheque cq
                LEFT JOIN customer_information ci ON ci.customer_id = cq.receivedfrom
                LEFT JOIN supplier_information si ON si.supplier_id = cq.paidto
                LEFT JOIN acc_coa ac1 ON ac1.HeadCode = cq.depositedbank
                LEFT JOIN acc_coa receive1 ON receive1.id = cq.receivedfrom
                LEFT JOIN acc_subcode receive2 ON receive2.id = cq.receivedfrom
                LEFT JOIN acc_coa paid1 ON paid1.id = cq.paidto
                LEFT JOIN acc_subcode paid2 ON paid2.id = cq.paidto
            ORDER BY 
                cq.id DESC
        ");

        $result = $query->result_array();


        echo json_encode($result);
    }

    public function refreshallcheques()
    {

        // $query = $this->db->get();

        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        // Fetch data from the database with effectivedate within the last 6 months
        $result = $this->db->select('cq.cheque_no, cq.effectivedate')
            ->from('cheque cq')
            ->join('customer_information ci', 'ci.customer_id = cq.receivedfrom', 'left')
            ->join('supplier_information si', 'si.supplier_id = cq.paidto', 'left')
            ->where('cq.type', '3rd Party')
            ->where_in('cq.status', ['Valid', 'Pending'])
            ->where('cq.effectivedate <=', $sixMonthsAgo) // Effectivedate within the last 6 months
            ->order_by('cq.updatedate', 'desc')
            ->get()
            ->result_array();

        // Process results
        foreach ($result as $row) {
            $effectiveDate = strtotime($row['effectivedate']);
            $sixMonthsAgoTimestamp = strtotime($sixMonthsAgo);

            if ($effectiveDate < $sixMonthsAgoTimestamp) {
                // Update status as valid
                $this->db->where('cheque_no', $row['cheque_no'])
                    ->update('cheque', ['status' => 'Invalid']);
            }
        }

        $result = $this->db->select('cq.cheque_no,cq.effectivedate')
            ->from('cheque cq')
            ->join('customer_information ci', 'ci.customer_id = cq.receivedfrom', 'left')
            ->join('supplier_information si', 'si.supplier_id = cq.paidto', 'left')
            ->where('cq.type', '3rd Party')
            ->where('cq.status', 'Pending')
            ->order_by('cq.updatedate', 'desc')
            ->get()
            ->result_array();

        foreach ($result as $row) {
            $effectiveDate = strtotime($row['effectivedate']);
            $currentDate = strtotime(date('Y-m-d')); // Current date without time component

            if ($effectiveDate <= $currentDate) {
                // Update status as valid
                $this->db->where('cheque_no', $row['cheque_no'])
                    ->update('cheque', ['status' => 'Valid']);
            }
        }




        echo json_encode($result);
    }

    public function savecheque()
    {
        $input_date_obj = new DateTime($this->input->post('effectivedate', true));
        $current_date_obj = new DateTime(date('Y-m-d'));

        $chequedata = array(
            'cheque_no'           => $this->input->post('chequeno', true),
            'effectivedate'      => $this->input->post('effectivedate', true),
            'amount'             => $this->input->post('amount', true),
            'type'               => '3rd Party',
            'status'             => $input_date_obj <= $current_date_obj ? "Valid" : "Pending",
            'createddate'        => (!empty($this->input->post('chequereceiveddate', TRUE)) ? $this->input->post('chequereceiveddate', TRUE) : date('Y-m-d')),
            'updatedate'         => (!empty($this->input->post('chequereceiveddate', TRUE)) ? $this->input->post('chequereceiveddate', TRUE) : date('Y-m-d')),
            'bankId  ' => $this->input->post('bank', true),
            'branchId ' => $this->input->post('branch', true),
            'receivedfrom ' => $this->input->post('receivedfrom', true),
            'description ' => $this->input->post('description', true),
            'ismanual ' => "yes"
        );
        $this->db->insert('cheque', $chequedata);
        echo json_encode("save sucessfully");
    }

    public function deletecheque()
    {
        $id = $this->input->post('id', true);
        $this->db->where('id', $id);
        $this->db->delete('cheque');
        if ($this->db->affected_rows() > 0) {
            echo json_encode("Deleted successfully");
        } else {
            echo json_encode("No changes made or delte failed");
        }
    }

    public function holdcheque()
    {

        $chequedata = array(
            'status' => "Hold",
        );
        $this->db->where('id', $this->input->post('id', true));
        $this->db->update('cheque', $chequedata);

        if ($this->db->affected_rows() > 0) {
            echo json_encode("Update successfully");
        } else {
            echo json_encode("No changes made or update failed");
        }
    }

    public function unholdcheque()
    {
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        $effectiveDate = strtotime($this->input->post('effectivedate', true));
        $sixMonthsAgoTimestamp = strtotime($sixMonthsAgo);
        if ($effectiveDate < $sixMonthsAgoTimestamp) {
            $this->db->where('id', $this->input->post('id', true))
                ->update('cheque', ['status' => 'Invalid']);
        } else {
            $current_date_obj = strtotime(date('Y-m-d'));
            $this->db->where('id', $this->input->post('id', true));
            $this->db->update('cheque',  ['status' => $effectiveDate <= $current_date_obj ? "Valid" : "Pending"]);
        }

        if ($this->db->affected_rows() > 0) {
            echo json_encode("Update successfully");
        } else {
            echo json_encode("No changes made or update failed");
        }
    }



    public function updatecheque()
    {
        $input_date_obj = new DateTime($this->input->post('effectivedate', true));
        $current_date_obj = new DateTime(date('Y-m-d'));

        $chequedata = array(
            'cheque_no'           => $this->input->post('chequeno', true),
            'effectivedate'      => $this->input->post('effectivedate', true),
            'amount'             => $this->input->post('amount', true),
            'status'             => $input_date_obj <= $current_date_obj ? "Valid" : "Pending",
            'createddate'        => (!empty($this->input->post('chequereceiveddate', TRUE)) ? $this->input->post('chequereceiveddate', TRUE) : date('Y-m-d')),
            'updatedate'         => (!empty($this->input->post('chequereceiveddate', TRUE)) ? $this->input->post('chequereceiveddate', TRUE) : date('Y-m-d')),
            'bankId  ' => $this->input->post('bank', true),
            'branchId ' => $this->input->post('branch', true),
            'receivedfrom ' => $this->input->post('receivedfrom', true),
            'description ' => $this->input->post('description', true),
        );
        $this->db->where('id', $this->input->post('id', true));
        $this->db->update('cheque', $chequedata);

        if ($this->db->affected_rows() > 0) {
            echo json_encode("Update successfully");
        } else {
            echo json_encode("No changes made or update failed");
        }
    }


    public function chequebydata()
    {


        $sql = "
        SELECT 
            cq.cheque_no, 
            cq.type, 
            cq.status AS chequestatus, 
            ac.HeadName, 
            cq.draftdate, 
            cq.effectivedate, 
            CASE 
                WHEN cq.receiptvoucher = 2 THEN receive2.name 
                WHEN cq.receiptvoucher = 1 THEN receive1.HeadName 
                ELSE ci.customer_name 
            END AS customer_name, 
            i.invoice, 
            i.date AS invoice_date, 
            cq.createddate, 
            CASE 
                WHEN cq.paymentvoucher = 2 THEN paid2.name 
                WHEN cq.paymentvoucher = 1 THEN paid1.HeadName 
                ELSE si.supplier_name 
            END AS supplier_name, 
            pi.chalan_no, 
            pi.purchase_date, 
            cq.transfered, 
            cq.amount, 
            cq.updatedate,
            cq.depositeddate,
            ac1.HeadName AS bank2
        FROM 
            cheque cq
            LEFT JOIN customer_information ci ON ci.customer_id = cq.receivedfrom
            LEFT JOIN supplier_information si ON si.supplier_id = cq.paidto
            LEFT JOIN invoice i ON i.invoice_id = cq.sales_no
            LEFT JOIN product_purchase pi ON pi.id = cq.purchase_no
            LEFT JOIN acc_coa ac ON ac.HeadCode = cq.coano
            LEFT JOIN acc_coa ac1 ON ac1.HeadCode = cq.depositedbank
            LEFT JOIN acc_coa receive1 ON receive1.id = cq.receivedfrom
            LEFT JOIN acc_subcode receive2 ON receive2.id = cq.receivedfrom
            LEFT JOIN acc_coa paid1 ON paid1.id = cq.paidto
            LEFT JOIN acc_subcode paid2 ON paid2.id = cq.paidto
    ";

        // Apply filters based on user input
        $filters = array();

        if ($this->input->post('fromdate', true) != "") {
            $filters[] = "cq.updatedate >= '" . $this->db->escape_str($this->input->post('fromdate', true)) . "'";
        }

        if ($this->input->post('todate', true) != "") {
            $filters[] = "cq.updatedate <= '" . $this->db->escape_str($this->input->post('todate', true)) . "'";
        }

        if ($this->input->post('status', true) != "All") {
            $filters[] = "cq.status = '" . $this->db->escape_str($this->input->post('status', true)) . "'";
        }

        if ($this->input->post('type', true) != "All") {
            $filters[] = "cq.type = '" . $this->db->escape_str($this->input->post('type', true)) . "'";
        }

        // Add filters to the SQL query
        if (!empty($filters)) {
            $sql .= " WHERE " . implode(' AND ', $filters);
        }

        // Add ordering
        $sql .= " ORDER BY cq.updatedate DESC;";

        // Execute the query
        $query = $this->db->query($sql);
        $result = $query->result_array();

        echo json_encode($result);
    }

    public function getchequebyid($id)
    {

        // $query = $this->db->get();
        // $postData = $this->input->post();
        $sql = "
        SELECT 
            cq.id,
            cq.cheque_no,
            cq.type,
            ac.HeadName AS coano_name,
            cq.draftdate,
            cq.effectivedate, 
            CASE 
                WHEN cq.receiptvoucher = 2 THEN receive2.name 
                WHEN cq.receiptvoucher = 1 THEN receive1.HeadName 
                ELSE ci.customer_name 
            END AS customer_name,
            CASE 
                WHEN cq.receiptvoucher = 2 THEN receive2.id 
                WHEN cq.receiptvoucher = 1 THEN receive1.id 
                ELSE ci.customer_id 
            END AS customer_id,
            i.invoice,
            i.date AS invoice_date,
            cq.createddate,
            CASE 
            WHEN cq.paymentvoucher = 2 THEN paid2.name 
            WHEN cq.paymentvoucher = 1 THEN paid1.HeadName 
            ELSE si.supplier_name 
        END AS supplier_name,
            pi.chalan_no,
            pi.purchase_date,
            cq.transfered,
            cq.amount,
            cq.updatedate,
            cq.status AS chequestatus,
            cq.description,
            cq.depositeddate,
            cq.bankId,
            cq.branchId,
            ac1.HeadName AS bank2,
            ba.bankname,
            br.branchname,
            cq.ismanual
        FROM 
            cheque cq
            LEFT JOIN customer_information ci ON ci.customer_id = cq.receivedfrom
            LEFT JOIN supplier_information si ON si.supplier_id = cq.paidto
            LEFT JOIN acc_coa receive1 ON receive1.id = cq.receivedfrom
            LEFT JOIN acc_subcode receive2 ON receive2.id = cq.receivedfrom
            LEFT JOIN acc_coa paid1 ON paid1.id = cq.paidto
            LEFT JOIN acc_subcode paid2 ON paid2.id = cq.paidto
            LEFT JOIN invoice i ON i.invoice_id = cq.sales_no
            LEFT JOIN product_purchase pi ON pi.id = cq.purchase_no
            LEFT JOIN acc_coa ac ON ac.HeadCode = cq.coano
            LEFT JOIN `3rdpartybank` ba ON ba.id = cq.bankId
            LEFT JOIN `3rdpartybranch` br ON br.id = cq.branchId
            LEFT JOIN acc_coa ac1 ON ac1.HeadCode = cq.depositedbank
        WHERE 
            cq.id = ?
        ORDER BY 
            cq.id DESC;
    ";

        // Execute the query with the provided ID
        $query = $this->db->query($sql, array($id));

        // Fetch the results
        $result = $query->result_array();


        echo json_encode($result);
    }


    public function bdtask_ChecksupplierList()
    {
        $postData = $this->input->post();
        $data     = $this->supplier_model->getsupplierList($postData);
        echo json_encode($data);
    }



    public function bdtask_form($id = null)
    {
        $encryption_key = Config::$encryption_key;

        $data['title'] = display('add_supplier');
        #-------------------------------#
        $this->form_validation->set_rules('supplier_name', display('supplier_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('supplier_mobile', display('supplier_mobile'), 'max_length[20]');
        if (empty($id)) {
            $this->form_validation->set_rules('supplier_email', display('email'), 'max_length[100]|valid_email|is_unique[supplier_information.email_address]');
        } else {
            $this->form_validation->set_rules('supplier_email', display('email'), 'max_length[100]|valid_email');
        }
        $this->form_validation->set_rules('contact', display('contact'), 'max_length[200]');
        $this->form_validation->set_rules('phone', display('phone'), 'max_length[20]');
        $this->form_validation->set_rules('city', display('city'), 'max_length[100]');
        $this->form_validation->set_rules('state', display('state'), 'max_length[100]');
        $this->form_validation->set_rules('zip', display('zip'), 'max_length[30]');
        $this->form_validation->set_rules('country', display('country'), 'max_length[100]');
        $this->form_validation->set_rules('supplier_address', display('supplier_address'), 'max_length[255]');
        $this->form_validation->set_rules('address2', display('address2'), 'max_length[255]');
        if (!$this->permission1->method('manage_supplier', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        #-------------------------------#


        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($this->input->post('supplier_id', TRUE))) {
                $query = "
                INSERT INTO supplier_information 
                (supplier_id, supplier_name, mobile, emailnumber, email_address, contact, phone, fax, city, state, zip, country, address, address2, status) 
                VALUES 
                ('{$this->input->post('supplier_id', TRUE)}',
                 AES_ENCRYPT('{$this->input->post('supplier_name', TRUE)}', '{$encryption_key}'),
                 AES_ENCRYPT('{$this->input->post('supplier_mobile', TRUE)}', '{$encryption_key}'),
                 AES_ENCRYPT('{$this->input->post('supplier_email', TRUE)}', '{$encryption_key}') ,
                 AES_ENCRYPT('{$this->input->post('email_address', TRUE)}', '{$encryption_key}') ,
                 AES_ENCRYPT('{$this->input->post('contact', TRUE)}', '{$encryption_key}') ,
                 AES_ENCRYPT('{$this->input->post('phone', TRUE)}', '{$encryption_key}'),
                 '{$this->input->post('fax', TRUE)}',
                 '{$this->input->post('city', TRUE)}',
                 '{$this->input->post('state', TRUE)}',
                 '{$this->input->post('zip', TRUE)}',
                 '{$this->input->post('country', TRUE)}',
                 AES_ENCRYPT('{$this->input->post('supplier_address', TRUE)}', '{$encryption_key}'),
                 AES_ENCRYPT('{$this->input->post('address2', TRUE)}', '{$encryption_key}'),
                 '{$this->input->post('status', TRUE)}'
                );";

                // $this->db->query($query);
                if ($this->supplier_model->create($query)) {
                    #set success message

                    $info['msg']    = display('save_successfully');
                    $info['status'] = 1;
                } else {
                    #set exception message

                    $info['msg']    = display('please_try_again');
                    $info['status'] = 0;
                }
            } else {
                $query = "
    UPDATE supplier_information 
    SET 
        supplier_name = AES_ENCRYPT('{$this->input->post('supplier_name', TRUE)}', '{$encryption_key}'),
        mobile = AES_ENCRYPT('{$this->input->post('supplier_mobile', TRUE)}', '{$encryption_key}'),
        emailnumber = AES_ENCRYPT('{$this->input->post('supplier_email', TRUE)}', '{$encryption_key}'),
        email_address = AES_ENCRYPT('{$this->input->post('email_address', TRUE)}', '{$encryption_key}'),
        contact = AES_ENCRYPT('{$this->input->post('contact', TRUE)}', '{$encryption_key}'),
        phone = AES_ENCRYPT('{$this->input->post('phone', TRUE)}', '{$encryption_key}'),
        fax = '{$this->input->post('fax', TRUE)}',
        city = '{$this->input->post('city', TRUE)}',
        state = '{$this->input->post('state', TRUE)}',
        zip = '{$this->input->post('zip', TRUE)}',
        country = '{$this->input->post('country', TRUE)}',
        address = AES_ENCRYPT('{$this->input->post('supplier_address', TRUE)}', '{$encryption_key}'),
        address2 = AES_ENCRYPT('{$this->input->post('address2', TRUE)}', '{$encryption_key}'),
        status = '{$this->input->post('status', TRUE)}'
    WHERE 
        supplier_id = '{$this->input->post('supplier_id', TRUE)}';
";

                $this->db->query($query);
                if ($this->supplier_model->update($query)) {
                    #set success message
                    $info['msg']    = display('update_successfully');
                    $info['status'] = 1;
                } else {
                    #set exception message
                    $info['msg']    = display('please_try_again');
                    $info['status'] = 0;
                }
            }

            echo json_encode($info);
        } else {
            if (empty($this->input->post('supplier_name', true))) {
                if (!empty($id)) {
                    $data['title']    = display('edit_supplier');
                    $data['supplier'] = $this->supplier_model->singledata($id);
                }
                $data['module']   = "supplier";
                $data['page']     = "form";
                echo Modules::run('template/layout', $data);
            } else {

                $info['msg']    = validation_errors();
                $info['status'] = 0;
                echo json_encode($info);
            }
        }
    }



    public function bdtask_delete($id)
    {
        $base_url = base_url();

        if ($this->supplier_model->delete($id)) {
            echo '<script type="text/javascript">
            alert("Supplier Details Deleted successfully");
            window.location.href = "' . $base_url . 'supplier_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this supplier beacause it is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'supplier_list";
           </script>';
        }
    }

    public function supplier_search($id)
    {
        $data["suppliers"] = $this->supplier_model->individual_info($id);
        $this->load->view('supplier_search', $data);
    }

    public function bdtask_supplier_ledger()
    {
        $data['title']    = display('supplier_ledger');
        #-------------------------------#       
        #
        #pagination starts
        #
        $config["base_url"]    = base_url('supplier_ledger');
        $config["total_rows"]  = $this->supplier_model->count_supplier_ledger();
        $config["per_page"]    = 10;
        $config["uri_segment"] = 2;
        $config["last_link"]   = "Last";
        $config["first_link"]  = "First";
        $config['next_link']   = 'Next';
        $config['prev_link']   = 'Prev';
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["ledgers"]  = $this->supplier_model->supplier_ledgerdata($config["per_page"], $page);
        $data["links"]    = $this->pagination->create_links();
        $data['supplier'] = $this->supplier_model->supplier_list_ledger();
        $data['supplier_name'] = '';
        $data['supplier_id'] = '';
        $data['address']  = '';
        $data['module']   = "supplier";
        $data['page']     = "supplier_ledger";
        echo Modules::run('template/layout', $data);
    }

    public function bdtask_supplier_ledgerData()
    {
        $start           = $this->input->post('from_date', true);
        $end             = $this->input->post('to_date', true);
        $supplier_id     = $this->input->post('supplier_id', true);
        $supplier_detail = $this->supplier_model->supplier_personal_data($supplier_id);
        $data['title']   = display('supplier_ledger');
        $data['supplier'] = $this->supplier_model->supplier_list_ledger();
        $data["ledgers"] = $this->supplier_model->supplierledger_searchdata($supplier_id, $start, $end);
        $data['supplier_name'] = $supplier_detail[0]['supplier_name'];
        $data['supplier_id'] = $supplier_id;
        $data['address']  = $supplier_detail[0]['address'];
        $data['module']   = "supplier";
        $data["links"]    = '';
        $data['page']     = "supplier_ledger";
        echo Modules::run('template/layout', $data);
    }


    public function bdtask_supplier_advance()
    {
        $data['title'] = display('supplier_advance');
        $data['supplier_list'] = $this->supplier_model->supplier_list_advance();
        $data['module'] = "supplier";
        $data['page']  = "supplier_advance";
        echo Modules::run('template/layout', $data);
    }

    public function insert_supplier_advance()
    {
        $advance_type = $this->input->post('type', TRUE);
        if ($advance_type == 1) {
            $dr = $this->input->post('amount', TRUE);
            $tp = 'd';
        } else {
            $cr = $this->input->post('amount', TRUE);
            $tp = 'c';
        }
        $createby = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        $transaction_id = $this->supplier_model->generator(10);
        $supplier_id = $this->input->post('supplier_id', TRUE);
        $supplierinfo = $this->db->select('*')->from('supplier_information')->where('supplier_id', $supplier_id)->get()->row();
        $headn = $supplier_id . '-' . $supplierinfo->supplier_name;
        $coainfo = $this->db->select('*')->from('acc_coa')->where('supplier_id', $supplier_id)->get()->row();
        $supplier_headcode = $coainfo->HeadCode;

        $supplier_accledger = array(
            'VNo'            =>  $transaction_id,
            'Vtype'          =>  'Advance',
            'VDate'          =>  date("Y-m-d"),
            'COAID'          =>  $supplier_headcode,
            'Narration'      =>  'supplier Advance For  ' . $supplierinfo->supplier_name,
            'Debit'          => (!empty($dr) ? $dr : 0),
            'Credit'         => (!empty($cr) ? $cr : 0),
            'IsPosted'       => 1,
            'CreateBy'       => $this->session->userdata('id'),
            'CreateDate'     => date('Y-m-d H:i:s'),
            'IsAppove'       => 1
        );
        $cc = array(
            'VNo'            =>  $transaction_id,
            'Vtype'          =>  'Advance',
            'VDate'          =>  date("Y-m-d"),
            'COAID'          =>  111000001,
            'Narration'      =>  'Cash in Hand  For ' . $supplierinfo->supplier_name . ' Advance',
            'Debit'          => (!empty($dr) ? $dr : 0),
            'Credit'         => (!empty($cr) ? $cr : 0),
            'IsPosted'       =>  1,
            'CreateBy'       =>  $this->session->userdata('id'),
            'CreateDate'     =>  date('Y-m-d H:i:s'),
            'IsAppove'       =>  1
        );

        $this->db->insert('acc_transaction', $supplier_accledger);
        $this->db->insert('acc_transaction', $cc);
        redirect(base_url('supplier_advance_receipt/' . $transaction_id . '/' . $supplier_id));
    }

    //supplier_advance_receipt
    public function supplier_advancercpt($receiptid = null, $supplier_id = null)
    {
        $data['title']         = display('advance_receipt');
        $supplier_id           = $this->uri->segment(3);
        $receiptdata           = $this->supplier_model->advance_details($receiptid, $supplier_id);
        $supplier_details      = $this->supplier_model->supplier_personal_data($supplier_id);
        $data['details']       = $receiptdata;
        $data['supplier_name'] = $supplier_details[0]['supplier_name'];
        $data['receipt_no']    = $receiptdata[0]['VNo'];
        $data['address']       = $supplier_details[0]['address'];
        $data['mobile']        = $supplier_details[0]['mobile'];
        $data['module']        = "supplier";
        $data['page']          = "supplier_advance_receipt";
        echo Modules::run('template/layout', $data);
    }

    public function bdtask_supplier_ledgerinfo($supplier_id)
    {
        $supplier_details = $this->supplier_model->supplier_personal_data($supplier_id);
        $supplier         = $this->supplier_model->supplier_list_advance();
        $ledgers          = $this->supplier_model->supplier_product_sale_info($supplier_id);

        $data = array(
            'title'           => display('supplier_ledger'),
            'ledgers'         => $ledgers,
            'supplier_id'     => $supplier_id,
            'supplier_name'   => $supplier_details[0]['supplier_name'],
            'address'         => $supplier_details[0]['address'],
            'supplier'        => $supplier,
            'links'           => '',
        );

        $data['module']    = "supplier";
        $data['page']      = "supplier_ledger";
        echo Modules::run('template/layout', $data);
    }
}
