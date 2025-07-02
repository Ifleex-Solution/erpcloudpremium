<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<style>
    .highlight {
        background-color: #007BFF;
        color: white;
    }

    .product_field {
        width: 200px;
        height: 10px;
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
<style>
    .compact-row td,
    .compact-row select,
    .compact-row input {
        padding: 2px 4px !important;
        font-size: 12px !important;
        line-height: 1.1 !important;
        height: 10px !important;
    }

    .compact-row select.form-control,
    .compact-row input.form-control {
        min-height: 10px !important;
        height: 10px !important;
    }
</style>

<style>
    .compact-row1 td,
    .compact-row1 select,
    .compact-row1 input {
        padding: 7px 4px !important;
        height: 20px !important;
    }

    .compact-row1 select.form-control,
    .compact-row1 input.form-control {
        min-height: 20px !important;
        height: 20px !important;
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
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label">Sale Date
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="sale_date" value="<?php echo $date; ?>" id="date" onkeyup='handleDateKeyPress(event)' />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <!-- <select class="form-control" id="branch" required name="branch" tabindex="3">


                                </select> -->
                                <input class='form-control' type='text' id="branchInput" placeholder='Branch...' onkeyup='handleBranchKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='branchId' hidden />
                                <div id='branchResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <!-- <select class="form-control" id="incidenttype" required name="incidenttype" tabindex="3">
                                    <option value=""></option>
                                    <option value="1">Sales</option>
                                    <option value="2">Whole Sale</option>

                                </select> -->
                                <input class='form-control' type='text' id="incidenttypeInput" placeholder='Incident Type...' onkeyup='handleIncidenttypeKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='incidenttype' hidden />
                                <div id='incidenttypeResults1' style='margin-left: 15px;    max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>









                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="adress" class="col-sm-4 col-form-label">Customer
                            </label>
                            <div class="col-sm-8">

                                <input class='form-control' type='text' id="customerInput" placeholder='Customer Id...' onkeyup='handleCustomerKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='customerId' hidden />
                                <div id='customerResults1' style='margin-left: 15px;   max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Employee
                            </label>
                            <div class="col-sm-8">
                                <input class='form-control' type='text' id="employeeInput" placeholder='Employee...' onkeyup='handleEmployeeKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='employeeId' hidden />
                                <div id='employeeResults1' style='margin-left: 15px;    max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="adress" class="col-sm-4 col-form-label"><?php echo display('details') ?>
                            </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" tabindex="4" id="details" name="sale_details" placeholder=" <?php echo display('details') ?>" rows="1" onkeyup='handleDetailKeyPress(event)'></textarea>
                            </div>
                        </div>
                    </div>

                </div>







                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Product
                            </label>
                            <div class="col-sm-8">
                                <input class='form-control' type='text' id="productInput" placeholder='Product...' onkeyup='handleProductKeyPress(event)' autocomplete='off' />
                                <div id='productResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>
                    </div>


                </div>


                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="saleTable">
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
                                <td class="product_field" style="height: 5px;">

                                    <input type="text" name="product[]"
                                        class="total_qntt_1 form-control text-left"
                                        id="product0" value="" min="0" readonly />
                                    <input type="hidden" name="product[]"
                                        id="productId0" value="" min="0" readonly />


                                </td>

                                <td class="rate">
                                    <!-- <select class="form-control" id="store0" name="store[]" tabindex="3" onchange="product_search(0,'store')">
                                        <option value=""></option>
                                    </select> -->
                                    <div style='position: relative; display: inline-block;'>
                                        <input class='form-control' type='text' id="storeInput" placeholder='Store...' onkeyup='handleStoreKeyPress(event)' autocomplete='off' />
                                        <input type='text' name='customer_id[]' id='storeId0' hidden />
                                        <div id='storeResults1' style='  width: 100%;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;' autocomplete='off'>

                                        </div>
                                    </div>
                                </td>

                                <td class="qty">
                                    <div style='position: relative; display: inline-block;'>

                                        <input type="text" name="unit[]"
                                            class="total_qntt_1 form-control text-right"
                                            id="unit0" value="" min="0" onkeyup='handleUnitKeyPress(event)' />
                                        <div id='unitResults1' style='  width: 100%;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;' autocomplete='off'>

                                        </div>
                                    </div>
                                </td>
                                <td class="qty">
                                    <input type="number" name="avqty[]" onkeyup="product_search(0,'avqty');"
                                        class="total_qntt_1 form-control text-right"
                                        id="avqty0" placeholder="0.00" min="0" readonly />
                                    <input type="hidden" name="avqty[]"
                                        class="total_qntt_1 form-control text-right"
                                        id="unittype0" placeholder="0.00" min="0" hidden />
                                    <input type="hidden" name="avqty[]"
                                        class="total_qntt_1 form-control text-right"
                                        id="conversionratio0" placeholder="0.00" min="0" hidden />
                                    <input type="hidden" name="avqty[]"
                                        class="total_qntt_1 form-control text-right"
                                        id="conversion_id0" placeholder="0.00" min="0" hidden />
                                </td>



                                <td class="qty">
                                    <input type="number" name="product_quantity[]" id="qty0" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(0,'qty',event);" onchange="calculate_sum(0,'qty',event);" placeholder="0.00" value="" tabindex="6" autocomplete='off' />
                                </td>
                                <td class="rate">
                                    <input type="number" name="product_rate[]" onkeyup="calculate_sum(0,'product_rate',event);" onchange="calculate_sum(0,'product_rate',event);" id="product_rate0" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" autocomplete='off' />
                                </td>

                                <td class="qty">
                                    <input type="number" name="discount_per[]" onkeyup="calculate_sum(0,'discount',event);" onchange="calculate_sum(0,'discount',event);" id="discount0" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" autocomplete='off' />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">

                                </td>
                                <td class="rate">
                                    <input type="text" name="discountvalue[]" id="discount_value0" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  start-->


                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="qty">
                                        <input type="number" name="vatpercent[]" onkeyup="calculate_sum(0,'vatpercent',event);" onchange="calculate_sum(0,'vatpercent',event);" id="vat_percent0" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" autocomplete='off' />
                                    </td>
                                    <td class="rate">
                                        <input type="text" name="vatvalue[]" id="vat_value0" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                    </td>
                                <?php } else { ?>
                                    <input type="hidden" class="form-control" name="vat" id="vat_percent0" value="0.0">
                                    <input type="hidden" class="form-control" name="vat" id="vat_value0" value="0.0">

                                <?php } ?>

                                <!-- VAT  end-->
                                <td class="product_field">
                                    <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price0" value="0.00" readonly="readonly" />

                                    <input type="hidden" id="total_discount0" class="" />
                                    <input type="hidden" id="all_discount0" class="total_discount dppr" name="discount_amount[]" />
                                </td>

                                <td>
                                </td>

                            </tr>

                            <?php
                            for ($i = 2; $i <= 20; $i++) {
                            ?>
                                <tr id="myRow<?php echo $i; ?>" style="height: 10px;" class="compact-row">
                                    <td class="product_field" style="height: 10px;">
                                        <p id="product<?php echo $i; ?>" style="padding-left: 10px; font-weight: bold;"></p>
                                        <input type="hidden" name="product[]"
                                            id="productId<?php echo $i; ?>" value="" min="0" readonly />
                                    </td>

                                    <td class="rate">
                                        <input type='text' name='customer_id[]' id='storeId<?php echo $i; ?>' hidden />
                                        <p id="store<?php echo $i; ?>" style="padding-left: 10px;font-weight: bold;"></p>
                                    </td>

                                    <td class="qty">
                                        <p id="unit<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <td class="qty">
                                        <p id="avqty<?php echo $i; ?>" style="text-align: center;font-weight: bold;"></p>
                                        <input type="hidden" name="avqty[]"
                                            class="total_qntt_1 form-control text-right"
                                            id="unittype<?php echo $i; ?>" placeholder="0.00" min="0" hidden />
                                        <input type="hidden" name="avqty[]"
                                            class="total_qntt_1 form-control text-right"
                                            id="conversionratio<?php echo $i; ?>" placeholder="0.00" min="0" hidden />
                                        <input type="hidden" name="avqty[]"
                                            class="total_qntt_1 form-control text-right"
                                            id="conversion_id<?php echo $i; ?>" placeholder="0.00" min="0" hidden />


                                    </td>

                                    <td class="qty">
                                        <p id="qty<?php echo $i; ?>" style="text-align: center;font-weight: bold;"></p>

                                    </td>

                                    <td class="rate">
                                        <p id="product_rate<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <td class="qty">
                                        <p id="discount<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                    </td>

                                    <td class="rate">
                                        <p id="discountval<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <?php if ($vtinfo->ischecked == 1) { ?>
                                        <td class="qty">
                                            <p id="vat_percent<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                        </td>
                                        <td class="rate">
                                            <p id="vat_value<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                        </td>
                                    <?php } ?>

                                    <td class="product_field">
                                        <p id="total_price<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <td>
                                        <!-- Delete button can remain -->
                                        <button class="btn btn-danger btn-xs" type="button" onclick="deleteRow(<?php echo $i; ?>)">
                                            <i class="fa fa-trash fa-xs"></i>
                                        </button>
                                    </td>
                                </tr>

                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr class="compact-row1">
                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('total') ?>:</b></td>

                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('total') ?>:</b></td>

                                <?php } ?>
                                <td class="text-right">
                                    <!-- <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" /> -->
                                    <p id="Total" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>


                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b>Sale Discount:</b>
                                    </td>
                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b>Sale Discount:</b>
                                    </td>
                                <?php } ?>
                                <td class="text-right">
                                    <input type="number" id="sale_discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_sum(0,'sale_discount',event)" name="discount" placeholder="0.00" value="" autocomplete='off' />
                                </td>

                                <td>

                                </td>
                            </tr>
                            <tr class="compact-row1">
                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('total_discount') ?>:</b></td>

                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('total_discount') ?>:</b></td>

                                <?php } ?>
                                <td class="text-right">
                                    <!-- <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" /> -->
                                    <p id="total_discount_ammount" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                </td>
                                <td>

                                </td>
                            </tr>
                            <?php if ($vtinfo->ischecked == 1) { ?>
                                <tr class="compact-row1">

                                    <td class="text-right" colspan="10"><b><?php echo display('ttl_val') ?>:</b></td>

                                    <td class="text-right">
                                        <!-- <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" /> -->
                                        <p id="total_vat_amnt" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                    </td>
                                    <td>

                                    </td>
                                </tr>
                            <?php } else {  ?>
                                <input type="hidden" class="form-control" name="vat" id="total_vat_amnt" value="0.0">
                            <?php } ?>



                            <tr class="compact-row1">

                                <?php if ($vtinfo->ischecked == 1) { ?>
                                    <td class="text-right" colspan="10"><b><?php echo display('grand_total') ?>:</b></td>

                                <?php } else { ?>
                                    <td class="text-right" colspan="8"><b><?php echo display('grand_total') ?>:</b></td>

                                <?php } ?>
                                <td class="text-right">
                                    <!-- <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly /> -->

                                    <p id="grandTotal" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                </td>
                                <td> </td>
                            </tr>

                        </tfoot>
                    </table>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <!-- <div class="col-sm-3 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row no-gutters">
                                <div class="form-group ">
                                    <label for="payments" class="col-form-label pb-2"><?php echo display('payment_type'); ?> <i class="text-danger">*</i>
                                    </label>
                                    <input class='form-control' type='text' id="paymentInput" placeholder='Payment Type...' onkeyup='handlePaymentKeyPress(event)' autocomplete='off' />
                                    <div id='paymentResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>
                <div class="col-sm-4 table-bordered p-20">
                    <div class="form-group row">
                        <label for="supplier_sss" class="col-sm-4 col-form-label">Payment Type
                            <i class="text-danger">*</i>
                        </label>
                        <div class="col-sm-8">
                            <!-- <select class="form-control" id="branch" required name="branch" tabindex="3">


                                </select> -->
                            <input type='text' name='customer_id[]' id='paymentId' hidden />
                            <input class='form-control' type='text' id="paymentInput" placeholder='Payment Type...' onkeyup='handlePaymentKeyPress(event)' autocomplete='off' />
                            <div id='paymentResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                            </div>
                        </div>

                    </div>

                </div>


            </div>
        </div>

    </div>
