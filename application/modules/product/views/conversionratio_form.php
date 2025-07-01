<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title ?> </h4>
                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open('conversionratio_form/' . $conversionratio->conversionratio_id, 'class="" id="subcategory_form"') ?>

                <input type="hidden" name="conversionratio_id" id="conversionratio_id" value="<?php echo $conversionratio->conversionratio_id ?>">

                <?php if (empty($conversionratio->date)) { ?>
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 text-right col-form-label">Date <i class="text-danger"> * </i>:</label>
                        <div class="col-sm-4">

                            <?php
                            date_default_timezone_set('Asia/Colombo');

                            $date = date('Y-m-d'); ?>
                            <input type="text" required tabindex="2" class="form-control datepicker" name="date" value="<?php echo  $date; ?>" id="date" />
                        </div>
                    </div>
                <?php } else {

                    // foreach ($supplier_pr as $supplier_product) {
                ?>
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 text-right col-form-label">Date <i class="text-danger"> * </i>:</label>
                        <div class="col-sm-4">

                            <?php
                            date_default_timezone_set('Asia/Colombo');

                            $date = date('Y-m-d'); ?>
                            <input type="text" required tabindex="2" class="form-control datepicker" name="date" value="<?php echo   $conversionratio->date; ?>" id="date" />
                        </div>
                    </div>
                <?php
                    // }
                } ?>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 text-right col-form-label">Product <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">

                        <select class="form-control" id="product_id" required name="product_id" tabindex="3" onchange="getproduct()">
                            <option value=""></option>
                            <?php if ($products) {
                            


                            ?>
                                <?php foreach ($products as $categories) { ?>
                                    <option value="<?php echo $categories['id'] ?>" <?php if ($conversionratio->product     == $categories['id']) {
                                                                                        echo 'selected';
                                                                                    } ?>>
                                        <?php echo $categories['product_name'] ?></option>

                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="conversion_ratio" class="col-sm-2 text-right col-form-label">Unit :</label>
                    <div class="col-sm-4">


                        <input type="text" name="unit" class="form-control" id="unit" placeholder="Unit" readonly >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="conversion_ratio" class="col-sm-2 text-right col-form-label">SubUnit :</label>
                    <div class="col-sm-4">


                        <input type="text" name="subunit" class="form-control" id="subunit" placeholder="subunit" readonly >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="conversion_ratio" class="col-sm-2 text-right col-form-label">Conversion Ratio <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">


                        <input type="text" name="conversion_ratio" class="form-control" id="conversion_ratio" placeholder="Conversion Ratio" value="<?php echo $conversionratio->conversion_ratio ?>">
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
<?php
echo "<script> ";
echo "let products=" . json_encode($products) . ";";
echo "</script>";
?>

<script>

function getproduct()
    {

        console.log(products)
        let product = products.find(product => product.id === document.getElementById('product_id').value);

        document.getElementById('unit').value=product.unit
        document.getElementById('subunit').value=product.subunit


        console.log(product)


    }

</script>