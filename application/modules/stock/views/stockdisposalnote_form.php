<style>
    .product_field {
        width: 220px;
    }

    .field {
        width: 170px;
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
                            <label for="address" class="col-sm-4 col-form-label">Reason
                            </label>
                            <div class="col-sm-8">

                                <?php if (!empty($id)) { ?>
                                    <textarea tabindex="" class="form-control" id="reason" name="reason" placeholder="Reasons" rows="5" readonly></textarea>
                                <?php } else { ?>
                                    <textarea tabindex="" class="form-control" id="reason" name="reason" placeholder="Reasons" rows="5"></textarea>

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
                            <th class="text-center ">Dam.Qty<i
                                    class="text-danger">*</i></th>
                            <th class="text-center ">Dam.Cost<i
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
                            <td class="field">
                                <select class="form-control" id="batch1" required name="batch[]" tabindex="2" onchange="quantity_calculate(1,'batch')">
                                    <option value=""></option>
                                </select>
                            </td>

                            <td class="field">
                                <select class="form-control" id="store1" required name="store[]" tabindex="3" onchange="quantity_calculate(1,'store')">
                                    <option value=""></option>
                                </select>
                            </td>
                            <td class="field">
                                <select class="form-control" id="floor1" required name="floor[]" tabindex="4" onchange="quantity_calculate(1,'floor')">
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
                            <td class="field">
                                <input type="text" name="cost[]" onkeyup="quantity_calculate(1,'cost');"
                                    class="total_qntt_1 form-control text-right"
                                    id="cost1" value="" min="0" tabindex="6" />
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

                                <td class="field">
                                    <select name="store[]" class="form-control" id="store<?php echo $i; ?>" tabindex="3" onchange="quantity_calculate(<?php echo $i; ?>, 'store')" required>
                                        <option value="">Select Store</option>
                                    </select>
                                </td>
                                <td class="field">
                                    <select class="form-control" id="floor<?php echo $i; ?>" required name="floor[]" tabindex="4" onchange="quantity_calculate(<?php echo $i; ?>, 'floor')">
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

                                <td class="field">
                                    <input type="text" name="cost[]" onkeyup="quantity_calculate(<?php echo $i; ?>,'cost');"
                                        class="total_qntt_1 form-control text-right"
                                        id="cost<?php echo $i; ?>" value="" min="0" tabindex="6" />
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
                url: $('#baseUrl2').val() + 'stock/stock/getDamageStockById',
                type: 'POST',
                data: {
                    pid: id,
                },
                success: function(response) {
                    var damageStocks = JSON.parse(response);
                    count = 1;
                    for (let i = 0; i < damageStocks.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';

                        // Call other functions based on data
                        getActiveProduct(damageStocks[i].product, a);
                        getActiveBatch(damageStocks[i].batch_id, a)
                        getActiveStore(damageStocks[i].store, a);
                        getActiveFloor(damageStocks[i].store, damageStocks[i].floor, a);

                        // Set form values
                        document.getElementById('qty' + a).value = Math.abs(damageStocks[i].actualstock);
                        document.getElementById('unit' + a).value = damageStocks[i].unit;
                        document.getElementById('avqty' + a).value = damageStocks[i].avstock;;
                        document.getElementById('date').value = damageStocks[i].date;
                        document.getElementById('reason').value = damageStocks[i].reason;

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


        // } else {
        //     alert("You can't add more than 10 rows")
        // }

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
                } else if (document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('floor' + i).value == "") {
                    alert("Floor shouldn't be empty")
                    return

                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else {
                    arrItem2.push({
                        product: document.getElementById('product' + i).value,
                        store: document.getElementById('store' + i).value,
                        floor: document.getElementById('floor' + i).value,
                        quantity: document.getElementById('qty' + i).value,
                        date: document.getElementById('date').value,
                        reason: document.getElementById('reason').value,
                        batch: document.getElementById('batch' + i).value


                    });
                }
            }

        }
        let check2 = valcheck();

        if (!check2) {
            alert("You can't use  same (product,store,floor,batch)  in multiple rows")
            return
        }


        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/update_damstock',
                type: 'POST',
                data: {
                    items: arrItem2,
                    id: id
                },
                success: function(response) {
                    alert("Stock Disposal Note Updated Successfully")
                    window.location.href = $('#baseUrl2').val() + 'manage_stockdisposalnote';


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/save_damstock',
                type: 'POST',
                data: {
                    items: arrItem2
                },
                success: function(response) {
                    alert("Stock Disposal Note added Successfully")
                    window.location.href = $('#baseUrl2').val() + 'manage_stockdisposalnote';



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

                    let check = arrItem.find(item => item.product == document.getElementById('product' + i).value &&
                        item.store == document.getElementById('store' + i).value &&
                        item.floor == document.getElementById('floor' + i).value && item.batch == document.getElementById('batch' + i).value);

                    if (check != undefined) {
                        if (check.product != '') {
                            return false
                        } else {
                            arrItem.push({
                                product: document.getElementById('product' + i).value,
                                store: document.getElementById('store' + i).value,
                                floor: document.getElementById('floor' + i).value,
                                batch: document.getElementById('batch' + i).value
                            });
                        }

                    } else {
                        arrItem.push({
                            product: document.getElementById('product' + i).value,
                            store: document.getElementById('store' + i).value,
                            floor: document.getElementById('floor' + i).value,
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
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            var $floorDropdown = $('#floor' + item);
            $floorDropdown.empty();
            document.getElementById('avqty' + item).value = "";
            document.getElementById('qty' + item).value = "";
            document.getElementById('cost' + item).value = "";



            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/stock_batchdetailsbyprodid',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    batches = JSON.parse(response);
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
            document.getElementById('qty' + item).value = "";
            document.getElementById('cost' + item).value = "";
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/store_detailsbybatchidandprodid',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    batchid: document.getElementById('batch' + item).value.toString()

                },
                success: function(response) {
                    stores = JSON.parse(response);
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
            document.getElementById('qty' + item).value = "";
            document.getElementById('cost' + item).value = "";
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
                    stock = JSON.parse(response);

                    document.getElementById('avqty' + item).value = stock[0].avgqty;
                    //getFloorsDropdown(floors, item);

                },
                error: function(error) {
                    console.log(error)
                }
            });
        }

        if (name === "qty") {

            if (id == null) {
                if (parseInt(document.getElementById("qty" + item).value) > parseInt(document.getElementById("avqty" + item).value)) {
                    document.getElementById("qty" + item).value = "";
                    alert("Entered qty more than available qty")
                }
            }

        }



    }

    function afterdelete(item, productId, floorId, storeId, batchId) {
        getActiveProduct(productId, item)
        getActiveBatch(batchId, item)
        getActiveStore(storeId, item);
        getActiveFloor(storeId, floorId, item)
        batches
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