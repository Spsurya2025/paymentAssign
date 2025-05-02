<?php include("../../../config.php"); ?>
<?php
// Ensure the required variable is set
if (!isset($_GET['py_req_id']) ) {
    echo "<p style='color: red;'>Error: Payment Request ID is missing.</p>";
    exit;
}
if (!isset($_GET['request_num'])) {
  echo "<p style='color: red;'>Error: Payment Request number is missing.</p>";
  exit;
}else{
  $request_no = $_GET['request_num'];
}
?> 
<!-- End of Scripts -->
<!-- transporter Form -->
<div class="row" style="margin-top: 20px;">
<?php
    $pr_id = $_GET['py_req_id'];
    $tnsprtqr = mysqli_query($con, "SELECT x.*,y.*,z.* FROM fin_payment_request x JOIN fin_all_pay_request y ON x.id = y.pay_request_id JOIN fin_payment_request_transporter z ON x.id = z.payreq_id where x.id = '" . $pr_id . "'");
    $fthtrns = mysqli_fetch_object($tnsprtqr);
    $sql1 = mysqli_query($con,"SELECT id,transport_name from prj_transport where id='$fthtrns->trnsprtnm'");
    $transporter = mysqli_fetch_object($sql1);
    $sql2 = mysqli_query($con,"SELECT id,pname from prj_project where id='$fthtrns->prjid'");
    $project = mysqli_fetch_object($sql2);
    $sql3 = mysqli_query($con,"SELECT id,spname from prj_subproject where id='$fthtrns->subprj_id'");
    $subproject = mysqli_fetch_object($sql3);
    $sql4 = mysqli_query($con,"SELECT id,sub_bms from prj_sub_bms where id='$fthtrns->bms_id'");
    $subbms = mysqli_fetch_object($sql4);
