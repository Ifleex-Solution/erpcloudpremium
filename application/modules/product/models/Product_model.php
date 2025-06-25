<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Product_model extends CI_Model
{

    public function category_list()
    {
        return $this->db->select('*')
            ->from('product_category')
            ->get()
            ->result();
    }


    public function create_category($data = [])
    {
        return $this->db->insert('product_category', $data);
    }

    public function vat_tax_setting()
    {
        $this->db->select('*');
        $this->db->from('vat_tax_setting');
        $query   = $this->db->get();
        return $query->row();
    }



    public function update_category($data = [])
    {
        return $this->db->where('category_id', $data['category_id'])
            ->update('product_category', $data);
    }

    public function single_category_data($id)
    {
        return $this->db->select('*')
            ->from('product_category')
            ->where('category_id', $id)
            ->get()
            ->row();
    }

    public function delete_category($id)
    {

        $productExists = $this->db->from('product_information')
            ->where('category_id', $id)
            ->count_all_results();

        if ($productExists > 0) {
            return false;
        } else {
            // No products linked, proceed to delete the category
            $this->db->where('category_id', $id)
                ->delete('product_category');
            return $this->db->affected_rows() > 0;
        }
    }


    // unit part
    public function unit_list()
    {
        return $this->db->select('*')
            ->from('units')
            ->get()
            ->result();
    }


    public function create_unit($data = [])
    {
        return $this->db->insert('units', $data);
    }



    public function update_unit($data = [])
    {
        return $this->db->where('unit_id', $data['unit_id'])
            ->update('units', $data);
    }

    public function single_unit_data($id)
    {
        return $this->db->select('*')
            ->from('units')
            ->where('unit_id', $id)
            ->get()
            ->row();
    }

    public function delete_unit($id)
    {

        $array = explode("1234", $id);

        $productExists = $this->db->from('product_information')
            ->where('unit', $array[0])
            ->count_all_results();

        if ($productExists > 0) {
            return false;
        } else {
            // No products linked, proceed to delete the category
            $this->db->where('unit_id', $array[1])
                ->delete('units');
            return $this->db->affected_rows() > 0;
        }

        $this->db->where('unit_id', $id)
            ->delete("units");
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }

    public function supplier_list()
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

    public function active_category()
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

    public function active_floorByStore($id)
    {
        $this->db->select('st.id, st.name');
        $this->db->from('store s');
        $this->db->join('storefloor sf', 'sf.store = s.id');
        $this->db->join('floor st', 'st.id = sf.floor');
        $this->db->where('s.id', $id);
        $this->db->where('st.status', 1);


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

    public function active_unit()
    {
        $this->db->select('*');
        $this->db->from('units');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return FALSE;
    }

    public function supplier_product_list($id)
    {
        $this->db->select('*');
        $this->db->from('supplier_product');
        $this->db->where('product_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return FALSE;
    }


    public function product_opening($id)
    {
        $this->db->select('*');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $id);
        $this->db->where('purchase_id IS NULL');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }



    public function single_product_data($id)
    {
        $encryption_key = Config::$encryption_key;

        return 
        $this->db->select("
    product_id,
    product_name,
    category_id,
    unit,
    product_vat,
    serial_no,
    AES_DECRYPT(price, '{$encryption_key}') AS price,
    product_model,
    AES_DECRYPT(cost_price, '{$encryption_key}') AS cost_price,
    product_details,
    store,
    IF(status = 1, 'Active', 'Inactive') as status_label
")
            ->from('product_information')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function create_product($query = null)
    {

        $this->db->query($query);
        return true;

       
    }


    public function update_product($query = null)
    {
        $this->db->query($query);
        return true;
    }

    public function getProductList($postData = null)
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
            $searchQuery = " (product_id like '%" . $searchValue . "%' or product_name like '%" . $searchValue . "%' or category_name like '%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        $this->db->join('product_category c', 'c.category_id = a.category_id');


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        $this->db->join('product_category c', 'c.category_id = a.category_id');

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;
        $encryption_key = Config::$encryption_key;

        ## Fetch records
        $this->db->select("
        a.id as id,
                a.product_name as product_name,
                a.product_id as product_id,c.category_name as category_name ,
                AES_DECRYPT(a.price, '{$encryption_key}') AS price ,
                AES_DECRYPT(a.cost_price, '{$encryption_key}') AS cost_price,
IF(a.status = 1, 'Active', 'Inactive') as status_label,s.name as sname");
        $this->db->from('product_information a');
        $this->db->join('store s', 's.id = a.store');

        $this->db->join('product_category c', 'c.category_id = a.category_id');


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()
        ->result();




        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            // $image = '<img src="' . $base_url . $record->image . '" class="img img-responsive" height="50" width="50">';

            if ($this->permission1->method('manage_product', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'product_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_product', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'product/product/bdtask_deleteproduct/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }

            // $button .= '  <a href="' . $base_url . 'qrcode/' . $record->id . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('qr_code') . '"><i class="fa fa-qrcode" aria-hidden="true"></i></a>';

            // $button .= '  <a href="' . $base_url . 'barcode/' . $record->id . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('barcode') . '"><i class="fa fa-barcode" aria-hidden="true"></i></a>';


            $product_name = '<a href="' . $base_url . 'product_details/' . $record->id . '">' . $record->product_name . '</a>';
            $supplier = '<a href="' . $base_url . 'supplier_ledgerinfo/' . $record->supplier_id . '">' . $record->supplier_name . '</a>';

            if($record->status_label=="Active"){
                $status='<span class="label label-success"  >'.$record->status_label.'</a>';

            }
            else{
                $status='<span class="label label-danger"  >'.$record->status_label.'</a>';

            }
            $data[] = array(
                'sl'               => $sl,
                'product_name'     => $product_name,
                'category_name'     => $record->category_name,
                'product_id'       => $record->product_id,
                'price'            =>$record->price!=""? number_format($record->price, 2, '.', ','):0,
                'cost_price'       =>$record->cost_price!=""? number_format($record->cost_price, 2, '.', ','):0,
                'sname'            =>$record->sname,
                'status'           => $status,
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


    public function delete_product($id)
    {

        $productExists = $this->db->from('stock_details')
            ->where('product', $id)
            ->count_all_results();

        if ($productExists > 0) {
            return false;
        } else {
            // No products linked, proceed to delete the category
            $this->db->where('id', $id)
            ->delete("product_information");
            return $this->db->affected_rows() > 0;
        }
    }

    public function check_product($id)
    {
        $this->db->select('*');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return FALSE;
    }

    public function bdtask_barcode_productdata($id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("id as id,
                product_name as product_name,
                product_id as product_id,
                AES_DECRYPT(price, '{$encryption_key}') AS price ,
                AES_DECRYPT(cost_price, '{$encryption_key}') AS cost_price")
            ->from('product_information')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function product_purchase_info($product_id)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("a.*,b.*,AES_DECRYPT(b.quantity, '". $encryption_key."') as quantity,
        AES_DECRYPT(b.total_price, '". $encryption_key."') as total_amount, AES_DECRYPT(a.chalan_no, '". $encryption_key."') as chalan_no,AES_DECRYPT(a.purchase_id, '". $encryption_key."') as purchase_id,
        AES_DECRYPT(c.supplier_name, '". $encryption_key."') as supplier_name");
        $this->db->from('purchase a');
        $this->db->join('purchase_details b', 'b.pid = a.id');
        $this->db->join('supplier_information c', 'c.supplier_id = a.supplier_id');
        $this->db->where('b.product', $product_id);
        $this->db->order_by('a.date', 'desc');
        $this->db->group_by('a.id');
        $this->db->limit(30);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function invoice_data($product_id)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("a.*,b.*,AES_DECRYPT(b.quantity, '". $encryption_key."') as quantity,
                AES_DECRYPT(b.total_price, '". $encryption_key."') as total_amount, AES_DECRYPT(a.sale_id, '". $encryption_key."') as sale_id,
        AES_DECRYPT(c.customer_name, '". $encryption_key."') as customer_name");
        $this->db->from('sale a');
        $this->db->join('sale_details b', 'b.pid = a.id');
        $this->db->join('customer_information c', 'c.customer_id = a.customer_id');
        $this->db->where('b.product', $product_id);
        $this->db->order_by('a.date', 'desc');
        $this->db->limit(30);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
}
