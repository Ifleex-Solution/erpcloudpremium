<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('add_new_payement_method') ?> </h4>
                </div>
            </div>
            <?php echo form_open_multipart('add_payment_method/' . $pmethod->id, array('class' => 'form-vertical', 'id' => 'validate')) ?>
            <div class="panel-body">
                <div class="form-group row">
                    <label for="payment_method_name" class="col-sm-3 col-form-label"><?php echo display('payment_method_name') ?> <i class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="HeadName" id="HeadName" required="" placeholder="<?php echo display('payment_method_name') ?>" value="<?php echo $pmethod->name ?>" tabindex="1" />
                        <input type="hidden" name="old_name" value="<?php echo $pmethod->name ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="status" class="col-sm-3 col-form-label">Status<i class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true" required>
                            <option value="">Select One</option>
                            <option value="1" <?php echo ($pmethod->status_label == "Active") ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($pmethod->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>



                <div class="form-group row">
                    <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-6">
                        <input type="reset" class="btn btn-danger" value="<?php echo display('reset') ?>" tabindex="5" />
                        <input type="submit" id="add-deposit" class="btn btn-success" name="add-deposit" value="<?php echo display('save') ?>" tabindex="2" />
                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>