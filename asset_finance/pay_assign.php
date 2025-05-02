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
<?php
    $assfiqr = mysqli_query($con, "SELECT * FROM hr_assetfinance_request where astfinl_id = '$request_no'");
    $fthassfi= mysqli_fetch_object($assfiqr);
?>
<!-- Asset finance Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Asset Finance Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="asfi_organization" id="asfi_organization" readonly>
          <?php
              $sql1 = mysqli_query($con, "SELECT id,organisation FROM `prj_organisation` WHERE id='$fthassfi->org_id'");
              $fthorg = mysqli_fetch_object($sql1);
              echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Initial Generated Request Number</label>
            <input type="text" class="form-control" name="af_req_id" id="af_req_id" value="<?php echo $fthassfi->af_req_id;?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="statenm">Benificiary A/c</label>
        <select class="form-control" name="benif_acc" id="benif_acc" readonly>
          <?php
            $sqlemp="SELECT x.ref_id, x.position, x.employee_id, y.id, y.fullname AS `name`, y.status, d.dept_name FROM hr_employee_service_register x JOIN mstr_emp y ON x.emp_name = y.id JOIN hr_department d ON x.department_id = d.id WHERE x.ref_id = 0 And y.id='$fthassfi->name_id' AND y.status = '1' UNION SELECT x.ref_id, x.position, x.employee_id, y.id, y.fullname AS name, y.status, d.dept_name FROM hr_employee_service_register x JOIN mstr_emp y ON y.mstr_ref_id = x.ref_id JOIN hr_department d ON x.department_id = d.id WHERE x.ref_id <> 0 AND y.status = '1'And y.id='$fthassfi->name_id' ORDER BY id ASC";
        		$empqr = mysqli_query($con, $sqlemp);
        		while($data=mysqli_fetch_object($empqr))
            {
              if ($data->id == '0') {
                  $optionName = "Other";
              } else {
                  $optionName = $data->name . " (" . $data->dept_name . ", " . $data->position . ", " . $data->employee_id . ")";
              }
            ?>
            <option value="<?php echo $data->id; ?>"> <?php echo $optionName; ?></option>
            <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Salary</label>
            <input type="text" class="form-control" name="gstno" id="gstno" value="<?php echo $fthassfi->salary;?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Requested Amount</label>
            <input type="text" class="form-control" name="af_request_amount" id="requested_amt" value="<?php echo $fthassfi->afl_amount;?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name<span style="color:red">*</span></label>
        <select class="form-control select2" name="prj_name" id="prjctnm">
          <option value="">--- Select Project ---</option>
          <?php
            $prjqr = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `status`='1' AND `ptype_org`='$fthorg->id' OR ptype='Corporate'");
            while ($prjnm = mysqli_fetch_object($prjqr)) {
              echo "<option value='$prjnm->id'>".$prjnm->pname."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sbprjctnm">Sub Project Name<span style="color:red">*</span></label>
        <select class="form-control select2" name="sbprjctnm" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Purpose</label>
            <input type="text" class="form-control" name="af_purpose" id="af_purpose" value="<?php echo $fthassfi->purpose;?>" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Message</label>
            <textarea name="af_message" id="af_message" class="form-control" readonly><?php echo $fthassfi->message;?></textarea>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">TL Remark</label>
            <textarea name="" id="" class="form-control" readonly><?php echo $fthassfi->tl_remark;?></textarea>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">HR Remark</label>
            <textarea name="" id="" class="form-control" readonly><?php echo $fthassfi->hr_remark;?></textarea>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Super Admin Remark</label>
            <textarea name="" id="" class="form-control" readonly><?php echo $fthassfi->sa_remark;?></textarea>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">Finance Remark</label>
            <textarea name="" id="" class="form-control" readonly><?php echo $fthassfi->f_remark;?></textarea>
        </div>
    </div>
    
  </div>        
</div>

<!-- End of Asset finance Payment Details Form -->
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