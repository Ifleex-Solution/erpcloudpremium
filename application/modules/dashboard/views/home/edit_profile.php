
        <div class="row">
            <div class="col-sm-12 col-md-4">
            </div>
            <div class="col-sm-12 col-md-4">

            <?php echo form_open_multipart('dashboard/home/update_profile', array('id' => 'insert_product'))?>
                <div class="card">
                   
                    <div class="card-content">
                        <div class="card-content-member">
                              <div >
                                <img src="<?php echo base_url((!empty($logo)?$logo:'assets/img/icons/default.jpg')) ?>" width="100px" height="100px" class="img-circle">
                              </div>
                              <br>
                            <h4 class="m-t-0"><?php echo $first_name .' '.$last_name?> </h4>
                        </div>
                        <div class="card-content-languages">
                            <div class="card-content-languages-group">
                                <div>
                                    <label><?php echo display('first_name') ?>:</label>
                                </div>
                                <div>
                                    <ul>
                                        <input type="text" placeholder="<?php echo display('first_name') ?>" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name?>" required readonly />
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content-languages-group">
                                <div>
                                    <label><?php echo display('last_name') ?>:</label>
                                </div>
                                <div>
                                    <ul>
                                        <li><input type="text" placeholder="<?php echo display('last_name') ?>" class="form-control" id="last_name" name="last_name" value="<?php echo $last_name?>" required readonly /></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content-languages-group">
                                <div>
                                    <label>User ID:</label>
                                </div>
                                <div>
                                    <ul>
                                        <li><input type="text" placeholder="<?php echo display('email') ?>" class="form-control" id="user_name" name="user_name" value="<?php echo $user_name?>" required readonly /></li>
                                    </ul>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                  
                </div>
                <?php echo form_close()?>
            </div>
        </div> 
 