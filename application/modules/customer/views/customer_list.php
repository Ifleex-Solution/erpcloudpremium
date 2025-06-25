 <div class="row">
     <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
             <div class="panel-heading">
                 <div class="panel-title">
                     <h4><?php echo $title ?> </h4>
                 </div>
             </div>

             <div class="panel-body">

                 <div class="table-responsive">
                     <table class="table table-bordered" id="CustomerList2" width="100%">
                         <thead>

                             <tr>
                                 <th><?php echo display('sl') ?></th>
                                 <th><?php echo display('customer_name') ?></th>
                                 <th>Primary Address</th>
                                 <th>Primary Contact No</th>
                                 <th><?php echo display('email'); ?></th>
                                 <th>State/Province</th>
                                 <th><?php echo display('country'); ?></th>
                                 <th>Status</th>

                                 <th width="50px;"><?php echo display('action') ?>
                                 </th>
                             </tr>
                         </thead>
                         <tbody id="customer_tablebody">

                         </tbody>

                     </table>
                 </div>

             </div>


         </div>
     </div>
 </div>
 <input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

 <script>
     $(document).ready(function() {
         // customer list
         var CSRF_TOKEN = $('#CSRF_TOKEN').val();
         var customer_id = $('#customer_id').val();
         var base_url = $('#baseUrl2').val();
         var mydatatable = $('#CustomerList2').DataTable({
             responsive: true,
             "aaSorting": [
                 [1, "asc"]
             ],
             "columnDefs": [{
                 "bSortable": false,
                 "aTargets": [0, 2, 3]
             }],
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
                     columns: [0, 1, 2,3,4,5,6,7]
                 },
                 className: "btn-sm prints"
             }, {
                 extend: "csv",
                 title: "Customer List",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5,6,7]
                 },
                 className: "btn-sm prints"
             }, {
                 extend: "excel",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5,6,7]
                 },
                 title: "Customer List",
                 className: "btn-sm prints"
             }, {
                 extend: "pdf",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5,6,7]
                 },
                 title: "Customer List",
                 className: "btn-sm prints"
             }, {
                 extend: "print",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5,6,7]
                 },
                 title: "<center> Customer List</center>",
                 className: "btn-sm prints"
             }],
             'serverMethod': 'post',
             'ajax': {
                 'url': base_url + 'customer/customer/bdtask_CheckCustomerList',
                 "data": function(data) {
                     data.csrf_test_name = CSRF_TOKEN;
                     data.customer_id = $('#customer_id').val();
                     data.customfiled = $("select[name='customsearch[]']").val();
                 }
             },
             'columns': [{
                     data: 'sl'
                 },
                 {
                     data: 'customer_name'
                 },
                 {
                     data: 'address'
                 },
                 {
                     data: 'mobile'
                 },
                 {
                     data: 'email'
                 },
                 {
                     data: 'state'
                 },
                 {
                     data: 'country'
                 },
                 {
                     data: 'status'
                 },
                 {
                     data: 'button'
                 },
             ]
         });

         $("#customer_id").on('change', function() {
             mydatatable.ajax.reload();
         });

         $("#customsearch").on('change', function() {
             mydatatable.ajax.reload();
         });
     });
 </script>