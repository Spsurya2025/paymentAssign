<?php 
require_once('../../auth.php');
require_once('../../config.php');
require_once '../../new_header.php'
 ?>
<?php
$empid = $_SESSION['ERP_SESS_ID'];
if(isset($_POST['payasgn']))
{    
   $msg = '';
   $bnkimprt_id = $_GET['bimpid'];
   $preqnum = mysqli_real_escape_string($con, $_POST['preqnum']);
   $statement_id = mysqli_real_escape_string($con, $_POST['stmnt_prvw']);
   $bankacc_id = mysqli_real_escape_string($con, $_POST['bankacc_id']);
   $trnsc_type = mysqli_real_escape_string($con, $_POST['trnsc_type']);
   $orgnsn_name = mysqli_real_escape_string($con, $_POST['pay_orgnstn']);
   $trnscto = mysqli_real_escape_string($con, $_POST['trnscto']);
   $payee_nm = mysqli_real_escape_string($con, $_POST['payee_nm']);
   $paid_amnt = mysqli_real_escape_string($con, $_POST['paidamt']);
   $created_on = date('Y-m-d H:i:s');
   $sql = "SELECT * FROM fin_payment_entry WHERE preqnum = '$preqnum'";
   $result = $con->query($sql);
   if ($result->num_rows > 0) 
   {       
      echo "<script>alert('Payment request number: $preqnum already exists!');</script>";
      echo "<script>window.history.go(-1);</script>";
   } 
   else 
   {
      $insqry = mysqli_query($con, "INSERT INTO `fin_payment_entry` (`bnkimprt_id`, `statement_id`, `bankacc_id`, `preqnum`, `trnsc_type`, `payment_mode`, `orgnsn_name`, `trnscto`, `payee_nm`, `pay_assgn_stat`, `pay_approval_stat`, `status`, `frst_apprv`, `frst_apprv_date`) VALUES ('$bnkimprt_id', '$statement_id', '$bankacc_id', '$preqnum', '$trnsc_type', 'offline', '$orgnsn_name', '$trnscto', '$payee_nm', '1', '1', '1','$empid','$created_on')");   
      $pentry_last_id = mysqli_insert_id($con);  
      $pay_request_id = mysqli_real_escape_string($con, $_POST['pay_rqst_id']);
      if($insqry)
      {
         $updpeqr = mysqli_query($con,"UPDATE fin_banking_imports SET pr_num='$preqnum',is_pay_asgnd='1',is_pay_aprvd='1' WHERE id='$bnkimprt_id'");
         if($trnscto == "Vendor")
         {
            $vndrnm = mysqli_real_escape_string($con, $_POST['vndrnm']);
            $prjct_name = mysqli_real_escape_string($con, $_POST['prjct_name']);
            $jobodr_num = mysqli_real_escape_string($con, $_POST['jobodr_num']);
            $jobodr_val = mysqli_real_escape_string($con, $_POST['jobodr_val']);
            $subprjct_nm = mysqli_real_escape_string($con, $_POST['subprjct_nm']);
            $bmsnm = mysqli_real_escape_string($con, $_POST['bmsnm']);
            $wrk_dscrptn = mysqli_real_escape_string($con, $_POST['wrk_dscrptn']);
            $subprjct_val = mysqli_real_escape_string($con, $_POST['subprjct_val']);
            $req_amt = mysqli_real_escape_string($con, $_POST['req_amt_v']);
            $vndrinqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_vendor` (`payent_id`, `pay_rqst_id`, `vndrnm`, `prjct_name`, `jobodr_num`, `jobodr_val`, `subprjct_nm`, `bmsnm`, `wrk_dscrptn`, `subprjct_val`, `rqst_amt`, `paid_amnt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$vndrnm', '$prjct_name', '$jobodr_num', '$jobodr_val', '$subprjct_nm', '$bmsnm', '$wrk_dscrptn', '$subprjct_val', '$req_amt', '$paid_amnt', '1')");
            if($vndrinqr)
            {
               echo "<script>alert('Vendor payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Supplier") 
         {
            $suplrnm = mysqli_real_escape_string($con, $_POST['suplrnm']);
            $prj_name = mysqli_real_escape_string($con, $_POST['prj_name']);
            $ponum = mysqli_real_escape_string($con, $_POST['ponum']);
            $podate = mysqli_real_escape_string($con, $_POST['podate']);
            $poamnt = mysqli_real_escape_string($con, $_POST['poamnt']);
            $spreq_typ = mysqli_real_escape_string($con, $_POST['spreq_typ']);
            if (!empty($_POST['pr_data'])) {
               foreach ($_POST['pr_data'] as $id) {
                  $pr_numbr = mysqli_real_escape_string($con, $_POST['pr_numbr'][$id]);
                  $subprj_nm = mysqli_real_escape_string($con, $_POST['subprj_nm'][$id]);
                  $bms_name = mysqli_real_escape_string($con, $_POST['bms_name'][$id]);
                  $pramnt = mysqli_real_escape_string($con, $_POST['pramnt'][$id]);
                  $pr_request_amt = mysqli_real_escape_string($con, $_POST['pr_reqamt'][$id]);
                  $trnsrsn = '';
                  $pr_paid_amnt = $paid_amnt;
                  $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '$pr_numbr', '$subprj_nm', '$bms_name', '$pramnt', '$pr_request_amt', '$pr_paid_amnt', '$trnsrsn', '0', '$trns_paid_amnt', '1')"); 
                  if($splrqr)
                  {
                     echo "<script>alert('Supplier payment assign details successfully inserted')</script>";
                  }  
               }

            }
            if(!empty($_POST['tr_data']))
            {
               foreach($_POST['tr_data'] as $id)
               {
                  $pr_numbr = '';
                  $subprj_nm = '';
                  $bms_name = '';
                  $pramnt = '';
                  $trnsrsn = mysqli_real_escape_string($con, $_POST['trnsrsn'][$id]);
                  $trns_rqst_amt = mysqli_real_escape_string($con, $_POST['trreqamt'][$id]);
                  $trns_paid_amnt = $paid_amnt;
                  $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`) VALUES ('$pentry_last_id', '0', '$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '$pr_numbr', '$subprj_nm', '$bms_name', '$pramnt', '0', '$pr_paid_amnt', '$trnsrsn', '$trns_rqst_amt', '$trns_paid_amnt', '1')");
                  if($splrqr)
                  {
                     echo "<script>alert('Supplier payment assign details successfully inserted')</script>";
                  } 
               }
            }
         } 
         else if ($trnscto == "Operator")
         {
            $op_py_req_amt = mysqli_real_escape_string($con, $_POST['op_req_amt']);
            $op_py_req_num = mysqli_real_escape_string($con, $_POST['req_num']);
            $op_id = mysqli_real_escape_string($con, $_POST['op_id']);
            $op_accno = mysqli_real_escape_string($con, $_POST['op_accno']);
            $op_mnth = mysqli_real_escape_string($con, $_POST['op_mnth']);
            $op_rate = mysqli_real_escape_string($con, $_POST['op_rate']);
            $oprinqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_operator` (`fin_pay_entry_id`, `pay_request_id`, `pay_req_num`, `operatorid`, `optraccno`, `rqsted_month`, `optr_rate`, `request_amt`, `amountpaid`, `entrydate`,`aprovalstate`) VALUES ('$pentry_last_id', '$pay_request_id', '$op_py_req_num', '$op_id', '$op_accno', '$op_mnth', '$op_rate', '$op_py_req_amt', '$paid_amnt', '$created_on', '0')");
            if($oprinqr)
            {
               echo "<script>alert('Operator payment assign details successfully inserted')</script>";
            } 
         }
         else if ($trnscto == "Transporter") 
         {
            $trnsprtrnm = mysqli_real_escape_string($con, $_POST['trnsprtrnm']);
            $prjctnm = mysqli_real_escape_string($con, $_POST['prjctnm']);
            $subprjnm = mysqli_real_escape_string($con, $_POST['subprjnm']);
            $bmsnm = mysqli_real_escape_string($con, $_POST['bmsnm']);
            $ponum = mysqli_real_escape_string($con, $_POST['ponum']);
            $place_from = mysqli_real_escape_string($con, $_POST['place_from']);
            $place_to = mysqli_real_escape_string($con, $_POST['place_to']);
            $distance = mysqli_real_escape_string($con, $_POST['distance']);
            $material_nm = mysqli_real_escape_string($con, $_POST['material_nm']);
            $mtrl_weight = mysqli_real_escape_string($con, $_POST['mtrl_weight']);
            $service_typ = mysqli_real_escape_string($con, $_POST['service_typ']);
            $lry_model = mysqli_real_escape_string($con, $_POST['lry_model']);
            $dala_typ = mysqli_real_escape_string($con, $_POST['dala_typ']);
            $carrycap = mysqli_real_escape_string($con, $_POST['carrycap']);
            $totalamnt = mysqli_real_escape_string($con, $_POST['totalamnt']);
            $rateper_km = mysqli_real_escape_string($con, $_POST['rateper_km']);
            $rateper_kg = mysqli_real_escape_string($con, $_POST['rateper_kg']);
            $adv_prcnt = mysqli_real_escape_string($con, $_POST['adv_prcnt']);
            $adv_amt = mysqli_real_escape_string($con, $_POST['adv_amt']);
            $final_amnt = mysqli_real_escape_string($con, $_POST['final_amnt']);
            $trnsp_req_amt = mysqli_real_escape_string($con, $_POST['trnsp_req_amt']);
            $trnsptqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_transporter` (`payent_id`, `pay_rqst_id`, `trnsprtrnm`, `prjctnm`, `subprjnm`, `bmsnm`, `ponum`, `place_from`, `place_to`, `distance`, `material_nm`, `mtrl_weight`, `service_typ`, `lry_model`, `dala_typ`, `carrycap`, `totalamnt`, `rateper_km`, `rateper_kg`, `adv_prcnt`, `adv_amt`, `final_amnt`, `trns_req_amt`, `paidamnt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$trnsprtrnm', '$prjctnm', '$subprjnm', '$bmsnm', '$ponum', '$place_from', '$place_to', '$distance', '$material_nm', '$mtrl_weight', '$service_typ', '$lry_model', '$dala_typ', '$carrycap', '$totalamnt', '$rateper_km', '$rateper_kg', '$adv_prcnt', '$adv_amt', '$final_amnt', '$trnsp_req_amt', '$paid_amnt', '1')");
            if($trnsptqr)
            {
               echo "<script>alert('Transporter payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Salary Processing") 
         { 
            $benif_acc = mysqli_real_escape_string($con, $_POST['benif_acc']);
            $location = mysqli_real_escape_string($con, $_POST['location']);
            $month = mysqli_real_escape_string($con, $_POST['month']);
            $year = mysqli_real_escape_string($con, $_POST['year']);
            $req_id = $preqnum;
            $orgname = $orgnsn_name;
            $sp_remarks = mysqli_real_escape_string($con, $_POST['sp_remarks']);
            $monthyr = $year.'-'.$month;
            $empsqaf = mysqli_query($con, "INSERT INTO `fin_payment_entry_sal_pro` (`payent_id`, `sp_req_id`, `benif_acc`, `orgname`, `location`, `month`, `sp_amount`,`sp_remarks`, `status`) VALUES ('$pentry_last_id', '$req_id', '$benif_acc', '$orgname', '$location', '$monthyr','$paid_amnt', '$sp_remarks', '1')");
            if($empsqaf)
            {
               echo "<script>alert('Salary Processing payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Expense") 
         { 
            $expns_for = mysqli_real_escape_string($con, $_POST['expns_for']);
            $exp_for_empcode = mysqli_real_escape_string($con, $_POST['exp_for_empcode']);
            $prjct = mysqli_real_escape_string($con, $_POST['prjct']);
            $sub_prjct = mysqli_real_escape_string($con, $_POST['sub_prjct']);
            $bmsnm = mysqli_real_escape_string($con, $_POST['bmsnm']);
            $expenen = mysqli_query($con, "INSERT INTO `fin_payment_entry_expense` (`payent_id`, `pay_rqst_id`, `expns_for`, `exp_for_empcode`, `prjct`, `sub_prjct`, `bmsnm`, `exp_req_amt`, `paid_exp_amt`, `status`) VALUES ('$pentry_last_id', '0', '$expns_for', '$exp_for_empcode', '$prjct', '$sub_prjct', '$bmsnm', '0', '$paid_amnt', '1')");
            if($expenen)
            {
               echo "<script>alert('Expense payment assign details successfully inserted')</script>";
            }
         }
         echo "<script>window.history.go(-2);</script>"; 
      } 
      else 
      {
         $msg= "<div class='alert alert-danger'>Error occurred while creating the payment entry. Please try again.</div>";
      }
   }
}    
?>
<title><?php if(isset($_GET['bimpid']) && isset($_GET['peid'])) { echo "Update Payment Assignment"; } else if (isset($_GET['bimpid'])) { echo "Add Payment Assignment"; } ?> : Suryam Group</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<style>
   .form-control.selectize-control {
      height: 28px !important;
   }
</style>
<script>
   $(document).ready(function () {
      $('#request_num').selectize({
      sortField: 'text',
      placeholder: '---Select Payment Request No.---',
      allowEmptyOption: false,
      });
      $('#trnscto').selectize({
      sortField: 'text'
      });
   });
</script>
<div id="page-wrapper" style="margin-left: 0;">
   <div class="row" style="margin-top: -35px;">
      <div class="col-lg-12">
         <h3 class="page-header" style="font-weight: bolder; color: #900c09; text-transform: uppercase; text-align: center;"><?php if(isset($_GET['bimpid']) && isset($_GET['peid'])) { echo "Update Payment Assignment"; } else if(isset($_GET['bimpid'])) { echo "Payment Assignment"; } ?></h3>
      </div>
      <!-- /.col-lg-12 -->
   </div>
   <!-- /.row -->
   <div class="row" style="margin: 10px;">
      <!-- Body Starts Here -->
      <?php if(isset($msg)) { echo "<i style=color:#33D15B;>".$msg."</i>"; } ?>
      <form name="form" method="post" class="forms-sample" style="margin-left: 5px;" onsubmit="return validForm()">
         <legend>
            <h5 style="color: #008787;">Uploaded Payment Details</h5>
         </legend>
         <?php
            if (isset($_GET['bimpid'])) {
               $bimpid = $_GET['bimpid'];
               $dtlsqr = mysqli_query($con, "SELECT x.*,y.* FROM `fin_banking_imports` x, `fin_statement_preview` y WHERE x.`preview_id`=y.`id` AND x.`id`='$bimpid' AND y.`status`='1'");
               $fthimps = mysqli_fetch_object($dtlsqr);
               $pay_req_number = mysqli_query($con, "SELECT * FROM fin_all_pay_request WHERE organisation_id='$fthimps->orgnstn_id'");
               $query = "SELECT request_for,pay_request_id,pr_num,organisation_id,payreq_amt FROM fin_all_pay_request WHERE FIND_IN_SET('$fthimps->pr_num', REPLACE(pr_num, '#', ','))";
               $result = mysqli_query($con, $query);
               $row1 = mysqli_fetch_object($result);
            }
            ?>
         <fieldset>
            <div class="row">
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6" style="display: none;">
                  <div class="form-group">
                     <label for="stmnt_prvw">Statement Preview ID</label>
                     <input type="text" class="form-control" name="stmnt_prvw" id="stmnt_prvw" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->preview_id; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6" style="display: none;">
                  <div class="form-group">
                     <label for="bankacc_id">Bank Account ID</label>
                     <input type="text" class="form-control" name="bankacc_id" id="bankacc_id" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->bnkacc_id; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="preqnum">Payment Request No.</label>
                     <select class="form-control" name="preqnum" id="request_num">
                        <option value="">---Select payment Request no.---</option>
                     </select>
                  </div>
               </div>
               <input type="hidden" name="preqnum" id="preqnum">
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="pay_orgnstn">Payment Under Organisation</label>
                     <select class="form-control" name="pay_orgnstn" id="pay_orgnstn" readonly>
                     <?php
                        if (isset($_GET['bimpid'])) {
                           $orgnid = $fthimps->orgnstn_id;
                           $orgqr = mysqli_query($con, "SELECT * FROM `prj_organisation` WHERE `id`='$orgnid'");
                           $fthorg = mysqli_fetch_object($orgqr);
                           echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
                        }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="pay_bnkacc">Payment Under Bank Account</label>
                     <select class="form-control" name="pay_bnkacc" id="pay_bnkacc" readonly>
                     <?php
                        if (isset($_GET['bimpid'])) {
                           $bnkaccid = $fthimps->bnkacc_id;
                           $bnkqr = mysqli_query($con, "SELECT * FROM `fin_bankaccount` WHERE `id`='$bnkaccid'");
                           $fthbacc = mysqli_fetch_object($bnkqr);
                           echo "<option value='".$fthbacc->id."'>".$fthbacc->accnm."</option>";
                        }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="trnsc_type">Payment Transaction Type</label>
                     <input type="text" class="form-control" name="trnsc_type" id="trnsc_type" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->transac_type; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="trnscto">Transaction To/Type</label>
                     <select class="form-control" name="trnscto" id="trnscto">
                     <?php
                           if (isset($_GET['bimpid']) && isset($_GET['peid'])) {
                              echo "<option value='".$row->trnscto."'>".$row->trnscto."</option>";
                           }
                           else { 
                           ?>
                        <option value="">--- Select Transaction To/Type ---</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Transporter">Transporter</option>
                        <option value="GST">GST</option>
                        <option value="Withdraw">Withdraw</option>
                        <!-- <option value="EMI">EMI</option> -->
                        <option value="Collection">Collection</option>
                        <option value="Expense">Expense</option>
                        <option value="Rent">Rent</option>
                        <option value="DD">DD</option>
                        <option value="FD">FD</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Salary Advance">Salary Advance</option>
                        <option value="Loan Advance">Employee Loan Advance</option>
                        <option value="Loan Assignment">Loan Assignment</option>
                        <option value="Asset Finance">Asset Finance</option>
                        <option value="LC Processing">LC Processing</option>
                        <option value="Salary Processing">Salary Processing</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Operator">Operator Payment</option>
                        <option value="Others">Others</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <input type="hidden" id="payment_req_id">

               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="payee_nm">Payee Name</label>
                     <input type="text" class="form-control" name="payee_nm" id="payee_nm" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->payee_name; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="paidamt">Paid/Approved Amount</label>
                     <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                        <input type="text" class="form-control" name="paidamt" id="paidamt" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->transac_amt; } ?>" readonly>
                     </div>
                  </div>
               </div>
            </div>
            <div id="showPay">
            </div>
            <div class="row">
               <div class="col-lg-12">
                  <div class="form-group">
                     <div style="margin-top: 15px; margin-bottom: 30px; float: right;">
                        <input type="submit" name="payasgn" id="payasgn" value="ASSIGN" class="btn btn-success mr-2" >
                     </div>
                  </div>
               </div>
            </div>
         </fieldset>
      </form>
      <!-- //Body Ends Here -->     
   </div>
   <!-- /.row -->
</div>
<!-- /#page-wrapper -->
<?php require_once('../../new_footer.php'); ?>
<!-- /#wrapper -->
<!-- Metis Menu Plugin JavaScript -->
<script>
   $(document).ready(function () {
      const trans_type = $("#trnsc_type").val().toUpperCase();
      if(trans_type == 'DEBIT')
      {
         $("#trnscto").change(function () {
            const transaction_to = $(this).val();
            const $select = $("#request_num")[0].selectize;
            const organisation_id = $("#pay_orgnstn").val();
            $select.clear();
            $select.clearOptions();
            $select.refreshOptions();             
            $("#showPay").html(''); 
            $("#payment_req_id").val(''); 
            if(transaction_to === "Supplier"){
               $.ajax({
                  url: "supplier_pay_assign/get_spl.php",
                  data: {
                     trans_to: transaction_to,
                     organisation_id: organisation_id
                  },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     response.forEach(function (item) {
                           const prNums = item.pr_num.split('#'); 
                           prNums.forEach(function (prNum) {
                              if (prNum.trim() !== "") {
                                 $select.addOption({
                                       value: JSON.stringify({ prNum: prNum, payRequestId: item.pay_request_id }),
                                       text: prNum
                                 });
                              }
                           });
                     });
                     $select.refreshOptions(); 
                  },
                  error: function () {
                     alert('Failed to fetch data');
                  }
               });
            }
            else if(transaction_to === "Vendor"){
               $.ajax({
                  url: "Vendor_pay_assign/get_ven.php",
                  data: {
                     trans_to: transaction_to,
                     organisation_id: organisation_id
                  },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     response.forEach(function (item) {
                           const prNums = item.pr_num.split('#'); 
                           prNums.forEach(function (prNum) {
                              if (prNum.trim() !== "") {
                                 $select.addOption({
                                       value: JSON.stringify({ prNum: prNum, payRequestId: item.pay_request_id }),
                                       text: prNum
                                 });
                              }
                           });
                     });

                     $select.refreshOptions(); 
                  },
                  error: function () {
                     alert('Failed to fetch data');
                  }
               });
            }
            else if(transaction_to === "Operator"){
               $.ajax({
                  url: "Vendor_pay_assign/get_ven.php",
                  data: {
                     trans_to: transaction_to,
                     organisation_id: organisation_id
                  },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     response.forEach(function (item) {
                           const prNums = item.pr_num.split('#'); 
                           prNums.forEach(function (prNum) {
                              if (prNum.trim() !== "") {
                                 $select.addOption({
                                       value: JSON.stringify({ prNum: prNum, payRequestId: item.pay_request_id }),
                                       text: prNum
                                 });
                              }
                           });
                     });

                     $select.refreshOptions(); 
                  },
                  error: function () {
                     alert('Failed to fetch data');
                  }
               });
            }
            else if(transaction_to === "Transporter"){
               $.ajax({
                  url: "transporter_pay_assign/get_tr.php",
                  data: {
                     trans_to: transaction_to,
                     organisation_id: organisation_id
                  },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     response.forEach(function (item) {
                           const prNums = item.pr_num.split('#'); 
                           prNums.forEach(function (prNum) {
                              if (prNum.trim() !== "") {
                                 $select.addOption({
                                       value: JSON.stringify({ prNum: prNum, payRequestId: item.pay_request_id }),
                                       text: prNum
                                 });
                              }
                           });
                     });

                     $select.refreshOptions(); 
                  },
                  error: function () {
                     alert('Failed to fetch data');
                  }
               });
            }
            else if(transaction_to === "Salary Processing"){
               $.ajax({
                  url: "salary_pay_assign/get_sal.php",
                  data: {
                     trans_to: transaction_to,
                     organisation_id: organisation_id
                  },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     response.forEach(function (item) {
                           const prNum = item.pr_num; 
                                 $select.addOption({
                                       value: JSON.stringify(prNum),
                                       text: prNum
                                 });
                     });
                     $select.refreshOptions(); 
                  },
                  error: function () {
                     alert('Failed to fetch data');
                  }
               });
            }
            else if(transaction_to === "Expense"){
               $.ajax({
                  url: "exp_pay_assign/get_exp.php",
                  data: {
                     trans_to: transaction_to,
                     organisation_id: organisation_id
                  },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     response.forEach(function (item) {
                           const prNum = item.pr_num; 
                                 $select.addOption({
                                       value: JSON.stringify(prNum),
                                       text: prNum
                                 });
                     });
                     $select.refreshOptions(); 
                  },
                  error: function () {
                     alert('Failed to fetch data');
                  }
               });
            }
         });
         $("#request_num").change(function () {
            $("#showPay").html(''); 
            $("#preqnum").val('');
            const selectedValue = $(this).val(); 
            if(selectedValue) {
               const parsedValue = JSON.parse(selectedValue); 
               const request_num = parsedValue.prNum; 
               const pay_request_id = parsedValue.payRequestId; 
               const request_num_sp = JSON.parse(selectedValue);
               const trnsto = $("#trnscto").val(); 
               $("#payment_req_id").val(pay_request_id); 
               $("#preqnum").val(request_num)
               if(request_num && trnsto === "Supplier") {
                  $.ajax({
                        url: "supplier_pay_assign/supplier_payasgn.php",
                        data: {
                           py_req_id: pay_request_id,
                           request_num: request_num
                        },
                        type: 'GET',
                        success: function (response) {
                           const resp = $.trim(response);
                           $("#showPay").html(resp); 
                        },
                        error: function () {
                           alert('Failed to fetch supplier data');
                        }
                  });
               }
               else if(request_num && trnsto === "Vendor") {
                  $.ajax({
                        url: "Vendor_pay_assign/vendor_payasign.php",
                        data: {
                           py_req_id: pay_request_id,
                           request_num: request_num
                        },
                        type: 'GET',
                        success: function (response) {
                           const resp = $.trim(response);
                           $("#showPay").html(resp); 
                        },
                        error: function () {
                           alert('Failed to fetch vendor data');
                        }
                  });
               } 
               else if(request_num && trnsto === "Operator") {
                  $.ajax({
                        url: "operator_pay_assign/operator_payasgn.php",
                        data: {
                           py_req_id: pay_request_id,
                           request_num: request_num
                        },
                        type: 'GET',
                        success: function (response) {
                           const resp = $.trim(response);
                           $("#showPay").html(resp); 
                        },
                        error: function () {
                           alert('Failed to fetch operator data');
                        }
                  });
               }  
               else if(request_num && trnsto === "Transporter") {
                  $.ajax({
                        url: "transporter_pay_assign/transport_pay_assign.php",
                        data: {
                           py_req_id: pay_request_id,
                           request_num: request_num
                        },
                        type: 'GET',
                        success: function (response) {
                           const resp = $.trim(response);
                           $("#showPay").html(resp); 
                        },
                        error: function () {
                           alert('Failed to fetch transporter data');
                        }
                  });
               } 
               else if(request_num_sp && trnsto === "Salary Processing") {
                  $("#preqnum").val(request_num_sp)
                  $.ajax({
                        url: "salary_pay_assign/salary_payassign.php",
                        data: {
                           request_num: request_num_sp
                        },
                        type: 'GET',
                        success: function (response) {
                           const resp = $.trim(response);
                           $("#showPay").html(resp); 
                        },
                        error: function () {
                           alert('Failed to fetch salary processing data');
                        }
                  });
               }
               else if(request_num_sp && trnsto === "Expense") {
                  $("#preqnum").val(request_num_sp)
                  $.ajax({
                        url: "exp_pay_assign/exp_payassign.php",
                        data: {
                           request_num: request_num_sp
                        },
                        type: 'GET',
                        success: function (response) {
                           const resp = $.trim(response);
                           $("#showPay").html(resp); 
                        },
                        error: function () {
                           alert('Failed to fetch expense data');
                        }
                  });
               }   
            }
         });
      }
      else if(trans_type == 'CREDIT')
      {
         $("#trnscto").change(function() {
             var trnscto = $(this).val();
             var trnsctn_typ = $("#trnsc_type").val();
             var paidamt = $("#paidamt").val();
             var pay_bnkacc = $("#pay_bnkacc").val();
             var pay_orgnstn = $("#pay_orgnstn").val();
             
             if(trnscto == "Vendor") {
               $.ajax({
                 url:"<?php echo SITE_URL; ?>/basic/finance/vendor_payasgn.php",
                 data:{bimpid:<?php echo $_GET['bimpid'];?>},
                 type:'GET',
                 success:function(response) {
                   var resp = $.trim(response);
                   $("#showPay").html(resp);
                 }
               });
             }
             else if(trnscto == "Supplier") {
               $.ajax({
                 url:"<?php echo SITE_URL; ?>/basic/finance/supplier_payasgn.php",
                 data:{bimpid:<?php echo $_GET['bimpid'];?>,trnsctyp:trnsctn_typ},
                 type:'GET',
                 success:function(response) {
                   var resp = $.trim(response);
                   $("#showPay").html(resp);
                 }
               });
             }
             else if(trnscto == "Transporter") {
               $("#dsplDtls").show();
               $.ajax({
                 url:"<?php echo SITE_URL; ?>/basic/finance/trnsprt_payasgn.php",
                 data:{bimpid:<?php echo $_GET['bimpid'];?>},
                 type:'GET',
                 success:function(response) {
                   var resp = $.trim(response);
                   $("#showPay").html(resp);
                 }
               });
             }
             else if(trnscto == "Salary Processing") {
               $.ajax({
                 url:"<?php echo SITE_URL; ?>/basic/finance/salaryprocessing_payasgn.php",
                 data:{bimpid:<?php echo $_GET['bimpid'];?>,paidamt:paidamt,bankaccdi:pay_bnkacc,pay_orgnstn:pay_orgnstn},
                 type:'GET',
                 success:function(response) {
                   var resp = $.trim(response);
                   $("#showPay").html(resp);
                 }
               });
             }
             else if(trnscto == "Operator"){
                $.ajax({
                url:"<?php echo SITE_URL; ?>/basic/finance/operator_payment/ajaxOperator_payasgn.php",
                data:{bimpid:<?php echo $_GET['bimpid'];?>},
                type:'GET',
                success:function(response){
                   var resp = $.trim(response);
                   $("#showPay").html(resp);
                }
                });
             }
             else {
               $("#showPay").html('');
             }
           });
           
      }    
   });
