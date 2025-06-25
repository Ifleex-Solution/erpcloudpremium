<style>
    .product_field {
        width: 150px;
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
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
            <div class="panel-body" style="margin: 20px;">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label"><?php echo display('supplier') ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select name="supplier_id" id="supplier_id" class="form-control " tabindex="1">
                                    <option value=" "><?php echo display('select_one') ?></option>
                                    <?php foreach ($all_supplier as $suppliers) { ?>
                                        <option value="<?php echo $suppliers['supplier_id'] ?>">
                                            <?php echo $suppliers['supplier_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <br /><br /> <br /><br />


                            <label for="date" class="col-sm-4 col-form-label">Date
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d');
                                ?>

                                <?php if (!empty($id)) { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" value="<?php echo html_escape($date); ?>" tabindex="4" readonly />
                                <?php } else { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" value="<?php echo html_escape($date); ?>" tabindex="4" />

                                <?php } ?>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">Details
                            </label>
                            <div class="col-sm-8">

                                <?php if (!empty($id)) { ?>
                                    <textarea tabindex="" class="form-control" id="details" name="details" placeholder="Details" rows="5" readonly></textarea>
                                <?php } else { ?>
                                    <textarea tabindex="" class="form-control" id="details" name="details" placeholder="Details" rows="5"></textarea>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div>
                <table class="table table-bordered table-hover" id="normalinvoice">
                    <thead>
                        <tr>
                            <th class="text-center product_field">Product<i
                                    class="text-danger">*</i></th>
                            <th class="text-center">Batch<i class="text-danger">*</i>
                            </th>
                            <th class="text-center">Store<i class="text-danger">*</i>
                            </th>
                            <th class="text-center ">Floor <i class="text-danger">*</i>
                            </th>
                            <th class="text-center ">Unit </th>
                            <th class="text-center ">Av.Qty</th>
                            <th class="text-center ">Qty<i
                                    class="text-danger">*</i></th>
                            <th class="text-center ">Price val <i
                                    class="text-danger"> *</i></th>
                            <th class="text-center ">Discount</th>
                            <th class="text-center ">Dis.val</th>
                            <th class="text-center ">Vat </th>
                            <th class="text-center ">Vat.val</th>
                            <th class="text-center ">total</th>

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
                            <td class="field">
                                <select class="form-control" id="batch1" name="batch[]" tabindex="2" onchange="product_search(1,'batch')">
                                    <option value=""></option>
                                </select>
                            </td>

                            <td class="field">
                                <select class="form-control" id="store1" name="store[]" tabindex="3" onchange="product_search(1,'store')">
                                    <option value=""></option>
                                </select>
                            </td>
                            <td class="field">
                                <select class="form-control" id="floor1" name="floor[]" tabindex="4" onchange="product_search(1,'floor')">
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
                            <td class="qty">
                                <input type="text" name="discountvalue[]" id="discount_value1" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                            </td>

                            <!-- VAT  start-->
                            <td class="qty">
                                <input type="text" name="vatpercent[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="vat_percent1" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                            </td>
                            <td class="qty">
                                <input type="text" name="vatvalue[]" id="vat_value1" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                            </td>
                            <!-- VAT  end-->
                            <td class="rate">
                                <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price1" value="0.00" readonly="readonly" />

                                <input type="hidden" id="total_discount1" class="" />
                                <input type="hidden" id="all_discount1" class="total_discount dppr" name="discount_amount[]" />
                            </td>

                            <td>
                            </td>

                        </tr>

                        <?php
                        // Assuming you want to generate 9 more rows dynamically (2 to 10)
                        for ($i = 2; $i <= 10; $i++) {
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

                                <td class="field">
                                    <select class="form-control" id="batch<?php echo $i; ?>" name="batch[]" tabindex="2" onchange="product_search(<?php echo $i; ?>, 'batch')">
                                        <option value=""></option>
                                    </select>
                                </td>

                                <td class="field">
                                    <select class="form-control" id="store<?php echo $i; ?>" name="store[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'store')">
                                        <option value=""></option>
                                    </select>
                                </td>

                                <td class="field">
                                    <select class="form-control" id="floor<?php echo $i; ?>" name="floor[]" tabindex="4" onchange="product_search(<?php echo $i; ?>, 'floor')">
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

                                <td class="qty">
                                    <input type="text" name="discountvalue[]" id="discount_value<?php echo $i; ?>" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT start -->
                                <td class="qty">
                                    <input type="text" name="vatpercent[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="vat_percent<?php echo $i; ?>" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                </td>
                                <td class="qty">
                                    <input type="text" name="vatvalue[]" id="vat_value<?php echo $i; ?>" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                </td>
                                <!-- VAT end -->

                                <td class="rate">
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

                            <td class="text-right" colspan="12"><b><?php echo display('total') ?>:</b></td>
                            <td class="text-right">
                                <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" />
                            </td>
                            <td> <button type="button" id="add_invoice_item" class="btn btn-info" name="add-invoice-item" onClick="addInputField('addinvoiceItem');" tabindex="9"><i class="fa fa-plus"></i></button>

                                <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
                            </td>
                        </tr>
                        <tr>

                            <td class="text-right" colspan="12"><b><?php echo display('purchase_discount') ?>:</b>
                            </td>
                            <td class="text-right">
                                <input type="text" id="discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_sum(1)" name="discount" placeholder="0.00" value="" />
                            </td>

                            <td>

                            </td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="12"><b><?php echo display('total_discount') ?>:</b></td>
                            <td class="text-right">
                                <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" />
                            </td>
                            <td> </td>

                        </tr>
                        <tr>
                            <td class="text-right" colspan="12"><b><?php echo display('ttl_val') ?>:</b></td>
                            <td class="text-right">
                                <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />
                            </td>
                            <td> </td>

                        </tr>

                        <tr>

                            <td class="text-right" colspan="12"><b><?php echo display('grand_total') ?>:</b></td>
                            <td class="text-right">
                                <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly />
                            </td>
                            <td> </td>
                        </tr>

                    </tfoot>
                </table>



                <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                <p hidden id="old-amount"><?php echo 0; ?></p>
                <p hidden id="pay-amount"></p>
                <p hidden id="change-amount"></p>
            </div>
            <div class="form-group row text-right">
                <div class="col-sm-12 p-20">
                    <!-- <input type="button" id="add_invoice" class="btn btn-success" name="add-invoice"
                        value="Save" tabindex="17"  /> -->

                    <button id="add_invoice" class="btn btn-success" name="add-invoice" onclick="save()">
                        <?php echo (empty($id) ? display('save') : display('update')) ?></button>




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
echo "let wholefloors=" . json_encode($floor_list) . ";";
echo "let suppliers=" . json_encode($all_supplier) . ";";

echo "let batches=" . json_encode($batches) . ";";


echo "</script>";
?>

<script>
    $('body').addClass("sidebar-mini sidebar-collapse");

    let count = 2


    $(document).ready(function() {
        console.log(id)

        for (let i = 2; i <= 10; i++) {
            document.getElementById('myRow' + i).style.display = 'none';

        }
        if (id != null) {
            $.ajax({
                url: $('#baseUrl2').val() + 'purchase/purchase/getPurchaseOrderById',
                type: 'POST',
                data: {
                    id: id,
                },
                success: function(response) {
                    var purchaseOrders = JSON.parse(response);
                    console.log(purchaseOrders)
                    document.getElementById('date').value = purchaseOrders[0].date;
                    document.getElementById('details').value = purchaseOrders[0].details;

                    var $supplierDropdown = $('#supplier_id');
                    $supplierDropdown.empty();
                    $supplierDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                    $.each(suppliers, function(index, supplier) {
                        $supplierDropdown.append('<option value="' + supplier.supplier_id + '">' + supplier.supplier_name + '</option>');
                    });
                    $supplierDropdown.val(purchaseOrders[0].supplier_id)

                    document.getElementById('total_discount_ammount').value = purchaseOrders[0].total_discount_ammount;
                    document.getElementById('total_vat_amnt').value = purchaseOrders[0].total_vat_amnt;
                    document.getElementById('grandTotal').value = purchaseOrders[0].grandTotal;
                    document.getElementById('Total').value = purchaseOrders[0].total;
                    document.getElementById('discount').value = purchaseOrders[0].discount;

                    count = 1;
                    for (let i = 0; i < purchaseOrders.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';
                        getActiveProduct(purchaseOrders[i].product, a);
                        getActiveBatch(purchaseOrders[i].batch_id, a)
                        getActiveStore(purchaseOrders[i].store, a);
                        getActiveFloor(purchaseOrders[i].store, purchaseOrders[i].floor, a);

                        document.getElementById('qty' + a).value = Math.abs(purchaseOrders[i].quantity);
                        document.getElementById('unit' + a).value = purchaseOrders[i].unit;
                        document.getElementById('avqty' + a).value = purchaseOrders[i].avstock;
                        document.getElementById('product_rate' + a).value = purchaseOrders[i].product_rate;
                        document.getElementById('discount' + a).value = purchaseOrders[i].discount2;
                        document.getElementById('discount_value' + a).value = purchaseOrders[i].discount_value;
                        document.getElementById('vat_percent' + a).value = purchaseOrders[i].vat_percent;
                        document.getElementById('vat_value' + a).value = purchaseOrders[i].vat_value;
                        document.getElementById('total_price' + a).value = purchaseOrders[i].total_price;
                        document.getElementById('total_discount' + a).value = purchaseOrders[i].total_discount;
                        document.getElementById('all_discount' + a).value = purchaseOrders[i].all_discount;



                        count = count + 1;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            }); // 2000 milliseconds = 2 seconds delay
        }


    });


    function addInputField(t) {
        // if (count < 11) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveStore(0, count);
        getActiveProduct(0, count)
        count = count + 1;

    }

    function save() {
        arrItem = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('supplier_id').value == "") {
                    alert("Supplier shouldn't be empty")
                    return
                }
                if (document.getElementById('batch' + i).value == "") {
                    alert("Batch shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('floor' + i).value == "") {
                    alert("Floor shouldn't be empty")
                    return

                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                }
                if (document.getElementById('product_rate' + i).value == "") {
                    alert("Price shouldn't be empty")
                    return
                } else {
                    arrItem.push({
                        product: document.getElementById('product' + i).value,
                        store: document.getElementById('store' + i).value,
                        floor: document.getElementById('floor' + i).value,
                        quantity: document.getElementById('qty' + i).value,
                        batch: document.getElementById('batch' + i).value,
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


        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'purchase/purchase/update__purchaseorder',
                type: 'POST',
                data: {
                    id: id,
                    items: arrItem,
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    supplier_id: document.getElementById('supplier_id').value,
                },
                success: function(response) {
                    alert("Purchase Order Updated Successfully")
                    window.location.href = $('#baseUrl2').val() + 'manage_purchase_order';


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'purchase/purchase/save_purchaseorder',
                type: 'POST',
                data: {
                    items: arrItem,
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    supplier_id: document.getElementById('supplier_id').value,
                },
                success: function(response) {
                    alert("Purchase Order saved Successfully")
                    window.location.href = $('#baseUrl2').val() + 'manage_purchase_order';



                },
                error: function(error) {
                    console.log(error)
                }
            });

        }







    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';

        document.getElementById('qty' + num).value = 0;
        document.getElementById('product_rate' + num).value = 0;
        document.getElementById('discount' + num).value = 0;
        document.getElementById('discount_value' + num).value = 0;
        document.getElementById('vat_percent' + num).value =0;
        document.getElementById('vat_value' + num).value =0;
        document.getElementById('total_price' + num).value =0;
        document.getElementById('total_discount' + num).value = 0;
        document.getElementById('all_discount' + num).value = 0;
        calculate_sum(num)
    }




    function product_search(item, name) {
        if (name === "product") {

            var $batchDropdown = $('#batch' + item);
            $batchDropdown.empty();
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            var $floorDropdown = $('#floor' + item);
            $floorDropdown.empty();



            document.getElementById('avqty' + item).value = "";
            //document.getElementById('qty' + item).value = "";



            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/stock_batchdetailsbyprodid',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let batches = JSON.parse(response);
                    getBatchDropdown(batches, item)
                    document.getElementById('unit' + item).value = batches[0].unit;


                },
                error: function(error) {
                    console.log(error)
                }
            });
        }

        if (name === "batch") {
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            var $floorDropdown = $('#floor' + item);
            $floorDropdown.empty();
            document.getElementById('avqty' + item).value = "";
            // document.getElementById('qty' + item).value = "";
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/store_detailsbybatchidandprodid',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    batchid: document.getElementById('batch' + item).value.toString()

                },
                success: function(response) {
                    let stores = JSON.parse(response);
                    getStoresDropdown(stores, item);

                },
                error: function(error) {
                    console.log(error)
                }
            });
        }

        if (name === "store") {

            var $floorDropdown = $('#floor' + item);
            $floorDropdown.empty();
            document.getElementById('avqty' + item).value = "";
            // document.getElementById('qty' + item).value = "";
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/floor_detailsbybatchidprodidandstoreid',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    batchid: document.getElementById('batch' + item).value.toString(),
                    storeid: document.getElementById('store' + item).value.toString()

                },
                success: function(response) {
                    floors = JSON.parse(response);
                    getFloorsDropdown(floors, item);

                },
                error: function(error) {
                    console.log(error)
                }
            });
        }


        if (name === "floor") {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/avg_avstock',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    batchid: document.getElementById('batch' + item).value.toString(),
                    storeid: document.getElementById('store' + item).value.toString(),
                    floorid: document.getElementById('floor' + item).value.toString()


                },
                success: function(response) {
                    let stock = JSON.parse(response);

                    document.getElementById('avqty' + item).value = stock[0].avgqty;

                },
                error: function(error) {
                    console.log(error)
                }
            });
        }




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

    }

    function afterdelete(item, productId, floorId, storeId, batchId) {
        getActiveProduct(productId, item)
        getActiveBatch(batchId, item)
        getActiveStore(storeId, item);
        getActiveFloor(storeId, floorId, item)

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

    function getActiveBatch(batchId, item) {
        console.log()
        var $batchDropdown = $('#batch' + item);
        $batchDropdown.empty();
        $batchDropdown.append('<option value="" disabled selected>Select Batch</option>'); // Add default option

        $.each(batches, function(index, batch) {
            $batchDropdown.append('<option value="' + batch.id + '">' + batch.batchid + '</option>');
        });

        if (batchId > 0) {
            {
                $batchDropdown.val(batchId)
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



    function getActiveFloor(selectedValue, floorId, item) {
        var floors = wholefloors.filter(s => s.storeid == selectedValue);
        var $floorDropdown = $('#floor' + item);
        $floorDropdown.empty();
        $floorDropdown.append('<option value="" disabled selected>Select Floor</option>'); // Add default option

        $.each(floors, function(index, floor) {
            $floorDropdown.append('<option value="' + floor.id + '">' + floor.name + '</option>');
        });

        if (floorId > 0) {
            {
                $floorDropdown.val(floorId)
            }
        }

    }


    function getBatchDropdown(batches, item) {
        var $batchDropdown = $('#batch' + item);
        $batchDropdown.empty();
        $batchDropdown.append('<option value="" disabled selected>Select Batch</option>'); // Add default option

        $.each(batches, function(index, batch) {
            $batchDropdown.append('<option value="' + batch.id + '">' + batch.batchid + '</option>');
        });
    }

    function getStoresDropdown(stores, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });
    }

    function getFloorsDropdown(floors, item) {
        var $floorDropdown = $('#floor' + item);
        $floorDropdown.empty();
        $floorDropdown.append('<option value="" disabled selected>Select Floor</option>'); // Add default option

        $.each(floors, function(index, floor) {
            $floorDropdown.append('<option value="' + floor.id + '">' + floor.name + '</option>');
        });
    }
</script>