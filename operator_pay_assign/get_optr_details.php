<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['optr_id'])) {
    include "db_connection.php"; // Include your DB connection file
    $op_id = $_GET['optr_id'];

    // Fetch operator details
    $query = mysqli_query($con, "SELECT * FROM prj_optr_prjdetails WHERE optrid = '$op_id'");
    $response = array();

    if ($row = mysqli_fetch_object($query)) {
        // Fetch project name
        $prjQuery = mysqli_query($con, "SELECT id, pname FROM prj_project WHERE id ='$row->prjid'");
        $project = mysqli_fetch_object($prjQuery);

        // Fetch sub-project name
        $subPrjQuery = mysqli_query($con, "SELECT id, spname FROM prj_subproject WHERE id ='$row->subprjid'");
        $subProject = mysqli_fetch_object($subPrjQuery);

        // Fetch state
        $stateQuery = mysqli_query($con, "SELECT id, sname FROM prj_state WHERE id='$row->stateid'");
        $state = mysqli_fetch_object($stateQuery);

        // Fetch district
        $districtQuery = mysqli_query($con, "SELECT id, distname FROM prj_district WHERE id='$row->distid'");
        $district = mysqli_fetch_object($districtQuery);

        // Fetch block
        $blockQuery = mysqli_query($con, "SELECT id, blockname FROM prj_block WHERE id='$row->blockid'");
        $block = mysqli_fetch_object($blockQuery);

        // Fetch Gram Panchayat
        $gPanchQuery = mysqli_query($con, "SELECT id, gpname FROM prj_gpanchayat WHERE id='$row->gpid'");
        $gPanch = mysqli_fetch_object($gPanchQuery);

        // Fetch village
        $villageQuery = mysqli_query($con, "SELECT id, villagename FROM prj_village WHERE id='$row->villageid'");
        $village = mysqli_fetch_object($villageQuery);
        $accQuery = mysqli_query($con, "SELECT bankaccntno FROM prj_optr_bankdetails WHERE optrid='$op_id'");
        $accnum = mysqli_fetch_object($accQuery);
        $rateQuery = mysqli_query($con, "SELECT optrrate FROM prj_optr_rate WHERE operatorid='$op_id'");
        $rate = mysqli_fetch_object($rateQuery);
        
        // Prepare response
        $response = array(
            "project_options" => "<option value='{$project->id}'>{$project->pname}</option>",
            "subproject_options" => "<option value='{$subProject->id}'>{$subProject->spname}</option>",
            "state" => $state->sname,
            "district" => $district->distname,
            "block" => $block->blockname,
            "g_panch" => $gPanch->gpname,
            "village" => $village->villagename,
            "accnum" => base64_decode($accnum->bankaccntno),
            "rate" => $rate->optrrate
        );
    }

    // Return JSON response
    echo json_encode($response);
}

?>