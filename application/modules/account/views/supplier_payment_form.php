<script src="<?php echo base_url() ?>my-assets/js/admin_js/account.js" type="text/javascript"></script>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>
                        <?php echo display('supplier_payment') ?>
                    </h4>
                </div>
            </div>
            <div class="panel-body">

                <?php echo  form_open_multipart('account/accounts/create_supplier_payment', 'id="supplier_paymentform"') ?>

                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label"><?php echo display('date') ?><i class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="<?php
                                                                                                                date_default_timezone_set('Asia/Colombo');

                                                                                                                echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="txtRemarks" class="col-sm-2 col-form-label"><?php echo display('remark') ?></label>
                    <div class="col-sm-4">
                        <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="debtAccVoucher">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo display('supplier_name') ?><i class="text-danger">*</i></th>
                                <th class="text-center"><?php echo display('voucher_no') ?></th>
                                <th class="text-center"><?php echo display('due_amount') ?></th>
                                <th class="text-center"><?php echo display('amount') ?><i class="text-danger">*</i></th>

                            </tr>
                        </thead>
                        <tbody id="debitvoucher">

                            <tr>
                                <td class="" width="300">
                                    <select name="supplier_id" id="supplier_id_1" class="form-control" onchange="load_supplier_code(this.value,1)" required>
                                        <<option value="">Select Supplier</option>}
                                            option
                                            <?php foreach ($supplier_list as $suplier) { ?>
                                                <option value="<?php echo html_escape($suplier->supplier_id); ?>">
                                                    <?php echo html_escape($suplier->supplier_name); ?></option>
                                            <?php } ?>
                                    </select>

                                </td>
                                <td><input type="hidden" name="txtCode" value="" class="form-control " id="txtCode_1">

                                    <?php echo  form_dropdown('voucher_no', null, null, 'class="form-control select2" required id="voucher_no_1" onchange="voucher_due(this.value)"') ?>
                                </td>
                                <td><input type="text" name="dueAmount" value="" class="form-control  text-right" id="due_1" readonly="">
                                </td>
                                <td><input type="number" step="0.01" name="txtAmount" value="" class="form-control total_price text-right" id="txtAmount_1" onkeyup="supplierRcvcalculation(1)" required>
                                </td>

                            </tr>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td>

                                </td>
                                <td colspan="2" class="text-right"><label for="reason" class="  col-form-label"><?php echo display('total') ?></label>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="grandTotal" class="form-control text-right " name="grand_total" value="" readonly="readonly" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <p hidden id="old-amount"><?php echo 0; ?></p>
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <div class="col-sm-6 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row no-gutters">
                                <div class="form-group col-md-5">
                                    <label for="payments" class="col-form-label pb-2"><?php echo display('payment_type'); ?></label>

                                    <?php
                                    $default_option = array('' => 'Select an option'); // Default option
                                    $all_pmethod = $default_option + $all_pmethod;
                                    echo form_dropdown('multipaytype[]', $all_pmethod, (!empty($card_type) ? $card_type : null), 'id="card_type_1" onchange = "check_creditsale(1)" class="card_typesl postform resizeselect form-control "') ?>

                                </div>
                                <div class="form-group col-md-5">
                                    <label for="4digit" class="col-form-label pb-2"><?php echo display('paid_amount'); ?></label>

                                    <input type="text" id="pamount_by_method_1" class="form-control number pay text-right valid_number" name="pamount_by_method[]" value="" onkeyup="changedueamount()" placeholder="0.00" required />

                                </div>
                                <div class="form-group col-md-2">
                                    <label for="payments" class="col-form-label pb-2 text-white"><?php echo display('payment_type'); ?></label>
                                    <!-- <button class="btn btn-danger" onclick="removeMethod(this,1)"><i class="fa fa-trash"></i></button> -->
                                </div>
                            </div>
                            <div class="form-group col-md-9">
                                <div class="form-group row">
                                    <div id="che_1" style="display:none;"> <input type="checkbox" id="checkbox_1" onclick="showHideDiv('1')"> Cheque Transaction</div>
                                </div>
                                <div id="myDiv_1" style="display:none;">
                                    <div style="margin-top: 20px;">
                                        <div class="form-group row">
                                            <label for="cheque_no" class="col-sm-4 col-form-label">Cheque No
                                                <i class="text-danger">*</i>
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" tabindex="3" class="form-control" name="cheque_no[]" placeholder="Cheque No" id="cheque_no_1"  />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="date" class="col-sm-4 col-form-label">Draft Date

                                            </label>
                                            <div class="col-sm-8">
                                                <?php
                                                date_default_timezone_set('Asia/Colombo');

                                                $date = date('Y-m-d'); ?>
                                                <input type="date" tabindex="2" class="form-control" name="draft_date[]" value="" id="draft_date1" />
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
                                                <input type="date" required tabindex="2" class="form-control" name="effective_date[]" value="<?php echo $date; ?>" id="effective_date1" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="description" class="col-sm-4 col-form-label">Description

                                            </label>
                                            <div class="col-sm-8">
                                                <textarea tabindex="3" class="form-control" name="description[]" placeholder="Description" id="description1"></textarea>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="" id="add_new_payment">



                            </div>
                            <div class="form-group text-right">
                                <div class="col-sm-12 pr-0">

                                    <button type="button" id="add_new_payment_type" class="btn btn-success w-md m-b-5"><?php echo display('new_p_method'); ?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-12 text-right">
                        <input type="hidden" name="finyear" id="finyear" value="<?php echo financial_year(); ?>">
                        <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="<?php echo display('save') ?>" tabindex="9" />

                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Cheques</h4>
            </div>
            <div class="modal-body">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Cheque No</th>
                            <th>Customer Name</th>
                            <th>Effective Date</th>
                            <th>Amount</th>
                            <th>Status</th>
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
<script src="<?php echo base_url('my-assets/js/admin_js/supplier_payment_form.js') ?>" type="text/javascript"></script>

