<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['fdno'])) {
    $fdno = $_GET['fdno'];
    $response = array();
    $sql = mysqli_query($con, "SELECT fd_amt FROM `fin_fddtls` WHERE `id`='$fdno'");
    $fdDetails = mysqli_fetch_object($sql);
    $response = array(
        "f_amt" => $fdDetails->fd_amt
    );
    echo json_encode($response);
}
?>
