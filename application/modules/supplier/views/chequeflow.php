<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<div class="row">
    <div class="col-sm-12">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Cheque Flow Report</h4>
                </div>
            </div>
            <div class="panel-body">

                <div>
                    <?php echo form_open('', array('class' => 'form-inline', 'method' => 'get')) ?>
                    <?php
                    date_default_timezone_set('Asia/Colombo');
                    $today = date('Y-m-d');
                    ?>
                    <div class="form-group mr-2" style="margin-left: 10px;"> <!-- Added mr-2 for margin -->
                        <label class="mr-2" for="from_date">From Date</label>
                        <input type="text" name="from_date" class="form-control datepicker" id="from_date" value="<?php echo date('Y-m-d', strtotime('first day of this month')); ?>" placeholder="<?php echo display('start_date') ?>">
                    </div>

                    <div class="form-group mr-2" style="margin-left: 10px;"> <!-- Added mr-2 for margin -->
                        <label class="mr-2" for="to_date">To Date</label>
                        <input type="text" name="to_date" class="form-control datepicker" id="to_date" placeholder="<?php echo display('end_date') ?>" value="<?php echo date('Y-m-d', strtotime('last day of this month')); ?>">
                    </div>

                    <div class="form-group mr-2" style="margin-left: 10px;"> <!-- Added mr-2 for margin -->
                        <label class="mr-2">Status</label>
                    </div>


                    <div class="form-group mr-2" style="width: 150px;"> <!-- Added mr-2 for margin -->
                        <!-- <label class="mr-2" for="status">Cheque Status</label> -->
                        <select name="status" id="status" class="form-control" style="width: 150px;">
                            <option value="All">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Valid">Valid</option>
                            <option value="Invalid">Invalid</option>
                            <option value="Transferred">Transferred</option>
                            <option value="Deposited">Deposited</option>
                            <option value="Bounced">Bounced</option>
                        </select>
                    </div>
                    <div class="form-group mr-2" style="margin-left: 10px;"> <!-- Added mr-2 for margin -->
                        <label class="mr-2">Type</label>
                    </div>


                    <div class="form-group mr-2" style="width: 150px;"> <!-- Added mr-2 for margin -->
                        <!-- <label class="mr-2" for="type">Cheque Type</label> -->
                        <select name="type" id="type" class="form-control">
                            <option value="All">All</option>
                            <option value="Own">Own</option>
                            <option value="3rd Party">3rd Party</option>
                        </select>
                    </div>

                    <button type="button" id="btn-filter" class="btn btn-success"><?php echo display('find') ?></button>

                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <input type="hidden" name="baseUrl" id="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />

            <div class="panel-body">
                <div class="table-responsive">
                    <div class="body">

                        <table class="table table-striped table-bordered" cellspacing="0" id="chequeList">
                            <thead>
                                <tr>
                                    <th>Cheque No</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Bank Of Cheque</th>
                                    <th>Effective Date</th>
                                    <th>Draft Date</th>
                                    <th>Received From</th>
                                    <th>Sales Invoice No</th>
                                    <th>Sales invoice Date</th>
                                    <th>Cheque Received Date</th>
                                    <th>Transfered To</th>
                                    <th>Purchase Invoice No</th>
                                    <th>Purchase Invoice Date</th>
                                    <th>Cheque Transferred Date</th>
                                    <th>Amount</th>
                                    <th>Cheque Transferred Date</th>
                                    <th>Amount</th>
                                    <th></th>





                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>



