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
                    <table  class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th><?php echo display('unit_name') ?></th>
                                <th ><?php echo display('status') ?></th>
                                <th class="text-center"><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 1;
                            if ($unit_list) {
                                foreach ($unit_list as $categories) {
                            ?>

                                    <tr>
                                        <td><?php echo $sl++; ?></td>
                                        <td ><?php echo $categories->unit_name ?></td>
                                        <td>
                                        <?php if ($categories->status == 1) { ?>
                                            <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                            <span class="label label-danger">Inactive</span>
                                        <?php } ?></td>
                                        <td>
                                            <center>

                                                <?php if ($this->permission1->method('manage_unit', 'update')->access()) { ?>
                                                    <a href="<?php echo base_url() . 'unit_form/' . $categories->unit_id; ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo display('update') ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                <?php } ?>
                                                <?php if ($this->permission1->method('manage_unit', 'delete')->access()) { ?>
                                                    <a href="<?php echo base_url() . 'product/product/bdtask_deleteunit/' . $categories->unit_name . "1234" . $categories->unit_id; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are You Sure To Want To Delete ?')" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo display('delete') ?> "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                <?php } ?>

                                            </center>
                                        </td>
                                    </tr>

                                <?php
                                }
                            } else { ?>

                                <tr>
                                    <td colspan="3" class="text-center"><?php echo display('no_record_found') ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>