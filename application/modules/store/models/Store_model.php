<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Store_model extends CI_Model
{


    // branch part
    public function create_branch($query=null)
    {
        $this->db->query($query);
        return true;
    }


    public function branchlist($postData = null)
    {

        $response = array();
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
            $searchQuery = " (a.code like '%" . $searchValue . "%'
            or AES_DECRYPT(a.name, '$encryption_key') LIKE '%$searchValue%'
           ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('branch a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('branch a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.id,a.code,
        AES_DECRYPT(a.name, '{$encryption_key}') AS name,
        IF(a.status = 1, 'Active', 'Inactive') as status_label");
        $this->db->from('branch a');
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
            if ($this->permission1->method('branch_list', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'branch_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('branch_list', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'store/store/bdtask_deletebranch/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }

            if($record->status_label=="Active"){
                $status='<span class="label label-success"  >'.$record->status_label.'</a>';

            }
            else{
                $status='<span class="label label-danger"  >'.$record->status_label.'</a>';

            }



            $data[] = array(
                'sl'     => $sl,
                'code'   => $record->code,
                'name'   => $record->name,
                'status_label' =>  $status,
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


    public function single_branch_data($id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("id,code,AES_DECRYPT(name, '{$encryption_key}') AS name,IF(status = 1, 'Active', 'Inactive') as status_label")
            ->from('branch')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function update_branch($query = null)
    {
        $this->db->query($query);
        return true;
    }

    public function delete_branch($id)
    {

        $this->db->where('id', $id)
        ->delete("branch");
        return true;
    }











    // Store Type part
    public function create_storetype($data = [])
    {
        return $this->db->insert('storetype', $data);
    }


    public function storetypelist($postData = null)
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
            $searchQuery = " (a.code like '%" . $searchValue . "%' or a.name like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('storetype a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('storetype a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,IF(a.status = 1, 'Active', 'Inactive') as status_label");
        $this->db->from('storetype a');
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
            if ($this->permission1->method('branchlist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'storetype_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('branchlist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'store/store/bdtask_deletestoretype/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'     => $sl,
                'code'   => $record->code,
                'name'   => $record->name,
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


    public function single_storetype_data($id)
    {
        return $this->db->select("*,IF(status = 1, 'Active', 'Inactive') as status_label")
            ->from('storetype')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function update_storetype($data = [])
    {
        return $this->db->where('id', $data['id'])
            ->update('storetype', $data);
    }

    public function delete_storetype($id)
    {
        try {
            $this->db->trans_start();

            $this->db->where('id', $id)
                ->delete("storetype");

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









    // Floor part
    public function create_floor($data = [])
    {
        return $this->db->insert('floor', $data);
    }


    public function floorlist($postData = null)
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
            $searchQuery = " (a.code like '%" . $searchValue . "%' or a.name like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('floor a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('floor a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,IF(a.status = 1, 'Active', 'Inactive') as status_label");
        $this->db->from('floor a');
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
            if ($this->permission1->method('floorlist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'floor_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('floorlist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'store/store/bdtask_deletefloor/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'     => $sl,
                'code'   => $record->code,
                'name'   => $record->name,
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


    public function single_floor_data($id)
    {
        return $this->db->select("*,IF(status = 1, 'Active', 'Inactive') as status_label")
            ->from('floor')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function update_floor($data = [])
    {
        return $this->db->where('id', $data['id'])
            ->update('floor', $data);
    }

    public function delete_floor($id)
    {
        try {
            $this->db->trans_start();

            $this->db->where('id', $id)
                ->delete("floor");

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












    // Store part
    public function create_store($data = [])
    {
        $this->db->insert('store', $data);
        $inserted_id = $this->db->insert_id();

        return $inserted_id;
    }


    // Store part
    public function create_storefloor($data = [])
    {
        return $this->db->insert('storefloor', $data);
    }


    public function storelist($postData = null)
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
            $searchQuery = " (a.code like '%" . $searchValue . "%' or a.name like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('store a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('store a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,IF(a.status = 1, 'Active', 'Inactive') as status_label,
        IF(a.auto_gdn = 1, 'Enable', 'Disable') as auto_gdn,
        IF(a.auto_grn = 1, 'Enable', 'Disable') as auto_grn");
        $this->db->from('store a');
      //  $this->db->join('storetype c', 'c.id = a.storetype', 'left');

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
            if ($this->permission1->method('storelist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'store_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('storelist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'store/store/bdtask_deletestore/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }

            if($record->status_label=="Active"){
                $status='<span class="label label-success"  >'.$record->status_label.'</a>';

            }
            else{
                $status='<span class="label label-danger"  >'.$record->status_label.'</a>';

            }

            $data[] = array(
                'sl'     => $sl,
                'code'   => $record->code,
                'name'   => $record->name,
                'auto_gdn'   => $record->auto_gdn,
                'auto_grn'   => $record->auto_grn,
                'status_label' => $status,
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



    public function single_store_data($id)
    {
        return $this->db->select("*,IF(status = 1, 'Active', 'Inactive') as status_label")
            ->from('store')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function update_store($data = [])
    {
        return $this->db->where('id', $data['id'])
            ->update('store', $data);
    }

    public function delete_store($id)
    {
        try {
            $this->db->trans_start();

            $this->db->where('id', $id)
                ->delete("store");

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


    public function active_floor()
    {
        $this->db->select('*');
        $this->db->from('floor');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_storetype()
    {
        $this->db->select('*');
        $this->db->from('storetype');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function active_branch()
    {
        $this->db->select('*');
        $this->db->from('branch');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function added_storefloor($id)
    {
        $this->db->select('floor');
        $this->db->from('storefloor');
        $this->db->where('store', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
}
