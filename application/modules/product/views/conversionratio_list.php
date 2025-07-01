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
                            <th >Date</th>
                            <th >Product Name</th>
                            <th>Conversion Ratio</th>

                            <th class="text-center"><?php echo display('action') ?></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($conversionration_list) {
                            $sl = 1;
                            foreach ($conversionration_list as $categories) {
                        ?>
                                <tr>
                                    <td><?php echo $sl++; ?></td>
                                    <td><?php echo $categories->date ?></td>
                                    <td><?php echo $categories->product_name ?></td>
                                    <td><?php echo $categories->conversion_ratio ?></td>
                                    <td class="text-center">
                                        <?php if ($this->permission1->method('conversionration_list', 'update')->access()) { ?>
                                            <a href="<?php echo base_url() . 'conversionratio_form/' . $categories->conversionratio_id; ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo display('update') ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <?php } ?>
                                        <?php if ($this->permission1->method('conversionration_list', 'delete')->access()) { ?>
                                            <a href="<?php echo base_url() . 'product/product/bdtask_deleteconversionratio/' . $categories->conversionratio_id; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are You Sure To Want To Delete ?')" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo display('delete') ?> "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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