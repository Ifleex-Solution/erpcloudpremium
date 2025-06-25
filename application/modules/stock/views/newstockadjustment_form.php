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
                            <label for="date" class="col-sm-4 col-form-label">Date
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d');
                                ?>

                                <?php if (!empty($id)) { ?>
                                    <input class="form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" readonly />
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

                            <textarea tabindex="" class="form-control" id="reason" name="reason" placeholder="Reasons" rows="3"></textarea>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="type" required name="type" tabindex="3" onchange="change_type()">
                                    <option value=""></option>
                                    <option value="openingstock">Opening Stock</option>
                                    <option value="storetransfer">Store Transfer</option>
                                    <option value="stockdisposal">Stock Disposal</option>
                                    <option value="stockadjustment">Stock Adjustment</option>

                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Stock Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="stocktype" required name="type" tabindex="3" onchange="quantity_calculate(0,'stocktype')">
                                    <!-- <option value=""></option>
                                    <option value="actualstock">Actual Stock</option>
                                    <option value="physicalstock">Physical Stock</option>
                                    <option value="both">Both</option> -->

                                </select>
                            </div>

                        </div>
                    </div>

                </div>


            </div>

            <div style="margin: 20px;">
                <table class="table table-bordered table-hover" id="normalinvoice">
                    <thead>
                        <tr>
                            <th class="text-center product_field">Product<i
                                    class="text-danger">*</i></th>

                            <th class="text-center">Store<i class="text-danger">*</i>
                            </th>

                            <th class="text-center ">Unit </th>
                            <th class="text-center ">Av.Qty</th>
                            <th class="text-center ">Adj.Type<i
                                    class="text-danger">*</i></th>
                            <th class="text-center ">Adj.Qty<i
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
                                <select class="form-control" id="store1" required name="store[]" tabindex="3" onchange="quantity_calculate(1,'store')">
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
                            <td class="field">


                                <select class="form-control" id="adj1" required name="adj[]" tabindex="4" onchange="quantity_calculate(1,'adj')">
                                    <option value=""></option>

                                    <!-- <option value="increase">Increase</option>
                                    <option value="decrease">Decrease</option> -->


                                </select>
                            </td>
                            <td class="qty">
                                <input type="number" name="qty[]" required onkeyup="quantity_calculate(1,'qty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="qty1" placeholder="0.00" min="0"  min="0" oninput="if(this.value < 0) this.value = ''"  tabindex="5" />
                            </td>

                            <td>
                            </td>

                        </tr>

                        <?php
                        // Assuming you want to generate 5 rows dynamically
                        for ($i = 2; $i <= 20; $i++) {
                        ?>
                            <tr id="myRow<?php echo $i; ?>">
                                <td class="product_field">
                                    <select name="product[]" class="form-control" id="product<?php echo $i; ?>" tabindex="1" onchange="quantity_calculate(<?php echo $i; ?>, 'product')" required>
                                        <option value="">Select Product</option>
                                    </select>
                                </td>

                                <td class="field">
                                    <select name="store[]" class="form-control" id="store<?php echo $i; ?>" tabindex="3" onchange="quantity_calculate(<?php echo $i; ?>, 'store')" required>
                                        <option value="">Select Store</option>
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
                                <td class="field">


                                    <select class="form-control" id="adj<?php echo $i; ?>" required name="adj[]" tabindex="4" onchange="quantity_calculate(<?php echo $i; ?>,'adj')">
                                        <option value=""></option>
                                        <!-- <option value="increase">Increase</option>
                                        <option value="decrease">Decrease</option>
 -->

                                    </select>
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
echo "let type=" . json_encode($type) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "</script>";
?>

<script>
    let count = 2

    let oldType = "";

    $(document).ready(function() {
        let type2 = ""

        if (usertype == 3) {
            document.getElementById('style12').style.backgroundColor = '#E0E0E0';
            const title = document.getElementById('title');
            title.style.color = 'blue';
            type2 = "B"

        } else {
            type2 = "A"

        }
    });

    function change_type() {
        if (document.getElementById('type').value === "openingstock") {

            var $stocktypeDropdown = $('#stocktype');
            $stocktypeDropdown.empty();
            $stocktypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $stocktypeDropdown.append('<option value="both">Both</option>');
        }

        if (document.getElementById('type').value === "storetransfer") {

            var $stocktypeDropdown = $('#stocktype');
            $stocktypeDropdown.empty();
            $stocktypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $stocktypeDropdown.append('<option value="actualstock">Actual Stock</option>');
        }

        if (document.getElementById('type').value === "stockdisposal") {

            var $stocktypeDropdown = $('#stocktype');
            $stocktypeDropdown.empty();
            $stocktypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $stocktypeDropdown.append('<option value="actualstock">Actual Stock</option>');

        }

        if (document.getElementById('type').value === "stockadjustment") {

            var $stocktypeDropdown = $('#stocktype');
            $stocktypeDropdown.empty();
            $stocktypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $stocktypeDropdown.append('<option value="actualstock">Actual Stock</option>');
            $stocktypeDropdown.append('<option value="physicalstock">Physical Stock</option>');
            $stocktypeDropdown.append('<option value="both">Both</option>');
        }

        clearTable();
        adj_type(1)

    }

    function adj_type(num) {
        if (document.getElementById('type').value === "openingstock") {
            var $adjtypeDropdown = $('#adj' + num);
            $adjtypeDropdown.empty();
            $adjtypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $adjtypeDropdown.append('<option value="increase">Increase</option>');
        }

        if (document.getElementById('type').value === "storetransfer") {
            var $adjtypeDropdown = $('#adj' + num);
            $adjtypeDropdown.empty();
            $adjtypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $adjtypeDropdown.append('<option value="increase">Increase</option>');
            $adjtypeDropdown.append('<option value="decrease">Decrease</option>');

        }

        if (document.getElementById('type').value === "stockdisposal") {
            var $adjtypeDropdown = $('#adj' + num);
            $adjtypeDropdown.empty();
            $adjtypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $adjtypeDropdown.append('<option value="decrease">Decrease</option>');
        }

        if (document.getElementById('type').value === "stockadjustment") {
            var $adjtypeDropdown = $('#adj' + num);
            $adjtypeDropdown.empty();
            $adjtypeDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option
            $adjtypeDropdown.append('<option value="increase">Increase</option>');
            $adjtypeDropdown.append('<option value="decrease">Decrease</option>');
        }

    }

    $(document).ready(function() {

        for (let i = 2; i <= 20; i++) {
            document.getElementById('myRow' + i).style.display = 'none';

        }
        if (id != null) {
            document.getElementById('type').value = type.type;
            change_type()
            document.getElementById('stocktype').value = type.stocktype;

            oldType = type.stocktype;


            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getAdjStockById',
                type: 'POST',
                data: {
                    pid: id,
                    // type2: "A",
                    type: type.stocktype
                },
                success: function(response) {
                    var adjStocks = JSON.parse(response);
                    count = 1;
                    for (let i = 0; i < adjStocks.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';

                        // Call other functions based on data
                        getActiveProduct(adjStocks[i].product, a);
                        getActiveStore(adjStocks[i].store, a);
                        getAdjDropdown(adjStocks[i].actualstock > 0 ? "increase" : "decrease", a)
                        // Set form values
                        document.getElementById('qty' + a).value = Math.abs(adjStocks[i].actualstock);
                        document.getElementById('unit' + a).value = adjStocks[i].unit;
                        if (type.stocktype != "both") {
                            document.getElementById('avqty' + a).value = adjStocks[i].avstock;

                        }

                        document.getElementById('date').value = adjStocks[i].date;
                        document.getElementById('reason').value = adjStocks[i].reason;


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
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveStore(0, count);
        getActiveProduct(0, count)
        count = count + 1;
        adj_type(count)

    }

    function save() {
        arrItem2 = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {

                if (document.getElementById('stocktype').value == "") {
                    alert("Stock Type shouldn't be empty")
                    return
                } else if (document.getElementById('type').value == "") {
                    alert("Type shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else if (document.getElementById('adj' + i).value == "") {
                    alert("Adj Type shouldn't be empty")
                    return
                } else {
                    arrItem2.push({
                        product: document.getElementById('product' + i).value,
                        store: document.getElementById('store' + i).value,
                        quantity: document.getElementById('qty' + i).value,
                        date: document.getElementById('date').value,
                        reason: document.getElementById('reason').value,
                        adj: document.getElementById('adj' + i).value,
                        type: document.getElementById('type').value,
                        stocktype: document.getElementById('stocktype').value,
                        type2: "A"
                    });
                }
            }

        }
        let check2 = valcheck();

        if (!check2) {
            alert("You can't use  same (product,store)  in multiple rows")
            return
        }

        $("#save_add").hide();

        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/update_adjstock',
                type: 'POST',
                data: {
                    items: arrItem2,
                    id: id,
                    oldType: oldType
                },
                success: function(response) {
                    alert("Stock Adjustment Updated Successfully")
                    $("#save_add").show();

                    window.location.href = $('#baseUrl2').val() + 'manage_stock_adjustment';


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/save_adjstock',
                type: 'POST',
                data: {
                    items: arrItem2
                },
                success: function(response) {
                    alert("Stock Adjustment added Successfully")
                    $("#save_add").show();

                    window.location.href = $('#baseUrl2').val() + 'manage_stock_adjustment';



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

    function clearTable() {
        //document.getElementById('receivedfrom').value = ""

        for (let i = 2; i <= 20; i++) {
            document.getElementById('myRow' + i).style.display = 'none';
        }
        getActiveProduct(0, 1);
        getActiveStore(0, 1);
        //getAdjDropdown("", 1)
        adj_type(1)
        document.getElementById('qty' + 1).value = "";
        document.getElementById('unit' + 1).value = "";
        document.getElementById('avqty' + 1).value = "";
    }


    function quantity_calculate(item, name) {

       
        if (name === "stocktype") {
            clearTable();
        }

        if (document.getElementById('stocktype').value == "") {
            alert("Stock Type shouldn't be empty")
            getActiveProduct(0, item);
            getActiveStore(0, item);
            // getAdjDropdown("", item)
            adj_type(item)
            document.getElementById('qty' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('avqty' + item).value = "";
            return
        }
        if (name === "product") {
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            //getAdjDropdown(0, item)
            adj_type(item)
            document.getElementById('avqty' + item).value = "";
            document.getElementById('qty' + item).value = "";
            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    getActiveStore(product[0].store, item);
                    avStock(item)

                    document.getElementById('unit' + item).value = product[0].unit;
                },
                error: function(error) {
                    console.log(error)
                }
            });
        }



        if (name === "store") {

            // var $floorDropdown = $('#floor' + item);
            // $floorDropdown.empty();
            avStock(item)
        }



        if (name === "qty") {

            if( document.getElementById("qty" + item).value=="-"){
                document.getElementById("qty" + item).value="";
            }

            if (id == null) {
                let qty = 0;
                if (document.getElementById("adj" + item).value === "decrease") {
                    qty = parseInt(document.getElementById("avqty" + item).value) - parseInt(document.getElementById("qty" + item).value)

                }


                if (qty < 0) {
                    document.getElementById("qty" + item).value = "";
                    alert("While decrease the qty shouldn't be less than available qty")
                }
            }

        }



    }

    function avStock(item) {
        document.getElementById('avqty' + item).value = "";
        document.getElementById('qty' + item).value = "";
        //  getAdjDropdown(0, item);
        adj_type(item);

        if (document.getElementById('stocktype').value == "actualstock") {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/avg_avstock',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    storeid: document.getElementById('store' + item).value.toString(),
                    // type2: "A"

                },
                success: function(response) {
                    console.log(response);
                    let stock = JSON.parse(response);
                    document.getElementById('avqty' + item).value = stock[0].avgqty == null ? 0 : stock[0].avgqty;

                },
                error: function(error) {
                    console.log(error)
                }
            });
        } else if (document.getElementById('stocktype').value == "physicalstock") {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/avg_phystock',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                    storeid: document.getElementById('store' + item).value.toString(),
                    // type2: "A"

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

    }

    // function afterdelete(item, productId, floorId, storeId, batchId) {
    //     getActiveProduct(productId, item)
    //     getActiveBatch(batchId, item)
    //     getActiveStore(storeId, item);
    //     getActiveFloor(storeId, floorId, item)

    // }

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


    function getAdjDropdown(adjId, item) {
        var $adjDropdown = $('#adj' + item);
        $adjDropdown.empty();
        $adjDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option

        $adjDropdown.append('<option value="increase">Increase</option>');
        $adjDropdown.append('<option value="decrease">Decrease</option>');



        if (adjId != "") {

            $adjDropdown.val(adjId)

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
</script>