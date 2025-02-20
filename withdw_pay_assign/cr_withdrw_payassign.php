<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#drwrnm').select2();      
    });
</script>
<!-- Withdraw ther Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Withdraw Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="drwrnm">Name<span style="color:red">*</span></label>
          <select class="form-control select2" name="drwrnm" id="drwrnm" readonly>
            <option value="">---Select Name---</option>
            <?php
              $empqr = mysqli_query($con, "SELECT x.id,x.fullname FROM `mstr_emp` x JOIN `hr_employee_service_register` y ON x.id=y.emp_name WHERE x.`status`='1' AND y.department_id='20'");
              while ($fthemp = mysqli_fetch_object($empqr)) {
                echo "<option value='".$fthemp->id."'>".$fthemp->fullname."</option>";
              }
            ?>
          </select>
        </div>
    </div>
  </div>        
</div>

<!-- End of Withdraw Payment Details Form -->