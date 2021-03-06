<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
    label{
        font-weight:bold;
    }
    .card{
        margin-top:2rem;
    }
    .card-header{
        text-align:center;
    }
    select{
        cursor: pointer;
    }
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header bg-info text-white">
           <h4> Add Location </h4>
        </div>
        <div class="card-body">
            <form id="add_location" action="<?= base_url('location/add'); ?>" method="POST">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="location">Location<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="location" type="text" required>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="state">State<span class="star" style="color:red"> *</span></label>
                        <select class="form-control" name="state" id="state" onchange="filter_districts('state','district')" required>
                            <option value="0" selected>State</option>
                            <?php
                                foreach($states as $r){ ?>
                                <option value="<?php echo $r->state_id;?>"    
                                <?php if($this->input->post('state') == $r->state_id) echo " selected "; ?>
                                ><?php echo $r->state;?></option>    
                                <?php }  ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="district">District<span class="star" style="color:red"> *</span></label>
                        <select class="form-control" name="district" id="district" required>
                            <option value="0" selected>District</option>
                            <?php
                                foreach($districts as $r){ ?>
                                <option value="<?php echo $r->district_id;?>"    
                                <?php if($this->input->post('district') == $r->district_id) echo " selected "; ?>
                                ><?php echo $r->district;?></option>    
                                <?php }  ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <button type="submit" class='btn btn-info'>Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    <?php if(isset($status)) { ?>
        const status = <?php echo $status; ?>;
        const msg= '<?php echo $msg; ?>';
    <?php } ?>
    
    if(status){
        swal({
            title: status == 200 ? "Success" : "Error",
            text: msg,
            type: status == 200 ? "success" : "error",
            timer: 2000
        });
        // resetting the dropdowns
        $('select').val(0);
    }

    function filter_districts(state, id){
        let districts = <?php echo json_encode($districts); ?>;
        let selected_state = $(`#${state}`).val();
        let filtered_ditricts;
        $(`#${id}`).empty().append(`<option value="0" selected>----------Select----------</option>`);
        filtered_ditricts = $.grep(districts , function(v){
            return v.state_id == selected_state;
        }) ;
        console.log(filtered_ditricts);  
        // iterating the filtered equipment types
        $.each(filtered_ditricts, function (indexInArray, valueOfElement) { 
            const {district_id ,district} = valueOfElement;
            $(`#${id}`).append($('<option></option>').val(district_id).html(district));
        });
    }
</script>