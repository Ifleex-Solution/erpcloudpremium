<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<style>
    .product_field {
        width: 200px;
    }

    .field {
        width: 30px;
    }

    .unit {
        width: 70px;
    }

    .qty {
        width: 100px;
    }

    .rate {
        width: 150px;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading" id="style12">
                <div class="panel-title">
                    <h4 id="title"><?php echo $title; ?></h4>
                </div>
            </div>

            <div class="panel-body">


                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label"><?php echo display('supplier') ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select name="supplier_id" id="supplier_id" class="form-control " required="" tabindex="1">
                                    <option value="">Select an option</option>

                                    <?php foreach ($all_supplier as $suppliers) { ?>
                                        <option value="<?php echo $suppliers['supplier_id'] ?>">
                                            <?php echo $suppliers['supplier_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php if ($this->permission1->method('add_supplier', 'create')->access()) { ?>
                                <div class="col-sm-2">
                                    <a class="btn btn-success" title="Add New Supplier" href="<?php echo base_url('add_supplier'); ?>"><i class="fa fa-user"></i></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label"><?php echo display('purchase_date') ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="purchase_date" value="<?php echo $date; ?>" id="date" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="chalan_no" class="col-sm-4 col-form-label">Invoice No
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <input type="text" tabindex="3" class="form-control" name="chalan_no" placeholder="Invoice No" id="chalan_no" required />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="adress" class="col-sm-4 col-form-label"><?php echo display('details') ?>
                            </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" tabindex="4" id="details" name="purchase_details" placeholder=" <?php echo display('details') ?>" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="branch" required name="branch" tabindex="3">


                                </select>
                            </div>

                        </div>

                    </div>
                </div>


                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="purchaseTable">
                        <thead>
                            <tr>
                                <th class="text-center product_field">Product<i
                                        class="text-danger">*</i></th>
                                <th class="text-center">Store<i class="text-danger">*</i>
                                </th>
                                <th class="text-center ">Unit </th>
                                <th class="text-center ">Av.Qty</th>
                                <th class="text-center ">Qty<i
                                        class="text-danger">*</i></th>
                                <th class="text-center ">Price val <i
                                        class="text-danger"> *</i></th>
                                <th class="text-center ">Discount</th>
                                <th class="text-center ">Dis.val</th>

                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <th class="text-center ">VAT </th>
                                    <th class="text-center ">VAT.val</th>
                                <?php } else { ?>

                                <?php } ?>


                                <th class="text-center ">Total</th>

                                <th class="text-center"><?php echo display('action') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field">

                                    <select name="product[]" class="form-control" id="product1" tabindex="1" onchange="product_search(1,'product')">
                                        <option value="">Select Product</option>
                                        <?php foreach ($products as $services) {
                                            echo $services['id']; ?>
                                            <option value="<?php echo $services['id']; ?>"><?php echo $services['product_name']; ?></option>

                                        <?php  }   ?>
                                    </select>


                                </td>

                                <td class="rate">
                                    <select class="form-control" id="store1" name="store[]" tabindex="3" onchange="product_search(1,'store')">
                                        <option value=""></option>
                                    </select>
                                </td>

                                <td class="qty">
                                    <input type="text" name="unit[]" onkeyup="product_search(1,'unit');"
                                        class="total_qntt_1 form-control text-right"
                                        id="unit1" value="" min="0" readonly />
                                </td>
                                <td class="qty">
                                    <input type="number" name="avqty[]" onkeyup="product_search(1,'avqty');"
                                        class="total_qntt_1 form-control text-right"
                                        id="avqty1" placeholder="0.00" min="0" readonly />
                                </td>



                                <td class="qty">
                                    <input type="text" name="product_quantity[]" id="qty1" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" />
                                </td>
                                <td class="rate">
                                    <input type="text" name="product_rate[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="product_rate1" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                </td>

                                <td class="qty">
                                    <input type="text" name="discount_per[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="discount1" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">

                                </td>
                                <td class="rate">
                                    <input type="text" name="discountvalue[]" id="discount_value1" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  start-->


                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="qty">
                                        <input type="text" name="vatpercent[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="vat_percent1" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                    </td>
                                    <td class="rate">
                                        <input type="text" name="vatvalue[]" id="vat_value1" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                    </td>
                                <?php } else { ?>
                                    <input type="hidden" class="form-control" name="vat" id="vat_percent1" value="0.0">
                                    <input type="hidden" class="form-control" name="vat" id="vat_value1" value="0.0">

                                <?php } ?>

                                <!-- VAT  end-->
                                <td class="product_field">
                                    <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price1" value="0.00" readonly="readonly" />

                                    <input type="hidden" id="total_discount1" class="" />
                                    <input type="hidden" id="all_discount1" class="total_discount dppr" name="discount_amount[]" />
                                </td>

                                <td>
                                </td>

                            </tr>

                            <?php
                            for ($i = 2; $i <= 20; $i++) {
                            ?>
                                <tr id="myRow<?php echo $i; ?>">
                                    <td class="product_field">
                                        <select name="product[]" class="form-control" id="product<?php echo $i; ?>" tabindex="1" onchange="product_search(<?php echo $i; ?>, 'product')">
                                            <option value="">Select Product</option>
                                            <?php foreach ($products as $services) { ?>
                                                <option value="<?php echo $services['id']; ?>"><?php echo $services['product_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>



                                    <td class="rate">
                                        <select class="form-control" id="store<?php echo $i; ?>" name="store[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'store')">
                                            <option value=""></option>
                                        </select>
                                    </td>



                                    <td class="qty">
                                        <input type="text" name="unit[]" onkeyup="product_search(<?php echo $i; ?>, 'unit');" class="total_qntt_1 form-control text-right" id="unit<?php echo $i; ?>" value="" min="0" readonly />
                                    </td>

                                    <td class="qty">
                                        <input type="number" name="avqty[]" onkeyup="product_search(<?php echo $i; ?>, 'avqty');" class="total_qntt_1 form-control text-right" id="avqty<?php echo $i; ?>" placeholder="0.00" min="0" readonly />
                                    </td>

                                    <td class="qty">
                                        <input type="text" name="product_quantity[]" id="qty<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" />
                                    </td>

                                    <td class="rate">
                                        <input type="text" name="product_rate[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="product_rate<?php echo $i; ?>" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                    </td>

                                    <td class="qty">
                                        <input type="text" name="discount_per[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="discount<?php echo $i; ?>" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                        <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                    </td>

                                    <td class="rate">
                                        <input type="text" name="discountvalue[]" id="discount_value<?php echo $i; ?>" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT start -->
                                    <?php if ($vtinfo->ischecked == 1) { ?>
                                        <td class="qty">
                                            <input type="text" name="vatpercent[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="vat_percent<?php echo $i; ?>" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                        </td>
                                        <td class="rate">
                                            <input type="text" name="vatvalue[]" id="vat_value<?php echo $i; ?>" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                        </td>
                                    <?php } else { ?>
                                        <input type="hidden" class="form-control" name="vat" id="vat_percent<?php echo $i; ?>" value="0.0">
                                        <input type="hidden" class="form-control" name="vat" id="vat_value<?php echo $i; ?>" value="0.0">

                                    <?php } ?>
                                    <!-- VAT end -->

                                    <td class="product_field">
                                        <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price<?php echo $i; ?>" value="0.00" readonly="readonly" />
                                        <input type="hidden" id="total_discount<?php echo $i; ?>" class="" />
                                        <input type="hidden" id="all_discount<?php echo $i; ?>" class="total_discount dppr" name="discount_amount[]" />
                                    </td>

                                    <td>
                                        <button class='btn btn-danger' type='button' value='Delete' onclick='deleteRow(<?php echo $i; ?>)'>
                                            <i class='fa fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('total') ?>:</b></td>

                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('total') ?>:</b></td>

                                <?php } ?>
                                <td class="text-right">
                                    <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" />
                                </td>
                                <td> <button type="button" id="add_invoice_item" class="btn btn-info" name="add-invoice-item"
                                        onClick="addInputField('addinvoiceItem');" tabindex="9"><i class="fa fa-plus"></i></button>

                                    <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
                                </td>
                            </tr>
                            <tr>


                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('purchase_discount') ?>:</b>
                                    </td>
                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('purchase_discount') ?>:</b>
                                    </td>
                                <?php } ?>
                                <td class="text-right">
                                    <input type="text" id="discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_store(1)" name="discount" placeholder="0.00" value="" />
                                </td>

                                <td>

                                </td>
                            </tr>
                            <tr>
                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('total_discount') ?>:</b></td>

                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('total_discount') ?>:</b></td>

                                <?php } ?>
                                <td class="text-right">
                                    <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" />
                                </td>
                            </tr>
                            <?php if ($vtinfo->ischecked == 1) { ?>
                                <tr>

                                    <td class="text-right" colspan="10"><b><?php echo display('ttl_val') ?>:</b></td>

                                    <td class="text-right">
                                        <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />
                                    </td>
                                </tr>
                            <?php } else {  ?>
                                <input type="hidden" class="form-control" name="vat" id="total_vat_amnt" value="0.0">
                            <?php } ?>


                            <tr>

                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('grand_total') ?>:</b></td>

                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('grand_total') ?>:</b></td>

                                <?php } ?>
                                <td class="text-right">
                                    <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly />
                                </td>
                                <td> </td>
                            </tr>

                        </tfoot>
                    </table>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <div class="col-sm-3 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row no-gutters">
                                <div class="form-group ">
                                    <label for="payments" class="col-form-label pb-2"><?php echo display('payment_type'); ?><i class="text-danger">*</i> </label>
                                    <select name="multipaytype[]" class="form-control" id="your_dropdown_id" tabindex="1">
                                        <option value="">Select an option</option>
                                        <?php foreach ($all_pmethod as $services) { ?>
                                            <option value="<?php echo $services['id']; ?>"><?php echo $services['name']; ?></option>
                                        <?php } ?>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <button id="save_add" class="btn btn-success" name="add-invoice" onclick="save()">
                            <?php echo (empty($id) ? display('save') : display('update')) ?></button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
