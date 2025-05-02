<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['trans_to']) && isset($_GET['organisation_id'])) {
    $trans_to = $_GET['trans_to'];
    $orga_id = $_GET['organisation_id'];
    $sql = "SELECT id AS pay_request_id,reffno AS pr_num FROM fin_loan_master WHERE `orgid`='$orga_id' AND `status`='1' AND `approvalstatus` = '2'";
    $result = mysqli_query($con, $sql);
    $response = [];
    while ($row = mysqli_fetch_object($result)) {  
        $response[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>