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
<script>
    $(document).ready(function () {
      $('#sbprjctnm').select2();
      $('#prjctnm').select2();
    });
    $("#prjctnm").change(function(){
      $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
      var prjid = $(this).val();
      if (prjid != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/get_data.php",
          data:{prjid:prjid,type:'prjnm'},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#sbprjctnm").html(rslt);
          }
        });
      }
    });  
</script>
<!-- Collection ther Payment Details Form -->
<?php
    $pr_id = $_GET['py_req_id'];
    $colcqr = mysqli_query($con, "SELECT * FROM fin_payment_request_collection where payreq_id = '" . $pr_id . "'");
    $fthcolc = mysqli_fetch_object($colcqr);
?>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Collection Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="co_organization_name" id="co_organization" readonly>
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
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="dbtr_typ">Debtor Type</label>
          <select class="form-control select2" name="debtor_typ" id="dbtr_typ" readonly>
            <?php
                $dtrid = $clctnrow->dbtr_typ;
                $gtdtrnm = mysqli_query($con, "SELECT id,subtypenm FROM `fin_grouping_subtype` WHERE `id`='$fthcolc->dbtr_typ'");
                $fthdtr = mysqli_fetch_object($gtdtrnm);
                echo "<option value='".$fthdtr->id."'>".$fthdtr->subtypenm."</option>";
            ?>
          </select>
        </div>
      </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="clientnm">Client Name</label>
            <select class="form-control" name="clientnm" id="clientnm" readonly>
                <?php
                    $sql = mysqli_query($con,"SELECT id,companynm FROM `fin_customers` WHERE `id`='$fthcolc->client_nm' AND `status`='1'");
                    $fetch = mysqli_fetch_object($sql);
                    echo "<option value='".$fetch->id."'>".$fetch->companynm."</option>";
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="prj_name" id="prjctnm">
        <option value="">--- Select Project ---</option>
        <?php
          $prjqr = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `status`='1' AND `ptype_org`='$fthorg->id'");
          while ($prjnm = mysqli_fetch_object($prjqr)) {
            echo "<option value='$prjnm->id'>".$prjnm->pname."</option>";
          }
        ?>
        </select>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sbprjctnm">Sub Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="sbprjctnm" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="col_requested_amt">Requested Amount</label>
            <input type="text" class="form-control" name="col_requested_amt" id="col_requested_amt" value="<?php echo $fthcolc->rqst_amnt?>" readonly>
        </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="remark">Remark<span style="color:red">*</span></label>
        <textarea class="form-control" name="remark" id="remark"></textarea>
      </div>
    </div>
  </div>        
</div>

<!-- End of Collection Payment Details Form -->