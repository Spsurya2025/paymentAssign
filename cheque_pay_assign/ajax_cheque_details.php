<?php 
require_once('../../../auth.php');
require_once('../../../config.php');
$cheque_id = mysqli_real_escape_string($con, $_POST['cheque_id']);
if (isset($cheque_id)) {
    $sql = mysqli_query($con,"SELECT * FROM chqissueentry WHERE `id`='".$cheque_id."'");
    $fetch = mysqli_fetch_object($sql);
    $purpose = '';
    if($fetch->prps_rl_nm==0) {
    $purpose = "OTHERS(SPECIFY DETAILS)";
    }else if($fetch->prps_rl_nm==2){  
    $purpose = "Insurance";
    }else if($fetch->prps_rl_nm==3){  
    $purpose = "Finance";          
    }else if($fetch->prps_rl_nm==4){  
    $purpose = "New account Opening";
    }else if($fetch->prps_rl_nm==5){
    $purpose = "For Unsecured Loan";
    }else if($fetch->prps_rl_nm==6){
    $purpose = "DD";
    }else if($fetch->prps_rl_nm==7){
    $purpose = "FD";
    }else if($fetch->prps_rl_nm==8){
    $purpose = "Vendor";
    }else if($fetch->prps_rl_nm==9){
    $purpose = "Transporter";
    }else if($fetch->prps_rl_nm==10){
    $purpose = "Employee";
    }else if($fetch->prps_rl_nm==1){
    $purpose = "SUPPLY";
    }
    if($fetch->prps_typ=='0'){
      $suplid = '0';
      $suplrnm=$fetch->party_other_nm;
    }else{
        $sql = "SELECT id,`supplier_name` FROM prj_supplier  where `status`='1' and `id`='$fetch->party_rl_nm' ";
        $res = mysqli_query($con, $sql);
        $fthsplr = mysqli_fetch_object($res);
        $suplrnm=$fthsplr->supplier_name;
        $suplid=$fthsplr->id;
    }
    $response = array(
        "status" => "success",
        "purpose" => $purpose,
        "client" => "<option value='{$suplid}'>{$suplrnm}</option>",
        "issuedate" => $fetch->issuedate,
        "req_amount" => $fetch->chqrqamnt,
        "amount" => $fetch->amount,
        "req_by" => $fetch->created_by,
        "reqno" => $fetch->reqno
    );
    echo json_encode($response);    
}

?>