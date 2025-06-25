<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="your-generated-integrity-hash" crossorigin="anonymous">

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        margin: 0;
        padding: 0.3em 0.3em;
        /* Adjust padding as necessary */
        border: none;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <input type="hidden" name="baseUrl" id="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
            <button type="button" id="btn-refresh" class="btn btn-success" style="margin-left: 30px;margin-top:20px;"><i class="fas fa-sync-alt"></i> Refresh Cheques</button>
            <button type="button" id="btn-create" class="btn btn-success" style="margin-left: 30px;margin-top:20px;"><i class="fas fa-plus"></i>Add Cheque</button>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="chequeList">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Cheque No</th>
                                <th>Type</th>
                                <th>Effective Date</th>
                                <th>Received From</th>
                                <th>Transfered To</th>
                                <th>Deposited In</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                                <th></th>
                                <th></th>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Cheque Detail</h4>
            </div>
            <div class="modal-body">
                <div id="chequedetail"></div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="title"></h4>
            </div>
            <div class="modal-body">

                <div class="form-group row">
                    <label for="cheque_no" class="col-sm-4 col-form-label">Cheque No
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <input type="text" tabindex="3" class="form-control" name="cheque_no[]" placeholder="Cheque No" id="chequeno" required />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-4 col-form-label">Effective Date
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <?php
                        date_default_timezone_set('Asia/Colombo');

                        $date = date('Y-m-d'); ?>
                        <input type="date" tabindex="2" class="form-control" name="draft_date[]" value="<?php echo $date; ?>" id="effectivedate" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-4 col-form-label">cheque received date
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <?php
                        date_default_timezone_set('Asia/Colombo');

                        $date = date('Y-m-d'); ?>
                        <input type="date" required tabindex="2" class="form-control" name="effective_date[]" value="<?php echo $date; ?>" id="chequereceiveddate" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="bank_name" class="col-sm-4 col-form-label">Bank Name
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <select tabindex="3" class="form-control" name="banks[]" id="banks" required>

                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="branch_name" class="col-sm-4 col-form-label">Branch Name
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <select tabindex="3" class="form-control" name="branch[]" id="branch" required>

                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="customers_name" class="col-sm-4 col-form-label">Received From
                    </label>
                    <div class="col-sm-8">
                        <select tabindex="3" class="form-control" name="customers[]" id="customers" required>

                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="amount" class="col-sm-4 col-form-label">Amount
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <input type="number" tabindex="3" class="form-control" name="amount[]" placeholder="Amount" id="amount" required />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-4 col-form-label">Description
                    </label>
                    <div class="col-sm-8">
                        <textarea tabindex="3" class="form-control" name="description[]" placeholder="Description" id="description"></textarea>
                    </div>
                </div>
                <button type="button" id="btn-save" class="btn btn-success" style="margin-left: 30px; margin-top: 20px;">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const btnCreate = document.getElementById('btn-create');
    let banks = null;
    let id = 0;
    let customers = null;

    function formatDate(date) {
        let d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }


    // Add click event listener
    btnCreate.addEventListener('click', function() {
        $('#title').text("Add Cheque Details")
        id = 0;
        let currentDate = formatDate(new Date());
        $("#exampleModal2").modal('show');
        $('#chequeno').val('');
        $('#amount').val('')
        $('#description').val('')
        $('#effectivedate').val(currentDate)
        $('#chequereceiveddate').val(currentDate)
        var $banksDropdown = $('#banks');
        $banksDropdown.empty();
        $banksDropdown.append('<option value="" disabled selected>Select Bank</option>'); // Add default option
        $.each(banks, function(index, bank) {
            $banksDropdown.append('<option value="' + bank.id + '">' + bank.bankname + '</option>');
        });

        var $branchDropdown = $('#branch');
        $branchDropdown.empty();

        var $customersDropdown = $('#customers');
        $customersDropdown.empty(); // Clear existing options
        $customersDropdown.append('<option value="" disabled selected></option>'); // Add default option
        $.each(customers, function(index, customer) {
            $customersDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
        });
        $customersDropdown.val(null);

    });


    const btnSave = document.getElementById('btn-save');


    $('#banks').on('change', function() {

        var selectedValue = $(this).val();
        onChangeBank(selectedValue, 0);


    });

    function onChangeBank(selectedValue, branchId) {
        var base_url = $("#baseUrl").val();
        $.ajax({
            type: "post",
            url: base_url + 'bank/bank/getBranchesById',
            data: {
                bank: selectedValue,
            },
            success: function(data1) {
                var branches = JSON.parse(data1);
                var $branchDropdown = $('#branch');
                $branchDropdown.empty(); // Clear existing options
                $branchDropdown.append('<option value="" disabled selected>Select Bank</option>'); // Add default option
                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.branchname + '</option>');
                });

                if (branchId > 0) {
                    {
                        $branchDropdown.val(branchId)
                    }
                }
            }
        });
    }

    // Add click event listener
    btnSave.addEventListener('click', function() {
        var base_url = $("#baseUrl").val();

        if ($('#chequeno').val() === '') {
            alert("Please enter the cheque number")
        } else if ($('#effectivedate').val() === '') {
            alert("Please enter the Effective Date")
        } else if ($('#chequereceiveddate').val() === '') {
            alert("Please enter the Cheque Received Date")
        } else if ($('#banks').val() == null) {
            alert("Please select the Bank")
        } else if ($('#branch').val() == null) {
            alert("Please select the Branch")
        } else if ($('#amount').val() === '') {
            alert("Please enter the Amount")
        } else {
            if (id == 0) {
                $.ajax({
                    type: "post",
                    url: base_url + 'supplier/supplier/savecheque',
                    data: {
                        chequeno: $('#chequeno').val(),
                        effectivedate: $('#effectivedate').val(),
                        chequereceiveddate: $('#chequereceiveddate').val(),
                        amount: $('#amount').val(),
                        bank: $('#banks').val(),
                        branch: $('#branch').val(),
                        receivedfrom: $('#customers').val(),
                        description: $('#description').val()
                    },
                    success: function(data1) {
                        alert("Cheque Detail Created Successfully")
                        location.reload();
                    }
                });
            } else {
                $.ajax({
                    type: "post",
                    url: base_url + 'supplier/supplier/updatecheque',
                    data: {
                        id: id,
                        chequeno: $('#chequeno').val(),
                        effectivedate: $('#effectivedate').val(),
                        chequereceiveddate: $('#chequereceiveddate').val(),
                        amount: $('#amount').val(),
                        bank: $('#banks').val(),
                        branch: $('#branch').val(),
                        receivedfrom: $('#customers').val(),
                        description: $('#description').val()
                    },
                    success: function(data1) {
                        alert("Cheque Detail Updated Successfully")
                        location.reload();
                    }
                });

            }


        }



    });

    document.getElementById('btn-refresh').addEventListener('click', function() {

        var base_url = $("#baseUrl").val();
        var csrf_test_name = '';
        var is_credit_edit = '';

        $.ajax({
            type: "post",
            url: base_url + 'supplier/supplier/refreshallcheques',
            data: {
                csrf_test_name: csrf_test_name,
                is_credit_edit: is_credit_edit
            },
            success: function(data1) {
                location.reload();

            }
        });

    });


    $(document).ready(function() {
        "use strict";
        // var csrf_test_name = $('#CSRF_TOKEN').val();
        // var total_purchase_no = $("#total_purchase_no").val();
        var base_url = $("#baseUrl").val();
        var csrf_test_name = '';
        var is_credit_edit = '';
        console.log(base_url)

        $.ajax({
            type: "post",
            url: base_url + 'supplier/supplier/getallcheques',
            data: {
                csrf_test_name: csrf_test_name,
                is_credit_edit: is_credit_edit
            },
            success: function(data1) {
                var parsedData1 = JSON.parse(data1);
                $('#chequeList').DataTable({
                    lengthMenu: [5, 10, 25, 50, 100], // Options for the number of rows per page
                    pageLength: 10,
                    "bDestroy": true,
                    ordering: false,
                    "data": parsedData1,
                    "columns": [{
                            data: 'seq'
                        },
                        {
                            data: 'cheque_no'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: 'effectivedate'
                        },
                        {
                            data: 'customer_name'
                        },
                        {
                            data: 'supplier_name'
                        },
                        {
                            data: 'bank2'
                        },

                        {
                            data: 'amount'
                        },
                        {
                            data: 'chequestatus'
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                // Check if row exists
                                if (row) {
                                    // // Encode row data to prevent errors in JSON stringification
                                    // var draftDateString = row.draftdate.toISOString(); // or use any desired date format
                                    // var effectiveDateString = row.effectivedate.toISOString();

                                    return '<button  class="btn btn-success btn-sm  mr-2" onclick="handleView(' + row.id + ')" data-toggle="tooltip" data-placement="left" title="View"><i class="pe-7s-note2" aria-hidden="true"></i></button>';
                                } else {
                                    return ''; // Return empty string if row is null or undefined
                                }
                            }
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                // Check if row exists
                                if (row.ismanual === "yes") {
                                    // // Encode row data to prevent errors in JSON stringification
                                    // var draftDateString = row.draftdate.toISOString(); // or use any desired date format
                                    // var effectiveDateString = row.effectivedate.toISOString();
                                    return (row.chequestatus !== "Hold") ? '<button  class="btn btn-info btn-sm  mr-2" onclick="handleEdit(' + row.id + ')" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fas fa-edit" aria-hidden="true"></i></button>' : '';
                                } else {
                                    return ''; // Return empty string if row is null or undefined
                                }
                            }
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                // Check if row exists
                                if (row.ismanual === "yes") {
                                    // // Encode row data to prevent errors in JSON stringification
                                    // var draftDateString = row.draftdate.toISOString(); // or use any desired date format
                                    // var effectiveDateString = row.effectivedate.toISOString();
                                    return (row.chequestatus !== "Hold") ? '  <button  class="btn btn-danger btn-sm  mr-2" onclick="handleDelete(' + row.id + ')" data-toggle="tooltip" data-placement="left" title="Delete"><i class="fas fa-trash" aria-hidden="true"></i></button>' : '';
                                } else {
                                    return ''; // Return empty string if row is null or undefined
                                }
                            }
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                // Check if row exists
                                // if (row.ismanual === "yes") {
                                if (row.chequestatus === "Hold") {
                                    return '<button class="btn btn-warning btn-sm mr-2" onclick="handleUnHold(' + row.id + ',\'' + row.effectivedate + '\')" data-toggle="tooltip" data-placement="left" title="Unhold"><i class="fas fa-play" aria-hidden="true"></i></button>';

                                } else {
                                    return '<button class="btn btn-warning btn-sm mr-2" onclick="handleOnHold(' + row.id + ')" data-toggle="tooltip" data-placement="left" title="Hold"><i class="fas fa-pause" aria-hidden="true"></i></button>';

                                }

                            }
                        }

                    ]
                });
            }
        });

        $.ajax({
            type: "post",
            url: base_url + 'bank/bank/getAllBanks',
            data: {
                chequeno: $('#chequeno').val(),
                effectivedate: $('#effectivedate').val(),
                chequereceiveddate: $('#chequereceiveddate').val(),
                amount: $('#amount').val()
            },
            success: function(data1) {
                banks = JSON.parse(data1);
                var $banksDropdown = $('#banks');
                $banksDropdown.empty(); // Clear existing options
                $banksDropdown.append('<option value="" disabled selected>Select Bank</option>'); // Add default option
                $.each(banks, function(index, bank) {
                    $banksDropdown.append('<option value="' + bank.id + '">' + bank.bankname + '</option>');
                });


            }
        });

        $.ajax({
            type: "post",
            url: base_url + 'customer/customer/getAllTheCustomers',
            data: {
                chequeno: $('#chequeno').val(),
                effectivedate: $('#effectivedate').val(),
                chequereceiveddate: $('#chequereceiveddate').val(),
                amount: $('#amount').val()
            },
            success: function(data2) {
                customers = JSON.parse(data2);
                var $customersDropdown = $('#customers');
                $customersDropdown.empty(); // Clear existing options
                $customersDropdown.append('<option value="" disabled selected></option>'); // Add default option
                $.each(customers, function(index, customer) {
                    $customersDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                });
            }
        });

    });


    function handleEdit(rowId) {

        var base_url = $("#baseUrl").val();

        $("#exampleModal2").modal('show');

        $.ajax({
            type: "post",
            url: base_url + 'supplier/supplier/getchequebyid/' + rowId,
            data: {
                fromdate: '',
                todate: ''
            },
            success: function(data) {
                var parsedData = JSON.parse(data);
                var $banksDropdown = $('#banks');
                $banksDropdown.empty();
                $banksDropdown.append('<option value="" disabled selected>Select Bank</option>'); // Add default option
                $.each(banks, function(index, bank) {
                    $banksDropdown.append('<option value="' + bank.id + '">' + bank.bankname + '</option>');
                });
                $banksDropdown.val(parsedData[0].bankId);
                onChangeBank(parsedData[0].bankId, parsedData[0].branchId);
                var $customersDropdown = $('#customers');
                $customersDropdown.empty(); // Clear existing options
                $customersDropdown.append('<option value="" disabled selected></option>'); // Add default option
                $.each(customers, function(index, customer) {
                    $customersDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                });
                $customersDropdown.val(parsedData[0].customer_id);
                id = parsedData[0].id;
                $('#chequeno').val(parsedData[0].cheque_no);
                $('#amount').val(parsedData[0].amount)
                $('#description').val(parsedData[0].description)
                $('#effectivedate').val(parsedData[0].effectivedate)
                $('#chequereceiveddate').val(parsedData[0].createddate)
                $('#title').text("Update Cheque Details");
            }
        });
    }

    function handleDelete(rowId) {
        var confirmation = confirm("Are you sure you want to delete this cheque detail?");
        var base_url = $("#baseUrl").val();
        if (confirmation) {
            $.ajax({
                type: "post",
                url: base_url + 'supplier/supplier/deletecheque',
                data: {
                    id: rowId
                },
                success: function(data1) {
                    alert("Cheque Detail Deleted Successfully");
                    location.reload();
                }
            });
        } else {
            // Action if user cancels the deletion
            alert("Cheque Detail not deleted");
        }
    }

    function handleOnHold(rowId) {
        var confirmation = confirm("Are you sure you want to hold this cheque detail?");
        var base_url = $("#baseUrl").val();
        if (confirmation) {
            $.ajax({
                type: "post",
                url: base_url + 'supplier/supplier/holdcheque',
                data: {
                    id: rowId
                },
                success: function(data1) {
                    alert("Cheque detail put on hold successfully.");
                    location.reload();
                }
            });
        } else {
            // Action if user cancels the deletion
            alert("Cheque detail was not held.");
        }
    }

    function handleUnHold(rowId, effectivedate) {
        var confirmation = confirm("Are you sure you want to Unhold this cheque detail?");
        var base_url = $("#baseUrl").val();
        if (confirmation) {
            $.ajax({
                type: "post",
                url: base_url + 'supplier/supplier/unholdcheque',
                data: {
                    id: rowId,
                    effectivedate: effectivedate
                },
                success: function(data1) {
                    alert("Cheque detail put unhold successfully.");
                    location.reload();
                }
            });
        } else {
            // Action if user cancels the deletion
            alert("Cheque detail was not held.");
        }
    }


    function handleView(rowId) {
        var base_url = $("#baseUrl").val();
        $("#exampleModal").modal('show');
        var csrf_test_name = 'jagan';
        var is_credit_edit = 'magan';
        $.ajax({
            type: "post",
            url: base_url + 'supplier/supplier/getchequebyid/' + rowId,
            data: {
                fromdate: '',
                todate: ''
            },
            success: function(data) {
                var parsedData = JSON.parse(data);


                var chequedetail = document.getElementById("chequedetail");
                chequedetail.innerHTML = "";

                if (parsedData[0].ismanual == "yes") {
                    chequedetail.innerHTML += "<p><b>Cheque No</b> : " + parsedData[0].cheque_no + "</p>";
                    chequedetail.innerHTML += "<p><b>Cheque Type</b> : " + parsedData[0].type + "</p>";
                    chequedetail.innerHTML += "<p><b>Effective Date</b> : " + parsedData[0].effectivedate + "</p>";
                    chequedetail.innerHTML += "<p><b>Bank Name</b> : " + parsedData[0].bankname + "</p>";
                    chequedetail.innerHTML += "<p><b>Branch Name</b> : " + parsedData[0].branchname + "</p>";
                    chequedetail.innerHTML += "<p><b>Received From</b> : " + parsedData[0].customer_name + "</p>";
                    chequedetail.innerHTML += "<p><b>Amount</b> : " + parsedData[0].customer_name + "</p>";
                    chequedetail.innerHTML += "<p><b>Cheque Status</b> : " + parsedData[0].chequestatus + "</p>";
                    chequedetail.innerHTML += "<p><b>Amount</b> : " + parsedData[0].amount + "</p>";
                    chequedetail.innerHTML += "<p><b>Cheque Status</b> : " + parsedData[0].chequestatus + "</p>";
                    if (parsedData[0].chequestatus === 'Bounced') {
                        chequedetail.innerHTML += "<p><b>Bounced  Date</b> : " + parsedData[0].updatedate + "</p>";
                    }
                    chequedetail.innerHTML += "<p><b>Last Updated Date</b> : " + parsedData[0].updatedate + "</p>";
                } else {
                    // Add the HTML string to the content container div
                    chequedetail.innerHTML += "<p><b>Cheque No</b> : " + parsedData[0].cheque_no + "</p>";
                    chequedetail.innerHTML += "<p><b>Cheque Type</b> : " + parsedData[0].type + "</p>";
                    if (parsedData[0].type != '3rd Party')
                        chequedetail.innerHTML += "<p><b>Bank of the Cheque</b> : " + parsedData[0].HeadName + "</p>";
                    chequedetail.innerHTML += "<p><b>Draft Date</b> : " + parsedData[0].draftdate + "</p>";
                    chequedetail.innerHTML += "<p><b>Effective Date</b> : " + parsedData[0].effectivedate + "</p>";

                    if (parsedData[0].customer_name != null) {
                        chequedetail.innerHTML += "<p><b>Received From</b> : " + parsedData[0].customer_name + "</p>";
                    }
                    if (parsedData[0].supplier_name != null) {
                        chequedetail.innerHTML += "<p><b>Transfered To</b> : " + parsedData[0].supplier_name + "</p>";
                    }

                    if (parsedData[0].invoice != null) {
                        chequedetail.innerHTML += "<br/><h4><b>Sales Invoice  Details</h4>"
                        chequedetail.innerHTML += "<p><b>Sales Invoice No</b> : " + parsedData[0].invoice + "</p>";
                        chequedetail.innerHTML += "<p><b>Sales Invoice Date</b> : " + parsedData[0].invoice_date + "</p>";
                        chequedetail.innerHTML += "<p><b>Cheque Received Date</b> : " + parsedData[0].createddate + "</p>";

                    }



                    if (parsedData[0].chalan_no != null) {
                        chequedetail.innerHTML += "<br/><h4><b>Pucharse Invoice  Details</h4>"
                        chequedetail.innerHTML += "<p><b>Purchase Invoice No</b> : " + parsedData[0].chalan_no + "</p>";
                        chequedetail.innerHTML += "<p><b>Purchase Invoice Date</b> : " + parsedData[0].purchase_date + "</p>";
                        chequedetail.innerHTML += "<p><b>Cheque Transferred Date</b> : " + parsedData[0].transfered + "</p>";
                    }



                    if (parsedData[0].chequestatus === 'Deposited') {
                        chequedetail.innerHTML += "<br/><h4><b>Deposited  Details</h4>"
                        chequedetail.innerHTML += "<p><b>Deposited Date</b> : " + parsedData[0].depositeddate + "</p>";
                        chequedetail.innerHTML += "<p><b> Deposited In</b> : " + parsedData[0].bank2 + "</p>";

                    }


                    chequedetail.innerHTML += "<br/><p><b>Amount</b> : " + parsedData[0].amount + "</p>";

                    chequedetail.innerHTML += "<p><b>Cheque Status</b> : " + parsedData[0].chequestatus + "</p>";

                    if (parsedData[0].chequestatus === 'Bounced') {
                        chequedetail.innerHTML += "<p><b>Bounced Date</b> : " + parsedData[0].updatedate + "</p>";
                    }

                    chequedetail.innerHTML += "<p><b>Last Updated Date</b> : " + parsedData[0].updatedate + "</p>";

                }
            }
        });
    }
</script>