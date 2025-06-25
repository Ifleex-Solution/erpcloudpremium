<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Supplier_model extends CI_Model
{


  public function create($query = null)
  {
    $this->db->query($query);

    return true;
  }

  public function supplier_dropdown()
  {
    $data =  $this->db->select("*")
      ->from('supplier_information')
      ->order_by('supplier_name', 'asc')
      ->get()
      ->result();

    $list[''] = display('select_option');
    if (!empty($data)) {
      foreach ($data as $value)
        $list[$value->supplier_id] = $value->supplier_name;
      return $list;
    } else {
      return false;
    }
  }





  public function supplier_list($offset = null, $limit = null)
  {


    return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('supplier_information a')
      ->join('acc_coa b', 'a.supplier_id = b.supplier_id', 'left')
      ->group_by('a.supplier_id')
      ->order_by('a.supplier_name', 'asc')
      ->limit($offset, $limit)
      ->get()
      ->result();
  }


  public function getsupplierList($postData = null)
  {

    $response = array();
    $supplier_id =  $this->input->post('supplier_id');
    $custom_data = $this->input->post('customfiled');
    if (!empty($custom_data)) {
      $cus_data = [''];
      foreach ($custom_data as $cusd) {
        $cus_data[] = $cusd;
      }
    }

    $encryption_key = Config::$encryption_key;

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
      $searchQuery = "(
        AES_DECRYPT(supplier_name, '$encryption_key') LIKE '%$searchValue%' OR
        AES_DECRYPT(mobile, '$encryption_key') LIKE '%$searchValue%' OR
        AES_DECRYPT(emailnumber, '$encryption_key') LIKE '%$searchValue%' OR
        AES_DECRYPT(phone, '$encryption_key') LIKE '%$searchValue%' OR
        AES_DECRYPT(address, '$encryption_key') LIKE '%$searchValue%' OR
        country LIKE '%$searchValue%' OR
        state LIKE '%$searchValue%' OR
        zip LIKE '%$searchValue%' OR
        city LIKE '%$searchValue%'
    )";    }

    ## Total number of records without filtering
    $this->db->select('count(*) as allcount');
    $this->db->from('supplier_information a');
    // $this->db->join('acc_coa b', 'a.supplier_id = b.supplier_id', 'left');

    if (!empty($supplier_id)) {
      $this->db->where('a.supplier_id', $supplier_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.supplier_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->group_by('a.supplier_id');
    $totalRecords = $this->db->get()->num_rows();

    ## Total number of record with filtering
    $this->db->select('count(*) as allcount');
    $this->db->from('supplier_information a');
    // $this->db->join('acc_coa b', 'a.supplier_id = b.supplier_id', 'left');
    if (!empty($supplier_id)) {
      $this->db->where('a.supplier_id', $supplier_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.supplier_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->group_by('a.supplier_id');
    $totalRecordwithFilter = $this->db->get()->num_rows();
    $encryption_key = Config::$encryption_key;

    ## Fetch records
    $this->db->select("a.supplier_id as supplier_id,
       AES_DECRYPT(a.supplier_name, '{$encryption_key}') AS supplier_name,
      AES_DECRYPT(a.mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.address, '{$encryption_key}') AS address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.emailnumber, '{$encryption_key}') AS emailnumber,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country,IF(a.status = 1, 'Active', 'Inactive') as status_label");
    $this->db->from('supplier_information a');
    // $this->db->join('acc_coa b', 'a.supplier_id = b.supplier_id', 'left');
    // $this->db->group_by('a.supplier_id');
    if (!empty($supplier_id)) {
      $this->db->where('supplier_id', $supplier_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('supplier_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->order_by($columnName, $columnSortOrder);
    $this->db->limit($rowperpage, $start);
    $records = $this->db->get()->result();
    $data = array();
    $sl = 1;

    foreach ($records as $record) {
      $button = '';
      $base_url = base_url();

      $jsaction = "return confirm('Are You Sure ?')";


      if ($this->permission1->method('manage_supplier', 'update')->access()) {
        $button .= ' <a href="' . $base_url . 'edit_supplier/' . $record->supplier_id . '" class="btn btn-info btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>';
      }
      if ($this->permission1->method('manage_supplier', 'delete')->access()) {
        // $button .= ' <a onclick="supplierdelete(' . $record->supplier_id . ')" href="javascript:void(0)"  class="btn btn-danger btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>';
     
        $button .= '  <a  style="margin-left:5px;" href="' . $base_url . 'supplier/supplier/bdtask_delete/' . $record->supplier_id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';

      }

      if($record->status_label=="Active"){
        $status='<span class="label label-success"  >'.$record->status_label.'</a>';

    }
    else{
        $status='<span class="label label-danger"  >'.$record->status_label.'</a>';

    }


      // $supplier = '<a href="' . $base_url . 'supplier_ledgerinfo/' . $record->supplier_id . '">' . $record->supplier_name . '</a>';


      $data[] = array(
        'sl'               => $sl,
        'supplier_name'    => $record->supplier_name,
        'address'          => $record->address,
        'address2'         => $record->address2,
        'mobile'           => $record->mobile,
        'phone'            => $record->phone,
        'email'            => $record->emailnumber,
        'email_address'    => $record->email_address,
        'contact'          => $record->contact,
        'fax'              => $record->fax,
        'city'             => $record->city,
        'state'            => $record->state,
        'zip'              => $record->zip,
        'country'          => $record->country,
        'status'           => $status,

        // 'balance'          => (!empty($record->balance) ? $record->balance : 0),
        'button'           => $button,

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





  public function individual_info($id)
  {
    return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('supplier_information a')
      ->join('acc_coa b', 'a.supplier_id = b.supplier_id', 'left')
      ->where('a.supplier_id', $id)
      ->group_by('a.supplier_id')
      ->order_by('a.supplier_name', 'asc')
      ->get()
      ->result();
  }






  public function singledata($id = null)
  {
    $encryption_key = Config::$encryption_key;

    return $this->db->select("a.supplier_id as supplier_id,
       AES_DECRYPT(a.supplier_name, '{$encryption_key}') AS supplier_name,
      AES_DECRYPT(a.mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.address, '{$encryption_key}') AS address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.emailnumber, '{$encryption_key}') AS emailnumber,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country,IF(a.status = 1, 'Active', 'Inactive') as status_label")
      ->from('supplier_information a')
      ->where('supplier_id', $id)
      ->get()
      ->row();
  }

  public function allsupplier()
  {
    return $this->db->select('*')
      ->from('supplier_information')
      ->get()
      ->result();
  }




  public function update($query = null)
  {
    $this->db->query($query);

   
    return true;
  }

  public function delete($id = null)
  {


    $productExists = $this->db->from('purchase')
      ->where('supplier_id', $id)
      ->count_all_results();

    if ($productExists > 0) {
      return false;
    } else {
      // No products linked, proceed to delete the category
      $this->db->where('referenceNo', $id)
        ->where('subTypeId', 4)
        ->delete('acc_subcode');

      $this->db->where('supplier_id', $id)
        ->delete("supplier_information");
      return $this->db->affected_rows() > 0;
    }
  }


  public function headcode()
  {
    $query = $this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '21110%'");
    return $query->row();
  }


  public function previous_balance_add($balance, $supplier_id)
  {
    $cusifo = $this->db->select('*')->from('supplier_information')->where('supplier_id', $supplier_id)->get()->row();
    $headn = $supplier_id . '-' . $cusifo->supplier_name;
    $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
    $supplier_headcode = $coainfo->HeadCode;
    $transaction_id = $this->generator(10);


    // supplier debit for previous balance
    $cosdr = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  $supplier_headcode,
      'Narration'      =>  'supplier debit For ' . $cusifo->supplier_name,
      'Debit'          =>  $balance,
      'Credit'         =>  0,
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );
    $inventory = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  1141,
      'Narration'      =>  'Inventory credit For Old sale For' . $cusifo->supplier_name,
      'Debit'          =>  0,
      'Credit'         =>  $balance, //purchase price asbe
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );


    if (!empty($balance)) {
      $this->db->insert('acc_transaction', $cosdr);
      $this->db->insert('acc_transaction', $inventory);
    }
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


  public function supplier_ledgerdata($per_page, $page)
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where('b.PHeadName', 'Suppliers');
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $this->db->limit($per_page, $page);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {

      return $query->result_array();
    }
    return false;
  }


  public function count_supplier_ledger()
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where('b.PHeadName', 'Suppliers');
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->num_rows();
    }
    return false;
  }


  public function supplier_list_ledger()
  {
    $this->db->select('*');
    $this->db->from('supplier_information');
    $this->db->order_by('supplier_name', 'asc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function supplier_personal_data($supplier_id)
  {
    $this->db->select('*');
    $this->db->from('supplier_information');
    $this->db->where('supplier_id', $supplier_id);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function supplierledger_searchdata($supplier_id, $start, $end)
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where(array('b.supplier_id' => $supplier_id, 'a.VDate >=' => $start, 'a.VDate <=' => $end));
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function supplier_list_advance()
  {
    $this->db->select('*');
    $this->db->from('supplier_information');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function advance_details($transaction_id, $supplier_id)
  {

    $headcode = $this->db->select('HeadCode')->from('acc_coa')->where('supplier_id', $supplier_id)->get()->row();
    return $data  = $this->db->select('*')
      ->from('acc_transaction')
      ->where('VNo', $transaction_id)
      ->where('COAID', $headcode->HeadCode)
      ->get()
      ->result_array();
  }

  public function supplier_product_sale_info($supplier_id)
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where('b.supplier_id', $supplier_id);
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }
}
