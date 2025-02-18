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

<!-- Oerator Form -->
 <?php
   $payment_req_id = $_GET['py_req_id'];
   $rnt_qry = mysqli_query($con, "SELECT * FROM fin_payment_request_rent WHERE payreq_id='$payment_req_id'");
   if(mysqli_num_rows($rnt_qry)>0){
    $ftch_rnt = mysqli_fetch_object($rnt_qry);
 ?>
 
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Rent Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="re_organization" id="re_organization" readonly>
          <?php
           
                $sql1 = mysqli_query($con, "SELECT id,organisation FROM prj_organisation WHERE id ='$ftch_rnt->org_id'");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
          ?>
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="req_n">Request No.</label>
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
        <label for="year">Year</label>
        <input type="text" name="year" id="year" value="<?php echo $ftch_rnt->year; ?>" class="form-control" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="year">Month</label>
        <select class="form-control" name="month" id="month" readonly>
          <?php
          $month = $ftch_rnt->month;
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
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="type">Type</label>
        <input type="text" name="type" id="type" value="<?php echo $ftch_rnt->type; ?>" class="form-control" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="purpose">Purpose</label>
        <input type="text" name="purpose" id="purpose" value="<?php echo $ftch_rnt->purpose; ?>" class="form-control" readonly>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
  </div>
    <?php
      $pay_req_id = $_GET['py_req_id'];
      $sql5 = mysqli_query($con, "SELECT * FROM `fin_payment_request_rent_details` WHERE `last_rnt_req_id`='$ftch_rnt->id' AND `rnt_rqst_num`='$request_no'");
    ?>
    <div class="col-lg-12" id="prDtls">
      <div class="col-lg-12">
        <legend><h6><strong style="color: #2bc59b;">Rent Details</strong></h6></legend>
        <div class="table-responsive">
          <table class="table table-bordered table-responsive">
            <thead>
              <tr>
                <th style="width: 120px;">Request No.</th>
                <th>Code</th>
                <th>Payment Date</th>
                <th>Client</th>
                <th>Project</th>
                <th>Sub Project</th>
                <th>Rate</th>
              </tr>
            </thead>
            <tbody>
              <?php while($fthDetails=mysqli_fetch_object($sql5)) { ?>
                <tr>
                  <td>
                    <input type="text" name="rnt_rqst_num" id="rnt_rqst_num" value="<?php echo $fthDetails->rnt_rqst_num;?>" class="form-control" readonly>
                  </td>
                  <td>
                    <input type="text" name="rnt_code" id="rnt_code" value="<?php echo $fthDetails->rnt_code;?>" class="form-control" readonly>
                  </td>
                  <td>
                    <input type="text" name="rnt_pymnt_dt" id="rnt_pymnt_dt" value="<?php echo $fthDetails->rnt_pymnt_dt;?>" class="form-control" readonly>
                  </td>
                  <td>
                    <select name="client" id="" class="form-control" readonly>
                      <?php 
                        $sql6 = mysqli_query($con, "SELECT id,companynm FROM `prj_creditor` WHERE id ='$fthDetails->client'");
                        $client = mysqli_fetch_object($sql6);
                        echo "<option value='".$client->id."'>".$client->companynm."</option>";
                      ?>
                    </select>
                  </td>
                  <td>
                    <select name="p_id" id="" class="form-control" readonly>
                      <?php 
                        $sql7 = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE id ='$fthDetails->p_id'");
                        $project = mysqli_fetch_object($sql7);
                        echo "<option value='".$project->id."'>".$project->pname."</option>";
                      ?>
                    </select>
                  </td>
                  <td>
                    <select name="sp_id" id="" class="form-control" readonly>
                      <?php 
                        $sql8 = mysqli_query($con, "SELECT id,spname FROM `prj_subproject` WHERE id ='$fthDetails->sp_id'");
                        $sproject = mysqli_fetch_object($sql8);
                        echo "<option value='".$sproject->id."'>".$sproject->spname."</option>";
                      ?>
                    </select>
                  </td>
                  <td>
                    <input type="text" name="rate" id="rate_request_amount" value="<?php echo $fthDetails->rate;?>" class="form-control" readonly>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

</div>
<?php } ?>

<!-- End of operator Form -->