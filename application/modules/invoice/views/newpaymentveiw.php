<div class="row no-gutters">
  <div class="form-group col-md-6">
    <label for="payments" class="col-form-label pb-2"><?php echo display('payment_type'); ?></label>

    <?php

    if ($is_credit == 1) {
      $card_type = 0;
    } else {

      $card_type = '';
    }
    echo form_dropdown('multipaytype[]', $all_pmethod, $card_type, 'onchange = "check_creditsale(' . $id . ')" class="card_typesl postform resizeselect form-control "') ?>
  </div>
  <div class="form-group col-md-6">
    <label for="4digit" class="col-form-label pb-2"><?php echo display('paid_amount'); ?></label>

    <input type="number" id="pamount_by_method" class="form-control number pay firstpay" name="pamount_by_method[]" value="" onkeyup="changedueamount()" placeholder="0" />

  </div>

</div>
<div class="form-group col-md-9">
  <div class="form-group row">
    <div id="che_<?php echo $id; ?>" style="display:none; "> <input type="checkbox" id="checkbox_<?php echo $id; ?>" onclick="showHideDiv('<?php echo $id; ?>')">Cheque Transaction</div>
  </div>
  <div id="myDiv_<?php echo $id; ?>" style="display:none;">
    <div style="margin-top: 20px;">
      <div class="form-group row">
        <label for="cheque_no" class="col-sm-4 col-form-label">Cheque No
          <i class="text-danger">*</i>
        </label>
        <div class="col-sm-8">
          <input type="text" tabindex="3" class="form-control" name="cheque_no[]" placeholder="Cheque No" id="cheque_no_<?php echo $id; ?>" onkeypress="handleKeyPress(event,'<?php echo $id; ?>')" />
        </div>
      </div>
      <div class="form-group row">
        <label for="date" class="col-sm-4 col-form-label">Draft Date
          <!-- <i class="text-danger">*</i> -->
        </label>
        <div class="col-sm-8">
          <?php
          date_default_timezone_set('Asia/Colombo');

          $date = date('Y-m-d'); ?>
          <input type="date" tabindex="2" class="form-control" name="draft_date[]" value="" id="date" />
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
          <input type="date" required tabindex="2" class="form-control" name="effective_date[]" value="<?php echo $date; ?>" id="date" />
        </div>
      </div>
      <div class="form-group row">
        <label for="description" class="col-sm-4 col-form-label">Description

        </label>
        <div class="col-sm-8">
          <textarea tabindex="3" class="form-control" name="description[]" placeholder="Description" id="description"></textarea>

        </div>
      </div>
      <div class="form-group row">
        <label for="bank_name" class="col-sm-4 col-form-label">Bank Name
          <i class="text-danger">*</i>
        </label>
        <div class="col-sm-8">
          <select tabindex="3" class="form-control" name="banks[]" id="banks_<?php echo $id; ?>" onchange="onChangeBank(<?php echo $id; ?>, this)" > </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="branch_name" class="col-sm-4 col-form-label">Branch Name
          <i class="text-danger">*</i>
        </label>
        <div class="col-sm-8">
          <select tabindex="3" class="form-control" name="branch[]" id="branch_<?php echo $id; ?>" >

          </select>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    "use strict";
    // select 2 dropdown 
    $("select.form-control:not(.dont-select-me)").select2({
      placeholder: "Select option",
      allowClear: true
    });
  });


  function showHideDiv(id) {

    var divId = "myDiv_" + id;
    if ($('#checkbox_' + id).prop('checked')) {
      $('#' + divId).show();
    } else {
      $('#' + divId).hide();
      $('#cheque_no_' + id).val("");
    }
  }

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
      var $banksDropdown = $('#banks_<?php echo json_decode($id); ?>');
      $banksDropdown.empty(); // Clear existing options
      $banksDropdown.append('<option value="" disabled selected>Select Bank</option>'); // Add default option
      $.each(banks, function(index, bank) {
        $banksDropdown.append('<option value="' + bank.id + '">' + bank.bankname + '</option>');
      });


    }
  });


</script>