<link href="<?php echo base_url('assets/css/return.css') ?>" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url() ?>my-assets/js/admin_js/return.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>my-assets/js/admin_js/invoice_return.js" type="text/javascript"></script>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('return_invoice') ?></h4>
                        </div>
                    </div>
                    <?php echo  form_open('return/returns/return_invoice', array('class' => 'form-vertical', 'id' => 'invoice_update')) ?>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-6" id="payment_from_1">
                                <div class="form-group row">
                                    <label for="product_name" class="col-sm-4 col-form-label"><?php echo display('customer_name') ?> <i class="text-danger"></i></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="customer_name" value="<?php echo $customer_name?>" class="form-control customerSelection" placeholder='<?php echo display('customer_name') ?>' required id="customer_name" tabindex="1" readonly="">

                                        <input type="hidden" class="customer_hidden_value" name="customer_id" value="<?php echo $customer_id?>" id="SchoolHiddenId"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="product_name" class="col-sm-4 col-form-label"><?php echo display('date') ?> <i class="text-danger"></i></label>
                                    <div class="col-sm-8">
                                        <input type="text" tabindex="2" class="form-control" name="invoice_date" value="<?php echo $date?>"  required readonly="" />
                                    </div>
                                </div>
                            </div>
                        </div>

                       <!-- ret part -->
                       


 <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="normalinvoice">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo display('item_information') ?> <i class="text-danger"></i></th>
                                        <th class="text-center"><?php echo display('sold_qty') ?></th>
                                        <th class="text-center"><?php echo display('batch_no') ?></th>
                                        <th class="text-center"><?php echo display('ret_quantity') ?>  <i class="text-danger">*</i></th>
                                        <th class="text-center"><?php echo display('rate') ?> <i class="text-danger"></i></th>
                                        <th class="text-center"><?php echo display('deduction') ?> %</th>
                                        <th class="text-center"><?php echo display('total') ?></th>
                                        <th class="text-center"><?php echo display('check_return') ?> <i class="text-danger">*</i></th>
                                    </tr>
                                </thead>
                                <tbody id="addinvoiceItemret">
                                    
                                    <?php foreach($invoice_all_data as $details){?>
                                    <tr>
                                        <td class="product_field">
                                            <input type="text" name="product_nameret" onclick="invoice_productList(<?php echo $details['sl']?>);" value="<?php echo $details['product_name']?>-(<?php echo $details['product_model']?>)" class="form-control productSelection" required placeholder='<?php echo display('product_name') ?>' id="product_names" tabindex="3" readonly="">

                                            <input type="hidden" class="product_idret_<?php echo $details['sl']?>  autocomplete_hidden_value"  value="<?php echo $details['product_id']?>" id="product_idret_<?php echo $details['sl']?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="sold_qtyret[]" id="sold_qtyret_<?php echo $details['sl']?>" class="form-control text-right available_quantityret_1" value="<?php echo $details['sum_quantity']?>" readonly="" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-right "
                                                value="<?php echo $details['batch_id']?>" readonly=""
                                                id="batch_no_<?php echo $details['sl']?>" />
                                        </td>
                                        <td>
                                            <input type="text"  onkeyup="quantity_calculate(<?php echo $details['sl']?>);" onchange="quantity_calculate(<?php echo $details['sl']?>);"  class="total_qnttret_<?php echo $details['sl']?> form-control text-right" id="total_qnttret_<?php echo $details['sl']?>" min="0" placeholder="0.00" tabindex="4" />
                                        </td>

                                        <td>
                                            <input type="text" name="product_rateret[]" onkeyup="quantity_calculate(<?php echo $details['sl']?>);" onchange="quantity_calculate(<?php echo $details['sl']?>);" value="<?php echo $details['rate']?>" id="price_itemret_<?php echo $details['sl']?>" class="price_itemret<?php echo $details['sl']?> form-control text-right" min="0" tabindex="5" required="" placeholder="0.00" readonly=""/>
                                        </td>
                                        <!-- Discount -->
                                        <td>
                                            <input type="text"  onkeyup="quantity_calculate(<?php echo $details['sl']?>);"  onchange="quantity_calculate(<?php echo $details['sl']?>);" id="discountret_<?php echo $details['sl']?>" class="form-control text-right" placeholder="0.00" value="" min="0" tabindex="6"/>

                                            <input type="hidden" value="<?php echo $discount_type ?>" name="discount_typeret" id="discount_typeret_<?php echo $details['sl']?>">
                                        </td>

                                        <td>
                                            <input class="total_priceret form-control text-right" type="text"  id="total_priceret_<?php echo $details['sl']?>" value="" readonly="readonly" />

                                            <input type="hidden" name="invoice_details_id[]" id="invoice_details_id" value="{invoice_details_id}"/>
                                        </td>
                                        <td>

                                            <!-- Tax calculate start-->
                                            <input id="total_tax_<?php echo $details['sl']?>" class="total_tax_<?php echo $details['sl']?>" type="hidden" value="{tax}">
                                            <input id="all_tax_<?php echo $details['sl']?>" class="total_tax" type="hidden" value="0" name="tax[]">
                                            <!-- Tax calculate end-->

                                            <!-- Discount calculate start-->
                                            <input type="hidden" id="total_discountret_<?php echo $details['sl']?>" class="" value=""/>

                                            <input type="hidden" id="all_discountret_<?php echo $details['sl']?>" class="total_discountret" value="" />
                                            <!-- Discount calculate end -->

                                            <input type="hidden" id="prod_wise_vat_<?php echo $details['sl']?>" class="prod_wise_vat" value="<?php echo $details['total_vat']?>" />

                                            <input type="hidden" id="return_prod_vat_<?php echo $details['sl']?>" class="return_prod_vat" />


                                            <input type="checkbox" name='rtn[]' onclick="checkboxcheck(<?php echo $details['sl']?>)" id="check_id_<?php echo $details['sl']?>" value="<?php echo $details['sl']?>" class="chk" >


                                        </td>
                                    </tr>
                                    <?php }?>
                                </tbody>

                                <tfoot>

                                    <tr>
                                        <td colspan="5" rowspan="3">
                                <center><label  for="details" class="  col-form-label text-center"><?php echo display('reason') ?></label></center>
                                <textarea class="form-control" name="details" id="details" placeholder="<?php echo display('reason') ?>"></textarea> <br>
                                <span class="usablity"><?php echo display('usablilties') ?> </span><br>
                                
                                <label class="ab"><?php echo display('adjs_with_stck') ?>
                                    <input type="radio" checked="checked" name="radio" value="1">
                                    <span class="checkmark"></span>
                                </label><br>

                               
                                <label class="ab"><?php echo display('wastage') ?>
                                    <input type="radio"  name="radio" value="3">
                                    <span class="checkmark"></span>
                                </label>

                                </td>
                                <td class="text-right" colspan="1"><b><?php echo display('to_deduction') ?>:</b></td>
                                <td class="text-right">
                                    <input type="text" id="total_discountret_ammount" class="form-control text-right" name="total_discountret" value="" readonly="readonly" />
                                </td>
                                </tr>
                                <tr>
                                 <td class="text-right" colspan="1"><b><?php echo display('ttl_val') ?>:</b></td>
                                 <td class="text-right">
                                     <input id="total_vat_ammount" tabindex="-1" class="form-control text-right valid"
                                         name="total_vat" value="" readonly="readonly" aria-invalid="false" type="text">
                                 </td>
                             </tr>
                                <tr>
                                    <td colspan="1"  class="text-right"><b><?php echo display('nt_return') ?>:</b></td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotalret" class="form-control text-right" name="grand_total_priceret" value="" readonly="readonly" />
                                    </td>
                                <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>"/>
                                <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $invoice_id?>"/>
                                <input type="hidden" name="dbinv_id" id="dbinv_id" value="<?php echo $dbinv_id?>"/>
                                </tr>


                                </tfoot>
                            </table>
                        </div>

                       <!-- ret part end -->
                    
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="old-amount"><?php echo 0;?></p>
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <?php if($invoice_all_data[0]['is_credit'] != 1 ){?>
                    <div class="col-sm-6 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row no-gutters">
                                <div class="form-group col-md-6">
                                    <label for="payments"
                                        class="col-form-label pb-2"><?php echo display('payment_type');?></label>

                                    <?php 
                                    $card_type=1020101; 
                                    echo form_dropdown('multipaytype[]',$all_pmethod,(!empty($card_type)?$card_type:null),' onchange = "check_creditsale()" class="card_typesl postform resizeselect form-control "') ?>

                                </div>
                                <div class="form-group col-md-6">
                                    <label for="4digit"
                                        class="col-form-label pb-2"><?php echo display('paid_amount');?></label>

                                    <input type="text" id="pamount_by_method" class="form-control number pay "
                                        name="pamount_by_method[]" value="" onkeyup="changedueamount()"
                                        placeholder="0" />

                                </div>
                            </div>

                            <div class="" id="add_new_payment">



                            </div>
                            <div class="form-group text-right">
                                <div class="col-sm-12 pr-0">

                                    <button type="button" id="add_new_payment_type"
                                        class="btn btn-success w-md m-b-5"><?php echo display('new_p_method');?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php }?>


                </div>
                        <div class="form-group row">
                            <label for="example-text-input" class=" col-form-label"></label>
                            <div class="col-sm-12 text-right" >

                            <input type="submit" id="add_invoice" class="btn btn-success btn-large" onclick="checkreturnamount()" name="add-invoice"  value="<?php echo display('return') ?>" readonly="readonly" tabindex="9"/>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>


        <script>

