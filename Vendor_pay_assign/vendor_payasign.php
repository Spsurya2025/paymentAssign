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
<?php
  if(isset($_GET['py_req_id'])){
    $prid = $_GET['py_req_id'];
    $getjobordertypeid = mysqli_query($con, "SELECT jobodrnum FROM `fin_payment_request_vendor` WHERE `payreq_id`='$prid'");
    $joborerid = mysqli_fetch_object($getjobordertypeid);
    $getjoborderdetails = mysqli_query($con, "SELECT joborder_type FROM `prj_joborder_req` WHERE `jon`='$joborerid->jobodrnum'");
    $joborerdtls = mysqli_fetch_object($getjoborderdetails);
    if ($joborerdtls->joborder_type == '3' || $joborerdtls->joborder_type == '2' || $joborerdtls->joborder_type == '6') {
        $subbmstable = 'prj_sub_bms_rate';
    } else {
        $subbmstable = 'prj_sub_bms';
    }
    $vnqr = mysqli_query($con, "SELECT x.*,y.*,z.vendor_name,z.id AS v_id,a.pname,a.id AS prj_id,b.spname,b.id AS sp_id,c.sub_bms,c.id AS sub_bms_id, u.payreq_dt FROM `fin_payment_request_vendor` x, `fin_payment_request_vendor_subprj` y, `fin_all_pay_request` u , `prj_vendor` z, `prj_project` a, `prj_subproject` b, `" . $subbmstable . "` c WHERE x.`id`=y.`vndr_req_id` AND x.`vendornm`=z.`id` AND x.`prjnm`=a.`id` AND y.`subprjid`=b.`id` AND u.pay_request_id = x.payreq_id AND y.`bmsid`=c.`id` AND x.`payreq_id`='$prid'");  
    $fthvndrs = mysqli_fetch_object($vnqr);
    $JobOrderNum = $fthvndrs->jobodrnum;
    $VendorId = $fthvndrs->vendornm;
    $bmsid = $fthvndrs->bmsid;
    $subprjid = $fthvndrs->subprjid;
    $payreq_id = $fthvndrs->payreq_id;
    $payreq_dt = $fthvndrs->payreq_dt;
    $pr_num = $fthvndrs->pr_num;
    $joquery = mysqli_query($con, "SELECT a.id FROM `prj_joborder_req` a WHERE a.jon ='$JobOrderNum'  ");
    $JobOrderId = '';
    $row = mysqli_num_rows($joquery);
    if ($row > 0) {
        while ($res = mysqli_fetch_object($joquery)) {
            $JobOrderId = $res->id;
        }
    }
        $totalPaidAmount_Vendor = 0;
        $totalPendingAmount_Vendor = 0;
        $totalPaidAmount_jo = 0;
        $totalPendingAmount_jo = 0;
        $totalPaidAmount_BMS = 0;  
        $totalPendingAmount_BMS = 0;
        $VendorDRCR_balance = 0;
        $result = mysqli_query($con, "SELECT *,sum(dr) as sumdr,sum(cr) as sumcr FROM ( (SELECT 1 AS type, a.id, a.invno AS invoice_no, SUM(b.totl) AS cr, NULL AS dr, a.userdt AS createddate, a.invtype, NULL AS particulars FROM fin_voucher a RIGHT JOIN fin_voucher_details b ON a.id=b.voucher_id WHERE a.status=1 AND a.vendornm=" . $VendorId . " GROUP BY a.id) UNION ALL (SELECT 2 AS type, b.id, b.debit_note_no AS invoice_no, NULL AS cr, b.total_amt AS dr, b.return_date AS createddate, b.dn_type AS invtype, b.inv_id AS particulars FROM debit_notes b RIGHT JOIN debit_note_items c ON b.id=c.debitnote_id WHERE b.user_type=2 AND b.user_id=" . $VendorId . " AND b.status=1 GROUP BY c.debitnote_id) UNION ALL (SELECT 3 AS type, b.id, b.ref_no AS invoice_no, NULL AS cr, a.amount AS dr, a.exp_dt AS createddate, NULL AS invtype, b.employee_name AS particulars FROM exp_journal_expenditure a LEFT JOIN exp_journal_entry b ON a.journal_id=b.id WHERE b.apprvl_stg='6' AND b.finl_aprvl='1' AND a.expense_reasons=2 AND a.expense_reasons_subgroup=" . $VendorId . ") UNION ALL (SELECT 4 AS type, a.id, a.invno AS invoice_no, NULL AS cr, a.trspamnt AS dr, a.userdt AS createddate, a.invtype, d.subtypenm AS particulars FROM fin_voucher a RIGHT JOIN fin_voucher_details b ON a.id=b.voucher_id LEFT JOIN fin_grouping_subtype d ON d.id=a.trs WHERE a.status=1 AND a.vendornm=" . $VendorId . " AND a.is_tds=1 GROUP BY a.id) UNION ALL (SELECT 5 AS type, a.id, a.adj_no AS invoice_no, NULL AS cr, b.crdt AS dr, a.entry_date AS createddate, b.inv_typ, b.nm FROM adjustment_entry a RIGHT JOIN adjustment_entry_details b ON a.id=b.adj_id WHERE a.status=1 AND b.dc_typ=2 AND a.id IN(SELECT adj_id FROM adjustment_entry_details WHERE inv_typ=2 AND dc_typ=1 AND nm=" . $VendorId . ")) UNION ALL (SELECT 5 AS type, a.id, a.adj_no AS invoice_no, b.dbt AS cr, NULL AS dr, a.entry_date AS createddate, b.inv_typ, b.nm FROM adjustment_entry a RIGHT JOIN adjustment_entry_details b ON a.id=b.adj_id WHERE a.status=1 AND b.dc_typ=1 AND a.id IN(SELECT adj_id FROM adjustment_entry_details WHERE inv_typ=2 AND dc_typ=2 AND nm=" . $VendorId . ")) UNION ALL (SELECT 6 AS type, a.id, a.pmt_no AS invoice_no, c.transac_amt AS cr, NULL AS dr, c.transac_dt AS createddate, NULL AS invtype, a.bankacc_id FROM fin_payment_entry a INNER JOIN fin_payment_entry_vendor b ON a.id=b.payent_id INNER JOIN fin_banking_imports c ON a.bnkimprt_id=c.id WHERE a.pay_approval_stat='2' AND a.trnsc_type='Credit' AND b.vndrnm=" . $VendorId . ") UNION ALL (SELECT 6 AS type, a.id, a.pmt_no AS invoice_no, NULL AS cr, c.transac_amt AS dr, c.transac_dt AS createddate, NULL AS invtype, a.bankacc_id FROM fin_payment_entry a INNER JOIN fin_payment_entry_vendor b ON a.id=b.payent_id INNER JOIN fin_banking_imports c ON a.bnkimprt_id=c.id WHERE a.pay_approval_stat='2' AND a.trnsc_type='Debit' AND b.vndrnm=" . $VendorId . ") UNION ALL (SELECT 6 AS type, a.id, a.pmt_no AS invoice_no, c.transac_amt AS cr, NULL AS dr, c.transac_dt AS createddate, NULL AS invtype, a.bankacc_id FROM fin_payment_entry a INNER JOIN fin_payment_entry_others b ON a.id=b.payent_id INNER JOIN fin_banking_imports c ON a.bnkimprt_id=c.id WHERE a.pay_approval_stat='2' AND a.trnscto='Others' AND a.trnsc_type='Credit' AND b.particlr=" . $VendorId . " AND b.othrhd=2) UNION ALL (SELECT 6 AS type, a.id, a.pmt_no AS invoice_no, NULL AS cr, c.transac_amt AS dr, c.transac_dt AS createddate, NULL AS invtype, a.bankacc_id FROM fin_payment_entry a INNER JOIN fin_payment_entry_others b ON a.id=b.payent_id INNER JOIN fin_banking_imports c ON a.bnkimprt_id=c.id WHERE a.pay_approval_stat='2' AND a.trnscto='Others' AND a.trnsc_type='Debit' AND b.particlr=" . $VendorId . " AND b.othrhd=2) UNION ALL (SELECT 7 AS type, id, 'Opening Balance' AS invoice_no, CASE WHEN amt_type='Credit' THEN ABS(opnbalamt) ELSE NULL END AS cr, CASE WHEN amt_type='Debit' THEN ABS(opnbalamt) ELSE NULL END AS dr, opnbal_date AS createddate, NULL AS invtype, NULL AS particulars FROM fin_opening_balance WHERE status='1' AND opnbalfor=2 AND seltypname=" . $VendorId . " ) ) results ORDER BY createddate ASC");
        $tlvchr = mysqli_num_rows($result);
        if ($tlvchr > 0) {
            while ($fetchp = mysqli_fetch_object($result)) {

                $dr = $fetchp->sumdr;
                $cr = $fetchp->sumcr;
            }
            $VendorDRCR_balance = $debitCrdit = ($cr - $dr);
        }
        $ttlpaidamountVnd = mysqli_query($con, "SELECT x.id,x.preqnum,x.trnscto,x.payee_nm,x.pay_approval_stat, y.pay_rqst_id,y.vndrnm,y.jobodr_num,y.bmsnm,y.subprjct_nm,y.paid_amnt  FROM `fin_payment_entry` x INNER JOIN fin_payment_entry_vendor y ON (x.id = y.payent_id) WHERE x.trnsc_type LIKE '%Debit%' AND x.pay_approval_stat IN ('0','1','2','3') AND y.vndrnm = '$VendorId' AND y.pay_rqst_id != '$payreq_id'");
        while ($rowAssignTable = mysqli_fetch_object($ttlpaidamountVnd)) {   
            $pay_approval_stat = $rowAssignTable->pay_approval_stat;
            $jobodr_num = $rowAssignTable->jobodr_num;
            $bmsnm = $rowAssignTable->bmsnm;
            $subprjct_nm = $rowAssignTable->subprjct_nm;
            $paid_amnt = $rowAssignTable->paid_amnt;

            if ($pay_approval_stat == '2') { 
                $totalPaidAmount_Vendor += $paid_amnt;
                if ($JobOrderNum == $jobodr_num) {
                    $totalPaidAmount_jo += $paid_amnt;
                }
                if (($bmsid == $bmsnm) && ($subprjid == $subprjct_nm) && ($JobOrderNum == $jobodr_num)) { 
                    $totalPaidAmount_BMS += $paid_amnt;
                }
            } else { 
                $totalPendingAmount_Vendor += $paid_amnt;
                if ($JobOrderNum == $jobodr_num) {
                    $totalPendingAmount_jo += $paid_amnt;
                }
                if (($bmsid == $bmsnm) && ($subprjid == $subprjct_nm) && ($JobOrderNum == $jobodr_num)) {
                    $totalPendingAmount_BMS += $paid_amnt;
                }
            }
        }
        $ttlpaidamountOth = mysqli_query($con, "SELECT x.id,x.preqnum,x.trnscto,x.payee_nm,x.pay_approval_stat, y.pay_rqst_id,y.othrhd,y.particlr,y.subproj_id,y.proj_id,y.paid_othr_amt FROM `fin_payment_entry` x INNER JOIN `fin_payment_entry_others` y ON (x.id = y.payent_id) WHERE x.trnsc_type LIKE '%Debit%' AND x.pay_approval_stat IN ('0','1','2','3') AND y.othrhd = '2' AND y.particlr = '$VendorId' AND y.pay_rqst_id != '$payreq_id' AND x.preqnum !='$pr_num'");
        while ($rowAssignTable = mysqli_fetch_object($ttlpaidamountVnd)) {  
            $pay_approval_stat = $rowAssignTable->pay_approval_stat;
            $paid_othr_amt = $rowAssignTable->paid_othr_amt;

            if ($pay_approval_stat == '2') {
                $totalPaidAmount_Vendor += $paid_othr_amt;
            } else {
                $totalPendingAmount_Vendor += $paid_othr_amt;
            }
        }
        $BankingImpPendingAmount_Vendor = 0;
        $BankingImpPendingAmount_jo = 0;
        $BankingImpPendingAmount_BMS = 0;
        $sqlBnkimpVendor = mysqli_query($con, "SELECT f.id, f.pay_request_id, f.pr_num, f.payreq_amt, f.payreq_dt, f.request_for, x.vendornm, x.jobodrnum, m.subprjid,m.bmsid FROM fin_all_pay_request f LEFT JOIN fin_payment_request_vendor x ON f.pay_request_id = x.payreq_id LEFT JOIN fin_payment_request_vendor_subprj m ON m.payreqid = f.pay_request_id WHERE f.request_for LIKE '%Vendor%' AND f.payreq_status = '1' AND x.payreq_id !='$payreq_id' AND f.payreq_dt > '2024-01-01' AND x.vendornm = '$VendorId' AND NOT EXISTS ( SELECT 1 FROM fin_payment_entry a WHERE f.pr_num = a.preqnum ) AND NOT EXISTS ( SELECT 1 FROM fin_payment_entry_vendor b WHERE f.pay_request_id = b.pay_rqst_id ) ");
        while ($rowbnkImpTablev = mysqli_fetch_object($sqlBnkimpVendor)) {   
            $jobodr_num57 = $rowbnkImpTablev->jobodrnum; 
            $bmsnm = $rowbnkImpTablev->bmsid; 
            $subprjct_nm = $rowbnkImpTablev->subprjid; 
            $bnkingImpPending = $rowbnkImpTablev->payreq_amt;

            $BankingImpPendingAmount_Vendor += $bnkingImpPending;  
            if ($JobOrderNum == $jobodr_num57) {  
                $BankingImpPendingAmount_jo += $bnkingImpPending;
            }
            if (($bmsid == $bmsnm) && ($subprjid == $subprjct_nm) && ($JobOrderNum == $jobodr_num57)) { 
                $BankingImpPendingAmount_BMS += $bnkingImpPending;
            }
        }
        $sqlBnkimpOthr = mysqli_query($con, "SELECT a.pay_request_id, a.pr_num, a.payreq_dt, a.request_for, a.payreq_amt, a.payreq_status, b.id, b.othrhead, b.prtclr, b.reqst_amt as FromOthReqAmt FROM `fin_all_pay_request` a LEFT JOIN `fin_payment_request_others` b ON (a.pay_request_id = b.payreq_id)  WHERE `request_for` LIKE 'Others' AND a.payreq_status = '1' AND a.payreq_dt > '2024-01-01' AND b.othrhead ='2' AND b.prtclr = '$VendorId' AND NOT EXISTS( SELECT 1 FROM fin_payment_entry x WHERE a.pr_num = x.preqnum ) AND NOT EXISTS ( SELECT 1 FROM fin_payment_entry_others y WHERE a.pay_request_id = y.pay_rqst_id ) ");
        while ($rowbnkImpTableO = mysqli_fetch_object($sqlBnkimpOthr)) {
            $bnkingImpPending = $rowbnkImpTableO->payreq_amt;
            $BankingImpPendingAmount_Vendor += $bnkingImpPending;
        }
        $PaymentReqPending_Vendor = 0;
        $PaymentReqPending_jo = 0;
        $PaymentReqPending_BMS = 0; 
        $sqlPayReqVendor = mysqli_query($con, "SELECT f.id, f.pay_request_id, f.pr_num, f.payreq_amt, f.payreq_dt, f.request_for, x.vendornm, x.jobodrnum, m.subprjid,m.bmsid FROM fin_all_pay_request f LEFT JOIN fin_payment_request_vendor x ON f.pay_request_id = x.payreq_id LEFT JOIN fin_payment_request_vendor_subprj m ON m.payreqid = f.pay_request_id WHERE f.request_for LIKE '%Vendor%' AND f.payreq_status IN ('0','2','4','5')  AND x.payreq_id !='$payreq_id'  AND x.vendornm = '$VendorId'  ");
        while ($rowPayRqTableVnd = mysqli_fetch_object($sqlPayReqVendor)) {
            $payreq_status = $rowPayRqTableVnd->payreq_status;
            $jobodr_num9 = $rowPayRqTableVnd->jobodrnum;
            $bmsnm9 = $rowPayRqTableVnd->bmsid;
            $subprjct_nm9 = $rowPayRqTableVnd->subprjid;
            $payreq_amt = $rowPayRqTableVnd->payreq_amt;

            $PaymentReqPending_Vendor += $payreq_amt;
            if ($JobOrderNum == $jobodr_num9) {
                $PaymentReqPending_jo += $payreq_amt;
            }
            if (($bmsid == $bmsnm9) && ($subprjid == $subprjct_nm9) && ($JobOrderNum == $jobodr_num9)) {
                $PaymentReqPending_BMS += $payreq_amt;
            }
        }
        $sqlPayReqOther = mysqli_query($con, "SELECT a.pay_request_id, a.pr_num, a.payreq_dt, a.request_for, a.payreq_amt, a.payreq_status, b.id, b.othrhead, b.prtclr, b.reqst_amt as FromOthReqAmt FROM `fin_all_pay_request` a LEFT JOIN `fin_payment_request_others` b ON (a.pay_request_id = b.payreq_id)  WHERE `request_for` LIKE 'Others' AND a.payreq_status = '1' AND b.othrhead ='2' AND b.prtclr = '$VendorId'  ");
        while ($rowPayRqTableOth = mysqli_fetch_object($sqlPayReqOther)) {
            $payreq_status = $rowPayRqTableOth->payreq_status;
            $payreq_amt = $rowPayRqTableOth->payreq_amt;

            if ($payreq_status == '1') {
                $PaymentReqApproved_Vendor += $payreq_amt;
            } else {
                $PaymentReqPending_Vendor += $payreq_amt;
            }
        }
        $Job_InvoiceValue = 0;
        $VndInvoiceSQl = mysqli_query($con, "SELECT a.id,a.joborder FROM fin_voucher a   WHERE a.status ='1' AND a.vendornm='$VendorId' AND a.joborder ='$JobOrderId'");
        $sumtot = 0;
        while ($rslt = mysqli_fetch_object($VndInvoiceSQl)) {
            $id = $rslt->id;
            $VendInvDtls = mysqli_query($con, "SELECT SUM(totl) as totl FROM `fin_voucher_details` WHERE `voucher_id` =$id");
            $rslt2 = mysqli_fetch_object($VendInvDtls);
            $total = $rslt2->totl;
            $Job_InvoiceValue += $total;
        }
        $VendorCreditLimit = 0;
        $JobOrderCreditLimit = 0;
        $vndrqr = mysqli_query($con, "SELECT * FROM `prj_credit_lmt_vndr` WHERE `vndr_id`='$VendorId' AND `status`='1' AND `credit_type`='Overalllimt'");

        if (mysqli_num_rows($vndrqr) > 0) {
            $currentDate = $payreq_dt;
            $selectedRecord = null;

            while ($fthcrlim = mysqli_fetch_object($vndrqr)) {
                $fromDate = $fthcrlim->fromdt;
                $expiryDate = $fthcrlim->todt_Expdt;
                if ($currentDate >= $fromDate && $currentDate <= $expiryDate) {
                    if ($selectedRecord === null || $fromDate > $selectedRecord->fromdt) {
                        $selectedRecord = $fthcrlim;
                    }
                }
            }
            if ($selectedRecord !== null) {
                $VendorCreditLimit = number_format($selectedRecord->vendor_amount, 2, ".", "");
            } else {
                $VendorCreditLimit = '0.00';
            }
        } else {
            $VendorCreditLimit = "0.00";
        }
        $joCrlimit = mysqli_query($con, " SELECT x.* FROM prj_credit_lmt_vndr x INNER JOIN prj_joborder_req y ON x.jo_no = y.jon WHERE x.credit_type='Joborder'   AND x.status='1'   AND x.vndr_id=$VendorId   AND( y.id=$JobOrderId  OR x.jon= '$JobOrderNum')");
        if (mysqli_num_rows($joCrlimit) > 0) {
            $currentDate = $payreq_dt;
            $selectedRecord = null;
            while ($fthcrlim = mysqli_fetch_object($joCrlimit)) {
                $fromDate = $fthcrlim->fromdt;
                $expiryDate = $fthcrlim->todt_Expdt;
                if ($currentDate >= $fromDate && $currentDate <= $expiryDate) {
                    if ($selectedRecord === null || $fromDate > $selectedRecord->fromdt) {
                        $selectedRecord = $fthcrlim;
                    }
                }
            }
            if ($selectedRecord !== null) {
                $JobOrderCreditLimit = number_format($selectedRecord->gst_amount, 2, ".", "");
            } else {
                $JobOrderCreditLimit = '0.00';
            }
        } else {
            $JobOrderCreditLimit = '0.00';
        }
        $OverAllVendor_wise_Paid = $totalPaidAmount_Vendor;
        $OverallVendor_wise_Pending = $totalPendingAmount_Vendor + $BankingImpPendingAmount_Vendor + $PaymentReqPending_Vendor; 
        $OverallJobOrder_wise_pending = $totalPendingAmount_jo + $BankingImpPendingAmount_jo + $PaymentReqPending_jo; // Total Pending (Assign_pending + BankingImp_pending + Payment_request_pending)
        $OverallBMS_wise_paid = $totalPaidAmount_BMS; // Total Vendor Wise paid Amount
        $OverallBMS_wise_pending = $totalPendingAmount_BMS + $BankingImpPendingAmount_BMS + $PaymentReqPending_BMS; // Total Pending (Assign_pending + BankingImp_pending + Payment_request_pending)
        $VendorCreditLimit = (float)$VendorCreditLimit;
        $OverAllbalance_Vendor = ($VendorDRCR_balance + $VendorCreditLimit) - ($OverallVendor_wise_Pending);
        $OverAllBalance_Jo = ($JobOrderCreditLimit + $Job_InvoiceValue);
        if ($OverAllBalance_Jo > $fthvndrs->jobodrval) $OverAllBalance_Jo = $fthvndrs->jobodrval;
        $OverAllBalance_Jo = ($OverAllBalance_Jo - ($OverallJobOrder_wise_paid + $OverallJobOrder_wise_pending));
    }
        ?>
