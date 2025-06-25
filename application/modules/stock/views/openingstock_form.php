<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="batch" class="col-sm-4 col-form-label">Batch
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">

                                <?php if (!empty($id)) { ?>
                                    <input class="form-control" name="batchname" type="text" id="batchname" tabindex="1" readonly>
                                    <input class="form-control" name="batch" type="hidden" id="batch" tabindex="1" readonly>

                                <?php } else { ?>
                                    <select class="form-control" id="batch" required name="batch" tabindex="3">
                                        <option value=""></option>
                                        <?php if ($batches) { ?>
                                            <?php foreach ($batches as $categories) { ?>
                                                <option value="<?php echo $categories['id'] ?>">
                                                    <?php echo $categories['batchid'] ?></option>



                                        <?php }
                                        } ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                        <br />
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
                                    <textarea tabindex="" class="form-control" id="details" name="details" placeholder="Details" rows="5" readonly></textarea>
                                <?php } else { ?>
                                    <textarea tabindex="" class="form-control" id="details" name="details" placeholder="Details" rows="5"></textarea>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div >
                <table class="table table-bordered table-hover" id="normalinvoice">
                    <thead>
                        <tr>
                            <th class="text-center product_field">Product<i
                                    class="text-danger">*</i></th>
                            <th class="text-center">Store<i class="text-danger">*</i>
                            </th>
                            <th class="text-center ">Floor <i class="text-danger">*</i>
                            </th>
                            <th class="text-center ">Unit </th>
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

                            <td class="product_field">
                                <select name="store[]" class="form-control" id="store1" tabindex="2" onchange="quantity_calculate(1,'store')" required>
                                    <option value="">Select Store</option>
                                    <?php foreach ($store_list as $services) {
                                        echo $services['id']; ?>
                                        <option value="<?php echo $services['id']; ?>"><?php echo $services['name']; ?></option>

                                    <?php  }   ?>
                                </select>
                            </td>
                            <td class="product_field">
                                <select class="form-control" id="floor1" required name="floor[]" tabindex="3" onchange="quantity_calculate(1,'floor')">
                                    <option value=""></option>

                                </select>
                            </td>
                            <td class="product_field">
                                <input type="text" name="unit[]" onkeyup="quantity_calculate(1,'unit');"
                                    onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                    id="unit1" value="" min="0" tabindex="4" readonly />
                            </td>

                            <td class="product_field">
                                <input type="number" name="qty[]" required onkeyup="quantity_calculate(1,'qty');"
                                    onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                    id="qty1" placeholder="0.00" value="1" min="0" tabindex="5" />
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

                                <td class="product_field">
                                    <select name="store[]" class="form-control" id="store<?php echo $i; ?>" tabindex="2" onchange="quantity_calculate(<?php echo $i; ?>, 'store')" required>
                                        <option value="">Select Store</option>
                                    </select>
                                </td>
                                <td class="product_field">
                                    <select class="form-control" id="floor<?php echo $i; ?>" required name="floor[]" tabindex="3" onchange="quantity_calculate(<?php echo $i; ?>, 'floor')">
                                        <option value=""></option>
                                    </select>
                                </td>
                                <td class="product_field">
                                    <input type="text" name="unit[]" onkeyup="quantity_calculate(<?php echo $i; ?>, 'unit');"
                                        onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                        id="unit<?php echo $i; ?>" value="" min="0" tabindex="4" readonly />
                                </td>

                                <td class="product_field">
                                    <input type="number" name="qty[]" onkeyup="quantity_calculate(<?php echo $i; ?>, 'qty');"
                                        onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                        id="qty<?php echo $i; ?>" placeholder="0.00" value="1" min="0" tabindex="5" />
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
                            <td colspan="8" rowspan="2">
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
echo "let products=".json_encode($products).";";
echo "let stores=".json_encode($store_list).";";
echo "let wholefloors=".json_encode($floor_list).";";

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
                        url: $('#baseUrl2').val() + 'stock/stock/getOpeningStockById',
                        type: 'POST',
                        data: {
                            pid: id,
                        },
                        success: function(response) {
                            var openingstocks = JSON.parse(response);
                            console.log(openingstocks)
                             count = 1;
                            for (let i = 0; i < openingstocks.length; i++) {
                                let a = i + 1;
                                document.getElementById('myRow' + a).style.display = 'table-row';

                                // Call other functions based on data
                                getActiveProduct(openingstocks[i].product, a);
                                getActiveStore(openingstocks[i].store, a);
                                getActiveFloor(openingstocks[i].store, openingstocks[i].floor, a);

                                // Set form values
                                document.getElementById('qty' + a).value = openingstocks[i].actualstock;
                                document.getElementById('unit' + a).value = openingstocks[i].unit;
                                document.getElementById('batch').value = openingstocks[i].batch_id;
                                document.getElementById('batchname').value = openingstocks[i].batchid;
                                document.getElementById('date').value = openingstocks[i].date;
                                document.getElementById('details').value = openingstocks[i].details;

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
        if (count < 11) {
            document.getElementById('myRow' + count).style.display = 'table-row';
            getActiveStore(0, count);
            getActiveProduct(0, count)
            count = count + 1;


        } else {
            alert("You can't add more than 10 rows")
        }

    }

    function save() {
        arrItem2 = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('batch').value == "") {
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
                    details: document.getElementById('details').value,
                    batch: document.getElementById('batch').value


                });
            }

        }
        let check2 = valcheck();

        if (!check2) {
            alert("You can't use  same (product,store,floor)  in multiple rows")
            return
        }


        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/update_openingstock',
                type: 'POST',
                data: {
                    items: arrItem2,
                    id:id
                },
                success: function(response) {
                    alert("Opening stock Updated Successfully")
                    window.location.href = $('#baseUrl2').val() + 'openingstockbatchlist';


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/save_openingstock',
                type: 'POST',
                data: {
                    items: arrItem2,
                },
                success: function(response) {
                    alert("Opening stock added Successfully")
                    window.location.href = $('#baseUrl2').val() + 'openingstockbatchlist';



                },
                error: function(error) {
                    console.log(error)
                }
            });

        }







    }

    function deleteRow(num) {
        arrItemDelete = [];
        for (let i = 1; i < count; i++) {
            arrItemDelete.push({
                product: document.getElementById('product' + i).value,
                store: document.getElementById('store' + i).value,
                floor: document.getElementById('floor' + i).value,
                qty: document.getElementById('qty' + i).value,
                unit: document.getElementById('unit' + i).value


            });
            var $floorDropdown = $('#floor' + i);
            $floorDropdown.empty();

            var $productDropdown = $('#product' + i);
            $productDropdown.empty();

            var $storeDropdown = $('#store' + i);
            $storeDropdown.empty();
            document.getElementById('qty' + i).value = 1;
            document.getElementById('unit' + i).value = "";



        }

        arrItemDelete.splice(num - 1, 1);
        count = count - 1;

        for (let i = 0; i < arrItemDelete.length; i++) {

            afterdelete(i + 1, arrItemDelete[i].product, arrItemDelete[i].floor, arrItemDelete[i].store)
            ind = i + 1
            document.getElementById('qty' + ind).value = arrItemDelete[i].qty;
            document.getElementById('unit' + ind).value = arrItemDelete[i].unit;
        }

        document.getElementById('myRow' + count).style.display = 'none';






    }

    function valcheck() {
        arrItem = [];

        if (count > 2) {
            for (let i = 1; i < count; i++) {
                let check = arrItem.find(item => item.product == document.getElementById('product' + i).value &&
                    item.store == document.getElementById('store' + i).value &&
                    item.floor == document.getElementById('floor' + i).value);

                if (check != undefined) {
                    if (check.product != '') {
                        return false
                    } else {
                        arrItem.push({
                            product: document.getElementById('product' + i).value,
                            store: document.getElementById('store' + i).value,
                            floor: document.getElementById('floor' + i).value,

                        });
                    }

                } else {
                    arrItem.push({
                        product: document.getElementById('product' + i).value,
                        store: document.getElementById('store' + i).value,
                        floor: document.getElementById('floor' + i).value,

                    });
                }
            }

        }
        return true;

    }


    function quantity_calculate(item, name) {
        if (name === "product") {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getProductById',
                type: 'POST',
                data: {
                    productid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    product = JSON.parse(response);
                    getActiveStore(product[0].store, item);
                    getActiveFloor(product[0].store, product[0].floor, item)
                    document.getElementById('unit' + item).value = product[0].unit;


                },
                error: function(error) {
                    console.log(error)
                }
            });
        }

        if (name === "store") {
            getActiveFloor(document.getElementById('store' + item).value, 0, item)
        }


        if (name === "floor") {
            //  getActiveFloor(document.getElementById('store' + item).value,0, item)
        }



    }

    function afterdelete(item, productId, floorId, storeId) {

        getActiveProduct(productId, item)
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
 
</script>