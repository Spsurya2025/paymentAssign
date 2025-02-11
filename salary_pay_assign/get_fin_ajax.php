<?php require_once('../../../config.php'); ?>
<?php
include "fin_loan/clsLoan.php";
$objLoan = new clsLoan($con);
?>
<?php
if(isset($_POST['lcnumid'])){
  $lcnumid = $_POST['lcnumid'];
  // $sql = "SELECT x.*, b.`bnkname`,b.`accnum`,y.`organisation`, z.`supplier_name`, a.`lcrno`, c.`fullname` FROM `fin_lce` x,`fin_lcr` a,`prj_supplier` z,`prj_organisation` y,`fin_bank` b,`mstr_emp` c  WHERE x.`lcrreqnum`=a.`id`and x.`id`='$lcnumid' and  x.`partyid`=z.`id` and x.`orgid`=y.`id` and x.`accnoid`=b.`id` and x.`created_by`= c.`id` and x.`status`='1' ORDER BY x.`id` DESC";
  $sql = "SELECT Lce.*, lclmtentry.fin_lclimit_id, lclmtentry.limits, lclmtentry.account_no, lclmtentry.fin_bankacc_id, lclmtentry.sanc_id_name, Supl.supplier_name, Org.organisation
    FROM fin_lce Lce
    LEFT JOIN lclimitentry_type lclmtentry ON (Lce.issuebank = lclmtentry.fin_lclimit_id AND Lce.limits_type = lclmtentry.limits)
    LEFT JOIN prj_supplier Supl ON Lce.partyid = Supl.id 
    LEFT JOIN prj_organisation Org ON Lce.orgid = Org.id
    WHERE Lce.id='".$lcnumid."' AND Lce.status='1'  
    ORDER BY Lce.id DESC";
  $res = mysqli_query($con, $sql);
  $cnt=mysqli_num_rows($res);
  if($cnt > 0)
  {
    $row = mysqli_fetch_object($res);

    $id=$row->id;
    $orgnm=$row->organisation;
    $orgid=$row->orgid;
    //$bnknm=$institutename;
    // $bnkid=$row->issuebank;
    //$accnm=$account_no;
    // $accid=$row->accnoid;
    $dte=$row->issudt;
    $lcsplrnm=$row->supplier_name;
    $lcsplrid=$row->partyid;    
    $amnt=$row->amnt;
    $cday=$row->cday;
  }else{
    $id='';
    $orgid='';
    $orgnm='';
    // $bnknm='';
    // $bnkid='';
    // $accid='';
    // $accnm='';
    $dte='';
    $lcsplrid='';
    $lcsplrnm='';
    $amnt='';
    $cday='';
  }
  echo $id."*".$orgnm."*".$orgid."*".$dte."*".$lcsplrnm."*".$lcsplrid."*".$amnt."*".$cday;
}
?>
<?php 
  if(isset($_GET['benif_acc'])){
    $benif_acc = $_GET['benif_acc'];
    $sa = "SELECT * FROM `hr_earlysalary_request` WHERE `fullname_id`='$benif_acc' AND `f_appr`='4' AND `status`='1'";
    $said=mysqli_query($con,$sa); 

    while ($esaid = mysqli_fetch_object($said))
    { 
      echo '<option value="'. $esaid->esadv_id . '">' . $esaid->esadv_id .'</option>';
    }
  }
?>
<?php 
  if(isset($_GET['benif_acc_sa'])){
    $benif_acc = $_GET['benif_acc_sa'];
    $la = "SELECT * FROM `hr_advanceloan_request` WHERE `fullname_id`='$benif_acc' AND `f_app`='4' AND `status`='1'";
    $laid=mysqli_query($con,$la); 

    while ($elaid = mysqli_fetch_object($laid))
    { 
      echo '<option value="'. $elaid->loan_id . '">' . $elaid->loan_id .'</option>';
    }
  }
?>
<?php 
  if(isset($_GET['benif_acc_af'])){
    $benif_acc = $_GET['benif_acc_af'];
    $la = "SELECT * FROM `hr_assetfinance_request` WHERE `name_id`='$benif_acc' AND `f_appr`='4' AND `status`='1'";
    $laid=mysqli_query($con,$la); 

    while ($elaid = mysqli_fetch_object($laid))
    { 
      echo '<option value="'. $elaid->astfinl_id . '">' . $elaid->astfinl_id .'</option>';
    }
  }
?>
<?php 
  if(isset($_GET['benif_acc']) && isset($_GET['location']) && isset($_GET['month']) && isset($_GET['year'])){
    $benif_acc = $_GET['benif_acc'];
    $location = $_GET['location'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $orgid = $_GET['orgid'];
    $monthyr = $year.'-'.$month;

    $sp = "SELECT t2.*, t1.* FROM hr_employee_salary_processing t1 INNER JOIN hr_employee_salary_report t2 ON t1.`id` = t2.`unique_id` WHERE t1.month = '$monthyr' AND t2.`emp_id`='$benif_acc' AND t2.`org_nm`='$orgid' AND t2.`location_id`='$location' AND t1.`f_appr`='3' AND t1.`status`='1'";
    $spid=mysqli_query($con,$sp); 

    while ($espid = mysqli_fetch_object($spid))
    { ?>
      <option value="<?php echo $espid->req_id; ?>" ><?php echo $espid->req_id; ?> </option>
      <?php
    }
  }
  if(isset($_POST['account_no'])){
    $account_no = $_POST['account_no'];
    $sql = "SELECT * FROM `fin_loan_master` WHERE `loan_accntno`='$account_no'";
    $res = mysqli_query($con, $sql);
    $cnt=mysqli_num_rows($res);
    if($cnt > 0)
    {
      $row = mysqli_fetch_object($res);
      $reffno=$row->reffno;
      $nbfcid=$row->nbfcname;
    }

    if($nbfcid!='NA'){
        $nbfcname = $objLoan->getNBFCName($nbfcid);
        $nbfc_name = $nbfcname->nbfcname;
    }else{
        $nbfc_name = "NA";
    }

    echo $nbfc_name."*".$reffno;
}
?>