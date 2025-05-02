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
<!-- Other Payment Details Form -->
<?php
    $pr_id = $_GET['py_req_id'];
    $othqr = mysqli_query($con, "SELECT * FROM fin_payment_request_others where payreq_id = '" . $pr_id . "'");
    $fthoth = mysqli_fetch_object($othqr);
?>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Other Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="ot_organization_name" id="ot_organization" readonly>
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
            <label for="othrhead">Head</label>
            <select class="form-control" name="othrhead" id="othrhead" readonly>
                <?php
                    $gethdqr = mysqli_query($con, "SELECT `id`,`subtypenm` FROM `fin_grouping_subtype` WHERE `id`='$fthoth->othrhead' AND `status`='1' AND `lnkwith`!='Indivisual' AND `lnkwith`!=''");
                    $fchhd = mysqli_fetch_object($gethdqr);
                    echo "<option value='".$fchhd->id."'>".$fchhd->subtypenm."</option>";
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="prjnm">Project Name</label>
            <select class="form-control" name="prjnm" id="prjnm" readonly>
                <?php
                    $gtpnm = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `id`='$fthoth->prjid'");
                    $fthpnm = mysqli_fetch_object($gtpnm);
                    echo "<option value='".$fthpnm->id."'>".$fthpnm->pname."</option>";
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="subprjnm">Sub Project Name</label>
            <select class="form-control" name="subprjnm" id="subprjnm" readonly>
                <?php
                    $spqr = mysqli_query($con, "SELECT id,spname FROM `prj_subproject` WHERE `id`='$fthoth->subprj_id'");
                    $sprjnm = mysqli_fetch_object($spqr);
                    echo "<option value='".$sprjnm->id."'>".$sprjnm->spname."</option>";
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="paytcr">Payment to be Credit to</label>
            <select class="form-control" name="paytcr" id="paytcr" readonly>
                <?php
                    $sql2 = mysqli_query($con, "SELECT `lnkwith`, `col_nm` FROM `fin_grouping_subtype` WHERE `id`='$fthoth->othrhead'");
                    $result1 = mysqli_fetch_object($sql2);
                    $col_nm = $result1->col_nm;
                    $lnkwith = $result1->lnkwith;
                    $gtprtclr = mysqli_query($con, "SELECT `id`, $col_nm FROM $lnkwith WHERE `id`='$fthoth->prtclr'");
                    $fthprtclr = mysqli_fetch_object($gtprtclr);
                    echo "<option value='".$fthprtclr->id."'>".$fthprtclr->$col_nm."</option>";
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="requested_amt">Requested Amount</label>
            <input type="text" class="form-control" name="requested_amt" id="requested_amt" value="<?php echo $fthoth->reqst_amt?>" readonly>
        </div>
    </div>
  </div>        
</div>

<!-- End of Other Payment Details Form -->