<link href="<?php echo base_url('assets/css/vat_tax_settings.css') ?>" rel="stylesheet" type="text/css" />
<style>
    /* Style for the switch container */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height:28px;
    }

    /* Hide the default checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Create the slider/track */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 23px;
    }

    /* Create the circle inside the slider */
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 26px;
        border-radius: 50%;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
    }

    /* When the checkbox is checked, change the background color and move the circle */
    input:checked+.slider {
        background-color: #4CAF50;
        /* Green when checked */
    }

    input:checked+.slider:before {
        transform: translateX(26px);
        /* Move the circle to the right */
    }
</style>
<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('vat_tax_setting') ?> </h4>
                </div>
            </div>
            <div class="panel-body container">

                <label class="switch">
                    <br/>
                    <input type="checkbox" id="toggle" name="fixordy" value="0" >
                    <span class="slider"></span>
                </label>
                <div class="form-group row">
                    <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-6">
                        <input id="add-settings" class="btn btn-success" name="add-settings"
                            value="<?php echo display('save') ?>"  onclick="save()" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo "<script>";
echo "let vtinfo=" . json_encode($vtinfo) . ";";


echo "</script>";
?>
<script>
    document.getElementById('toggle').checked=vtinfo.ischecked==1;
    const toggleSwitch = document.getElementById('toggle');
    let ischecked=0;
    toggleSwitch.addEventListener('change', function() {
        ischecked=toggleSwitch.checked?1:0;
    });

    function save(){
         $.ajax({
                url: $('#baseUrl2').val() + 'tax/tax/save_vat',
                type: 'POST',
                data: {
                    ischecked: ischecked
                },
                success: function(response) {
                    alert("Vat Status Updated Successfully")
                    window.location.href = $('#baseUrl2').val() + 'vat_tax_setting';


                },
                error: function(error) {
                    console.log(error)
                }
            });
    }
</script>