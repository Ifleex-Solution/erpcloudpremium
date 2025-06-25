<script src="<?php echo base_url() ?>my-assets/js/admin_js/json/product.js" type="text/javascript"></script>
<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js" type="text/javascript"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <?php echo form_open_multipart('store_form/' . $store->id, array('class' => 'form-vertical', 'id' => 'insert_store', 'name' => 'insert_store', 'onsubmit' => 'return validateForm(event)')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="code" class="col-sm-4 col-form-label">Store Code<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php if (!empty($store->id)) { ?>
                                    <input class="form-control" name="code" type="text" id="code" placeholder="Store Code" tabindex="1" value="<?php echo $store->code ?>" readonly>
                                <?php } else { ?>
                                    <input class="form-control" name="code" type="text" id="code" placeholder="Store Code" tabindex="1" value="<?php echo $storeid ?>">
                                <?php } ?>
                                <input type="hidden" name="button" id="button" />

                            </div>
                        </div>
                    </div>


                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">Store Name<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" tabindex="" class="form-control" id="name" name="name" placeholder="Store Name" value="<?php echo $store->name ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">GRN<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="auto_grn" name="auto_grn" tabindex="-1" aria-hidden="true">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($store->auto_grn == "1") ? 'selected' : ''; ?>>Enable</option>
                                    <option value="0" <?php echo ($store->auto_grn == "0") ? 'selected' : ''; ?>>Disable</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">GDN<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="auto_gdn" name="auto_gdn" tabindex="-1" aria-hidden="true">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($store->auto_gdn == "1") ? 'selected' : ''; ?>>Enable</option>
                                    <option value="0" <?php echo ($store->auto_gdn == "0") ? 'selected' : ''; ?>>Disable</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">Status<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($store->status == "1") ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($store->status == "0") ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>





                <!-- <div class="row"> -->

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <?php echo (empty($store->id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($store->id)) { ?>
                            <button type="submit" class="btn btn-success" name="add-another">
                                <?php echo display('save_and_add_another'); ?>
                            </button>
                        <?php } ?>

                    </div>
                </div>


                <!-- </div> -->
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>
<?php
echo "<script>";
echo "var id = " . json_encode($store->id) . ";";
echo "</script>";
?>

<script>
    let code = 0;

    if (id != null) {
        code = document.getElementById("code").value.toString();
    }

    function validateForm(event) {
        // Prevent default form submission
        event.preventDefault();

        // Identify which button was clicked
        const buttonName = event.submitter.name; // Get the name of the button that was clicked

        async function checkStore() {
            try {
                let response = await $.ajax({
                    type: "POST",
                    url: $('#baseUrl2').val() + 'store/store/getStoreById',
                    data: {
                        code: document.getElementById('code').value.toString().padStart(6, '0'),
                    }
                });

                let data = JSON.parse(response);
                if (data === "success") {
                    return true;
                } else {
                    if (code == document.getElementById('code').value.toString().padStart(6, '0')) {
                        return true;
                    } else {
                        alert("Store code already exists");
                        return false;
                    }
                }

            } catch (error) {
                alert("An error occurred: " + error);
                return false;
            }
        }

        checkStore().then((isValid) => {
            if (isValid) {
                if (buttonName === 'save') {
                    document.getElementById('button').value = "save";
                    document.getElementById('insert_store').submit();
                } else if (buttonName === 'add-another') {
                    document.getElementById('button').value = "add-another";
                    document.getElementById('insert_store').submit();
                }
            } else {
                return false;
            }
        });
    }
</script>