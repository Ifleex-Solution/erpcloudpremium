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
                    <span id="title"><?php echo $title; ?></span>
                    <span class="padding-lefttitle">
                        <button type="button" id="btn-filter" class="btn btn-primary" onclick="syncButtonClick()">
                            <i class="ti-reload"> </i> Sync
                        </button>
                    </span>

                </div>

            </div>
            <br />
            <div class="panel-body" style="margin-left: 120px;">


                <?php
                date_default_timezone_set('Asia/Colombo');

                $today = date('Y-m-d');
                ?>

                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="checkbox" id="single_date_checkbox" name="single_date_checkbox">
                    <label for="single_date_checkbox">Single Date</label>
                </div>
                <div class="form-group" style="display: flex; gap: 20px;">
                    <div>
                        <label for="from_date">From Date: </label>
                        <input type="text" name="from_date" class="form-control datepicker" id="from_date"
                            placeholder="<?php echo display('start_date') ?>" value="<?php echo $today ?>" style="width: 200px;">
                    </div>
                    <div id="to_date_container">
                        <div>
                            <label for="to_date">To Date:</label>
                            <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                                placeholder="<?php echo display('end_date') ?>" value="<?php echo $today ?>" style="width: 200px;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="product"><?php echo display('product') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="product_id" class="form-control" style="width: 250px;" id="productid">
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
                    <label for="scenario">Scenario</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="scenario" class="form-control" style="width: 250px;" id="scenario" onchange="change_type()">
                            <option value=""> </option>
                            <option value="purchaseinvoice">Purchase Invoice</option>
                            <option value="saleinvoice">Sale Invoice</option>
                            <option value="stock">Stock</option>
                            <option value="GRN">GRN</option>
                            <option value="GDN">GDN</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="scenario">Incident</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="incident" class="form-control" style="width: 250px;" id="incident">
                            <option value=""> </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product"><?php echo display('store') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="store_id" class="form-control" style="width: 250px;" id="storeid">
                            <option value=""></option>

                        </select>
                    </div>
                </div>




                <div class="form-group">
                    <?php if ($this->session->userdata('user_level2') == 3) { ?>
                        <label for="empid" class="mr-2 mb-0">Password</label>
                        <input type="password" tabindex="4" class="form-control" name="empid" id="empid" value="" style="width: 200px;">

                    <?php } else { ?>
                        <input type="hidden" tabindex="4" class="form-control" name="empid" id="empid" value="normal">
                    <?php } ?>
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
                var $storeDropdown = $('#storeid');
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
    function change_type() {
        if (document.getElementById('scenario').value === "purchaseinvoice") {
            var $incidentDropdown = $('#incident');
            $incidentDropdown.empty();
            $incidentDropdown.append('<option value=""></option>');
            $incidentDropdown.append('<option value="localpurchase">Local Purchase</option>');
            $incidentDropdown.append('<option value="internationalpurchase">International Purchase</option>');
        }
        if (document.getElementById('scenario').value === "saleinvoice") {
            var $incidentDropdown = $('#incident');
            $incidentDropdown.empty();
            $incidentDropdown.append('<option value=""></option>');
            $incidentDropdown.append('<option value="sale">Sale</option>');
            $incidentDropdown.append('<option value="wholesale">Whole Sale</option>');
        }

        if (document.getElementById('scenario').value === "stock") {
            var $incidentDropdown = $('#incident');
            $incidentDropdown.empty();
            $incidentDropdown.append('<option value=""></option>');
            $incidentDropdown.append('<option value="openingstock">Opening Stock</option>');
            $incidentDropdown.append('<option value="storetransfer">Store Transfer</option>');
            $incidentDropdown.append('<option value="stockdisposal">Stock Disposal</option>');
            $incidentDropdown.append('<option value="stockadjustment">Stock Adjustment</option>');
        }


        if (document.getElementById('scenario').value === "GRN") {
            var $incidentDropdown = $('#incident');
            $incidentDropdown.empty();
            $incidentDropdown.append('<option value=""></option>');
            $incidentDropdown.append('<option value="purchase">Purchase</option>');
            $incidentDropdown.append('<option value="salesreturn">Sales Return</option>');
            $incidentDropdown.append('<option value="storetransfer">Store Transfer</option>');
        }

        if (document.getElementById('scenario').value === "GDN") {
            var $incidentDropdown = $('#incident');
            $incidentDropdown.empty();
            $incidentDropdown.append('<option value=""></option>');
            $incidentDropdown.append('<option value="sale">Sale</option>');
            $incidentDropdown.append('<option value="wholesale">Whole Sale</option>');
            $incidentDropdown.append('<option value="purchasereturn">Purchase Return</option>');
            $incidentDropdown.append('<option value="storetransfer">Store Transfer</option>');
            $incidentDropdown.append('<option value="stockdisposal">Stock Disposal</option>');


        }
    }

    function syncButtonClick() {
        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/audit_stock_report_sync',
            data: {},
            success: function(data1) {
                alert("sucessfully synced")

            }
        });
    }

    function onFilterButtonClick() {
        let type = "";
        if ($("#empid").val() == "normal") {
            type = "A"
        } else if ($("#empid").val() == "god") {
            type = "B"
        } else if ($("#empid").val() == "all") {
            type = "All"
        } else {
            alert("please enter password or not valid password")
            return
        }


        if ($('#productid').val() == '') {
            alert("Product is required")
            return
        }

        if (document.getElementById('single_date_checkbox').checked) {
            if ($('#from_date').val() == '') {
                alert("From Date is required")
                return
            }
        } else {
            if ($('#from_date').val() == '') {
                alert("From Date is required")
                return
            }
            if ($('#to_date').val() == '') {
                alert("To Date is required")
                return
            }
        }


        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/audit_stock_report',
            data: {
                from_date: $('#from_date').val(),
                to_date: document.getElementById('single_date_checkbox').checked ? $('#from_date').val() : $('#to_date').val(),
                empid: type,
                istype: document.getElementById('single_date_checkbox').checked,
                productid: $('#productid').val(),
                storeid: $('#storeid').val(),
                incident: $('#incident').val(),
                scenario: $('#scenario').val(),
            },
            success: function(data1) {
                datas = JSON.parse(data1);
                console.log(datas)

                const formData = new FormData();
                formData.append("aulist", JSON.stringify(datas));
                formData.append("fdate", $('#from_date').val());

                if (document.getElementById('single_date_checkbox').checked) {
                    formData.append("tdate", $('#from_date').val());
                } else {
                    formData.append("tdate", $('#to_date').val());

                }



                fetch($('#base_url').val() + "report/report/generate_auditreport", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Optional: indicates it's an AJAX call
                        },
                        body: formData // Send JSON directly in the body
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Failed to download report");
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = "stock_audit_report(" + $('#from_date').val() + " To " + $('#to_date').val() + ").xlsx";
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error("Download error:", error);
                    });


            }
        });

    }
</script>
<script>
    document.getElementById('single_date_checkbox').addEventListener('change', function() {
        let fromDate = document.getElementById('from_date');
        let toDate = document.getElementById('to_date');
        let toDateContainer = document.getElementById('to_date_container');
        if (this.checked) {
            toDate.value = fromDate.value;
            toDateContainer.style.display = 'none';
        } else {
            toDateContainer.style.display = 'block';
        }
    });
</script>