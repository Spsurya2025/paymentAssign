<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['trans_to']) && isset($_GET['organisation_id'])) {
    $trans_to = $_GET['trans_to'];
    $orga_id = $_GET['organisation_id'];
    $sql = "SELECT req_no AS pr_num FROM exp_payment_request WHERE `prj_org_id`='$orga_id' AND `bank_payment_sts`='1' AND `status`='1' AND NOT EXISTS (SELECT 1 FROM fin_payment_entry fpe WHERE fpe.preqnum = exp_payment_request.req_no)";
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