<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="your-generated-integrity-hash" crossorigin="anonymous">


<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <input type="hidden" name="baseUrl" id="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
            <button type="button" id="btn-create" class="btn btn-success" style="margin-left: 30px;margin-top:20px;"><i class="fas fa-plus"></i>Add Bank</button>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="bankList">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bank</th>
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


<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add New Bank</h4>
            </div>
            <div class="modal-body">

                <div class="form-group row">
                    <label for="Bank_name" class="col-sm-4 col-form-label">Bank Name
                        <i class="text-danger">*</i>
                    </label>
                    <div class="col-sm-8">
                        <input type="text" tabindex="3" class="form-control" name="Bank_name[]" placeholder="Bank Name" id="bankname" required />
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

    // Add click event listener
    btnCreate.addEventListener('click', function() {
        // Replace with your desired functionality
        $("#exampleModal2").modal('show');
    });


    const btnSave = document.getElementById('btn-save');

    // Add click event listener
    btnSave.addEventListener('click', function() {
        var base_url = $("#baseUrl").val();
        if ($('#bankname').val() === '') {
            alert("Please enter the bank name")
        } else {
            $.ajax({
                type: "post",
                url: base_url + 'bank/bank/savebank',
                data: {
                    bank: $('#bankname').val()
                },
                success: function(data1) {
                    alert("Bank Details Created Successfully")
                    location.reload();
                }
            });

        }

        //console.log($('#chequeno').val())
    });




    $(document).ready(function() {
        "use strict";
        // var csrf_test_name = $('#CSRF_TOKEN').val();
        // var total_purchase_no = $("#total_purchase_no").val();
        var base_url = $("#baseUrl").val();
        var csrf_test_name = '';
        var is_credit_edit = '';


        $.ajax({
            type: "post",
            url: base_url + 'bank/bank/getAllBanks',
            data: {
                csrf_test_name: csrf_test_name,
                is_credit_edit: is_credit_edit
            },
            success: function(data1) {

                var parsedData1 = JSON.parse(data1);
                console.log(parsedData1)
                $('#bankList').DataTable({
                    "bDestroy": true,
                    "data": parsedData1,
                    responsive: true,
                    'processing': true,
                    dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
                    buttons: [{
                        extend: "copy",
                        exportOptions: {
                            columns: [0, 1] //Your Colume value those you want
                        },
                        className: "btn-sm prints"
                    }, {
                        extend: "csv",
                        title: "Bank List",
                        exportOptions: {
                            columns: [0, 1] //Your Colume value those you want print
                        },
                        className: "btn-sm prints"
                    }, {
                        extend: "excel",
                        exportOptions: {
                            columns: [0, 1] //Your Colume value those you want print
                        },
                        title: "Bank List",
                        className: "btn-sm prints"
                    }, {
                        extend: "pdf",
                        exportOptions: {
                            columns: [0, 1] //Your Colume value those you want print
                        },
                        title: "Bank List",
                        className: "btn-sm prints"
                    }, {
                        extend: "print",
                        exportOptions: {
                            columns: [0, 1] //Your Colume value those you want print
                        },
                        title: "<center>Bank List</center>",
                        className: "btn-sm prints"
                    }],
                    "columns": [{
                            data: 'id'
                        },
                        {
                            data: 'bankname'
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
                var banks = JSON.parse(data1);
                var $banksDropdown = $('#banks');
                $banksDropdown.empty(); // Clear existing options
                $banksDropdown.append('<option value="" disabled selected>Select Bank</option>'); // Add default option
                $.each(banks, function(index, bank) {
                    $banksDropdown.append('<option value="' + bank.id + '">' + bank.bankname + '</option>');
                });
            }
        });

 


        // $('#btn-filter').click(function() {
        //     purchasedatatable.ajax.reload();
        // });

    });
</script>