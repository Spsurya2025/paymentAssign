<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
if (isset($_GET['trans_to']) && isset($_GET['organisation_id'])) {
    $trans_to = $_GET['trans_to'];
    $orga_id = $_GET['organisation_id'];
    $sql = "SELECT chquniqnm AS pr_num FROM chq_rqentry x LEFT JOIN chqissueentry y ON x.id=y.reqno WHERE x.`organisation`='$orga_id' AND x.`status`='1' AND x.`aprvl_status`='3' AND y.recvdstatus='2'";
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