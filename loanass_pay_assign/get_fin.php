<?php
require_once('../../../config.php');
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