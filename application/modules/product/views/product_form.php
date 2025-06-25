<script src="<?php echo base_url() ?>my-assets/js/admin_js/json/product.js" type="text/javascript"></script>
<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>

            <?php echo form_open_multipart('product_form/' . $id, array('class' => 'form-vertical', 'id' => 'insert_product', 'name' => 'insert_product',  'onsubmit' => 'return validateForm(event)')) ?>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="barcode_or_qrcode" class="col-sm-2 col-form-label"><?php echo display('barcode_or_qrcode') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-10">
                                <?php if (!empty($id)) { ?>
                                    <input class="form-control" name="product_id" type="text" id="product_id" placeholder="<?php echo display('barcode_or_qrcode') ?>" tabindex="1" value="<?php echo $product->product_id ?>" readonly>
                                <?php } else { ?>
                                    <input class="form-control" name="product_id" type="text" id="product_id" placeholder="<?php echo display('barcode_or_qrcode') ?>" tabindex="1" value="<?php echo $productId ?>">
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="button" id="button" value="akakak" />


                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="product_name" class="col-sm-4 col-form-label"><?php echo display('product_name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="product_name" type="text" id="product_name" placeholder="<?php echo display('product_name') ?>" value="<?php echo $product->product_name ?>" required tabindex="1">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="serial_no" class="col-sm-4 col-form-label"><?php echo display('serial_no') ?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" tabindex="" class="form-control " id="serial_no" name="serial_no" placeholder="111,abc,XYz" value="<?php echo $product->serial_no ?>" />
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="category_id" class="col-sm-4 col-form-label"><?php echo display('category') ?>
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="category_id" required name="category_id" tabindex="3">
                                    <option value=""></option>
                                    <?php if ($category_list) { ?>
                                        <?php foreach ($category_list as $categories) { ?>
                                            <option value="<?php echo $categories['category_id'] ?>" <?php if ($product->category_id == $categories['category_id']) {
                                                                                                            echo 'selected';
                                                                                                        } ?>>
                                                <?php echo $categories['category_name'] ?></option>

                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="product_model" class="col-sm-4 col-form-label"><?php echo display('model') ?> </label>
                            <div class="col-sm-8">
                                <input type="text" tabindex="" class="form-control" id="product_model" name="model" placeholder="<?php echo display('model') ?>" value="<?php echo $product->product_model ?>" />
                            </div>
                        </div>
                    </div>


                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="sell_price" class="col-sm-4 col-form-label">Sale Price
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-right" id="sell_price" name="price" type="text" placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->price ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="unit" class="col-sm-4 col-form-label"><?php echo display('unit') ?><i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="unit" name="unit" required tabindex="-1" aria-hidden="true">
                                    <option value="">Select One</option>
                                    <?php if ($unit_list) { ?>
                                        <?php foreach ($unit_list as $units) { ?>
                                            <option value="<?php echo $units['unit_name'] ?>" <?php if ($product->unit == $units['unit_name']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>
                                                <?php echo $units['unit_name'] ?></option>

                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php if (empty($id)) { ?>
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label for="cost_price" class="col-sm-4 col-form-label">Purchase Price
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control text-right" id="cost_price" name="cost_price" type="text" placeholder="0.00" tabindex="5" min="0">
                                </div>
                            </div>
                        </div>
                    <?php } else {
                        // foreach ($supplier_pr as $supplier_product) {
                    ?>
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label for="cost_price" class="col-sm-4 col-form-label">Purchase Price
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control text-right" id="cost_price" name="cost_price" type="text" placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->cost_price ?>">
                                </div>
                            </div>
                        </div>
                    <?php
                        // }
                    } ?>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="serial_no" class="col-sm-4 col-form-label"><?php echo display('product_details') ?> </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="description" id="description" rows="1" placeholder="<?php echo display('product_details') ?>" tabindex="2"><?php echo $product->product_details ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="store" class="col-sm-4 col-form-label">Default Store
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="store" required name="store" tabindex="3">
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
                            <label for="status" class="col-sm-4 col-form-label">Status<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" required tabindex="-1" aria-hidden="true">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($product->status_label == "Active") ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($product->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                            </div>
                        </div>
                    </div>


                    <?php if ($vtinfo->ischecked == 1) { ?>
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label for="cost_price" class="col-sm-4 col-form-label">Product VAT %
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control text-right" id="vat" name="vat" type="text" placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->product_vat ?>">
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" class="form-control" name="vat" id="vat" value="0.0">
                    <?php } ?>



                </div>
                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <?php echo (empty($product->id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($product->id)) { ?>
                            <button type="submit" class="btn btn-success" name="add-another">
                                <?php echo display('save_and_add_another'); ?>
                            </button>

                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>
        <?php echo form_close() ?>
    </div>
</div>
<?php
echo "<script>";
echo "var id = " . json_encode($id) . ";";
echo "var floorId = " . json_encode($product->floor) . ";";
echo "var storeId = " . json_encode($product->store) . ";";


echo "</script>";
?>

<script>
    if (floorId > 0) {
        onChangeStore(storeId, floorId);

    }
    let code = 0;

    if (id != null) {
        code = document.getElementById("product_id").value.toString();
    }

    function validateForm(event) {
        // Prevent default form submission
        event.preventDefault();

        // Identify which button was clicked
        const buttonName = event.submitter.name; // Get the name of the button that was clicked
        async function checkProduct() {
            try {
                let response = await $.ajax({
                    type: "POST",
                    url: $('#baseUrl2').val() + 'product/product/getProductById',
                    data: {
                        code: document.getElementById('product_id').value.toString().padStart(6, '0'),
                    }
                });

                let data = JSON.parse(response);
                if (data === "success") {
                    return true;
                } else {
                    if (code == document.getElementById('product_id').value.toString().padStart(6, '0')) {
                        return true;
                    } else {
                        alert("Product code already exists");
                        return false;
                    }
                }

            } catch (error) {
                alert("An error occurred: " + error);
                return false;
            }
        }

        checkProduct().then((isValid) => {
            if (isValid) {
                if (buttonName === 'save') {
                    document.getElementById('button').value = "save";
                    document.getElementById('insert_product').submit();
                } else if (buttonName === 'add-another') {
                    document.getElementById('button').value = "add-another";
                    document.getElementById('insert_product').submit();
                }
            } else {
                return false;
            }
        });
    }
</script>