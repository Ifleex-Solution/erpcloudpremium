<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title ?> </h4>
                </div>
            </div>

            <div class="panel-body">

                <?php echo form_open("dashboard/permission/branchcreate") ?>
                <div class="form-group row">
                    <label for="blood" class="col-sm-3 col-form-label">
                        <?php echo display('user') ?> <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control placeholder-single" name="userid" id="userid" onchange="userBranch(this.value)" data-placeholder="<?php echo display('select_one') ?>">
                            <option value=""></option>
                            <?php
                            foreach ($user as $udata) {
                            ?>
                                <option value="<?php echo $udata['user_id'] ?>"><?php echo $udata['first_name'] . ' ' . $udata['last_name'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="blood" class="col-sm-3 col-form-label">
                        Branch Name<span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control" name="branchid" id="branchid">
                            <option value=""><?php echo display('select_one') ?></option>
                            <?php
                            foreach ($branch_list as $data) {
                            ?>
                                <option value="<?php echo $data['id'] ?>"><?php echo $data['name'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>


                <div class="form-group row text-right">
                    <div class="col-md-12">
                        <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo display('reset') ?></button>
                        <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('save') ?></button>
                    </div>
                </div>
                <?php echo form_close() ?>

                <div class="col-md-4">
                    <h3>Exsisting Branch</h3>
                    <div id="existrole">

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

</script>

<script>
    function userBranch(id) {

        $.ajax({
            url: "<?php echo site_url('dashboard/permission/select_to_branch/') ?>" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#existrole').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#existrole').html("<p style='color:red'><?php echo display('no_role_selected'); ?></p>");
            }
        });
    }

    function handleCheckboxClick(id, checkbox) {
        if (confirm("Do you want to make it default")) {

            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'dashboard/permission/update_secbranch',
                data: {
                    id: id,
                    status: checkbox.checked?1:0
                },
                success: function(data1) {
                    userBranch($('#userid').val())
                    alert("updated successfully")
                }
            });
        }else{
            userBranch($('#userid').val())
        }
    }

    function deleteRow(id) {
        if (confirm("Do you want to Delete this record")) {
            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'dashboard/permission/delete_branch',
                data: {
                    id: id

                },
                success: function(data1) {
                    alert("deleted successfully")
                    userBranch($('#userid').val())

                }
            });
        }
    }
</script>