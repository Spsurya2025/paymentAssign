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
<div class="row" style="margin-top: 20px;">
  <?php
    $prid = $_GET['py_req_id'];
    $getpr = mysqli_query($con, "SELECT x.*, y.fullname FROM `fin_payment_request` x, `mstr_emp` y WHERE x.`reqbyid`=y.`id` AND x.`status`='1' AND x.`id`='$prid'");
    $fthpr = mysqli_fetch_object($getpr);
    $getDetails = mysqli_query($con,"SELECT * FROM fin_all_pay_request WHERE pay_request_id='$prid' AND request_for='Operator'");
    $arrData = mysqli_fetch_object($getDetails);
    $get_opt_Details = mysqli_query($con,"SELECT * FROM prj_optr_payrqst WHERE pay_request_id='$prid' AND rqstfor='Operator'");
    $arr_optr_Data = mysqli_fetch_object($get_opt_Details);
  ?>
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Operator Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="suplrnm">Project Name</label>
        <select class="form-control" name="projnm" id="projnm" readonly>
          <?php
            $prjqry = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE id ='$arr_optr_Data->projectid'");
            $fthtrns = mysqli_fetch_object($prjqry);
          ?>
         <option value="<?php echo $fthtrns->id;?>"><?php echo $fthtrns->pname;?></option>
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="o_organization_name" id="o_organization" readonly>
          <?php
            if (isset($_GET['py_req_id'])) {
                $pay_rid = $_GET['py_req_id'];
                $sql1 = mysqli_query($con, "SELECT y.id,y.organisation FROM `fin_all_pay_request` x, `prj_organisation` y WHERE x.`pay_request_id`='$pay_rid' AND x.`organisation_id`=y.`id`");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">Request No.</label>
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
        <label for="prj_name">Sub Project Name</label>
        <select class="form-control" name="sb_prj_name" id="sb_prj_name" readonly>
          <?php
            $prjqry = mysqli_query($con, "SELECT id,spname FROM `prj_subproject` WHERE id ='$arrData->sub_prj'");
            $fthtrns = mysqli_fetch_object($prjqry);
          ?>
          <option value="<?php echo $fthtrns->id;?>"><?php echo $fthtrns->spname;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">State</label>
        <select class="form-control" name="state" id="state" readonly>
          <?php
            $sql1 = "SELECT id,sname FROM  prj_state
            WHERE id='$arr_optr_Data->states_id'";
            $result1 = mysqli_query($con, $sql1);
            $fthtrns = mysqli_fetch_object($result1);
            if($fthtrns != '' && $fthtrns != 0){
              echo $state = $fthtrns->sname;
            }else{
              echo $state = "Not Available.";
            }
          ?>
          <option value="<?php echo $fthtrns->id;?>"><?php echo $state;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">District</label>
        <select class="form-control" name="district" id="district" readonly>
          <?php
            $sql2 = "SELECT id,distname FROM  prj_district
            WHERE id='$arr_optr_Data->districts_id'";
            $result2 = mysqli_query($con, $sql2);
            $fthtrns = mysqli_fetch_object($result2);
            if($fthtrns != '' && $fthtrns != '0'){
              echo $district = $fthtrns->distname;
            }else{
              echo $district = "Not Available.";
            }
          ?>
          <option value="<?php echo $fthtrns->id;?>"><?php echo $district;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">Block</label>
        <select class="form-control" name="block" id="block" readonly>
          <?php
            $sql3 = "SELECT id,blockname FROM  prj_block
            WHERE id='$arr_optr_Data->blocks_id'";
            $result3 = mysqli_query($con, $sql3);
            $fthtrns = mysqli_fetch_object($result3);
            if($fthtrns != '' && $fthtrns != 0){
              echo $block = $fthtrns->blockname;
            }else{
              echo $block = "Not Available.";
            }
          ?>
          <option value="<?php echo $fthtrns->id;?>"><?php echo $block;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">GP Name</label>
        <select class="form-control" name="gp_name" id="gp_name" readonly>
          <?php
            $sql4 = "SELECT id,gpname FROM  prj_gpanchayat
            WHERE id='$arr_optr_Data->grmps_id'";
            $result4 = mysqli_query($con, $sql4);
            $fthtrns = mysqli_fetch_object($result4);
            if($fthtrns != '' && $fthtrns != 0){
              echo $gp = $fthtrns->gpname;
            }else{
              echo $gp = "Not Available.";
            }
          ?>
          <option value="<?php echo $fthtrns->id;?>"><?php echo $gp;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">Total Requested Operator Amount</label>
        <input type="text" name="op_req_amt" id="op_req_amt" value="<?php echo $prjname = $arrData->payreq_amt; ?>" class="form-control" readonly>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
  </div>
    <?php
      $pay_req_id = $_GET['py_req_id'];
      $sql5 = mysqli_query($con, "SELECT * FROM `prj_optr_payrqst` WHERE `pay_request_id`='$pay_req_id' AND `rqstno`='$request_no'");
    ?>
    <div class="col-lg-12" id="prDtls">
      <div class="col-lg-12">
        <legend><h6><strong style="color: #2bc59b;">Operator Details</strong></h6></legend>
        <div class="table-responsive">
          <table class="table table-bordered table-responsive">
            <thead>
              <tr>
                <th>#</th>
                <th>Operator Name</th>
                <th style="width: 120px;">Request No.</th>
                <th>Registration No.</th>
                <th>Account No.</th>
                <th>IFSC</th>
                <th>No. of Month</th>
                <th>Amount</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
                 $i = 0;
                 while($fthop = mysqli_fetch_object($sql5)){
                 $sql6 = mysqli_query($con,"SELECT id,operatorid,operatorname FROM prj_optr_master WHERE status='1' AND operatorid ='$fthop->operatorid'");
                 $result5 = mysqli_fetch_object($sql6);
                 $sql7 =  mysqli_query($con,"SELECT id,optrid,bankifsc FROM prj_optr_bankdetails WHERE optrid ='$fthop->operatorid'");
                 $result6 = mysqli_fetch_object($sql7);
                 ?>
                 <tr>
                  <td><?php echo ++$i;?></td>
                  <td>
                    <select class="form-control" name="oper_na" id="" readonly>
                      <option value="<?php echo $result1->id;?>"><?php echo $result5->operatorname;?></option>
                    </select>
                  </td>
                  <td>
                    <input class="form-control" type="text" name="req_num" value="<?php echo $fthop->rqstno;?>" readonly>
                  </td>
                  <td>
                    <input class="form-control" type="text" name="op_id" value="<?php echo $fthop->operatorid;?>" readonly>
                  </td>
                  <td>
                    <input class="form-control" type="text" name="op_accno" value="<?php echo $fthop->optr_acntno;?>" readonly>
                  </td>
                  <td>
                    <input class="form-control" type="text" value="<?php echo $result6->bankifsc;?>" readonly>
                  </td>
                  <td>
                    <input class="form-control" type="text" name="op_mnth" value="<?php echo $fthop->noofmonth;?>" readonly>
                  </td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                      <input class="form-control" type="text" name="op_rate" value="<?php echo $fthop->optramount;?>" readonly>
                    </div>
                  </td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                      <input class="form-control" type="text" name="op_tpaid" id="all_total" value="<?php echo $fthop->totalamnt;?>" readonly>
                      
                    </div>
                    <span id="amt-error" class="error-message"></span>
                  </td>
                 </tr>

                <?php }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

</div>


<!-- End of operator Form -->