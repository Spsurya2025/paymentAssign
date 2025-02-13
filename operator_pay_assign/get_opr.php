<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['trans_to']) && isset($_GET['organisation_id'])) {
    $trans_to = $_GET['trans_to'];
    $orga_id = $_GET['organisation_id'];
    $query = "SELECT pay_request_id, pr_num FROM fin_all_pay_request  WHERE payreq_status = '1' AND payment_status = '0' AND bank_payment_sts = '1' AND request_for = '$trans_to' AND organisation_id = '$orga_id'";
    $result = mysqli_query($con, $query);
    $response = [];
    while ($row = mysqli_fetch_object($result)) {  
        $response[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>