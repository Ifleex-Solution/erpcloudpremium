<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <?php echo form_open_multipart('stockbatch_form/' . $stockbatch->id, array('class' => 'form-vertical', 'id' => 'insert_stockbatch', 'name' => 'insert_stockbatch', 'onsubmit' => 'return validateForm(event)')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="batchid" class="col-sm-4 col-form-label">Batch Id<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php if (!empty($stockbatch->id)) { ?>
                                    <input class="form-control" name="batchid" type="text" id="batchid" placeholder="Batch Id" tabindex="1" value="<?php echo $stockbatch->batchid ?>" readonly>
                                <?php } else { ?>
                                    <input class="form-control" name="batchid" type="text" id="batchid" placeholder="Batch Id" tabindex="1" value="<?php echo $stockbatch->batchid ?>" required>
                                <?php } ?>
                                <input type="hidden" name="button" id="button"   />

                            </div>
                        </div>
                    </div>
                   

                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">Details</label>
                            <div class="col-sm-8">
                                <input type="text" tabindex="" class="form-control" id="details" name="details" placeholder="Details" value="<?php echo $stockbatch->details ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">status<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true" required>
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($stockbatch->status_label == "Active") ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($stockbatch->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>



                <!-- <div class="row"> -->

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <?php echo (empty($stockbatch->id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($stockbatch->id)) { ?>
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
echo "var id = " . json_encode($stockbatch->id) . ";";
echo "</script>";
?>

<script>
    let code = 0;

    if (id != null) {
        code = document.getElementById("batchid").value.toString();
        console.log(code)
    }

    function validateForm(event) {
    // Prevent default form submission
    event.preventDefault();

    // Identify which button was clicked
    const buttonName = event.submitter.name;  // Get the name of the button that was clicked

    async function checkstockbatch() {
        try {
            let response = await $.ajax({
                type: "POST",
                url: $('#baseUrl2').val() + 'stock/stock/getStockBatchById',
                data: {
                    batchid: document.getElementById('batchid').value.toString(),
                }
            });

            let data = JSON.parse(response);
            if (data === "success") {
                return true;
            } else {
                if (code == document.getElementById('batchid').value.toString()) {
                    return true;
                } else {
                    alert("Batch Id already exists");
                    return false;
                }
            }

        } catch (error) {
            alert("An error occurred: " + error);
            return false;
        }
    }

    checkstockbatch().then((isValid) => {
        if (isValid) {
            if (buttonName === 'save') {
                document.getElementById('button').value="save";
                document.getElementById('insert_stockbatch').submit();
            } else if (buttonName === 'add-another') {
                document.getElementById('button').value="add-another";
                document.getElementById('insert_stockbatch').submit();
            }
        } else {
            return false;
        }
    });
}

</script>