<?php include("../../../config.php"); ?>
<?php
	if(isset($_POST['empnm'])) {

	  $emplid = mysqli_real_escape_string($con, $_POST['empnm']);

	  $sql = mysqli_query($con, "SELECT * FROM `hr_assign_department` WHERE `empname_id`= '$emplid'");

	  if(mysqli_num_rows($sql) > 0) {
	    $fchecd = mysqli_fetch_object($sql);
	    echo $fchecd->emp_cre_id;
	  }

	  else {
	  	echo "Employee Code Not Found";
	  }
	}
?>