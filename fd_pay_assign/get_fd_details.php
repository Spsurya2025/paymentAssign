<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['fdno'])) {
    $fdno = $_GET['fdno'];
    // Fetch operator details
    $response = array();
    $sql = mysqli_query($con, "SELECT * FROM `fin_fddtls` WHERE `fd_no`='$fdno'");
    $fdDetails = mysqli_fetch_object($sql);
    // Prepare response
    $response = array(
        "f_amt" => $fdDetails->fd_amt
    );
    
    // Return JSON response
    echo json_encode($response);
}
?>
