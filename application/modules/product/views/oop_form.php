<div class="row">
     <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
             <div class="panel-heading">
                 <div class="panel-title">
                     <h4><?php echo $title ?> </h4>
                 </div>
             </div>

             <div class="panel-body">
                 <?php echo form_open('oop_form/' . $oop->oop_id, 'class="" id="oop_form"') ?>

                 <input type="hidden" name="oop_id" id="oop_id" value="<?php echo $oop->oop_id ?>">
                 <div class="form-group row">
                     <label for="oop_name" class="col-sm-2 text-right col-form-label">Origin Of Product Name <i class="text-danger"> * </i>:</label>
                     <div class="col-sm-4">


                         <input type="text" name="oop_name" class="form-control" id="oop_name" placeholder="Origin Of Product Name" value="<?php echo $oop->oop_name ?>">
                     </div>
                 </div>
                 <div class="form-group row">
                     <label for="status" class="col-sm-2 text-right col-form-label"><?php echo display('status') ?> <i class="text-danger"> * </i>:</label>
                     <div class="col-sm-4">

                         <select name="status" id="status" class="form-control" required>
                             <?php if (!empty($oop->oop_name)) { ?>
                                 <option value="1" <?php if ($oop->status == 1) {
                                                        echo 'selected';
                                                    } ?>><?php echo display('active') ?></option>
                                 <option value="0" <?php if ($oop->status == 0) {
                                                        echo 'selected';
                                                    } ?>><?php echo display('inactive') ?></option>
                             <?php } else { ?>
                                 <option value="1"><?php echo display('active') ?></option>
                                 <option value="0"><?php echo display('inactive') ?></option>
                             <?php } ?>
                         </select>
                     </div>
                 </div>

                 <div class="form-group row">

                     <div class="col-sm-6 text-right">


                         <button type="submit" class="btn btn-success ">
                             <?php echo (empty($oop->oop_id) ? display('save') : display('update')) ?></button>

                         <?php if (empty($oop->oop_id)) { ?>
                             <button type="submit" class="btn btn-success" name="add-another"><?php echo display('save_and_add_another') ?></button>
                         <?php } ?>

                     </div>
                 </div>


                 <?php echo form_close(); ?>
             </div>

         </div>
     </div>
 </div>