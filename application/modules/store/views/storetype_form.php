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
            <?php echo form_open_multipart('storetype_form/' . $storetype->id, array('class' => 'form-vertical', 'id' => 'insert_storetype', 'name' => 'insert_storetype', 'onsubmit' => 'return validateForm(event)')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="code" class="col-sm-4 col-form-label">Store Type Code<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php if (!empty($storetype->id)) { ?>
                                    <input class="form-control" name="code" type="text" id="code" placeholder="Store Type Code" tabindex="1" value="<?php echo $storetype->code ?>" readonly>
                                <?php } else { ?>
                                    <input class="form-control" name="code" type="text" id="code" placeholder="Store Type Code" tabindex="1" value="<?php echo $storetypeid ?>">
                                <?php } ?>
                                <input type="hidden" name="button" id="button"   />

                            </div>
                        </div>
                    </div>
                  

                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">Store Type<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" tabindex="" class="form-control" id="name" name="name" placeholder="Store Type" value="<?php echo  $storetype->name ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">status<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ( $storetype->status_label == "Active") ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ( $storetype->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>



                <!-- <div class="row"> -->

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <?php echo (empty($storetype->id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($storetype->id)) { ?>
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
echo "var id = " . json_encode($storetype->id) . ";";
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
    const buttonName = event.submitter.name;  // Get the name of the button that was clicked

    async function checkStoreType() {
        try {
            let response = await $.ajax({
                type: "POST",
                url: $('#baseUrl2').val() + 'store/store/getStoreTypeById',
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
                    alert("Store Type code already exists");
                    return false;
                }
            }

        } catch (error) {
            alert("An error occurred: " + error);
            return false;
        }
    }

    checkStoreType().then((isValid) => {
        if (isValid) {
            if (buttonName === 'save') {
                document.getElementById('button').value="save";
                document.getElementById('insert_storetype').submit();
            } else if (buttonName === 'add-another') {
                document.getElementById('button').value="add-another";
                document.getElementById('insert_storetype').submit();
            }
        } else {
            return false;
        }
    });
}

</script>