$(document).on('click','#add_invoice',function(){
    var MyJSStringVar = "<?php Print($invoice_all_data[0]['is_credit']) ?>";
    if(MyJSStringVar==0){
  var total = 0;
    $( ".pay" ).each( function(){
      total += parseFloat( $( this ).val() ) || 0;
    });
    
    var gtotal=$("#grandTotalret").val();
    if (total != gtotal) {
    toastr.error('Paid Amount Should Equal To Payment Amount')

      return false;
    }
    }
  
  });

  function quantity_calculate(item) {
         var a = 0,o = 0,m = 0,r = 0,d = 0,p = 0;
        var sold_qty = $("#sold_qtyret_" + item).val();
        var quantity = $("#total_qnttret_" + item).val();
        var prod_tot_vat = $("#prod_wise_vat_" + item).val();
        var price_item = $("#price_itemret_" + item).val();
        var discount = $("#discountret_" + item).val();
        if(parseInt(sold_qty) < parseInt(quantity)){
            alert("Sold quantity less than quantity!");
            $("#total_qnttret_"+item).val("");
            $("#total_priceret_" + item).val("");
            return false;
        }
        if (parseInt(quantity) > 0) {
            var price = (quantity * price_item);
            var dis = price * (discount / 100);
            $("#all_discountret_" + item).val(dis);
            var ttldis = $("#all_discountret_" + item).val();

            // total vat calculate
            var per_qty_vat = prod_tot_vat / sold_qty;
            var ret_prod_vat = per_qty_vat * quantity;

            $("#return_prod_vat_" + item).val(ret_prod_vat);//
            //Total price calculate per product
            var temp = price - ttldis;
            $("#total_priceret_" + item).val(temp);//

            $(".return_prod_vat").each(function () {
                isNaN(this.value) || m == this.value.length || (r += parseFloat(this.value));
            }),

            $("#total_vat_ammount").val(r.toFixed(2, 2));

            $(".total_priceret").each(function () {
                isNaN(this.value) || o == this.value.length || (a += parseFloat(this.value));
            }),
                    $("#grandTotalret1").val(a+r.toFixed(2, 2));
                    var grand_tot = a+r;
                $("#grandTotalret").val(grand_tot.toFixed(2, 2));
                $("#pamount_by_method").val(grand_tot.toFixed(2, 2));
                

                  $(".total_discountret").each(function () {
                isNaN(this.value) || p == this.value.length || (d += parseFloat(this.value));
            }),
                    $("#total_discountret_ammount").val(d.toFixed(2, 2));
        }

    }

            function checkreturnamount() {
        // var vatamnt = 0;
        // var gt      = $("#grandTotal").val();
        // var gtret   = $("#grandTotalret").val(); 
        // vatamnt     = $("#total_vat_amnt").val();
        
        // var grnt_totals = parseFloat(gt) + parseFloat(vatamnt);
    
        // console.log(gt);
        // console.log(gtret);
        // console.log(vatamnt)
        
        // if (gtret > grnt_totals) {

        //     toastr["error"]('Grand Total Must Greater Then Net Return Amount');
        // }
    }

    $(document).on('click','#add_new_payment_type',function(){
    var base_url = $('#base_url').val();
    var csrf_test_name = $('[name="csrf_test_name"]').val();
    var gtotal=$("#grandTotalret").val();
    
    var total = 0;
    $( ".pay" ).each( function(){
      total += parseFloat( $( this ).val() ) || 0;
    });
    
   
    var is_credit_edit = $('#is_credit_edit').val();
    if(total>=gtotal){
      alert("Paid amount is exceed to Total amount.");
      
      return false;
    }
      
    var url= base_url + "invoice/invoice/bdtask_showpaymentmodal";
    $.ajax({
      type: "post",
      url: url,
      data:{is_credit_edit:is_credit_edit, csrf_test_name:csrf_test_name},
      success: function(data) {
        $($('#add_new_payment').append(data)).show("slow", function(){
          });
        var length = $(".number").length;

        var total3 = 0;
        $( ".pay" ).each( function(){
          total3 += parseFloat( $( this ).val() ) || 0;
        });

        var nextamnt = gtotal -total3;


        $(".number:eq("+(length-1)+")").val(nextamnt.toFixed(2,2));
        var total2 = 0;
        $( ".number" ).each( function(){
          total2 += parseFloat( $( this ).val() ) || 0;
        });
        var dueamnt = parseFloat(gtotal) - total2
        
        
      }
    }); 
  });
        </script>

  