echo "<script>";
echo "var id = " . json_encode($id) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let suppliers=" . json_encode($all_supplier) . ";";
echo "let pmethods=" . json_encode($all_pmethod) . ";";
echo "let vtinfo=" . json_encode($vtinfo) . ";";

echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";

echo "</script>";
?>
<script>
    $('body').addClass("sidebar-mini sidebar-collapse");
    let type2 = ""
    if (usertype == 3) {
        document.getElementById('style12').style.backgroundColor = '#E0E0E0';
        const title = document.getElementById('title');
        title.style.color = 'blue';
        type2 = "B"

    } else {
        type2 = "A"

    }
    let count = 2

    $(document).ready(function() {
        for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }

        if (id != null) {
            $.ajax({
                url: $('#base_url').val() + 'purchase/purchase/getPurchaseById',
                type: 'POST',
                data: {
                    id: id,
                    type2: type2
                },
                success: function(response) {
                    var purchases = JSON.parse(response);
                    document.getElementById('date').value = purchases[0].date;
                    document.getElementById('details').value = purchases[0].details;

                    getBranchDropdown(purchases[0].branch);


                    var $supplierDropdown = $('#supplier_id');
                    $supplierDropdown.empty();
                    $supplierDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                    $.each(suppliers, function(index, supplier) {
                        $supplierDropdown.append('<option value="' + supplier.supplier_id + '">' + supplier.supplier_name + '</option>');
                    });
                    $supplierDropdown.val(purchases[0].supplier_id)

                    var $paymentDropdown = $('#your_dropdown_id');
                    $paymentDropdown.empty();
                    $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                    $.each(pmethods, function(index, supplier) {
                        $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
                    });
                    $paymentDropdown.val(purchases[0].payment_type)

                    document.getElementById('total_discount_ammount').value = purchases[0].total_discount_ammount;
                    document.getElementById('total_vat_amnt').value = purchases[0].total_vat_amnt;
                    document.getElementById('grandTotal').value = purchases[0].grandTotal;
                    document.getElementById('Total').value = purchases[0].total;
                    document.getElementById('discount').value = purchases[0].discount;
                    document.getElementById('chalan_no').value = purchases[0].chalan_no;


                    // count = 1;
                    for (let i = 0; i < purchases.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';
                        getActiveProduct(purchases[i].product, a);
                        getActiveStore(purchases[i].store, a);

                        document.getElementById('qty' + a).value = purchases[i].quantity;
                        document.getElementById('unit' + a).value = purchases[i].unit;
                        document.getElementById('avqty' + a).value = purchases[i].avstock;
                        document.getElementById('product_rate' + a).value = purchases[i].product_rate;
                        document.getElementById('discount' + a).value = purchases[i].discount2;
                        document.getElementById('discount_value' + a).value = purchases[i].discount_value;

                        if (vtinfo.ischecked == 1) {
                            document.getElementById('vat_percent' + a).value = purchases[i].vat_percent;
                        }
                        document.getElementById('vat_value' + a).value = purchases[i].vat_value;
                        document.getElementById('total_price' + a).value = purchases[i].total_price;
                        document.getElementById('total_discount' + a).value = purchases[i].total_discount;
                        document.getElementById('all_discount' + a).value = purchases[i].all_discount;



                        count = count + 1;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            }); // 2000 milliseconds = 2 seconds delay
        } else {
            getBranchDropdown(0);

        }
    });

    function addInputField(t) {
        // if (count < 11) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveStore(0, count);
        getActiveProduct(0, count)
        count = count + 1;

    }

    function product_search(item, name) {
        if (name === "product") {
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            document.getElementById('avqty' + item).value = "";
            document.getElementById('qty' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";


            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    getActiveStore(product[0].store, item);
                    avStock(item)

                    document.getElementById('unit' + item).value = product[0].unit;
                    document.getElementById('product_rate' + item).value = product[0].cost_price;

                    if (vtinfo.ischecked == 1) {
                        document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    }
                    //document.getElementById('vat_value' + item).value = 0;



                },
                error: function(error) {
                    console.log(error)
                }
            });
        }


        if (name === "store") {


            avStock(item);
        }
    }

    function avStock(item) {
        document.getElementById('avqty' + item).value = "";
        document.getElementById('qty' + item).value = "";
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/avg_avstock',
            type: 'POST',
            data: {
                prodid: document.getElementById('product' + item).value.toString(),
                storeid: document.getElementById('store' + item).value.toString(),
                type2: type2
            },
            success: function(response) {
                let stock = JSON.parse(response);


                document.getElementById('avqty' + item).value = stock[0].avgqty == null ? 0 : stock[0].avgqty;

            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';

        document.getElementById('qty' + num).value = 0;
        document.getElementById('product_rate' + num).value = 0;
        document.getElementById('discount' + num).value = 0;
        document.getElementById('discount_value' + num).value = 0;
        document.getElementById('vat_percent' + num).value = 0;
        document.getElementById('vat_value' + num).value = 0;
        document.getElementById('total_price' + num).value = 0;
        document.getElementById('total_discount' + num).value = 0;
        document.getElementById('all_discount' + num).value = 0;
        calculate_sum(num)
    }



    function calculate_sum(sl) {

        var p = 0;
        var v = 0;
        var gr_tot = 0;
        var dis = 0;
        var item_ctn_qty = $("#qty" + sl).val();
        var vendor_rate = $("#product_rate" + sl).val();

        var total_price = item_ctn_qty * vendor_rate;
        $("#total_price" + sl).val(total_price.toFixed(2));

        var quantity = $("#qty" + sl).val();
        var discount = $("#discount" + sl).val();
        var dis_type = $("#discount_type").val();
        var price_item = $("#product_rate" + sl).val();
        var vat_percent = $("#vat_percent" + sl).val();
        var avqty = $("#avqty" + sl).val();


        // if (parseInt(quantity) > parseInt(avqty)) {
        //     $("#qty" + sl).val("");
        //     alert("Quantity shouldn't be greater than available quantity");
        //     return;
        // }

        if (quantity > 0 || discount > 0 || vat_percent > 0) {
            if (dis_type == 1) {
                var price = quantity * price_item;
                var disc = +(price * discount / 100);
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);
                //Total price calculate per product
                var temp = price - disc;
                // product wise vat start
                var vat = +(temp * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end
                var ttletax = 0;
                $("#total_price" + sl).val(temp);



            } else if (dis_type == 2) {
                var price = quantity * price_item;

                // Discount cal per product
                var disc = (discount * quantity);
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);

                //Total price calculate per product
                var temp = price - disc;
                $("#total_price" + sl).val(temp);
                // product wise vat start
                var vat = +(temp * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end

                var ttletax = 0;

            } else if (dis_type == 3) {
                var total_price = quantity * price_item;
                var disc = discount;
                // Discount cal per product
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);
                //Total price calculate per product
                var price = total_price - disc;
                $("#total_price" + sl).val(price);
                // product wise vat start
                var vat = +(price * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end

                $("#total_price" + sl).val(price);


                var ttletax = 0;

            }
        }

        //Total Price
        $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
        $(".discount").each(function() {
            isNaN(this.value) || 0 == this.value.length || (dis += parseFloat(this.value))
        });
        //Total Discount
        $(".total_discount_val").each(function() {
                isNaN(this.value) || 0 == this.value.length || (p += parseFloat(this.value))
            }),
            $("#total_discount_ammount").val(p.toFixed(2, 2)),

            $(".total_vatamnt").each(function() {
                isNaN(this.value) || 0 == this.value.length || (v += parseFloat(this.value))
            }),
            $("#total_vat_amnt").val(v.toFixed(2, 2)),

            $("#Total").val(gr_tot.toFixed(2, 2));
        var vatamnt = $("#total_vat_amnt").val();

        var gttl = gr_tot - dis;
        var grandtotal = parseFloat(gttl) + parseFloat(vatamnt);
        $("#grandTotal").val(grandtotal.toFixed(2, 2));
        // $("#pamount_by_method").val(grandtotal.toFixed(2, 2));

        // $('#paidAmount').val(grandtotal.toFixed(2, 2));

        var purchase_edit_page = $("#purchase_edit_page").val();
        $("#add_new_payment").empty();

        $("#pay-amount").text('0');
        //   $("#dueAmmount").val(0);

        if (purchase_edit_page == 1) {

            var base_url = $('#base_url').val();
            var is_credit_edit = $('#is_credit_edit').val();

            var csrf_test_name = $('[name="csrf_test_name"]').val();
            var gtotal = $(".grandTotalamnt").val();
            var url = base_url + "purchase/purchase/bdtask_showpaymentmodal";
            $.ajax({
                type: "post",
                url: url,
                data: {
                    is_credit_edit: is_credit_edit,
                    csrf_test_name: csrf_test_name
                },
                success: function(data) {


                    $('#add_new_payment').append(data);

                    //  $("#pamount_by_method").val(gtotal);
                    $("#add_new_payment_type").prop('disabled', false);
                    var card_typesl = $('.card_typesl').val();

                    if (card_typesl == 0) {
                        $("#add_new_payment_type").prop('disabled', true);
                    }

                }
            });

        }

    }

    function getActiveProduct(productId, item) {
        var $productDropdown = $('#product' + item);
        $productDropdown.empty();
        $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

        $.each(products, function(index, product) {
            $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
        });

        if (productId > 0) {
            {
                $productDropdown.val(productId)
            }
        }
    }




    function getActiveStore(storeId, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });

        if (storeId > 0) {
            {
                $storeDropdown.val(storeId)
            }
        }
    }

    function getStoresDropdown(stores, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });
    }

    function getBranchDropdown(branchId) {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "store/store/getbranchbyuserid",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data) {
                var branches = JSON.parse(data);
                console.log(branches)
                var $branchDropdown = $('#branch');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id)
                    }
                });

                if (branchId > 0) {
                    {
                        $branchDropdown.val(branchId)
                    }
                }



            }
        });
    }


    function save() {
        arrItem = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('supplier_id').value === "" || document.getElementById('supplier_id').value === " ") {
                    alert("Supplier shouldn't be empty")
                    return
                } else if (document.getElementById('your_dropdown_id').value == "") {
                    alert("Payment Type shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('branch').value == "") {
                    alert("Branch shouldn't be empty")
                    return
                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else
                if (document.getElementById('product_rate' + i).value == "") {
                    alert("Price shouldn't be empty")
                    return
                } else if (document.getElementById('chalan_no').value == "") {
                    alert("Invoice No shouldn't be empty")
                    return
                } else {
                    var dropdown = document.getElementById('product' + i);


                    arrItem.push({
                        product: document.getElementById('product' + i).value,
                        product_name: dropdown.options[dropdown.selectedIndex].text,
                        store: document.getElementById('store' + i).value,
                        quantity: document.getElementById('qty' + i).value,
                        product_rate: document.getElementById('product_rate' + i).value,
                        discount: document.getElementById('discount' + i).value,
                        discount_value: document.getElementById('discount_value' + i).value,
                        vat_percent: document.getElementById('vat_percent' + i).value,
                        vat_value: document.getElementById('vat_value' + i).value,
                        total_price: document.getElementById('total_price' + i).value,
                        total_discount: document.getElementById('total_discount' + i).value,
                        all_discount: document.getElementById('all_discount' + i).value,
                    });
                }
            }

        }

        var paymentdropdown = document.getElementById('your_dropdown_id');
        $("#save_add").hide();

        if (id > 0) {
            $.ajax({
                url: $('#base_url').val() + 'purchase/purchase/update__purchase',
                type: 'POST',
                data: {
                    id: id,


                    items: arrItem,
                    type2: type2,
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    supplier_id: document.getElementById('supplier_id').value,
                    payment_type: document.getElementById('your_dropdown_id').value,
                    chalan_no: document.getElementById('chalan_no').value,
                    payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    branch: document.getElementById('branch').value


                },
                success: function(response) {
                    alert("Purchase Details Updated Successfully")
                    datas = JSON.parse(response);
                    clearDetails()
                    $("#save_add").show();

                    // window.location.href = $('#base_url').val() + 'purchase_list';
                    printRawHtml(datas.details);


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {

            $.ajax({
                url: $('#base_url').val() + 'purchase/purchase/save_purchase',
                type: 'POST',
                data: {


                    items: arrItem,
                    type2: type2,
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    supplier_id: document.getElementById('supplier_id').value,
                    payment_type: document.getElementById('your_dropdown_id').value,
                    chalan_no: document.getElementById('chalan_no').value,
                    payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    branch: document.getElementById('branch').value

                },
                success: function(response) {
                    alert("Purchase Details saved Successfully")
                    datas = JSON.parse(response);
                    clearDetails()
                    $("#save_add").show();

                    // window.location.href = $('#base_url').val() + 'purchase_list';
                    printRawHtml(datas.details);



                },
                error: function(error) {
                    console.log(error)
                }
            });

        }

    }

    function clearDetails() {
        for (let i = 1; i < 20; i++) {
            var $productDropdown = $('#product' + i);
            $productDropdown.empty();
            $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

            $.each(products, function(index, product) {
                $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
            });

            var $storeDropdown = $('#store' + i);
            $storeDropdown.empty();
            $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

            $.each(stores, function(index, store) {
                $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
            });

            document.getElementById('myRow' + i).style.display = 'none';
            document.getElementById('qty' + i).value = "";
            document.getElementById('avqty' + i).value = "";
            document.getElementById('unit' + i).value = "";

            document.getElementById('product_rate' + i).value = "";
            document.getElementById('discount' + i).value = "";
            document.getElementById('discount_value' + i).value = "";
            document.getElementById('vat_percent' + i).value = "";
            document.getElementById('vat_value' + i).value = "";
            document.getElementById('total_price' + i).value = "";
            document.getElementById('total_discount' + i).value = "";
            document.getElementById('all_discount' + i).value = "";

        }
        document.getElementById('myRow1').style.display = 'table-row';

        document.getElementById('discount').value = ""
        document.getElementById('total_discount_ammount').value = ""
        document.getElementById('total_vat_amnt').value = ""
        document.getElementById('grandTotal').value = ""
        document.getElementById('date').value = ""
        document.getElementById('details').value = ""
        document.getElementById('Total').value = ""
        document.getElementById('supplier_id').value = ""
        document.getElementById('your_dropdown_id').value = ""

        var $customerDropdown = $('#supplier_id');
        $customerDropdown.empty();
        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
        $.each(suppliers, function(index, customer) {
            $customerDropdown.append('<option value="' + customer.supplier_id + '">' + customer.supplier_name + '</option>');
        });

        var $paymentDropdown = $('#your_dropdown_id');
        $paymentDropdown.empty();
        $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
        $.each(pmethods, function(index, supplier) {
            $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
        });
    }

    function printRawHtml(view) {


        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.reload();
            })
        });
    }
</script>