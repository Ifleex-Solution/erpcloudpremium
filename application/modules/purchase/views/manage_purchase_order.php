<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('manage_purchase_order') ?></span>
                    <span class="padding-lefttitle">

                        <?php if ($this->permission1->method('new_purchase_order', 'create')->access()) { ?>
                            <a href="<?php echo base_url('new_purchase_order') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('new_purchase_order') ?> </a>
                        <?php } ?>
                    </span>

                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="stockdisposalnote">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Order Number</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th><?php echo display('action') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <input type="hidden" id="total_product" value="<?php echo $total_product; ?>" name="">
    </div>
</div>
<script>
    $(document).ready(function() {
        "use strict";
        var csrf_test_name = $('#CSRF_TOKEN').val();
        var base_url = $('#base_url').val();
        var total_product = $("#total_product").val();
        $('#stockdisposalnote').DataTable({
            responsive: true,

            "aaSorting": [
                [1, "asc"]
            ],
            "columnDefs": [{
                    "bSortable": false,
                    "aTargets": [0, 1, , 2]
                },

            ],
            'processing': true,
            'serverSide': true,


            'lengthMenu': [
                [10, 25, 50, 100, 250, 500, 1000],
                [10, 25, 50, 100, 250, 500, 1000]
            ],

            dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: [{
                extend: "copy",
                exportOptions: {
                    columns: [0, 1, 2] //Your Colume value those you want
                },
                className: "btn-sm prints"
            }, {
                extend: "csv",
                title: "Stock Batch List",
                exportOptions: {
                    columns: [0, 1, 2] //Your Colume value those you want print
                },
                className: "btn-sm prints"
            }, {
                extend: "excel",
                exportOptions: {
                    columns: [0, 1, 2] //Your Colume value those you want print
                },
                title: "Stock Batch List",
                className: "btn-sm prints"
            }, {
                extend: "pdf",
                exportOptions: {
                    columns: [0, 1, 2] //Your Colume value those you want print
                },
                title: "Stock Batch List",
                className: "btn-sm prints"
            }, {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2] //Your Colume value those you want print
                },
                title: "<center>Stock Batch List</center>",
                className: "btn-sm prints"
            }],

            'serverMethod': 'post',
            'ajax': {
                'url': base_url + 'purchase/purchase/checkpurchase',
                data: {
                    csrf_test_name: csrf_test_name,
                }
            },
            'columns': [{
                    data: 'sl'
                },
                {
                    data: 'id'
                },
                {
                    data: 'supplier_name'
                },
                {
                    data: 'date'
                },
                {
                    data: 'grandTotal'
                },
                {
                    data: 'button'
                },
            ],

        });

    });
</script>