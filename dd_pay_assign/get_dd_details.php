<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['ddno'])) {
    $ddno = $_GET['ddno'];
    // Fetch dd details
    $response = array();
    $sql = mysqli_query($con, "SELECT dd_amt,purpose_dd FROM `fin_ddtls` WHERE `id`='$ddno'");
    $ddDetails = mysqli_fetch_object($sql);
    // Prepare response
    $response = array(
        "d_amt" => $ddDetails->dd_amt,
        "purpose" => $ddDetails->purpose_dd,
    );
    
    // Return JSON response
    echo json_encode($response);
}
?>
