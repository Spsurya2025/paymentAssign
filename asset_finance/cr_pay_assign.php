<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#banifi').select2();
    });
</script>
<!-- GST asset finance Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Asset Finance Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="statenm">Benificiary A/c</label>
        <select class="form-control" name="benif_acc" id="benif_acc">
            <option value="">---Select---</option>
          <?php
            $sqlemp="SELECT x.ref_id, x.position, x.employee_id, y.id, y.fullname AS `name`, y.status, d.dept_name FROM hr_employee_service_register x JOIN mstr_emp y ON x.emp_name = y.id JOIN hr_department d ON x.department_id = d.id WHERE x.ref_id = 0 And x.org_nm='$_GET[org_id]' AND y.status = '1' UNION SELECT x.ref_id, x.position, x.employee_id, y.id, y.fullname AS name, y.status, d.dept_name FROM hr_employee_service_register x JOIN mstr_emp y ON y.mstr_ref_id = x.ref_id JOIN hr_department d ON x.department_id = d.id WHERE x.ref_id <> 0 AND y.status = '1'And x.org_nm='$_GET[org_id]' ORDER BY id ASC";
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
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name<span style="color:red">*</span></label>
        <select class="form-control select2" name="prj_name" id="prjctnm">
          <option value="">--- Select Project ---</option>
          <?php
            $prjqr = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `status`='1' AND `ptype_org`='$_GET[org_id]' OR ptype='Corporate'");
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
        <select class="form-control select2" name="sbprjctnm" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
  </div>        
</div>

<!-- End of asset finance Payment Details Form -->
<script>
    $(document).ready(function () {
      $("#benif_acc").select2();
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