</script>
<script>
   function validForm()
   {
      var trnscto = document.getElementById('trnscto').value.trim();
      var request_num = document.getElementById('request_num').value.trim();
      var paidamt = parseFloat(document.getElementById('paidamt').value);
      var pay_orgnstn = document.getElementById('pay_orgnstn').value.trim();
      if (trnscto === '')
      {
         alert('Please select transaction to/type')
         return false;
      }
      else if( request_num === '')
      {
         alert("Please select request number")
         return false;
      }
      else if(trnscto == 'Supplier')
      {
         var sp_tamt = document.getElementById('all_total');
         var s_organization = document.getElementById('s_organization').value.trim();
         if(pay_orgnstn != s_organization)
         {
            alert("Organisation should be matched");
            return false;
         }
         else if(parseFloat(sp_tamt.value) != paidamt){
            alert("Total request amount should be matched with paid amount");
            sp_tamt.style.border = '1px solid';
            sp_tamt.style.borderColor = 'red';
            return false;
         }
      }
      else if(trnscto == 'Vendor')
      {
         var ven_req_amt = document.getElementById('all_total');
         var v_organization = document.getElementById('v_organization').value.trim();
         if(pay_orgnstn != v_organization)
         {
            alert("Organisation should be matched");
            return false;
         }
         else if(parseFloat(ven_req_amt.value) != paidamt){
            alert("Total request amount should be matched with paid amount");
            ven_req_amt.style.border = '1px solid';
            ven_req_amt.style.borderColor = 'red';
            return false;
         }
      }
      else if(trnscto == 'Operator')
      {
         var op_req_amt = document.getElementById('all_total');
         var o_organization = document.getElementById('o_organization').value.trim();
         if(pay_orgnstn != o_organization)
         {
            alert("Organisation should be matched");
            return false;
         }
         else if(parseFloat(op_req_amt.value) != paidamt){
            alert("Total amount should be matched with paid amount");
            op_req_amt.style.border = '1px solid';
            op_req_amt.style.borderColor = 'red';
            return false;
         }
      }
      else if(trnscto == 'Transporter')
      {
         var tr_req_amt = document.getElementById('all_total');
         var t_organization = document.getElementById('t_organization').value.trim();
         if(pay_orgnstn != t_organization)
         {
            alert("Organisation should be matched");
            return false;
         }
         if(parseFloat(tr_req_amt.value)!= paidamt){
            alert("Requested amount should be matched with paid amount");
            tr_req_amt.style.border = '1px solid';
            tr_req_amt.style.borderColor = 'red';
            return false;
         }
      }
      else if(trnscto == 'Salary Processing')
      {
         var sp_remark = document.getElementById('sp_remarks');
         var sp_req_amt = document.getElementById('all_total');
         if(sp_remark.value == '')
         {
            alert("Provide Remark");
            sp_remark.style.border = '1px solid';
            sp_remark.style.borderColor = 'red';
            return false;
         }
         else if(parseFloat(sp_req_amt.value)!= paidamt){
            alert("Net payment should be matched with paid amount");
            sp_req_amt.style.border = '1px solid';
            sp_req_amt.style.borderColor = 'red';
            return false;
         }
      }
      else
      {
         return true;
      }
   }
</script>

