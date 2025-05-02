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
   $acc_id = $_GET['accid'];
   $statement_id = mysqli_real_escape_string($con, $_POST['stmnt_prvw']);
   $bankacc_id = mysqli_real_escape_string($con, $_POST['bankacc_id']);
   $trnsc_type = mysqli_real_escape_string($con, $_POST['trnsc_type']);
   $orgnsn_name = mysqli_real_escape_string($con, $_POST['pay_orgnstn']);
   $trnscto = mysqli_real_escape_string($con, $_POST['trnscto']);
   $payee_nm = mysqli_real_escape_string($con, $_POST['payee_nm']);
   $paid_amnt = mysqli_real_escape_string($con, $_POST['paidamt']);
   if (strtoupper($trnsc_type) == 'DEBIT' && $trnscto == "Others")
   {
      $preqnum = mysqli_real_escape_string($con, $_POST['oth_req_num']);
   } 
   else{
      $preqnum = mysqli_real_escape_string($con, $_POST['preqnum']);
   }
   $created_on = date('Y-m-d H:i:s');
   $banimpt_id = mysqli_query($con,"SELECT id FROM fin_payment_entry WHERE bnkimprt_id = '$bnkimprt_id'");
   $sql = "SELECT * FROM fin_payment_entry WHERE preqnum = '$preqnum'";
   $result = $con->query($sql);
   $chequedetails = mysqli_query($con, "SELECT y.*,y.id FROM `chqissueentry` y JOIN chq_rqentry x ON y.reqno=x.id WHERE y.status = '1' AND x.chquniqnm = '".$preqnum."'");
   $requnum_count_details = mysqli_num_rows($chequedetails);
   // backend validation for dd payment assign
   $ddno = mysqli_real_escape_string($con, $_POST['ddno']);
   $dd_num = mysqli_query($con, "SELECT x.*,y.dd_no FROM fin_payment_entry_dd x JOIN fin_ddtls y ON x.ddno = y.id WHERE x.ddno = '$ddno'");
   $dd_count = mysqli_num_rows($dd_num);
   // end dd
   //backend validation for fd payment assign
   $fdno = mysqli_real_escape_string($con, $_POST['fdno']);
   $fd_num = mysqli_query($con, "SELECT x.*,y.fd_no FROM fin_payment_entry_fd x JOIN fin_fddtls y ON x.fdno = y.id WHERE x.fdno = '$fdno'");
   $fd_count = mysqli_num_rows($fd_num);

   // end fd
   // supplier backend validation
   if (strtoupper($trnsc_type) == 'DEBIT' && $trnscto == "Supplier") {
      $duplicate_found = 0;
      // Validate PR Data
      if (!empty($_POST['pr_data'])) {
         foreach ($_POST['pr_data'] as $id) {
            $req_num_sup = mysqli_real_escape_string($con, $_POST['req_num_sup'][$id]);
            $check_query = mysqli_query($con, "SELECT id FROM fin_payment_entry_supplier WHERE req_num = '$req_num_sup'");
            if (mysqli_num_rows($check_query) > 0) {
               echo "<script>alert('Request number $req_num_sup already assigned.');</script>";
               $duplicate_found++;
            }
         }
      }
      // Validate Transfer Data
      if (!empty($_POST['tr_data'])) {
         foreach ($_POST['tr_data'] as $id) {
            $req_num_sup_tr = mysqli_real_escape_string($con, $_POST['req_num_sup_tr'][$id]);
            $check_query = mysqli_query($con, "SELECT id FROM fin_payment_entry_supplier WHERE req_num = '$req_num_sup_tr'");
            if (mysqli_num_rows($check_query) > 0) {
               echo "<script>alert('Request number $req_num_sup_tr already assigned.');</script>";
               $duplicate_found++;
            }
         }
      }
      // Exit only if any duplicate found
      if ($duplicate_found > 0) {
          echo "<script>window.history.go(-1);</script>";
          exit;
      }
   }
   // end supplier
   if(mysqli_num_rows($banimpt_id)>0){
      echo "<script>alert('Already assigned');</script>";
      echo "<script>window.history.go(-1);</script>";
   }
   else if(strtoupper($trnsc_type) == 'DEBIT' && $trnscto == "FD" && $fd_count > 0)
   {
      $fd_fh = mysqli_fetch_object($fd_num);
      $fd_n = $fd_fh->fd_no;
      echo "<script>alert('Payment request already entered for FD number: $fd_n');</script>";
      echo "<script>window.history.go(-1);</script>";
   }
   else if(strtoupper($trnsc_type) == 'DEBIT' && $trnscto == "DD" && $dd_count > 0)
   {
      $dd_fh = mysqli_fetch_object($dd_num);
      $dd_n = $dd_fh->dd_no;
      echo "<script>alert('Payment request already entered for DD number: $dd_n');</script>";
      echo "<script>window.history.go(-1);</script>";
   }
   else if(strtoupper($trnsc_type) == 'DEBIT' && $trnscto == "Cheque" && $result->num_rows == $requnum_count_details){
      echo "<script>alert('Payment request already entered');</script>";
      echo "<script>window.history.go(-1);</script>";
   }
   else if (strtoupper($trnsc_type) == 'DEBIT' && $trnscto != "FD" && $trnscto != "Supplier" && $trnscto != "DD" && $trnscto != "Bank Transfer" && $trnscto != "Others" && $trnscto != "Cheque"  && $result->num_rows > 0) 
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
            $othr_re = mysqli_real_escape_string($con, $_POST['other_reason']);
            $othr_amt = mysqli_real_escape_string($con, $_POST['other_amt']);
            $vndrinqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_vendor` (`payent_id`, `pay_rqst_id`, `vndrnm`, `prjct_name`, `jobodr_num`, `jobodr_val`, `subprjct_nm`, `bmsnm`, `wrk_dscrptn`, `subprjct_val`,`other_charges_id`,`other_charge_amt`,`rqst_amt`, `paid_amnt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$vndrnm', '$prjct_name', '$jobodr_num', '$jobodr_val', '$subprjct_nm', '$bmsnm', '$wrk_dscrptn', '$subprjct_val','$othr_re','$othr_amt', '$req_amt', '$paid_amnt', '1')");
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
            $othr_re = mysqli_real_escape_string($con, $_POST['other_reason']);
            $othr_amt = mysqli_real_escape_string($con, $_POST['other_amt']);
            if (!empty($_POST['pr_data'])) {
               foreach ($_POST['pr_data'] as $id) {
                  $pr_numbr = mysqli_real_escape_string($con, $_POST['pr_numbr'][$id]);
                  $subprj_nm = mysqli_real_escape_string($con, $_POST['subprj_nm'][$id]);
                  $req_num_sup = mysqli_real_escape_string($con, $_POST['req_num_sup'][$id]);
                  $subpr_id = mysqli_real_escape_string($con, $_POST['subprj_id'][$id]);
                  $bms_name = mysqli_real_escape_string($con, $_POST['bms_name'][$id]);
                  $pramnt = mysqli_real_escape_string($con, $_POST['pramnt'][$id]);
                  $pr_request_amt = mysqli_real_escape_string($con, $_POST['pr_reqamt'][$id]);
                  $trnsrsn = '';
                  $pr_paid_amnt = $paid_amnt;
                  $trns_paid_amnt = '';
                  $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `req_num`,`suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`,`subprjid`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`,`other_charges_id`,`other_charge_amt`, `request_amount`, `paid_amount`) VALUES ('$pentry_last_id', '$pay_request_id', '$req_num_sup', '$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '$pr_numbr', '$subprj_nm','$subpr_id','$bms_name', '$pramnt', '$pr_request_amt', '$pr_request_amt', '$trnsrsn', '0', '$trns_paid_amnt', '1','','','$pr_request_amt','$pr_paid_amnt')");  
               }

            }
            if(!empty($_POST['tr_data']))
            {
               foreach($_POST['tr_data'] as $id)
               {
                  $pr_numbr = '';
                  $subprj_nm = '';
                  $req_num_sup_tr = mysqli_real_escape_string($con, $_POST['req_num_sup_tr'][$id]);
                  $subpr_id = '';
                  $bms_name = '';
                  $pramnt = '';
                  $trnsrsn = mysqli_real_escape_string($con, $_POST['trnsrsn'][$id]);
                  $trns_rqst_amt = mysqli_real_escape_string($con, $_POST['trreqamt'][$id]);
                  $trns_paid_amnt = $paid_amnt;
                  $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `req_num`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`,`subprjid`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`,`other_charges_id`,`other_charge_amt`, `request_amount`, `paid_amount`) VALUES ('$pentry_last_id', '$pay_request_id', '$req_num_sup_tr','$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '$pr_numbr', '$subprj_nm', '$subpr_id','$bms_name', '$pramnt', '', '', '$trnsrsn', '$trns_rqst_amt', '$trns_rqst_amt', '1','','','$trns_rqst_amt','$trns_paid_amnt')");
               }
            }
            if($othr_re != ''){
               $otherchrages = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`, `subprjid`,`bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`,`other_charges_id`,`other_charge_amt`, `request_amount`, `paid_amount`) VALUES ('$pentry_last_id', '$pay_request_id', '$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '', '', '','', '', '0', '0', '', '', '', '1','$othr_re','$othr_amt','$othr_amt','$paid_amnt')");
            }
            if(empty($_POST['pr_data']) && empty($_POST['tr_data'])){
               $splrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_supplier` (`payent_id`, `pay_rqst_id`, `req_num`, `suplrnm`, `prj_name`, `ponum`, `podate`, `poamnt`, `pr_numbr`, `subprj_nm`,`subprjid`, `bms_name`, `pramnt`, `pr_request_amt`, `pr_paid_amnt`, `trnsrsn`, `trns_rqst_amt`, `trns_paid_amnt`, `status`,`other_charges_id`,`other_charge_amt`, `request_amount`, `paid_amount`) VALUES ('$pentry_last_id', '', '','$suplrnm', '$prj_name', '$ponum', '$podate', '$poamnt', '', '', '','', '', '', '', '', '', '', '1','','','$paid_amnt','$paid_amnt')");
            }
            echo "<script>alert('Supplier payment assign details successfully inserted')</script>";

         } 
         else if ($trnscto == "Others") 
         { 
            $othrhead = mysqli_real_escape_string($con, $_POST['othrhead']);
            $linkedwith = mysqli_real_escape_string($con, $_POST['linkedwith']);
            $othr_re = mysqli_real_escape_string($con, $_POST['other_reason']);
            $othr_amt = mysqli_real_escape_string($con, $_POST['other_amt']);
            if($linkedwith== 'Indivisual'){
               $prjnm = mysqli_real_escape_string($con, $_POST['prjnm']);
               $subprjnm = mysqli_real_escape_string($con, $_POST['subprjnm']);
            }
            else{
               $prjnm = mysqli_real_escape_string($con, $_POST['prjnm_req_num']);
               $subprjnm = mysqli_real_escape_string($con, $_POST['subprjnm_req_num']); 
            }
            $paytcr = mysqli_real_escape_string($con, $_POST['paytcr']);
            $oth_req_num = mysqli_real_escape_string($con, $_POST['oth_req_num']);
            $requested_amt = mysqli_real_escape_string($con, $_POST['requested_amt']);
            $otheren = mysqli_query($con, "INSERT INTO `fin_payment_entry_others` (`payent_id`, `pay_rqst_id`, `othrhd`, `prj_name`, `sprj_name`, `particlr`,`other_charges_id`,`other_charge_amt`, `othr_req_amt`, `paid_othr_amt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$othrhead', '$prjnm', '$subprjnm', '$paytcr','$othr_re','$othr_amt', '$requested_amt','$paid_amnt','1')");
            if($otheren)
            {
               echo "<script>alert('Others payment assign details successfully inserted')</script>";
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
            $othr_re = mysqli_real_escape_string($con, $_POST['other_reason']);
            $othr_amt = mysqli_real_escape_string($con, $_POST['other_amt']);
            $trnsptqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_transporter` (`payent_id`, `pay_rqst_id`, `trnsprtrnm`, `prjctnm`, `subprjnm`, `bmsnm`, `ponum`, `place_from`, `place_to`, `distance`, `material_nm`, `mtrl_weight`, `service_typ`, `lry_model`, `dala_typ`, `carrycap`, `totalamnt`, `rateper_km`, `rateper_kg`, `adv_prcnt`, `adv_amt`, `final_amnt`,`other_charges_id`,`other_charge_amt`, `trns_req_amt`, `paidamnt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$trnsprtrnm', '$prjctnm', '$subprjnm', '$bmsnm', '$ponum', '$place_from', '$place_to', '$distance', '$material_nm', '$mtrl_weight', '$service_typ', '$lry_model', '$dala_typ', '$carrycap', '$totalamnt', '$rateper_km', '$rateper_kg', '$adv_prcnt', '$adv_amt', '$final_amnt','$othr_re','$othr_amt', '$trnsp_req_amt', '$paid_amnt', '1')");
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
            $expns_req_num = $preqnum;
            $expns_for = mysqli_real_escape_string($con, $_POST['expns_for']);
            $exp_for_empcode = mysqli_real_escape_string($con, $_POST['exp_for_empcode']);
            $prjct = mysqli_real_escape_string($con, $_POST['prjct']);
            $sub_prjct = mysqli_real_escape_string($con, $_POST['sub_prjct']);
            $bmsnm = mysqli_real_escape_string($con, $_POST['bmsnm']);
            $expns_req_amt = mysqli_real_escape_string($con, $_POST['expns_req_amt']);
            $expns_other_charges = mysqli_real_escape_string($con, $_POST['exp_other_charges']);
            $expns_other_amt = mysqli_real_escape_string($con, $_POST['exp_other_amt']);
            $expns_total_amt = mysqli_real_escape_string($con, $_POST['expns_total_amt']);
            $expenen = mysqli_query($con, "INSERT INTO `fin_payment_entry_expense` (`payent_id`, `pay_rqst_id`, `expns_for`, `exp_for_empcode`, `prjct`, `sub_prjct`, `bmsnm`, `expreqno`, `exp_req_amt`, `paid_exp_amt`,`other_charge`,`other_charge_amnt`,`total_amnt`,`status`) VALUES ('$pentry_last_id', '$expns_req_id', '$expns_for', '$exp_for_empcode', '$prjct', '$sub_prjct', '$bmsnm','$expns_req_num','$expns_req_amt', '$paid_amnt','$expns_other_charges','$expns_other_amt','$expns_total_amt', '1')");
            if($expenen)
            {
               echo "<script>alert('Expense payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Rent") 
         { // If Transaction Type is 'Rent'
            $rent_yr = mysqli_real_escape_string($con, $_POST['year']);
            $preqnum_r = mysqli_real_escape_string($con, $_POST['preqnum']);
            $rnt_month = mysqli_real_escape_string($con, $_POST['month']);
            $rent_typ = mysqli_real_escape_string($con, $_POST['type']);
            $purpose = mysqli_real_escape_string($con, $_POST['purpose']);
            if(!empty($_POST['selected_row']))
            {
               $selected_index = mysqli_real_escape_string($con, $_POST['selected_row']);
               // Escape input values
               $prjct = mysqli_real_escape_string($con, $_POST['p_id'][$selected_index]);
               $sub_prj = mysqli_real_escape_string($con, $_POST['sp_id'][$selected_index]);
               $rnt_code = mysqli_real_escape_string($con, $_POST['rnt_code'][$selected_index]);
               $rnt_pymnt_dt = mysqli_real_escape_string($con, $_POST['rnt_pymnt_dt'][$selected_index]);
               $client = mysqli_real_escape_string($con, $_POST['client'][$selected_index]);
               $rate = mysqli_real_escape_string($con, $_POST['rate'][$selected_index]);
               $rnt_dt = $created_on = date('Y-m-d H:i:s');
            }
            else
            {
               $prjct = mysqli_real_escape_string($con, $_POST['p_id']);
               $sub_prj = mysqli_real_escape_string($con, $_POST['sp_id']);
               $rnt_code = mysqli_real_escape_string($con, $_POST['rnt_code']);
               $rnt_pymnt_dt = mysqli_real_escape_string($con, $_POST['rnt_pymnt_dt']);
               $rent_req_amt = mysqli_real_escape_string($con, $_POST['rate']);
               $client = mysqli_real_escape_string($con, $_POST['client']);
               $rate = mysqli_real_escape_string($con, $_POST['rate']);
               $rnt_dt = $created_on = date('Y-m-d H:i:s');  
            }
            //Details data to payment entry table
            $rentpaymententry = mysqli_query($con, "INSERT INTO `fin_payment_entry_rent` (`payent_id`, `pay_rqst_id`, `orgnstn`, `rent_yr`, `rnt_month`, `rent_typ`, `purpose`, `rnt_code`, `rnt_dt`, `client`, `prjct`, `sub_prj`, `rent_req_amt`, `paid_rent_amt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$orgnsn_name', '$rent_yr', '$rnt_month', '$rent_typ', '$purpose', '$rnt_code', '$rnt_pymnt_dt', '$client', '$prjct', '$sub_prj', '$rent_req_amt', '$paid_amnt', '1')");
            if($rentpaymententry){
              // rent code details inserting code
              $rentpaymentid= mysqli_insert_id($con);
              $rentdetailsentry = mysqli_query($con, "INSERT INTO `fin_payment_entry_rentdetails` (`rent_id`, `rent_code`, `owner_name`, `rent_amount`, `payment_assign_date`) VALUES ('$rentpaymentid','$rnt_code','$client', '$rate', '$rnt_dt')");
              if($rentdetailsentry){
                  $currentdate = date('d-m-y');
                  $payrequestdetails = mysqli_query($con, "SELECT * FROM `fin_payment_request_rent` WHERE org_id = '$orgnsn_name' AND year ='$rent_yr' AND month ='$rnt_month' AND type ='$rent_typ' AND purpose ='$purpose' AND payment_status = '0'");
                  $getreq = mysqli_fetch_object($payrequestdetails);
                  $payreq_id = $getreq->payreq_id;
                  $rentdeatils = mysqli_query($con, "SELECT * FROM `fin_payment_request_rent_details` WHERE payreq_id = '$payreq_id'");
                  $getrent = mysqli_fetch_object($rentdeatils);
                  $rnt_code = $getrent->rnt_code;
                  // Update emi paid status
                  $updates=mysqli_query($con,"UPDATE rent_emi_details SET paid_status = '1',payment_date ='$currentdate' WHERE rent_code = '$rnt_code' AND year ='$rent_yr' AND month ='$rnt_month' AND rent_type ='$rent_typ' AND purpose ='$purpose'"); 
                  if($updates){
                    $updat=mysqli_query($con,"UPDATE fin_payment_request_rent SET payment_status = '1' WHERE org_id = '$orgnsn_name' AND year ='$rent_yr' AND month ='$rnt_month' AND type ='$rent_typ' AND purpose ='$purpose'");
                  }
              }
            }
            echo "<script>alert('Rent payment assign details successfully inserted')</script>";
         }
         else if ($trnscto == "FD") 
         { // If Transaction Type is 'FD'
            $fdno = mysqli_real_escape_string($con, $_POST['fdno']);
            $fdpurpose = mysqli_real_escape_string($con, $_POST['fdpurpose']);
            $fdmessage = mysqli_real_escape_string($con, $_POST['fdmessage']);
            $fd_rqst_amt = mysqli_real_escape_string($con, $_POST['fd_rqst_amt']);
            $prj_name = mysqli_real_escape_string($con, $_POST['prj_name']);
            $sprj_name = mysqli_real_escape_string($con, $_POST['sprj_name']);
            $empsqfd = mysqli_query($con, "INSERT INTO `fin_payment_entry_fd` (`payent_id`, `pay_rqst_id`, `req_no`, `req_by`, `empcode`,`prj_name`,`sprj_name`,`fdno`, `fdpurpose`, `fd_message`, `fd_rqst_amt`, `paid_amnt`, `status`) VALUES ('$pentry_last_id', '0', '', '', '','$prj_name','$sprj_name', '$fdno', '$fdpurpose', '$fdmessage', '$fd_rqst_amt', '$paid_amnt', '1')");
            if($empsqfd)
            {
               echo "<script>alert('FD payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Collection") 
         { // If Transaction Type is 'Collection'
            $debtor_typ = mysqli_real_escape_string($con, $_POST['debtor_typ']);
            $clientnm = mysqli_real_escape_string($con, $_POST['clientnm']);
            $transaction_for = mysqli_real_escape_string($con, $_POST['transaction_for']);
            $unit_nmid = mysqli_real_escape_string($con, $_POST['unit_nmid']);
            $prj_name = mysqli_real_escape_string($con, $_POST['prj_name']);
            $sbprjctnm = mysqli_real_escape_string($con, $_POST['sbprjctnm']);
            $col_requested_amt = mysqli_real_escape_string($con, $_POST['col_requested_amt']);
            $remark = mysqli_real_escape_string($con, $_POST['remark']);
            $colldqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_collection` (`payent_id`, `pay_rqst_id`, `debtor_typ`, `clientnm`, `transaction_for`, `unit_nmid`, `prj_id`, `subprj_id`, `col_req_amt`, `paid_col_amt`,`remarks`,`status`, `created_date`) VALUES ('$pentry_last_id', '$pay_request_id', '$debtor_typ', '$clientnm', '$transaction_for', '$unit_nmid', '$prj_name','$sbprjctnm','$col_requested_amt', '$paid_amnt','$remark', '1', '$created_on')");
            if($colldqr)
            {
               echo "<script>alert('Collection payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "GST") 
         { // If Transaction Type is 'GST'
            $organisation = $orgnsn_name;
            $state_nm = mysqli_real_escape_string($con, $_POST['statenm']);
            $gstnum = mysqli_real_escape_string($con, $_POST['gstno']);
            $fromdate = mysqli_real_escape_string($con, $_POST['fromdate']);
            $todate = mysqli_real_escape_string($con, $_POST['todate']);
            $reqamt = mysqli_real_escape_string($con, $_POST['reqamt']);
            $gsttqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_gst` (`payent_id`, `pay_rqst_id`, `organisation`, `state_nm`, `gstnum`, `gst_req_amt`, `fromdate`, `todate`, `paid_gst_amt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$organisation', '$state_nm', '$gstnum', '$reqamt', '$fromdate', '$todate', '$paid_amnt', '1')");
            if($gsttqr)
            {
               echo "<script>alert('GST payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Withdraw") 
         { // If Transaction Type is 'Withdraw'
            $wdrawer_nm = mysqli_real_escape_string($con, $_POST['drwrnm']);
            $reqamt = mysqli_real_escape_string($con, $_POST['reqamt']);
            $ownrwdqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_owner_withdraw` (`payent_id`, `pay_rqst_id`, `wdrawer_nm`, `withdrw_req_amt`, `paid_wd_amt`, `status`) VALUES ('$pentry_last_id', '$pay_request_id', '$wdrawer_nm', '$reqamt', '$paid_amnt', '1')");
            if($ownrwdqr)
            {
               echo "<script>alert('Withdraw payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Bank Transfer") 
         { // If Transaction Type is 'Bank Transfer'
            $org_nm = mysqli_real_escape_string($con, $_POST['org_nm']);
            $bnkaccnt = mysqli_real_escape_string($con, $_POST['bnkaccnt']);
            $bnktrn_remarks = mysqli_real_escape_string($con, $_POST['bnktrn_remarks']);             
            $banktrqr = mysqli_query($con, "INSERT INTO `fin_payment_entry_bank_trans` (`payent_id`, `org_nm`, `bnkaccnt`,`paid_amnt`, `bnktrn_remarks`, `created_by`) VALUES ('$pentry_last_id', '$org_nm', '$bnkaccnt', '$paid_amnt', '$bnktrn_remarks', '$empid')");
            if($banktrqr)
            {
               echo "<script>alert('Bank transfer payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Cheque") 
         { // If Transaction Type is 'Cheque'
            $chqno = mysqli_real_escape_string($con, $_POST['chqno']);
            $chqpurpose = mysqli_real_escape_string($con, $_POST['chqpurpose']);
            $chqclient = mysqli_real_escape_string($con, $_POST['chqclient']);
            $chqmessage = mysqli_real_escape_string($con, $_POST['chqmessage']);
            $chq_rqst_amt = mysqli_real_escape_string($con, $_POST['chq_rqst_amt']);
            $req_by = mysqli_real_escape_string($con, $_POST['req_by']);
            $payrqstid = mysqli_real_escape_string($con, $_POST['payrqstid']);
      
            $empsqchq = mysqli_query($con, "INSERT INTO `fin_payment_entry_chq` (`payent_id`, `pay_rqst_id`, `req_no`, `req_by`, `empcode`, `chqno`, `purpose`, `chqclient`, `chqmessage`, `chq_rqst_amt`, `paid_amnt`, `status`) VALUES ('$pentry_last_id', '$payrqstid', '$preqnum', '$req_by', '', '$chqno', '$chqpurpose', '$chqclient', '$chqmessage', '$chq_rqst_amt', '$paid_amnt', '1')");
            if($empsqchq)
            {
               echo "<script>alert('Cheque payment assign details successfully inserted')</script>";
            }
         }
         else if ($trnscto == "Asset Finance") 
         { // If Transaction Type is 'Asset Finance'
            $benif_acc = mysqli_real_escape_string($con, $_POST['benif_acc']);
            $af_message = mysqli_real_escape_string($con, $_POST['af_message']);
            $af_req_id = mysqli_real_escape_string($con, $_POST['af_req_id']);
            $af_purpose = mysqli_real_escape_string($con, $_POST['af_purpose']);
            $af_request_amount = mysqli_real_escape_string($con, $_POST['af_request_amount']);
            $af_finalid = $preqnum;           
            $prj_id = mysqli_real_escape_string($con, $_POST['prj_name']);
            $sprj_id = mysqli_real_escape_string($con, $_POST['sbprjctnm']);
            $assetfiquery = mysqli_query($con, "INSERT INTO `fin_payment_entry_asset_fin` (`payent_id`, `af_req_id`, `name_id`, `afl_amount`, `purpose`, `message`, `astfinl_id`,`prj_id`,`sprj_id`,`status`) VALUES ('$pentry_last_id', '$af_req_id', '$benif_acc', '$paid_amnt', '$af_purpose', '$af_message', '$af_finalid','$prj_id','$sprj_id','1')");
            if($assetfiquery)
            {
               echo "<script>alert('Asset finance payment assign details successfully inserted')</script>"; 
            }
         }
         else if ($trnscto == "DD") 
         { // If Transaction Type is 'DD'
            $ddno = mysqli_real_escape_string($con, $_POST['ddno']);
            $ddpurpose = mysqli_real_escape_string($con, $_POST['ddpurpose']);
            $ddexprsn = mysqli_real_escape_string($con, $_POST['ddexprsn']);
            $ddbenificiary = mysqli_real_escape_string($con, $_POST['ddbenificiary']);
            $ddprjname = mysqli_real_escape_string($con, $_POST['prj_name']);
            $ddsubprjname = mysqli_real_escape_string($con, $_POST['sprj_name']);
            $ddmessage = mysqli_real_escape_string($con, $_POST['ddmessage']);
            $ddreqamt = mysqli_real_escape_string($con, $_POST['dd_rqst_amt']);
            $ddothid = mysqli_real_escape_string($con, $_POST['other_reason']);
            $ddothamt = mysqli_real_escape_string($con, $_POST['other_amt']);
            $ddentryqry = mysqli_query($con, "INSERT INTO `fin_payment_entry_dd` (`payent_id`, `pay_rqst_id`, `req_no`, `req_by`, `empcode`, `ddno`, `prj_id`, `sprj_id`, `purpose`, `exprsn`, `benificiary`, `dd_message`, `dd_rqst_amt`, `other_charges_id`, `other_charge_amt`, `paid_amnt`, `status`) VALUES ('$pentry_last_id', '0', '', '', '', '$ddno','$ddprjname','$ddsubprjname', '$ddpurpose', '$ddexprsn', '$ddbenificiary', '$ddmessage', '$ddreqamt','$ddothid','$ddothamt', '$paid_amnt', '1')");
            if($ddentryqry)
            {
               echo "<script>alert('DD payment assign details successfully inserted')</script>"; 
            }
         }
         else if ($trnscto == "Loan Assignment") { // If Transaction Type is 'Loan Assignment'
            $typeid = mysqli_real_escape_string($con, $_POST['typeid']);
            $loanid = mysqli_real_escape_string($con, $_POST['refno']);
            $account_no =  mysqli_real_escape_string($con, $_POST['account_no']);
            $nbfc_name = mysqli_real_escape_string($con, $_POST['nbfcname']);
        
            $other_crg_emi = mysqli_real_escape_string($con, $_POST['other_crg_emi']);
            $other_amt_emi = mysqli_real_escape_string($con, $_POST['other_amt_emi']);
            $total_amt = mysqli_real_escape_string($con, $_POST['total_amt']);
            $chkeminumb = count($_POST['chkemi']);
              for($i=0; $i<$chkeminumb; $i++)  
              {  
                $chkemi = $_POST['chkemi'][$i];
                $prexpld = explode("/", $chkemi);
                $loan_id = $prexpld[0];
                $emi_dt = $prexpld[1];
                $emi_amt = $prexpld[2];
                $emi_interest = $prexpld[3];
                $emi_principal = $prexpld[4];
                $emi_total = $prexpld[5];
                $emi_outstanding = $prexpld[6];
        
                $emi_entry = mysqli_query($con,"INSERT INTO `fin_payment_entry_term_loan`(`payent_id`,`acc_no`,`loan_id`,`emi_dt`,`emi_amt`,`emi_interest`,`emi_principal`,`emi_total`,`emi_outstanding`, `paid_status`,`paid_dt`) VALUES ('$pentry_last_id','$account_no','$loan_id','$emi_dt', '$emi_amt', '$emi_interest', '$emi_principal', '$emi_total', '$emi_outstanding','1','$created_on')");
                if(!$emi_entry){

                }
              } 
              $oth_entry = mysqli_query($con,"INSERT INTO `fin_payment_entry_term_loan_oth`(`payent_id`,`loan_id`,`pay_type`,`tot_amt`,`oth_char`,`othr_amt`) VALUES ('$pentry_last_id','$loanid','EMI','$total_amt','$other_crg_emi','$other_amt_emi')");
        
              
              echo "<script>alert('Term loan details successfully inserted')</script>"; 
         }
        echo "<script>window.location.href='../bankassign/mngpayoverview.php?accid=$acc_id';</script>";
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
                     <label for="trnscto">Transaction To/Type</label>
                     <select class="form-control" name="trnscto" id="trnscto">
                     <?php
                           if (strtoupper($fthimps->transac_type) == 'DEBIT') {?>
                             <option value="">--- Select Transaction To/Type ---</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Transporter">Transporter</option>
                        <option value="Expense">Expense</option>
                        <option value="Operator">Operator Payment</option>
                        <option value="Salary Processing">Salary Processing</option>
                        <option value="Others">Others</option>
                        <option value="Collection">Collection</option>
                        <option value="Rent">Rent</option>
                        <option value="FD">FD</option>
                        <option value="GST">GST</option>
                        <option value="Withdraw">Withdraw</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Asset Finance">Asset Finance</option>
                        <option value="DD">DD</option>
                        <option value="Loan Assignment">Loan Assignment</option>
                        <!--<option value="Salary Advance">Salary Advance</option>-->
                        <!--<option value="Loan Advance">Employee Loan Advance</option>-->
                        <!--<option value="LC Processing">LC Processing</option>-->
                           <?php } 
                           else if(strtoupper($fthimps->transac_type) == 'CREDIT'){ ?>
                           <option value="">--- Select Transaction To/Type ---</option>
                           <option value="Supplier">Supplier</option>
                           <option value="Vendor">Vendor</option>
                           <option value="Expense">Expense</option>
                           <option value="Operator">Operator Payment</option>
                           <option value="Transporter">Transporter</option>
                           <option value="Salary Processing">Salary Processing</option>
                           <option value="Others">Others</option>
                           <option value="Collection">Collection</option>
                           <option value="GST">GST</option>
                           <option value="Withdraw">Withdraw</option>
                           <option value="Rent">Rent</option>
                           <option value="FD">FD</option>
                           <option value="DD">DD</option>
                           <option value="Bank Transfer">Bank Transfer</option>
                           <option value="Asset Finance">Asset Finance</option>
                        <?php } else { ?>
                           <option value="">--- Select Transaction To/Type ---</option>
                           <?php } ?>
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
                     <label for="preqnum">Payment Request No.</label>
                     <select class="form-control" name="preqnum1" id="request_num" <?php echo (strtoupper($fthimps->transac_type) == 'CREDIT')? 'disabled' : ''?>>
                        <option value="">---Select payment Request no.---</option>
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
            <?php if($fthimps->is_pay_aprvd == '0') { ?>
            <div class="row">
               <div class="col-lg-12">
                  <div class="form-group">
                     <div style="margin-top: 15px; margin-bottom: 30px; float: right;">
                        <input type="submit" name="payasgn" id="payasgn" value="ASSIGN" class="btn btn-success mr-2" >
                     </div>
                  </div>
               </div>
            </div>
            <?php }?>
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
            $("#payasgn").prop("disabled", true); // Disable submit button
            // Define API endpoint mappings based on transaction type
            if(trnsctn_typ.toUpperCase() == 'DEBIT')
            {
               const apiEndpoints = {
               "Supplier": "supplier_pay_assign/get_spl.php",
               "Vendor": "Vendor_pay_assign/get_ven.php",
               "Operator": "operator_pay_assign/get_opr.php",
               "Transporter": "transporter_pay_assign/get_tr.php",
               "Salary Processing": "salary_pay_assign/get_sal.php",
               "Others": "other_pay_assign/others_payasn.php",
               "Expense": "exp_pay_assign/get_exp.php",
               "Rent": "rent_pay_assign/get_rent.php",
               "FD": "fd_pay_assign/fd_payassign.php",
               "GST": "gst_pay_assign/get_gst.php",
               "Withdraw": "withdw_pay_assign/get_with.php",
               "Collection": "colctn_pay_assign/get_col.php",
               "Bank Transfer": "banktr_pay_assign/banktr_payassign.php",
               "Cheque": "cheque_pay_assign/che_exp.php",
               "Asset Finance": "asset_finance/get_req.php",
               "DD": "dd_pay_assign/dd_payassign.php",
               "Loan Assignment": "loanass_pay_assign/get_loan.php"
               };

               // Check if transaction_to exists in mapping
               if (!apiEndpoints[transaction_to]) {
               return;
               }
               let dataType = transaction_to === "FD" || transaction_to === "DD" || transaction_to === "Bank Transfer" || transaction_to === "Others"  ? "html" : "json";
               // Fetch data dynamically
               $.ajax({
                  url: apiEndpoints[transaction_to],
                  data: { trans_to: transaction_to, organisation_id: organisation_id },
                  type: 'GET',
                  dataType: dataType,
                  success: function (response) {
                    if (transaction_to === "FD" || transaction_to === "DD" || transaction_to === "Bank Transfer" || transaction_to === "Others") 
                     {
                        var resp = $.trim(response);
                        $("#showPay").html(resp);
                     } else 
                     {
                        if (response.length === 0) 
                        {
                           alert('No data available');
                           return;
                        }
                        
                        handleResponse(response, $select, transaction_to);
                     }
                     $("#payasgn").prop("disabled", false); // Enable submit button after success
                  },
                  error: function () {
                        alert('Failed to fetch data');
                        $("#payasgn").prop("disabled", true); // Disable submit button after fail
                  }
               });
            }
            else if(trnsctn_typ.toUpperCase() == 'CREDIT')
            {  
               const c_apiEndpoints = {
                 "Supplier": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/supplier_pay_assign/cr_supplier_payasn.php",
                 "Vendor": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/cr_vendor_payasign.php",
                 "Transporter": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/transporter_pay_assign/cr_transport_pay_assign.php",
                 "Expense": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/exp_pay_assign/cr_exp_payassign.php",
                 "Salary Processing": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/salary_pay_assign/cr_salary_payassign.php",
                 "Others": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/cr_others_payasn.php",
                 "Operator": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/operator_pay_assign/cr_operator_payasgn.php",
                 "Rent": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/rent_pay_assign/cr_rent_payassign.php",
                 "FD": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/fd_pay_assign/cr_fd_payassign.php",
                 "Collection": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/colctn_pay_assign/cr_col_payassign.php",
                 "GST": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/gst_pay_assign/cr_gst_payassign.php",
                 "Withdraw": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/withdw_pay_assign/cr_withdrw_payassign.php",
                 "Bank Transfer": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/banktr_pay_assign/banktr_payassign.php",
                 "Asset Finance": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/asset_finance/cr_pay_assign.php",
                 "DD": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/dd_pay_assign/cr_dd_payassign.php"
               };
               const c_data = {
                 "Supplier": {bimpid:<?php echo $_GET['bimpid'];?>,trnsctyp:trnsctn_typ},
                 "Vendor": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Transporter": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Expense": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Salary Processing": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Others": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Operator": {bimpid:<?php echo $_GET['bimpid'];?>},
                 "Rent": {bimpid:<?php echo $_GET['bimpid'];?>},
                 "FD": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Collection": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "GST": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "Withdraw": {bimpid:<?php echo $_GET['bimpid'];?>},
                 "Bank Transfer": {bimpid:<?php echo $_GET['bimpid'];?>},
                 "Asset Finance": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>},
                 "DD": {bimpid:<?php echo $_GET['bimpid'];?>,org_id:<?php echo $fthorg->id;?>}
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
                        $("#payasgn").prop("disabled", false); // Enable submit button after success
                    },
                    error: function () {
                        alert('Failed to fetch data');
                        $("#payasgn").prop("disabled", true); // disable submit button after fail
                    }
                });
            }         
        });
        function handleResponse(response, selectizeInstance, transaction_to) {
            response.forEach(function (item) {
            let prNums = [];

            if (transaction_to === "Salary Processing" || transaction_to === "Expense" || transaction_to === "Supplier") {
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
            "Others": "other_pay_assign/others_payasn.php",
            "Expense": "exp_pay_assign/exp_payassign.php",
            "Collection": "colctn_pay_assign/col_payassign.php",
            "Rent": "rent_pay_assign/rent_payassign.php",
            "GST": "gst_pay_assign/gst_payassign.php",
            "Withdraw": "withdw_pay_assign/withdrw_payassign.php",
            "Cheque": "cheque_pay_assign/che_payassign.php",
            "Asset Finance": "asset_finance/pay_assign.php",
            "Loan Assignment": "loanass_pay_assign/loan_payassign.php"
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
                $("#payasgn").prop("disabled", false); // Enable submit button after success
            },
            error: function () {
                alert(`Failed to fetch ${trnsto.toLowerCase()} data`);
                $("#payasgn").prop("disabled", true); // Disable submit button after fail
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
        if (!request_num && trnscto !== "FD" && trnscto !== "Bank Transfer" && trnscto !== "Others" && trnscto !== "DD") {
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
            "Expense": "all_total",
            "Rent": "rate_request_amount",
            "FD": "amount",
            "Collection": "col_requested_amt",
            "GST": "reqamt",
            "Withdraw": "reqamt",
            "Cheque" : "chqrqamnt",
            "Asset Finance": "requested_amt",
            "DD": "amount",
            "Loan Assignment" : "total_amt_emi"
        };
        var errorMessages = {
            "Supplier": "Total request amount should match the paid amount",
            "Vendor": "Total request amount should match the paid amount",
            "Operator": "Total amount should match the paid amount",
            "Transporter": "Requested amount should match the paid amount",
            "Salary Processing": "Net payment should match the paid amount",
            "Expense": "Total payment should match the paid amount",
            "Rent": "Rent rate must match the paid amount",
            "FD": "FD amount must match the paid amount",
            "Collection": "Requested amount must match the paid amount",
            "GST": "Amount must match the paid amount",
            "Withdraw": "Request amount must match the paid amount",
            "Cheque" : "Request amount must match the paid amount",
            "Asset Finance": "Request amount must match the paid amount",
            "DD": "Total amount must match the paid amount",
            "Loan Assignment" : "Total amount must match the paid amount"
        };
        var organ_fields = {
            "Supplier": "s_organization",
            "Vendor": "v_organization",
            "Operator": "o_organization",
            "Transporter": "t_organization",
            "Salary Processing": "sal_organization",
            "Others": "ot_organization",
            "Expense": "e_organization",
            "Collection": "co_organization",
            "Rent": "re_organization",
            "GST": "gst_organization",
            "Withdraw": "w_organization",
            "Cheque": "ch_organization",
            "Asset Finance": "asfi_organization",
            "Loan Assignment": "loan_organization"
        };
        // Additional validation 
        if (trnscto === "FD") 
         {
            const fd_fields = [
            { id:'fdno', name: 'FD no.'},
            { id: 'prjctnm', name: 'Project name'},
            { id: 'sbprjctnm', name: 'Sub project name'},
            { id: 'purpose', name: 'Purpose'},
            { id: 'message', name: 'Message'}
           ];
            for (let fd_field of fd_fields) {
               let fd_value = document.getElementById(fd_field.id).value.trim();
               if (!fd_value) {
                  alert(`${fd_field.name} field is required!`);
                  return false;
               }
            }
         }
         if (trnscto === "Collection") 
         {
            let prjctnm = document.getElementById("prjctnm").value;
            let sbprjctnm = document.getElementById("sbprjctnm").value;
            let remark = document.getElementById("remark").value;
            if(prjctnm == ""){
               alert("Please enter project");
               $("#prjctnm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
               $("#prjctnm").siblings(".select2-container").find(".select2-selection").focus();
               return false;
            }else{
               $("#prjctnm").siblings(".select2-container").find(".select2-selection").css("border", "");
            }
            if(sbprjctnm == ""){
               alert("Please enter sub project");
               $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
               $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").focus();
               return false;
            }else{
               $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").css("border", "");
            }
            if(remark == ""){
               alert("Please enter remark");
               $("#remark").css("border", "1px solid #ec1313");
               $("#remark").focus();
               return false;
            }else{
               $("#remark").css("border", ""); 
            }
         }
        if (trnscto === "Bank Transfer") 
        {
            const bank_fields = [
            { id: 'org_nm', name: 'Organization name'},
            { id: 'bnkaccnt', name: 'Bank Allias Name'},
            { id: 'bnktrn_remarks', name: 'Remarks'}
            ];
            for (let bank_field of bank_fields) {
               let bank_value = document.getElementById(bank_field.id).value.trim();
               if (!bank_value) {
                  alert(`${bank_field.name} field is required!`);
                  return false;
               }
            }
         }
         if (trnscto === "Cheque") 
         {
            const ch_fields = [
            { id: 'chqno', name: 'Cheque No'},
            { id: 'chqmessage', name: 'Message'}
            ];
            for (let ch_field of ch_fields) {
               let ch_value = document.getElementById(ch_field.id).value.trim();
               let ch_id =  document.getElementById(ch_field.id);
               if (!ch_value) {
                  alert(`${ch_field.name} field is required!`);
                  ch_id.style.border = '1px solid red';
                  return false;
               }
               else{
                  ch_id.style.border = ''; // Reset border if valid
               }
            }
         }
         if (trnscto === "Asset Finance") 
         {
            const ass_fields = [
            { id: 'prjctnm', name: 'Project name'},
            { id: 'sbprjctnm', name: 'Sub project name'}
           ];
            for (let ass_field of ass_fields) {
               let ass_value = document.getElementById(ass_field.id).value.trim();
               if (!ass_value) {
                  alert(`${ass_field.name} field is required!`);
                  $("#"+ass_field.id).siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313"); // Apply border to Select2 container
                  $("#"+ass_field.id).siblings(".select2-container").find(".select2-selection").focus();
                  return false;
               }
               else {
                  $("#"+ass_field.id).siblings(".select2-container").find(".select2-selection").css("border", "");
               }
            }
         }
         if (trnscto === "Others") 
         {
            let head = document.getElementById('othrhead').value.trim();
            let linkwith = document.getElementById('linkedwith').value.trim()
            if(!head)
            {
               alert('Please select head');
               $("#othrhead").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313"); // Apply border to Select2 container
               $("#othrhead").siblings(".select2-container").find(".select2-selection").focus();
               return false;
            }
            else
            {
               $("#othrhead").siblings(".select2-container").find(".select2-selection").css("border", ""); 
            }
            if(linkwith == 'Indivisual')
            {
               let prjctnm = document.getElementById('prjctnm').value.trim();
               let sbprjctnm = document.getElementById('sbprjctnm').value.trim();
               if(!prjctnm){
                  alert('Please select project')
                  $("#prjctnm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313"); // Apply border to Select2 container
                  $("#prjctnm").siblings(".select2-container").find(".select2-selection").focus();
                  return false;
               }else{
                  $("#prjctnm").siblings(".select2-container").find(".select2-selection").css("border", ""); 
               }
               if(!sbprjctnm){
                  alert('Please select sub project')
                  $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313"); // Apply border to Select2 container
                  $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").focus();
                  return false;
               }else{
                  $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").css("border", ""); 
               }
            }else{
               let otherReason = document.getElementById("othres").value;
               let otherAmount = document.getElementById("oth_amount").value.trim();
               if (otherReason !== "" && otherAmount === "") {
                  alert("Please enter Other Charges Amount if you selected an Other Reason.");
                  $("#oth_amount").css("border", "1px solid #ec1313");
                  $("#oth_amount").focus();
                  return false; // Prevent form submission
               }
               else{
                  $("#oth_amount").css("border", "");
               }
               let request_num_dr = document.getElementById('request_num_dr').value.trim();
               if(!request_num_dr){
                  alert('Please request number')
                  $("#request_num_dr").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313"); // Apply border to Select2 container
                  $("#request_num_dr").siblings(".select2-container").find(".select2-selection").focus();
                  return false;
               }else{
                  $("#request_num_dr").siblings(".select2-container").find(".select2-selection").css("border", ""); 
               }
               // Compare total amount with paid amount
               let total_amt_oth = parseFloat(document.getElementById('requested_amt').value) || 0;
               let totalAmount = Number.isFinite(total_amt_oth) && total_amt_oth % 1 !== 0 ? Math.trunc(total_amt_oth) : total_amt_oth;
               let paidAmount = Number.isFinite(paidamt) && paidamt % 1 !== 0 ? Math.trunc(paidamt) : paidamt;
               if (totalAmount !== paidAmount) {
                  alert("Requested Amount and Paid Amount must be the same!");
                  $("#requested_amt").css("border", "1px solid #ec1313"); // Apply border to Select2 container
                  $("#requested_amt").focus();
                  return false;
               }
               else{
                  $("#requested_amt").css("border", ""); 
               }
            }

         }
         if(trnscto === "Supplier" || trnscto === "Vendor" || trnscto === "Transporter" || trnscto === "DD"  || trnscto === "Loan Assignment")
         {
            let otherReason = document.getElementById("othres").value;
            let otherAmount = document.getElementById("oth_amount").value.trim();
            if (otherReason !== "" && otherAmount === "") {
               alert("Please enter Other Charges Amount if you selected an Other Reason.");
               $("#oth_amount").css("border", "1px solid #ec1313");
               $("#oth_amount").focus();
               return false; // Prevent form submission
            }
            else{
               $("#oth_amount").css("border", "");
            }
         }
         if (trnscto === "DD") 
         {
            const dd_fields = [
            { id:'ddno', name: 'DD no.'},
            { id: 'prjctnm', name: 'Project name'},
            { id: 'sbprjctnm', name: 'Sub project name'},
            { id: 'purpose', name: 'Purpose'},
            { id: 'ddexprsn', name: 'Expense Reasons'},
            { id: 'ddbenificiary', name: 'Benificiary'},
            { id: 'message', name: 'Message'}
           ];
            for (let dd_field of dd_fields) {
               let dd_value = document.getElementById(dd_field.id).value.trim();
               if (!dd_value) {
                  alert(`${dd_field.name} field is required!`);
                  return false;
               }
            }
         }
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
            var totalAmountValue = parseFloat(totalAmountField.value) || 0;
            var totalAmount = Number.isFinite(totalAmountValue) && totalAmountValue % 1 !== 0 ? Math.trunc(totalAmountValue) : totalAmountValue;
            var paidAmount = Number.isFinite(paidamt) && paidamt % 1 !== 0 ? Math.trunc(paidamt) : paidamt;
            if (totalAmount !== paidAmount) {
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
      if(trnscto === "FD" || trnscto === "DD")
      {
         let otherReason = document.getElementById("othres").value;
         let otherAmount = document.getElementById("oth_amount").value.trim();
         if (otherReason !== "" && otherAmount === "") {
            alert("Please enter Other Charges Amount if you selected an Other Reason.");
            $("#oth_amount").css("border", "1px solid #ec1313");
            $("#oth_amount").focus();
            return false; // Prevent form submission
         }
         else{
            $("#oth_amount").css("border", "");
         }
      }
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
            { id: 'ponum', name: 'PO number'}
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
      else if(trnscto === "Others")
      {
         const oth_fields = [
            { id:'othrhead', name: 'Head'},
            { id: 'ptcrt', name: 'Payment to be Credit to'},
            { id: 'prjctnm', name: 'Project Name'},
            { id: 'sbprjctnm', name: 'Sub Project Name'}
         ];
         for (let oth_field of oth_fields) {
            let oth_value = document.getElementById(oth_field.id).value.trim();
            if (!oth_value) {
               alert(`${oth_field.name} field is required!`);
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
      else if(trnscto === "Operator")
      {
         const op_fields = [
            { id:'op_id', name: 'Operator Name'},
            { id: 'rate', name: 'Rate'},
            { id: 'month', name: 'Month'},
            { id: 'accnum', name: 'Account Number'},
            { id: 'total_amt', name: 'Total Amount'}
         ];
         for (let op_field of op_fields) {
            let op_value = document.getElementById(op_field.id).value.trim();
            if (!op_value) {
               alert(`${op_field.name} field is required!`);
               return false;
            }
         }
         // Compare total amount with paid amount
         let total_amt = parseFloat(document.getElementById('total_amt').value) || 0;
         let totalAmount = Number.isFinite(total_amt) && total_amt % 1 !== 0 ? Math.trunc(total_amt) : total_amt;
         let paidAmount = Number.isFinite(paidamt) && paidamt % 1 !== 0 ? Math.trunc(paidamt) : paidamt;
         if (totalAmount !== paidAmount) {
            alert("Total Amount and Paid Amount must be the same!");
            return false;
         }
         return true;
      }
      else if(trnscto === "Salary Processing")
      {
         const sp_fields = [
            { id:'benif_acc', name: 'Benificiary A/c'},
            { id: 'location', name: 'Location name'},
            { id: 'year', name: 'Year'},
            { id: 'month', name: 'Month'},
            { id: 'sp_remarks', name: 'Remark'}
         ];
         for (let sp_field of sp_fields) {
            let sp_value = document.getElementById(sp_field.id).value.trim();
            if (!sp_value) {
               alert(`${sp_field.name} field is required!`);
               return false;
            }
         }
         return true;
      }
      else if (trnscto === "Collection") 
      {
         let dbtr_typ = document.getElementById("dbtr_typ").value;
         let client_nm = document.getElementById("client_nm").value;
         let prjctnm = document.getElementById("prjctnm").value;
         let sbprjctnm = document.getElementById("sbprjctnm").value;
         let remark = document.getElementById("remark").value;

         if(dbtr_typ == ""){
            alert("Please enter debtor type");
            $("#dbtr_typ").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
            $("#dbtr_typ").siblings(".select2-container").find(".select2-selection").focus();
            return false;
         }else{
            $("#dbtr_typ").siblings(".select2-container").find(".select2-selection").css("border", "");
         }
         if(client_nm == ""){
            alert("Please enter client name");
            $("#client_nm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
            $("#client_nm").siblings(".select2-container").find(".select2-selection").focus();
            return false;
         }else{
            $("#client_nm").siblings(".select2-container").find(".select2-selection").css("border", "");
         }
         if(dbtr_typ == '12'){
            let transaction_for = document.getElementById("transaction_for").value;
            if(transaction_for == ""){
               alert("Please enter transaction for");
               $("#transaction_for").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
               $("#transaction_for").siblings(".select2-container").find(".select2-selection").focus();
               return false;
            }else{
               $("#transaction_for").siblings(".select2-container").find(".select2-selection").css("border", "");
            }
         }
         if(prjctnm == ""){
            alert("Please enter project");
            $("#prjctnm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
            $("#prjctnm").siblings(".select2-container").find(".select2-selection").focus();
            return false;
         }else{
            $("#prjctnm").siblings(".select2-container").find(".select2-selection").css("border", "");
         }
         if(sbprjctnm == ""){
            alert("Please enter sub project");
            $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
            $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").focus();
            return false;
         }else{
            $("#sbprjctnm").siblings(".select2-container").find(".select2-selection").css("border", "");
         }
         if(transaction_for == 'RESCO'){
            let unit = document.getElementById("unit_nmid").value;
            if(unit ==""){
               alert("Please enter unit name");
               $("#unit_nmid").siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313");
               $("#unit_nmid").siblings(".select2-container").find(".select2-selection").focus();
               return false;
            }else{
               $("#unit_nmid").siblings(".select2-container").find(".select2-selection").css("border", "");
            }
         }
         if(remark == ""){
            alert("Please enter remark");
            $("#remark").css("border", "1px solid #ec1313");
            $("#remark").focus();
            return false;
         }else{
            $("#remark").css("border", ""); 
         }
         
      } 
      else if (trnscto === "FD") 
      {
         const fd_fields = [
         { id:'fdno', name: 'FD no.'},
         { id: 'prjctnm', name: 'Project name'},
         { id: 'sbprjctnm', name: 'Sub project name'},
         { id: 'purpose', name: 'Purpose'},
         { id: 'message', name: 'Message'}
         ];
         for (let fd_field of fd_fields) {
            let fd_value = document.getElementById(fd_field.id).value.trim();
            if (!fd_value) {
               alert(`${fd_field.name} field is required!`);
               return false;
            }
         }
         // Compare fd amount with paid amount
         let fdamt = parseFloat(document.getElementById('amount').value) || 0;
         let totalAmount = Number.isFinite(fdamt) && fdamt % 1 !== 0 ? Math.trunc(fdamt) : fdamt;
         let paidAmount = Number.isFinite(paidamt) && paidamt % 1 !== 0 ? Math.trunc(paidamt) : paidamt;
         if (totalAmount !== paidAmount) {
            alert("Total Amount and Paid Amount must be the same!");
            $("#amount").css("border", "1px solid #ec1313");
            $("#amount").focus();
            return false; // Prevent form submission
         }
         else{
            $("#amount").css("border", "");
         }
      }
      else if (trnscto === "GST") 
      {
         const gst_fields = [
         { id: 'statenm', name: 'State name'},
         { id: 'gstno', name: 'GSTIN no.'},
         { id: 'fromdt', name: 'From date'},
         { id: 'todt', name: 'To date'},
         { id: 'remark', name: 'Ramark'}
         ];
         for (let gst_field of gst_fields) {
            let gst_value = document.getElementById(gst_field.id).value.trim();
            let gst_id =  document.getElementById(gst_field.id);
            if (!gst_value) {
               alert(`${gst_field.name} field is required!`);
               gst_id.style.border = '1px solid red';
               return false;
            }
            else {
               gst_id.style.border = ''; // Reset border if valid
            }
         }
      }   
      else if (trnscto === "Withdraw") 
      {
         let with_value = document.getElementById('drwrnm').value.trim();
         if (!with_value) {
            alert('Name field is required!');
            return false;
         }
      }
      else if(trnscto === "Rent")
      {
         const rnt_fields = [
            { id:'year', name: 'Year'},
            { id: 'month', name: 'Month'},
            { id: 'type', name: 'Type'},
            { id: 'purpose', name: 'Purpose'}
         ];
         for (let rnt_field of rnt_fields) {
            let rnt_value = document.getElementById(rnt_field.id).value.trim();
            if (!rnt_value) {
               alert(`${rnt_field.name} field is required!`);
               return false;
            }
         }
        let selected = document.querySelector('input[name="selected_row"]:checked');
        if (!selected) {
            alert("Please select one of the rent details.");
            return false; // Prevent form submission
        }
        // Extract the row index from selected radio button
         let index = selected.value;
         // Get the rate and paid amount fields
         let rate = parseFloat(document.getElementById(`rate${index}`).value) || 0;
         let totalAmount = Number.isFinite(rate) && rate % 1 !== 0 ? Math.trunc(rate) : rate;
         let paidAmount = Number.isFinite(paidamt) && paidamt % 1 !== 0 ? Math.trunc(paidamt) : paidamt;
         if (paidAmount !== totalAmount) {
            alert(`Paid amount must match the rate.`);
            return false;
         }

        //return true;
      }
      else if (trnscto === "Bank Transfer") 
      {
         const bank_fields = [
         { id: 'org_nm', name: 'Organization name'},
         { id: 'bnkaccnt', name: 'Bank Allias Name'},
         { id: 'bnktrn_remarks', name: 'Remarks'}
         ];
         for (let bank_field of bank_fields) {
            let bank_value = document.getElementById(bank_field.id).value.trim();
            if (!bank_value) {
               alert(`${bank_field.name} field is required!`);
               return false;
            }
         }
      }
      else if (trnscto === "Asset Finance") 
      {
         const asset_fields = [
         { id: 'benif_acc', name: 'Benificiary A/c'},
         { id: 'prjctnm', name: 'Project name'},
         { id: 'sbprjctnm', name: 'Sub project name'}
         ];
         for (let asset_field of asset_fields) {
            let asset_value = document.getElementById(asset_field.id).value.trim();
            if (!asset_value) {
               alert(`${asset_field.name} field is required!`);
               $("#"+asset_field.id).siblings(".select2-container").find(".select2-selection").css("border", "1px solid #ec1313"); // Apply border to Select2 container
               $("#"+asset_field.id).siblings(".select2-container").find(".select2-selection").focus();
               return false;
            }
            else {
               $("#"+asset_field.id).siblings(".select2-container").find(".select2-selection").css("border", "");
            }
         }
      }
      else if (trnscto === "DD") 
      {
         const dd_fields = [
         { id:'ddno', name: 'DD no.'},
         { id: 'prjctnm', name: 'Project name'},
         { id: 'sbprjctnm', name: 'Sub project name'},
         { id: 'purpose', name: 'Purpose'},
         { id: 'ddexprsn', name: 'Expense Reasons'},
         { id: 'ddbenificiary', name: 'Benificiary'},
         { id: 'message', name: 'Message'}
         ];
         for (let dd_field of dd_fields) {
            let dd_value = document.getElementById(dd_field.id).value.trim();
            if (!dd_value) {
               alert(`${dd_field.name} field is required!`);
               return false;
            }
         }
         // Compare dd amount with paid amount
         let ddamt = parseFloat(document.getElementById('amount').value) || 0;
         let totalAmount = Number.isFinite(ddamt) && ddamt % 1 !== 0 ? Math.trunc(ddamt) : ddamt;
         let paidAmount = Number.isFinite(paidamt) && paidamt % 1 !== 0 ? Math.trunc(paidamt) : paidamt;
         if (totalAmount !== paidAmount) {
            alert("Total Amount and Paid Amount must be the same!");
            $("#amount").css("border", "1px solid #ec1313");
            $("#amount").focus();
            return false; // Prevent form submission
         }
         else{
            $("#amount").css("border", "");
         }
      }
      return true;
    }
    else
    {
        alert("Payment Transaction Type is invalid")
        return false;
    }

  }

</script>

