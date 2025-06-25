<style>
    .product_field {
        width: 300px;
    }

    .field {
        width: 170px;
    }

    .unit {
        width: 120px;
    }

    .qty {
        width: 120px;
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
            <input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
            <div class="panel-body" style="margin: 20px;">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Store
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="store" required name="store[]" tabindex="3" onchange="get_type('store')">
                                    <option value=""></option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label">Date
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d');
                                ?>

                                <?php if (!empty($id)) { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" />
                                <?php } else { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" />

                                <?php } ?>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="type" required name="type" tabindex="3" onchange="get_type('type')">
                                    <option value=""></option>
                                    <option value="sale">Sales</option>
                                    <option value="wholesale">Whole Sale</option>
                                    <option value="purchasereturn">Purchase Return</option>
                                    <option value="storetransfer">Store Transfer</option>
                                    <option value="stockdisposal">Stock Disposal</option>

                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">Vehicle No
                            </label>
                            <div class="col-sm-8">

                                <input tabindex="" class="form-control" id="vehicleno" name="vehicleno" placeholder="Vehicle No" />

                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Voucher No
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="voucherno" required name="voucherno" tabindex="3" onchange="get_type('voucherno')">
                                    <option value=""></option>
                                </select>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Customer
                            </label>
                            <div class="col-sm-6">
                                <select name="customer_id" id="customer_id" class="form-control " tabindex="1">
                                    <option value="">Select an option</option>
                                    <?php foreach ($all_customer as $customer) { ?>
                                        <option value="<?php echo $customer['customer_id'] ?>">
                                            <?php echo $customer['customer_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php if ($this->permission1->method('add_customer', 'create')->access()) { ?>
                                <div class=" col-sm-1">
                                    <a href="<?php echo base_url('add_customer'); ?>" class="client-add-btn btn btn-success" aria-hidden="true">
                                        <i class="fa fa-user"></i></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">Details
                            </label>
                            <div class="col-sm-8">

                                <input tabindex="" class="form-control" id="detail" name="detail" placeholder="Details" />

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">Salesman
                            </label>
                            <div class="col-sm-8">

                                <select name="employee_id" id="employee_id" class="form-control" tabindex="1">
                                    <option value="">Select an option</option>
                                    <?php foreach ($all_employee as $employee) { ?>
                                        <option value="<?php echo $employee['id'] ?>">
                                            <?php echo $employee['first_name'] . " " . $employee['last_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <!-- <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">Sent To
                            </label>
                            <div class="col-sm-6">

                                <input tabindex="" class="form-control" id="sentto" name="sentto" placeholder="Sent To" readonly />

                            </div>
                        </div>
                    </div>-->
                </div>


            </div>

            <div style="margin: 20px;">
                <table class="table table-bordered table-hover" id="normalinvoice">
                    <thead>
                        <tr>
                            <th class="text-center product_field">Product<i
                                    class="text-danger">*</i></th>
                            <th class="text-center ">Unit </th>
                            <th class="text-center ">Available Qty</th>
                            <th class="text-center ">Sold Qty</th>
                            <th class="text-center ">Sent Qty</th>
                            <th class="text-center ">Pending Qty</th>
                            <th class="text-center ">Qty<i
                                    class="text-danger">*</i></th>

                            <th class="text-center"><?php echo display('action') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="addinvoiceItem">
                        <tr id="myRow1">
                            <td class="product_field">

                                <select name="product[]" class="form-control" id="product1" tabindex="1" onchange="quantity_calculate(1,'product')" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $services) {
                                        echo $services['id']; ?>
                                        <option value="<?php echo $services['id']; ?>"><?php echo $services['product_name']; ?></option>

                                    <?php  }   ?>
                                </select>


                            </td>

                            <td class="qty">
                                <input type="text" name="unit[]" onkeyup="quantity_calculate(1,'unit');"
                                    class="total_qntt_1 form-control text-right"
                                    id="unit1" value="" min="0" readonly />
                            </td>
                            <td class="qty">
                                <input type="number" name="avqty[]" required onkeyup="quantity_calculate(1,'avqty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="avqty1" placeholder="0.00" min="0" readonly />
                            </td>
                            <td class="qty">
                                <input type="number" name="soqty[]" required onkeyup="quantity_calculate(1,'soqty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="soqty1" placeholder="0.00" min="0" tabindex="5" readonly />
                            </td>
                            <td class="qty">
                                <input type="number" name="seqty[]" required onkeyup="quantity_calculate(1,'seqty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="seqty1" placeholder="0.00" min="0" tabindex="5" readonly />
                            </td>
                            <td class="qty">
                                <input type="number" name="penqty[]" required onkeyup="quantity_calculate(1,'penqty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="penqty1" placeholder="0.00" min="0" tabindex="5" readonly />
                            </td>
                            <td class="qty">
                                <input type="number" name="qty[]" required onkeyup="quantity_calculate(1,'qty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="qty1" placeholder="0.00" min="0" tabindex="5" />
                            </td>

                            <td>
                            </td>

                        </tr>

                        <?php
                        // Assuming you want to generate 5 rows dynamically
                        for ($i = 2; $i <= 70; $i++) {
                        ?>
                            <tr id="myRow<?php echo $i; ?>">
                                <td class="product_field">
                                    <select name="product[]" class="form-control" id="product<?php echo $i; ?>" tabindex="1" onchange="quantity_calculate(<?php echo $i; ?>, 'product')" required>
                                        <option value="">Select Product</option>
                                    </select>
                                </td>




                                <td class="qty">
                                    <input type="text" name="unit[]" onkeyup="quantity_calculate(<?php echo $i; ?>, 'unit');"
                                        onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                        id="unit<?php echo $i; ?>" value="" min="0" readonly />
                                </td>

                                <td class="qty">
                                    <input type="number" name="avqty[]" required onkeyup="quantity_calculate(<?php echo $i; ?>,'avqty');"
                                        class="total_qntt_1 form-control text-right"
                                        id="avqty<?php echo $i; ?>" placeholder="0.00" min="0" readonly />
                                </td>

                                <td class="qty">
                                    <input type="number" name="soqty[]" required onkeyup="quantity_calculate(<?php echo $i; ?>,'soqty');"
                                        class="total_qntt_1 form-control text-right"
                                        id="soqty<?php echo $i; ?>" placeholder="0.00" min="0" tabindex="5" readonly />
                                </td>
                                <td class="qty">
                                    <input type="number" name="seqty[]" required onkeyup="quantity_calculate(<?php echo $i; ?>,'seqty');"
                                        class="total_qntt_1 form-control text-right"
                                        id="seqty<?php echo $i; ?>" placeholder="0.00" min="0" tabindex="5" readonly />
                                </td>
                                <td class="qty">
                                    <input type="number" name="penqty[]" required onkeyup="quantity_calculate(<?php echo $i; ?>,'penqty');"
                                        class="total_qntt_1 form-control text-right"
                                        id="penqty<?php echo $i; ?>" placeholder="0.00" min="0" tabindex="5" readonly />
                                </td>

                                <td class="qty">
                                    <input type="number" name="qty[]" onkeyup="quantity_calculate(<?php echo $i; ?>, 'qty');"
                                        onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                        id="qty<?php echo $i; ?>" placeholder="0.00" min="0" tabindex="5" />
                                </td>



                                <td style="display: flex; justify-content: center; align-items: center;">
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
                            <td colspan="9" rowspan="2">
                                <button type="button" id="add_invoice_item" class="btn btn-info"
                                    name="add-invoice-item" onClick="addInputField('addinvoiceItem');"><i
                                        class='fa fa-plus'></i> Add New Item</button>
                                <input type="hidden" name="" id="discount_type" value="<?php echo $discount_type ?>">
                            </td>


                        </tr>
                    </tfoot>
                </table>



                <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                <p hidden id="old-amount"><?php echo 0; ?></p>
                <p hidden id="pay-amount"></p>
                <p hidden id="change-amount"></p>
            </div>
            <div class="form-group row text-right" style="margin-right: 5px;">
                <div class="col-sm-12 p-20">
                    <!-- <input type="button" id="add_invoice" class="btn btn-success" name="add-invoice"
                        value="Save" tabindex="17"  /> -->

                    <button id="save_add" class="btn btn-success" name="add-invoice" onclick="save()">
                        <?php echo (empty($id) ? display('save') : display('update')) ?></button>




                </div>
            </div>
        </div>
    </div>
</div>


<?php
echo "<script>";
echo "var id = " . json_encode($id) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let customers=" . json_encode($all_customer) . ";";
echo "let employees=" . json_encode($all_employee) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>

<script>
    let count = 2
    let type2 = ""


    $(document).ready(function() {
        if (usertype == 3) {
            document.getElementById('style12').style.backgroundColor = '#E0E0E0';
            const title = document.getElementById('title');
            title.style.color = 'blue';
            type2 = "B"

        } else {
            type2 = "A"

        }
        getActiveStore(0);
        for (let i = 2; i <= 70; i++) {
            document.getElementById('myRow' + i).style.display = 'none';

        }
        if (id != null) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getgdnStockById',
                type: 'POST',
                data: {
                    pid: id,
                    type2: type2
                },
                success: function(response) {
                    var gdnStocks = JSON.parse(response);
                    // count = 1;
                    for (let i = 0; i < gdnStocks.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';

                        // Call other functions based on data
                        getActiveProduct(gdnStocks[i].product, a);
                        getActiveStore(gdnStocks[i].store);

                        getType(gdnStocks[i].type, gdnStocks[i].voucherno);
                        //  getAdjDropdown(adjStocks[i].actualstock > 0 ? "increase" : "decrease", a)
                        // Set form values
                        document.getElementById('qty' + a).value = Math.abs(gdnStocks[i].actualstock);
                        document.getElementById('unit' + a).value = gdnStocks[i].unit;
                        document.getElementById('avqty' + a).value = gdnStocks[i].avstock;
                        document.getElementById('date').value = gdnStocks[i].date;
                        document.getElementById('detail').value = gdnStocks[i].details;
                        // document.getElementById('sentto').value = grnStocks[i].supplier_name;
                        document.getElementById('vehicleno').value = gdnStocks[i].vehicleno;



                        $.ajax({
                            url: $('#baseUrl2').val() + 'stock/stock/getSaleByVoucherNoAndProductId',
                            type: 'POST',
                            data: {
                                store: gdnStocks[i].store,
                                type2: type2,
                                voucherno: gdnStocks[i].voucherno,
                                product: gdnStocks[i].product

                            },
                            success: function(response) {
                                let items = JSON.parse(response);
                                document.getElementById('avqty' + a).value = items[0].avstock;
                                document.getElementById('soqty' + a).value = items[0].quantity;

                                let seqty = Math.abs(items[0].sequatity) - Math.abs(gdnStocks[i].actualstock);

                                document.getElementById('seqty' + a).value = seqty;

                                let penqty = items[0].quantity - Math.abs(items[0].sequatity) + Math.abs(gdnStocks[i].actualstock);

                                document.getElementById('penqty' + a).value = penqty;
                            },
                            error: function(error) {
                                console.log(error)
                            }
                        });

                        count = count + 1;
                    }

                    var $customerDropdown = $('#customer_id');
                    $customerDropdown.empty();
                    $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                    $.each(customers, function(index, customer) {
                        $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                    });
                    $customerDropdown.val(gdnStocks[0].customer_id)

                    var $employeeDropdown = $('#employee_id');
                    $employeeDropdown.empty();
                    $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                    $.each(employees, function(index, employee) {
                        $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                    });
                    $employeeDropdown.val(gdnStocks[0].employee_id)

                    let incidenttype = 0;

                    if (document.getElementById('type').value === "sale") {
                        incidenttype = 1;
                    } else if (document.getElementById('type').value === "wholesale") {
                        incidenttype = 2;
                    } else {
                        incidenttype = 5;
                    }


                    $.ajax({
                        url: $('#baseUrl2').val() + 'stock/stock/getVoucherNoSale',
                        type: 'POST',
                        data: {
                            store: document.getElementById('store').value,
                            type2: type2,
                            incidenttype: incidenttype
                        },
                        success: function(response) {

                            if (response != "") {
                                let vouchers = JSON.parse(response);
                                getVoucher(vouchers, gdnStocks[0].voucherno)
                            } else {
                                getVoucher(null, 0)
                            }

                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });




                },
                error: function(error) {
                    console.log(error);
                }
            }); // 2000 milliseconds = 2 seconds delay
        }


    });


    function addInputField(t) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveProduct(0, count)
        count = count + 1;

    }

    function save() {
        arrItem2 = [];
        for (let i = 1; i <= count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {

                if (document.getElementById('store').value == "") {
                    alert("Store shouldn't be empty")
                    return
                } else if (document.getElementById('type').value == "") {
                    alert("Type shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('qty' + i).value == "" || document.getElementById('qty' + i).value < 0) {
                    alert("Qty shouldn't be empty or quantity greater than 0")
                    return
                } else {
                    var dropdown = document.getElementById('product' + i);
                    var storedropdown = document.getElementById('store');
                    var typedropdown = document.getElementById('type');

                    var vouchernodropdown = "";

                    if (document.getElementById('voucherno').value == "") {
                        vouchernodropdown = "";
                    } else {
                        vouchernodropdown = document.getElementById('voucherno');
                        vouchernodropdown = vouchernodropdown.options[vouchernodropdown.selectedIndex].text.split("-")[0]
                    }




                    arrItem2.push({
                        product: document.getElementById('product' + i).value,
                        store: document.getElementById('store').value,
                        customer_id: document.getElementById('customer_id').value,
                        customer_id: document.getElementById('customer_id').value,
                        employee_id: document.getElementById('employee_id').value,
                        quantity: document.getElementById('qty' + i).value,
                        date: document.getElementById('date').value,
                        detail: document.getElementById('detail').value,
                        vehicleno: document.getElementById('vehicleno').value,
                        type: document.getElementById('type').value,
                        voucherno: document.getElementById('voucherno').value,
                        voucher_no: vouchernodropdown,
                        type2: type2,
                        product_name: dropdown.options[dropdown.selectedIndex].text,
                        store_name: storedropdown.options[storedropdown.selectedIndex].text,
                        type_name: typedropdown.options[typedropdown.selectedIndex].text,
                        unit: document.getElementById('unit' + i).value

                    });
                }
            }

        }
        // let check2 = valcheck();

        // if (!check2) {
        //     alert("You can't use  same (product,store)  in multiple rows")
        //     return
        // }
        $("#save_add").hide();


        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/update_gdn',
                type: 'POST',
                data: {
                    items: arrItem2,
                    id: id
                },
                success: function(response) {
                    alert("Good Dispatch Note Updated Successfully")

                    datas = JSON.parse(response);
                    $("#save_add").show();


                    printRawHtml(datas.details);


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/save_gdn',
                type: 'POST',
                data: {
                    items: arrItem2
                },
                success: function(response) {
                    alert("Good Dispatch Note saved Successfully")

                    datas = JSON.parse(response);
                    $("#save_add").show();


                    printRawHtml(datas.details);



                },
                error: function(error) {
                    console.log(error)
                }
            });

        }
    }

    function printRawHtml(view) {


        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.href = $('#baseUrl2').val() + 'manage_gdn';
            })
        });
    }


    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';
    }

    function valcheck() {
        arrItem = [];

        if (count > 2) {
            for (let i = 1; i < count; i++) {
                if (document.getElementById('myRow' + i).style.display != "none") {
                    let check = arrItem.find(item => item.product == document.getElementById('product' + i).value &&
                        item.store == document.getElementById('store' + i).value);

                    if (check != undefined) {
                        if (check.product != '') {
                            return false
                        } else {
                            arrItem.push({
                                product: document.getElementById('product' + i).value,
                                store: document.getElementById('store' + i).value

                            });
                        }

                    } else {
                        arrItem.push({
                            product: document.getElementById('product' + i).value,
                            store: document.getElementById('store' + i).value

                        });
                    }
                }

            }

        }
        return true;

    }

    function get_type(name) {

        if (name === "type" || name === "store") {
            var $voucherDropdown = $('#voucherno');
            $voucherDropdown.empty();
            clearTable();
            if (
                document.getElementById('store').value !== "") {

                let incidenttype = 0;

                if (document.getElementById('type').value === "sale") {
                    incidenttype = 1;
                } else if (document.getElementById('type').value === "wholesale") {
                    incidenttype = 2;
                } else {
                    incidenttype = 5;
                }


                $.ajax({
                    url: $('#baseUrl2').val() + 'stock/stock/getVoucherNoSale',
                    type: 'POST',
                    data: {
                        store: document.getElementById('store').value,
                        type2: type2,
                        incidenttype: incidenttype
                    },
                    success: function(response) {

                        if (response != "") {
                            let vouchers = JSON.parse(response);
                            getVoucher(vouchers, 0)
                        } else {
                            getVoucher(null, 0)
                        }

                    },
                    error: function(error) {
                        console.log(error)
                    }
                });

            }
        }

        if (name === "voucherno") {
            clearTable();
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getSaleByVoucherNo',
                type: 'POST',
                data: {
                    store: document.getElementById('store').value,
                    type2: type2,
                    voucherno: document.getElementById('voucherno').value
                },
                success: function(response) {
                    let items = JSON.parse(response);
                    // document.getElementById('sentto').value = items[0].customer_name
                    for (let i = 2; i <= 70; i++) {
                        document.getElementById('myRow' + i).style.display = 'none';

                    }
                    for (let i = 0; i < items.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';
                        getActiveProduct(items[i].product_id, a);
                        document.getElementById('unit' + a).value = items[i].unit;


                        document.getElementById('avqty' + a).value = items[i].avstock;
                        document.getElementById('soqty' + a).value = items[i].quantity;
                        document.getElementById('seqty' + a).value = Math.abs(items[i].sequatity);

                        let penqty = items[i].quantity - Math.abs(items[i].sequatity);

                        document.getElementById('penqty' + a).value = penqty;


                        count = count + 1;
                    }

                    var $customerDropdown = $('#customer_id');
                    $customerDropdown.empty();
                    $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                    $.each(customers, function(index, customer) {
                        $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                    });
                    $customerDropdown.val(items[0].customer_id)

                    var $employeeDropdown = $('#employee_id');
                    $employeeDropdown.empty();
                    $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                    $.each(employees, function(index, employee) {
                        $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                    });
                    $employeeDropdown.val(items[0].employee_id)


                    //console.log(items);
                },
                error: function(error) {
                    console.log(error)
                }
            });


        }



    }

    function clearTable() {
        // document.getElementById('sentto').value = ""

        for (let i = 2; i <= 70; i++) {
            document.getElementById('myRow' + i).style.display = 'none';
        }
        getActiveProduct(0, 1);
        document.getElementById('avqty' + 1).value = "";
        document.getElementById('qty' + 1).value = "";
        document.getElementById('soqty' + 1).value = "";
        document.getElementById('seqty' + 1).value = "";
        document.getElementById('penqty' + 1).value = "";
    }

    function getVoucher(vouchers, voucherId) {
        var $voucherDropdown = $('#voucherno');
        $voucherDropdown.empty();
        $voucherDropdown.append('<option value="" disabled selected>Select Voucher No</option>'); // Add default option

        $.each(vouchers, function(index, voucher) {
            $voucherDropdown.append('<option value="' + voucher.id + '">' + voucher.voucherno + " - " + voucher.customer_name + '</option>');
        });

        if (voucherId > 0) {
            {
                $voucherDropdown.val(voucherId)
            }
        }
    }



    function quantity_calculate(item, name) {
        if (name === "product") {
            document.getElementById('avqty' + item).value = "";
            document.getElementById('qty' + item).value = "";
            document.getElementById('soqty' + item).value = "";
            document.getElementById('seqty' + item).value = "";
            document.getElementById('penqty' + item).value = "";
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    avStock(item)
                    document.getElementById('unit' + item).value = product[0].unit;
                    $.ajax({
                        url: $('#baseUrl2').val() + 'stock/stock/getSaleByVoucherNoAndProductId',
                        type: 'POST',
                        data: {
                            store: document.getElementById("store").value,
                            type2: type2,
                            voucherno: document.getElementById("voucherno").value,
                            product: document.getElementById("product" + item).value

                        },
                        success: function(response) {
                            let items = JSON.parse(response);
                            document.getElementById('avqty' + item).value = items[0].avstock;
                            document.getElementById('soqty' + item).value = items[0].quantity;

                            document.getElementById('seqty' + item).value = Math.abs(items[0].sequatity);

                            let penqty = items[0].quantity - Math.abs(items[0].sequatity);

                            document.getElementById('penqty' + item).value = penqty;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                },
                error: function(error) {
                    console.log(error)
                }
            });
        }
        if (name === "qty") {
            if (parseInt(document.getElementById("qty" + item).value) > parseInt(document.getElementById("penqty" + item).value)) {
                document.getElementById("qty" + item).value = "";
                alert("Entered qty more than available qty")
            }
        }



    }

    function avStock(item) {
        document.getElementById('avqty' + item).value = "";
        document.getElementById('qty' + item).value = "";
        // getAdjDropdown(0, item)
        $.ajax({
            url: $('#baseUrl2').val() + 'stock/stock/avg_phystock',
            type: 'POST',
            data: {
                prodid: document.getElementById('product' + item).value.toString(),
                storeid: document.getElementById('store').value.toString(),
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




    function getActiveStore(storeId) {
        var $storeDropdown = $('#store');
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
            if(store.default==1){
                $storeDropdown.val(store.id)

            }
        });

        if (storeId > 0) {
            {
                $storeDropdown.val(storeId)
            }
        }
    }



    function getType(typeId, voucherno) {
        var $typeDropdown = $('#type');
        $typeDropdown.empty();
        $typeDropdown.append('<option value="" disabled selected>Select Type</option>'); // Add default option
        $typeDropdown.append('<option value="sale">Sales</option>');
        $typeDropdown.append('<option value="wholesale">Whole Sale</option>');
        $typeDropdown.append('<option value="purchasereturn">Purchase Return</option>');
        $typeDropdown.append('<option value="storetransfer">Store Transfer</option>');
        $typeDropdown.append('<option value="stockdisposal">Stock Disposal</option>');
        if (typeId != "") {
            {

                $typeDropdown.val(typeId)

                $.ajax({
                    url: $('#baseUrl2').val() + 'stock/stock/getVoucherNoSale',
                    type: 'POST',
                    data: {
                        store: document.getElementById('store').value,
                        type2: type2
                    },
                    success: function(response) {

                        if (response != "") {
                            let vouchers = JSON.parse(response);
                            getVoucher(vouchers, voucherno)
                        }

                    },
                    error: function(error) {
                        console.log(error)
                    }
                });
            }
        }
    }
</script>