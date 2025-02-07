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
   if (strtoupper($trnsc_type) == 'DEBIT' && $result->num_rows > 0) 
   {       
      echo "<script>alert('Payment request number: $preqnum already exists!');</script>";
      echo "<script>window.history.go(-1);</script>";
   }
   else 
   {
      $insqry = mysqli_query($con, "INSERT INTO `fin_payment_entry` (`bnkimprt_id`, `statement_id`, `bankacc_id`, `preqnum`, `trnsc_type`, `payment_mode`, `orgnsn_name`, `trnscto`, `payee_nm`, `pay_assgn_stat`, `pay_approval_stat`, `status`, `frst_apprv`, `frst_apprv_date`) VALUES ('$bnkimprt_id', '$statement_id', '$bankacc_id', '$preqnum', '$trnsc_type', 'offline', '$orgnsn_name', '$trnscto', '$payee_nm', '1', '1', '1','$empid','$created_on')");   
      $pentry_last_id = mysqli_insert_id($con);  
      $pay_request_id = isset($_POST['pay_rqst_id']) ? mysqli_real_escape_string($con, $_POST['pay_rqst_id']) : 0;
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
            else if(!empty($_POST['tr_data']))
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
                  $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '$pr_numbr', '$subprj_nm', '$bms_name', '$pramnt', '0', '$pr_paid_amnt', '$trnsrsn', '$trns_rqst_amt', '$trns_paid_amnt', '1')");
                  if($splrqr)
                  {
                     echo "<script>alert('Supplier payment assign details successfully inserted')</script>";
                  } 
               }
            }
            else
            { 
                $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`) VALUES ('$pentry_last_id', '0', '$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '', '', '', '', '0', '0', '', '', '', '1')");
                if($splrqr)
                {
                    echo "<script>alert('Supplier payment assign details successfully inserted')</script>";
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
            $expns_req_id = mysqli_real_escape_string($con, $_POST['exp_payreq_id']);
            $expns_for = mysqli_real_escape_string($con, $_POST['expns_for']);
            $exp_for_empcode = mysqli_real_escape_string($con, $_POST['exp_for_empcode']);
            $prjct = mysqli_real_escape_string($con, $_POST['prjct']);
            $sub_prjct = mysqli_real_escape_string($con, $_POST['sub_prjct']);
            $bmsnm = mysqli_real_escape_string($con, $_POST['bmsnm']);
            $expns_req_amt = mysqli_real_escape_string($con, $_POST['expns_req_amt']);
            $expns_other_charges = mysqli_real_escape_string($con, $_POST['exp_other_charges']);
            $expns_other_amt = mysqli_real_escape_string($con, $_POST['exp_other_amt']);
            $expenen = mysqli_query($con, "INSERT INTO `fin_payment_entry_expense` (`payent_id`, `pay_rqst_id`, `expns_for`, `exp_for_empcode`, `prjct`, `sub_prjct`, `bmsnm`, `exp_req_amt`, `paid_exp_amt`, `status`) VALUES ('$pentry_last_id', '$expns_req_id', '$expns_for', '$exp_for_empcode', '$prjct', '$sub_prjct', '$bmsnm', '$expns_req_amt', '$paid_amnt', '1')");
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
                     <select class="form-control" name="preqnum" id="request_num" <?php echo (strtoupper($fthimps->transac_type) == 'CREDIT')? 'disabled' : ''?>>
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
                           if (strtoupper($fthimps->transac_type) == 'DEBIT') {?>
                             <option value="">--- Select Transaction To/Type ---</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Transporter">Transporter</option>
                        <option value="GST">GST</option>
                        <option value="Withdraw">Withdraw</option>
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
                           <?php } 
                           else if(strtoupper($fthimps->transac_type) == 'CREDIT'){ ?>
                           <option value="">--- Select Transaction To/Type ---</option>
                           <option value="Supplier">Supplier</option>
                           <option value="Vendor">Vendor</option>
                           <!-- <option value="Transporter">Transporter</option> -->
                           <option value="Expense">Expense</option>
                        <?php } else { ?>
                           <option value="">--- Select Transaction To/Type ---</option>
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
        $("#trnscto").change(function () {
            const transaction_to = $(this).val();
            const organisation_id = $("#pay_orgnstn").val();
            const trnsctn_typ = $("#trnsc_type").val();
            const $select = $("#request_num")[0].selectize;
            $select.clear();
            $select.clearOptions();
            $select.refreshOptions();
            $("#showPay").html('');
            $("#payment_req_id").val('');
            // Define API endpoint mappings based on transaction type
            if(trnsctn_typ.toUpperCase() == 'DEBIT')
            {
               const apiEndpoints = {
               "Supplier": "supplier_pay_assign/get_spl.php",
               "Vendor": "Vendor_pay_assign/get_ven.php",
               "Operator": "Vendor_pay_assign/get_ven.php",
               "Transporter": "transporter_pay_assign/get_tr.php",
               "Salary Processing": "salary_pay_assign/get_sal.php",
               "Expense": "exp_pay_assign/get_exp.php"
               };

               // Check if transaction_to exists in mapping
               if (!apiEndpoints[transaction_to]) {
               return;
               }
               // Fetch data dynamically
               $.ajax({
                  url: apiEndpoints[transaction_to],
                  data: { trans_to: transaction_to, organisation_id: organisation_id },
                  type: 'GET',
                  dataType: 'json',
                  success: function (response) {
                     if (response.length === 0) {
                        alert('No data available');
                        return;
                     }
                     handleResponse(response, $select, transaction_to);
                  },
                  error: function () {
                        alert('Failed to fetch data');
                  }
               });
            }
            else if(trnsctn_typ.toUpperCase() == 'CREDIT')
            {  
               const c_apiEndpoints = {
                 "Supplier": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/supplier_pay_assign/cr_supplier_payasn.php",
                 "Vendor": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/cr_vendor_payasign.php",
                 "Transporter": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/transporter_pay_assign/cr_transport_pay_assign.php",
                 "Expense": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/exp_pay_assign/cr_exp_payassign.php"
               };
               const c_data = {
                 "Supplier": {bimpid:<?php echo $_GET['bimpid'];?>,trnsctyp:trnsctn_typ},
                 "Vendor": {bimpid:<?php echo $_GET['bimpid'];?>},
                 "Transporter": {bimpid:<?php echo $_GET['bimpid'];?>},
                 "Expense": {bimpid:<?php echo $_GET['bimpid'];?>}
               };
               if (!c_apiEndpoints[transaction_to]) {
                  alert("Transaction to/type not available or not implemented");
                  return;
               }
                $.ajax({
                    url: c_apiEndpoints[transaction_to],
                    data: c_data[transaction_to],
                    type: 'GET',
                    success: function (response) {
                        var resp = $.trim(response);
                        $("#showPay").html(resp);
                    },
                    error: function () {
                        alert('Failed to fetch data');
                    }
                });
            }         
        });
        function handleResponse(response, selectizeInstance, transaction_to) {
            response.forEach(function (item) {
            let prNums = [];

            if (transaction_to === "Salary Processing" || transaction_to === "Expense") {
                prNums = [item.pr_num]; // Single value case
            } else {
                prNums = item.pr_num.split('#'); // Multiple values case
            }
            prNums.forEach(function (prNum) {
                if (prNum.trim() !== "") {
                    selectizeInstance.addOption({
                        value: JSON.stringify({ prNum: prNum, payRequestId: item.pay_request_id || '' }),
                        text: prNum
                    });
                }
            });
            });
          selectizeInstance.refreshOptions();
        }
        $("#request_num").change(function () {
          $("#showPay").html('');
          $("#preqnum").val('');
          const selectedValue = $(this).val();
          if (!selectedValue) return;
          const parsedValue = JSON.parse(selectedValue);
          const request_num = parsedValue.prNum;
          const pay_request_id = parsedValue.payRequestId || '';
          const trnsto = $("#trnscto").val();
          $("#payment_req_id").val(pay_request_id);
          $("#preqnum").val(request_num);

          // Define API endpoint mappings based on transaction type
          const apiEndpoints = {
            "Supplier": "supplier_pay_assign/supplier_payasgn.php",
            "Vendor": "Vendor_pay_assign/vendor_payasign.php",
            "Operator": "operator_pay_assign/operator_payasgn.php",
            "Transporter": "transporter_pay_assign/transport_pay_assign.php",
            "Salary Processing": "salary_pay_assign/salary_payassign.php",
            "Expense": "exp_pay_assign/exp_payassign.php"
          };

          // Check if transaction type exists in mapping
          if (!apiEndpoints[trnsto]) return;

          // Prepare data payload
          const requestData = trnsto === "Salary Processing" || trnsto === "Expense"
            ? { request_num: request_num }  // No pay_request_id for Salary Processing & Expense
            : { py_req_id: pay_request_id, request_num: request_num };

          // Perform AJAX request dynamically
          $.ajax({
            url: apiEndpoints[trnsto],
            data: requestData,
            type: 'GET',
            success: function (response) {
                $("#showPay").html($.trim(response));
            },
            error: function () {
                alert(`Failed to fetch ${trnsto.toLowerCase()} data`);
            }
          });
        });
  });
</script>
<script>
  function validForm() {
   var trnscto = document.getElementById('trnscto').value.trim();
   var request_num = document.getElementById('request_num').value.trim();
   var paidamt = parseFloat(document.getElementById('paidamt').value) || 0;
   var pay_orgnstn = document.getElementById('pay_orgnstn').value.trim();
   var trnsctn_typ = document.getElementById('trnsc_type').value;
   if (!trnscto) {
      alert('Please select transaction type');
      return false;
   }
   if(trnsctn_typ.toUpperCase()=='DEBIT')
    {
        if (!request_num) {
            alert("Please select request number");
            return false;
        }

        // Mapping transaction types to their respective total amount field IDs
        var amountFields = {
            "Supplier": "all_total",
            "Vendor": "all_total",
            "Operator": "all_total",
            "Transporter": "all_total",
            "Salary Processing": "all_total",
            "Expense": "all_total"
        };
        var errorMessages = {
            "Supplier": "Total request amount should match the paid amount",
            "Vendor": "Total request amount should match the paid amount",
            "Operator": "Total amount should match the paid amount",
            "Transporter": "Requested amount should match the paid amount",
            "Salary Processing": "Net payment should match the paid amount",
            "Expense": "Total payment should match the paid amount"
        };
        var organ_fields = {
            "Supplier": "s_organization",
            "Vendor": "v_organization",
            "Operator": "o_organization",
            "Transporter": "t_organization",
            "Salary Processing": "sal_organization",
            "Expense": "e_organization"
        };
        if (organ_fields[trnscto]) {
            var orgaField = document.getElementById(organ_fields[trnscto]);

            if (!orgaField || isNaN(orgaField.value)) {
                alert("Organisation name is missing or invalid");
                return false;
            }

            var orgaName = orgaField.value || 0;

            if (orgaName !== pay_orgnstn) {
                alert("Organisation name should be matched");
                orgaField.style.border = '1px solid red';
                return false;
            } else {
                orgaField.style.border = ''; // Reset border if valid
            }
        }
        if (amountFields[trnscto]) {
            var totalAmountField = document.getElementById(amountFields[trnscto]);

            if (!totalAmountField || isNaN(parseFloat(totalAmountField.value))) {
                alert("Total amount field is missing or invalid");
                return false;
            }

            var totalAmount = parseFloat(totalAmountField.value) || 0;

            if (totalAmount !== paidamt) {
                alert(errorMessages[trnscto]);
                totalAmountField.style.border = '1px solid red';
                return false;
            } else {
                totalAmountField.style.border = ''; // Reset border if valid
            }
        }
        
        // Additional validation for Salary Processing
        if (trnscto === "Salary Processing") {
            var sp_remark = document.getElementById("sp_remarks");
            if (!sp_remark || sp_remark.value.trim() === '') {
                alert("Provide Remark");
                sp_remark.style.border = '1px solid red';
                return false;
            } else {
                sp_remark.style.border = ''; // Reset border if valid
            }
        }

        return true;
    }
    else if(trnsctn_typ.toUpperCase()=='CREDIT')
    {
      if (trnscto === "Supplier") 
      {
         const fields = [
            { id: 'suplrnm', name: 'Supplier' },
            { id: 'prj_name', name: 'Project name' },
            { id: 'ponum', name: 'PO number' },
            { id: 'podate', name: 'PO date' },
            { id: 'poamnt', name: 'PO amount' }
         ];
         for (let field of fields) {
            let value = document.getElementById(field.id).value.trim();
            if (!value) {
               alert(`${field.name} field is required!`);
               return false;
            }
         }
         return true;
      }
      else if(trnscto === "Transporter")
      {
         const tr_fields = [
            { id:'trnsprtrnm', name: 'Transporter'},
            { id: 'prjctnm', name: 'Project name'},
            { id: 'subprjnm', name: 'Sub project name'},
            { id: 'bmsnm', name: 'Billing milestone'},
            { id: 'ponum', name: 'PO number'},
            { id: 'place_from', name: 'Place from'},
            { id: 'place_to', name: 'Place to'},
            { id: 'distance', name: 'Distance'},
            { id: 'material_nm', name: 'Material name'},
            { id: 'mtrl_weight', name: 'Material weight'},
            { id: 'service_typ', name: 'Service type'},
            { id: 'lry_model', name: 'Lorry model'},
            { id: 'dala_typ', name: 'Dala type'},
            { id: 'carrycap', name: 'Carrying capacity'},
            { id: 'totalamnt', name: 'Total amount'},
            { id: 'rateper_km', name: 'Rate per KM'},
            { id: 'rateper_kg', name: 'Rate per KG'}
         ];
         for (let tr_field of tr_fields) {
            let tr_value = document.getElementById(tr_field.id).value.trim();
            if (!tr_value) {
               alert(`${tr_field.name} field is required!`);
               return false;
            }
         }
         return true;
      }
      else if(trnscto === "Vendor")
      {
         const vr_fields = [
            { id:'vndrnm', name: 'Vendor name'},
            { id: 'prjct_name', name: 'Project name'},
            { id: 'jobodr_num', name: 'Job order number'},
            { id: 'jobodr_val', name: 'Job order value'},
            { id: 'subprjct_nm', name: 'Sub project name'},
            { id: 'bmsnm', name: 'BMS'},
            { id: 'wrk_dscrptn', name: 'Work description'},
            { id: 'subprjct_val', name: 'Sub project value'}
         ];
         for (let vr_field of vr_fields) {
            let vr_value = document.getElementById(vr_field.id).value.trim();
            if (!vr_value) {
               alert(`${vr_field.name} field is required!`);
               return false;
            }
         }
         return true;
      }
      else if(trnscto === "Expense")
      {
         const ex_fields = [
            { id:'expns_for', name: 'Expense for'},
            { id: 'exp_for_empcode', name: 'Employee Code'},
            { id: 'prjct', name: 'Project name'},
            { id: 'sub_prjct', name: 'Sub project name'},
            { id: 'bmsnm', name: 'Billing Milestone'}
         ];
         for (let ex_field of ex_fields) {
            let ex_value = document.getElementById(ex_field.id).value.trim();
            if (!ex_value) {
               alert(`${ex_field.name} field is required!`);
               return false;
            }
         }
         return true;
      }
    }
    else
    {
        alert("Payment Transaction Type is invalid")
        return false;
    }

  }

</script>

