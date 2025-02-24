<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
?>
<?php
if (isset($_POST['org_nm'])) {
    $orgid = mysqli_real_escape_string($con, $_POST['org_nm']);
    $gtbacc = mysqli_query($con, "SELECT id,accnm FROM `fin_bankaccount` WHERE `orgid`='$orgid' AND status='1'");

    echo "<option value=''>--- Select Bank Account ---</option>";
    while ($fthbacc = mysqli_fetch_object($gtbacc)) {
      echo "<option value='".$fthbacc->id."'>".$fthbacc->accnm."</option>";
    }
  }

  if (isset($_POST['bankid'])) {
	  $bankid = mysqli_real_escape_string($con, $_POST['bankid']); 
    $fetchacc = mysqli_query($con,"SELECT accntnum,bnkname FROM `fin_bankaccount` WHERE `id`='$bankid'");
    $rowacc = mysqli_fetch_object($fetchacc);

    $fetch = mysqli_query($con,"SELECT branch,ifsc,cifno,location FROM `fin_bank` WHERE `id`='$rowacc->bnkname'");
    $row = mysqli_fetch_object($fetch);

    $locqry=mysqli_query($con,"SELECT lname FROM `fin_location` WHERE `id` = ".$row->location." and `status`='1'");
    $getloc = mysqli_fetch_object($locqry);
    if ($row->branch!='') {
      $branch = $row->branch;
    }else{
      $branch = "";
    }
    if ($row->ifsc!='') {
      $ifsc = $row->ifsc;
    }else{
      $ifsc = "";
    }
    if ($rowacc->accntnum!='') {
      $accnum = $rowacc->accntnum;
    }else{
      $accnum = "";
    }
    if ($row->cifno!='') {
      $cifno = $row->cifno;
    }else{
      $cifno = "";
    }
    if ($getloc->lname!='') {
      $locid = $getloc->lname;
    }else{
      $locid = "";
    }
    if ($getloc->lname!='') {
      $locnm = $getloc->lname;
    }else{
      $locnm = "";
    }
    echo $branch.'*'.$accnum.'*'.$ifsc.'*'.$cifno.'*'.$locid.'*'.$locnm;
  }

?>