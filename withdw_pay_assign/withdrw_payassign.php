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
    $wdwcqr = mysqli_query($con, "SELECT * FROM fin_payment_request_withdraw where payreq_id = '$pr_id'");
    $fthwdw = mysqli_fetch_object($wdwcqr);
?>
<!-- GST ther Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Withdraw Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="w_organization" id="w_organization" readonly>
          <?php
            if (isset($_GET['py_req_id'])) {
                $pay_rid = $_GET['py_req_id'];
                $sql1 = mysqli_query($con, "SELECT y.id,y.organisation FROM `fin_all_pay_request` x, `prj_organisation` y WHERE x.`pay_request_id`='$pay_rid' AND x.`organisation_id`=y.`id`");
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
          <label for="drwrnm">Name</label>
          <select class="form-control select2" name="drwrnm" id="drwrnm" readonly>
            <?php
              $empqr = mysqli_query($con, "SELECT id,fullname FROM `mstr_emp` WHERE `status`='1' AND id='$fthwdw->drwrnm'");
              while ($fthemp = mysqli_fetch_object($empqr)) {
                echo "<option value='".$fthemp->id."'>".$fthemp->fullname."</option>";
              }
            ?>
          </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="reqamt">Request Amount</label>
            <input type="text" class="form-control" name="reqamt" id="reqamt" value="<?php echo $fthwdw->req_amnt?>" readonly>
        </div>
    </div>
  </div>        
</div>

<!-- End of GST Payment Details Form -->