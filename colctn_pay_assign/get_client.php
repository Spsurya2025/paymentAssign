<?php include("../../../config.php"); ?>

<?php
	if(isset($_POST['dbtr_typ'])) {
    //echo getDebtorTypeDetails($con, $partynm,$pancol,$tancol,$lnkwith,$dbtrtype);
	function getDebtorTypeDetails($con, $partyColumn, $panColumn, $tanColumn, $lnkwith, $dbtrtype) {
		$options = '<option value="">---- Select Client Name ----</option>';
		
		$table = mysqli_real_escape_string($con, $lnkwith);
	
		$query_str = "SELECT id, `$partyColumn`, `$panColumn`, `$tanColumn`, pan_or_tan_prefer, supplyplace, bilphone, dprtmnt FROM `$table` WHERE `cust_status` = '1' AND `status`='1'";
	
		if ($dbtrtype) {
			$query_str .= " AND group_subtype='" . mysqli_real_escape_string($con, $dbtrtype) . "'";
		}
	
		$query = mysqli_query($con, $query_str);
	
		if ($query) {
			while ($row = mysqli_fetch_assoc($query)) {
				$pan = '';
				if ($row['pan_or_tan_prefer'] == 0) {
					$pan = $row[$panColumn];
				} elseif ($row['pan_or_tan_prefer'] == 1) {
					$pan = $row[$tanColumn];
				}
	
				// Get state name
				$state = '';
				$getstate = mysqli_query($con, "SELECT sname FROM prj_state WHERE id='" . mysqli_real_escape_string($con, $row['supplyplace']) . "'");
				if ($statede = $getstate->fetch_object()) {
					$state = $statede->sname;
				}
	
				$phone = htmlspecialchars($row['bilphone']);
				$depart = htmlspecialchars($row['dprtmnt']);
				$partyName = htmlspecialchars($row[$partyColumn]);
	
				$optionName = $partyName . ' (' . htmlspecialchars($pan) . ', ' . htmlspecialchars($state) . ', ' . $phone . ', ' . htmlspecialchars($depart) . ')';
	
				$options .= '<option value="' . htmlspecialchars($row['id']) . '">' . $optionName . '</option>';
			}
		}
		
		return $options;
	}
	$dbtrtype = mysqli_real_escape_string($con, $_POST['dbtr_typ']);
    $partynm = 'companynm';
    $pancol = 'pan';
    $tancol = 'tan';
    $lnkwith = 'fin_customers';
	echo getDebtorTypeDetails($con, $partynm,$pancol,$tancol,$lnkwith,$dbtrtype);
}
?>