<?php include("../../../config.php"); ?>

<?php
// Ensure the required variable is set
if (!isset($_GET['request_num'])) {
  echo "<p style='color: red;'>Error: Payment Request number is missing.</p>";
  exit;
}else{
  $request_no = $_GET['request_num'];
  // echo $request_no;
}

?>
<script type="text/javascript">
  function calc() {
    var expns_req_amt = parseFloat($("#expns_req_amt").val()) || 0.0;
    var other_amt = parseFloat($("#other_amt").val()) || 0.0;

    var all_total = parseFloat(expns_req_amt) + parseFloat(other_amt);
    $("#all_total").val(all_total.toFixed(2));
  }
</script>
<!-- End of Scripts -->

<!-- Salary Processing Form -->
<?php
  $sql = mysqli_query($con,"SELECT * FROM exp_payment_request WHERE req_no='$request_no'");
  $fthex = mysqli_fetch_object($sql);
?>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Expense Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Expense For</label>
        <select class="form-control" name="expns_for" id="expns_for" readonly>
          <?php
            $exq = "SELECT id,fullname FROM `mstr_emp` WHERE id='$fthex->req_for'";
            $sql1=mysqli_query($con,$exq);
            while ($exf = mysqli_fetch_object($sql1))
            {
              echo '<option value="'. $exf->id . '">' . $exf->fullname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="e_organization_name" id="e_organization" readonly>
          <?php
                $sql1 = mysqli_query($con, "SELECT id,organisation FROM prj_organisation WHERE id='$fthex->prj_org_id'");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Employee Code</label>
        <input type="text" class="form-control" name="exp_for_empcode" value="<?php echo $fthex->req_for_emp_code;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="prjct">Project</label>
        <select class="form-control" name="prjct" id="prjct" readonly>
         <?php
            $sql2 = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE id='$fthex->project_id'");
            while ($expr = mysqli_fetch_object($sql2))
            {
              echo '<option value="'. $expr->id . '">' . $expr->pname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="sub_prjct">Sub-Project</label>
        <select class="form-control" name="sub_prjct" id="sub_prjct" readonly>
         <?php
            $sql3 = mysqli_query($con, "SELECT id,spname FROM `prj_subproject` WHERE `id`= '$fthex->sub_project_id'");
            while ($exspr = mysqli_fetch_object($sql3))
            {
              echo '<option value="'. $exspr->id . '">' . $exspr->spname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="bmsnm">Billing Milestone</label>
        <select class="form-control" name="bmsnm" id="bmsnm" readonly>
         <?php
            $sql4 = mysqli_query($con, "SELECT id,sub_bms FROM `prj_sub_bms` WHERE `id`= '$fthex->bill_milestone'");
            while ($exbms = mysqli_fetch_object($sql4))
            {
              echo '<option value="'. $exbms->id . '">' . $exbms->sub_bms .'</option>';
            }
          ?>
        </select>
      </div>
    </div> 
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Request Amount</label>
        <input type="text" class="form-control" name="expns_req_amt" id="expns_req_amt" value="<?php echo $fthex->total_amount;?>" readonly>
        <input type="hidden" name="exp_payreq_id" value="<?php echo $fthex->id;?>">
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Other Charges</label>
        <textarea class="form-control" name="exp_other_charges"></textarea>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Other Amount</label>
        <input type="number" class="form-control" name="exp_other_amt" id="other_amt" onkeyup="calc()">
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Total Amount</label>
        <input type="text" class="form-control" id="all_total" name="expns_total_amt" value="<?php echo $fthex->total_amount;?>" readonly>
      </div>
    </div>
  </div>        
</div>

<!-- End of Salary Form -->