<script>
    $('#chequeList').hide();
    document.getElementById('btn-filter').addEventListener('click', function() {
        $('#chequeList').show();

        var base_url = $("#baseUrl").val();
        $.ajax({
            type: "post",
            url: base_url + 'supplier/supplier/chequebydata',
            data: {
                fromdate: $('#from_date').val(),
                todate: $('#to_date').val(),
                type: $('#type').val(),
                status: $('#status').val()
            },
            success: function(data1) {



                var parsedData1 = JSON.parse(data1);
                $('#chequeList').DataTable({
                    "bDestroy": true,
                    "data": parsedData1,
                    scrollY: '1500px', // Example height
                    scrollCollapse: true,
                    paging: true,
                    searching: false,

                    dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
                    "columnDefs": [{
                            "width": "50px",
                            "targets": '_all'
                        } // Set a specific width for all columns
                    ],
                    buttons: [{
                        extend: "copy",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] //Your Colume value those you want
                        },
                        className: "btn-sm prints"
                    }, {
                        extend: "csv",
                        title: "Cheque Flow",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] //Your Colume value those you want print
                        },
                        className: "btn-sm prints"
                    }, {
                        extend: "excel",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] //Your Colume value those you want print
                        },
                        title: "Cheque Flow",
                        className: "btn-sm prints"
                    }, {
                        extend: "pdf",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] //Your Colume value those you want print
                        },
                        title: "Cheque Flow",
                        className: "btn-sm prints"
                    }, {
                        extend: "print",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] //Your Colume value those you want print
                        },
                        title: "<center> Cheque Flow</center>",
                        className: "btn-sm prints"
                    }],
                    "columns": [{
                            data: 'cheque_no',

                        },
                        {
                            data: 'type',

                        },
                        {
                            data: 'chequestatus',

                        },
                        {
                            data: 'HeadName',

                        },
                        {
                            data: 'effectivedate',

                        },
                        {
                            data: 'draftdate',

                        },
                        {
                            data: 'customer_name',

                        },

                        {
                            data: 'invoice',

                        },
                        {
                            data: 'invoice_date',

                        },
                        {
                            data: 'createddate',

                        },
                        {
                            data: 'supplier_name',

                        },
                        {
                            data: 'chalan_no',

                        },
                        {
                            data: 'purchase_date',

                        },
                        {
                            data: 'transfered',

                        },
                        {
                            data: 'depositeddate',

                        },
                        {
                            data: 'bank2',

                        },
                        {
                            data: 'updatedate',

                        }, {
                            data: 'amount',

                        },








                    ],

                });
                $('.dataTables_scrollHead').hide()

                // $('.dataTables_scrollHead').css({
                //     'overflow-x': 'auto', // Enable horizontal scrolling
                //     'max-height': '100px', // Set a maximum height if needed
                //     'position': 'sticky', // Sticky positioning to keep header visible
                //     'top': '0' // Position sticky to top of the container
                // });

                var newHeaderHtml = '<thead>' +
                    '<tr>' +
                    '<th>Cheque No</th>' +
                    '<th>Type</th>' +
                    '<th>Status</th>' +
                    '<th>Bank Of Cheque</th>' +
                    '<th>Effective Date</th>' +
                    '<th>Draft Date</th>' +
                    '<th>Received From</th>' +
                    '<th>Sales Invoice No</th>' +
                    '<th>Sales invoice Date</th>' +
                    '<th>Cheque Received Date</th>' +
                    '<th>Paid To</th>' +
                    '<th>Purchase Invoice No</th>' +
                    '<th>Purchase Invoice Date</th>' +
                    '<th>Cheque Transferred Date</th>' +
                    '<th>Deposited Date </th>' +
                    '<th>Deposited Bank</th>' +
                    '<th>Last Updated Date</th>' +
                    '<th>Amount</th>' +
                    '<th></th>' +

                    '</tr>' +
                    '</thead>';

                $('#chequeList').prepend(newHeaderHtml);


                // $('#chequeList .dataTables_scrollBody').on('scroll', function() {
                //     var scrollLeft = $(this).scrollLeft();
                //     $('.dataTables_scrollHead').scrollLeft(scrollLeft);
                //     $('#headerTable').scrollLeft(scrollLeft);
                // });
                $('#chequeList').on('draw.dt', function() {
                    var newHeaderHtml = '<thead>' +
                        '<tr>' +
                        '<th>Cheque No</th>' +
                        '<th>Type</th>' +
                        '<th>Status</th>' +
                        '<th>Bank Of Cheque</th>' +
                        '<th>Effective Date</th>' +
                        '<th>Draft Date</th>' +
                        '<th>Received From</th>' +
                        '<th>Sales Invoice No</th>' +
                        '<th>Sales invoice Date</th>' +
                        '<th>Cheque Received Date</th>' +
                        '<th>Paid To</th>' +
                        '<th>Purchase Invoice No</th>' +
                        '<th>Purchase Invoice Date</th>' +
                        '<th>Cheque Transferred Date</th>' +
                        '<th>Deposited Date </th>' +
                        '<th>Deposited Bank</th>' +
                        '<th>Last Updated Date</th>' +
                        '<th>Amount</th>' +
                        '<th></th>' +

                        '</tr>' +
                        '</thead>';
                    $('#chequeList thead').replaceWith(newHeaderHtml);
                });

            }
        });




    });
</script>