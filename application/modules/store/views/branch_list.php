<div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <span><?php echo display('branch_list') ?></span>
                           <span class="padding-lefttitle">
                                   
         <?php if($this->permission1->method('add_branch','create')->access()){ ?>
                    <a href="<?php echo base_url('branch_form') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_branch') ?> </a>
                                 <?php }?>
                           </span>

                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="productList"> 
                                <thead>
                                    <tr>
                                        <th><?php echo display('sl') ?></th>
                                        <th>Branch Code</th>
                                        <th>Branch Name</th>
                                        <th>Status</th>
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
                <input type="hidden" id="total_product" value="<?php echo $total_product;?>" name="">
            </div>
        </div>
        <script>
            $(document).ready(function() { 
      "use strict";
   var csrf_test_name = $('#CSRF_TOKEN').val();
   var base_url = $('#base_url').val();
    var total_product = $("#total_product").val();
     $('#productList').DataTable({ 
             responsive: true,

             "aaSorting": [[ 1, "asc" ]],
             "columnDefs": [
                { "bSortable": false, "aTargets": [0,1,,2,3] },

            ],
           'processing': true,
           'serverSide': true,

          
           'lengthMenu':[[10, 25, 50,100,250,500, 1000], [10, 25, 50,100,250,500, 1000]],

             dom:"'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip", buttons:[ {
                extend: "copy",exportOptions: {
                       columns: [ 0, 1, 2, 3] //Your Colume value those you want
                           }, className: "btn-sm prints"
            }
            , {
                extend: "csv", title: "branch List",exportOptions: {
                       columns: [ 0, 1, 2, 3] //Your Colume value those you want print
                           }, className: "btn-sm prints"
            }
            , {
                extend: "excel",exportOptions: {
                       columns: [ 0, 1, 2, 3 ] //Your Colume value those you want print
                           }, title: "branch List", className: "btn-sm prints"
            }
            , {
                extend: "pdf",exportOptions: {
                       columns: [ 0, 1, 2, 3 ] //Your Colume value those you want print
                           }, title: "branch List", className: "btn-sm prints"
            }
            , {
                extend: "print",exportOptions: {
                       columns: [ 0, 1, 2, 3 ] //Your Colume value those you want print
                           },title: "<center>branch List</center>", className: "btn-sm prints"
            }
            ],
            
            'serverMethod': 'post',
            'ajax': {
               'url': base_url + 'store/store/checkbranchList',
               data:{
                csrf_test_name : csrf_test_name,
               }
            },
          'columns': [
             { data: 'sl' },
             { data: 'code' },
             { data: 'name'},
             { data: 'status_label' },
             { data: 'button'},
          ],

    });

});
        </script>