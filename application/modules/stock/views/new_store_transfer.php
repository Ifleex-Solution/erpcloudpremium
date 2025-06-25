<style>
    .product_field {
        width: 270px;
    }

    .field {
        width: 220px;
    }

    .unit {
        width: 70px;
    }

    .qty {
        width: 110px;
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
                            <label for="date" class="col-sm-4 col-form-label">Date
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d');
                                ?>

                                <?php if (!empty($id)) { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" readonly />
                                <?php } else { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" />

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
                                    <input type="text" class="form-control" id="details" name="details" placeholder="Details" readonly></textarea>
                                <?php } else { ?>
                                    <input type="text" class="form-control" id="details" name="details" placeholder="Details"></textarea>

                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">From Store <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">

                                <select class="form-control" id="fstore" required name="fstore" tabindex="3">
                                    <option value=""></option>
                                    <?php if ($store_list) { ?>
                                        <?php foreach ($store_list as $categories) { ?>
                                            <option value="<?php echo $categories['id'] ?>" <?php if ($product->store == $categories['id']) {
                                                                                                echo 'selected';
                                                                                            } ?>>
                                                <?php echo $categories['name'] ?></option>

                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">To Store <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">

                                <select class="form-control" id="tstore" required name="tstore" tabindex="3">
                                    <option value=""></option>
                                    <?php if ($store_list) { ?>
                                        <?php foreach ($store_list as $categories) { ?>
                                            <option value="<?php echo $categories['id'] ?>" <?php if ($product->store == $categories['id']) {
                                                                                                echo 'selected';
                                                                                            } ?>>
                                                <?php echo $categories['name'] ?></option>

                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">From Floor <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">

                                <select class="form-control" id="ffloor" required name="ffloor" tabindex="3">
                                    <option value=""></option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">To Floor <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">

                                <select class="form-control" id="tfloor" required name="tfloor" tabindex="3">
                                    <option value=""></option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div >
                <table class="table table-bordered table-hover" id="normalinvoice" style="width: 85%;">
                    <thead>
                        <tr>
                            <th class="text-center product_field">Product<i
                                    class="text-danger">*</i></th>
                            <th class="text-center">Batch<i class="text-danger">*</i>
                            </th>
                            <th class="text-center ">Unit </th>
                            <th class="text-center ">Av.Qty</th>
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
                                </select>


                            </td>
                            <td class="field">
                                <select class="form-control" id="batch1" required name="batch[]" tabindex="2" onchange="quantity_calculate(1,'batch')">
                                    <option value=""></option>
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
                                <input type="number" name="qty[]" required onkeyup="quantity_calculate(1,'qty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="qty1" placeholder="0.00" min="0" tabindex="5" />
                            </td>

                            <td>
                            </td>

                        </tr>

                        <?php
                        // Assuming you want to generate 5 rows dynamically
                        for ($i = 2; $i <= 10; $i++) {
                        ?>
                            <tr id="myRow<?php echo $i; ?>">
                                <td class="product_field">
                                    <select name="product[]" class="form-control" id="product<?php echo $i; ?>" tabindex="1" onchange="quantity_calculate(<?php echo $i; ?>, 'product')" required>
                                        <option value="">Select Product</option>
                                    </select>
                                </td>
                                <td class="field">
                                    <select class="form-control" id="batch<?php echo $i; ?>" required name="batch[]" tabindex="2" onchange="quantity_calculate(<?php echo $i; ?>,'batch')">
                                        <option value=""></option>
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
echo "var id = " . json_encode($id) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let wholefloors=" . json_encode($floor_list) . ";";
echo "let batches=" . json_encode($batches) . ";";


echo "</script>";
?>

<script>
    let count = 2


    $(document).ready(function() {
        for (let i = 2; i <= 10; i++) {
            document.getElementById('myRow' + i).style.display = 'none';

        }
        if (id != null) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getStStockById',
                type: 'POST',
                data: {
                    pid: id,
                },
                success: function(response) {
                    var adjStocks = JSON.parse(response);
                    count = 1;
                    for (let i = 0; i < adjStocks.length; i++) {

                        if (adjStocks[i].actualstock > 0) {
                            document.getElementById('myRow' + count).style.display = 'table-row';
                            getActiveStore(adjStocks[i].store, 't');
                            getActiveFloor(adjStocks[i].store, adjStocks[i].floor, 't');
                            let prods = products.filter(s => s.store == adjStocks[i].store && s.floor == adjStocks[i].floor);

                            getActiveProduct(prods,adjStocks[i].product, count);
                            getActiveBatch(adjStocks[i].batch_id, count)
                            // document.getElementById('avqty' + count).value = adjStocks[i].avstock;

                            document.getElementById('qty' + count).value = adjStocks[i].actualstock;
                            document.getElementById('unit' + count).value = adjStocks[i].unit;

                            document.getElementById('date').value = adjStocks[i].date;
                            document.getElementById('details').value = adjStocks[i].details;
                            count = count + 1;

                        } else {
                            a=count-1;
                            document.getElementById('avqty' + a).value = adjStocks[i].avstock;

                            getActiveStore(adjStocks[i].store, 'f');
                            getActiveFloor(adjStocks[i].store, adjStocks[i].floor, 'f');
                        }





                    }
                },
                error: function(error) {
                    console.log(error);
                }
            }); // 2000 milliseconds = 2 seconds delay
        }


    });


    function addInputField(t) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        let prods = products.filter(s => s.store == document.getElementById("fstore").value && s.floor == document.getElementById("ffloor").value);

        getActiveProduct(prods,0, count)
        count = count + 1;
    }

    function save() {
        arrItem2 = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {

                if (document.getElementById('batch' + i).value == "") {
                    alert("Batch shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('fstore').value == "") {
                    alert("From Store shouldn't be empty")
                    return

                } else if (document.getElementById('ffloor').value == "") {
                    alert("From Floor shouldn't be empty")
                    return

                } else if (document.getElementById('tstore').value == "") {
                    alert("From Store shouldn't be empty")
                    return

                } else if (document.getElementById('tfloor').value == "") {
                    alert("To Floor shouldn't be empty")
                    return

                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else {
                    arrItem2.push({
                        product: document.getElementById('product' + i).value,
                        fstore: document.getElementById('fstore').value,
                        ffloor: document.getElementById('ffloor').value,
                        tstore: document.getElementById('tstore').value,
                        tfloor: document.getElementById('tfloor').value,
                        quantity: document.getElementById('qty' + i).value,
                        date: document.getElementById('date').value,
                        details: document.getElementById('details').value,
                        batch: document.getElementById('batch' + i).value
                    });
                }
            }

        }
        let check2 = valcheck();

        if (!check2) {
            alert("You can't use  same (product,batch)  in multiple rows")
            return
        }


        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/update_nststock',
                type: 'POST',
                data: {
                    items: arrItem2,
                    id: id
                },
                success: function(response) {
                    alert("Store Transfer Updated Successfully")
                    window.location.href = $('#baseUrl2').val() + 'manage_store_transfer';


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/save_nststock',
                type: 'POST',
                data: {
                    items: arrItem2
                },
                success: function(response) {
                    alert("Store Transfer added Successfully")
                    window.location.href = $('#baseUrl2').val() + 'manage_store_transfer';



                },
                error: function(error) {
                    console.log(error)
                }
            });

        }







    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';
    }

    function valcheck() {
        arrItem = [];

        if (count > 2) {
            for (let i = 1; i < count; i++) {
                if (document.getElementById('myRow' + i).style.display != "none") {
                    let check = arrItem.find(item => item.product == document.getElementById('product' + i).value && item.batch == document.getElementById('batch' + i).value);

                    if (check != undefined) {
                        if (check.product != '') {
                            return false
                        } else {
                            arrItem.push({
                                product: document.getElementById('product' + i).value,
                                batch: document.getElementById('batch' + i).value
                            });
                        }

                    } else {
                        arrItem.push({
                            product: document.getElementById('product' + i).value,
                            batch: document.getElementById('batch' + i).value

                        });
                    }
                }

            }

        }
        return true;

    }


    function quantity_calculate(item, name) {
        if (name === "product") {

            var $batchDropdown = $('#batch' + item);
            $batchDropdown.empty();
            document.getElementById('qty' + item).value = "";
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/stock_batchdetailsbyprodidandstorefloor',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    store: document.getElementById('fstore' ).value.toString(),
                    floor: document.getElementById('ffloor' ).value.toString(),

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
            document.getElementById('qty' + item).value = "";
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/avg_avstock',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    batchid: document.getElementById('batch' + item).value.toString(),
                    storeid: document.getElementById('fstore').value.toString(),
                    floorid: document.getElementById('ffloor').value.toString()


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



        if (name === "qty") {



        }



    }

    function getActiveProduct(prods,productId, item) {

        var $productDropdown = $('#product' + item);
        $productDropdown.empty();
        $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

        $.each(prods, function(index, product) {
            $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
        });

        if (productId > 0) {
            {
                $productDropdown.val(productId)
            }
        }
    }

    function getActiveBatch(batchId, item) {
        console.log(batches)
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
        var $storeDropdown = $('#' + item + 'store');
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
        var $floorDropdown = $('#' + item + 'floor');
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




    $('#fstore').on('change', function() {
        removeAllRows();

        let floors = wholefloors.filter(s => s.storeid == $(this).val());
        var $floorDropdown = $('#ffloor');
        $floorDropdown.empty();
        $floorDropdown.append('<option value="" disabled selected>Select Floor</option>'); // Add default option

        $.each(floors, function(index, floor) {
            $floorDropdown.append('<option value="' + floor.id + '">' + floor.name + '</option>');
        });

    });


    $('#tstore').on('change', function() {
        let floors = wholefloors.filter(s => s.storeid == $(this).val());

        let $floorDropdown = $('#tfloor');
        $floorDropdown.empty();
        $floorDropdown.append('<option value="" disabled selected>Select Floor</option>'); // Add default option

        $.each(floors, function(index, floor) {
            $floorDropdown.append('<option value="' + floor.id + '">' + floor.name + '</option>');
        });
    });



    $('#ffloor').on('change', function() {
        removeAllRows();
        let $productDropdown = $('#product1');

        var selectedValue = $(this).val();
        let prods = products.filter(s => s.store == document.getElementById("fstore").value && s.floor == selectedValue);

        $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option
        $.each(prods, function(index, prod) {
            $productDropdown.append('<option value="' + prod.id + '">' + prod.product_name + '</option>');
        });
    });

    function removeAllRows() {
        let $productDropdown = $('#product1');
        $productDropdown.empty();

        let $batchDropdown = $('#batch1');
        $batchDropdown.empty();
        document.getElementById('qty1').value = '';
        document.getElementById('avqty1').value = '';
        document.getElementById('unit1').value = '';



        for (let i = 2; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                document.getElementById('myRow' + i).style.display = 'none';

            }
        }
    }
</script>