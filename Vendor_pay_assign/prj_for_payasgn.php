<?php include("../../../config.php"); ?>
<?php
if (isset($_POST['vndrnm'])) {

	$vndrid = mysqli_real_escape_string($con, $_POST['vndrnm']);
	// Getting Associated Project Names
	$prjqr = mysqli_query($con, "SELECT x.*,y.pname,y.id as pid FROM `prj_joborder_req` x, `prj_project` y WHERE x.`proj_id`=y.`id` AND x.`vendor`='$vndrid' GROUP BY y.pname");
	if (mysqli_num_rows($prjqr) > 0) {
		$prjlist = "<option value=''>--- Pick A Project ---</option>";
		while ($fthprj = mysqli_fetch_object($prjqr)) {
			$prjlist .= "<option value='" . $fthprj->pid . "'>" . $fthprj->pname . "</option>";
		}
	} else {
		$prjlist = "<option value=''>No Project Found</option>";
	}

	$final_data = ['prjnames' => $prjlist];

	echo json_encode($final_data);
}
?>