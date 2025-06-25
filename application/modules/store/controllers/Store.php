<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Store extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'store_model'
        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }




    // branch part
    public function bdtask_branchlist()
    {
        if (!$this->permission1->method('branch_list', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']      = display('branch_list');
        $data['module']     = "store";
        $data['page']       = "branch_list";
        echo modules::run('template/layout', $data);
    }


    public function checkbranchList()
    {
        $postData = $this->input->post();
        $data = $this->store_model->branchlist($postData);
        echo json_encode($data);
    }


    public function bdtask_branch_form($id = null)
    {
        $data['title'] = display('add_branch');
        #-------------------------------#
        $this->form_validation->set_rules('name', display('name'), 'required');
        $this->form_validation->set_rules('code', "Code", 'required');
        $this->form_validation->set_rules('status', "Status", 'required');
        $encryption_key = Config::$encryption_key;

        if (!$this->permission1->method('branch_list', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }


        #-------------------------------#

        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {

                $branch_query = "
    INSERT INTO branch 
    (code, name ,status) 
    VALUES (
        '{$this->input->post('code', TRUE)}',
        AES_ENCRYPT('{$this->input->post('name', TRUE)}', '{$encryption_key}'),
        '{$this->input->post('status', TRUE)}'
    );
";
                if ($this->store_model->create_branch($branch_query)) {
                    if ($this->input->post('button', true) === "add-another") {
                        echo '
                    <script type="text/javascript">
                    alert("Branch Details Saved successfully");
                    window.location.href = "' . $base_url . 'branch_form";
                   </script>';
                    } else {
                        echo '
                    <script type="text/javascript">
                    alert("Branch Details Saved successfully");
                    window.location.href = "' . $base_url . 'branch_list";
                   </script>';
                    }
                } else {
                    echo '
                        <script type="text/javascript">
                        alert("error");
                        window.location.href = "' . $base_url . 'branch_form";
                       </script>';
                }
            } else {

                $branch_query = "
    UPDATE branch SET
        code = '{$this->input->post('code', TRUE)}',
        name = AES_ENCRYPT('{$this->input->post('name', TRUE)}', '{$encryption_key}'),
        status = '{$this->input->post('status', TRUE)}'
    WHERE id = '{$id}';
";

                if ($this->store_model->update_branch($branch_query)) {
                    if ($this->input->post('button', true) === "add-another") {
                        echo '
                    <script type="text/javascript">
                    alert("Branch Details Updated successfully");
                    window.location.href = "' . $base_url . 'branch_form";
                   </script>';
                    } else {
                        echo '
                    <script type="text/javascript">
                    alert("Branch Details Updated successfully");
                    window.location.href = "' . $base_url . 'branch_list";
                   </script>';
                    }
                } else {
                    echo '
                        <script type="text/javascript">
                        alert("error");
                        window.location.href = "' . $base_url . 'branch_form";
                       </script>';
                }
            }
        } else {

            if (!empty($id)) {
                $data['title']         = display('edit_branch');
                $data['branch']       = $this->store_model->single_branch_data($id);
            } else {
                $sql3 = "SELECT MAX(code)+1 AS highest_branch_id FROM branch;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['branchid'] = !empty($result3->highest_branch_id) ? str_pad($result3->highest_branch_id, 6, '0', STR_PAD_LEFT) : "000001";
            }



            $data['module']   = "store";
            $data['page']     = "branch_form";
            echo Modules::run('template/layout', $data);
        }
    }

    public function getbranchById()
    {
        $this->db->select('*');
        $this->db->from('branch a');
        $this->db->where('a.code', $this->input->post('code'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }


    public function bdtask_deletebranch($id = null)
    {

        $base_url = base_url();

        if ($this->store_model->delete_branch($id)) {
            echo '<script type="text/javascript">
            alert("Branch Details Deleted successfully");
                    window.location.href = "' . $base_url . 'branch_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Something went wrong or The branch record is already referenced in another record.");
                    window.location.href = "' . $base_url . 'branch_list";
           </script>';
        }
    }








    // storetype part
    public function bdtask_storetypelist()
    {
        $data['title']      = display('storetypelist');
        $data['module']     = "store";
        $data['page']       = "storetype_list";
        echo modules::run('template/layout', $data);
    }


    public function checkStoretypeList()
    {
        $postData = $this->input->post();
        $data = $this->store_model->storetypelist($postData);
        echo json_encode($data);
    }


    public function bdtask_storetype_form($id = null)
    {
        $data['title'] = display('add_storetype');
        #-------------------------------#
        $this->form_validation->set_rules('name', display('name'), 'required');
        $this->form_validation->set_rules('code', "Code", 'required');
        $this->form_validation->set_rules('status', "Status", 'required');

        #-------------------------------#
        $data['storetype'] = (object)$postData = [
            'id'      => $id,
            'code'    => $this->input->post('code', true),
            'name'    => $this->input->post('name', true),
            'status'           => $this->input->post('status', true),
        ];


        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->store_model->create_storetype($postData)) {
                    #set success message
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                if ($this->input->post('button', true) === "add-another") {
                    redirect(base_url('storetype_form'));
                    exit;
                } else {
                    redirect("storetypelist");
                }
            } else {
                if ($this->store_model->update_storetype($postData)) {
                    $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                if ($this->input->post('button', true) === "add-another") {
                    redirect(base_url('storetype_form'));
                    exit;
                } else {
                    redirect("storetypelist");
                }
            }
        } else {

            if (!empty($id)) {
                $data['title']         = "Edit Store Type";
                $data['storetype']       = $this->store_model->single_storetype_data($id);
            } else {
                $sql3 = "SELECT MAX(code)+1 AS highest_storetype_id FROM storetype;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['storetypeid'] = !empty($result3->highest_storetype_id) ? str_pad($result3->highest_storetype_id, 6, '0', STR_PAD_LEFT) : "000001";
            }



            $data['module']   = "store";
            $data['page']     = "storetype_form";
            echo Modules::run('template/layout', $data);
        }
    }

    public function getStoreTypeById()
    {
        $this->db->select('*');
        $this->db->from('storetype a');
        $this->db->where('a.code', $this->input->post('code'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }


    public function bdtask_deletestoretype($id = null)
    {

        if ($this->store_model->delete_storetype($id)) {
            $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception', "Something went wrong or The store type record is already referenced in another record.");
        }



        redirect("storetypelist");
    }






    // floor part
    public function bdtask_floorlist()
    {
        $data['title']      = display('floorlist');
        $data['module']     = "store";
        $data['page']       = "floor_list";
        echo modules::run('template/layout', $data);
    }


    public function checkfloorList()
    {
        $postData = $this->input->post();
        $data = $this->store_model->floorlist($postData);
        echo json_encode($data);
    }


    public function bdtask_floor_form($id = null)
    {
        $data['title'] = display('add_floor');
        #-------------------------------#
        $this->form_validation->set_rules('name', display('name'), 'required');
        $this->form_validation->set_rules('code', "Code", 'required');
        $this->form_validation->set_rules('status', "Status", 'required');

        #-------------------------------#
        $data['floor'] = (object)$postData = [
            'id'      => $id,
            'code'    => $this->input->post('code', true),
            'name'    => $this->input->post('name', true),
            'status'           => $this->input->post('status', true),
        ];


        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->store_model->create_floor($postData)) {
                    #set success message
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                if ($this->input->post('button', true) === "add-another") {
                    redirect(base_url('floor_form'));
                    exit;
                } else {
                    redirect("floorlist");
                }
            } else {
                if ($this->store_model->update_floor($postData)) {
                    $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                if ($this->input->post('button', true) === "add-another") {
                    redirect(base_url('floor_form'));
                    exit;
                } else {
                    redirect("floorlist");
                }
            }
        } else {

            if (!empty($id)) {
                $data['title']         = "Edit Floor";
                $data['floor']       = $this->store_model->single_floor_data($id);
            } else {
                $sql3 = "SELECT MAX(code)+1 AS highest_floor_id FROM floor;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['floorid'] = !empty($result3->highest_floor_id) ? str_pad($result3->highest_floor_id, 6, '0', STR_PAD_LEFT) : "000001";
            }



            $data['module']   = "store";
            $data['page']     = "floor_form";
            echo Modules::run('template/layout', $data);
        }
    }

    public function getFloorById()
    {
        $this->db->select('*');
        $this->db->from('floor a');
        $this->db->where('a.code', $this->input->post('code'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }


    public function bdtask_deletefloor($id = null)
    {

        if ($this->store_model->delete_floor($id)) {
            $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception', "Something went wrong or The floor record is already referenced in another record.");
        }

        redirect("floorlist");
    }












    // store part
    public function bdtask_storelist()
    {
        $data['title']      = display('storelist');
        $data['module']     = "store";
        $data['page']       = "store_list";
        if (!$this->permission1->method('storelist', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function checkstoreList()
    {
        $postData = $this->input->post();
        $data = $this->store_model->storelist($postData);
        echo json_encode($data);
    }


    public function bdtask_store_form($id = null)
    {
        $data['title'] = display('add_store');
        #-------------------------------#
        $this->form_validation->set_rules('name', display('name'), 'required');
        $this->form_validation->set_rules('code', "Code", 'required');
        $this->form_validation->set_rules('status', "Status", 'required');
        $this->form_validation->set_rules('auto_grn', "Auto GRN", 'required');
        $this->form_validation->set_rules('auto_gdn', "Auto GDN", 'required');

        #-------------------------------#
        $data['store'] = (object)$postData = [
            'id'      => $id,
            'code'    => $this->input->post('code', true),
            'name'    => $this->input->post('name', true),
            'status'           => $this->input->post('status', true),
            'auto_grn'           => $this->input->post('auto_grn', true),
            'auto_gdn'           => $this->input->post('auto_gdn', true)

        ];

        if (!$this->permission1->method('storelist', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {


                $inserted_id = $this->store_model->create_store($postData);

                if ($this->input->post('button', true) === "add-another") {
                    echo '
                    <script type="text/javascript">
                    alert("Store Details Saved successfully");
                    window.location.href = "' . $base_url . 'store_form";
                   </script>';
                    exit;
                } else {

                    echo '<script type="text/javascript">
                    alert("Store Details Saved successfully");
                    window.location.href = "' . $base_url . 'storelist";
                   </script>';
                }
            } else {
                $this->store_model->update_store($postData);
                if ($this->input->post('button', true) === "add-another") {
                    // redirect(base_url('store_form'));
                    echo '<script type="text/javascript">
                    alert("Store Details Saved successfully");
                    window.location.href = "' . $base_url . 'store_form";
                   </script>';
                    exit;
                } else {

                    echo '<script type="text/javascript">
                    alert("Store Details Saved successfully");
                    window.location.href = "' . $base_url . 'storelist";
                   </script>';
                }
            }
        } else {

            if (!empty($id)) {
                $data['title']         = "Edit Store";
                $data['store']       = $this->store_model->single_store_data($id);
            } else {
                $sql3 = "SELECT MAX(code)+1 AS highest_store_id FROM store;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['storeid'] = !empty($result3->highest_store_id) ? str_pad($result3->highest_store_id, 6, '0', STR_PAD_LEFT) : "000001";
            }

            $data['module']   = "store";
            $data['page']     = "store_form";
            echo Modules::run('template/layout', $data);
        }
    }

    public function getStoreById()
    {
        $this->db->select('*');
        $this->db->from('store a');
        $this->db->where('a.code', $this->input->post('code'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }


    public function bdtask_deletestore($id = null)
    {
        $base_url = base_url();

        if ($this->store_model->delete_store($id)) {
            echo '<script type="text/javascr ipt">
            alert("Store Details Saved successfully");
            window.location.href = "' . $base_url . 'storelist";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Store Details Saved successfully");
            window.location.href = "' . $base_url . 'storelist";
           </script>';
        }
    }

    public function getbranchbyuserid()
    {
        $encryption_key = Config::$encryption_key;
        if ($this->session->userdata('user_level2') == 1) {
            $branch_reult = $this->db->select("id,AES_DECRYPT(name, '{$encryption_key}') AS name,0 as default")
                     ->from('branch')
                     ->where('status', 1)
                     ->get()
                      ->result();
             echo json_encode($branch_reult);

        } else {
            $branch_reult = $this->db->select("branch.id,AES_DECRYPT(branch.name, '{$encryption_key}') AS name,sec_branch.default")
                ->from('sec_branch')
                ->join('branch', 'branch.id=sec_branch.branchid')
                ->where('sec_branch.userid', $this->session->userdata('id'))
                ->group_by('sec_branch.branchid')
                ->get()
                ->result();
            echo json_encode($branch_reult);
        }
    }

    public function getstorebyuserid()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $branch_reult = $this->db->select("id,name,0 as default")
                     ->from('store')
                     ->where('status', 1)
                     ->get()
                      ->result();
             echo json_encode($branch_reult);

        } else {
            $store_reult =  $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->group_by('sec_store.storeid')
                ->get()
                ->result();
            echo json_encode($store_reult);
        }
    }
}
