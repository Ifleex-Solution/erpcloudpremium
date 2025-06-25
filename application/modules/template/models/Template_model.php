<?php defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Template_model extends CI_Model
{

    public function setting()
    {
        return $this->db->get('web_setting')->row();
    }

    public function bdtask_company_info()
    {
        $this->db->select('*');
        $this->db->from('company_information');
        $this->db->limit('1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function bdtask_bank_list()
    {
        $this->db->select('*');
        $this->db->from('bank_add');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function out_of_stock_count()
    {

        $encryption_key = Config::$encryption_key;

        $this->db->select("
    pi.product_name,
    pi.unit,
    pc.category_name,
    (SELECT SUM(CAST(AES_DECRYPT(sd.stock, '" . $encryption_key . "') AS SIGNED)) 
     FROM stock_details sd 
     WHERE sd.product = pi.product_id) AS av_stock
");
        $this->db->from('product_information pi');
        $this->db->join('product_category pc', 'pc.category_id = pi.category_id', 'left');
        $this->db->having('av_stock <=', 21);
        $query = $this->db->get();
        $num_rows = $query->num_rows(); 
        // $result = $query->result_array();
        // $stock = 0;
        // $i = 0;
        // foreach ($result as $stockproduct) {
        //     $stokqty = $stockproduct['totalBuyQnty'] - $stockproduct['totalSalesQnty'];
        //     if ($stokqty < 10) {

        //         $stock = $stock + 1;
        //     }
        //     $i++;
        // }
        return $num_rows;
    }
}
