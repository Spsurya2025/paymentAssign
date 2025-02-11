<?php 
require_once('../../auth.php');
require_once('../../config.php');
require_once '../../new_header.php' ?>
<?php
$empid = $_SESSION['ERP_SESS_ID'];
$peid = $_GET['peid'];
$bnkimprt_id = $_GET['bimpid'];
$acc_id = $_GET['accid'];
$message = '';
if(isset($_POST['payasgn']))
{ 
   $msg = '1';
   $created_on = date('Y-m-d H:i:s');
   $updpeqr = "UPDATE fin_payment_entry SET pay_approval_stat='$msg',frst_apprv='$empid',frst_apprv_date='$created_on' WHERE id='$peid' AND bnkimprt_id='$bnkimprt_id'";
   if(mysqli_query($con, $updpeqr))
   {
      $updbankim = mysqli_query($con,"UPDATE fin_banking_imports SET is_pay_asgnd='1',is_pay_aprvd='1' WHERE id='$bnkimprt_id'");
      echo "<script>alert('Successfully updated')</script>";
      echo "<script>window.location.href='../bankassign/mngpayoverview.php?accid=$acc_id';</script>";
   }
   else
   {
      $message= "<div class='alert alert-danger'>Error occured: " . mysqli_error($con)."</div>";
   }
}
elseif(isset($_POST['payrej']))
{
   $msg = '3';
   $created_on = date('Y-m-d H:i:s');
   $updpeqr = "UPDATE fin_payment_entry SET pay_approval_stat='$msg',sec_apprv='$empid',sec_apprv_date='$created_on' WHERE id='$peid' AND bnkimprt_id='$bnkimprt_id'";
   if(mysqli_query($con, $updpeqr))
   {
      $updbankim = mysqli_query($con,"UPDATE fin_banking_imports SET is_pay_aprvd='3' WHERE id='$bnkimprt_id'");
      echo "<script>alert('Successfully updated')</script>";
      echo "<script>window.location.href='../bankassign/mngpayoverview.php?accid=$acc_id';</script>";
   }
   else
   {
      $message= "<div class='alert alert-danger'>Error occured: " . mysqli_error($con)."</div>";
   }
}
?>
<title><?php if(isset($_GET['bimpid']) && isset($_GET['peid'])) { echo "Update Payment Assignment"; } else if (isset($_GET['bimpid'])) { echo "Add Payment Assignment"; } ?> : Suryam Group</title>
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
      <?php if(isset($message)) { echo "<i style=color:#33D15B;>".$message."</i>"; } ?>
      <form name="form" method="post" class="forms-sample" style="margin-left: 5px;">
         <legend>
            <h5 style="color: #008787;">Uploaded Payment Details</h5>
         </legend>
         <?php
            if (isset($_GET['bimpid'])) {
               $bimpid = $_GET['bimpid'];
               $dtlsqr = mysqli_query($con, "SELECT x.*,y.* FROM `fin_banking_imports` x, `fin_statement_preview` y  WHERE x.`preview_id`=y.`id` AND x.`id`='$bimpid' AND y.`status`='1'");
               $fthimps = mysqli_fetch_object($dtlsqr);
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
                     <input type="text" class="form-control" name="preqnum" id="preqnum" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->pr_num; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="pay_orgnstn">Payment Under Organisation</label>
                     <select class="form-control" name="pay_orgnstn" id="pay_orgnstn" readonly>
                     <?php
                        if (isset($_GET['bimpid'])) {
                           $orgnid = $fthimps->orgnstn_id;
                           $orgqr = mysqli_query($con, "SELECT id,organisation FROM `prj_organisation` WHERE `id`='$orgnid'");
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
                           $bnkqr = mysqli_query($con, "SELECT id,accnm FROM `fin_bankaccount` WHERE `id`='$bnkaccid'");
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
                     <select class="form-control" name="trnscto" id="trnscto" readonly>
                        <?php
                            $sql1 = mysqli_query($con, "SELECT trnscto FROM fin_payment_entry WHERE bnkimprt_id='$bnkimprt_id' AND id='$peid'");
                            $row = mysqli_fetch_object($sql1);
                           if (isset($_GET['bimpid']) && isset($_GET['peid'])) {
                             echo "<option value='".$row->trnscto."'>".$row->trnscto."</option>";
                           }
                           ?>
                     </select>
                  </div>
               </div>

               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="payee_nm">Payee Name</label>
                     <input type="text" class="form-control" name="payee_nm" id="payee_nm" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->payee_name; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="bank_trans_date">Transaction date</label>
                     <input type="text" class="form-control" name="bank_trans_date" id="bank_trans_date" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->transac_dt; } ?>" readonly>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="bank_ref">Reference</label>
                     <input type="text" class="form-control" name="bank_ref" id="bank_ref" value="<?php if (isset($_GET['bimpid'])) { echo $fthimps->reference; } ?>" readonly>
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
               <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                  <div class="form-group">
                     <label for="bank_desc">Description</label>
                     <textarea name="" class="form-control" name="bank_desc" id="bank_desc" readonly><?php if (isset($_GET['bimpid'])) { echo $fthimps->dscrptn; } ?></textarea>
                  </div>
               </div>
               <input type="hidden" class="form-control" name="payee_nm" id="request_num" value="<?php if (isset($_GET['bimpid'])) { echo $row1->pr_num; } ?>" readonly>
            </div>
            <div id="showPay">
            </div>

            <div class="row">
               <div class="col-lg-12">
                  <div class="form-group">
                   <?php if($fthimps->is_pay_aprvd == '0') { ?>
                     <?php if($row->trnscto == 'Supplier' || $row->trnscto == 'Vendor' || $row->trnscto == 'Transporter' || $row->trnscto == 'Operator' || $row->trnscto == 'Salary Processing' || $row->trnscto == 'Expense') { ?>
                     <div style="margin-top: 15px; margin-bottom: 30px; float: right;">
                        <input type="submit" name="payasgn" id="payasgn" value="ASSIGN" class="btn btn-success mr-2" onclick="return confirm('Are you sure you want to assign?')">
                        <input type="submit" name="payrej" id="payrej" value="Reject" class="btn btn-danger mr-2" onclick="return confirm('Are you sure you want to reject?')">
                     </div>
                     <?php } ?>
                    <?php } ?>
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
<script>
   $(document).ready(function(){
      var trnsto = $("#trnscto").val();
      var request_num = $("#preqnum").val();
      var pay_req_id = <?php echo isset($row1->pay_request_id) ? $row1->pay_request_id : 'null'; ?>;
      var peid = <?php echo $peid;?>;
      if((trnsto == "Supplier")){
      $.ajax({
         url: "supplier_pay_assign/supplier_payasgn.php",
         data:{
            py_req_id: pay_req_id,
            request_num:request_num,
            peid:peid
         },
         type: 'GET',
         success: function(response) {
            var resp = $.trim(response);
            $("#showPay").html(resp); 
         }
      });  
      } 
      if((trnsto == "Vendor")){
      $.ajax({
         url: "Vendor_pay_assign/vendor_payasign.php",
         data:{
            py_req_id: pay_req_id,
            request_num:request_num,
            peid:peid
         },
         type: 'GET',
         success: function(response) {
            var resp = $.trim(response);
            $("#showPay").html(resp); 
         }
      });  
      }  
      if((trnsto == "Operator")){
      $.ajax({
         url: "operator_pay_assign/operator_payasgn.php",
         data:{
            py_req_id: pay_req_id,
            request_num:request_num,
            peid:peid
         },
         type: 'GET',
         success: function(response) {
            var resp = $.trim(response);
            $("#showPay").html(resp); 
         }
      });  
      }  
      if((trnsto == "Transporter")){
      $.ajax({
         url: "transporter_pay_assign/transport_pay_assign.php",
         data:{
            py_req_id: pay_req_id,
            request_num:request_num,
            peid:peid
         },
         type: 'GET',
         success: function(response) {
            var resp = $.trim(response);
            $("#showPay").html(resp); 
         }
      });  
      }    
      if((trnsto == "Salary Processing")){
      $.ajax({
         url: "salary_pay_assign/salary_payassign.php",
         data:{
            request_num:request_num,
            peid:peid
         },
         type: 'GET',
         success: function(response) {
            var resp = $.trim(response);
            $("#showPay").html(resp); 
         }
      });  
      }
      if((trnsto == "Expense")){
      $.ajax({
         url: "exp_pay_assign/exp_payassign.php",
         data:{
            request_num:request_num,
            peid:peid
         },
         type: 'GET',
         success: function(response) {
            var resp = $.trim(response);
            $("#showPay").html(resp); 
         }
      });  
      }
   });               
</script>
<script src="../js/metisMenu.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="../js/startmin.js"></script>