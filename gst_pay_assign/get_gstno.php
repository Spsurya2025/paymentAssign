<?php require_once('../../../config.php'); ?>

<?php
if(isset($_POST['statenm'])) {
    $statenm = mysqli_real_escape_string($con, $_POST['statenm']);
    $addresstype = mysqli_real_escape_string($con, $_POST['addressType']);
    $org_id = mysqli_real_escape_string($con, $_POST['org_id']);
    $sql = mysqli_query($con,"SELECT gst_no FROM `pur_address` WHERE `addrss_type`='$addresstype' AND `sid`='$statenm' AND `org_id`='$org_id'");

  if(mysqli_num_rows($sql) > 0) {
    while($row = mysqli_fetch_object($sql)) {
      echo $row->gst_no;
    }
  }
  else {
    echo '';
  }
}
 ?>