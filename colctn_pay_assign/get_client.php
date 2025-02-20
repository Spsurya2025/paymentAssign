<?php include("../../../config.php"); ?>
<?php
	if(isset($_POST['dbtr_typ'])) {
	 $dbtr_typ = mysqli_real_escape_string($con, $_POST['dbtr_typ']);
	  $sql = mysqli_query($con,"SELECT id,companynm FROM `fin_customers` WHERE `status`='1' AND `group_subtype`='$dbtr_typ' ORDER BY `companynm` ASC");

	  if (mysqli_num_rows($sql) > 0) {
	  	echo "<option value=''>--- Select Client Name ---</option>";
	  	while ($clntnm = mysqli_fetch_object($sql)) {
	  		echo "<option value='".$clntnm->id.">".$clntnm->companynm."</option>";
	  	}
	  }
	  else {
	  	echo "<option value=''>No Client Found</option>";
	  }
	}
?>