<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Stock extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'stock_model',
            'store/store_model',
            'product/product_model',
            'service/service_model',
            'purchase/purchase_model',

        ));

        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }




    // stock batch part
    public function bdtask_stockbatchlist()
    {
        $data['title']      = display('stockbatchlist');
        $data['module']     = "stock";
        $data['page']       = "stockbatch_list";
        echo modules::run('template/layout', $data);
    }


    public function checkstockbatchList()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->stockbatchlist($postData);
        echo json_encode($data);
    }


    public function bdtask_stockbatch_form($id = null)
    {
        $data['title'] = display('add_stockbatch');
        #-------------------------------#
        $this->form_validation->set_rules('batchid', "Batch Id", 'required');
        $this->form_validation->set_rules('batchid', "Batch Id", 'required');
        //  $this->form_validation->set_rules('details', "Details", 'required');

        #-------------------------------#



        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                $data['stockbatch'] = (object)$postData = [
                    'id'      => $id,
                    'batchid'    => $this->input->post('batchid', true),
                    'details'    => $this->input->post('details', true),
                    'opening_stock_used' => 'no',
                    'status'           => $this->input->post('status', true),
                ];
                if ($this->stock_model->create_stockbatch($postData)) {
                    #set success message
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                if ($this->input->post('button', true) === "add-another") {
                    redirect(base_url('stockbatch_form'));
                    exit;
                } else {
                    redirect("stockbatchlist");
                }
            } else {
                $data['stockbatch'] = (object)$postData = [
                    'id'      => $id,
                    'batchid'    => $this->input->post('batchid', true),
                    'details'    => $this->input->post('details', true),
                    'status'           => $this->input->post('status', true),
                ];
                if ($this->stock_model->update_stockbatch($postData)) {
                    $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
                if ($this->input->post('button', true) === "add-another") {
                    redirect(base_url('stockbatch_form'));
                    exit;
                } else {
                    redirect("stockbatchlist");
                }
            }
        } else {

            if (!empty($id)) {
                $data['title']         = "Edit Stock Batch";
                $data['stockbatch']       = $this->stock_model->single_stockbatch_data($id);
            }
            //   else {
            //      $sql3 = "SELECT MAX(code)+1 AS highest_location_id FROM location;";
            //      $query3 = $this->db->query($sql3);
            //      $result3 = $query3->row();
            //      $data['stockbatchid'] = !empty($result3->highest_stockbatch_id) ? str_pad($result3->highest_stockbatch_id, 6, '0', STR_PAD_LEFT) : "000001";
            //  }



            $data['module']   = "stock";
            $data['page']     = "stockbatch_form";
            echo Modules::run('template/layout', $data);
        }
    }

    public function getStockBatchById()
    {
        $this->db->select('*');
        $this->db->from('stockbatch a');
        $this->db->where('a.batchid', $this->input->post('batchid'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }


    public function bdtask_deletestockbatch($id = null)
    {


        if ($this->stock_model->delete_stockbatch($id)) {
            $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception', "Something went wrong or The stockbatch record is already referenced in another record.");
        }

        redirect("stockbatchlist");
    }










    //Opening stock part


    public function bdtask_openingstockbatchlist()
    {
        $data['title']      = display('openingstockbatchlist');
        $data['module']     = "stock";
        $data['page']       = "openingstock_list";
        echo modules::run('template/layout', $data);
    }


    public function checkopeningstockbatchList()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->openingstockbatchlist($postData);
        echo json_encode($data);
    }

    public function bdtask_openingstock_form($id = null)
    {
        $data = array(
            'title'         => display('add_openingstock'),
        );
        $data["batches"] = $this->active_batch();
        $data['products'] = $this->active_product();
        $data['floor_list'] = $this->active_floor();
        $data['store_list'] = $this->product_model->active_store();

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "openingstock_form";

        if ($id != null) {

            $data['title'] = "Edit Opening Stock";
        }
        echo modules::run('template/layout', $data);
    }

    public function active_batch()
    {
        $this->db->select('*');
        $this->db->from('stockbatch');
        $this->db->where('status', 1);
        $this->db->where('opening_stock_used', 'no');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function active_product()
    {
        $this->db->select('id,product_name');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function stocktype($id = null)
    {
        $this->db->select('stocktype,type');
        $this->db->from('adj_stock');
        $this->db->where('id', $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    public function active_productbyfloorandstore()
    {
        $this->db->select('pi.id, pi.product_name, sd.floor, sd.store');
        $this->db->from('stock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->where('pi.status', 1);
        $this->db->group_by('pi.id, sd.floor, sd.store');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }




    public function active_floor()
    {
        $this->db->select('st.id, st.name,s.id as storeid');
        $this->db->from('store s');
        $this->db->join('storefloor sf', 'sf.store = s.id');
        $this->db->join('floor st', 'st.id = sf.floor');
        $this->db->where('st.status', 1);


        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }



    public function getProductById()
    {
        $this->db->select('*');
        $this->db->from('product_information a');
        $this->db->where('a.id', $this->input->post('productid'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }



    public function getOpeningStockById()
    {

        $encryption_key = Config::$encryption_key;

        $this->db->select('sb.batchid,b.batch_id, b.product, b.store, b.floor, a.date AS date, a.details AS details, 
        AES_DECRYPT( b.actualstock , "' . $encryption_key . '") AS actualstock, pi.unit, 
    (SELECT   SUM(AES_DECRYPT( c.actualstock , "' . $encryption_key . '")) AS actualstock 
     FROM stock_details c 
     WHERE c.batch_id = b.batch_id 
       AND b.product = c.product
       AND b.store = c.store 
       AND b.floor = c.floor) AS avstock');
        $this->db->from('ope_stock a');
        $this->db->join('stock_details b', 'b.pid = a.id');
        $this->db->join('stockbatch sb', 'sb.id = b.batch_id');

        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'opening_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }

        return false;
    }

    public function save_openingstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;



        $data = array(
            'id' => 0,
            'date'  => $items[0]['date'],
            'details' => $items[0]['details']
        );


        $this->db->insert('ope_stock', $data);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {


            $query = "
    INSERT INTO stock_details 
    (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
    VALUES 
    (0, 
     '{$item['batch']}', 
     '{$item['product']}', 
     '{$item['store']}', 
     '{$item['floor']}', 
     AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
      AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
    AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
     'opening_stock', 
     '{$inserted_id}'
    );
";
            $this->db->query($query);
        }

        $this->db->where('id', $items[0]['batch'])
            ->update('stockbatch', ['opening_stock_used' => "yes"]);

        echo json_encode("Success");
    }


    public function update_openingstock()
    {
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);
        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'opening_stock')
            ->delete('stock_details');

        foreach ($items as $item) {
            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['store']}', 
             '{$item['floor']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
             'opening_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);
        }

        $this->db->where('id', $items[0]['batch'])
            ->update('stockbatch', ['opening_stock_used' => "yes"]);

        echo json_encode("Success");
    }

    public function delete_opetock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'opening_stock')
            ->delete('stock_details');

        $this->db->where('id', $id)
            ->delete('opening_stock');

        redirect("openingstockbatchlist");
    }



    //Stock Disposal part

    public function bdtask_stockdisposalnote_form($id = null)
    {
        $data = array(
            'title'         => display('new_stockdisposalnote'),
        );
        $data["batches"] = $this->active_batches();
        $data['products'] = $this->active_product();
        $data['floor_list'] = $this->active_floor();
        $data['store_list'] = $this->product_model->active_store();

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "stockdisposalnote_form";

        if ($id != null) {

            $data['title'] = "Edit Stock Disposal Note";
        }
        echo modules::run('template/layout', $data);
    }

    public function active_batches()
    {
        $this->db->select('*');
        $this->db->from('stockbatch');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function stock_batchdetailsbyprodid()
    {
        $this->db->select('sb.id, sb.batchid, pi.product_name,pi.unit');
        $this->db->from('stock_details sd');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sb.status', 1);

        $this->db->group_by('sb.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function stock_batchdetailsbyprodidandstorefloor()
    {
        $this->db->select('sb.id, sb.batchid, pi.product_name,pi.unit');
        $this->db->from('stock_details sd');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sd.store', $this->input->post('store', TRUE));
        $this->db->where('sd.floor', $this->input->post('floor', TRUE));
        $this->db->where('sb.status', 1);

        $this->db->group_by('sb.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function store_detailsbybatchidandprodid()
    {
        $this->db->select('s.id, s.name,pi.unit');
        $this->db->from('stock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        // $this->db->where('sb.id', $this->input->post('batchid', TRUE));
        $this->db->where('s.status', 1);
        $this->db->group_by('s.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getproduct()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("pi.product_id,
    pi.product_name,
    pi.category_id,
    pi.unit,
    pi.product_vat,
    pi.serial_no,
    AES_DECRYPT(pi.price, '{$encryption_key}') AS price,
    pi.product_model,
    AES_DECRYPT(pi.cost_price, '{$encryption_key}') AS cost_price,
    pi.product_details,
    pi.store");
        $this->db->from('product_information pi');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function getVoucherNo()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('p.id,AES_DECRYPT( p.chalan_no , "' . $encryption_key . '")  as voucherno,AES_DECRYPT(si.supplier_name, "' . $encryption_key . '")  as supplier_name');
        $this->db->from('purchase_details pd');
        $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
        $this->db->join('store s', 's.id = pd.store', 'inner');
        $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
        $this->db->where('s.id', $this->input->post('store', TRUE));
        $this->db->where('p.status', 0);

        $this->db->where("AES_DECRYPT(p.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));

        $this->db->group_by('voucherno');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }




    public function floor_detailsbybatchidprodidandstoreid()
    {
        $this->db->select('f.id, f.name');
        $this->db->from('stock_details sd');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        $this->db->join('floor f', 'f.id = sd.floor', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sb.id', $this->input->post('batchid', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));
        $this->db->where('f.status', 1);
        $this->db->group_by('f.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function avg_avstock()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('sum(AES_DECRYPT( sd.stock , "' . $encryption_key . '"))  as avgqty');
        $this->db->from('stock_details sd');
        // $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        // $this->db->join('floor f', 'f.id = sd.floor', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        // $this->db->where("AES_DECRYPT(sd.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));
        // $this->db->where('f.id', $this->input->post('floorid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function save_damstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;


        $data = array(
            'id' => 0,
            'date'  => $items[0]['date'],
            'reason' => $items[0]['reason']
        );


        $this->db->insert('dam_stock', $data);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {

            $quantity = -$item['quantity'];
            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['store']}', 
             '{$item['floor']}', 
             AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
              AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            AES_ENCRYPT('0', '{$encryption_key}'),  
             'dam_stock', 
             '{$inserted_id}'
            );
        ";
            $this->db->query($query);
        }
        echo json_encode("Success");
    }


    public function update_damstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'dam_stock')
            ->delete('stock_details');
        foreach ($items as $item) {

            $quantity = -$item['quantity'];

            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['store']}', 
             '{$item['floor']}', 
             AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
              AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            AES_ENCRYPT('{0}', '{$encryption_key}'),  
             'dam_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);
        }
        echo json_encode("Success");


        $this->db->where('id', $items[0]['batch'])
            ->update('stockbatch', ['opening_stock_used' => "yes"]);

        echo json_encode("Success");
    }

    public function delete_damstock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'dam_stock')
            ->delete('stock_details');

        $this->db->where('id', $id)
            ->delete('dam_stock');

        redirect("manage_stockdisposalnote");
    }

    public function bdtask_manage_stockdisposalnote()
    {
        $data['title']      = display('manage_stockdisposalnote');
        $data['module']     = "stock";
        $data['page']       = "manage_stockdisposalnote";
        echo modules::run('template/layout', $data);
    }

    public function checkdamstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->damagestock($postData);
        echo json_encode($data);
    }


    public function getDamageStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('b.batch_id, b.product, b.store, b.floor, a.date AS date, a.reason AS reason,AES_DECRYPT( b.actualstock , "' . $encryption_key . '") AS actualstock , pi.unit, 
    (SELECT   SUM(AES_DECRYPT( c.actualstock , "' . $encryption_key . '")) AS actualstock 
     FROM stock_details c 
     WHERE c.batch_id = b.batch_id 
       AND b.product = c.product
       AND b.store = c.store 
       AND b.floor = c.floor) AS avstock');
        $this->db->from('dam_stock a');
        $this->db->join('stock_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'dam_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }


    //Stock Adjustment part

    public function bdtask_newstockadjustment_form($id = null)
    {
        $data = array(
            'title'         => display('new_stock_adjustment'),
        );
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->product_model->active_store();
        $data['id'] = $id;
        $data['type'] = null;

        $data['module']      = 'stock';
        $data['page']    = "newstockadjustment_form";

        if (!$this->permission1->method('manage_stock_adjustment', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        if ($id != null) {
            $data['type'] = $this->stocktype($id);

            $data['title'] = "Edit Stock Adjustment";
        }
        echo modules::run('template/layout', $data);
    }


    public function save_adjstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;


        // $data = array(
        //     'id' => 0,
        //     'date'  => $items[0]['date'],
        //     'reason' => $items[0]['reason'],
        //     'type2' => $items[0]['type2'],
        //     'stocktype' => $items[0]['stocktype'],
        //     'type' => $items[0]['type']
        // );


        // $this->db->insert('adj_stock', $data);


        $type2 = $this->db->escape($items[0]['type2']);
        $date = $this->db->escape($items[0]['date']);
        $reason = $this->db->escape($items[0]['reason']);
        $stocktype = $this->db->escape($items[0]['stocktype']);
        $type = $this->db->escape($items[0]['type']);

        // Now construct the SQL query
        $query = "
            INSERT INTO adj_stock 
                (id, date, reason, type2, stocktype, type)
            VALUES 
                (0, 
                 {$date}, 
                 {$reason}, 
                 AES_ENCRYPT({$type2}, '{$encryption_key}'), 
                 {$stocktype}, 
                 {$type});
        ";

        // Execute the query
        $this->db->query($query);

        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {
            $quantity = 0;



            if ($item["adj"] == "increase") {
                $quantity = $item['quantity'];
            } else if ($item["adj"] == "decrease") {
                $quantity = -$item['quantity'];
            }

            if ($items[0]['stocktype'] == "actualstock") {
                $query = "
                INSERT INTO stock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
                 '{$inserted_id}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);


                if ($items[0]['type'] == "storetransfer" || $items[0]['type'] == "stockdisposal") {
                    $query = "
                    INSERT INTO phystock_details 
                    (id,product, store, stock, type, pid,date) 
                    VALUES 
                    (0, 
                     '{$item['product']}', 
                     '{$item['store']}', 
                     AES_ENCRYPT('{$quantity}', '{$encryption_key}'),
                     'adj_stock',
                     '{$inserted_id}','{$items[0]['date']}'
                    );
                ";
                    // $this->db->query($query);

                    if ($item["adj"] == "increase") {
                        $store   =   $this->db->select("auto_grn")->from('store')->where('id', $item['store'])->get()->row();
                        if ($store->auto_grn == 0) {
                            $this->db->query($query);
                        }
                    } else if ($item["adj"] == "decrease") {
                        $store   =   $this->db->select("auto_gdn")->from('store')->where('id', $item['store'])->get()->row();
                        if ($store->auto_gdn == 0) {
                            $this->db->query($query);
                        }
                    }
                }
            } else  if ($items[0]['stocktype'] == "physicalstock") {
                $query = "
                INSERT INTO phystock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
                 '{$inserted_id}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);
            } else {
                $query = "
                INSERT INTO stock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
                 '{$inserted_id}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);

                $query = "
                INSERT INTO phystock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
                 '{$inserted_id}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);
            }
        }

        $lastupdate = date('Y-m-d H:i:s');
        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'stock', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        echo json_encode("Success");
    }




    public function update_adjstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;



        if ($this->input->post('oldType', TRUE) == "actualstock") {
            $this->db->where('pid', $this->input->post('id', TRUE))
                ->where('type', 'adj_stock')
                ->delete('stock_details');
            $this->db->where('pid', $this->input->post('id', TRUE))
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        } else 
        if ($this->input->post('oldType', TRUE) == "physicalstock") {
            $this->db->where('pid', $this->input->post('id', TRUE))
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        } else {
            $this->db->where('pid', $this->input->post('id', TRUE))
                ->where('type', 'adj_stock')
                ->delete('stock_details');

            $this->db->where('pid', $this->input->post('id', TRUE))
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        }


        $data = array(
            'date'  => $items[0]['date'],
            'reason' => $items[0]['reason'],
            'stocktype' => $items[0]['stocktype'],
            'type' => $items[0]['type']
        );



        $this->db->where('id', $this->input->post('id', TRUE));
        $this->db->update('adj_stock', $data);

        foreach ($items as $item) {
            $quantity = 0;
            if ($item["adj"] == "increase") {
                $quantity = $item['quantity'];
            } else if ($item["adj"] == "decrease") {
                $quantity = -$item['quantity'];
            }
            if ($items[0]['stocktype'] == "actualstock") {

                $query = "
                INSERT INTO stock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
         '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);

                if ($items[0]['type'] == "storetransfer" || $items[0]['type'] == "stockdisposal") {
                    $query = "
                    INSERT INTO phystock_details 
                    (id,product, store, stock, type, pid,date) 
                    VALUES 
                    (0, 
                     '{$item['product']}', 
                     '{$item['store']}', 
                     AES_ENCRYPT('{$quantity}', '{$encryption_key}'),
                     'adj_stock',
                     '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
                    );
                ";
                    if ($item["adj"] == "increase") {
                        $store   =   $this->db->select("auto_grn")->from('store')->where('id', $item['store'])->get()->row();
                        if ($store->auto_grn == 0) {
                            $this->db->query($query);
                        }
                    } else if ($item["adj"] == "decrease") {
                        $store   =   $this->db->select("auto_gdn")->from('store')->where('id', $item['store'])->get()->row();
                        if ($store->auto_gdn == 0) {
                            $this->db->query($query);
                        }
                    }
                }
            } else  if ($items[0]['stocktype'] == "physicalstock") {
                $query = "
                INSERT INTO phystock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
         '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);
            } else {
                $query = "
                INSERT INTO stock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
         '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);

                $query = "
                INSERT INTO phystock_details 
                (id, product, store, stock, type, pid,date) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                 'adj_stock', 
         '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
                );
            ";
                $this->db->query($query);
            }
        }
        $lastupdate = date('Y-m-d H:i:s');

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'stock', 
            'update', 
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}','{$lastupdate}'
        );
    ";

        $this->db->query($query);


        echo json_encode("Success");
    }

    public function delete_adjstock($id = null)
    {
        $data = $this->stocktype($id);

        if ($data['stocktype'] == "actualstock") {
            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('stock_details');

            if ($data['type'] == "storetransfer" || $data['type'] == "stockdisposal") {
                $this->db->where('pid', $id)
                    ->where('type', 'adj_stock')
                    ->delete('phystock_details');
            }
        } else 
        if ($data['stocktype'] == "physicalstock") {
            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        } else {
            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('stock_details');

            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        }
        $this->db->where('id', $id)
            ->delete('adj_stock');

        $lastupdate = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
            VALUES (
                0, 
                'stock', 
                'delete', 
                '{$id}', 
                '{$this->session->userdata('id')}','{$lastupdate}'
            );
        ";

        $this->db->query($query);
        $base_url = base_url();
        echo '<script type="text/javascript">
        alert("Deleted successfully");
        window.location.href = "' . $base_url . 'manage_stock_adjustment";
       </script>';

        // redirect("manage_stock_adjustment");
    }

    public function bdtask_manage_stock_adjustment()
    {
        $data['title']      = display('manage_stock_adjustment');
        $data['module']     = "stock";
        $data['page']       = "manage_stock_adjustment";
        if (!$this->permission1->method('manage_stock_adjustment', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function checkadjstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->adjstock($postData);
        echo json_encode($data);
    }


    public function getAdjStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('  b.product, b.store, a.date AS date, a.reason AS reason,
    AES_DECRYPT(b.stock, "' . $encryption_key . '") AS actualstock, 
    pi.unit, a.type,
    (SELECT SUM(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS actualstock 
     FROM ' . ($this->input->post('type') == "physicalstock" ? 'phystock_details' : 'stock_details') . ' c
     WHERE b.product = c.product
     AND b.store = c.store
    ) AS avstock');
        $this->db->from('adj_stock a');
        if ($this->input->post('type') == "physicalstock") {
            $this->db->join('phystock_details b', 'b.pid = a.id');
        } else {
            $this->db->join('stock_details b', 'b.pid = a.id');
        }
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'adj_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }
















    //Store Transfer part

    public function bdtask_new_store_transfer($id = null)
    {
        $data = array(
            'title'         => display('new_store_transfer'),
        );
        $data['products'] = $this->active_productbyfloorandstore();
        $data['floor_list'] = $this->active_floor();
        $data["batches"] = $this->active_batches();

        $data['store_list'] = $this->product_model->active_store();
        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "new_store_transfer";

        if ($id != null) {

            $data['title'] = "Edit Store Transfer";
        }
        echo modules::run('template/layout', $data);
    }


    public function save_nststock()
    {
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);


        $data = array(
            'id' => 0,
            'date'  => $items[0]['date'],
            'details' => $items[0]['details']
        );


        $this->db->insert('st_stock', $data);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {

            $query = "
    INSERT INTO stock_details 
    (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
    VALUES 
    (0, 
     '{$item['batch']}', 
     '{$item['product']}', 
     '{$item['tstore']}', 
     '{$item['tfloor']}', 
     AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
      AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
    AES_ENCRYPT('0', '{$encryption_key}'),  
     'st_stock', 
     '{$inserted_id}'
    );
";
            $this->db->query($query);

            $quantity = -$item['quantity'];
            $query = "
    INSERT INTO stock_details 
    (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
    VALUES 
    (0, 
     '{$item['batch']}', 
     '{$item['product']}', 
     '{$item['fstore']}', 
     '{$item['ffloor']}', 
     AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
      AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
    AES_ENCRYPT('0', '{$encryption_key}'),  
     'st_stock', 
     '{$inserted_id}'
    );
";
            $this->db->query($query);
        }
        echo json_encode("Success");
    }


    public function update_nststock()
    {
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'st_stock')
            ->delete('stock_details');
        foreach ($items as $item) {
            $query = "
            INSERT INTO stock_details 
            (id, product, store, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['tstore']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
            AES_ENCRYPT('0', '{$encryption_key}'),  
             'st_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);

            $quantity = -$item['quantity'];
            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['fstore']}', 
             '{$item['ffloor']}', 
             AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
              AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            AES_ENCRYPT('0', '{$encryption_key}'),  
             'st_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);
        }
        echo json_encode("Success");




        echo json_encode("Success");
    }


    public function bdtask_manage_store_transfer()
    {
        $data['title']      = display('manage_store_transfer');
        $data['module']     = "stock";
        $data['page']       = "manage_store_transfer";
        echo modules::run('template/layout', $data);
    }

    public function checkststock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->ststock($postData);
        echo json_encode($data);
    }


    public function delete_ststock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'st_stock')
            ->delete('stock_details');

        $this->db->where('id', $id)
            ->delete('st_stock');

        redirect("manage_store_transfer");
    }

    public function getStStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('b.batch_id, b.product, b.store, b.floor, a.date AS date, a.details AS details,AES_DECRYPT( b.actualstock , "' . $encryption_key . '") AS actualstock, pi.unit, 
    (SELECT SUM(AES_DECRYPT( c.actualstock , "' . $encryption_key . '")) AS actualstock 
     FROM stock_details c 
     WHERE c.batch_id = b.batch_id 
       AND b.product = c.product
       AND b.store = c.store 
       AND b.floor = c.floor) AS avstock');
        $this->db->from('st_stock a');
        $this->db->join('stock_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'st_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }




    // GRN
    public function bdtask_newgrn_form($id = null)
    {
        $data = array(
            'title'         => "New Good Received Notes",
        );
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->active_storegrn();
        $data['all_supplier'] = $this->purchase_model->supplier_list();

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "new_grn";

        if (!$this->permission1->method('manage_grn', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        if ($id != null) {

            $data['title'] = "Edit Good Received Notes";
        }
        echo modules::run('template/layout', $data);
    }

    public function active_storegrn()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where('auto_grn', 1);
        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_grn', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_storegrndrop()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where('auto_grn', 1);
        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_grn', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function save_grn()
    {
        $items = $this->input->post('items', TRUE);
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $num = $this->number_generatorgrn($items[0]['type2']);
        $encryption_key = Config::$encryption_key;

        $query = "
        INSERT INTO grn_stock 
        (id,grn_id, date, detail, vehicleno, type, voucherno,lastupdateddate,createddate,supplier_id,type2,already) 
        VALUES 
        (0,  AES_ENCRYPT('{$num}', '{$encryption_key}')  , 
         '{$items[0]['date']}',
         '{$items[0]['detail']}', 
           '{$items[0]['vehicleno']}',
         '{$items[0]['type']}',   
          '{$items[0]['voucherno']}',   
          '{$lastupdate}',
           '{$lastupdate}',
          '{$items[0]['supplier_id']}',
            AES_ENCRYPT('{$items[0]['type2']}', '{$encryption_key}'),0
        );";


        $this->db->query($query);

        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {

            $query = "
            INSERT INTO phystock_details 
            (id, product, store, stock, type, pid,date) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
             'grn_stock', 
             '{$inserted_id}','{$items[0]['date']}'
            );
        ";
            $this->db->query($query);
        }
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $supplier_info    =  $this->supplier_info($items[0]['supplier_id']);


        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'GRN', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $data = array(
            'invoice_all_data' =>  $items,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $num,
            'name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'title' => "Goods Received Note",
            'title1' => "GRN No:",

        );


        $data['details'] = $this->load->view('stock/pos_print',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");      

        echo json_encode($data);
    }


    public function getgrnStockForPos()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('pi.product_name,pi.unit,AES_DECRYPT( b.stock , "' . $encryption_key . '") AS quantity,s.name as store_name,
        a.date AS date,a.vehicleno,AES_DECRYPT( p.chalan_no , "' . $encryption_key . '") AS   voucher_no,a.type as type_name,AES_DECRYPT(a.grn_id  , "' . $encryption_key . '") as grn_id, a.supplier_id');
        $this->db->from('grn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', "left");
        $this->db->join('purchase p', 'p.id = a.voucherno', "left");
        $this->db->join('store s', 's.id = b.store', "left");
        $this->db->join('product_information pi', 'pi.id = b.product', "left");
        $this->db->join('supplier_information si1', 'si1.supplier_id = a.supplier_id', "left");

        $this->db->where('b.pid', $this->input->post('id', TRUE));
        $this->db->where('b.type', 'grn_stock');
        $query = $this->db->get();
        $result = $query->result_array();

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $supplier_info    =  $this->supplier_info($result[0]['supplier_id']);


        $data = array(
            'invoice_all_data' =>   $result,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $result[0]['date'],
            'invoiceno' => $result[0]['grn_id'],
            'name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'title' => "Goods Received Note",
            'title1' => "GRN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);

        echo json_encode($data);
    }



    public function update_grn()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'grn_stock')
            ->delete('phystock_details');
        $this->db->where('voucher_id', $this->input->post('id', TRUE))
            ->where('scenario', 'GRN')
            ->delete('audit_stock');

        $data = array(
            'date' => $items[0]['date'],
            'detail' => $items[0]['detail'],
            'vehicleno' => $items[0]['vehicleno'],
            'voucherno' => $items[0]['voucherno'],
            'lastupdateddate' => $lastupdate,
            'supplier_id' => $items[0]['supplier_id'],
            'type' => $items[0]['type'],
            'already' => 0

        );

        $this->db->where('id', $this->input->post('id', TRUE));
        $this->db->update('grn_stock', $data);

        foreach ($items as $item) {

            $query = " INSERT INTO phystock_details 
            (id, product, store, stock, type, pid,date) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
             'grn_stock', 
 '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
            );
        ";
            $this->db->query($query);
        }

        $lastupdate = date('Y-m-d H:i:s');

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'grn', 
            'update', 
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}','{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $grn_id = $this->invoice_no($this->input->post('id', TRUE));
        $supplier_info    =  $this->supplier_info($items[0]['supplier_id']);



        $data = array(
            'invoice_all_data' =>  $items,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $grn_id[0]['grn_id'],
            'name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'title' => "Goods Received Note",
            'title1' => "GRN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);

        echo json_encode($data);
    }

    public function supplier_info($supplier_id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("a.supplier_id as supplier_id,
       AES_DECRYPT(a.supplier_name, '{$encryption_key}') AS supplier_name,
      AES_DECRYPT(a.mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.address, '{$encryption_key}') AS address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.emailnumber, '{$encryption_key}') AS emailnumber,
       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country")
            ->from('supplier_information a')
            ->where('supplier_id', $supplier_id)
            ->get()
            ->row();
    }

    public function invoice_no($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(grn_id, '" . $encryption_key . "') AS grn_id")
            ->from('grn_stock')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function invoice_nogdn($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(gdn_id, '" . $encryption_key . "') AS gdn_id")
            ->from('gdn_stock')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function number_generatorgrn($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(grn_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('grn_stock');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 1000000000;
            } else {
                $invoice_no = 3000000000;
            }
        }
        return $invoice_no;
    }



    public function delete_grnstock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'grn_stock')
            ->delete('phystock_details');

        $this->db->where('voucher_id', $id)
            ->where('scenario', 'GRN')
            ->delete('audit_stock');

        $this->db->where('id', $id)
            ->delete('grn_stock');


        $lastupdate = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
            VALUES (
                0, 
                'grn', 
                'delete', 
                '{$id}', 
                '{$this->session->userdata('id')}','{$lastupdate}'
            );
        ";

        $this->db->query($query);
        $base_url = base_url();

        echo '<script type="text/javascript">
   alert("Deleted successfully");
   window.location.href = "' . $base_url . 'manage_grn";
  </script>';
    }


    public function checkgrnstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->grnstock($postData, $this->input->post('type2', TRUE), $this->input->post('storeid', TRUE));
        echo json_encode($data);
    }

    public function bdtask_manage_grn()
    {
        $data['title']      = "Manage Good Received Note";
        $data['module']     = "stock";
        $data['page']       = "manage_grn";
        if (!$this->permission1->method('manage_grn', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function getgrnStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select(' b.product, b.store, a.date AS date, a.detail AS details,AES_DECRYPT( b.stock , "' . $encryption_key . '") AS actualstock,
         pi.unit, a.vehicleno,a.voucherno,a.type,AES_DECRYPT( si.supplier_name, "' . $encryption_key . '") as supplier_name ,
    (SELECT SUM(AES_DECRYPT( c.stock , "' . $encryption_key . '")) AS actualstock 
     FROM phystock_details c 
     WHERE  b.product = c.product
       AND b.store = c.store ) AS avstock
       ,(SELECT   sum(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS stock FROM grn_stock gs
INNER JOIN phystock_details c on c.pid=gs.id
where gs.voucherno=b.pid and b.product = c.product AND b.store = c.store  ) AS arquatity,si1.supplier_id
       
       ');
        $this->db->from('grn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', "left");
        $this->db->join('product_information pi', 'pi.id = b.product', "left");
        $this->db->join('purchase p', 'p.id = a.voucherno', "left");
        $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', "left");
        $this->db->join('supplier_information si1', 'si1.supplier_id = a.supplier_id', "left");

        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'grn_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }



    // GDN
    public function bdtask_manage_gdn()
    {
        $data['title']      = "Manage Good Dispatch Note";
        $data['module']     = "stock";
        $data['page']       = "manage_gdn";
        if (!$this->permission1->method('manage_gdn', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function bdtask_newgdn_form($id = null)
    {
        $data = array(
            'title'         => "New Good Dispatch Note",
        );
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->active_storegdn();
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "new_gdn";
        if (!$this->permission1->method('manage_gdn', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        if ($id != null) {

            $data['title'] = "Edit Good Dispatch Note";
        }
        echo modules::run('template/layout', $data);
    }

    public function employee_list()
    {
        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select('*')
            ->from('employee_history')
            // ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function customer_list()
    {
        $encryption_key = Config::$encryption_key;

        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select(' customer_id, AES_DECRYPT(customer_name,"' . $encryption_key . '") AS customer_name')
            ->from('customer_information')
            ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function customer_info($customer_id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("a.customer_id as customer_id,
       AES_DECRYPT(a.customer_name, '{$encryption_key}') AS customer_name,
      AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS customer_mobile,
       AES_DECRYPT(a.customer_address, '{$encryption_key}') AS customer_address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS customer_mobile,
       AES_DECRYPT(a.customer_email, '{$encryption_key}') AS customer_email,

       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country")
            ->from('customer_information a')
            ->where('customer_id', $customer_id)
            ->get()
            ->row();
    }


    public function active_storegdn()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where('auto_gdn', 1);
        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_gdn', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_storegdndrop()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where('auto_gdn', 1);
        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_gdn', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getVoucherNoSale()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('p.id, AES_DECRYPT( p.sale_id  , "' . $encryption_key . '") as voucherno,AES_DECRYPT(si.customer_name  , "' . $encryption_key . '") as customer_name ');
        $this->db->from('sale_details pd');
        $this->db->join('sale p', 'p.id = pd.pid', 'inner');
        $this->db->join('store s', 's.id = pd.store', 'inner');
        $this->db->join('customer_information si', 'si.customer_id = p.customer_id', 'inner');
        $this->db->where('s.id', $this->input->post('store', TRUE));
        $this->db->where('p.status', 0);
        $this->db->where("AES_DECRYPT(p.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));

        $this->db->group_by('voucherno');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getSaleByVoucherNo()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('
            AES_DECRYPT(pd.quantity, "' . $encryption_key . '") AS quantity, 
            p.customer_id,
            AES_DECRYPT(si.customer_name, "' . $encryption_key . '") AS customer_name ,
            pi.id AS product_id,
            pi.product_name,
            pi.unit,
            (SELECT SUM(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS actualstock 
             FROM phystock_details c 
             WHERE pd.product = c.product
               AND pd.store = c.store
            ) AS avstock,
(SELECT   sum(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS stock FROM phystock_details c
INNER JOIN gdn_stock gs on c.pid=gs.id
where gs.voucherno=pd.pid and pd.product = c.product AND pd.store = c.store and c.type="gdn_stock"  ) AS sequatity
        ');
        $this->db->from('sale_details pd');
        $this->db->join('sale p', 'p.id = pd.pid', 'inner');
        $this->db->join('store s', 's.id = pd.store', 'inner');
        $this->db->join('customer_information si', 'si.customer_id = p.customer_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
        $this->db->where('p.id', $this->input->post('voucherno'));
        $this->db->where('pd.store', $this->input->post('store'));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function getSaleByVoucherNoAndProductId()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('
            AES_DECRYPT(pd.quantity, "' . $encryption_key . '") AS quantity, 
            (SELECT SUM(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS actualstock 
             FROM phystock_details c 
             WHERE pd.product = c.product
               AND pd.store = c.store
            ) AS avstock,
            (SELECT   sum(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS stock FROM phystock_details c
INNER JOIN gdn_stock gs on c.pid=gs.id
where gs.voucherno=pd.pid and pd.product = c.product AND pd.store = c.store and c.type="gdn_stock"  ) AS sequatity
        ');
        $this->db->from('sale_details pd');
        $this->db->join('sale p', 'p.id = pd.pid', 'inner');
        $this->db->join('store s', 's.id = pd.store', 'inner');
        $this->db->join('customer_information si', 'si.customer_id = p.customer_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
        $this->db->where('p.id', $this->input->post('voucherno'));
        $this->db->where('pd.product', $this->input->post('product'));
        $this->db->where('p.status', 0);
        $this->db->where('pd.store', $this->input->post('store'));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }




    public function save_gdn()
    {
        $items = $this->input->post('items', TRUE);
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $num = $this->number_generatorgdn($items[0]['type2']);
        $encryption_key = Config::$encryption_key;

        $query = "
        INSERT INTO gdn_stock 
        (id,gdn_id, date, detail, vehicleno, type, voucherno,lastupdateddate,createddate,type2,customer_id,employee_id,already) 
        VALUES 
        (0,  AES_ENCRYPT('{$num}', '{$encryption_key}')  , 
         '{$items[0]['date']}',
         '{$items[0]['detail']}', 
           '{$items[0]['vehicleno']}',
         '{$items[0]['type']}',   
          '{$items[0]['voucherno']}',   
          '{$lastupdate}',
          '{$lastupdate}',
            AES_ENCRYPT('{$items[0]['type2']}', '{$encryption_key}'),
             '{$items[0]['customer_id']}',
         '{$items[0]['employee_id']}',0  
        );";


        $this->db->query($query);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {
            $quantity = -$item['quantity'];

            $query = "
            INSERT INTO phystock_details 
            (id, product, store, stock, type, pid,date) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
            AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
             'gdn_stock', 
             '{$inserted_id}','{$items[0]['date']}'
            );
        ";
            $this->db->query($query);
        }
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();

        $customer_info    = $this->customer_info($items[0]['customer_id']);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'GDN', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);


        $data = array(
            'invoice_all_data' =>  $items,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $num,
            'title' => "Goods Dispatch Notes",
            'name'   => $customer_info->customer_name,
            'address' => $customer_info->address,
            'mobile' => $customer_info->mobile,
            'contact' => $customer_info->contact,
            'email_address'  => $customer_info->email_address,
            'title1' => "GDN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");      

        echo json_encode($data);
    }

    public function number_generatorgdn($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(gdn_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('gdn_stock');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 1000000000;
            } else {
                $invoice_no = 3000000000;
            }
        }
        return $invoice_no;
    }



    public function update_gdn()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'gdn_stock')
            ->delete('phystock_details');
        $this->db->where('voucher_id', $this->input->post('id', TRUE))
            ->where('scenario', 'GDN')
            ->delete('audit_stock');

        $data = array(
            'date' => $items[0]['date'],
            'detail' => $items[0]['detail'],
            'vehicleno' => $items[0]['vehicleno'],
            'voucherno' => $items[0]['voucherno'],
            'lastupdateddate' => $lastupdate,
            'type' => $items[0]['type'],
            'customer_id' => $items[0]['customer_id'],
            'employee_id' => $items[0]['employee_id'],
            'already' => 0
        );

        $this->db->where('id', $this->input->post('id', TRUE));
        $this->db->update('gdn_stock', $data);

        foreach ($items as $item) {
            $quantity = -$item['quantity'];

            $query = "INSERT INTO phystock_details 
            (id, product, store, stock, type, pid,date) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
            AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
             'gdn_stock', 
 '{$this->input->post('id', TRUE)}','{$items[0]['date']}'
            );
        ";
            $this->db->query($query);
        }


        $lastupdate = date('Y-m-d H:i:s');

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'gdn', 
            'update', 
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}','{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $grn_id = $this->invoice_nogdn($this->input->post('id', TRUE));
        $customer_info    = $this->customer_info($items[0]['customer_id']);



        $data = array(
            'invoice_all_data' =>  $items,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $grn_id[0]['gdn_id'],
            'title' => "Goods Dispatch Notes",
            'name'   => $customer_info->customer_name,
            'address' => $customer_info->address,
            'mobile' => $customer_info->mobile,
            'contact' => $customer_info->contact,
            'ptype' => "",
            'email_address'  => $customer_info->email_address,
            'title1' => "GDN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);

        echo json_encode($data);
    }

    public function checkgdnstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->gdnstock($postData, $this->input->post('type2'),$this->input->post('storeid', TRUE));
        echo json_encode($data);
    }

    public function delete_gdnstock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'gdn_stock')
            ->delete('phystock_details');
        $this->db->where('voucher_id', $id)
            ->where('scenario', 'GDN')
            ->delete('audit_stock');

        $this->db->where('id', $id)
            ->delete('gdn_stock');

        $lastupdate = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
            VALUES (
                0, 
                'gdn', 
                'delete', 
                '{$id}', 
                '{$this->session->userdata('id')}','{$lastupdate}'
            );
        ";

        $this->db->query($query);
        $base_url = base_url();

        echo '<script type="text/javascript">
        alert("Deleted successfully");
        window.location.href = "' . $base_url . 'manage_gdn";
       </script>';
    }

    public function getgdnStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select(' b.product, b.store, a.date AS date, a.detail AS details,AES_DECRYPT( b.stock , "' . $encryption_key . '") AS actualstock,
         pi.unit, a.vehicleno,a.voucherno,a.type, AES_DECRYPT( ci.customer_name , "' . $encryption_key . '") as customer_name,
    (SELECT SUM(AES_DECRYPT( c.stock , "' . $encryption_key . '")) AS actualstock 
     FROM phystock_details c 
     WHERE  b.product = c.product
       AND b.store = c.store ) AS avstock,a.employee_id,a.customer_id');
        $this->db->from('gdn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->join('sale s', 's.id = a.voucherno', "left");
        $this->db->join('customer_information ci', 'ci.customer_id = s.customer_id', "left");
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'gdn_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }



    public function getPurchaseByVoucherNo()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('
            AES_DECRYPT(pd.quantity, "' . $encryption_key . '") AS quantity, 
            p.supplier_id,
        AES_DECRYPT( si.supplier_name, "' . $encryption_key . '") AS supplier_name   ,
            pi.id AS product_id,
            pi.product_name,
            pi.unit,
            (SELECT SUM(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS actualstock 
             FROM phystock_details c 
             WHERE pd.product = c.product
               AND pd.store = c.store
            ) AS avstock,(SELECT   sum(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS stock FROM phystock_details c
INNER JOIN grn_stock gs on c.pid=gs.id
where gs.voucherno=pd.pid and pd.product = c.product AND pd.store = c.store and c.type="grn_stock"  ) AS arquatity
        ');
        $this->db->from('purchase_details pd');
        $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
        $this->db->join('store s', 's.id = pd.store', 'inner');
        $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
        $this->db->where('p.id', $this->input->post('voucherno'));
        $this->db->where('p.status', 0);
        $this->db->where('pd.store', $this->input->post('store'));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function getPurchaseByVoucherNoAndProductId()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('
            AES_DECRYPT(pd.quantity, "' . $encryption_key . '") AS quantity, 
            (SELECT SUM(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS actualstock 
             FROM phystock_details c 
             WHERE pd.product = c.product
               AND pd.store = c.store
            ) AS avstock,(SELECT   sum(AES_DECRYPT(c.stock, "' . $encryption_key . '")) AS stock FROM phystock_details c
INNER JOIN grn_stock gs on c.pid=gs.id
where gs.voucherno=pd.pid and pd.product = c.product AND pd.store = c.store and c.type="grn_stock"  ) AS arquatity
        ');
        $this->db->from('purchase_details pd');
        $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
        $this->db->join('store s', 's.id = pd.store', 'inner');
        $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
        $this->db->where('p.id', $this->input->post('voucherno'));
        $this->db->where('pd.product', $this->input->post('product'));
        $this->db->where('p.status', 0);
        $this->db->where('pd.store', $this->input->post('store'));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }



    public function avg_phystock()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('sum(AES_DECRYPT( sd.stock , "' . $encryption_key . '"))  as avgqty');
        $this->db->from('phystock_details sd');
        // $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        // $this->db->join('floor f', 'f.id = sd.floor', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        // $this->db->where("AES_DECRYPT(sd.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));
        // $this->db->where('f.id', $this->input->post('floorid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function getgdnStockForPos()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('pi.product_name,pi.unit,AES_DECRYPT( b.stock , "' . $encryption_key . '") AS quantity,s.name as store_name,
        a.date AS date,a.vehicleno,AES_DECRYPT( sa.sale_id , "' . $encryption_key . '") AS   voucher_no,a.type as type_name,AES_DECRYPT(a.gdn_id  , "' . $encryption_key . '") as gdn_id,a.customer_id');
        $this->db->from('gdn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', "left");
        $this->db->join('sale sa', 'sa.id = a.voucherno', "left");
        $this->db->join('store s', 's.id = b.store', "left");
        $this->db->join('product_information pi', 'pi.id = b.product', "left");
        $this->db->where('b.pid', $this->input->post('id', TRUE));
        $this->db->where('b.type', 'gdn_stock');
        $query = $this->db->get();
        $result = $query->result_array();

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $customer_info    = $this->customer_info($result[0]['customer_id']);


        $data = array(
            'invoice_all_data' =>   $result,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $result[0]['date'],
            'invoiceno' => $result[0]['gdn_id'],
            'title' => "Goods Dispatch Notes",
            'name'   => $customer_info->customer_name,
            'address' => $customer_info->address,
            'mobile' => $customer_info->mobile,
            'contact' => $customer_info->contact,
            'email_address'  => $customer_info->email_address,
            'title1' => "GDN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);

        echo json_encode($data);
    }
}
