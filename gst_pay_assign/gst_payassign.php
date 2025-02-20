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
  // echo $request_no;
}
?>
<?php
    $pr_id = $_GET['py_req_id'];
    $gstcqr = mysqli_query($con, "SELECT * FROM fin_payment_request_gst where payreq_id = '$pr_id'");
    $fthgst = mysqli_fetch_object($gstcqr);
    echo "SELECT id,organisation FROM `prj_organisation` WHERE id='$fthgst->orgnstn'";
?>
<!-- GST ther Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">GST Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="gst_organization" id="gst_organization" readonly>
          <?php
            if (isset($_GET['py_req_id'])) {
                $pay_rid = $_GET['py_req_id'];
                $sql1 = mysqli_query($con, "SELECT id,organisation FROM `prj_organisation` WHERE id='$fthgst->orgnstn'");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
            }
          ?>
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="statenm">State</label>
        <select class="form-control select2" name="statenm" id="statenm" readonly>
          <?php
            $stateqr = mysqli_query($con, "SELECT id,sname FROM `prj_state` WHERE `status`='1' AND id='$fthgst->statenm'");
            while ($fthstate = mysqli_fetch_object($stateqr)) {
              echo "<option value='".$fthstate->id."'>".$fthstate->sname."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">GSTIN No.</label>
            <input type="text" class="form-control" name="gstno" id="gstno" value="<?php echo $fthgst->gstin?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="fromdate">From Date</label>
            <input type="text" class="form-control" name="fromdate" id="fromdate" value="<?php echo $fthgst->fromdt?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="todate">To Date</label>
            <input type="text" class="form-control" name="todate" id="todate" value="<?php echo $fthgst->todt?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="reqamt">Amount</label>
            <input type="text" class="form-control" name="reqamt" id="reqamt" value="<?php echo $fthgst->amnt?>" readonly>
        </div>
    </div>
  </div>        
</div>

<!-- End of GST Payment Details Form -->