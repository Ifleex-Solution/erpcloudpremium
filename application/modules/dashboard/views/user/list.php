<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo (!empty($title)?$title:null) ;?></h4>
                </div>
            </div>
            <div class="panel-body">
 
                <div class="">
                    <table class="datatable table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl_no') ?></th>
                                <th><?php echo display('image') ?></th>
                                <th>User Name</th>
                                <th>User Id</th>
                                <th><?php echo display('status') ?></th>
                                <th><?php echo display('action') ?></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($user)) ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($user as $value) { ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><img src="<?php echo (!empty($value->logo)?base_url().$value->logo:base_url('assets/img/icons/default.jpg')) ; ?>" alt="Image" height="50" ></td>
                                <td><?php echo $value->fullname; ?></td>
                                <td><?php echo $value->email; ?></td>
                                <td><?php echo (($value->status==1)?display('active'):display('inactive')); ?></td>
                                <td>
                                    <?php if ($this->session->userdata('isAdmin') == 1) { ?>
                                    <a href="<?php echo base_url("add_user/$value->user_id") ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <a href="<?php echo base_url("dashboard/user/bdtask_deleteuser/$value->user_id") ?>" onclick="return confirm('<?php echo display('are_you_sure') ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    <?php } 
                                    else if($this->session->userdata('id') == $value->user_id){ ?> 

                                        
                                     <a href="<?php echo base_url("add_user/$value->user_id") ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?> 
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
    </div>
</div>

 