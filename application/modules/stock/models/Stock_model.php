<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Stock_model extends CI_Model
{


    // stock batch part
    public function create_stockbatch($data = [])
    {
        return $this->db->insert('stockbatch', $data);
    }


    public function stockbatchlist($postData = null)
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
            $searchQuery = " (a.batchid like '%" . $searchValue . "%' or a.details like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('stockbatch a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('stockbatch a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,IF(a.status = 1, 'Active', 'Inactive') as status_label");
        $this->db->from('stockbatch a');
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
            if ($this->permission1->method('stockbatchlist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'stockbatch_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('stockbatchlist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/bdtask_deletestockbatch/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'     => $sl,
                'batchid'   => $record->batchid,
                'details'   => $record->details,
                'status_label' => $record->status_label,
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


    public function single_stockbatch_data($id)
    {
        return $this->db->select("*,IF(status = 1, 'Active', 'Inactive') as status_label")
            ->from('stockbatch')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function update_stockbatch($data = [])
    {
        return $this->db->where('id', $data['id'])
            ->update('stockbatch', $data);
    }

    public function delete_stockbatch($id)
    {
        try {
            $this->db->trans_start();

            $this->db->where('id', $id)
                ->delete("stockbatch");

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }








    //Opening stockbatch
    public function openingstockbatchlist($postData = null)
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
            $searchQuery = " (sd.batch_id like '%" . $searchValue . "%' or sb.batchid like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ope_stock os');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ope_stock os');


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('os.id,sd.batch_id, sb.batchid, os.date');
        $this->db->from('ope_stock os');
        $this->db->join('stock_details sd', 'sd.pid = os.id');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id');
        $this->db->group_by('sd.batch_id');


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
            if ($this->permission1->method('openingstockbatchlist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'openingstock_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('openingstockbatchlist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_opetock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'batch_id' => $record->batch_id,
                'batchid'  => $record->batchid,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }


    //Dam stockbatch
    public function damagestock($postData = null)
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
            $searchQuery = " (ds.date like '%" . $searchValue . "%' or ds.reason like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('dam_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('dam_stock ds');

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('ds.id,ds.reason, ds.date');
        $this->db->from('dam_stock ds');
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
            if ($this->permission1->method('manage_stockdisposalnote', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'stockdisposalnote_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_stockdisposalnote', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_damstock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'reason'  => $record->reason,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }



    //adj stockbatch
    public function adjstock($postData = null)
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
            $searchQuery = " (ds.stocktype like '%" . $searchValue .  "%' or ds.type like '%" . $searchValue .  "%' or ds.id like '%" . $searchValue .  "%' or ds.date like '%" . $searchValue . "%' or ds.reason like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('adj_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('adj_stock ds');

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('ds.id, ds.reason, ds.date,');
        $this->db->select("(CASE 
                WHEN ds.type = 'openingstock' THEN 'Opening Stock' 
                WHEN ds.type = 'storetransfer' THEN 'Store Transfer' 
                WHEN ds.type = 'stockdisposal' THEN 'Stock Disposal' 
                WHEN ds.type = 'stockadjustment' THEN 'Stock Adjustment' 
            END) AS type", false);
        $this->db->select("(CASE 
                WHEN ds.stocktype = 'actualstock' THEN 'Actual Stock' 
                WHEN ds.stocktype = 'physicalstock' THEN 'Physical Stock' 
                WHEN ds.stocktype = 'both' THEN 'Both' 
            END) AS stocktype", false);
        $this->db->from('adj_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by("ds.id", "desc");
        $this->db->order_by("ds.date", "desc");

        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            if ($this->permission1->method('manage_stock_adjustment', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'newstockadjustment_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_stock_adjustment', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_adjstock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'id'     => $record->id,
                'reason'  => $record->reason,
                'stocktype' => $record->stocktype,
                'type' => $record->type,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }


    //adj stockbatch
    public function ststock($postData = null)
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
            $searchQuery = " (ds.date like '%" . $searchValue . "%' or ds.details like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('st_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('st_stock ds');

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('ds.id,ds.details, ds.date');
        $this->db->from('st_stock ds');
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
            if ($this->permission1->method('manage_store_transfer', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'new_store_transfer/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_store_transfer', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_ststock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'details'  => $record->details,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }




    //grn stockbatch
    //grn stockbatch
    public function grnstock($postData = null, $type2, $storeid)
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
        $encryption_key = Config::$encryption_key;


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

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = "(
                        ds.date LIKE '%" . $searchValue . "%' 
                        OR ds.detail LIKE '%" . $searchValue . "%' 
                        OR AES_DECRYPT(ds.grn_id, '" . $encryption_key . "') LIKE '%" . $searchValue . "%'
                        OR AES_DECRYPT(si1.supplier_name, '" . $encryption_key . "') LIKE '%" . $searchValue . "%'
                    )";
        }

        ## Total number of records without filtering
        $this->db->select("count(*) as allcount");
        $this->db->from('grn_stock ds');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('supplier_information si1', 'si1.supplier_id = ds.supplier_id', "left");
        $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $type2);

        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('s.id', $storeids);
            }
        }

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select("count(*) as allcount");
        $this->db->from('grn_stock ds');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('supplier_information si1', 'si1.supplier_id = ds.supplier_id', "left");
        $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $type2);

        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('s.id', $storeids);
            }
        }


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("ds.id,
    AES_DECRYPT(ds.grn_id, '" . $encryption_key . "') AS grn_id,
    ds.detail,
    ds.date,
    AES_DECRYPT(p.chalan_no, '" . $encryption_key . "') AS voucherno,
    ds.type,
    s.name AS store,
    p.status AS status,
    AES_DECRYPT(si1.supplier_name, '" . $encryption_key . "') AS supplier_name");

        $this->db->from('grn_stock ds');
        $this->db->join('purchase p', 'ds.voucherno = p.id', 'left');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('supplier_information si1', 'si1.supplier_id = ds.supplier_id', 'left');

        $this->db->where('pd.type', 'grn_stock');
        $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $type2);

        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('s.id', $storeids);
            }
        }



        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $this->db->group_by('grn_id');

        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";

            if ($record->status == 0) {
                if ($this->permission1->method('manage_grn', 'update')->access()) {

                    $button .= ' <a href="' . $base_url . 'new_grn/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if ($this->permission1->method('manage_grn', 'delete')->access()) {

                    $button .= '  <a href="' . $base_url . 'stock/stock/delete_grnstock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
                }
            }

            //  if ($this->permission1->method('manage_grn', 'delete')->access()) {

            //     $button .= '  <a href="' . $base_url . 'stock/stock/getgrnStockForPos/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            // }


            $button .= '  </button>  <button  style="margin-left:5px;" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="left" title="Reprint" 
               onclick="reprintInvoice(' . $record->id . ')">
               <i class="fa fa-fax" ></i>
           </button>';

            $data[] = array(
                'sl'       => $sl,
                'grn_id'   => $record->grn_id,
                'details'  => $record->detail,
                'date'     => $record->date,
                'supplier_name' => $record->supplier_name,
                'voucherno' => $record->voucherno,
                'type'     => $record->type,
                'store'     => $record->store,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }



    //gdn stockbatch
    //grn stockbatch
    public function gdnstock($postData = null, $type2,$storeid)
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
        $encryption_key = Config::$encryption_key;

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

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = "(
               ds.date LIKE '%" . $searchValue . "%' 
               OR ds.detail LIKE '%" . $searchValue . "%' 
               OR ds.voucherno LIKE '%" . $searchValue . "%' 
               OR AES_DECRYPT(ci1.customer_name, '" . $encryption_key . "') LIKE '%" . $searchValue . "%' 
           )";
        }

        ## Total number of records without filtering
        $this->db->select("count(*) as allcount");
        $this->db->from('gdn_stock ds');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('customer_information ci1', 'ci1.customer_id = ds.customer_id', "left");
        $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $type2);

        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('s.id', $storeids);
            }
        }


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select("count(*) as allcount");
        $this->db->from('gdn_stock ds');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('customer_information ci1', 'ci1.customer_id = ds.customer_id', "left");
        $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $type2);

        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('s.id', $storeids);
            }
        }



        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("ds.id,
       AES_DECRYPT(ds.gdn_id,'$encryption_key') AS gdn_id,
       ds.detail,
       ds.date,
       AES_DECRYPT(p.sale_id,'$encryption_key') AS voucherno,
       ds.type,
       s.name as store,
       p.status as status,AES_DECRYPT(ci1.customer_name, '" . $encryption_key . "') as customer_name");
        $this->db->from('gdn_stock ds');
        $this->db->join('sale p', 'ds.voucherno = p.id', "left");
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', "left");
        $this->db->join('store s', 's.id = pd.store', "left");
        $this->db->join('customer_information ci1', 'ci1.customer_id = ds.customer_id', "left");

        $this->db->where('pd.type', 'gdn_stock');
        $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $type2);

        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('s.id', $storeids);
            }
        }




        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->group_by('gdn_id');

        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            if ($record->status == 0) {

                if ($this->permission1->method('manage_gdn', 'update')->access()) {
                    $button .= ' <a href="' . $base_url . 'new_gdn/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if ($this->permission1->method('manage_gdn', 'delete')->access()) {

                    $button .= '  <a href="' . $base_url . 'stock/stock/delete_gdnstock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
                }
            }

            $button .= '  </button>  <button  style="margin-left:5px;" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="left" title="Reprint" 
           onclick="reprintInvoice(' . $record->id . ')">
           <i class="fa fa-fax" ></i>
       </button>';


            $data[] = array(
                'sl'       => $sl,
                'gdn_id'   => $record->gdn_id,
                'details'  => $record->detail,
                'date'     => $record->date,
                'voucherno' => $record->voucherno,
                'type'     => $record->type,
                'store'     => $record->store,
                'customer_name' => $record->customer_name,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }
}
