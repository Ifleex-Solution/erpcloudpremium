<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts extends MX_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->db->query('SET SESSION sql_mode = ""');
    $this->load->model(array(
      'accounts_model'
    ));
    ini_set("memory_limit", "1024M");
    $this->load->library('form_validation');
    if (!$this->session->userdata('isLogIn'))
      redirect('login');
  }




  // payment type add
  public function payment_type()
  {
    $data['title']  = display('payment_type');
    $data['paytype'] = $this->accounts_model->paytype();
    $data['module'] = "account";
    $data['page']   = "payment_type";
    echo Modules::run('template/layout', $data);
  }



  public function add_paymenttype()
  {
    $parent_type = $this->input->post('parent_type', true);
    if (empty($parent_type)) {
      $coa = $this->accounts_model->paymentparentcode();
    } else {
      $coa = $this->accounts_model->paymenchildcode();
    }
    if ($coa->HeadCode != NULL) {
      $headcode = $coa->HeadCode + 1;
    } else {
      if (empty($parent_type)) {
        $headcode = "102010001";
      } else {
        $headcode = "10201020001";
      }
    }

    $headname = $this->input->post('typename', true);
    $createby = $this->session->userdata('fullname');
    $createdate = date('Y-m-d H:i:s');
    $data['aco']  = (object) $coaData = [
      'HeadCode'         => $headcode,
      'HeadName'         => $headname,
      'PHeadName'        => (!empty($parent_type) ? $parent_type : 'Cash & Cash Equivalent'),
      'HeadLevel'        => (!empty($parent_type) ? 4 : 3),
      'IsActive'         => '1',
      'IsTransaction'    => '1',
      'IsGL'             => '0',
      'HeadType'         => 'A',
      'IsBudget'         => '0',
      'IsDepreciation'   => '0',
      'DepreciationRate' => '0',
      'CreateBy'         => $createby,
      'CreateDate'       => $createdate,
    ];

    $createid = $this->accounts_model->create_coa($coaData);
    addActivityLog("coa_account", "create", $createid, "acc_coa", 1, $coaData);

    $this->session->set_flashdata('message', display('save_successfully'));

    redirect('account/accounts/payment_type');
  }
  // tree view controller
  public function show_tree($id = null)
  {

    $data['title'] = display('c_o_a');
    $id      = ($id ? $id : 2);
    $data['userList'] = $this->accounts_model->get_accountList();
    $data['userID']   = set_value('userID');
    $data['module'] = "account";

    $data['id'] = $id;
    $data['page']   = "treeview";
    echo Modules::run('template/layout', $data);
  }


  public function selectedform($id)
  {
    $role_reult = $this->accounts_model->treeview_selectform($id);
    $baseurl = base_url() . 'account/accounts/insert_coa';
    $html = "";
    if ($role_reult) {
      $html .= "
           <form name=\"coaform\" id=\"coaform\" action=\"#\" method=\"post\" enctype=\"multipart/form-data\" onSubmit=\" return validate('nameLabel');\">
            <input type=\"hidden\" name=\"txtPHeadCode\" id=\"txtPHeadCode\"  value=\"" . $role_reult->PHeadCode . "\"/>
            <input type=\"hidden\" name=\"cnodeelem\" id=\"cnodeelem\"  value=\"\"/>
            <input type=\"hidden\" name=\"clevel\" id=\"clevel\"  value=\"\"/>                
            <table class=\"coaTable\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
            <input type=\"hidden\" name=\"csrf_test_name\" id=\"CSRF_TOKEN\" value=\"" . $this->security->get_csrf_hash() . "\"/>
            <input type=\"hidden\" name=\"txtHeadLevel\" id=\"txtHeadLevel\" class=\"form_input\"  value=\"" . $role_reult->HeadLevel . "\"/>
            <input type=\"hidden\" name=\"txtHeadType\" id=\"txtHeadType\" class=\"form_input\"  value=\"" . $role_reult->HeadType . "\"/>    
            <tr>
              <td>Head Code</td>
              <td><input type=\"text\" name=\"txtHeadCode\" id=\"txtHeadCode\" class=\"form_input\"  value=\"" . $role_reult->HeadCode . "\" readonly=\"readonly\"/></td>
            </tr>
            <tr>
              <td>Head Name</td>
              <td><input type=\"text\" name=\"txtHeadName\" id=\"txtHeadName\" class=\"form_input\" value=\"" . $role_reult->HeadName . "\"  onkeyUp=\"checkNameField('txtHeadName','nameLabel')\"/>
              <input type=\"hidden\" name=\"HeadName\" id=\"HeadName\" class=\"form_input\" value=\"" . $role_reult->HeadName . "\"/><label id=\"nameLabel\" class=\"errore\"></label>
              </td>
            </tr>
            <tr>
            <td>Parent Head</td>
            <td><input type=\"text\" name=\"txtPHead\" id=\"txtPHead\" class=\"form_input\" readonly=\"readonly\" value=\"" . $role_reult->PHeadName . "\"/></td>
          </tr>";
      if ($role_reult->HeadLevel > 3) {
        $html .= "<tr>
              <td>Note No</td>
              <td><input type=\"text\" name=\"noteNo\" id=\"noteNo\" class=\"form_input\"  value=\"" . $role_reult->noteNo . "\"/></td>
             </tr>";
      }

      $html .= "<tr>
           <td>&nbsp;</td>
           <td id=\"innerCheck\">";
      $html .= "<input type=\"checkbox\" value=\"1\" name=\"IsActive\" id=\"IsActive\" size=\"28\"";
      if ($role_reult->IsActive == 1) {
        $html .= "checked";
      }
      $html .= "/><label for=\"IsActive\">&nbsp;Is Active</label> &nbsp;&nbsp; ";

      if ($role_reult->HeadLevel > 3 && ($role_reult->HeadType == "A" || $role_reult->HeadType == "L")) {
        $html .= "<input type=\"checkbox\" name=\"isFixedAssetSch\" value=\"1\" id=\"isFixedAssetSch\" size=\"28\"  onchange=\"isFixedAssetSch_change('isFixedAssetSch','" . $role_reult->HeadType . "')\"";
        if ($role_reult->isFixedAssetSch == 1) {
          $html .= "checked";
        }
        $html .= "/><label for=\"isFixedAssetSch\">&nbsp;Is Fixed Asset</label> &nbsp;&nbsp; ";
      }
      if ($role_reult->HeadLevel > 3) {
        if ($role_reult->HeadType == "A") {
          $html .= "<input type=\"checkbox\" name=\"isStock\" value=\"1\" id=\isStock\" size=\"28\"  onchange=\"isStock_change()\"";
          if ($role_reult->isStock == 1) {
            $html .= "checked";
          }
          $html .= "/><label for=\isStock\">&nbsp;Is Stock</label> &nbsp;&nbsp; ";
          $html .= "<br/><input type=\"checkbox\" name=\"isCashNature\" value=\"1\" id=\"isCashNature\" size=\"28\"  onchange=\"isCashNature_change()\"";
          if ($role_reult->isCashNature == 1) {
            $html .= "checked";
          }
          $html .= "/><label for=\"isCashNature\">&nbsp;Is Cash Nature</label> &nbsp;&nbsp; ";

          $html .= "<input type=\"checkbox\" name=\"isBankNature\" value=\"1\" id=\"isBankNature\" size=\"28\"  onchange=\"isBankNature_change()\"";
          if ($role_reult->isBankNature == 1) {
            $html .= "checked";
          }
          $html .= "/><label for=\"isBankNature\">&nbsp;Is Bank Nature</label> &nbsp;&nbsp; ";
        }
        $html .= "<input type=\"checkbox\" name=\"isSubType\" value=\"1\" id=\"isSubType\" size=\"28\"  onchange=\"isSubType_change('isSubType')\"";
        if ($role_reult->isSubType == 1) {
          $html .= "checked";
        }
        $html .= "/><label for=\"isSubType\">&nbsp;Is Sub Type</label> &nbsp;&nbsp; ";
      }
      $html .= "</tr>";
      if ($role_reult->isFixedAssetSch == 1) {
        if ($role_reult->HeadLevel > 3 && $role_reult->HeadType == "A") {
          $html .= "<tr id=\"fixedassetCode\">";
          $html .= "<td>Fixed Asset Code</td><td><input type=\"text\" name=\"assetCode\" id=\"assetCode\" class=\"form_input\" value=\"" . $role_reult->assetCode . "\"/></td>";
          $html .= "</tr>";
          $html .= "<tr id=\"fixedassetRate\">";
          $html .= "<td>Depreciation Rate % </td><td><input type=\"text\" name=\"DepreciationRate\" id=\"DepreciationRate\" class=\"form_input\" value=\"" . $role_reult->DepreciationRate . "\"/></td>";
          $html .= "</tr>";
        } else if ($role_reult->HeadLevel > 3 &&  $role_reult->HeadType == "L") {
          $html .= "<tr id=\"depreciationCode\"> <td>Depraciation Code</td><td><input type=\"text\" name=\"depCode\" id=\"depCode\" class=\"form_input\" value=\"" . $role_reult->depCode . "\"/></td></tr>";
        }
      } else {
        $html .= "<tr id=\"fixedassetCode\"> </tr>";
        $html .= "<tr id=\"depreciationCode\"> </tr>";
      }
      if ($role_reult->isSubType == 1) {
        $html .= "<tr id=\"subtypeContent\">";
        $subdata = $this->accounts_model->getsubTypeData();
        if ($subdata) {
          $html .= "<td>Subtype</td>
                    <td><select  name=\"subtype\" id=\"subtype\" style=\"width: 90%;\" class=\"form-control\" >";
          foreach ($subdata as $sub) {
            $scheck = $sub->id == $role_reult->subType ? 'selected' : '';
            $html .= "<option value=\"" . $sub->id . "\" " . $scheck . " >" . $sub->subtypeName . "</option>";
          }
          $html .= "</select><br/></td>";
        }
        $html .= "</tr>";
      } else {
        $html .= "<tr id=\"subtypeContent\"> </tr>";
      }
      $html .= "<tr> <td>&nbsp;</td><td>";

      if ($this->permission1->method('show_tree', 'read')->access()) :
        if ($role_reult->HeadLevel >= 2 &&  $role_reult->HeadLevel <= 3) :
          $html .= "<input type=\"button\" name=\"btnNew\" id=\"btnNew\" class=\"btn btn-success\"  value=\"New\" onClick=\"newdata(" . $role_reult->HeadCode . ")\" />
               <input type=\"submit\" name=\"btnSave\" id=\"btnSave\" class=\"btn btn-success\"  value=\"Save\" disabled=\"disabled\"/>&nbsp;&nbsp;";
        endif;
      endif;

      if ($this->permission1->method('show_tree', 'update')->access()) :
        if ($role_reult->HeadLevel >= 2 &&  $role_reult->HeadLevel <= 4) :
          $html .= " <input type=\"submit\" name=\"btnUpdate\" id=\"btnUpdate\" value=\"Update\" class=\"btn btn-success\" /> &nbsp;&nbsp;";
        endif;
      endif;

      if ($this->permission1->method('show_tree', 'delete')->access()) :
        if ($role_reult->HeadLevel >= 2 &&  $role_reult->HeadLevel <= 4) :
          $html .= "<input type=\"button\" name=\"btnDelete\" id=\"btnDelete\" class=\"btn btn-success\"  value=\"Delete\" onClick=\"delDataAcc(" . $role_reult->HeadCode . ")\" /> ";
        endif;
      endif;


      if ($role_reult->HeadLevel >= 2) :
        $html .= "&nbsp;&nbsp; <input type=\"button\" name=\"btnUndo\" id=\"btnUndo\" class=\"btn btn-success\"  value=\"Undo\" onClick=\"loadData('" . $role_reult->HeadCode . "_anchor','" . $role_reult->HeadCode . "')\" />";

      endif;

      $html .= "</td></tr><td> &nbsp; </td> <td> <div id=\"successResult\"> </div>";
      $html .= "</td></tr></table></form> ";
    }
    echo json_encode($html);
  }

  public function newform($id)
  {



    $newdata = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadCode', $id)
      ->get()
      ->row();


    $newidsinfo = $this->db->select_max('HeadCode')
      ->from('acc_coa')
      ->where('pheadcode', $newdata->HeadCode)
      ->get()
      ->row();

    $nid  = $newidsinfo->HeadCode;
    if ($nid > 0) {
      $HeadCode = $nid + 1;
    } else {
      $n = $nid + 1;
      if ($n / 10 < 1)
        $HeadCode = $id . "0" . $n;
      else
        $HeadCode = $id . $n;
    }

    $info['headcode']  =  $HeadCode;
    $info['rowdata']   =  $newdata;
    $info['headlabel'] =  $newdata->HeadLevel + 1;
    echo json_encode($info);
  }

  public function deleteaccount($id)
  {
    $isDel = true;
    $checkTransation =  $this->accounts_model->check_istransation_account($id);
    $checkSub = $this->accounts_model->check_child_account($id);
    if ($checkSub) {
      foreach ($checkSub as $sub) {
        if ($sub->HeadLevel == 4) {
          $chtran =  $this->accounts_model->check_istransation_account($sub->HeadCode);
          if (!$chtran) {
            $isDel = false;
          }
        } else {
          $checkSub = $this->accounts_model->check_child_account($sub->HeadCode);
          if ($checkSub) {
            foreach ($checkSub as $sub) {
              if ($sub->HeadLevel == 4) {
                $chtran =  $this->accounts_model->check_istransation_account($sub->HeadCode);
                if (!$chtran) {
                  $isDel = false;
                }
              }
            }
          }
        }
      }
    }
    if (!$checkTransation) {
      $isDel = false;
    }
    if ($isDel) {
      $this->db->where('HeadCode', $id);
      $deldata = $this->db->delete('acc_coa');

      $this->db->where('Pheadcode', $id);
      $deldatap = $this->db->delete('acc_coa');
    } else {
      $deldata  = false;
      $deldatap  = false;
    }

    if ($deldata || $deldatap) {
      $info['status'] =  'success';
    } else {
      $info['status'] =  'faild';
    }
    echo json_encode($info);
  }
  public function insert_coa()
  {
    $this->form_validation->set_rules('txtHeadName', display('HeadName'), 'required|max_length[100]');
    if ($this->form_validation->run() === false) {
      $this->session->set_flashdata('exception',  display('please_enter_HeadName'));
      redirect($_SERVER['HTTP_REFERER']);
    } else {

      $headcode    = $this->input->post('txtHeadCode', true);
      $pheadcode   = $this->input->post('txtPHeadCode', true);
      $HeadName    = $this->input->post('txtHeadName', true);
      $PHeadName   = $this->input->post('txtPHead', true);
      $HeadLevel   = $this->input->post('txtHeadLevel', true);
      $txtHeadType = $this->input->post('txtHeadType', true);
      $isact       = $this->input->post('IsActive', true);
      $IsActive    = (!empty($isact) ? $isact : 0);
      $stock       = $this->input->post('isStock', true);
      $isStock     = (!empty($stock) ? $stock : 0);
      $cashnature  = $this->input->post('isCashNature', true);
      $isCashNature     = (!empty($cashnature) ? $cashnature : 0);
      $banknature  = $this->input->post('isBankNature', true);
      $isBankNature     = (!empty($banknature) ? $banknature : 0);
      $fixedassets  = $this->input->post('isFixedAssetSch', true);
      $isFixedAssetSch     = (!empty($fixedassets) ? $fixedassets : 0);
      $isstype     = $this->input->post('isSubType', true);
      $isSubType   = (!empty($isstype) ? $isstype : 0);
      $createby    = $this->session->userdata('id');
      $createdate = date('Y-m-d H:i:s');
      if ($isFixedAssetSch == 1) {
        if ($txtHeadType == 'A') {
          $assetCode   = $this->input->post('assetCode');
          $DepreciationRate   = $this->input->post('DepreciationRate');
          $depCode   = null;
        } else {
          $depCode   = $this->input->post('depCode');
          $DepreciationRate = 0;
          $assetCode   = null;
        }
      } else {
        $assetCode   = null;
        $depCode   = null;
        $DepreciationRate = 0;
      }
      if ($isSubType == 1) {
        $subtype   = $this->input->post('subtype', true);
      } else {
        $subtype   = 1;
      }
      $noteNo   = (!empty($this->input->post('noteNo')) ? $this->input->post('noteNo') : null);
      $postData = array(
        'HeadCode'         =>  $headcode,
        'Pheadcode'        =>  $pheadcode,
        'HeadName'         =>  $HeadName,
        'PHeadName'        =>  $PHeadName,
        'HeadLevel'        =>  $HeadLevel,
        'IsActive'         =>  $IsActive,
        'isStock'          =>  $isStock,
        'isSubType'        =>  $isSubType,
        'DepreciationRate' =>  $DepreciationRate,
        'HeadType'         => $txtHeadType,
        'IsBudget'         => 0,
        'isCashNature'     => $isCashNature,
        'isBankNature'     => $isBankNature,
        'isFixedAssetSch'  => $isFixedAssetSch,
        'assetCode'        => $assetCode,
        'depCode'          => $depCode,
        'subType'          => $subtype,
        'noteNo'           => $noteNo,
        'CreateBy'         => $createby,
        'CreateDate'       => $createdate,
      );


      $upinfo = $this->db->select('*')
        ->from('acc_coa')
        ->where('HeadCode', $headcode)
        ->get()
        ->row();
      if (empty($upinfo)) {
        $insdata = $this->db->insert('acc_coa', $postData);
        addActivityLog("coa_account", "create", $this->db->insert_id(), "acc_coa", 1, $postData);
        if ($insdata) {
          $this->session->set_flashdata('message', display('message_coa_create'));
          echo json_encode(array('info' => $postData, 'message' => 'successfully saved', 'type' => 'new'));
        }
      } else {

        $hname = $this->input->post('HeadName', true);
        $updata = array(
          'HeadName'      =>  $HeadName,
        );


        $this->db->where('HeadCode', $headcode)
          ->update('acc_coa', $postData);
        addActivityLog("coa_account", "update", $headcode, "acc_coa", 2, $postData);

        $this->session->set_flashdata('message', display('message_coa_update'));
        echo json_encode(array('message' => 'updated successfully', 'type' => 'update'));
      }
    }
  }

  //get subtype data foe charter account
  public function getsubtype($id = null)
  {
    $subdata = $this->accounts_model->getsubTypeData($id);
    $html = "";
    if ($subdata) {
      $html .= "<td>Subtype</td>
          <td><select  name=\"subtype\" id=\"subtype\" style=\"width: 90%;\" class=\" form-control\" >";
      foreach ($subdata as $sub) {
        $html .= "<option value=\"" . $sub->id . "\">" . $sub->subtypeName . "</option>";
      }
      $html .= "</select><br/></td>";
    }
    echo json_encode($html);
  }

  // opening balance form 
  public function add_opening_balance()
  {
    $data['title']      = display('opening_balance');
    $data['acc']        = $this->accounts_model->AssetLiabilities();
    $data['oldyears'] = $this->accounts_model->get_old_financialyear();
    $data['crcc']       = $this->accounts_model->Cracc();
    $data['module']     = "account";
    $data['page']       = "opening_balance";
    echo Modules::run('template/layout', $data);
  }


  // opening balance form 
  public function edit_opening_balance($id)
  {

    $this->permission1->method('opening_balance', 'update')->access();
    $data['title']      = display('opening_balance');
    $data['acc']        = $this->accounts_model->AssetLiabilities();
    $data['oldyears'] = $this->accounts_model->get_old_financialyear();
    $data['openingBalance']       = $this->accounts_model->getopeningBalanceById($id);
    $data['module']     = "account";
    $data['page']       = "edit_opening_balance";
    echo Modules::run('template/layout', $data);
  }



  //Create opening balance
  public function create_opening_balance()
  {

    $this->form_validation->set_rules('fyear', display('financial_year'), 'max_length[100]');
    $this->form_validation->set_rules('dtpDate', display('date'), 'max_length[200]');
    if ($this->form_validation->run()) {
      if ($this->accounts_model->insert_openingBalance()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('opening_balance');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("opening_balance");
    } else {
      $this->session->set_flashdata('exception',  display('please_try_again'));
      redirect("opening_balance");
    }
  }

  //update opening balance
  public function update_opening_balance()
  {
    $this->form_validation->set_rules('fyear', display('financial_year'), 'max_length[100]');
    $this->form_validation->set_rules('dtpDate', display('date'), 'max_length[200]');
    if ($this->form_validation->run()) {
      if ($this->accounts_model->update_openingBalance()) {
        $this->session->set_flashdata('message', display('update_successfully'));
        redirect('opening_balance');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("opening_balance");
    } else {
      $this->session->set_flashdata('exception',  display('please_try_again'));
      redirect("opening_balance");
    }
  }


  //delete opening balance
  public function delete_opening_balance($id)
  {

    $this->permission1->method('opening_balance', 'read')->access();
    $openinfbalance = $this->accounts_model->getopeningBalanceById($id);
    $openingbalancetdelete = array(
      'id'             =>  $openinfbalance->id,
      'fyear'          =>  $openinfbalance->fyear,
      'COAID'          =>  $openinfbalance->COAID,
      'subType'        =>  $openinfbalance->subType,
      'subCode'        =>  $openinfbalance->subCode,
      'openDate'       =>  $openinfbalance->openDate,
      'Debit'          =>  $openinfbalance->Debit,
      'Credit'         =>  $openinfbalance->Credit,
      'deleteBy'       =>  $this->session->userdata('id'),
      'deleteDate'     =>  date('Y-m-d h:i:s'),
    );
    if ($this->accounts_model->delete_openingBalance($id)) {
      addActivityLog("opening_balance", "delete", $id, "acc_opening_balance", 3, $openingbalancetdelete);
      $this->session->set_flashdata('message', display('delete_successfully'));
      redirect('opening_balance');
    } else {
      $this->session->set_flashdata('exception',  display('please_try_again'));
    }
    redirect("opening_balance");
  }


  // opening balance list 
  public function opening_balance()
  {

    $data['title']      = display('opening_balance');
    #-------------------------------#       
    #
    #pagination starts
    #
    $config["base_url"] = base_url('account/accounts/opening_balance/');
    $config["total_rows"]  = $this->accounts_model->count_opening_balance();
    $config["per_page"]    = 25;
    $config["uri_segment"] = 4;
    $config["last_link"] = "Last";
    $config["first_link"] = "First";
    $config['next_link'] = 'Next';
    $config['prev_link'] = 'Prev';
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
    $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
    $data["openingBalances"] = $this->accounts_model->opening_balance($config["per_page"], $page);

    $data["links"] = $this->pagination->create_links();
    #
    #pagination ends
    #   
    $data['module']     = "account";
    $data['page']   = "opening_balance_list";
    echo Modules::run('template/layout', $data);
  }

  // Subaccount year list 
  public function subaccount_list()
  {

    $this->form_validation->set_rules('subtype', display('subtype'), 'required');
    $this->form_validation->set_rules('account_name', display('account_name'), 'required');


    if ($this->form_validation->run() === true) {
      $postData = [


        'subTypeId'      => $this->input->post('subtype', true),
        'name'           => $this->input->post('account_name', true),
        'referenceNo'    => 0,
        'status'         => 1,
        'created_by'     => $this->session->userdata('id'),
        'created_date'   => date('Y-m-d h:i:s')


      ];


      if ($this->accounts_model->subaccount_create($postData) != null) {

        $this->session->set_flashdata('message', display('successfully_created'));
      } else {
      }
      redirect("subaccount_list");
    } else {

      $data['title']      = display('sub_account');
      $data["subAccounts"] = $this->accounts_model->get_subaccount();
      $data["subTypes"] = $this->accounts_model->getsubTypeData();

      $data['module']     = "account";
      $data['page']   = "subaccount_list";
      echo Modules::run('template/layout', $data);
    }
  }


  //get financial year if exists for edit
  public function edit_sub_account($id = null)
  {

    $exist = $this->accounts_model->get_subaccount($id);
    echo json_encode($exist);
  }

  // Update financial year 


  public function update_sub_account()
  {
    $data['title']      = display('sub_account');

    $this->form_validation->set_rules('subtype', display('subtype'), 'required');
    $this->form_validation->set_rules('account_name', display('account_name'), 'required');

    $postData = [

      'id'             => $this->input->post('rid', true),
      'subTypeId'      => $this->input->post('subtype', true),
      'name'           => $this->input->post('account_name', true),
      'updated_by'     => $this->session->userdata('id'),
      'updated_date'   => date('Y-m-d h:i:s')

    ];
    #-------------------------------#
    if ($this->form_validation->run() === true) {

      if ($this->accounts_model->subaccount_update($postData)) {
        addActivityLog("sub_account", "update", $postData['id'], "acc_subcode", 2, $postData);
        $this->session->set_flashdata('message', display('save_successfully'));
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("subaccount_list");
    } else {

      $data["subAccounts"] = $this->accounts_model->get_subaccount();
      $data["subTypes"] = $this->accounts_model->getsubTypeData();

      $data['module']     = "account";
      $data['page']   = "subaccount_list";
      echo Modules::run('template/layout', $data);
    }
  }



  //delete financial year
  public function delete_sub_account($id)
  {

    $exist = $this->accounts_model->get_subaccount($id);

    $delete_subaccount = array(
      'id'             => $exist->id,
      'subTypeId'      => $exist->subTypeId,
      'name'           => $exist->name,
      'deleteBy'       =>  $this->session->userdata('id'),
      'deleteDate'     =>  date('Y-m-d h:i:s')
    );
    $deldata = $this->accounts_model->delete_sub_account($id);
    if ($deldata) {
      addActivityLog("sub_account", "delete", $id, "acc_subcode", 3, $delete_subaccount);
      redirect("subaccount_list");
    }
  }


  // financial year list 
  public function financial_year()
  {

    $this->permission1->method('financiall_year', 'read')->access();
    $this->form_validation->set_rules('financial_year', display('financial_year'), 'required');
    $this->form_validation->set_rules('financial_year_start_date', display('financial_year_start_date'), 'required');
    $this->form_validation->set_rules('financial_year_end_date', display('financial_year_end_date'), 'max_length[50]|required');

    if ($this->form_validation->run() === true) {

      $start_date = $this->input->post('financial_year_start_date', true);
      $end_date   = $this->input->post('financial_year_end_date', true);

      $initial    = $this->db->select('*')->from('financial_year')->get()->row();
      $chkstart   = $this->db->select('*')->from('financial_year')->where('startDate<=', $start_date)->where('endDate>=', $start_date)->get()->num_rows();
      $chkend     = $this->db->select('*')->from('financial_year')->where('startDate<=', $end_date)->where('endDate>=', $end_date)->get()->num_rows();
      $chkbtn     = $this->db->select('*')->from('financial_year')->where('startDate>=', $start_date)->where('endDate<=', $end_date)->get()->num_rows();

      if ($start_date >= $end_date) {

        $this->session->set_flashdata('exception',  "Invalid Date, End date must be greater than Start date");
        redirect($_SERVER['HTTP_REFERER']);
      } else if ($chkstart > 0 || $chkend > 0 || $chkbtn > 0) {

        $this->session->set_flashdata('exception',  "Invalid Date, Start date or End date fall into an old Financial Year");
        redirect($_SERVER['HTTP_REFERER']);
      } else {

        $postData = [

          'yearName'     => $this->input->post('financial_year', true),
          'startDate'    => $this->input->post('financial_year_start_date', true),
          'endDate'      => $this->input->post('financial_year_end_date', true),
          'status'       => $this->input->post('status', true),
          'created_by'   => $this->session->userdata('id'),
          'created_date' => date('Y-m-d h:i:s')

        ];

        $fid = $this->accounts_model->financial_year_create($postData);

        if ($fid) {

          $this->session->set_userdata('fyear', $fid);
          $this->session->set_userdata('fyearName', $this->input->post('financial_year', true));
          $this->session->set_userdata('fyearStartDate', $this->input->post('financial_year_start_date', true));
          $this->session->set_userdata('fyearEndDate', $this->input->post('financial_year_end_date', true));
          $this->session->set_flashdata('message', display('successfully_created'));
        } else {

          $this->session->set_flashdata('exception',  display('please_try_again'));
        }
        redirect("financial_year");
      }
    } else {

      $data['title']      = display('financial_year');
      #-------------------------------#       
      #
      #pagination starts
      #
      $config["base_url"] = base_url('financial_year');
      $config["total_rows"]  = $this->accounts_model->count_financial_year();
      $config["per_page"]    = 25;
      $config["uri_segment"] = 4;
      $config["last_link"] = "Last";
      $config["first_link"] = "First";
      $config['next_link'] = 'Next';
      $config['prev_link'] = 'Prev';
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
      $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
      //$config["per_page"], $page
      $data["financialYear"] = $this->accounts_model->financial_year();
      $data["links"] = $this->pagination->create_links();
      $data["lastclosedid"] = $this->accounts_model->last_closed_financial_year();
      $data['module']     = "account";
      $data['page']   = "financial_year_list";
      echo Modules::run('template/layout', $data);
    }
  }


  //get financial year if exists for edit
  public function edit_financial_year($id = null)
  {
    $this->permission1->method('financiall_year', 'update')->access();

    $exist = $this->db->select('*')
      ->from('financial_year')
      ->where('id', $id)
      ->get()
      ->row();
    echo json_encode($exist);
  }

  // Update financial year 
  public function update_financial_year()
  {
    $data['title']      = display('financial_year');
    #-------------------------------#
    $this->permission1->method('financiall_year', 'read')->access();

    $this->form_validation->set_rules('financial_year', display('financial_year'), 'required');
    $this->form_validation->set_rules('financial_year_start_date', display('financial_year_start_date'), 'required');
    $this->form_validation->set_rules('financial_year_end_date', display('financial_year_end_date'), 'max_length[50]|required');
    $postData = [
      'id'     => $this->input->post('rid', true),
      'yearName'     => $this->input->post('financial_year', true),
      'startDate'    => $this->input->post('financial_year_start_date', true),
      'endDate'      => $this->input->post('financial_year_end_date', true),
      'status'       => $this->input->post('status', true),
      'updated_by'   => $this->session->userdata('id'),
      'updated_date' => date('Y-m-d h:i:s')

    ];
    #-------------------------------#
    if ($this->form_validation->run() === true) {
      if ($this->accounts_model->update_financial_year($postData)) {
        addActivityLog("financial_year", "update", $this->db->insert_id(), "financial_year", 2, $postData);
        $this->session->set_flashdata('message', display('save_successfully'));
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("financial_year");
    } else {
      $data["financialYear"] = $this->accounts_model->financial_year();
      $data['module']     = "account";
      $data['page']   = "financial_year_list";
      echo Modules::run('template/layout', $data);
    }
  }



  //delete financial year
  public function delete_financial_year($id)
  {
    $this->permission1->method('financiall_year', 'update')->access();

    $exist = $this->db->select('*')->from('financial_year')->where('id', $id)->get()->row();

    $delete_fyear = array(
      'id'           => $exist->id,
      'yearName'     => $exist->yearName,
      'startDate'    => $exist->startDate,
      'endDate'      => $exist->endDate,
      'status'       => $exist->status,
      'deleteBy'       =>  $this->session->userdata('id'),
      'deleteDate'     =>  date('Y-m-d h:i:s')
    );


    $deldata = $this->accounts_model->delete_financial_year($id);
    if ($deldata) {
      $current = $this->accounts_model->last_active_financial_year();
      $this->session->set_userdata('fyear', $current->id);
      $this->session->set_userdata('fyearName', $current->yearName);
      $this->session->set_userdata('fyearStartDate',  $current->startDate);
      $this->session->set_userdata('fyearEndDate',  $current->endDate);
      addActivityLog("financial_year", "delete", $id, "financial_year", 3, $delete_fyear);
      redirect("financial_year");
    }
  }





  // cash adjustment
  public function cash_adjustment()
  {
    $data['title']      = display('cash_adjustment');
    $data['voucher_no'] = $this->accounts_model->Cashvoucher();
    $data['module']     = "account";
    $data['page']       = "cash_adjustment";
    echo Modules::run('template/layout', $data);
  }

  //Bank Adjustment
  public function bank_adjustment()
  {
    $data['title']      = display('bank_adjustment');
    $data['voucher_no'] = $this->accounts_model->bankvoucher();
    $data['banks']      = $this->accounts_model->banklist();
    $data['module']     = "account";
    $data['page']       = "bank_adjustment";
    echo Modules::run('template/layout', $data);
  }

  //Create Cash Adjustment
  public function create_cash_adjustment()
  {
    $this->form_validation->set_rules('txtAmount', display('amount'), 'required|max_length[100]');
    $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
    $this->form_validation->set_rules('type', display('adjustment_type'), 'required|max_length[20]');

    if ($this->form_validation->run()) {
      if ($this->accounts_model->insert_cashadjustment()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('cash_adjustment');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("cash_adjustment");
    } else {
      $data['title']      = display('cash_adjustment');
      $data['voucher_no'] = $this->accounts_model->Cashvoucher();
      $data['module']     = "account";
      $data['page']       = "cash_adjustment";
      echo Modules::run('template/layout', $data);
    }
  }


  // Create Bank Adjustment
  public function create_bank_adjustment()
  {
    $this->form_validation->set_rules('txtAmount', display('amount'), 'max_length[100]');
    $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
    $this->form_validation->set_rules('type', display('adjustment_type'), 'required|max_length[20]');
    if ($this->form_validation->run()) {
      if ($this->accounts_model->insert_bankadjustment()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('account/accounts/bank_adjustment/');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("account/accounts/bank_adjustment");
    } else {
      $data['title']      = display('bank_adjustment');
      $data['voucher_no'] = $this->accounts_model->bankvoucher();
      $data['banks']      = $this->accounts_model->banklist();
      $data['module']     = "account";
      $data['page']       = "bank_adjustment";
      echo Modules::run('template/layout', $data);
    }
  }

  // get subcode by subtype
  public function getSubcode($id)
  {
    $htm = '';
    $subcodes = $this->accounts_model->getSubcode($id);

    if ($subcodes) {
      foreach ($subcodes as $sc) {
        $htm .= '<option value="' . $sc->id . '" >' . $sc->name . '</option>';
      }
    }
    echo json_encode($htm);
  }

  // get account head by subtype
  public function getAccountHead($id)
  {
    $htm = '';
    $subcodes = $this->accounts_model->get_account_head_by_subtype($id);
    if ($subcodes) {
      foreach ($subcodes as $sc) {
        $htm .= '<option value="' . $sc->HeadCode . '" >' . $sc->HeadName . '</option>';
      }
    }
    echo json_encode($htm);
  }





  // Opening balance get subtype
  public function getsubtypecode($id)
  {
    $htm = '';
    $debitvcode = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadCode', $id)
      ->get()
      ->row();

    if ($debitvcode->subType != 1) {
      $subcodes = $this->db->select('*')
        ->from('acc_subcode')
        ->where('subTypeId', $debitvcode->subType)
        ->get()->result();

      foreach ($subcodes as $sc) {
        $htm .= '<option value="' . $sc->id . '" >' . $sc->name . '</option>';
      }
    }

    echo json_encode($htm);
  }
  // Opening balance get subtype code
  public function getsubtypbyid($id)
  {
    $htm = '';
    $debitvcode = $this->db->select('subType')
      ->from('acc_coa')
      ->where('HeadCode', $id)
      ->get()
      ->row();
    $data = array('subType' => $debitvcode->subType);
    echo json_encode($data);
  }


  // Debit voucher code select onchange
  public function debtvouchercode($id)
  {

    $debitvcode = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadCode', $id)
      ->get()
      ->row();
    $code = $debitvcode->HeadCode;
    echo json_encode($code);
  }




  //Supplier code 
  public function supplier_headcode($id)
  {
    $supplier_info = $this->db->select('supplier_name')->from('supplier_information')->where('supplier_id', $id)->get()->row();
    $head_name = $id . '-' . $supplier_info->supplier_name;
    $supplierhcode = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadName', $head_name)
      ->get()
      ->row();
    $code = $supplierhcode->HeadCode;
    echo json_encode($code);
  }

  public function supplier_dueheadcode()
  {
    $id            = $this->input->post('supplier_id');
    $voucher_info  = $this->accounts_model->supplier_due_vouchers($id);

    echo  json_encode($voucher_info);
  }



  // Debit voucher list 
  public function debit_voucher()
  {

    $data['title']      = display('debit_voucher');
    $data['module']     = "account";
    $data['page']   = "debit_voucher_list";
    echo Modules::run('template/layout', $data);
  }

  public function CheckdebitvoucherList()
  {
    $postData  = $this->input->post();
    $data = $this->accounts_model->getdebitvoucherList($postData);
    echo json_encode($data);
  }

  // Debit voucher Create 
  public function create_debit_voucher()
  {
    $this->permission1->method('debit_voucher', 'create')->access();

    $data['title']      = display('debit_voucher');
    $data['acc']        = $this->accounts_model->getTransationHead();
    $data['voucher_no'] = $this->accounts_model->voNO();
    $data['crcc']       = $this->accounts_model->getCashbankHead();
    $data['module']     = "account";
    $data['page']       = "debit_voucher";
    echo Modules::run('template/layout', $data);
  }


  //Create Debit Voucher
  public function store_debit_voucher()
  {

    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("debit_voucher");
    } else {
      if ($this->form_validation->run()) {
        $financialyears = $this->accounts_model->read('*', 'financial_year', array('status' => 1));
        $date = $this->input->post('dtpDate', true);
        $startfdate = $financialyears->startDate;
        $crdate = date("Y-m-d");
        if ($startfdate > $date) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("debit_voucher");
          exit;
        } else if ($date > $crdate || $date > $financialyears->endDate) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("debit_voucher");
          exit;
        } else {
          if ($this->accounts_model->insert_debitvoucher()) {
            $this->session->set_flashdata('message', display('save_successfully'));
            redirect('debit_voucher');
          } else {
            $this->session->set_flashdata('exception',  display('please_try_again'));
            redirect("debit_voucher");
          }
        }
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("debit_voucher");
      }
    }
  }

  public function predefined_accounts()
  {

    $filednames = $this->accounts_model->getPredefineCode();
    $fvalues = $this->accounts_model->getPredefineCodeValues();
    $MesTitle = 'PredefineCode';
    if ($this->input->method(TRUE) == 'POST') {
      foreach ($filednames as $fields) {
        if ($fields != 'id') {
          $rules[$fields] = 'max_length[20]';
        }
      }
      foreach ($filednames as $fields) {
        $definedata[$fields] = $this->input->post($fields);
      }

      $updata = $this->accounts_model->update_content('acc_predefine_account', $definedata, array('id' => $fvalues->id));
      if ($updata) {
        addActivityLog($MesTitle, "update", $fvalues->id, "acc_predefine_account", 2, $definedata);


        $this->session->set_flashdata('message', display('update_successfully'));
        redirect("predefined_accounts");
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("predefined_accounts");
      }
    } else {
      $data['title']  = display('predefined_accounts');
      $data['moduleTitle'] = 'Accounts';
      $data['module'] = "account";
      $data['fieldNames']  = $filednames;
      $data['filedvalues'] = $fvalues;
      $data['allheads']    = $this->accounts_model->getCoaHeads();
      $data['page']   = "predefined_accounts";
      echo Modules::run('template/layout', $data);
    }
  }

  public function bank_reconciliation()
  {
    $this->permission1->method('bank_reconciliation', 'update')->access();
    date_default_timezone_set('Asia/Colombo');

    $dtpFromDate         = $this->input->post('dtpFromDate') ? $this->input->post('dtpFromDate') : date('Y-m-d', strtotime('first day of this month'));
    $dtpToDate           = $this->input->post('dtpToDate') ? $this->input->post('dtpToDate') : date('Y-m-d');
    $bankCode            = $this->input->post('bankCode') ? $this->input->post('bankCode') : null;
    if ($bankCode == '' || $bankCode == null) {
      $bankCode = null;
    } else {
      $bankCode = $bankCode;
    }
    $data['title']       = display('bank_reconciliation');
    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate']   = $dtpToDate;
    $data['bankCode']    = $bankCode;
    $data['module']      = "account";
    $data['setting'] = $this->accounts_model->setting();
    $data['vauchers']    = $this->accounts_model->reconciliation_voucher($dtpFromDate, $dtpToDate, $bankCode, 0);
    $data['banks']       = $this->accounts_model->get_all_bank_list();
    $data['page']        = "bank_reconciliation";
    echo Modules::run('template/layout', $data);
  }

  // Bulk voucher Approve
  public function bank_reconciliation_approve()
  {
    if (!empty($this->input->post('vapprove'))) {
      $voucherlist = array();
      foreach ($this->input->post('vapprove') as $vid) {
        array_push($voucherlist, $vid);
      }
      $av = $this->accounts_model->reconciliation_all($voucherlist);
      if ($av) {
        $this->session->set_flashdata('message', display('successfully_approved'));
      } else {
        $this->session->set_flashdata('exception', display('please_try_again'));
      }
      redirect($_SERVER['HTTP_REFERER']);
    }
  }

  public function bank_reconciliation_report()
  {

    date_default_timezone_set('Asia/Colombo');

    $this->permission1->method('bank_reconciliation', 'read')->access();

    $dtpFromDate         = $this->input->post('dtpFromDate') ? $this->input->post('dtpFromDate') : date('Y-m-d', strtotime('first day of this month'));
    $dtpToDate           = $this->input->post('dtpToDate') ? $this->input->post('dtpToDate') : date('Y-m-d');
    $bankCode            = $this->input->post('bankCode') ? $this->input->post('bankCode') : null;
    if ($bankCode == '' || $bankCode == null) {
      $bankCode = null;
    } else {
      $bankCode = $bankCode;
    }
    $data['title']       = display('bank_reconciliation');
    $data['setting']     = $this->accounts_model->setting();
    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate']   = $dtpToDate;
    $data['bankCode']    = $bankCode;
    $data['module']      = "account";
    $data['vauchers']    = $this->accounts_model->reconciliation_voucher($dtpFromDate, $dtpToDate, $bankCode, 0);

    $data['banks']       = $this->accounts_model->get_all_bank_list();
    $data['page']   = "bank_reconciliation_report";
    echo Modules::run('template/layout', $data);
  }

  public function bank_reconciliation_report_excel($dtpFromDate, $dtpToDate, $bankCode = null)
  {

    $this->permission1->method('bank_reconciliation', 'read')->access();

    $setting     = $this->accounts_model->setting();
    $vauchers    = $this->accounts_model->reconciliation_voucher($dtpFromDate, $dtpToDate, $bankCode, 0);

    // create file name
    $fileName = display('bank_reconciliation_report') . '_' . date('d-m-Y', strtotime($dtpFromDate)) . '_to_' . date('d-m-Y', strtotime($dtpToDate)) . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:L1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('bank_reconciliation_report') . '  on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To '  . date('d-m-Y', strtotime($dtpToDate)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:L2');

    $objPHPExcel->getActiveSheet()->SetCellValue('A3',   display('approved'));
    $objPHPExcel->getActiveSheet()->SetCellValue('G3',   display('unapproved'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:F3');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G3:K3');

    $objPHPExcel->getActiveSheet()->SetCellValue('A4',  display('sl_no'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B4',  display('voucher_no'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C4',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D4',  display('check_no'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E4',  display('check_date'));
    $objPHPExcel->getActiveSheet()->SetCellValue('F4',  display('amount'));
    $objPHPExcel->getActiveSheet()->SetCellValue('G4',  display('voucher_no'));
    $objPHPExcel->getActiveSheet()->SetCellValue('H4',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('I4',  display('check_no'));
    $objPHPExcel->getActiveSheet()->SetCellValue('J4',  display('check_date'));
    $objPHPExcel->getActiveSheet()->SetCellValue('K4',  display('amount'));

    // set Row
    $rowCount = 5;
    $sl = 1;
    $hsum = 0;
    $nsum = 0;
    if ($vauchers) {
      foreach ($vauchers as $appr) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $sl);
        if ($appr->isHonour == 1) {
          $hsum += $appr->Debit;
          $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $appr->VNo);
          $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  $appr->accountName);
          $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  $appr->chequeno);
          $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  $appr->chequeDate);
          $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($appr->Debit, 2));
          $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  '');
        } else {
          $nsum += $appr->Debit;
          $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  '');
          $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $appr->VNo);
          $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  $appr->accountName);
          $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  $appr->chequeno);
          $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  $appr->chequeDate);
          $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($appr->Debit, 2));
        }
        $rowCount++;
      }
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  '');
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($hsum, 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($nsum, 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':E' . $rowCount);
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $rowCount . ':J' . $rowCount);
    }

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }




  public function fixedasset_schedule()
  {
    $this->permission1->method('fixedasset_schedule', 'read')->access();

    $data['fyears'] = $this->accounts_model->get_financial_years();
    $data['title']  = display('fixed_assets_form');
    $data['module'] = "account";
    $data['page']   = "fixed_assets_form";
    echo Modules::run('template/layout', $data);
  }

  public function fixed_assets_report()
  {

    $this->permission1->method('fixedasset_schedule', 'read')->access();

    $fyear         = $this->input->post('fyear');
    $perdefined = $this->db->select('fixedAsset')->from('acc_predefine_account')->get()->row();

    $data['fixedAssets'] =  $this->accounts_model->get_fixedasset_report('A', $perdefined->fixedAsset, $fyear);

    $data['fyears'] = $this->accounts_model->get_financial_years();
    $data['curentYear'] = $this->accounts_model->get_financial_years($fyear);
    $data['title']       = display('fixed_assets_report');
    $data['setting']     = $this->accounts_model->setting();
    $data['fyear']     = $fyear;


    $data['module'] = "account";
    $data['page']   = "fixed_assets_report";
    echo Modules::run('template/layout', $data);
  }
  public function fixed_assets_report_excel($fyear)
  {

    $this->permission1->method('fixedasset_schedule', 'read')->access();
    $perdefined = $this->db->select('fixedAsset')->from('acc_predefine_account')->get()->row();

    $fixedAssets =  $this->accounts_model->get_fixedasset_report('A', $perdefined->fixedAsset, $fyear);

    $fyears = $this->accounts_model->get_financial_years();
    $curentYear = $this->accounts_model->get_financial_years($fyear);
    $data['title']       = display('fixed_assets_report');

    $setting     = $this->accounts_model->setting();
    // create file name
    $fileName = 'fixed_assets_report_' . $curentYear->yearName . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:L1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('fixed_assets_report') . ' ' . $curentYear->yearName);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:L2');

    $objPHPExcel->getActiveSheet()->SetCellValue('A3',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',  display('opening_balance_fixed_assets'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C3',  display('additions'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D3',  display('adjustment'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E3',  display('closing_balance_fixed_assets'));
    $objPHPExcel->getActiveSheet()->SetCellValue('F3',  display('depreciation_rate'));
    $objPHPExcel->getActiveSheet()->SetCellValue('G3',  display('depreciation_value'));
    $objPHPExcel->getActiveSheet()->SetCellValue('H3',  display('opening_balance_accumulated_depreciation'));
    $objPHPExcel->getActiveSheet()->SetCellValue('I3',  display('additions'));
    $objPHPExcel->getActiveSheet()->SetCellValue('J3',  display('adjustment'));
    $objPHPExcel->getActiveSheet()->SetCellValue('K3',  display('closing_balance_accumulated_depreciation'));
    $objPHPExcel->getActiveSheet()->SetCellValue('L3',  display('written_down_value'));




    // set Row
    $rowCount = 5;
    if (count($fixedAssets) > 0) {
      foreach ($fixedAssets as $fixedAsset) {

        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $fixedAsset['headName']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':L' . $rowCount);

        if (count($fixedAsset['nextlevel']) > 0) {
          $rowCount++;
          foreach ($fixedAsset['nextlevel'] as  $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':L' . $rowCount);

            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {


                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['openig'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($inner['curentDebit'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($inner['curentCredit'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($inner['curentValue'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  $inner['depRate'] . ' %');
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($inner['depAmount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($inner['revOpening'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($inner['revCredit'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format($inner['revDebit'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($inner['revBalance'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format($inner['famount'], 2));
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }
    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($fixedAssets[0]['subtotal1'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($fixedAssets[0]['subtotal2'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($fixedAssets[0]['subtotal3'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($fixedAssets[0]['subtotal4'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  '');
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($fixedAssets[0]['subtotal5'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($fixedAssets[0]['subtotal6'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($fixedAssets[0]['subtotal7'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format($fixedAssets[0]['subtotal8'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($fixedAssets[0]['subtotal9'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format($fixedAssets[0]['subtotal10'], 2));


    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  display('checked_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  display('authorised_by'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':D' . $rowCount);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $rowCount . ':H' . $rowCount);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('I' . $rowCount . ':L' . $rowCount);

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }

  public function income_statement_form()
  {

    $this->permission1->method('income_statement_form', 'read')->access();
    $data['fyears'] = $this->accounts_model->get_financial_years();
    $data['title']  = display('income_statement_form');
    $data['module'] = "account";
    $data['page']   = "income_statement_form";
    echo Modules::run('template/layout', $data);
  }

  public function income_statement()
  {

    $this->permission1->method('income_statement_form', 'read')->access();

    $fyear   = $this->input->post('fyear');
    $data['title']  = display('income_statement');
    $perdefined = $this->db->select('costs_of_good_solds')->from('acc_predefine_account')->get()->row();
    $data['incomes'] =  $this->accounts_model->get_monthly_income('I', 'Income', $fyear);
    $data['costofgoodsolds'] =  $this->accounts_model->get_from_secondlevel_expenses('E', $perdefined->costs_of_good_solds, $fyear);

    $data['expenses'] =  $this->accounts_model->get_monthly_income('E', 'Expenses', $fyear);
    $data['fyears'] = $this->accounts_model->get_financial_years();
    $data['curentYear'] = $this->accounts_model->get_financial_years($fyear);

    $data['setting'] = $this->accounts_model->setting();


    $data['module'] = "account";
    $data['page']   = "income_statement";
    echo Modules::run('template/layout', $data);
  }
  public function income_statement_excel($fyear)
  {

    $this->permission1->method('income_statement', 'read')->access();

    $perdefined = $this->db->select('costs_of_good_solds')->from('acc_predefine_account')->get()->row();
    $incomes =  $this->accounts_model->get_monthly_income('I', 'Income', $fyear);
    $costofgoodsolds =  $this->accounts_model->get_from_secondlevel_expenses('E', $perdefined->costs_of_good_solds, $fyear);
    $expenses =  $this->accounts_model->get_monthly_income('E', 'Expenses', $fyear);
    $fyears = $this->accounts_model->get_financial_years();
    $curentYear = $this->accounts_model->get_financial_years($fyear);
    $setting = $this->accounts_model->setting();
    $cellhead = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M');
    // create file name
    $fileName = 'income_statement_report-' . $curentYear->yearName . '.xlsx';
    // load excel library
    $this->load->library('excel');

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:M1');

    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2', display('income_statement') . ' ' . display('for') . ' ' . $curentYear->yearName);
    $objPHPExcel->getActiveSheet()->SetCellValue('L2', display('date') . ': ' . date('d-M-Y'));

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:K2');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L2:M2');

    $objPHPExcel->getActiveSheet()->SetCellValue('A3', display('particulars'));

    $time = strtotime($curentYear->startDate);
    $startmonth = date('n',  strtotime($curentYear->startDate));
    for ($i = 0; $i < 12; $i++) {
      $monthname = date("M-y", strtotime("+ " . $i . " month", $time));
      $objPHPExcel->getActiveSheet()->SetCellValue($cellhead[$i] . '3', $monthname);
    }
    $rowCount = 5;
    //INCOMES
    if (count($incomes) > 0) {
      foreach ($incomes as $income) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $income['head']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, '');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':M' . $rowCount);

        if (count($income['nextlevel']) > 0) {
          $rowCount++;
          foreach ($income['nextlevel'] as $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, '');
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':M' . $rowCount);

            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {
                if ($startmonth == 1) {
                  $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $inner['headName']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($inner['amount1'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($inner['amount2'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($inner['amount3'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($inner['amount4'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($inner['amount5'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($inner['amount6'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($inner['amount7'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($inner['amount8'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($inner['amount9'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($inner['amount10'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($inner['amount11'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($inner['amount12'], 2));
                } else {

                  $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $inner['headName']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($inner['amount7'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($inner['amount8'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($inner['amount9'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($inner['amount10'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($inner['amount11'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($inner['amount12'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($inner['amount1'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($inner['amount2'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($inner['amount3'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($inner['amount4'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($inner['amount5'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($inner['amount6'], 2));
                }
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }
    $rowCount++;
    if ($startmonth == 1) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, display('total_income'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($incomes[0]['gtotal1'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($incomes[0]['gtotal2'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($incomes[0]['gtotal3'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($incomes[0]['gtotal4'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($incomes[0]['gtotal5'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($incomes[0]['gtotal6'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($incomes[0]['gtotal7'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($incomes[0]['gtotal8'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($incomes[0]['gtotal9'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($incomes[0]['gtotal0'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($incomes[0]['gtotal11'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($incomes[0]['gtotal12'], 2));
    } else {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, display('total_income'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($incomes[0]['gtotal7'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($incomes[0]['gtotal8'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($incomes[0]['gtotal9'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($incomes[0]['gtotal10'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($incomes[0]['gtotal11'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($incomes[0]['gtotal12'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($incomes[0]['gtotal1'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($incomes[0]['gtotal2'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($incomes[0]['gtotal3'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($incomes[0]['gtotal4'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($incomes[0]['gtotal5'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($incomes[0]['gtotal6'], 2));
    }

    //COST OF GOODSOLDS
    if (count($costofgoodsolds) > 0) {
      foreach ($costofgoodsolds as $costofgoodsold) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $costofgoodsold['headName']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':M' . $rowCount);

        if (count($costofgoodsold['innerHead']) > 0) {
          $rowCount++;
          foreach ($costofgoodsold['innerHead'] as $inner) {
            if ($startmonth == 1) {
              $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $inner['headName']);
              $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($inner['amount1'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($inner['amount2'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($inner['amount3'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($inner['amount4'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($inner['amount5'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($inner['amount6'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($inner['amount7'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($inner['amount8'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($inner['amount9'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($inner['amount10'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($inner['amount11'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($inner['amount12'], 2));
            } else {

              $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $inner['headName']);
              $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($inner['amount7'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($inner['amount8'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($inner['amount9'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($inner['amount10'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($inner['amount11'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($inner['amount12'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($inner['amount1'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($inner['amount2'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($inner['amount3'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($inner['amount4'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($inner['amount5'], 2));
              $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($inner['amount6'], 2));
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }
    $rowCount++;
    if ($startmonth == 1) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, display('total_cogs'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($costofgoodsolds[0]['subtota1'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format($costofgoodsolds[0]['subtota2'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format($costofgoodsolds[0]['subtota3'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format($costofgoodsolds[0]['subtota4'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format($costofgoodsolds[0]['subtota5'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format($costofgoodsolds[0]['subtota6'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($costofgoodsolds[0]['subtota7'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($costofgoodsolds[0]['subtota8'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($costofgoodsolds[0]['subtota9'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($costofgoodsolds[0]['subtota0'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($costofgoodsolds[0]['subtota11'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($costofgoodsolds[0]['subtota12'], 2));
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, display('gross_profit'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format(($incomes[0]['gtotal1'] - $costofgoodsolds[0]['subtota1']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format(($incomes[0]['gtotal2'] - $costofgoodsolds[0]['subtota2']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format(($incomes[0]['gtotal3'] - $costofgoodsolds[0]['subtota3']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format(($incomes[0]['gtotal4'] - $costofgoodsolds[0]['subtota4']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format(($incomes[0]['gtotal5'] - $costofgoodsolds[0]['subtota5']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format(($incomes[0]['gtotal6'] - $costofgoodsolds[0]['subtota6']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format(($incomes[0]['gtotal7'] - $costofgoodsolds[0]['subtota7']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format(($incomes[0]['gtotal8'] - $costofgoodsolds[0]['subtota8']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format(($incomes[0]['gtotal9'] - $costofgoodsolds[0]['subtota9']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format(($incomes[0]['gtotal10'] - $costofgoodsolds[0]['subtota10']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format(($incomes[0]['gtotal11'] - $costofgoodsolds[0]['subtota11']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format(($incomes[0]['gtotal12'] - $costofgoodsolds[0]['subtota12']), 2));
    } else {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, display('total_cogs'));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format($costofgoodsolds[0]['subtota7'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format($costofgoodsolds[0]['subtota8'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format($costofgoodsolds[0]['subtota9'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format($costofgoodsolds[0]['subtota0'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format($costofgoodsolds[0]['subtota11'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format($costofgoodsolds[0]['subtota12'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format($costofgoodsolds[0]['subtota1'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($costofgoodsolds[0]['subtota2'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($costofgoodsolds[0]['subtota3'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($costofgoodsolds[0]['subtota4'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($costofgoodsolds[0]['subtota5'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($costofgoodsolds[0]['subtota6'], 2));
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, display('gross_profit'));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, number_format(($incomes[0]['gtotal7'] - $costofgoodsolds[0]['subtota7']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, number_format(($incomes[0]['gtotal8'] - $costofgoodsolds[0]['subtota8']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, number_format(($incomes[0]['gtotal9'] - $costofgoodsolds[0]['subtota9']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, number_format(($incomes[0]['gtotal10'] - $costofgoodsolds[0]['subtota10']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, number_format(($incomes[0]['gtotal11'] - $costofgoodsolds[0]['subtota11']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, number_format(($incomes[0]['gtotal12'] - $costofgoodsolds[0]['subtota12']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, number_format(($incomes[0]['gtotal1'] - $costofgoodsolds[0]['subtota1']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, number_format(($incomes[0]['gtotal2'] - $costofgoodsolds[0]['subtota2']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, number_format(($incomes[0]['gtotal3'] - $costofgoodsolds[0]['subtota3']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, number_format(($incomes[0]['gtotal4'] - $costofgoodsolds[0]['subtota4']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, number_format(($incomes[0]['gtotal5'] - $costofgoodsolds[0]['subtota5']), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, number_format(($incomes[0]['gtotal6'] - $costofgoodsolds[0]['subtota6']), 2));
    }

    // EXPENSES
    if (count($expenses) > 0) {
      foreach ($expenses as $expense) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $expense['head']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':M' . $rowCount);

        if (count($expense['nextlevel']) > 0) {
          $rowCount++;
          foreach ($expense['nextlevel'] as $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':M' . $rowCount);

            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {
                if ($startmonth == 1) {
                  $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount1'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($inner['amount2'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($inner['amount3'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($inner['amount4'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($inner['amount5'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($inner['amount6'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($inner['amount7'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($inner['amount8'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format($inner['amount9'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($inner['amount10'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format($inner['amount11'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,  number_format($inner['amount12'], 2));
                } else {

                  $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount7'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($inner['amount8'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($inner['amount9'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($inner['amount10'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($inner['amount11'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($inner['amount12'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($inner['amount1'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($inner['amount2'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format($inner['amount3'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($inner['amount4'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format($inner['amount5'], 2));
                  $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,  number_format($inner['amount6'], 2));
                }
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }
    $rowCount++;
    if ($startmonth == 1) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total_expense'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($expenses[0]['gtotal1'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($expenses[0]['gtotal2'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($expenses[0]['gtotal3'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($expenses[0]['gtotal4'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($expenses[0]['gtotal5'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($expenses[0]['gtotal6'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($expenses[0]['gtotal7'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($expenses[0]['gtotal8'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format($expenses[0]['gtotal9'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($expenses[0]['gtotal0'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format($expenses[0]['gtotal11'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,  number_format($expenses[0]['gtotal12'], 2));
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('net_amount'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($incomes[0]['gtotal1'] - ($costofgoodsolds[0]['subtota1'] + $expenses[0]['gtotal1'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format(($incomes[0]['gtotal2'] - ($costofgoodsolds[0]['subtota2'] + $expenses[0]['gtotal2'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format(($incomes[0]['gtotal3'] - ($costofgoodsolds[0]['subtota3'] + $expenses[0]['gtotal3'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format(($incomes[0]['gtotal4'] - ($costofgoodsolds[0]['subtota4'] + $expenses[0]['gtotal4'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format(($incomes[0]['gtotal5'] - ($costofgoodsolds[0]['subtota5'] + $expenses[0]['gtotal5'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format(($incomes[0]['gtotal6'] - ($costofgoodsolds[0]['subtota6'] + $expenses[0]['gtotal6'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format(($incomes[0]['gtotal7'] - ($costofgoodsolds[0]['subtota7'] + $expenses[0]['gtotal7'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format(($incomes[0]['gtotal8'] - ($costofgoodsolds[0]['subtota8'] + $expenses[0]['gtotal8'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format(($incomes[0]['gtotal9'] - ($costofgoodsolds[0]['subtota9'] + $expenses[0]['gtotal9'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format(($incomes[0]['gtotal10'] - ($costofgoodsolds[0]['subtota10'] + $expenses[0]['gtotal10'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format(($incomes[0]['gtotal11'] - ($costofgoodsolds[0]['subtota11'] + $expenses[0]['gtotal11'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,  number_format(($incomes[0]['gtotal12'] - ($costofgoodsolds[0]['subtota12'] + $expenses[0]['gtotal12'])), 2));
    } else {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total_expense'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($expenses[0]['gtotal7'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($expenses[0]['gtotal8'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($expenses[0]['gtotal9'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($expenses[0]['gtotal10'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format($expenses[0]['gtotal11'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($expenses[0]['gtotal12'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($expenses[0]['gtotal1'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($expenses[0]['gtotal2'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format($expenses[0]['gtotal3'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format($expenses[0]['gtotal4'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format($expenses[0]['gtotal5'], 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,  number_format($expenses[0]['gtotal6'], 2));
      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('net_amount'));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format(($incomes[0]['gtotal7'] - ($costofgoodsolds[0]['subtota7'] + $expenses[0]['gtotal7'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format(($incomes[0]['gtotal8'] - ($costofgoodsolds[0]['subtota8'] + $expenses[0]['gtotal8'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  number_format(($incomes[0]['gtotal9'] - ($costofgoodsolds[0]['subtota9'] + $expenses[0]['gtotal9'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,  number_format(($incomes[0]['gtotal10'] - ($costofgoodsolds[0]['subtota10'] + $expenses[0]['gtotal10'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,  number_format(($incomes[0]['gtotal11'] - ($costofgoodsolds[0]['subtota11'] + $expenses[0]['gtotal11'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,  number_format(($incomes[0]['gtotal12'] - ($costofgoodsolds[0]['subtota12'] + $expenses[0]['gtotal12'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($incomes[0]['gtotal1'] - ($costofgoodsolds[0]['subtota1'] + $expenses[0]['gtotal1'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format(($incomes[0]['gtotal2'] - ($costofgoodsolds[0]['subtota2'] + $expenses[0]['gtotal2'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format(($incomes[0]['gtotal3'] - ($costofgoodsolds[0]['subtota3'] + $expenses[0]['gtotal3'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format(($incomes[0]['gtotal4'] - ($costofgoodsolds[0]['subtota4'] + $expenses[0]['gtotal4'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  number_format(($incomes[0]['gtotal5'] - ($costofgoodsolds[0]['subtota5'] + $expenses[0]['gtotal5'])), 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format(($incomes[0]['gtotal6'] - ($costofgoodsolds[0]['subtota6'] + $expenses[0]['gtotal6'])), 2));
    }

    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  display('accounts'));
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  display('authorized_signature'));
    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount,  display('chairman'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':C' . $rowCount);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $rowCount . ':F' . $rowCount);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $rowCount . ':I' . $rowCount);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J' . $rowCount . ':M' . $rowCount);

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }
  public function year_closing()
  {

    $this->permission1->method('financiall_year', 'update')->access();
    $fyear =   $this->accounts_model->get_current_financial_year();
    $data['title']          = display('year_closing');
    $dtpFromDate   = $fyear->startDate;
    $dtpToDate     = $fyear->endDate;
    $oldyearid     = $fyear->id;
    $oldyearname   = $fyear->yearName;
    if (strpos($oldyearname, '-')) {
      list($preV, $postV) = explode('-', $oldyearname);
      $preV++;
      $postV++;
      $newyear = $preV . '-' . $postV;
    } else {
      $newyear = $oldyearname + 1;
    }

    $CreateBy = $this->session->userdata('id');
    $createdate = date('Y-m-d H:i:s');
    $openDate = date('Y-m-d', strtotime($dtpToDate . ' +1 day'));
    $endDate = date('Y-m-d', strtotime($dtpToDate . ' +1 year'));

    $assets     = $this->accounts_model->year_closing_summery('A', 'Assets', $dtpFromDate, $dtpToDate, $oldyearid);
    $liabilities = $this->accounts_model->year_closing_summery('L', 'Liabilities', $dtpFromDate, $dtpToDate, $oldyearid);
    $equitys     = $this->accounts_model->year_closing_summery('L', 'Shareholder\'s Equity', $dtpFromDate, $dtpToDate, $oldyearid);
    if ($assets && $liabilities  &&  $equitys) {
      $postFyearData = [
        'yearName'     => $newyear,
        'startDate'    => $openDate,
        'endDate'      => $endDate,
        'isCloseReq'   => 0,
        'status'       => 1,
        'created_by'   => $CreateBy,
        'created_date' => $createdate
      ];
      $check =   $this->accounts_model->check_financial_year($openDate, $endDate, $newyear);
      if ($check) {
        $chkbtn     = $this->db->select('*')->from('financial_year')->where('startDate>=', $fyear->endDate)->get()->num_rows();
        if ($chkbtn == 0) {

          $newid_fyear =  $this->accounts_model->financial_year_create($postFyearData);
        }
      }

      $updateFyearData = array(
        'isCloseReq'   => 1,
        'status'       => 0,
        'updated_by'   => $createdate,
        'updated_date' => $createdate
      );
      $upyear = $this->db->where('id', $oldyearid)
        ->update('financial_year', $updateFyearData);
      if ($upyear) {

        //store new financial year to session 
        $this->session->set_userdata('fyear', $newid_fyear);
        $this->session->set_userdata('fyearName', $newyear);
        $this->session->set_userdata('fyearStartDate', $openDate);
        $this->session->set_userdata('fyearEndDate', $endDate);


        $this->session->set_flashdata('message',  'You have successfully clossing the financial year ' . $oldyearname . ' and next financial is now activated');

        $voucherarray = array(
          'isyearClosed' => 1
        );
        $upvoucher = $this->db->where('fyear', $oldyearid)
          ->update('acc_vaucher', $voucherarray);
        if ($upvoucher) {
          redirect("financial_year");
        }
      }
    }
  }


  // reversed closing financial yer
  public function reversed_financial_year($id)
  {

    $this->permission1->method('financiall_year', 'update')->access();

    $CreateBy = $this->session->userdata('id');
    $createdate = date('Y-m-d H:i:s');

    $this->db->where('fyear', $id)
      ->delete('acc_opening_balance');


    $updateFyearData = array(
      'isCloseReq'   => 0,
      'status'       => 1,
      'updated_by'   => $createdate,
      'updated_date' => $createdate
    );
    $upyear = $this->db->where('id', $id)
      ->update('financial_year', $updateFyearData);


    $voucherarray = array(
      'isyearClosed' => 0
    );
    $upvoucher = $this->db->where('fyear', $id)
      ->update('acc_vaucher', $voucherarray);

    $this->session->set_flashdata('message',  'You have successfully reverse financial year clossing');

    redirect("financial_year");
  }

  public function balance_sheet()
  {
    $this->permission1->method('balance_sheet', 'read')->access();
    $data['title']           = display('balance_sheet');
    $dtpFromDate            = (!empty($this->input->post('dtpFromDate')) ? $this->input->post('dtpFromDate') : $this->session->userdata('fyearStartDate'));

    $dtpToDate              = (!empty($this->input->post('dtpToDate')) ? $this->input->post('dtpToDate') : date('Y-m-d'));
    $data['dtpFromDate']    = $dtpFromDate;
    $data['dtpToDate']      = $dtpToDate;


    $data['financialyears'] = $this->accounts_model->get_previous_financial_year(3);

    $data['assets']         = $this->accounts_model->get_balancedheet_summery('A', 'Assets', $dtpFromDate, $dtpToDate);
    $data['liabilities']    = $this->accounts_model->get_balancedheet_summery('L', 'Liabilities', $dtpFromDate, $dtpToDate);
    $data['equitys']        = $this->accounts_model->get_balancedheet_summery('L', 'Shareholder\'s Equity', $dtpFromDate, $dtpToDate);

    $data['incomes'] = $this->accounts_model->get_head_summery('I', 'Income', $dtpFromDate, $dtpToDate, 0);
    $data['expenses'] = $this->accounts_model->get_head_summery('E', 'Expenses', $dtpFromDate, $dtpToDate, 0);
    $data['setting']    = $this->accounts_model->setting();

    $data['module']      = "account";
    $data['page']        = "balance_sheet";
    echo Modules::run('template/layout', $data);
  }

  public function balance_sheet_excel($dtpFromDate, $dtpToDate)
  {
    $this->permission1->method('balance_sheet', 'read')->access();
    $financialyears = $this->accounts_model->get_previous_financial_year(3);
    $assets         = $this->accounts_model->get_balancedheet_summery('A', 'Assets', $dtpFromDate, $dtpToDate);
    $liabilities    = $this->accounts_model->get_balancedheet_summery('L', 'Liabilities', $dtpFromDate, $dtpToDate);
    $equitys        = $this->accounts_model->get_balancedheet_summery('L', 'Shareholder\'s Equity', $dtpFromDate, $dtpToDate);

    $incomes = $this->accounts_model->get_head_summery('I', 'Income', $dtpFromDate, $dtpToDate, 0);
    $expenses = $this->accounts_model->get_head_summery('E', 'Expenses', $dtpFromDate, $dtpToDate, 0);

    $setting     = $this->accounts_model->setting();



    // create file name
    $fileName = display('balance_sheet') . '_on_' . date('d-m-Y', strtotime($dtpFromDate)) . '_To_'  . date('d-m-Y', strtotime($dtpToDate)) . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('balance_sheet') . '  on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To '  . date('d-m-Y', strtotime($dtpToDate)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:E2');

    $objPHPExcel->getActiveSheet()->SetCellValue('A3',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',  $this->session->userdata('fyearName'));
    $ix = 1;
    foreach ($financialyears as $financialyear) {
      if ($ix == 1) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C3',  $financialyear);
      } else if ($ix == 2) {
        $objPHPExcel->getActiveSheet()->SetCellValue('D3',  $financialyear);
      } else if ($ix == 3) {
        $objPHPExcel->getActiveSheet()->SetCellValue('E3',  $financialyear);
      }
      $ix++;
    }


    // set Row
    $rowCount = 5;
    if (count($assets) > 0) {
      foreach ($assets as $asset) {

        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $asset['head']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':E' . $rowCount);

        if (count($asset['nextlevel']) > 0) {
          $rowCount++;
          foreach ($asset['nextlevel'] as  $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($value['subtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($value['ssubtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($value['tsubtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($value['fsubtotal'], 2));

            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {


                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($inner['secondyear'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($inner['thirdyear'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($inner['fourthyear'], 2));
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }
    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total_assets'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($assets[0]['gtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($assets[0]['sgtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($assets[0]['tgtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($assets[0]['fgtotal'], 2));

    $rowCount++;
    $rowCount++;

    if (count($liabilities) > 0) {
      foreach ($liabilities as $liability) {

        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $liability['head']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':E' . $rowCount);

        if (count($liability['nextlevel']) > 0) {
          $rowCount++;
          foreach ($liability['nextlevel'] as  $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($value['subtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($value['ssubtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($value['tsubtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($value['fsubtotal'], 2));

            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {


                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($inner['secondyear'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($inner['thirdyear'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($inner['fourthyear'], 2));
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }

    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total_liabilities'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($liabilities[0]['gtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($liabilities[0]['sgtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($liabilities[0]['tgtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($liabilities[0]['fgtotal'], 2));


    if (count($equitys) > 0) {
      foreach ($equitys as $equity) {

        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $equity['head']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':E' . $rowCount);

        if (count($equity['nextlevel']) > 0) {
          $rowCount++;
          foreach ($equity['nextlevel'] as  $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($value['subtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($value['ssubtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($value['tsubtotal'], 2));
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($value['fsubtotal'], 2));

            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {


                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($inner['secondyear'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($inner['thirdyear'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($inner['fourthyear'], 2));
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }

    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total_equity'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($equitys[0]['gtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($equitys[0]['sgtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format($equitys[0]['tgtotal'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format($equitys[0]['fgtotal'], 2));


    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total_liabilities_equity'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($equitys[0]['gtotal'] + $liabilities[0]['gtotal']), 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format(($equitys[0]['sgtotal'] + $liabilities[0]['sgtotal']), 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  number_format(($equitys[0]['tgtotal'] + $liabilities[0]['tgtotal']), 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  number_format(($equitys[0]['fgtotal'] + $liabilities[0]['fgtotal']), 2));


    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  display('checked_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  display('authorised_by'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $rowCount . ':D' . $rowCount);


    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }


  // Update Debit voucher 
  public function update_debit_voucher()
  {

    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("debit_voucher");
    } else {
      $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
      if ($this->form_validation->run()) {
        if ($this->accounts_model->update_debitvoucher()) {
          $this->session->set_flashdata('message', display('update_successfully'));
          redirect('debit_voucher');
        } else {
          $this->session->set_flashdata('exception',  display('please_try_again'));
        }
        redirect("debit_voucher");
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("debit_voucher");
      }
    }
  }

  //Credit voucher  list
  public function credit_voucher()
  {

    $this->permission1->method('credit_voucher', 'read')->access();
    $data['title']      = display('credit_voucher');

    $data['module']     = "account";
    $data['page']   = "credit_voucher_list";
    echo Modules::run('template/layout', $data);
  }

  public function CheckcreditvoucherList()
  {
    $postData  = $this->input->post();
    $data = $this->accounts_model->getcreditvoucherList($postData);
    echo json_encode($data);
  }

  //create Credit voucher 
  public function create_credit_voucher()
  {

    $this->permission1->method('credit_voucher', 'create')->access();
    $data['title']      = display('credit_voucher');
    $data['acc']        = $this->accounts_model->getTransationHead();
    $data['voucher_no'] = $this->accounts_model->crVno();
    $data['crcc']       = $this->accounts_model->getCashbankHead();
    $data['module']     = "account";
    $data['page']       = "credit_voucher";
    echo Modules::run('template/layout', $data);
  }

  //Create Credit Voucher
  public function store_credit_voucher()
  {


    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("credit_voucher");
    } else {
      $this->permission1->method('credit_voucher', 'create')->access();

      $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
      $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
      if ($this->form_validation->run()) {
        $financialyears = $this->accounts_model->read('*', 'financial_year', array('status' => 1));
        $date = $this->input->post('dtpDate', true);
        $startfdate = $financialyears->startDate;
        $crdate = date("Y-m-d");


        if ($startfdate > $date) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("credit_voucher");
          exit;
        } else if ($date > $crdate || $date > $financialyears->endDate) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("credit_voucher");
          exit;
        } else {

          if ($this->accounts_model->insert_creditvoucher()) {
            $this->session->set_flashdata('message', display('save_successfully'));
            redirect('credit_voucher/');
          } else {
            $this->session->set_flashdata('exception',  display('please_try_again'));
            redirect("credit_voucher");
          }
        }
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("account/accounts/credit_voucher");
      }
    }
  }

  // Contra Voucher form
  public function contra_voucher()
  {

    $this->permission1->method('contra_voucher', 'read')->access();
    $data['title']      = display('contra_voucher');
    $data["voucherInfo"] = $this->accounts_model->get_voucher('CT');
    $data['setting'] = $this->accounts_model->setting();
    $data['module']     = "account";
    $data['page']   = "contra_voucher_list";
    echo Modules::run('template/layout', $data);
  }
  // Contra Voucher form
  public function create_contra_voucher()
  {

    $this->permission1->method('contra_voucher', 'create')->access();

    $data['title']      = display('contra_voucher');
    $data['acc']        = $this->accounts_model->getCashbankHead();
    $data['voucher_no'] = $this->accounts_model->contra();
    $data['module']     = "account";
    $data['page']       = "contra_voucher";
    echo Modules::run('template/layout', $data);
  }

  //Create Contra Voucher
  public function store_contra_voucher()
  {


    $this->permission1->method('contra_voucher', 'create')->access();
    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("contra_voucher");
    } else {
      $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
      $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
      if ($this->form_validation->run()) {
        $financialyears = $this->accounts_model->read('*', 'financial_year', array('status' => 1));
        $date = $this->input->post('dtpDate', true);
        $startfdate = $financialyears->startDate;
        $crdate = date("Y-m-d");
        if ($startfdate > $date) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("contra_voucher");
          exit;
        } else if ($date > $crdate || $date > $financialyears->endDate) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("contra_voucher");
          exit;
        } else {
          if ($this->accounts_model->insert_contravoucher()) {
            $this->session->set_flashdata('message', display('save_successfully'));
            redirect('contra_voucher');
          } else {
            $this->session->set_flashdata('exception',  display('please_try_again'));
            redirect("contra_voucher");
          }
        }
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("contra_voucher");
      }
    }
  }

  // Journal voucher
  public function journal_voucher()
  {
    $this->permission1->method('journal_voucher', 'read')->access();
    $data['title']        = display('journal_voucher');
    $data['module']       = "account";
    $data['page']         = "journal_voucher_list";
    echo Modules::run('template/layout', $data);
  }

  public function CheckjournalvoucherList()
  {
    $postData  = $this->input->post();
    $data = $this->accounts_model->getjournalvoucherList($postData);
    echo json_encode($data);
  }

  // Journal voucher
  public function create_journal_voucher()
  {
    $this->permission1->method('journal_voucher', 'create')->access();

    $data['title']      = display('journal_voucher');
    $data['acc']        = $this->accounts_model->getTransationHead();
    $data['voucher_no'] = $this->accounts_model->journal();
    $data['page']       = 'journal_voucher';
    $data['module']     = "account";
    echo Modules::run('template/layout', $data);
  }


  //Create Journal Voucher
  public function store_journal_voucher()
  {
    $this->permission1->method('journal_voucher', 'create')->access();

    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("journal_voucher");
    } else {
      if ($this->form_validation->run()) {
        $financialyears = $this->accounts_model->read('*', 'financial_year', array('status' => 1));
        $date = $this->input->post('dtpDate', true);
        $startfdate = $financialyears->startDate;
        $crdate = date("Y-m-d");
        if ($startfdate > $date) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("journal_voucher");
          exit;
        } else if ($date > $crdate || $date > $financialyears->endDate) {
          $this->session->set_flashdata('error_message',  display('please_try_again'));
          redirect("journal_voucher");
          exit;
        } else {
          if ($this->accounts_model->insert_journalvoucher()) {
            $this->session->set_flashdata('message', display('save_successfully'));
            redirect('journal_voucher');
          } else {
            $this->session->set_flashdata('exception',  display('please_try_again'));
            redirect("journal_voucher");
          }
        }
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("journal_voucher");
      }
    }
  }

  //Aprove voucher
  public function aprove_v()
  {

    $this->permission1->method('aprove_v', 'update')->access();
    $data['title']    = display('voucher_approve');
    $data['module']   = "account";
    $data['page']     = "voucher_approve";
    echo Modules::run('template/layout', $data);
  }

  public function CheckvoucherList()
  {
    $postData  = $this->input->post();
    $data = $this->accounts_model->getvoucherList($postData);
    echo json_encode($data);
  }

  // Bulk voucher Approve
  public function bulk_voucher_approve()
  {

    $this->permission1->method('aprove_v', 'update')->access();

    if (!empty($this->input->post('vapprove'))) {
      foreach ($this->input->post('vapprove') as $vid) {
        $av = $this->accounts_model->approved_vaucher($vid, 'active');
      }
      if ($av) {
        $this->session->set_flashdata('message', display('successfully_approved'));
      } else {
        $this->session->set_flashdata('exception', display('please_try_again'));
      }
      redirect($_SERVER['HTTP_REFERER']);
    }
  }

  // Voucher Approve
  public function isactive($id = null, $action = null)
  {
    if ($this->accounts_model->approved_vaucher($id, $action)) {
      $this->session->set_flashdata('message', display('successfully_approved'));
    } else {
      $this->session->set_flashdata('exception', display('please_try_again'));
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  //Update voucher 
  public function edit_voucher($id = null)
  {
    $vtype = $this->db->select('*')
      ->from('acc_vaucher')
      ->where('id', $id)
      ->get()
      ->row();

    $data['module']             = "account";
    $data['rowid']              = $vtype->id;
    $data['vNo']                = $vtype->VNo;
    $data['reverseid']          = $vtype->RevCodde;
    $data['VDate']              = $vtype->VDate;
    $data['Narration']          = $vtype->Narration;
    $data['isApproved']         = $vtype->isApproved;
    $data['fyear']              = $vtype->fyear;
    $data['CreateBy']           = $vtype->CreateBy;
    $data['CreateDate']         = $vtype->CreateDate;
    $data['crcc']               = $this->accounts_model->getCashbankHead();
    $data['acc']                = $this->accounts_model->getTransationHead();
    $data['voucher_info']       = $this->accounts_model->get_dbvoucher_info($vtype->VNo);

    if ($vtype->Vtype == "DV") {
      $data['title']            = display('update_debit_voucher');
      $data['page']             =  'update_dbt_crtvoucher';
    }
    if ($vtype->Vtype == "CV") {
      $data['title']            = display('update_credit_voucher');
      $data['page']             =  'update_credit_bdtvoucher';
    }
    if ($vtype->Vtype == "JV") {
      $data['title'] = 'Update' . ' ' . display('journal_voucher');
      $data['page']             =  'update_journal_voucher';
    }

    if ($vtype->Vtype == "CT") {
      $data['title']            = 'Update' . ' ' . display('contra_voucher');
      $data['page']             =  'update_contra_voucher';
    }
    echo Modules::run('template/layout', $data);
  }

  //Get vaucher detail by ajax
  public function voucherDetails()
  {
    $vid = $this->input->post('vno');

    $vtype = explode('-', $vid);
    $data['results']      = $this->accounts_model->getVoucherDetails($vid);


    $data['settings_info'] = $this->accounts_model->setting();
    $data['company_info']  = $this->accounts_model->retrieve_company();

    if ($vtype[0] == 'DV') {

      $details = $this->load->view("dv_view_details", $data, true);
    }
    if ($vtype[0] == 'CV') {
      $details = $this->load->view("cv_view_details", $data, true);
    }
    if ($vtype[0] == 'JV') {
      $details = $this->load->view("jv_view_details", $data, true);
    }
    if ($vtype[0] == 'CT') {
      $details = $this->load->view("tv_view_details", $data, true);
    }

    echo json_encode(array('data' => $details));
  }

  //Update voucher 
  public function voucher_update($id = null)
  {
    $vtype = $this->db->select('*')
      ->from('acc_transaction')
      ->where('VNo', $id)
      ->get()
      ->row();

    if ($vtype->Vtype == "DV") {
      $data['title']            = display('update_debit_voucher');
      $data['dbvoucher_info']   = $this->accounts_model->dbvoucher_updata($id);
      $data['credit_info']      = $this->accounts_model->crvoucher_updata($id);
      $data['module']           = "account";
      $data['page']             =  'update_dbt_crtvoucher';
    }
    if ($vtype->Vtype == "CV") {
      $data['title']            = display('update_credit_voucher');
      $data['crvoucher_info']   = $this->accounts_model->crdtvoucher_updata($id);
      $data['debit_info']       = $this->accounts_model->debitvoucher_updata($id);
      $data['module']           = "account";
      $data['page']             =  'update_credit_bdtvoucher';
    }
    if ($vtype->Vtype == "JV") {
      $data['title']            = 'Update' . ' ' . display('journal_voucher');
      $data['acc']              = $this->accounts_model->Transacc();
      $data['voucher_info']     = $this->accounts_model->journal_updata($id);
      $data['module']           = "account";
      $data['page']             =  'update_journal_voucher';
    }

    if ($vtype->Vtype == "Contra") {
      $data['title']            = 'Update' . ' ' . display('contra_voucher');
      $data['acc']              = $this->accounts_model->Transacc();
      $data['voucher_info']     = $this->accounts_model->journal_updata($id);
      $data['module']           = "account";
      $data['page']             =  'update_contra_voucher';
    }

    $data['crcc']               = $this->accounts_model->Cracc();
    $data['acc']                = $this->accounts_model->Transacc();
    echo Modules::run('template/layout', $data);
  }


  // update credit voucher 
  public function update_credit_voucher()
  {

    $this->permission1->method('credit_voucher', 'update')->access();

    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("credit_voucher");
    } else {
      $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
      if ($this->form_validation->run()) {
        if ($this->accounts_model->update_creditvoucher()) {
          $this->session->set_flashdata('message', display('save_successfully'));
          redirect('credit_voucher');
        } else {
          $this->session->set_flashdata('exception',  display('please_try_again'));
        }
        redirect("credit_voucher");
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("credit_voucher");
      }
    }
  }

  public function update_journal_voucher()
  {

    $this->permission1->method('journal_voucher', 'update')->access();

    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("journal_voucher");
    } else {
      $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
      if ($this->form_validation->run()) {
        if ($this->accounts_model->update_journalvoucher()) {
          $this->session->set_flashdata('message', display('successfully_updated'));
          redirect('journal_voucher');
        } else {
          $this->session->set_flashdata('exception',  display('please_try_again'));
        }
        redirect("journal_voucher");
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("journal_voucher");
      }
    }
  }

  public function update_contra_voucher()
  {

    $this->permission1->method('contra_voucher', 'update')->access();
    $finyear = $this->input->post('finyear', true);
    if ($finyear <= 0) {
      $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
      redirect("contra_voucher");
    } else {
      $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
      if ($this->form_validation->run()) {
        if ($this->accounts_model->update_contravoucher()) {
          $this->session->set_flashdata('message', display('successfully_updated'));
          redirect('contra_voucher');
        } else {
          $this->session->set_flashdata('exception',  display('please_try_again'));
        }
        redirect("contra_voucher");
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
        redirect("contra_voucher");
      }
    }
  }


  //Trial Balannce
  public function trial_balance()
  {

    $this->permission1->method('trial_balance', 'read')->access();
    $data['title']  = display('trial_balance');
    $data['module'] = "account";
    $data['page']   = "trial_balance";
    echo Modules::run('template/layout', $data);
  }


  //Trial Balance Report
  public function trial_balance_report()
  {

    $this->permission1->method('trial_balance', 'read')->access();
    $data['software_info'] = '';
    $dtpFromDate     = $this->input->post('dtpFromDate');
    $dtpToDate       = $this->input->post('dtpToDate');
    $chkWithOpening  = $this->input->post('chkWithOpening', true);
    $accounts = $this->accounts_model->get_transational_accounts();
    $transationList = array();
    $openingList = array();
    foreach ($accounts as $account) {
      $opening =   $this->accounts_model->get_opening_balance($account->HeadCode, $dtpFromDate, $dtpToDate);
      $transsummery  = $this->accounts_model->get_general_ledger_report($account->HeadCode, $dtpFromDate, $dtpToDate, 0, 0);
      $transsummery['HeadName'] = $account->HeadName;
      $transsummery['HeadCode'] = $account->HeadCode;
      $transsummery['HeadType'] = $account->HeadType;
      $transsummery['subType'] = $account->subType;
      $transsummery['PHeadName'] = $account->PHeadName;
      $transsummery['pheadcode'] = $account->PHeadCode;
      $transationList[$account->HeadCode] = $transsummery;
      $openingList[$account->HeadCode] = $opening;
    }

    $data['openings']  = $openingList;
    $data['results']  = $transationList;

    $data['dtpFromDate']  = $dtpFromDate;
    $data['dtpToDate']    = $dtpToDate;
    $data['setting'] = $this->accounts_model->setting();
    $data['title']  = display('trial_balance_report');


    $data['module'] = "account";
    $data['page']   = "trial_balance_report";
    echo Modules::run('template/layout', $data);
  }


  public function trail_balance_detail()
  {

    $this->permission1->method('trial_balance', 'read')->access();
    $cmbCode = $this->input->post('coaid');
    $dtpFromDate = $this->input->post('sdate');
    $dtpToDate = $this->input->post('edate');

    $HeadName = $this->accounts_model->general_led_report_headname($cmbCode);
    $pre_balance = $this->accounts_model->get_opening_balance($cmbCode, $dtpFromDate, $dtpToDate);
    $HeadName2 = $this->accounts_model->get_general_ledger_report($cmbCode, $dtpFromDate, $dtpToDate, 1, 0);

    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['cmbCode'] = $cmbCode;
    $data['HeadName'] = $HeadName;
    $data['ledger'] = $HeadName;
    $data['HeadName2'] = $HeadName2;
    $data['prebalance'] =  $pre_balance;
    $data['settings'] = $this->accounts_model->setting();

    $data['coaid'] = $cmbCode;
    $data['edate'] = $dtpToDate;
    $data['sdate'] = $dtpFromDate;
    $data['achead'] = $this->accounts_model->get_gl_headname($cmbCode);
    $output = $this->load->view("trial_balance_detail2", $data, true);
    echo json_encode(array('data' => $output));
  }

  // working
  public function sub_ledger()
  {

    $this->permission1->method('sub_ledger', 'read')->access();
    $general_ledger = $this->accounts_model->getsubTypeDatahasSubcode();
    $data['general_ledger']  = $general_ledger;
    $data['title']  = display('sub_ledger');
    $data['module'] = "account";
    $data['page']   = "sub_ledger";
    echo Modules::run('template/layout', $data);
  }

  public function sub_ledger_report()
  {
    $this->permission1->method('sub_ledger', 'read')->access();

    $subtype = $this->input->post('subtype', true);
    $subcode = $this->input->post('subcode', true);
    $accounthead = $this->input->post('accounthead', true);
    $dtpFromDate = $this->input->post('dtpFromDate', true);
    $dtpToDate = $this->input->post('dtpToDate', true);
    $subLedger = $this->accounts_model->get_subcode_byid($subcode);
    $HeadName = $this->accounts_model->general_led_report_headname($accounthead);
    $pre_balance = $this->accounts_model->get_opening_balance_subtype($accounthead, $dtpFromDate, $dtpToDate, $subtype, $subcode);
    $HeadName2 = $this->accounts_model->get_general_ledger_report($accounthead, $dtpFromDate, $dtpToDate, 1, 0, $subtype, $subcode);


    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['subtype'] = $subtype;
    $data['subcode'] = $subcode;
    $data['accounthead'] = $accounthead;

    $data['ledger'] = $HeadName;
    $data['subLedger'] = $subLedger;
    $data['HeadName2'] = $HeadName2;
    $data['prebalance'] =  $pre_balance;
    $data['setting'] = $this->accounts_model->setting();

    $data['general_ledger']  = $this->accounts_model->getsubTypeDatahasSubcode();
    $data['subcodes']  = $this->accounts_model->get_subTypeItems($subtype);
    $data['acchead']  = $this->accounts_model->get_account_head_by_subtype($subtype);
    $data['title'] = display('sub_ledger');
    $data['module'] = "account";
    $data['page']   = "subl_ledger_report";
    echo Modules::run('template/layout', $data);
  }



  public function vouchar_cash($date)
  {
    $vouchar_view = $this->accounts_model->get_vouchar_view($date);
    $data = array(
      'vouchar_view' => $vouchar_view,
    );
    $data['title'] = display('accounts_form');
    $content = $this->parser->parse('newaccount/vouchar_cash', $data, true);
    $this->template->full_admin_html_view($content);
  }

  // working
  public function general_ledger()
  {

    $this->permission1->method('general_ledger', 'read')->access();
    $general_ledger = $this->accounts_model->get_general_ledger();
    $data['general_ledger']  = $general_ledger;
    $data['title']  = display('general_ledger');
    $data['setting'] = $this->accounts_model->setting();
    $data['module'] = "account";
    $data['page']   = "general_ledger";
    echo Modules::run('template/layout', $data);
  }

  public function general_led($Headid = NULL)
  {
    $Headid = $this->input->post('Headid');
    $HeadName = $this->accounts_model->general_led_get($Headid);
    echo  "<option>Transaction Head</option>";
    $html = "";
    foreach ($HeadName as $data) {
      $html .= "<option value='$data->HeadCode'>$data->HeadName</option>";
    }
    echo $html;
  }

  public function voucher_report_serach($vouchar = NULL)
  {

    $this->permission1->method('general_ledger', 'read')->access();
    $vouchar = $this->input->post('vouchar', true);
    $voucher_report_serach = $this->accounts_model->voucher_report_serach($vouchar);
    if ($voucher_report_serach->Amount == '') {
      $pay = '0.00';
    } else {
      $pay = $voucher_report_serach->Amount;
    }
    $baseurl = base_url() . 'account/accounts/vouchar_cash/' . $vouchar;
    $html = "";
    $html .= "<td>
                   <a href=\"$baseurl\">CV-BAC-$vouchar</a>
                 </td>
                 <td>Aggregated Cash Credit Voucher of $vouchar</td>
                 <td>$pay</td>
                 <td align=\"center\">$vouchar</td>";
    echo $html;
  }

  public function accounts_report_search()
  {
    $this->permission1->method('general_ledger', 'read')->access();
    $cmbCode = $this->input->post('cmbCode', true);
    $dtpFromDate = $this->input->post('dtpFromDate', true);
    $dtpToDate = $this->input->post('dtpToDate', true);
    $HeadName = $this->accounts_model->general_led_report_headname($cmbCode);
    $pre_balance = $this->accounts_model->get_opening_balance($cmbCode, $dtpFromDate, $dtpToDate);
    $HeadName2 = $this->accounts_model->get_general_ledger_report($cmbCode, $dtpFromDate, $dtpToDate, 1);

    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['cmbCode'] = $cmbCode;
    $data['HeadName'] = $HeadName;
    $data['ledger'] = $HeadName;
    $data['HeadName2'] = $HeadName2;
    $data['prebalance'] =  $pre_balance;

    $general_ledger = $this->accounts_model->get_general_ledger();
    $data['general_ledger']  = $general_ledger;
    $data['title'] = display('general_ledger_report');
    $data['setting']     = $this->accounts_model->setting();

    $data['module'] = "account";
    $data['page']   = "general_ledger_report";
    echo Modules::run('template/layout', $data);
  }


  public function general_ledger_report_excel($dtpFromDate, $dtpToDate, $cmbCode)
  {

    $this->permission1->method('general_ledger', 'read')->access();
    $HeadName = $this->accounts_model->general_led_report_headname($cmbCode);
    $pre_balance = $this->accounts_model->get_opening_balance($cmbCode, $dtpFromDate, $dtpToDate);
    $HeadName2 = $this->accounts_model->get_general_ledger_report($cmbCode, $dtpFromDate, $dtpToDate, 1);

    $ledger = $HeadName;

    $general_ledger = $this->accounts_model->get_general_ledger();
    $setting     = $this->accounts_model->setting();


    // create file name
    $fileName = 'receipt_payment-' . $dtpFromDate . '_to_' . $dtpToDate . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('general_ledger_of') . ' ' . $ledger->HeadName . ' on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To '  . date('d-m-Y', strtotime($dtpToDate)));
    $objPHPExcel->getActiveSheet()->SetCellValue('I2', display('date') . ': ' . date('d-M-Y'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:H2');

    $objPHPExcel->getActiveSheet()->getStyle('3:3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A3',  display('sl'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',  display('transdate'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C3',  display('voucher_no'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D3',  display('voucher_type'));
    $objPHPExcel->getActiveSheet()->SetCellValue('E3',  "Head Name");
    $objPHPExcel->getActiveSheet()->SetCellValue('F3',  display('ledger_comment'));
    $objPHPExcel->getActiveSheet()->SetCellValue('G3',  display('debit'));
    $objPHPExcel->getActiveSheet()->SetCellValue('H3',  display('credit'));
    $objPHPExcel->getActiveSheet()->SetCellValue('I3',  display('balance'));

    $openid = 1;
    $objPHPExcel->getActiveSheet()->SetCellValue('A4' . $rowCount,  $openid);
    $objPHPExcel->getActiveSheet()->SetCellValue('B4' . $rowCount,  date('d-m-Y', strtotime($dtpFromDate)));

    $objPHPExcel->getActiveSheet()->SetCellValue('C4',  'Opening Balance');
    $objPHPExcel->getActiveSheet()->SetCellValue('G4',  number_format(0, 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('H4',  number_format(0, 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('I4',  number_format($prebalance, 2));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C4:F4');
    // set Row
    $rowCount = 5;
    $TotalCredit = 0;
    $TotalDebit  = 0;
    $CurBalance = $prebalance;
    $VTYP = '';

    if ($HeadName2) {
      foreach ($HeadName2 as $key => $data) {
        $TotalDebit += $data->Debit;
        $TotalCredit += $data->Credit;
        if ($HeadName->HeadType == 'A' || $HeadName->HeadType == 'E') {
          if ($data->Debit > 0) {
            $CurBalance += $data->Debit;
          }
          if ($data->Credit > 0) {
            $CurBalance -= $data->Credit;
          }
        } else {
          if ($data->Debit > 0) {
            $CurBalance -= $data->Debit;
          }
          if ($data->Credit > 0) {
            $CurBalance += $data->Credit;
          }
        }
        if ($data->Vtype == 'DV') {
          $VTYP = 'Debit';
        } else if ($data->Vtype == 'CV') {
          $VTYP = 'Credit';
        } else if ($data->Vtype == 'JV') {
          $VTYP = 'Journal';
        } else {
          $VTYP = 'Contra';
        }

        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, (++$key + $openid));
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  date('d-m-Y', strtotime($data->VDate)));
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  $data->VNo);
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  $VTYP);
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  $data->HeadName);
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  $data->ledgerComment);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($data->Debit, 2));
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($data->Credit, 2));
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($CurBalance, 2));

        $rowCount++;
      }

      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  '');
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
      $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  '');
      $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  '');
      $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount,  '');
      $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount,  number_format($TotalDebit, 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount,  number_format($TotalCredit, 2));
      $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount,  number_format($CurBalance, 2));
    }

    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  display('checked_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount,  display('authorised_by'));

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }

  public function cash_book()
  {


    $this->permission1->method('cash_book', 'read')->access();
    $data['title']   = display('cash_book');
    $data['all_pmethod'] = $this->pmethod_dropdown();

    $data['module']  = "account";
    $data['cashs']  = $this->accounts_model->get_all_cash();
    $data['company'] = '';
    $data['setting'] = $this->accounts_model->setting();
    $data['page']    = "cash_book";
    echo Modules::run('template/layout', $data);
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

  public function cash_book_report()
  {


    $this->permission1->method('cash_book', 'read')->access();
    $cmbCode = $this->input->post('cmbCode');
    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate = $this->input->post('dtpToDate');
    $HeadName = $this->accounts_model->general_led_report_headname($cmbCode);
    $pre_balance = $this->accounts_model->get_opening_balance($cmbCode, $dtpFromDate, $dtpToDate);
    $HeadName2 = $this->accounts_model->get_general_ledger_report($cmbCode, $dtpFromDate, $dtpToDate, 1, 0);

    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['cmbCode'] = $cmbCode;
    $data['HeadName'] = $HeadName;
    $data['ledger'] = $HeadName;
    $data['HeadName2'] = $HeadName2;
    $data['prebalance'] =  $pre_balance;
    $data['title']   = display('cash_book');
    $data['module']  = "account";
    $data['company'] = '';
    $data['setting'] = $this->accounts_model->setting();
    $data['cashs']  = $this->accounts_model->get_all_cash();
    $data['page']    = "cash_book_report";
    echo Modules::run('template/layout', $data);
  }

  public function day_book()
  {

    $this->permission1->method('day_book', 'read')->access();
    $data['title']   = display('day_book');
    $data['module']  = "account";
    $data['company'] = '';
    $data['setting'] = $this->accounts_model->setting();
    $data['page']    = "day_book";
    echo Modules::run('template/layout', $data);
  }

  public function day_book_report()
  {

    $this->permission1->method('day_book', 'read')->access();

    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate = $this->input->post('dtpToDate');
    $data['voucherInfo']     = $this->accounts_model->get_voucher_byDate($dtpFromDate, $dtpToDate, 1);
    $data['module']     = "account";
    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['title']   = display('day_book');
    $data['module']  = "account";
    $data['setting'] = $this->accounts_model->setting();
    $data['page']    = "day_book_report";
    echo Modules::run('template/layout', $data);
  }

  public function day_book_report_detail()
  {

    $this->permission1->method('day_book', 'read')->access();

    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate = $this->input->post('dtpToDate');
    $voucherarray = array();
    $voucherList = $this->accounts_model->get_day_book_voucher_byDate($dtpFromDate, $dtpToDate, 1);

    if ($voucherList) {
      foreach ($voucherList as $voucher) {
        $results = $this->accounts_model->getVoucherDetails($voucher->VNo);
        array_push($voucherarray, $results);
      }
    }

    $data['voucherInfo']     = $this->accounts_model->get_voucher_byDate($dtpFromDate, $dtpToDate, 1);
    $data['module']     = "account";
    $data["allvouchers"] = $voucherarray;
    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['title']   = display('day_book');
    $data['module']  = "account";
    $data['setting'] = $this->accounts_model->setting();

    // PDF Generator 
    $this->load->library('pdfgenerator');
    $dompdf = new DOMPDF();
    $page = $this->load->view('account/day_book_report_pdf', $data, true);
    $dompdf->load_html($page);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents('assets/data/pdf/Day-Book-report' . $dtpFromDate . '_To_' . $dtpToDate . '.pdf', $output);
    $data['pdf']    = 'assets/data/pdf/Day-Book-report' . $dtpFromDate . '_To_' . $dtpToDate . '.pdf';

    $data['page']    = "day_book_report";
    echo Modules::run('template/layout', $data);
  }

  public function receipt_payment()
  {

    $this->permission1->method('receipt_payment', 'read')->access();

    $data['title']   = display('receipt_payment');
    $data['module']  = "account";
    $data['company'] = '';
    $data['setting'] = $this->accounts_model->setting();
    $data['page']    = "receipt_payment";
    echo Modules::run('template/layout', $data);
  }

  public function receipt_payment_report()
  {

    $this->permission1->method('receipt_payment', 'read')->access();

    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate = $this->input->post('dtpToDate');
    $reporttype =  $this->input->post('reportType');
    $cashCode = $this->accounts_model->get_predefined_head('cashCode');
    $bankCode = $this->accounts_model->get_predefined_head('bankCode');
    $advancedCode = $this->accounts_model->get_predefined_head('advance');

    $data['cashOpening']   =  $this->accounts_model->get_openningSummery($cashCode, $dtpFromDate, $dtpToDate);
    $data['bankOpening']   =  $this->accounts_model->get_openningSummery($bankCode, $dtpFromDate, $dtpToDate);
    $data['advOpening']    =  $this->accounts_model->get_openningSummery($advancedCode, $dtpFromDate, $dtpToDate);
    $data['cashClosing']   =  $this->accounts_model->get_clossingSummery($cashCode, $dtpToDate);
    $data['bankClosing']   =  $this->accounts_model->get_clossingSummery($bankCode, $dtpToDate);
    $data['advClosing']    =  $this->accounts_model->get_clossingSummery($advancedCode, $dtpToDate);
    $data['receiptitems']  = $this->accounts_model->get_item_ledger_receipt_payment($reporttype, $dtpFromDate, $dtpToDate, 'CV');
    $data['paymentitems']   = $this->accounts_model->get_item_ledger_receipt_payment($reporttype, $dtpFromDate, $dtpToDate, 'DV');


    $data['title']       = display('receipt_payment');
    $data['setting']     = $this->accounts_model->setting();
    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate']   = $dtpToDate;
    $data['reportType']  = $reporttype;


    $data['module']      = "account";
    $data['page']        = "receipt_payment_report";
    echo Modules::run('template/layout', $data);
  }

  public function receipt_payment_report_excel($dtpFromDate, $dtpToDate, $rtype)
  {

    $this->permission1->method('receipt_payment', 'read')->access();

    $reporttype = str_replace('%20', ' ', $rtype);
    $cashCode = $this->accounts_model->get_predefined_head('cashCode');
    $bankCode = $this->accounts_model->get_predefined_head('bankCode');
    $advancedCode = $this->accounts_model->get_predefined_head('advance');
    $cashOpening =  $this->accounts_model->get_openningSummery($cashCode, $dtpFromDate, $dtpToDate);
    $bankOpening =  $this->accounts_model->get_openningSummery($bankCode, $dtpFromDate, $dtpToDate);
    $advOpening  =  $this->accounts_model->get_openningSummery($advancedCode, $dtpFromDate, $dtpToDate);
    $cashClosing =  $this->accounts_model->get_clossingSummery($cashCode, $dtpToDate);
    $bankClosing =  $this->accounts_model->get_clossingSummery($bankCode, $dtpToDate);
    $advClosing  =  $this->accounts_model->get_clossingSummery($advancedCode, $dtpToDate);
    $receiptitems = $this->accounts_model->get_item_ledger_receipt_payment($reporttype, $dtpFromDate, $dtpToDate, 'CV');
    $paymentitems = $this->accounts_model->get_item_ledger_receipt_payment($reporttype, $dtpFromDate, $dtpToDate, 'DV');

    $setting     = $this->accounts_model->setting();
    // create file name
    $fileName = 'receipt_payment-' . $dtpFromDate . '_to_' . $dtpToDate . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:B1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('receipt_payment') . ' ' . display('from_date') . ' ' . date('d-m-Y', strtotime($dtpFromDate)) . ' ' . display('to_date') . ' ' . date('d-m-Y', strtotime($dtpToDate)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:B2');
    $objPHPExcel->getActiveSheet()->SetCellValue('A3',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',  display('balance'));
    $objPHPExcel->getActiveSheet()->SetCellValue('A4',  display('opening_balance'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:B4');

    $objPHPExcel->getActiveSheet()->SetCellValue('A5',  display('cashinhand'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B5',  number_format($cashOpening, 2));

    $objPHPExcel->getActiveSheet()->SetCellValue('A6',  display('cash_bank'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B6',  number_format($bankOpening, 2));

    $objPHPExcel->getActiveSheet()->SetCellValue('A7',  display('advance'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B7',  number_format($advOpening, 2));

    $objPHPExcel->getActiveSheet()->SetCellValue('A8',  display('receipt'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A8:B8');


    // set Row
    $rowCount = 10;
    if (count($receiptitems) > 0) {
      $gtotal = 0;
      foreach ($receiptitems as $receiptitem) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $receiptitem['headName']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':B' . $rowCount);
        if (count($receiptitem['innerHead']) > 0) {
          $rowCount++;
          foreach ($receiptitem['innerHead'] as $inner) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['credit'], 2));
            $gtotal += $inner['credit'];
            $rowCount++;
          }
        }
        $rowCount++;
      }
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($gtotal, 2));
    }
    $xcount = $rowCount + 1;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $xcount,  display('gtotal'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $xcount,  number_format(($gtotal + $cashOpening + $bankOpening + $advOpening), 2));

    $prowCount = $xcount + 1;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $prowCount, display('receipt'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $prowCount . ':B' . $prowCount);
    $prowCount++;
    if (count($paymentitems) > 0) {
      $pgtotal = 0;
      $prowCount++;
      foreach ($paymentitems as $paymentitem) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $prowCount,  $paymentitem['headName']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $prowCount . ':B' . $prowCount);
        if (count($paymentitem['innerHead']) > 0) {
          $prowCount++;
          foreach ($paymentitem['innerHead'] as $inner) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $prowCount,  $inner['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $prowCount,  number_format($inner['debit'], 2));
            $pgtotal += $inner['debit'];
            $prowCount++;
          }
        }
      }
      $prowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $prowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $prowCount, number_format($pgtotal, 2));
    }

    $newrow = $prowCount + 2;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $newrow,  display('closing_balance'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $newrow . ':B' . $newrow);
    $newrow++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $newrow,  display('cashinhand'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $newrow,  number_format($cashClosing, 2));
    $newrow++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $newrow,  display('cash_bank'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $newrow,  number_format($bankClosing, 2));
    $newrow++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $newrow,  display('advance'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $newrow,  number_format($advClosing, 2));
    $newrow++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $newrow,  display('gtotal'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $newrow,  number_format(($pgtotal + $advClosing + $bankClosing + $cashClosing), 2));


    $newrow++;
    $newrow++;
    $newrow++;
    $newrow++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $newrow,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $newrow,  display('checked_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $newrow,  display('authorised_by'));


    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }

  public function bank_book()
  {

    $this->permission1->method('bank_book', 'read')->access();

    $data['title']    = display('bank_book');
    $data['module']  = "account";
    $data['banks']  = $this->accounts_model->get_all_bank();
    $data['setting'] = $this->accounts_model->setting();
    $data['page']    = "bank_book";
    echo Modules::run('template/layout', $data);
  }

  public function bank_book_report()
  {

    $this->permission1->method('bank_book', 'read')->access();

    $cmbCode = $this->input->post('cmbCode');
    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate = $this->input->post('dtpToDate');
    $HeadName = $this->accounts_model->general_led_report_headname($cmbCode);
    $pre_balance = $this->accounts_model->get_opening_balance($cmbCode, $dtpFromDate, $dtpToDate);
    $HeadName2 = $this->accounts_model->get_general_ledger_report($cmbCode, $dtpFromDate, $dtpToDate, 1, 0);
    $data['banks']  = $this->accounts_model->get_all_bank();
    $data['dtpFromDate'] = $dtpFromDate;
    $data['dtpToDate'] = $dtpToDate;
    $data['cmbCode'] = $cmbCode;
    $data['HeadName'] = $HeadName;
    $data['ledger'] = $HeadName;
    $data['HeadName2'] = $HeadName2;
    $data['prebalance'] =  $pre_balance;
    $data['title']   = display('bank_book');
    $data['module']  = "account";
    $data['company'] = '';
    $data['setting'] = $this->accounts_model->setting();
    $data['page']    = "bank_book_report";
    echo Modules::run('template/layout', $data);
  }

  // Inventory Report
  public function inventory_ledger()
  {
    $data['software_info'] = $this->Accounts_model->software_setting_info();
    $data['title'] = display('Inventory_ledger');
    $content = $this->parser->parse('newaccount/inventory_ledger', $data, true);
    $this->template->full_admin_html_view($content);
  }

  public function voucher_report()
  {
    $get_cash = $this->accounts_model->get_cash();
    $get_vouchar = $this->accounts_model->get_vouchar();
    $data = array(
      'get_cash'    => $get_cash,
      'get_vouchar' => $get_vouchar,
    );
    $data['title']  = display('voucher_report');
    $data['module'] = "account";
    $data['page']   = "coa";
    echo Modules::run('template/layout', $data);
  }

  public function deleteVoucher()
  {
    $vno = $this->input->post('vno');
    $vdata = array(
      'voNO'        => $vno,
      'deleteBy'    => $this->session->userdata('id'),
      'deleteDate'  => date('Y-m-d h:i:s'),
    );

    $del = $this->accounts_model->deleteVoucher($vno);
    if ($del) {
      addActivityLog("delete_vaucher", "delete", $vno, "acc_vaucher", 3, $vdata);
      $data = array('success' => 'ok');
    } else {
      $data = array('success' => 'faild');
    }
    echo json_encode($data);
  }

  public function reverseVoucher()
  {

    $finyear = financial_year();
    if ($finyear != '') {

      $vno = $this->input->post('vno');
      $rev = $this->accounts_model->reverseVoucher($vno);
      if ($rev) {
        $data = array('success' => 'ok');
      } else {
        $data = array('success' => 'faild');
      }
    } else {
      $data = array('success' => 'nofiscalyeal');
    }
    echo json_encode($data);
  }

  public function coa_print()
  {

    $this->permission1->method('coa_print', 'read')->access();

    $data['title'] = display('accounts_form');
    $data['module'] = "account";
    $data['setting'] = $this->accounts_model->setting();
    $data['page']   = "coa_print";
    echo Modules::run('template/layout', $data);
  }

  //Profit loss report page
  public function profit_loss_report()
  {

    $this->permission1->method('profit_loss_report', 'read')->access();

    $data['title']   = display('profit_loss_report');
    $data['module'] = "account";
    $data['page']   = "profit_loss_report";
    echo Modules::run('template/layout', $data);
  }

  //Profit loss serch result
  public function profit_loss_report_search()
  {

    $this->permission1->method('profit_loss_report', 'read')->access();

    $data['title']  = display('profit_loss_report');
    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate   = $this->input->post('dtpToDate');

    $data['incomes'] = $this->accounts_model->get_head_summery('I', 'Income', $dtpFromDate, $dtpToDate, 0);
    $data['expenses'] = $this->accounts_model->get_head_summery('E', 'Expenses', $dtpFromDate, $dtpToDate, 0);

    $data['dtpFromDate']  = $dtpFromDate;
    $data['dtpToDate']    = $dtpToDate;
    $data['setting']     = $this->accounts_model->setting();

    $data['module'] = "account";
    $data['page']   = "profit_loss_report_search";
    echo Modules::run('template/layout', $data);
  }

  //Profit loss serch result
  public function profit_loss_report_excel($dtpFromDate, $dtpToDate)
  {

    $this->permission1->method('profit_loss', 'read')->access();

    $incomes = $this->accounts_model->get_head_summery('I', 'Income', $dtpFromDate, $dtpToDate, 0);
    $expenses = $this->accounts_model->get_head_summery('E', 'Expenses', $dtpFromDate, $dtpToDate, 0);
    $setting     = $this->accounts_model->setting();
    // create file name
    $fileName = 'profit_loss_report-' . $dtpFromDate . '_to_' . $dtpToDate . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:C1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('profit_loss') . ' ' . display('from_date') . ' ' . date('d-m-Y', strtotime($dtpFromDate)) . ' ' . display('to_date') . ' ' . date('d-m-Y', strtotime($dtpToDate)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:C2');
    $objPHPExcel->getActiveSheet()->SetCellValue('A3',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',  display('amount'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C3',  display('amount'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:C4');


    // set Row
    $rowCount = 6;
    if (count($incomes) > 0) {
      foreach ($incomes as $income) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $income['head']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':C' . $rowCount);
        if (count($income['nextlevel']) > 0) {
          $rowCount++;
          foreach ($income['nextlevel'] as $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($value['subtotal'], 2));
            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  '');
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }

    $rowCount++;
    if ($incomes[0]['gtotal'] < $expenses[0]['gtotal']) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('profit_loss'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($expenses[0]['gtotal'] - $incomes[0]['gtotal']), 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);

      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($incomes[0]['gtotal'] + ($expenses[0]['gtotal'] - $incomes[0]['gtotal'])), 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);

      $rowCount++;
    } else {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($incomes[0]['gtotal'], 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);
      $rowCount++;
    }

    if (count($expenses) > 0) {
      $rowCount++;
      foreach ($expenses as $expense) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $expense['head']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':C' . $rowCount);
        if (count($expense['nextlevel']) > 0) {
          $rowCount++;
          foreach ($expense['nextlevel'] as $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($value['subtotal'], 2));
            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  '');
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }

    $rowCount++;
    if ($incomes[0]['gtotal'] > $expenses[0]['gtotal']) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('profit_loss'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($incomes[0]['gtotal'] - $expenses[0]['gtotal']), 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);

      $rowCount++;
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format(($expenses[0]['gtotal'] + ($incomes[0]['gtotal'] - $expenses[0]['gtotal'])), 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);

      $rowCount++;
    } else {
      $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
      $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($expenses[0]['gtotal'], 2));
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);

      $rowCount++;
    }

    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  display('accounts'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  display('authorized_signature'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  display('chairman'));

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }


  //expenditure_statement
  public function expenditure_statement()
  {

    $this->permission1->method('expenditure_statement', 'read')->access();

    $data['title']   = display('expenditure_statement');
    $data['module'] = "account";
    $data['page']   = "expenditure_statement";
    echo Modules::run('template/layout', $data);
  }

  //expenditure_statement report
  public function expenditure_statement_report()
  {

    $this->permission1->method('expenditure_statement', 'read')->access();

    $data['title']  = display('expenditure_statement');
    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate   = $this->input->post('dtpToDate');

    $data['expenses'] = $this->accounts_model->get_head_summery('E', 'Expenses', $dtpFromDate, $dtpToDate, 0);

    $data['dtpFromDate']  = $dtpFromDate;
    $data['dtpToDate']    = $dtpToDate;
    $data['setting']     = $this->accounts_model->setting();


    $data['module'] = "account";
    $data['page']   = "expenditure_statement_report";
    echo Modules::run('template/layout', $data);
  }


  //Profit loss serch result
  public function expenditure_statement_report_excel($dtpFromDate, $dtpToDate)
  {

    $this->permission1->method('expenditure_statement', 'read')->access();

    $expenses = $this->accounts_model->get_head_summery('E', 'Expenses', $dtpFromDate, $dtpToDate, 0);
    $setting     = $this->accounts_model->setting();
    // create file name
    $fileName = 'expenditure_statement-' . $dtpFromDate . '_to_' . $dtpToDate . '.xlsx';
    // load excel library
    $this->load->library('excel');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:C1');
    // set Header
    $objPHPExcel->getActiveSheet()->SetCellValue('A1',   $setting->title);
    $objPHPExcel->getActiveSheet()->getStyle('2:2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A2',   display('expenditure_statement') . ' ' . display('from_date') . ' ' . date('d-m-Y', strtotime($dtpFromDate)) . ' ' . display('to_date') . ' ' . date('d-m-Y', strtotime($dtpToDate)));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:C2');
    $objPHPExcel->getActiveSheet()->SetCellValue('A3',  display('particulars'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B3',  display('amount'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C3',  display('amount'));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:C4');


    // set Row
    $rowCount = 6;

    if (count($expenses) > 0) {
      $rowCount++;
      foreach ($expenses as $expense) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $expense['head']);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowCount . ':C' . $rowCount);
        if (count($expense['nextlevel']) > 0) {
          $rowCount++;
          foreach ($expense['nextlevel'] as $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $value['headName']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  '');
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  number_format($value['subtotal'], 2));
            if (count($value['innerHead']) > 0) {
              $rowCount++;
              foreach ($value['innerHead'] as $inner) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  $inner['headName']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($inner['amount'], 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  '');
                $rowCount++;
              }
            }
            $rowCount++;
          }
        }
        $rowCount++;
      }
    }

    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('total'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  number_format($expenses[0]['gtotal'], 2));
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $rowCount . ':C' . $rowCount);

    $rowCount++;


    $rowCount++;
    $rowCount++;
    $rowCount++;
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,  display('prepared_by'));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount,  display('accounts'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount,  display('authorized_signature'));
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount,  display('chairman'));

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($fileName);
    // download file
    header("Content-Type: application/vnd.ms-excel");
    redirect(site_url() . $fileName);
  }



  //Cash flow page
  public function cash_flow_report()
  {
    $data['title']  = display('cash_flow_report');
    $data['module'] = "account";
    $data['page']   = "cash_flow_report";
    echo Modules::run('template/layout', $data);
  }

  //Cash flow report search
  public function cash_flow_report_search()
  {
    $data['software_info'] = '';
    $dtpFromDate = $this->input->post('dtpFromDate');
    $dtpToDate   = $this->input->post('dtpToDate');
    $data['dtpFromDate']  = $dtpFromDate;
    $data['dtpToDate']    = $dtpToDate;
    $data['setting'] = $this->accounts_model->setting();

    // PDF Generator 
    $this->load->library('pdfgenerator');
    $dompdf = new DOMPDF();
    $page = $this->load->view('account/cash_flow_report_search_pdf', $data, true);
    $dompdf->load_html($page);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents('assets/data/pdf/Cash Flow Statement ' . $dtpFromDate . ' To ' . $dtpToDate . '.pdf', $output);

    $data['pdf']    = 'assets/data/pdf/Cash Flow Statement ' . $dtpFromDate . ' To ' . $dtpToDate . '.pdf';
    $data['title']  = display('general_ledger_report');

    $data['module'] = "account";
    $data['page']   = "cash_flow_report_search";
    echo Modules::run('template/layout', $data);;
  }

  public function balance_adjustment()
  {
    $data['title']      = display('balance_adjustment');
    $data['voucher_no'] = $this->accounts_model->balanceadjvoucher();
    $data['paytype']    = $this->accounts_model->paytype();
    $data['module']     = "account";
    $data['page']       = "balance_adjustment";
    echo Modules::run('template/layout', $data);
  }

  // Balance Adjustment Create
  public function create_balance_adjustment()
  {
    $this->form_validation->set_rules('type', display('adjustment_type'), 'required');
    $this->form_validation->set_rules('parent_type', display('parent_head'), 'required');
    $this->form_validation->set_rules('parent_type', display('parent_head'), 'required');
    $this->form_validation->set_rules('amount', display('amount'), 'required');
    $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
    if ($this->form_validation->run()) {
      if ($this->accounts_model->insert_balanceadjustment()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('account/accounts/balance_adjustment/');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("account/accounts/balance_adjustment");
    } else {
      $data['title']      = display('cash_adjustment');
      $data['voucher_no'] = $this->accounts_model->balanceadjvoucher();
      $data['paytype']    = $this->accounts_model->paytype();
      $data['module']     = "account";
      $data['page']       = "balance_adjustment";
      echo Modules::run('template/layout', $data);
    }
  }


  public function payemntheadcode()
  {

    $query = $this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE PHeadName='Cash at Bank' ");
    return $query->row();
  }

  public function bdtask_payment_method_form($id = null)
  {
    $data['title'] = display('add_payment_method');
    #-------------------------------#
    $this->form_validation->set_rules('HeadName', display('HeadName'), 'required|max_length[200]');
    $bank_coa = [
      'name'         => $this->input->post('HeadName', TRUE),
      'status'        => $this->input->post('status', TRUE)
    ];

    if (!$this->permission1->method('payment_method_list', 'create')->access()) {
      $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
      redirect($previous_url);
    }
    #-------------------------------#
    if ($this->form_validation->run() === true) {
      if (empty($id)) {

        $base_url = base_url();

        if ($this->accounts_model->create_method2($bank_coa)) {
          echo '<script type="text/javascript">
            alert("Payment Details saved successfully");
            window.location.href = "' . $base_url . 'payment_method_list";
           </script>';
        } else {
          $message = display('please_try_again');
          echo '<script type="text/javascript">
            alert("' . $message . '");
            window.location.href = "' . $base_url . 'payment_method_list";
           </script>';
        }
      } else {

        $bank_coa = [
          'name'         => $this->input->post('HeadName', TRUE),
          'status'        => $this->input->post('status', TRUE)
        ];
        $this->db->where('id', $id)
          ->update('payment_type', $bank_coa);

        $base_url = base_url();

        echo '<script type="text/javascript">
            alert("Payment Details updated successfully");
            window.location.href = "' . $base_url . 'payment_method_list";
           </script>';
      }
    } else {
      if (!empty($id)) {
        $data['title']    = display('edit_payment_method');
        $data['pmethod']  = $this->accounts_model->payment_methoddata($id);
      }
      $data['module']   = "account";
      $data['page']     = "payment_method_form";
      echo Modules::run('template/layout', $data);
    }
  }

  public function delete_payment_method($id = null)
  {


    $base_url = base_url();

    if ($this->accounts_model->delete_payment($id)) {
      echo '<script type="text/javascript">
            alert("Payment Details Deleted successfully");
            window.location.href = "' . $base_url . 'payment_method_list";
           </script>';
    } else {
      echo '<script type="text/javascript">
            alert("Cannot delete this payment method beacause its linked to it or something went wrong");
            window.location.href = "' . $base_url . 'payment_method_list";
           </script>';
    }
  }

  public function payment_method_list()
  {
    $data['title']      = display('payment_method_list');
    $data['pmethods']   = $this->accounts_model->payment_methodlist();
    $data['module']     = "account";
    $data['page']       = "payment_method_list";
    if (!$this->permission1->method('payment_method_list', 'read')->access()) {
      $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
      redirect($previous_url);
    }
    echo Modules::run('template/layout', $data);
  }

  public function bdtask_supplier_payment()
  {
    $data['title']          = display('supplier_payment');
    $data['supplier_list']  = $this->accounts_model->get_supplier();
    $data['voucher_no']     = $this->accounts_model->Spayment();
    $data['all_pmethod']   = $this->accounts_model->pmethod_dropdown();
    $data['module']         = "account";
    $data['page']           = "supplier_payment_form";
    echo modules::run('template/layout', $data);
  }

  public function create_supplier_payment()
  {
    $this->form_validation->set_rules('txtCode', display('txtCode'), 'max_length[100]');
    $this->form_validation->set_rules('dueAmount', display('due_amount'), 'required');
    $this->form_validation->set_rules('voucher_no', display('voucher_no'), 'required');
    $this->form_validation->set_rules('txtAmount', display('amount'), 'required|max_length[30]');
    if ($this->form_validation->run()) {
      $purchase_date = $this->accounts_model->supplier_payment_insert();
      if ($purchase_date) {
        $info['setting']       = $this->accounts_model->setting();
        $info['supplier_info'] = $this->accounts_model->supplierinfo($this->input->post('supplier_id', TRUE));
        $info['payment_info']  = $this->accounts_model->supplierpaymentinfo($this->input->post('voucher_no', TRUE), $purchase_date);
        $info['company_info']  = $this->accounts_model->retrieve_company();
        $info['message']       = display('save_successfully');
        $info['details']       = $this->load->view('account/supplier_payment_receipt', $info, true);
        $info['status']        = true;
        echo json_encode($info);
        //  echo $data;
        exit;
      } else {
        $info['exception']            = display('please_try_again');
        $info['status']             = false;
        echo json_encode($info);
        exit;
      }
    } else {
      $info['exception']            = validation_errors();
      $info['status']             = false;
      echo json_encode($info);
      exit;
    }
  }

  public function supplier_paymentreceipt($supplier_id, $voucher_no, $coaid)
  {
    $supplier_id           = $this->uri->segment(2);
    $voucher_no            = $this->uri->segment(3);
    $coaid                 = $this->uri->segment(4);
    $info['setting']       = $this->accounts_model->setting();
    $data['supplier_info'] = $this->accounts_model->supplierinfo($supplier_id);
    $data['payment_info']  = $this->accounts_model->supplierpaymentinfo($voucher_no, $coaid);
    $data['title']         = display('supplier_payment_receipt');
    $data['module']        = "account";
    $data['page']          = "supplier_payment_receipt";
    echo modules::run('template/layout', $data);
  }


  //Customer Receive
  public function customer_receive()
  {
    $data['customer_list'] = $this->accounts_model->get_customer();
    $data['voucher_no']    = $this->accounts_model->Creceive();
    $data['all_pmethod']   = $this->accounts_model->pmethod_dropdown();
    $data['title']         = display('customer_receive');
    $data['module']        = "account";
    $data['page']          = "customer_receive_form";
    echo modules::run('template/layout', $data);
  }


  public function customer_headcode()
  {
    $id = $this->input->post('customer_id');
    $voucher_info  = $this->accounts_model->customer_due_vouchers($id);
    echo json_encode($voucher_info);
  }
  public function customer_voucher_due_amount()
  {
    $invoice_id = $this->input->post('invoice_id');
    $due_data = $this->db->select('due_amount')->from('invoice')->where('invoice_id', $invoice_id)->get()->row();

    echo ($due_data->due_amount ? $due_data->due_amount : 0);
  }


  public function create_customer_receive()
  {
    $this->form_validation->set_rules('voucher_no', display('voucher_no'), 'required');
    $this->form_validation->set_rules('txtCode', display('txtCode'), '|max_length[100]');
    $this->form_validation->set_rules('txtAmount', display('amount'), 'max_length[100]');
    if ($this->form_validation->run()) {
      $receive_data = $this->accounts_model->customer_receive_insert();
      if ($receive_data) {
        $info['setting']       = $this->accounts_model->setting();
        $info['company_info']       = $this->accounts_model->retrieve_company();
        $info['customer_info']      = $this->accounts_model->custoinfo($this->input->post('customer_id', TRUE));
        $info['payment_info']       = $this->accounts_model->customerreceiptinfo($this->input->post('voucher_no', TRUE), $receive_data);
        $info['message']            = display('save_successfully');
        $info['details']            = $this->load->view('account/customer_payment_receipt', $info, true);
        $info['status']             = true;
        $base_url = base_url();
        // echo json_encode("thayaan");
        echo '<script type="text/javascript">
          alert("saved successfully");
          window.location.href = "' . $base_url . 'customer_receive";
         </script>';
        exit;
      } else {

        $info['exception']            = display('please_try_again');
        $info['status']             = false;
        echo json_encode($info);
        exit;
      }
    } else {
      $info['exception']            = validation_errors();
      $info['status']             = false;
      echo json_encode($info);
      exit;
    }
  }
  public function create_service_payment()
  {
    $this->form_validation->set_rules('voucher_no', display('voucher_no'), 'required');
    $this->form_validation->set_rules('txtAmount', display('amount'), 'max_length[100]');
    if ($this->form_validation->run()) {
      $aprove = $this->accounts_model->customer_service_payment_insert();
      if ($aprove) {
        $info['setting']            = $this->accounts_model->setting();
        $info['company_info']       = $this->accounts_model->retrieve_company();
        $info['customer_info']      = $this->accounts_model->custoinfo($this->input->post('customer_id', TRUE));
        $info['payment_info']       = $this->accounts_model->customerservicereceiptinfo($this->input->post('voucher_no', TRUE), $aprove);
        $info['message']            = display('save_successfully');
        $info['details']            = $this->load->view('account/service_payment_receipt', $info, true);
        $info['status']             = true;
        echo json_encode($info);
        exit;
      } else {

        $info['exception']            = display('please_try_again');
        $info['status']             = false;
        echo json_encode($info);
        exit;
      }
    } else {
      $info['exception']            = validation_errors();
      $info['status']             = false;
      echo json_encode($info);
      exit;
    }
  }


  public function customer_receipt($customer_id, $voucher_no, $coaid)
  {
    $customer_id           = $this->uri->segment(2);
    $voucher_no            = $this->uri->segment(3);
    $coaid                 = $this->uri->segment(4);
    $data['customer_info'] = $this->accounts_model->custoinfo($customer_id);
    $data['payment_info']  = $this->accounts_model->customerreceiptinfo($voucher_no, $coaid);
    $data['title']         = display('customer_receive');
    $data['module']        = "account";
    $data['page']          = "customer_payment_receipt";
    echo modules::run('template/layout', $data);
  }

  //service Paymwnt
  public function service_payment_view()
  {
    $data['customer_list'] = $this->accounts_model->get_customer();
    $data['voucher_no']    = $this->accounts_model->Creceive();
    $data['all_pmethod']   = $this->accounts_model->pmethod_dropdown();
    $data['title']         = display('service_payment');
    $data['module']        = "account";
    $data['page']          = "service_payment_form";
    echo modules::run('template/layout', $data);
  }

  public function bdtask_cash_adjustment()
  {
    $data['title']      = display('cash_adjustment');
    $data['voucher_no'] = $this->accounts_model->Cashvoucher();
    $data['module']     = "account";
    $data['page']       = "cash_adjustment";
    echo modules::run('template/layout', $data);
  }


  public function customer_service_headcode()
  {
    $id = $this->input->post('customer_id');
    $voucher_info  = $this->accounts_model->customer_service_due_vouchers($id);
    echo json_encode($voucher_info);
  }

  public function customer_service_voucher_due_amount()
  {
    $voucher_no = $this->input->post('invoice_id');
    $due_data = $this->db->select('due_amount')->from('service_invoice')->where('voucher_no', $voucher_no)->get()->row();

    echo ($due_data->due_amount ? $due_data->due_amount : 0);
  }

  public function voucher_due_amount()
  {
    $purchase_id = $this->input->post('purchase_id');
    $due_data = $this->db->select('due_amount')->from('product_purchase')->where('purchase_id', $purchase_id)->get()->row();

    echo ($due_data->due_amount ? $due_data->due_amount : 0);
  }


  public function bdtask_showpaymentmodal()
  {
    $data['all_pmethod'] = $this->accounts_model->pmethod_dropdown();
    $this->load->view('account/newpaymentveiw', $data);
  }
}