?>
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Transporter Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="trnsprtrnm">Transporter Name</label>
        <select class="form-control" name="trnsprtrnm" id="trnsprtrnm" readonly>
         <option value="<?php echo $transporter->id;?>"><?php echo $transporter->transport_name?></option>
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="t_organization_name" id="t_organization" readonly>
          <?php
            if (isset($_GET['py_req_id'])) {
                $pay_rid = $_GET['py_req_id'];
                $sql1 = mysqli_query($con, "SELECT y.id,y.organisation FROM `fin_all_pay_request` x, `prj_organisation` y WHERE x.`pay_request_id`='$pay_rid' AND x.`organisation_id`=y.`id`");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name</label>
        <select class="form-control" name="prjctnm" id="prjctnm" readonly>
            <option value="<?php echo $project->id;?>"><?php echo $project->pname;?></option>
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">Request No.</label>
        <?php
              if(isset($_GET['py_req_id']))
              {
                $pay_req_id = $_GET['py_req_id'];
                $all_req = mysqli_query($con, "SELECT pr_num FROM `fin_all_pay_request` WHERE pay_request_id='$pay_req_id'");
                $result_all = mysqli_fetch_object($all_req);
              }
        ?>
        <input type="text" name="req_n" id="req_n" value="<?php echo $result_all->pr_num; ?>" class="form-control" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="subprjnm">Sub Project Name</label>
        <select class="form-control" name="subprjnm" id="subprjnm" readonly>
            <option value="<?php echo $subproject->id;?>"><?php echo $subproject->spname?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="bmsnm">Billing Milestone</label>
        <select class="form-control" name="bmsnm" id="bmsnm" readonly>
            <option value="<?php echo $subbms->id;?>"><?php echo $subbms->sub_bms;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">PO Number</label>
        <input type="text" class="form-control" name="ponum" value="<?php echo $fthtrns->po_no;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="place_from">Place From</label>
        <input type="text" class="form-control" name="place_from" value="<?php echo $fthtrns->plc_from;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="place_to">Place To</label>
        <input type="text" class="form-control" name="place_to" value="<?php echo $fthtrns->place_to;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="distance">Distance</label>
        <div class="input-group">
          <input type="text" name="distance" id="distance" class="form-control" placeholder="99.99" value="<?php echo $fthtrns->distance;?>" readonly>
          <span class="input-group-addon" style="font-weight: bold;">kms</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="material_nm">Material Name</label>
        <input type="text" class="form-control" name="material_nm" value="<?php echo $fthtrns->material;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="mtrl_weight">Material Weight</label>
        <div class="input-group">
          <input type="text" name="mtrl_weight" id="mtrl_weight" class="form-control" value="<?php echo $fthtrns->mat_weight;?>" readonly>
          <span class="input-group-addon" style="font-weight: bold;">kgs</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="service_typ">Service Type</label>
        <input type="text" name="service_typ" id="service_typ" class="form-control" value="<?php echo $fthtrns->srvc_typ;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="lry_model">Lorry Model</label>
        <input type="text" class="form-control" name="lry_model" value="<?php echo $fthtrns->lorry_model;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="dala_typ">Dala Type</label>
        <input type="text" class="form-control" name="dala_typ" value="<?php echo $fthtrns->dala_type;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="carrycap">Carrying Capacity</label>
        <div class="input-group">
          <input type="text" name="carrycap" id="carrycap" class="form-control" value="<?php echo $fthtrns->carry_cap;?>" readonly>
          <span class="input-group-addon" style="font-weight: bold;">kgs</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="rateper_km">Rate Per KM</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" name="rateper_km" id="rateper_km" class="form-control" placeholder="Rate/km" value="<?php echo $fthtrns->rate_per_km;?>" readonly>
          <span class="input-group-addon" style="font-weight: bold;">/km</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="rateper_kg">Rate Per KG</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" name="rateper_kg" id="rateper_kg" class="form-control" placeholder="Rate/kg" value="<?php echo $fthtrns->rate_per_kg;?>" readonly>
          <span class="input-group-addon" style="font-weight: bold;">/kg</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="totalamnt">Total Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="totalamnt" id="totalamnt" placeholder="9999.99" value="<?php echo $fthtrns->amount;?>" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
        <div class="form-group">
            <label for="adv_cent">Advance Percentage <span style="color:red">*</span></label>
            <div class="input-group">
              <input type="text" name="adv_prcnt" id="adv_prcnt" class="form-control" value="<?php echo $fthtrns->adv_cent;?>" placeholder="99.99" readonly>
              <span class="input-group-addon"><i class="fa fa-percent"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="adv_amt">Advance Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="adv_amt" id="adv_amt" placeholder="9999.99" value="<?php echo $fthtrns->adv_amnt?>" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="final_amnt">Final Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="final_amnt" id="final_amnt" placeholder="9999.99" value="<?php echo $fthtrns->final_amnt;?>" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="trnsp_req_amt">Requested Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="trnsp_req_amt" id="all_total_oth" placeholder="9999.99" value="<?php echo $fthtrns->rqst_amnt;?>" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="trnsp_req_amt">Other Charges Reason</label>
        <select class="form-control select2" name="other_reason" id="othres">
          <option value="">---Select---</option>
          <?php 
            $queryoth = mysqli_query($con, "SELECT id,subtypenm FROM fin_grouping_subtype WHERE lnkwith LIKE 'Indivisual'");
            while($other = mysqli_fetch_object($queryoth))
            {
              echo "<option value='$other->id'>".$other->subtypenm."</option>"; 
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="trnsp_req_amt">Other Charges Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="other_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*)\\./g, '$1')" id="oth_amount">
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="trnsp_req_amt">Total Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="trnsp_req_amt_oth" id="all_total" placeholder="9999.99" value="<?php echo $fthtrns->rqst_amnt;?>" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3">
      
    </div>
</div>



<!-- End of transporter Form -->
<script>
  $(document).ready(function () {
    $('#othres').select2();
    // Disable the 'Other Amount' field initially
    $('#oth_amount').prop('disabled', true);
    var subtotal = parseFloat($("#all_total").val()) || 0.00;
    $("#all_total").val(subtotal.toFixed(2));
    // Enable 'Other Amount' field when a valid option is selected
    $('#othres').on('change', function () {
        if ($(this).val() === '') {
            $('#oth_amount').prop('disabled', true).val('');
            calc();
        } else {
            $('#oth_amount').prop('disabled', false);
        }
    });

    // Function to calculate total
    function calc() {
        var othAmt = parseFloat($("#oth_amount").val()) || 0.00;
        var grandTotal = subtotal + othAmt;
        $("#all_total").val(grandTotal.toFixed(2));
    }

    // Trigger calc when 'Other Amount' is changed
    $('#oth_amount').on('input', calc);
    
  });
</script>
<?php if(isset($_GET['peid'])) { ?>
  <script>
    $('#othres').prop('disabled', true); 
  </script>
  <?php } ?>