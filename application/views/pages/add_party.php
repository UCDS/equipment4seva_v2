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
        text-align:left;
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
<?php 
    $logged_in=$this->session->userdata('logged_in');
?>
<div class="container">
    <div class="card">
       <div class="card-header bg-info text-white">
           <h4> Add Party</h4>
        </div>
        <div class="card-body">
            <form id="add_party" action="<?= base_url('parties/add'); ?>" method="POST">
                <div class="row">
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_name">Party Name<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="party_name" type="text" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_type">Party type</label>
                        <select class="form-control" name="party_type" id="party_type" required>
                            <option value="" selected>Party type</option>
                            <?php
                                foreach($party_types as $r){ ?>
                                <option value="<?php echo $r->party_type_id;?>"    
                                <?php if($this->input->post('party_type') == $r->party_type_id) echo " selected "; ?>
                                ><?php echo $r->party_type;?></option>    
                                <?php }  ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_address">Party address<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="party_address" type="text" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_place">Place<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="party_place" type="text" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="district">District</label>
                        <select class="form-control" name="district" id="district" required>
                            <option value="" selected>District</option>
                            <?php
                                foreach($districts as $r){ ?>
                                <option value="<?php echo $r->district_id;?>"    
                                <?php if($this->input->post('district') == $r->district_id) echo " selected "; ?>
                                ><?php echo $r->district;?></option>    
                                <?php }  ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="bank_account_no">Bank Account No.<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="bank_account_no" type="number" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="bank_name">Bank Name<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="bank_name" type="text" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="bank_branch">Bank Branch<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="bank_branch" type="text" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="bank_branch_ifsc">Branch IFSC Code<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="bank_branch_ifsc" type="text" minlength="11" maxlength="1" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_email">Party email<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="party_email" type="email" required >
                    </div>
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_phone">Party phone number<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="party_phone" type="text" required >
                    </div>
                    <!-- contact person id -->
                    <div class="form-group col-md-4 col-lg-3 col-xs-12">
                        <label for="party_pan">Party Pan number<span class="star" style="color:red"> *</span></label>
                        <input class="form-control" name="party_pan" type="text" minlength="10" maxlength="10" required >
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-xs-12">
                        <button type="submit" class='btn btn-info btn-block'>Submit</button>                        
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
    
    if(status==200){
        swal({
            title: "Success",
            text: msg,
            type: "success",
            timer: 2000
        });
    }
</script>