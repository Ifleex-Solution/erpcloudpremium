<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                <h4><?php echo $title ?> </h4>
                </div>
            </div>

            <div class="panel-body">
                <table class="table table-bordered datatable">
                    <thead>
                        <tr>
                            <th><?php echo display('sl_no') ?></th>
                            <th ><?php echo display('category_name') ?></th>
                            <th>Status</th>

                            <th class="text-center"><?php echo display('action') ?></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($category_list) {
                            $sl = 1;
                            foreach ($category_list as $categories) {
                        ?>
                                <tr>
                                    <td><?php echo $sl++; ?></td>
                                    <td><?php echo $categories->category_name ?></td>
                                    <td>
                                        <?php if ($categories->status == 1) { ?>
                                            <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                            <span class="label label-danger">Inactive</span>
                                        <?php } ?>

                                    <td class="text-center">
                                        <?php if ($this->permission1->method('manage_category', 'update')->access()) { ?>
                                            <a href="<?php echo base_url() . 'category_form/' . $categories->category_id; ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo display('update') ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <?php } ?>
                                        <?php if ($this->permission1->method('manage_category', 'delete')->access()) { ?>
                                            <a href="<?php echo base_url() . 'product/product/bdtask_deletecategory/' . $categories->category_id; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are You Sure To Want To Delete ?')" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo display('delete') ?> "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        <?php } ?>
                                    </td>

                                </tr>
                        <?php }
                        } ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>