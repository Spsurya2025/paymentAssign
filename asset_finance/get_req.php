<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['trans_to']) && isset($_GET['organisation_id'])) {
    $trans_to = $_GET['trans_to'];
    $orga_id = $_GET['organisation_id'];
    $query = "SELECT id AS pay_request_id, astfinl_id AS pr_num FROM hr_assetfinance_request h WHERE h.status = '1' AND h.org_id = '$orga_id' AND h.f_appr = '4' AND NOT EXISTS (SELECT 1 FROM fin_payment_entry fpe WHERE fpe.preqnum = h.astfinl_id);";
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