<script>
    var i = 0;
    $(document).on('click', '#add_new_payment_type', function() {
        var base_url = $('#base_url').val();
        var csrf_test_name = $('[name="csrf_test_name"]').val();
        var gtotal = $("#grandTotal").val();

        var total = 0;
        $(".pay").each(function() {
            total += parseFloat($(this).val()) || 0;
        });


        if (total >= gtotal) {
            toastr.error("Paid amount is exceed to Total amount.");

            return false;
        }

        var url = base_url + "account/accounts/bdtask_showpaymentmodal";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                csrf_test_name: csrf_test_name
            },
            success: function(data) {
                console.log($("#pay-amount").text())
               // console.log(data)
                $($('#add_new_payment').append(data)).show("slow", function() {});
                var length = $(".number").length;
                $(".number:eq(" + (length - 1) + ")").val(parseFloat($("#pay-amount").text()));
                var total2 = 0;
                $(".number").each(function() {
                    total2 += parseFloat($(this).val()) || 0;
                });




            }
        });
    });
    // $(document).on('click', '#add_new_payment_type', function() {


    //     var base_url = $('#base_url').val();
    //     var csrf_test_name = $('[name="csrf_test_name"]').val();
    //     var gtotal = $("#grandTotal").val();

    //     var total = 0;
    //     $(".pay").each(function() {
    //         total += parseFloat($(this).val()) || 0;
    //     });
    //     var is_credit_edit = $('#is_credit_edit').val();
    //     if (total >= gtotal) {
    //         alert("Paid amount is exceed to Total amount.");

    //         return false;
    //     }
    //     i++;

    //     var url = base_url + "purchase/purchase/bdtask_showpaymentmodal1/" + i;
    //     $.ajax({
    //         type: "post",
    //         url: url,
    //         data: {
    //             is_credit_edit: is_credit_edit,
    //             csrf_test_name: csrf_test_name
    //         },
    //         success: function(data) {

    //             $($('#add_new_payment').append(data));
    //             var length = $(".number").length;
    //             var total3 = 0;
    //             $(".pay").each(function() {
    //                 total3 += parseFloat($(this).val()) || 0;
    //             });

    //             var nextamnt = gtotal - total3;


    //             $(".number:eq(" + (length - 1) + ")").val(nextamnt.toFixed(2, 2));
    //             var total2 = 0;
    //             $(".number").each(function() {
    //                 total2 += parseFloat($(this).val()) || 0;
    //             });
    //             var dueamnt = parseFloat(gtotal) - total2

    //         }
    //     });
    // });
    function changedueamount() {
        var inputval = parseFloat(0);
        console.log(inputval)
        var maintotalamount = $('#grandTotal').val();

        $(".number").each(function() {
            var inputdata = parseFloat($(this).val());
            inputval = inputval + inputdata;

            if (parseFloat(maintotalamount) < parseFloat(inputval)) {
                toastr["error"]('You Can not Pay More than Total Amount');
                $(this).val(0)
                return false;
            }

        });



        var restamount = (parseFloat(maintotalamount)) - (parseFloat(inputval));
        var changes = restamount.toFixed(3);
        if (changes <= 0) {
            $("#pay-amount").text(0);
        } else {
            $("#pay-amount").text(changes);
        }

    }

    function showHideDiv(id) {
        var divId = "myDiv_" + id;
        if ($('#checkbox_' + id).prop('checked')) {
            $('#' + divId).show();
        } else {
            $('#' + divId).hide();
            $('#cheque_no_' + id).val("");
            $('#description' + id).val("");


        }
    }

    function validateForm() {

        var elements = document.getElementsByName("cheque_no[]");
        const array = [];
        var csrf_test_name = $('[name="csrf_test_name"]').val();
        var is_credit_edit = $('#is_credit_edit').val();



        for (var i = 0; i < elements.length; i++) {
            console.log(elements[i].value)
            if (elements[i].value != "") {
                if (array.some(item => item === elements[i].value)) {
                    alert("Can't use 1 perticular cheque number 2 times ");
                    return false;
                } else {
                    array.push(elements[i].value)
                }

            }
        }


        // var elements2 = document.getElementsByName("multipaytype[]");

        // for (var j = 0; j < elements2.length; j++) {
        //     if(elements2[j].value ==""){
        //         alert("Not valid payment type please check")
        //     }
        //     // (function(j) {
        //     //     var url2 = $('#base_url').val() + "purchase/purchase/bdtask_typeofthepayment/" + elements2[j].value;
        //     //     console.log(elements2[j].value);

        //     //     $.ajax({
        //     //         type: "post",
        //     //         url: url2,
        //     //         data: {
        //     //             is_credit_edit: is_credit_edit,
        //     //             csrf_test_name: csrf_test_name
        //     //         },
        //     //         success: function(data) {
        //     //             var parsedData = JSON.parse(data);
        //     //             if (parsedData[0].HeadName === '3rd party cheque') {
        //     //                 console.log(elements[j].value); 
        //     //             }
        //     //         }
        //     //     });
        //     // })(j);
        // }
        return true;
    }

    function handleTableClick(rowId, customerId, amount, id, chequeno, draftdate, effectivedate, description) {
        // Parse JSON string back to object
        // rowData = JSON.parse(rowData);
        // Handle click here
        $('#pamount_by_method_' + id).val(amount);
        $('#pamount_by_method_' + id).prop('readonly', true);
        $('#' + "che_" + id).hide();
        $('#' + "myDiv_" + id).show();


        $('#cheque_no_' + id).val(chequeno);
        $('#cheque_no_' + id).prop('readonly', true);

        $('#draft_date' + id).val(draftdate);
        $('#draft_date' + id).prop('readonly', true);


        $('#effective_date' + id).val(effectivedate);
        $('#effective_date' + id).prop('readonly', true);


        //    $('#description' + id).val(description);
        // $('#description' + id).prop('readonly', true);



        var inputval = parseFloat(0);
        var maintotalamount = $('#grandTotal').val();

        $(".number").each(function() {
            var inputdata = parseFloat(amount);
            inputval = inputval + inputdata;

            if (parseFloat(maintotalamount) < parseFloat(inputval)) {
                toastr["error"]('You Can not Pay More than Total Amount');
                $(this).val(0)
                return false;
            }

        });



        var restamount = (parseFloat(maintotalamount)) - (parseFloat(inputval));
        var changes = restamount.toFixed(3);
        if (changes <= 0) {
            $("#pay-amount").text(0);
        } else {
            $("#pay-amount").text(changes);
        }


        $("#exampleModal").modal('hide');




        // You can access properties of rowData as needed
    }

    function check_creditsale(id) {
        $('#cheque_no_' + id).prop('readonly', false);
        // $('#description' + id).prop('readonly', false);
        $('#draft_date' + id).prop('readonly', false);
        $('#effective_date' + id).prop('readonly', false);
        $('#pamount_by_method_' + id).prop('readonly', false);
        var elements = document.getElementsByName("multipaytype[]");
        if ($('#multipaytype_'+id).val() != 0) {
            var url = $('#base_url').val() + "purchase/purchase/bdtask_typeofthepayment/" + $('#card_type_'+id).val() ;
            $.ajax({
                type: "post",
                url: url,
                data: {
                    is_credit_edit: is_credit_edit,
                    csrf_test_name: csrf_test_name
                },
                success: function(data) {
                    var parsedData = JSON.parse(data);


                     
                    if (parsedData[0].HeadName === '3rd party cheque') {

                        $('#cheque_no_' + id).val("");
                        $('#description' + id).val("");
                        $("#exampleModal").modal('show');
                        var url1 = $('#base_url').val() + "purchase/purchase/getallcheques";
                        $.ajax({
                            type: "post",
                            url: url1,
                            data: {
                                csrf_test_name: csrf_test_name,
                                is_credit_edit: is_credit_edit
                            },
                            success: function(data1) {

                                
                                var parsedData1 = JSON.parse(data1);
                               
                                $('#example').DataTable({
                                    "bDestroy": true,
                                    "data": parsedData1, // Use parsed data as the DataTable source
                                    "columns": [{
                                            "data": "cheque_no"
                                        },
                                        {
                                            "data": "customer_name"
                                        },
                                        {
                                            "data": "effectivedate"
                                        },
                                        {
                                            "data": "amount"
                                        },
                                        {
                                            "data": "status"
                                        },
                                        {
                                            "data": null,
                                            "render": function(data, type, row) {
                                                // Check if row exists
                                                if (row) {
                                                   
                                                    return '<button class="btn btn-primary" onclick="handleTableClick(' + row.id + ',' + row.customer_id + ',' + row.amount + ',' + id + ',\'' + row.cheque_no + '\',\'' + row.draftdate + '\',\'' + row.effectivedate + '\',\'' + row.description + '\')">Call</button>';
                                                } else {
                                                    return ''; // Return empty string if row is null or undefined
                                                }
                                            }
                                        }

                                    ]
                                });
                            }
                        });


                    } else if (parsedData[0].PHeadName === 'Cash at Bank') {
                        $('#' + "che_" + id).show();
                        $('#' + "myDiv_" + id).hide();
                        $('#cheque_no_' + id).val("");
                        $('#description' + id).val("");
                        $('#draft_date' + id).val("");
                        $('#effective_date' + id).val(new Date().toISOString().slice(0, 10));




                    } else {
                        $('#' + "che_" + id).hide();
                        $('#' + "myDiv_" + id).hide();
                        $('#cheque_no_' + id).val("");
                        $('#description' + id).val("");
                        $('#draft_date' + id).val("");
                        $('#effective_date' + id).val(new Date().toISOString().slice(0, 10));


                    }


                }
            });
        }





        // $('#add_new_payment').append("<h1>Thayaan</h1><h1>Thayaan</h1><h1>Thayaan</h1><h1>Thayaan</h1>");
        // // Get all elements with the name "multipaytype[]"
        // var elements = document.getElementsByName("multipaytype[]");



        // Loop through the elements (if there are multiple)
        // for (var i = 0; i < elements.length; i++) {
        //     // Do something with each element
        //     console.log(elements[i].value);
        // }

        // Select the appended elements inside the element with ID "add_new_payment"
        // var appendedElements = $('#add_new_payment').children();

        // // Loop through the selected elements
        // appendedElements.each(function(index, element) {
        //     // Retrieve the text of each element
        //     var text = $(element).text();

        //     // Log or do something with the text
        //     console.log("Appended value: " + text);
        // });

        var card_typesl = $('.card_typesl').val();
        if (card_typesl == 0) {
            $("#add_new_payment").empty();
            var gtotal = $(".grandTotalamnt").val();
            $("#pamount_by_method").val(gtotal);
            $("#paidAmount").val(0);
            $("#dueAmmount").val(gtotal);
            $(".number:eq(0)").val(0);
            $("#add_new_payment_type").prop('disabled', true);

        } else {
            $("#add_new_payment_type").prop('disabled', false);
        }
        $("#pay-amount").text('0');

        var purchase_edit_page = $("#purchase_edit_page").val();
        var is_credit_edit = $('#is_credit_edit').val();
        if (purchase_edit_page == 1 && card_typesl == 0) {

            $("#add_new_payment").empty();
            var base_url = $('#base_url').val();
            var csrf_test_name = $('[name="csrf_test_name"]').val();
            var gtotal = $(".grandTotalamnt").val();
            var url = base_url + "purchase/purchase/bdtask_showpaymentmodal";
            $.ajax({
                type: "post",
                url: url,
                data: {
                    csrf_test_name: csrf_test_name,
                    is_credit_edit: is_credit_edit
                },
                success: function(data) {
                    $('#add_new_payment').append(data);
                    $("#pamount_by_method").val(gtotal);
                    $("#add_new_payment_type").prop('disabled', true);
                }
            });

        }
    }
</script>