<!-- Supplier Form -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Vendor Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="suplrnm">Vendor Name</label>
        <select class="form-control" name="vndrnm" id="vndrnm" readonly>
            <option value="<?php echo $fthvndrs->v_id;?>"><?php echo $fthvndrs->vendor_name;?></option>;
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="v_organization_name" id="v_organization" readonly>
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
        <input type="text" name="requestn" id="podate" value="<?php echo $result_all->pr_num; ?>" class="form-control" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prj_name">Project Name</label>
        <select class="form-control" name="prjct_name" id="prjct_name" readonly>
          <option value="<?php echo $fthvndrs->prj_id; ?>"><?php echo $fthvndrs->pname; ?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">Job Order Number</label>
        <select class="form-control" name="jobodr_num" id="jobodr_num" readonly>
          <option value="<?php echo $fthvndrs->jobodrnum;?>"><?php echo $fthvndrs->jobodrnum;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">Job Order Value</label>
        <input type="text" name="jobodr_val" id="jobodr_val" value="<?php echo $fthvndrs->jobodrval; ?>" class="form-control" placeholder="yyyy-mm-dd" readonly>
      </div>
    </div>
  </div>
  <div class="col-lg-12">   
  </div>
  <?php
    $joucqr = mysqli_query($con, "SELECT `joborder_type` FROM `prj_joborder_req` WHERE `jon`='$fthvndrs->jobodrnum'");
    $fthuc = mysqli_fetch_object($joucqr);
    $jobordertype = $fthuc->joborder_type;
  ?>
  <div class="col-lg-12" id="prDtls">
      <div class="col-lg-12">
        <legend><h6><strong style="color: #2bc59b;">Job Order Details</strong></h6></legend>
        <div class="table-responsive">
          <table class="table table-bordered table-responsive">
            <thead>
              <tr>
                <th>#</th>
                <th style="width: 120px;">Job Order No.</th>
                <th>Project</th>
                <th>JO Value</th>
                <th>Invoice Value</th>
                <th>Request Amounts</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control" value="<?php echo $fthvndrs->jobodrnum; ?>" readonly></td>
                    <td><input type="text" class="form-control" value="<?php echo $fthvndrs->pname; ?>" readonly></td>
                    <td>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                        <input type="text" class="form-control" value="<?php echo $fthvndrs->jobodrval; ?>" readonly>
                      </div>
                    </td>
                    <td>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                        <input type="text" class="form-control" value="<?php echo $Job_InvoiceValue; ?>" readonly>
                      </div>
                    </td>
                    <td>
                      <div class="input-group">
                       <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                       <input type="text" class="form-control" value="<?php echo $fthvndrs->req_amt; ?>" readonly>
                      </div>
                    </td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-12" id="trDtls">
      <div class="col-lg-12">
        <legend><h6><strong style="color: #2bc59b;">Sub Project Details</strong></h6></legend>
        <div class="table-responsive">
          <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sub Project</th>
                    <th>BMS</th>
                    <th>Work Desc (Alias)</th>
                    <th>Value</th>
                    <th>Request Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $bmsid = $fthvndrs->bmsid;
                    $subprjid = $fthvndrs->subprjid;
                    $paidvalue = $OverallBMS_wise_paid;
                    $processamnt = $OverallBMS_wise_pending;
                    $totalamt = $fthvndrs->subprj_val;
                    $balance = $fthvndrs->subprj_val - ($paidvalue + $processamnt);
                ?>
                <tr>
                    <td></td>
                    <td>
                      <select name="subprjct_nm" class="form-control" id="" readonly>
                        <option value="<?php echo $fthvndrs->sp_id;?>"><?php echo $fthvndrs->spname;?></option>
                      </select>
                    </td>
                    <td>
                      <select name="bmsnm" class="form-control" id="" readonly>
                        <option value="<?php echo $fthvndrs->sub_bms_id;?>"><?php echo $fthvndrs->sub_bms;?></option>
                      </select>
                    <td><input type="text" name="wrk_dscrptn" class="form-control" value="<?php echo $fthvndrs->work_desc;?>" readonly></td>
                    <td>
                     <div class="input-group">
                       <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                       <input type="text" name="subprjct_val" class="form-control" value="<?php echo $fthvndrs->subprj_val;?>" readonly>
                      </div>
                    </td>
                    <td>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                        <input type="text" class="form-control" name="req_amt_v" value="<?php echo  $fthvndrs->req_amt;?>" readonly>
                      </div>
                    </td>
                    <tbody>
                      <tr>
                          <th colspan="3">Total Requested amount : </th>
                          <th>
                            <input type="text" class="form-control" id="all_total" value="<?php echo $fthvndrs->req_amt;?>" readonly>
                            <span id="amt-error" class="error-message"></span>
                          </th>
                      </tr>
                    </tbody>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>  
</div>
<!-- End of Supplier Form -->