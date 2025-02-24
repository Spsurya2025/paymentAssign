<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['trans_to']) && isset($_GET['organisation_id'])) {
    $trans_to = $_GET['trans_to'];
    $orga_id = $_GET['organisation_id'];
    $sql = "SELECT hsr.req_id AS pr_num FROM hr_employee_salary_report hsr JOIN hr_employee_salary_processing hsp ON hsp.id = hsr.unique_id WHERE  hsr.org_nm = '$orga_id' AND hsr.payment_status='0' AND hsp.status = '1' AND hsp.f_appr='3' AND NOT EXISTS (SELECT 1 FROM fin_payment_entry fpe WHERE fpe.preqnum = hsr.req_id);";
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