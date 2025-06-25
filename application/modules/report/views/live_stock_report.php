<!-- Sales report -->
<style>
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php
                        echo $title;
                        ?></h4>
                </div>
            </div>
            <br />
            <div class="panel-body" style="margin-left: 120px;">


                <div class="form-group">
                    <label for="product"><?php echo display('product') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="product" class="form-control" style="width: 250px;" id="product">
                            <option value=""></option>
                            <?php foreach ($product_list as $productss) { ?>
                                <option value="<?php echo  $productss['id'] ?>"
                                    <?php ?>>
                                    <?php echo  $productss['product_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category"><?php echo display('category') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="category" class="form-control" id="category">
                            <option value="">--select one -- </option>
                            <?php
                            foreach ($category_list as $category) {
                            ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $category_id) {
                                                                                            echo 'selected';
                                                                                        } ?>><?php echo $category['category_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="store"><?php echo display('store') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="store" class="form-control" id="store">
                            <option value="">--select one -- </option>
                           
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="stocktype">Stock Type</label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="stocktype" class="form-control" id="stocktype">
                            <option value="">--select one -- </option>
                            <option value="all">All</option>
                            <option value="actualstock">Actual Stock</option>
                            <option value="physicalstock">Physical Stock</option>

                        </select>
                    </div>
                </div>

                <button type="button" id="btn-filter" class="btn btn-success" onclick="onFilterButtonClick()">
                    Generate Report
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<script src="<?php echo base_url('my-assets/js/admin_js/sales_report.js') ?>" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        getStoreDropdown(0);
    });

    function getStoreDropdown(branchId) {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "store/store/getstorebyuserid",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data3) {
                var stores = JSON.parse(data3);
                var $storeDropdown = $('#store');
                $storeDropdown.empty();
                $storeDropdown.append('<option value="" disabled selected>Select Store</option>'); // Add default option

                $.each(stores, function(index, store) {
                    $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
                    if (store.default != 0) {
                        $storeDropdown.val(store.id)
                    }
                });
            }
        });
    }

    function onFilterButtonClick() {
        let type = "";
        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/livestock_reportdata',
            data: {
                product: $('#product').val(),
                category: $('#category').val(),
                store: $('#store').val(),
                stocktype: $('#stocktype').val()
            },
            success: function(data1) {
                datas = JSON.parse(data1);
                console.log(datas)
                if (datas.length != 0) {
                    window.open(`generate_livestockreport`, '_blank');

                } else {
                    alert("There is no data available for the selected parameters.")
                }

                // updateTable(datas)
                // $("#exampleModal").modal('hide');

            }
        });

    }
</script>