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
  
<!-- End of Scripts -->

<!-- Salary Processing Form -->
<?php
  $sql = mysqli_query($con,"SELECT t2.*, t1.* FROM hr_employee_salary_processing t1 INNER JOIN hr_employee_salary_report t2 ON t1.`id` = t2.`unique_id` WHERE t2.`req_id`='$request_no'");
  $fthsp = mysqli_fetch_object($sql);
?>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Salary Processing</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Benificiary A/c</label>
        <select class="form-control splpr" name="benif_acc" id="benif_acc" readonly>
          <?php
            $eq = "SELECT id,fullname FROM `mstr_emp` WHERE id='$fthsp->emp_id'";
            $efq=mysqli_query($con,$eq);
            while ($egq = mysqli_fetch_object($efq))
            {
              echo '<option value="'. $egq->id . '">' . $egq->fullname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="sal_organization_name" id="sal_organization" readonly>
          <?php
                $sql1 = mysqli_query($con, "SELECT id,organisation FROM prj_organisation WHERE id='$fthsp->org_nm'");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Location</label>
        <select class="form-control splpr" name="location" id="location" readonly>
          <?php
            $loc = "SELECT id,lname FROM `hr_location` WHERE id='$fthsp->location_id'";
            $eloc=mysqli_query($con,$loc);
            while ($elocq = mysqli_fetch_object($eloc))
            {
              echo '<option value="'. $elocq->id . '">' . $elocq->lname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Year</label>
        <select class="form-control splpr" name="year" id="year" readonly>
          <?php
            $myr = $fthsp->month;
            $split = explode("-",$myr);
            echo '<option value="'. $split[0] . '">' . $split[0] .'</option>';
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group splpr">
        <label for="org_id">Month</label>
        <select class="form-control" name="month" id="month" readonly>
          <?php
          $month = $split[1];
          switch ($month) {
            case "01":
              echo "<option value='01'>January</option>";
              break;
            case "02":
              echo "<option value='02'>February</option>";
              break;
            case "03":
              echo "<option value='03'>March</option>";
              break;
            case "04":
              echo "<option value='04'>April</option>";
              break;
            case "05":
              echo "<option value='05'>May</option>";
              break;
            case "06":
              echo "<option value='06'>June</option>";
              break;
            case "07":
              echo "<option value='07'>July</option>";
              break;
            case "08":
              echo "<option value='08'>August</option>";
              break;
            case "09":
              echo "<option value='09'>September</option>";
              break;
            case "10":
              echo "<option value='10'>October</option>";
              break;
            case "11":
              echo "<option value='11'>November</option>";
              break;
            case "12":
              echo "<option value='12'>December</option>";
              break;
          }
        ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="type">Department</label>
        <?php
          $sql1 = mysqli_query($con, "SELECT dept_name FROM hr_department WHERE id='$fthsp->department_id'");
          $dept = mysqli_fetch_object($sql1);
        ?>
        <input type="text" class="form-control" name="depart" id="depart" value="<?php echo $dept->dept_name;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="type">Employee Code</label>
        <input type="text" class="form-control" name="emp_code" id="emp_code" value="<?php echo $fthsp->employ_id;?>" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="type">Requested Net Payment</label>
        <input type="text" class="form-control" name="net_pay" id="all_total" value="<?php echo $fthsp->net_pay;?>" readonly>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="form-group">
        <label for="type">Remarks</label>
        <textarea class="form-control" name="sp_remarks" id="sp_remarks"></textarea>
      </div>
    </div>
  </div>        
</div>

<!-- End of Salary Form -->