<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Product extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'product_model',
            'supplier/supplier_model'
        ));
        $this->load->library('ciqrcode');
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }

    // category part
    function bdtask_category_list()
    {
        $data['title']      = "Category List";
        $data['module']     = "product";
        $data['page']       = "category_list";
        $data["category_list"] = $this->product_model->category_list();
        if (!$this->permission1->method('manage_category', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_category_form($id = null)
    {
        $data['title'] = display('add_category');
        #-------------------------------#
        $this->form_validation->set_rules('category_name', display('category_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['category'] = (object)$postData = [
            'category_id'      => $id,
            'category_name'    => $this->input->post('category_name', true),
            'status'           => $this->input->post('status', true),
        ];

        if (!$this->permission1->method('manage_category', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_category($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Category Details Saved successfully");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Category Details Saved successfully");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                }
            } else {
                if ($this->product_model->update_category($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Category Details Updated successfully");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Category Details Updated successfully");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = display('edit_category');
                $data['category'] = $this->product_model->single_category_data($id);
            }
            $data['module']   = "product";
            $data['page']     = "category_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function bdtask_deletecategory($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_category($id)) {
            echo '<script type="text/javascript">
            alert("Category Details Deleted successfully");
            window.location.href = "' . $base_url . 'category_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this category because products are linked to it or something went wrong");
            window.location.href = "' . $base_url . 'category_list";
           </script>';
        }
    }

    // unit part
    function bdtask_unit_list()
    {
        $data['title']      = "Unit List";
        $data['module']     = "product";
        $data['page']       = "unit_list";
        $data["unit_list"] = $this->product_model->unit_list();
        if (!$this->permission1->method('manage_unit', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_unit_form($id = null)
    {
        $data['title'] = display('add_unit');
        #-------------------------------#
        $this->form_validation->set_rules('unit_name', display('unit_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['unit'] = (object)$postData = [
            'unit_id'      => $id,
            'unit_name'    => $this->input->post('unit_name', true),
            'status'       => $this->input->post('status', true),
        ];

        if (!$this->permission1->method('manage_unit', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        $base_url = base_url();
        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_unit($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Unit Details Saved successfully");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Unit Details Saved successfully");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                }
            } else {


                if ($this->product_model->update_unit($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Unit Details Updated successfully");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Unit Details Updated successfully");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = display('edit_unit');
                $data['unit'] = $this->product_model->single_unit_data($id);
            }
            $data['module']   = "product";
            $data['page']     = "unit_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function bdtask_deleteunit($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_unit($id)) {
            echo '<script type="text/javascript">
            alert("Unit Details Deleted successfully");
            window.location.href = "' . $base_url . 'unit_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Unit because products are linked to it or something went wrong");
            window.location.href = "' . $base_url . 'unit_list";
           </script>';
        }
    }

    public function getProductById()
    {
        $this->db->select('*');
        $this->db->from('product_information a');
        $this->db->where('a.product_id', $this->input->post('code'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }

    public function getActivefloorBystoreId()
    {
        $data = $this->product_model->active_floorByStore($this->input->post('id', TRUE));
        echo json_encode($data);
    }


    // product part
    public function bdtask_product_form($id = null)
    {
        $data['title'] = display('add_product');
        $data['product_open']   = null;
        #-------------------------------#
        $this->form_validation->set_rules('product_name', display('product_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('category_id', display('category'), 'required|max_length[20]');
        $this->form_validation->set_rules('unit', display('unit'), 'required');
        $this->form_validation->set_rules('status', "Status", 'required');
        $this->form_validation->set_rules('store', "Store", 'required');

        if (!$this->permission1->method('manage_product', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }


        $product_id = (!empty($this->input->post('product_id', TRUE)) ? $this->input->post('product_id', TRUE) : $this->generator(8));
        $sup_price = $this->input->post('supplier_price', TRUE);
        // $s_id      = $this->input->post('supplier_id',TRUE);
        $product_model = $this->input->post('model', TRUE);
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();




        #-------------------------------#
        $data['product'] = (object)$postData = [
            'product_id'     => $this->input->post('product_id', TRUE),
            'product_name'   => $this->input->post('product_name', TRUE),
            'category_id'    => $this->input->post('category_id', TRUE),
            'unit'           => $this->input->post('unit', TRUE),
            'product_vat'            => $this->input->post('vat', TRUE),
            'serial_no'      => $this->input->post('serial_no', TRUE),
            'price'          => $this->input->post('price', TRUE) > 0 ? $this->input->post('price', TRUE) : 0.0,
            'product_model'  => $this->input->post('model', TRUE),
            'cost_price'  => $this->input->post('cost_price', TRUE),

            'product_details' => $this->input->post('description', TRUE),
            'store'  => $this->input->post('store', TRUE),
            // 'floor' => $this->input->post('floor', TRUE),
            // 'product_vat'    => $this->input->post('product_vat', TRUE),
            // 'image'          => (!empty($image) ? $image : 'my-assets/image/product.png'),
            'status'         => $this->input->post('status', TRUE),
        ];

        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;
        if ($num_column > 0) {
            $txf = [];
            for ($i = 0; $i < $num_column; $i++) {
                $txf[$i] = 'tax' . $i;
            }
            foreach ($txf as $key => $value) {
                $postData[$value] = (!empty($this->input->post($value)) ? $this->input->post($value) : 0) / 100;
            }
        }
        $encryption_key = Config::$encryption_key;



        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                $query = "
    INSERT INTO product_information 
    (product_id, product_name, category_id, unit, product_vat, serial_no, price, product_model, cost_price, product_details, store, status) 
    VALUES 
    ('{$this->input->post('product_id', TRUE)}',
     '{$this->input->post('product_name', TRUE)}',
     '{$this->input->post('category_id', TRUE)}',
     '{$this->input->post('unit', TRUE)}',
     '{$this->input->post('vat', TRUE)}',
     '{$this->input->post('serial_no', TRUE)}',
     AES_ENCRYPT('{$this->input->post('price', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('model', TRUE)}',
     AES_ENCRYPT('{$this->input->post('cost_price', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('description', TRUE)}',
     '{$this->input->post('store', TRUE)}',
     '{$this->input->post('status', TRUE)}'
    );";


                if ($this->product_model->create_product($query)) {
                    if ($this->input->post('button', true) == "add-another") {
                        echo '
                        <script type="text/javascript">
                        alert("Product Details Saved successfully");
                        window.location.href = "' . $base_url . 'product_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Product Details Saved successfully");
                        window.location.href = "' . $base_url . 'product_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if ($this->input->post('button', true) == "add-another") {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'product_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'product_list";
                       </script>';
                    }
                }
            } else {
                $query = "
    UPDATE product_information 
    SET 
        product_name = '{$this->input->post('product_name', TRUE)}',
        category_id = '{$this->input->post('category_id', TRUE)}',
        unit = '{$this->input->post('unit', TRUE)}',
        product_vat = '{$this->input->post('vat', TRUE)}',
        serial_no = '{$this->input->post('serial_no', TRUE)}',
        price = AES_ENCRYPT('{$this->input->post('price', TRUE)}', '{$encryption_key}'),
        product_model = '{$this->input->post('model', TRUE)}',
        cost_price = AES_ENCRYPT('{$this->input->post('cost_price', TRUE)}', '{$encryption_key}'),
        product_details = '{$this->input->post('description', TRUE)}',
        store = '{$this->input->post('store', TRUE)}',
        status = '{$this->input->post('status', TRUE)}'
    WHERE id = '{$id}';
";
                if ($this->product_model->update_product($query)) {
                    if ($this->input->post('button', true) == "add-another") {
                        echo '
        <script type="text/javascript">
        alert("Product Details Updated successfully");
        window.location.href = "' . $base_url . 'product_form";
       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
        alert("Product Details Updated successfully");
        window.location.href = "' . $base_url . 'product_list";
       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if ($this->input->post('button', true) == "add-another") {
                        echo '
        <script type="text/javascript">
        alert("' . $message . '");
        window.location.href = "' . $base_url . 'product_form";
       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
        alert("' . $message . '");
        window.location.href = "' . $base_url . 'product_list";
       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']         = display('edit_product');
                $data['product']       = $this->product_model->single_product_data($id);
            } else {
                $sql3 = "SELECT MAX(product_id)+1 AS highest_product_id FROM product_information;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['productId'] = !empty($result3->highest_product_id) ? str_pad($result3->highest_product_id, 6, '0', STR_PAD_LEFT) : "000001";
            }
            $data['supplier']      = $this->product_model->supplier_list();
            $data['vattaxinfo']    = $this->product_model->vat_tax_setting();
            $data['id']            =  $id;
            $data['category_list'] = $this->product_model->active_category();
            $data['store_list'] = $this->product_model->active_store();
            $data['unit_list']     = $this->product_model->active_unit();
            $data['supplier_pr']   = $this->product_model->supplier_product_list($id);
            $data['product_open']   = $this->product_model->product_opening($id);
            $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
            $data['taxfield']      = $taxfield;
            $data['module']        = "product";
            $data['page']          = "product_form";

            echo Modules::run('template/layout', $data);
        }
    }




    public function bdtask_product_list()
    {
        $data['title']         = display('manage_product');
        $data['total_product'] = $this->db->count_all("product_information");
        $data['module']        = "product";
        $data['page']          = "product_list";
        if (!$this->permission1->method('manage_product', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function CheckProductList()
    {
        $postData = $this->input->post();
        $data = $this->product_model->getProductList($postData);
        echo json_encode($data);
    }

    public function bdtask_deleteproduct($id = null)
    {
        $base_url = base_url();
        if ($this->product_model->delete_product($id)) {
            echo '<script type="text/javascript">
            alert("Product Details Deleted successfully");
            window.location.href = "' . $base_url . 'product_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Product because this product is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'product_list";
           </script>';
        }
    }

    public function bdtask_csv_product()
    {
        $data['title']         = display('add_product_csv');
        $data['module']        = "product";
        $data['page']          = "add_product_csv";
        if (!$this->permission1->method('manage_product', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    function uploadCsv()
    {
        $filename = $_FILES['upload_csv_file']['name'];
        $basenameAndExtension = explode('.', $filename);
        $ext = end($basenameAndExtension);
        if ($ext == 'csv') {
            $count = 0;
            $fp = fopen($_FILES['upload_csv_file']['tmp_name'], 'r') or die("can't open file");

            if (($handle = fopen($_FILES['upload_csv_file']['tmp_name'], 'r')) !== FALSE) {

                while ($csv_line = fgetcsv($fp, 1024)) {
                    //keep this if condition if you want to remove the first row
                    for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                        $product_id = $this->generator(10);
                        $insert_csv = array();
                        $insert_csv['supplier_id'] = (!empty($csv_line[1]) ? $csv_line[1] : null);
                        $insert_csv['product_name'] = (!empty($csv_line[2]) ? $csv_line[2] : null);
                        $insert_csv['product_model'] = (!empty($csv_line[3]) ? $csv_line[3] : null);
                        $insert_csv['category_id'] = (!empty($csv_line[4]) ? $csv_line[4] : null);
                        $insert_csv['price'] = (!empty($csv_line[5]) ? $csv_line[5] : null);
                        $insert_csv['supplier_price'] = (!empty($csv_line[6]) ? $csv_line[6] : null);
                        $insert_csv['opening_stock'] = (!empty($csv_line[7]) ? $csv_line[7] : null);
                        $insert_csv['opening_batch'] = (!empty($csv_line[8]) ? $csv_line[8] : null);
                    }
                    // $check_supplier = $this -> db -> select('*') -> from('supplier_information') -> where('supplier_name', $insert_csv['supplier_id']) -> get() -> row();
                    // if (!empty($check_supplier)) {
                    //     $supplier_id = $check_supplier -> supplier_id;
                    // } else {
                    //     $supplierinfo = array(
                    //         'supplier_name' => $insert_csv['supplier_id'],
                    //         'address'           => '',
                    //         'mobile'            => '',
                    //         'details'           => '',
                    //         'status'            => 1
                    //     );
                    //     if ($count > 0) {
                    //         $this -> db -> insert('supplier_information', $supplierinfo);
                    //     }
                    //     $supplier_id = $this -> db -> insert_id();
                    //     $coa = $this -> supplier_model -> headcode();
                    //     if ($coa -> HeadCode != NULL) {
                    //         $headcode = $coa -> HeadCode + 1;
                    //     }
                    //     else {
                    //         $headcode = "21110000001";
                    //     }
                    //     $c_acc = $supplier_id.'-'.$insert_csv['supplier_id'];
                    //     $createby = $this -> session -> userdata('id');
                    //     $createdate = date('Y-m-d H:i:s');


                    //     $supplier_coa = [
                    //         'HeadCode'         => $headcode,
                    //         'HeadName'         => $c_acc,
                    //         'PHeadName'        => 'Suppliers',
                    //         'HeadLevel'        => '3',
                    //         'IsActive'         => '1',
                    //         'IsTransaction'    => '1',
                    //         'IsGL'             => '0',
                    //         'HeadType'         => 'L',
                    //         'IsBudget'         => '0',
                    //         'IsDepreciation'   => '0',
                    //         'supplier_id'      => $supplier_id,
                    //         'DepreciationRate' => '0',
                    //         'CreateBy'         => $createby,
                    //         'CreateDate'       => $createdate,
                    //     ];

                    //     if ($count > 0) {
                    //         $this -> db -> insert('acc_coa', $supplier_coa);
                    //     }
                    // }

                    $check_category = $this->db->select('*')->from('product_category')->where('category_name', $insert_csv['category_id'])->get()->row();
                    if (!empty($check_category)) {
                        $category_id = $check_category->category_id;
                    } else {
                        $categorydata = array(
                            'category_name'         => $insert_csv['category_id'],
                            'status'                => 1
                        );
                        if ($count > 0) {
                            $this->db->insert('product_category', $categorydata);
                            $category_id = $this->db->insert_id();
                        }
                    }
                    $data = array(
                        'product_id'    => $product_id,
                        'category_id'   => $category_id,
                        'product_name'  => $insert_csv['product_name'],
                        'product_model' => $insert_csv['product_model'],
                        'price'         => $insert_csv['price'],
                        'unit'          => '',
                        'tax'           => '',
                        'product_details' => 'Csv Product',
                        'image'         => 'my-assets/image/product.png',
                        'status'        => 1
                    );

                    if ($count > 0) {

                        $result = $this->db->select('*')
                            ->from('product_information')
                            ->where('product_name', $data['product_name'])
                            ->where('product_model', $data['product_model'])
                            ->where('category_id', $category_id)
                            ->get()
                            ->row();
                        if (empty($result)) {
                            $this->db->insert('product_information', $data);
                            $product_id = $product_id;
                        } else {
                            $product_id = $result->product_id;
                            $udata = array(
                                'product_id'     => $result->product_id,
                                'category_id'    => $category_id,
                                'product_name'   => $result->product_name,
                                'product_model'  => $insert_csv['product_model'],
                                'price'          => $insert_csv['price'],
                                'unit'           => '',
                                'tax'            => '',
                                'product_details' => 'Csv Uploaded Product',
                                'image'         => 'my-assets/image/product.png',
                                'status'        => 1
                            );
                            $this->db->where('product_id', $result->product_id);
                            $this->db->update('product_information', $udata);
                        }

                        $supp_prd = array(
                            'product_id'     => $product_id,
                            'supplier_id'    => 1,
                            'supplier_price' => $insert_csv['supplier_price'],
                            'products_model' => $insert_csv['product_model'],
                        );

                        $splprd = $this->db->select('*')
                            ->from('supplier_product')
                            ->where('supplier_id', 1)
                            ->where('product_id', $product_id)
                            ->get()
                            ->num_rows();

                        if ($splprd == 0) {
                            $this->db->insert('supplier_product', $supp_prd);
                        } else {
                            $supp_prd = array(
                                'supplier_id'    => 1,
                                'supplier_price' => $insert_csv['supplier_price'],
                                'products_model' => $insert_csv['product_model']
                            );
                            $this->db->where('product_id', $product_id);
                            $this->db->where('supplier_id', 1);
                            $this->db->update('supplier_product', $supp_prd);
                        }



                        $data1 = array(
                            'product_id'         => $product_id,
                            'quantity'           => $insert_csv['opening_stock'],
                            'batch_id'           =>  $insert_csv['opening_batch'],
                            'status'             => 1
                        );
                        $this->db->insert('product_purchase_details', $data1);
                    }
                    $count++;
                }
            }

            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('product_list'));
        } else {
            $this->session->set_flashdata(array('error_message' => 'Please Import Only Csv File'));
            redirect(base_url('bulk_products'));
        }
    }




    public function qrgenerator($product_id)
    {
        $config['cacheable'] = true; //boolean, the default is true
        $config['cachedir'] = ''; //string, the default is application/cache/
        $config['errorlog'] = ''; //string, the default is application/logs/
        $config['quality'] = true; //boolean, the default is true
        $config['size'] = '1024'; //interger, the default is 1024
        $config['black'] = array(224, 255, 255); // array, default is 
        $config['white'] = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);
        $params['data'] = $product_id;
        $params['level'] = 'H';
        $params['size'] = 10;
        $image_name = $product_id . '.png';
        $params['savename'] = FCPATH . 'my-assets/image/qr/' . $image_name;
        $this->ciqrcode->generate($params);
        $product_info = $this->product_model->bdtask_barcode_productdata($product_id);
        $data = array(
            'title'           => display('qr_code'),
            'product_name'    => $product_info[0]['product_name'],
            'product_model'   => $product_info[0]['product_model'],
            'price'           => $product_info[0]['price'],
            'product_details' => $product_info[0]['product_details'],
            'qr_image'        => $image_name,
        );
        $data['module']        = "product";
        $data['page']          = "barcode_print_page";
        echo modules::run('template/layout', $data);
    }


    // bar code part
    public function barcode_print($product_id)
    {
        $product_info = $this->product_model->bdtask_barcode_productdata($product_id);

        $data = array(
            'title'           => display('barcode'),
            'product_id'      => $product_id,
            'product_name'    => $product_info[0]['product_name'],
            'product_model'   => $product_info[0]['product_model'],
            'price'           => $product_info[0]['price'],
            'product_details' => $product_info[0]['product_details'],

        );

        $data['module']        = "product";
        $data['page']          = "barcode_print_page";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_product_details($product_id = null)
    {
        $details_info = $this->product_model->bdtask_barcode_productdata($product_id);
        $purchaseData = $this->product_model->product_purchase_info($product_id);
        $totalPurchase = 0;
        $totalPrcsAmnt = 0;

        if (!empty($purchaseData)) {
            foreach ($purchaseData as $k => $v) {
                $purchaseData[$k]['final_date'] = $purchaseData[$k]['date'];
                $totalPrcsAmnt = ($totalPrcsAmnt + $purchaseData[$k]['total_amount']);
                $totalPurchase = ($totalPurchase + $purchaseData[$k]['quantity']);
            }
        }

        $salesData = $this->product_model->invoice_data($product_id);

        $totalSales = 0;
        $totaSalesAmt = 0;
        if (!empty($salesData)) {
            foreach ($salesData as $k => $v) {
                $salesData[$k]['final_date'] = $salesData[$k]['date'];
                $totalSales = ($totalSales + $salesData[$k]['quantity']);
                $totaSalesAmt = ($totaSalesAmt + $salesData[$k]['total_amount']);
            }
        }

        $stock = ($totalPurchase - $totalSales);
        $data = array(
            'title'               => display('product_details'),
            'product_name'        => $details_info[0]['product_name'],
            'product_model'       => $details_info[0]['product_model'],
            'price'               => $details_info[0]['price'],
            'purchaseTotalAmount' => number_format($totalPrcsAmnt, 2, '.', ','),
            'salesTotalAmount'    => number_format($totaSalesAmt, 2, '.', ','),
            'img'                 => $details_info[0]['image'],
            'total_purchase'      => $totalPurchase,
            'total_sales'         => $totalSales,
            'purchaseData'        => $purchaseData,
            'salesData'           => $salesData,
            'stock'               => $stock,
        );

        $data['module']        = "product";
        $data['page']          = "product_details";
        echo modules::run('template/layout', $data);
    }


    public function generator($lenth)
    {
        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 9);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }
}
