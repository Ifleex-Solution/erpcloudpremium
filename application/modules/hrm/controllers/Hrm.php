<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Hrm extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'hrm_model',
            'country_model'
        ));
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
    }


    public function bdtask_designation_list()
    {
        $data['title']            = display('manage_designation');
        $data['designation_list'] = $this->hrm_model->designation_list();
        $data['module']           = "hrm";
        $data['page']             = "hrm/designation_list";
        if (!$this->permission1->method('manage_designation', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_designation_form($id = null)
    {
        $data['title'] = display('add_designation');
        #-------------------------------#
        $this->form_validation->set_rules('designation', display('designation'), 'required|max_length[200]');
        $this->form_validation->set_rules('details', display('details'), 'max_length[250]');
        #-------------------------------#
        $data['designation'] = (object)$postData = [
            'id'             => $id,
            'designation'    => $this->input->post('designation', true),
            'details'        => $this->input->post('details', true),
        ];

        if (!$this->permission1->method('manage_designation', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        #-------------------------------#
        if ($this->form_validation->run() === true) {

            $base_url = base_url();


            #if empty $id then insert data
            if (empty($id)) {
                if ($this->hrm_model->create_designation($postData)) {
                    #set success message
                    echo '<script type="text/javascript">
                   alert("Designation Details Saved successfully");
                   window.location.href = "' . $base_url . 'designation_list";
                  </script>';
                } else {
                    echo '<script type="text/javascript">
                 alert("Please try again");
                 window.location.href = "' . $base_url . 'designation_list";
                </script>';
                }
            } else {
                if ($this->hrm_model->update_designation($postData)) {
                    echo '<script type="text/javascript">
                   alert("Designation Details Updated successfully");
                   window.location.href = "' . $base_url . 'designation_list";
                  </script>';
                } else {
                    echo '<script type="text/javascript">
                alert("Please try again");
                window.location.href = "' . $base_url . 'designation_list";
               </script>';
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']       = display('designation_update_form');
                $data['designation'] = $this->hrm_model->single_designation_data($id);
            }
            $data['module']      = "hrm";
            $data['page']        = "hrm/designation_form";
            echo Modules::run('template/layout', $data);
        }
    }


    public function bdtask_deletedesignation($id = null)
    {

        $base_url = base_url();

        if ($this->hrm_model->delete_designation($id)) {
            // $this->session->set_flashdata('message', display('delete_successfully'));

            echo '<script type="text/javascript">
        alert("Designation details deleted successfully");
        window.location.href = "' . $base_url . 'designation_list";
       </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Please try again");
            window.location.href = "' . $base_url . 'designation_list";
           </script>';
        }
    }


    /*employee part start*/
    public function bdtask_employee_form($id = null)
    {
        $data['title'] = display('add_employee');
        $this->form_validation->set_rules('first_name', display('first_name'), 'required|max_length[100]');
        $this->form_validation->set_rules('last_name', display('last_name'), 'required|max_length[100]');
        $this->form_validation->set_rules('designation', display('designation'), 'required|max_length[100]');
        $this->form_validation->set_rules('phone', display('phone'), 'max_length[20]');
        $this->form_validation->set_rules('hrate', display('salary'), 'max_length[20]');
        $this->load->library('fileupload');
        $img = $this->fileupload->do_upload(
            './my-assets/image/employee/',
            'image'

        );
        $old_image = $this->input->post('old_image');

        $base_url = base_url();


        if (!$this->permission1->method('manage_employee', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }


        $data['employee'] = (object)$postData = [
            'id'            => $this->input->post('id', true),
            'first_name'    => $this->input->post('first_name', true),
            'last_name'     => $this->input->post('last_name', true),
            'designation'   => $this->input->post('designation', true),
            'phone'         => $this->input->post('phone', true),
            'image'         => (!empty($img) ? $img : $old_image),
            'rate_type'     => $this->input->post('rate_type', true),
            'email'         => $this->input->post('email', true),
            'hrate'         => $this->input->post('hrate', true),
            'address_line_1' => $this->input->post('address_line_1', true),
            'address_line_2' => $this->input->post('address_line_2', true),
            'blood_group'   => $this->input->post('blood_group', true),
            'country'       => $this->input->post('country', true),
            'city'          => $this->input->post('city', true),
            'zip'           => $this->input->post('zip', true),
        ];
        #-------------------------------#
        if ($this->form_validation->run()) {

            if (empty($id)) {
                if ($this->hrm_model->create_employee($postData)) {
                    echo '<script type="text/javascript">
        alert("Employee details saved successfully");
        window.location.href = "' . $base_url . 'employee_list";
       </script>';
                } else {
                    echo '<script type="text/javascript">
                    alert("Please try again");
                    window.location.href = "' . $base_url . 'employee_list";
                   </script>';
                }
            } else {
                if ($this->hrm_model->update_employee($postData)) {
                    $old_head    =  $this->input->post('id', true) . '-' . $this->input->post('old_first_name', true) . '' . $this->input->post('old_last_name', true);
                    $up_headname = $id . '-' . $data['first_name'] . '' . $data['last_name'];
                    $updatedby   = $this->session->userdata('id');
                    $updateddate = date('Y-m-d H:i:s');
                    $coa_inf = array(
                        'HeadName'  => $up_headname,
                        'UpdateBy'  => $updatedby,
                        'UpdateDate' => $updateddate,
                    );
                    $this->db->where('HeadName', $old_head);
                    $this->db->update('acc_coa', $coa_inf);
                    echo '<script type="text/javascript">
        alert("Employee details updated successfully");
        window.location.href = "' . $base_url . 'employee_list";
       </script>';
                } else {
                    echo '<script type="text/javascript">
        alert("Please try again");
        window.location.href = "' . $base_url . 'employee_list";
       </script>';
                }
            }
        } else {
            if (!empty($id)) {
                $data['employee']    = $this->hrm_model->single_employee_data($id);
                $data['title']       = display('edit_employee');
            }
            $data['country_list'] = $this->country_model->country();
            $data['desig']       = $this->hrm_model->designation_dropdown();
            $data['module']      = "hrm";
            $data['page']        = "hrm/employee_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function bdtask_employee_list()
    {
        $data['title']        = display('manage_employee');
        $data['employee_list'] = $this->hrm_model->employee_list();
        $data['module']       = "hrm";
        $data['page']         = "hrm/employee_list";
        if (!$this->permission1->method('manage_employee', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_employee_profile($id)
    {

        $data['title']      = display('employee_profile');
        $data['row']        = $this->hrm_model->employee_details($id);
        $data['module']     = "hrm";
        $data['page']       = "hrm/resumepdf";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_delete_employee($id = null)
    {
        $base_url = base_url();

        if ($this->hrm_model->delete_employee($id)) {
            echo '<script type="text/javascript">
            alert("Employee details Deleted successfully");
            window.location.href = "' . $base_url . 'employee_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Please try again");
            window.location.href = "' . $base_url . 'employee_list";
           </script>';
        }
    }
}
