<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title ?> </h4>
                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open('subcategory_form/' . $subcategory->subcategory_id, 'class="" id="subcategory_form"') ?>

                <input type="hidden" name="subcategory_id" id="subcategory_id" value="<?php echo $subcategory->subcategory_id ?>">
                <div class="form-group row">
                    <label for="subcategory_name" class="col-sm-2 text-right col-form-label">Subcategory Name <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">


                        <input type="text" name="subcategory_name" class="form-control" id="subcategory_name" placeholder="Subcategory Name" value="<?php echo $subcategory->subcategory_name ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="status" class="col-sm-2 text-right col-form-label">Category <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">

                        <select class="form-control" id="category_id" required name="category_id" tabindex="3">
                            <option value=""></option>
                            <?php if ($category_list) { ?>
                                <?php foreach ($category_list as $categories) { ?>
                                    <option value="<?php echo $categories['category_id'] ?>" <?php if ($subcategory->category_id == $categories['category_id']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>
                                        <?php echo $categories['category_name'] ?></option>

                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 text-right col-form-label"><?php echo display('status') ?> <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">

                        <select name="status" id="status" class="form-control" required>
                            <?php if (!empty($subcategory->subcategory_name)) { ?>
                                <option value="1" <?php if ($subcategory->status == 1) {
                                                        echo 'selected';
                                                    } ?>><?php echo display('active') ?></option>
                                <option value="0" <?php if ($subcategory->status == 0) {
                                                        echo 'selected';
                                                    } ?>><?php echo display('inactive') ?></option>
                            <?php } else { ?>
                                <option value="1"><?php echo display('active') ?></option>
                                <option value="0"><?php echo display('inactive') ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success ">
                            <?php echo (empty($subcategory->subcategory_id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($subcategory->subcategory_id)) { ?>
                            <button type="submit" class="btn btn-success" name="add-another"><?php echo display('save_and_add_another') ?></button>
                        <?php } ?>

                    </div>
                </div>


                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>