</div>

<?php
echo "<script>";
echo "let id = " . json_encode($id) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let customers=" . json_encode($all_customer) . ";";
echo "let employees=" . json_encode($all_employee) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";

echo "let pmethods=" . json_encode($all_pmethod) . ";";
echo "let vtinfo=" . json_encode($vtinfo) . ";";
echo "</script>";
?>
<script>
    let branches = null;
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

    function handleDateKeyPress() {
        if (event.key === 'Enter') {

            let element2 = document.getElementById("customerInput");
            element2.focus();
            element2.select()



        }

    }
    document.addEventListener('keydown', function(event) {
        // Check if Shift is pressed and the key is '+'

        if (event.shiftKey) {
            event.preventDefault();
            if (confirm("Are you sure you want to save this record?")) {
                save()
            }
        }

    });

    function handleCustomerKeyPress(event, count) {
        const inputElement = document.getElementById('customerInput');
        const query = inputElement.value;
        const results = customers
            .filter(customer => customer.customer_name.toLowerCase().includes(query));
        console.log(customers)
        if (event.key === 'ArrowDown') {
            $("#customerId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemcustomer(currentIndex);
            }
        } else if (event.key === 'ArrowUp') {
            $("#customerId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemcustomer(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("incidentTypeInput");
            element2.focus();
            document.getElementById('incidentTypeInput').select()

        } else if (event.key === 'Enter') {

            if (document.getElementById('customerId').value != "") {

                let element2 = document.getElementById("employeeInput");
                element2.focus();
                document.getElementById('employeeInput').select()


            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('customerInput').value == "") {
                    alert("Customer shouldn't be empty")
                    return

                }
                document.getElementById('customerInput').value = results[currentIndex].customer_name + " ";

                document.getElementById('customerInput').select()
                document.getElementById('customerId').value = results[currentIndex].customer_id;
                clearResults();
            }


        } else if (event.key === "Backspace") {
            document.getElementById('customerId').value = "";
        } else {
            currentIndex = -1;
            displayResultsCustomer(results, count);

        }
    }

    function handleDetailKeyPress(event) {
        if (event.key === 'Enter') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()

        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            let element2 = document.getElementById("employeeInput");
            element2.focus();
            document.getElementById('employeeInput').select()

        }

    }

    function handleDiscountKeyPress(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            let element2 = document.getElementById("paymentInput");
            element2.focus();
            document.getElementById('paymentInput').select()

        }

    }



    function handleEmployeeKeyPress(event) {

        const employeeElement = document.getElementById('employeeInput');
        const query = employeeElement.value;
        const results = employees
            .filter(employee => employee.first_name.includes(query));
        if (event.key === 'ArrowDown') {
            event.preventDefault();

            $("#employeeId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();

            let element2 = document.getElementById("customerInput");
            element2.focus();
            document.getElementById('customerInput').select()

        } else if (event.key === 'ArrowUp') {
            event.preventDefault();

            $("#employeeId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'Enter') {

            if (document.getElementById('employeeId').value != "") {

                let element2 = document.getElementById("details");
                element2.focus();
                document.getElementById('details').select()




            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                // if (document.getElementById('employeeInput').value == "") {
                //     alert("Employee shouldn't be empty")
                //     return
                // }
                document.getElementById('employeeInput').value = results[currentIndex].first_name + " " + results[currentIndex].last_name;

                document.getElementById('employeeInput').select()
                document.getElementById('employeeId').value = results[currentIndex].id;
                clearResults();
            }


        } else if (event.key === "Backspace") {
            event.preventDefault();
            document.getElementById('employeeId').value = "";
        } else {
            currentIndex = -1;
            displayResultsEmployee(results, count);

        }

    }

    function handleIncidenttypeKeyPress() {

        const incidenttypeElement = document.getElementById('incidenttypeInput');
        const query = incidenttypeElement.value;

        incidentTypes = ['Sale', 'Wholesale']
        const results = incidentTypes
            .filter(incidenttype => incidenttype.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#incidenttype").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("branchInput");
            element2.focus();
            document.getElementById('branchInput').select()

        } else if (event.key === 'ArrowUp') {
            $("#incidenttype").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'Enter') {
            if (document.getElementById('incidenttype').value != "") {

                let element2 = document.getElementById("customerInput");
                element2.focus();
                document.getElementById('customerInput').select()


            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('incidenttypeInput').value == "") {
                    alert("Incident Type shouldn't be empty")
                    return
                }
                document.getElementById('incidenttypeInput').value = results[currentIndex];

                document.getElementById('incidenttypeInput').select()

                if (results[currentIndex] == "Sale") {
                    document.getElementById('incidenttype').value = 1;
                } else if (results[currentIndex] == "Wholesale") {
                    document.getElementById('incidenttype').value = 2;
                }
                clearResults();
            }


        } else if (event.key === "Backspace") {
            document.getElementById('incidenttype').value = "";
        } else {
            currentIndex = -1;
            displayResultsIncidentType(results, count);

        }

    }

    function handleBranchKeyPress() {

        const branchElement = document.getElementById('branchInput');
        const query = branchElement.value;

        // incidentTypes = ['Sale', 'Wholesale']
        const results = branches
            .filter(branch => branch.name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("date");
            element2.focus();
            document.getElementById('date').select()

        } else if (event.key === 'ArrowUp') {
            $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'Enter') {
            if (document.getElementById('branchId').value != "") {

                let element2 = document.getElementById("incidenttypeInput");
                element2.focus();
                document.getElementById('incidenttypeInput').value = "Sale";
                document.getElementById('incidenttype').value = 1;
                document.getElementById('incidenttypeInput').select()


            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('branchInput').value == "") {
                    alert("Branch shouldn't be empty")
                    return
                }
                document.getElementById('branchInput').value = results[currentIndex].name;

                document.getElementById('branchInput').select()
                document.getElementById('branchId').value = results[currentIndex].id;
                clearResults();
            }


        } else if (event.key === "Backspace") {
            document.getElementById('branchId').value = "";
        } else {
            currentIndex = -1;
            displayResultsBranch(results, count);

        }

    }


    function handleProductKeyPress() {

        const productElement = document.getElementById('productInput');
        const query = productElement.value;

        // incidentTypes = ['Sale', 'Wholesale']
        const results = products
            .filter(product => product.product_name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            //  $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItememployee(currentIndex);
            }

        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("details");
            element2.focus();
            document.getElementById('details').select()

        } else if (event.key === 'ArrowUp') {
            //  $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemproduct(currentIndex);
            }
        } else if (event.key === 'Escape') {
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            document.getElementById('product0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('unit0').value = '';
            document.getElementById('avqty0').value = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';

        } else if (event.key === 'Enter') {
            // if (document.getElementById('branchId').value != "") {

            //     // let element2 = document.getElementById("branchId");
            //     // element2.focus();

            // }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('productInput').value == "") {
                    alert("Product shouldn't be empty")
                    return
                }
                document.getElementById('product0').value = results[currentIndex].product_name;
                document.getElementById('productId0').value = results[currentIndex].id;
                document.getElementById('productInput').value = "";

                // document.getElementById('branchId').value = results[currentIndex].id;
                product_search2(0, "product", results[currentIndex].id)
                clearResults();
            }


        } else if (event.key === "Backspace") {
            // document.getElementById('branchId').value = "";
        } else {
            currentIndex = -1;
            displayResultsProduct(results, count);

        }

    }

    let defaultstocktype = 0

    function handleStoreKeyPress() {

        const productElement = document.getElementById('storeInput');
        const query = productElement.value;

        const results = stores
            .filter(store => store.name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#storeId0").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemstore(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()

        } else if (event.key === 'ArrowUp') {
            $("#storeId0").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemstore(currentIndex);
            }
        } else if (event.key === 'Escape') {
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            document.getElementById('product0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('unit0').value = '';
            document.getElementById('avqty0').value = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';


        } else if (event.key === 'Enter') {
            if (document.getElementById('storeId0').value != "") {

                let element2 = document.getElementById("unit0");
                element2.focus();
                element2.select();



            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('storeInput').value == "") {
                    alert("Store shouldn't be empty")
                    return
                }
                document.getElementById('storeInput').value = results[currentIndex].name;

                document.getElementById('storeInput').select()
                document.getElementById('storeId0').value = results[currentIndex].id;
                document.getElementById('unit0').value = unitlist.find(unit => unit.id == results[currentIndex].dstock).value;


                avStock2(0, document.getElementById('productId0').value, results[currentIndex].id)

                clearResults();
            }



        } else if (event.key === "Backspace") {
            document.getElementById('storeId0').value = "";
        } else {
            currentIndex = -1;
            displayResultsStore(results, count);

        }

    }

    function handleUnitKeyPress() {

        const productElement = document.getElementById('unit0');
        const query = productElement.value;

        console.log(unitlist)

        const results = unitlist
            .filter(unit => unit.value.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#storeId0").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemunit(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("storeInput");
            element2.focus();
            document.getElementById('storeInput').select()

        } else if (event.key === 'ArrowUp') {
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemunit(currentIndex);
            }
        } else if (event.key === 'Escape') {
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            document.getElementById('product0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('unit0').value = '';
            document.getElementById('avqty0').value = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';


        } else if (event.key === 'Enter') {
            if (document.getElementById('unit0').value != "") {

                let element2 = document.getElementById("qty0");
                element2.focus();
                element2.select();



            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('unit0').value == "") {
                    alert("Unit shouldn't be empty")
                    return
                }

                document.getElementById('unit0').value = unitlist.find(unit => unit.id == results[currentIndex].id).value;


                avStock2(0, document.getElementById('productId0').value, document.getElementById('storeId0').value)

                clearResults();
            }



        } else if (event.key === "Backspace") {
            document.getElementById('storeId0').value = "";
        } else {
            currentIndex = -1;
            displayResultsUnit(results, count);

        }

    }

    function handlePaymentKeyPress() {

        const paymentElement = document.getElementById('paymentInput');
        const query = paymentElement.value;

        // incidentTypes = ['Sale', 'Wholesale']
        const results = pmethods
            .filter(pmethod => pmethod.name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            //  $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItempayment(currentIndex);
            }

        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()

        } else if (event.key === 'ArrowUp') {
            //  $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItempayment(currentIndex);
            }
        } else if (event.key === 'Enter') {
            if (document.getElementById('paymentId').value != "") {


                if (confirm("Are you sure you want to save this record?")) {
                    save()
                }

            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                if (document.getElementById('paymentInput').value == "") {
                    alert("Payment shouldn't be empty")
                    return
                }
                // document.getElementById('product0').value = results[currentIndex].product_name;
                document.getElementById('paymentId').value = results[currentIndex].id;
                document.getElementById('paymentInput').value = results[currentIndex].name;
                document.getElementById('paymentInput').select()
                // document.getElementById('branchId').value = results[currentIndex].id;
                clearResults();
            }


        } else if (event.key === "Backspace") {
            // document.getElementById('branchId').value = "";
        } else {
            currentIndex = -1;
            displayResultsPayment(results, count);

        }

    }

    function calculate_sum(sl, type, e) {

        if (type != '') {
            if (e.key === 'Enter') {
                e.preventDefault();

                if (type == 'qty') {

                    if (document.getElementById("qty0").value == '') {
                        alert("Please enter qty")
                        return

                    }
                    let element2 = document.getElementById("product_rate0");
                    element2.focus();
                    element2.select();




                }

                if (type == 'sale_discount') {

                    // if (document.getElementById("qty0").value == '') {
                    //     alert("Please enter qty")
                    //     return

                    // }
                    let element2 = document.getElementById("paymentInput");
                    element2.focus();
                    element2.select();
                    return




                }


                if (type == 'product_rate') {

                    if (document.getElementById("product_rate0").value == '') {
                        alert("Please enter Price")
                        return

                    }

                    let element2 = document.getElementById("discount0");
                    element2.focus();
                    element2.select();
                }

                if (type == 'discount') {


                    if (vtinfo.ischecked == 1) {
                        let element2 = document.getElementById("vat_percent0");
                        element2.focus();
                        element2.select();
                    } else {
                        if (document.getElementById("qty0").value == '') {
                            alert("Please enter qty")
                            return

                        }


                        if (document.getElementById("product_rate0").value == '') {
                            alert("Please enter Price")
                            return

                        }

                        addInputField('addinvoiceItem')
                    }
                }


                if (vtinfo.ischecked == 1) {

                    if (type == 'vatpercent') {
                        if (document.getElementById("qty0").value == '') {
                            alert("Please enter qty")
                            return

                        }


                        if (document.getElementById("product_rate0").value == '') {
                            alert("Please enter Price")
                            return

                        }

                        addInputField('addinvoiceItem')

                    }

                }


            }

            if (e.key === 'ArrowLeft') {
                if (type == 'qty') {
                    let element2 = document.getElementById("unit0");
                    element2.focus();
                    element2.select();
                }
                if (type == 'product_rate') {

                    let element2 = document.getElementById("qty0");
                    element2.focus();
                    element2.select();
                }
                if (type == 'discount') {

                    let element2 = document.getElementById("product_rate0");
                    element2.focus();
                    element2.select();
                }

                if (vtinfo.ischecked == 1) {

                    if (type == 'vatpercent') {

                        let element2 = document.getElementById("discount0");
                        element2.focus();
                        element2.select();
                    }

                }
            }


            if (event.key === 'Escape') {
                let element2 = document.getElementById("sale_discount");
                element2.focus();
                document.getElementById('sale_discount').select()
                document.getElementById('product0').value = '';
                document.getElementById('storeInput').value = '';
                document.getElementById('unit0').value = '';
                document.getElementById('avqty0').value = '';
                document.getElementById('qty0').value = '';
                document.getElementById('product_rate0').value = '';
                document.getElementById('discount0').value = '';
                document.getElementById('discount_value0').value = '';
                document.getElementById('vat_percent0').value = '';
                document.getElementById('vat_value0').value = '';
                document.getElementById('total_price0').value = '';


            }

        }


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


        if (parseInt(quantity) > parseInt(avqty)) {
            $("#qty" + sl).val("");
            alert("Quantity shouldn't be greater than available quantity");
            return;
        }

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

        let tprice = 0;
        let tdis = 0;
        let tvat = 0;



        for (let i = 2; i < count; i++) {

            if (document.getElementById('myRow' + i).style.display != "none") {
                tprice = tprice + parseFloat(document.getElementById('total_price' + i).innerHTML);
                tdis = tdis + parseFloat(document.getElementById('discountval' + i).innerHTML);

                if (vtinfo.ischecked == 1) {
                    tvat = tvat + parseFloat(document.getElementById('vat_value' + i).innerHTML);
                }



            }
        }

        var grandtotal = tprice;


        if ($("#sale_discount").val() != "") {
            tdis = tdis + parseFloat(document.getElementById('sale_discount').value);
            grandtotal = tprice - parseFloat(document.getElementById('sale_discount').value);
        }



        // $("").val(tprice.toFixed(2, 2));

        document.getElementById('Total').innerHTML = tprice.toFixed(2, 2)


        // $("#total_discount_ammount").val(tdis.toFixed(2, 2));

        document.getElementById('total_discount_ammount').innerHTML = tdis.toFixed(2, 2)


        if (vtinfo.ischecked == 1) {
            document.getElementById('total_vat_amnt').innerHTML = tvat.toFixed(2, 2)

            grandtotal = parseFloat(grandtotal) + parseFloat(tvat);
        }

        document.getElementById('grandTotal').innerHTML = grandtotal.toFixed(2, 2)


        var purchase_edit_page = $("#purchase_edit_page").val();
        $("#add_new_payment").empty();

        $("#pay-amount").text('0');


    }


    unitlist = [];
    conversionratio = 0;
    conversionratioId = 0;

    function product_search2(item, name, id) {

        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('avqty' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            document.getElementById('avqty' + item).value = "";
            document.getElementById('qty' + item).value = "";
            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: id,
                },
                success: function(response) {
                    let product = JSON.parse(response);

                    console.log(product)

                    conversionratio = 0
                    const matchedStore = stores.find(store => store.id.toLowerCase().includes(product[0].store.toLowerCase()));
                    document.getElementById('storeInput').value = matchedStore ? matchedStore.name : '';

                    document.getElementById('storeInput').select()
                    document.getElementById('storeId0').value = product[0].store;

                    let element2 = document.getElementById("storeInput");
                    element2.focus();

                    avStock2(item, id, product[0].store);

                    document.getElementById('unit' + item).value = product[0].unit;

                    if (product[0].dstock == 1) {
                        document.getElementById('unit' + item).value = product[0].unit;
                        document.getElementById('product_rate' + item).value = product[0].price;

                        document.getElementById('unittype' + item).value = "unit";
                        document.getElementById('conversionratio' + item).value = 0;
                        document.getElementById('conversion_id' + item).value = product[0].conversion_id;




                    } else {
                        document.getElementById('unit' + item).value = product[0].subunit;
                        document.getElementById('product_rate' + item).value = product[0].sprice;
                        document.getElementById('unittype' + item).value = "subunit";
                        document.getElementById('conversionratio' + item).value = product[0].conversion_ratio;
                        document.getElementById('conversion_id' + item).value = product[0].conversion_id;





                    }


                    if (vtinfo.ischecked == 1) {
                        document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    }
                    unitlist = [];

                    unitlist.push({
                        id: 1,
                        value: product[0].unit
                    })
                    unitlist.push({
                        id: 0,
                        value: product[0].subunit
                    })
                    conversionratio = product[0].conversion_ratio
                    conversionratioId = product[0].conversion_id


                    //document.getElementById('vat_value' + item).value = 0;



                },
                error: function(error) {
                    console.log(error)
                }
            });
        }


        if (name === "store") {


            console.log("store")
            avStock2(item, id)
        }

        if (name === "unit") {


            avStock2(item, id)
        }
    }

    function avStock2(item, id, storeId) {
        document.getElementById('avqty' + item).value = "";
        document.getElementById('qty' + item).value = "";
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/avg_avstock',
            type: 'POST',
            data: {
                prodid: id,
                storeid: storeId,
                type2: type2
            },
            success: function(response) {
                let stock = JSON.parse(response);

                let id = unitlist.find(unit => unit.value.toLowerCase() == document.getElementById('unit0').value.toLowerCase()).id;

                if (id == 0) {
                    let st = stock[0].avgqty == null ? 0 : stock[0].avgqty
                    st = stock[0].avgqty * conversionratio;
                    document.getElementById('avqty' + item).value = st;
                    document.getElementById('product_rate' + item).value = stock[0].sprice;
                    document.getElementById('unittype' + item).value = "subunit";
                    document.getElementById('conversionratio' + item).value = conversionratio;
                    document.getElementById('conversion_id' + item).value = conversionratioId;


                } else {
                    document.getElementById('avqty' + item).value = stock[0].avgqty == null ? 0 : stock[0].avgqty;
                    document.getElementById('product_rate' + item).value = stock[0].price;
                    document.getElementById('unittype' + item).value = "unit";
                    document.getElementById('conversionratio' + item).value = 0;
                    document.getElementById('conversion_id' + item).value = conversionratioId;



                }



            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';
        calculate_sum(count, '')
    }






    function displayResultsCustomer(results, count) {
        const searchResultsDiv = document.getElementById('customerResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.customer_name + " ";
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemcustomer(0);

    }

    function displayResultsEmployee(results, count) {
        const searchResultsDiv = document.getElementById('employeeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.first_name + " " + item.last_name;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItememployee(0);

    }


    function displayResultsIncidentType(results, count) {
        const searchResultsDiv = document.getElementById('incidenttypeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemIncidenttype(0);

    }


    function displayResultsBranch(results, count) {
        const searchResultsDiv = document.getElementById('branchResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.name;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemBranch(0);

    }


    function displayResultsProduct(results, count) {
        const searchResultsDiv = document.getElementById('productResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.product_name;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }

    function displayResultsPayment(results, count) {
        const searchResultsDiv = document.getElementById('paymentResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.name;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItempayment(0);

    }

    function displayResultsStore(results, count) {
        const searchResultsDiv = document.getElementById('storeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.name;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }

    function displayResultsUnit(results, count) {
        console.log(results)
        const searchResultsDiv = document.getElementById('unitResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {
                const resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.value;
                resultItem.setAttribute('data-index', index);
                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemunit(0);

    }



    function highlightItemcustomer(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItememployee(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }


    function highlightItemIncidenttype(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemBranch(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemproduct(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemstore(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemunit(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItempayment(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function clearResults() {
        // Clear the results div
        document.getElementById('customerResults1').innerHTML = '';
        document.getElementById('employeeResults1').innerHTML = '';
        document.getElementById('incidenttypeResults1').innerHTML = '';
        document.getElementById('productResults1').innerHTML = '';
        document.getElementById('branchResults1').innerHTML = '';
        document.getElementById('storeResults1').innerHTML = '';
        document.getElementById('unitResults1').innerHTML = '';

        document.getElementById('paymentResults1').innerHTML = '';
    }


    $(document).ready(function() {
        let element2 = document.getElementById("branchInput");
            element2.focus();
            document.getElementById('branchInput').select()
        getBranchDropdown(0);
        for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }


        if (id != null) {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getSaleById',
                type: 'POST',
                data: {
                    id: id,
                    type2: type2
                },
                success: function(response) {
                    var sales = JSON.parse(response);
                    document.getElementById('date').value = sales[0].date;
                    document.getElementById('details').value = sales[0].details;



                    document.getElementById('customerInput').value = customers.find(customer => customer.customer_id == sales[0].customer_id).customer_name;
                    document.getElementById('customerId').value = sales[0].customer_id

                    document.getElementById('employeeInput').value = employees.find(employee => employee.id == sales[0].employee_id).first_name + " " + employees.find(employee => employee.id == sales[0].employee_id).last_name;
                    document.getElementById('employeeId').value = sales[0].employee_id

                    document.getElementById('branchInput').value = branches.find(branch => branch.id == sales[0].branch).name;
                    document.getElementById('branchId').value = sales[0].branch

                    if (sales[0].incidenttype == 1) {
                        document.getElementById('incidenttypeInput').value = "Sale";
                    } else {
                        document.getElementById('incidenttypeInput').value = "Wholesale";
                    }
                    document.getElementById('incidenttype').value = sales[0].incidenttype

                    document.getElementById('paymentInput').value = pmethods.find(branch => branch.id == sales[0].payment_type).name;
                    document.getElementById('paymentId').value = sales[0].payment_type
                    document.getElementById('paymentInput').focus()
                    document.getElementById('paymentInput').select()



                    document.getElementById('total_discount_ammount').innerHTML = sales[0].total_discount_ammount;
                    document.getElementById('total_vat_amnt').innerHTML = sales[0].total_vat_amnt;
                    document.getElementById('grandTotal').innerHTML = sales[0].grandTotal;
                    document.getElementById('Total').innerHTML = sales[0].total;
                    document.getElementById('sale_discount').value = sales[0].discount;

                    count = 2;
                    for (let i = 0; i < sales.length; i++) {
                        document.getElementById('myRow' + count).style.display = 'table-row';

                        document.getElementById('product' + count).innerHTML = products.find(product => product.id == sales[i].product).product_name
                        document.getElementById('store' + count).innerHTML = stores.find(store => store.id == sales[i].store).name
                        document.getElementById('productId' + count).value = sales[i].product
                        document.getElementById('storeId' + count).value = sales[i].store
                        document.getElementById('unit' + count).innerHTML = sales[i].unit
                        document.getElementById('avqty' + count).innerHTML = sales[i].avstock
                        document.getElementById('qty' + count).innerHTML = sales[i].quantity
                        document.getElementById('product_rate' + count).innerHTML = parseFloat(sales[i].product_rate).toFixed(2)

                        document.getElementById('discount' + count).innerHTML = sales[i].discount2 ? sales[i].discount2 : 0
                        document.getElementById('discountval' + count).innerHTML = sales[i].discount_value ? parseFloat(sales[i].discount_value).toFixed(2) : 0.00

                        if (vtinfo.ischecked == 1) {
                            document.getElementById('vat_percent' + count).innerHTML = sales[i].vat_percent ? sales[i].vat_percent : 0
                            document.getElementById('vat_value' + count).innerHTML = sales[i].vat_value ? parseFloat(sales[i].vat_value).toFixed(2) : 0.00
                        }

                        document.getElementById('total_price' + count).innerHTML = sales[i].total_price ? parseFloat(sales[i].total_price).toFixed(2) : 0.00



                        count = count + 1;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            getBranchDropdown(0);

        }
    });

    function addInputField(t) {
        // if (count < 11) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        // getActiveStore(0, count);
        // getActiveProduct(0, count)

        document.getElementById('productId' + count).value = document.getElementById('productId0').value
        document.getElementById('storeId' + count).value = document.getElementById('storeId0').value

        document.getElementById('product' + count).innerHTML = document.getElementById('product0').value
        document.getElementById('store' + count).innerHTML = document.getElementById('storeInput').value
        document.getElementById('unit' + count).innerHTML = document.getElementById('unit0').value
        document.getElementById('avqty' + count).innerHTML = document.getElementById('avqty0').value
        document.getElementById('qty' + count).innerHTML = document.getElementById('qty0').value
        document.getElementById('product_rate' + count).innerHTML = parseFloat(document.getElementById('product_rate0').value).toFixed(2)

        document.getElementById('discount' + count).innerHTML = document.getElementById('discount0').value ? document.getElementById('discount0').value : 0
        document.getElementById('discountval' + count).innerHTML = document.getElementById('discount_value0').value ? parseFloat(document.getElementById('discount_value0').value).toFixed(2) : 0.00

        document.getElementById('vat_percent' + count).innerHTML = document.getElementById('vat_percent0').value ? document.getElementById('vat_percent0').value : 0
        document.getElementById('vat_value' + count).innerHTML = document.getElementById('vat_value0').value ? parseFloat(document.getElementById('vat_value0').value).toFixed(2) : 0.00
        document.getElementById('total_price' + count).innerHTML = document.getElementById('total_price0').value ? parseFloat(document.getElementById('total_price0').value).toFixed(2) : 0.00
        document.getElementById('unittype' + count).value = document.getElementById('unittype0').value;
        document.getElementById('conversionratio' + count).value = document.getElementById('conversionratio0').value;
        document.getElementById('conversion_id' + count).value = document.getElementById('conversion_id0').value;


        count = count + 1;

        document.getElementById('product0').value = '';
        document.getElementById('storeInput').value = '';
        document.getElementById('unit0').value = '';
        document.getElementById('avqty0').value = '';
        document.getElementById('qty0').value = '';
        document.getElementById('product_rate0').value = '';
        document.getElementById('discount0').value = '';
        document.getElementById('discount_value0').value = '';
        document.getElementById('vat_percent0').value = '';
        document.getElementById('vat_value0').value = '';
        document.getElementById('total_price0').value = '';

        let element2 = document.getElementById("productInput");
        element2.focus();
        // calculate_sum(0, '')


    }


    function product_search(item, name) {

        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('avqty' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            document.getElementById('avqty' + item).value = "";
            document.getElementById('qty' + item).value = "";
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
                    avStock(item);

                    document.getElementById('unit' + item).value = product[0].unit;
                    document.getElementById('product_rate' + item).value = product[0].price;

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


            avStock(item)
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
                branches = JSON.parse(data);
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
        if (document.getElementById('customerId').value == "") {
            alert("Customer shouldn't be empty")
            let element2 = document.getElementById("customerInput");
            element2.focus();
            element2.select()
            return
        } else if (document.getElementById('paymentId').value == "") {
            alert("Payment shouldn't be empty")
            let element2 = document.getElementById("paymentInput");
            element2.focus();
            element2.select()
            return
        } else if (document.getElementById('branchId').value == "") {
            alert("Branch shouldn't be empty")
            let element2 = document.getElementById("branchInput");
            element2.focus();
            element2.select()
            return
        } else if (document.getElementById('incidenttype').value == "") {
            alert("Incident Type shouldn't be empty")
            let element2 = document.getElementById("incidenttypeInput");
            element2.focus();
            element2.select()
            return
        } else if (document.getElementById('date').value == "") {
            alert("Date shouldn't be empty")
            let element2 = document.getElementById("date");
            element2.focus();
            element2.select()
            return
        }
        for (let i = 2; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                // var dropdown = document.getElementById('productId' + i);

                let qty = 0;
                if (document.getElementById('unittype' + i).value == "unit") {
                    qty = parseFloat(document.getElementById('qty' + i).innerHTML)

                } else {
                    qty = parseFloat(document.getElementById('qty' + i).innerHTML) /
                        parseFloat(document.getElementById('conversionratio' + i).value)

                }


                arrItem.push({
                    product: document.getElementById('productId' + i).value,
                    product_name: document.getElementById('product' + i).innerHTML,
                    store: document.getElementById('storeId' + i).value,
                    quantity: qty,
                    product_rate: document.getElementById('product_rate' + i).innerHTML,
                    discount: document.getElementById('discount' + i).innerHTML,
                    discount_value: document.getElementById('discountval' + i).innerHTML,
                    vat_percent: document.getElementById('vat_percent' + i).innerHTML,
                    vat_value: document.getElementById('vat_value' + i).innerHTML,
                    total_price: document.getElementById('total_price' + i).innerHTML,
                    total_discount: 0,
                    all_discount: 0,
                    unittype: document.getElementById('unittype' + i).value,
                    conversionratio: document.getElementById('conversionratio' + i).value,
                    conversion_id: document.getElementById('conversion_id' + i).value,


                });


            }

        }

        var paymentdropdown = document.getElementById('your_dropdown_id');
        $("#save_add").hide();

        if (id > 0) {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/update_sale',
                type: 'POST',
                data: {
                    id: id,
                    items: arrItem,
                    discount: document.getElementById('sale_discount').value,
                    type2: type2,
                    total_discount_ammount: document.getElementById('total_discount_ammount').innerHTML,
                    total_vat_amnt: document.getElementById('total_vat_amnt').innerHTML,
                    grandTotal: document.getElementById('grandTotal').innerHTML,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').innerHTML,
                    customer_id: document.getElementById('customerId').value,
                    employee_id: document.getElementById('employeeId').value,
                    payment_type: document.getElementById('paymentId').value,
                    payment: document.getElementById('paymentId').value,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branchId').value

                },
                success: function(response) {
                    // alert("Invoice Details Updated Successfully")
                    // window.location.href = $('#base_url').val() + 'invoice_list';

                    datas = JSON.parse(response);
                    // clearDetails()
                    $("#save_add").show();

                    alert("Invoice Details Updated Successfully")
                    printRawHtml(datas.details);


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {

            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/save_sale',
                type: 'POST',
                data: {
                    items: arrItem,
                    type2: type2,
                    discount: document.getElementById('sale_discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').innerHTML,
                    total_vat_amnt: document.getElementById('total_vat_amnt').innerHTML,
                    grandTotal: document.getElementById('grandTotal').innerHTML,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').innerHTML,
                    customer_id: document.getElementById('customerId').value,
                    payment_type: document.getElementById('paymentId').value,
                    payment: document.getElementById('paymentInput').innerHTML,
                    employee_id: document.getElementById('employeeId').value,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branchId').value



                },
                success: function(response) {
                    datas = JSON.parse(response);
                    // clearDetails()
                    $("#save_add").show();

                    alert("Invoice Details saved Successfully")
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
        document.getElementById('customer_id').value = ""
        document.getElementById('your_dropdown_id').value = ""

        var $customerDropdown = $('#customer_id');
        $customerDropdown.empty();
        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
        $.each(customers, function(index, customer) {
            $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
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