<?php
include("../../../config.php");
if($_POST['type'] == 'db_type'){
    $dbtr_typ = mysqli_real_escape_string($con, $_POST['dbtr_typ']);
    $org_id = mysqli_real_escape_string($con, $_POST['org_id']);
    if($dbtr_typ != '12'){
        echo '<option value="">--- Select Project ---</option>';
            // EPC or default: Show org-specific or Corporate projects
            $query = "SELECT id, pname 
                      FROM prj_project 
                      WHERE status = '1' 
                      AND (ptype_org = '$org_id' OR ptype = 'Corporate')";
        
        $result = mysqli_query($con, $query);
        
        while ($row = mysqli_fetch_object($result)) {
            echo "<option value='{$row->id}'>{$row->pname}</option>";
        }
    }else{
        echo '<option value="">--- Select Project ---</option>';
    }
   
}else if($_POST['type'] == 'unit'){
    $project_id = mysqli_real_escape_string($con, $_POST['prjid']);
    $subprj_id = mysqli_real_escape_string($con, $_POST['subprj']);
    $query = "SELECT id, unitname FROM resco_unitbill WHERE proj='$project_id' AND subpr_name='$subprj_id' AND approval1st_status='1' AND approval2nd_status='1' ORDER by unitname ASC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_object($result)) {
        echo "<option value='{$row->id}'>{$row->unitname}</option>";
    }

}else{
    $transaction_for = mysqli_real_escape_string($con, $_POST['transaction_for']);
    $org_id = mysqli_real_escape_string($con, $_POST['org_id']);
    
    echo '<option value="">--- Select Project ---</option>';
    
    if ($transaction_for == "RESCO" || $transaction_for == "EPC") {
        // RESCO: Show only RESCO projects
        $query = "SELECT id, pname 
                  FROM prj_project 
                  WHERE status = '1' 
                  AND ptype = '$transaction_for' OR (ptype = '$transaction_for' AND ptype_org = '$org_id')";
    
    $result = mysqli_query($con, $query);
    
    while ($row = mysqli_fetch_object($result)) {
        echo "<option value='{$row->id}'>{$row->pname}</option>";
    }}
    // else{
    //     echo '<option value="">--- Select Project ---</option>';
    // }
}

?>
