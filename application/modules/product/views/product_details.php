    



     <div class="row">
         <div class="col-sm-12">
             <div class="panel panel-bd lobidrag">
                 <div class="panel-heading">
                     <div class="panel-title">
                         <h4><?php echo display('product_details') ?> </h4>
                     </div>
                 </div>
                 <div class="panel-body">
                     <div class="col-sm-6">
                         <h2> <span><?php echo display('product_name') ?>: </span><span><?php echo $product_name; ?></span></h2>
                         <h4> <span><?php echo display('price') ?>:</span> <span>
                                 <?php echo (($position == 0) ? "$currency $price" : "$price $currency") ?></span></h4>
                     </div>
                    
                     <table class="table">
                         <tr>
                             <th><?php echo display('total_purchase') ?> = <span class="text-danger"><?php echo $total_purchase; ?></span></th>
                             <th><?php echo display('total_sales') ?> = <span class="text-danger"> <?php echo $total_sales; ?></span></th>
                             <th><?php echo display('stock') ?> = <span class="text-danger"> <?php echo $stock; ?></span></th>
                         </tr>
                     </table>
                 </div>
             </div>
         </div>
     </div>

     <!-- Total Purchase report -->
     <div class="row">
         <div class="col-sm-12">
             <div class="panel panel-bd lobidrag">
                 <div class="panel-heading">
                     <div class="panel-title">
                         <h4><?php echo display('purchase_report') ?> </h4>
                     </div>
                 </div>
                 <div class="panel-body">
                     <div class="table-responsive">
                         <table id="datatable" class="table table-bordered table-striped table-hover">
                             <thead>
                                 <tr>
                                     <th><?php echo display('date') ?></th>
                                     <th><?php echo display('invoice_no') ?></th>
                                     <th><?php echo display('invoice_id') ?></th>
                                     <th><?php echo display('supplier_name') ?></th>
                                     <th class="text-center"><?php echo display('quantity') ?></th>
                                     <th class="text-center"><?php echo display('rate') ?></th>
                                     <th class="text-center"><?php echo display('total_ammount') ?></th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php
                                    if ($purchaseData) {
                                    ?>
                                     <?php foreach ($purchaseData as $purchasedata) { ?>
                                         <tr>
                                             <td><?php echo $purchasedata['final_date'] ?></td>
                                             <td>
                                             <?php echo $purchasedata['chalan_no'] ?>
                                             </td>
                                             <td>
                                             <?php echo $purchasedata['purchase_id'] ?>
                                             </td>
                                             <td>
                                             <?php echo $purchasedata['supplier_name'] ?>
                                                                                         </td>
                                             <td class="text-right"><?php echo $purchasedata['quantity'] ?></td>
                                             <td class="text-right"><?php echo (($position == 0) ? $currency . ' ' . $purchasedata['rate'] : $purchasedata['rate'] . ' ' . $currency) ?></td>
                                             <td class="text-right"> <?php echo (($position == 0) ? $currency . ' ' . $purchasedata['total_amount'] : $purchasedata['total_amount'] . ' ' . $currency) ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php
                                    } else {
                                    ?>
                                     <tr>
                                         <td colspan="7" class="text-center text-danger"><?php echo display('no_record_found') ?></td>
                                     </tr>
                                 <?php } ?>
                             </tbody>
                             <tfoot>
                                 <tr>
                                     <td colspan="4" class="text-right"><b><?php echo display('total') ?></b></td>

                                     <td class="text-right"> <?php echo $total_purchase; ?></td>
                                     <td class="text-right"><b><?php echo display('grand_total') ?></b></td>
                                     <td class="text-right"><b> <?php echo (($position == 0) ? "$currency $purchaseTotalAmount" : "$purchaseTotalAmount $currency") ?></b></td>
                                 </tr>
                             </tfoot>
                         </table>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!--Total sales report -->
     <div class="row">
         <div class="col-sm-12">
             <div class="panel panel-bd lobidrag">
                 <div class="panel-heading">
                     <div class="panel-title">
                         <h4><?php echo display('sales_report') ?> </h4>
                     </div>
                 </div>
                 <div class="panel-body">
                     <div class="table-responsive">
                         <table id="dataTableExample3" class="table table-bordered table-striped table-hover">
                             <thead>
                                 <tr>
                                     <th><?php echo display('date') ?></th>
                                     \ <th><?php echo display('invoice_no') ?></th>
                                     <th><?php echo display('customer_name') ?></th>
                                     <th><?php echo display('quantity') ?></th>
                                     <th><?php echo display('rate') ?></th>
                                     <th class="text-right"><?php echo display('total_ammount') ?></th>
                                     <th class="text-right">Invoice Total</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php
                                    if ($salesData) {
                                    ?>
                                     <?php foreach ($salesData as $saledata) { ?>
                                         <tr>
                                             <td><?php echo $saledata['final_date'] ?></td>
                                             <td>
                                                 <?php echo $saledata['sale_id'] ?>
                                             </td>
                                             <td>
                                                 <?php echo $saledata['customer_name'] ?>
                                             </td>
                                             <td class="text-right"><?php echo $saledata['quantity'] ?></td>
                                             <td class="text-right"> <?php echo (($position == 0) ? $currency . ' ' . $saledata['rate'] : $saledata['rate'] . ' ' . $currency) ?></td>
                                             <td class="text-right"> <?php echo (($position == 0) ? $currency . ' ' . $saledata['total_amount'] : $saledata['total_amount'] . ' ' . $currency) ?></td>
                                             <td class="text-right"> <?php echo (($position == 0) ? $currency . ' ' . $saledata['total_amount'] : $saledata['total_amount'] . ' ' . $currency) ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php
                                    }
                                    ?>
                             </tbody>
                             <tfoot>
                                 <tr>
                                     <td colspan="2" class="text-right;"></td>
                                     <td class="text-right"><b><?php echo display('total') ?></b></td>
                                     <td class="text-right"><?php echo $total_sales; ?></td>
                                     <td colspan="2" class="text-right"><b><?php echo display('grand_total') ?></b></td>
                                     <td class="text-right"><b> <?php echo (($position == 0) ? $currency . ' ' . $salesTotalAmount : $salesTotalAmount . ' ' . $currency) ?></b></td>
                                 </tr>
                             </tfoot>
                         </table>
                     </div>
                 </div>
             </div>
         </div>
     </div>