<?php
require_once('../../../config.php');

if ($_POST['type'] == 'head_debit') {
    if (isset($_POST['othrhead'])) {
        $othrhead = mysqli_real_escape_string($con, $_POST['othrhead']);
        $oth_org = mysqli_real_escape_string($con, $_POST['org_id']);
        $sql = mysqli_query($con, "SELECT `lnkwith`, `col_nm` FROM `fin_grouping_subtype` WHERE `id`='$othrhead'");
        $res = mysqli_fetch_object($sql);

        $lnkwith = $res->lnkwith;
        $col_nm = $res->col_nm;

        if (empty($lnkwith) || $lnkwith == 'Indivisual') {
            $request_number = "";
            $linkwith_data = 'Indivisual';

        } else {
            $linkwith_data = '';
            $query = "SELECT pr_num FROM fin_all_pay_request fpr LEFT JOIN fin_payment_request_others fqrd ON fpr.pay_request_id=fqrd.payreq_id
                      WHERE fpr.payreq_status = '1' 
                      AND fqrd.othrhead = '$othrhead'
                      AND fpr.payment_status = '0' 
                      AND fpr.bank_payment_sts = '1' 
                      AND fpr.request_for = 'Others'
                      AND fpr.organisation_id = '$oth_org' AND NOT EXISTS (SELECT 1 FROM fin_payment_entry fpe WHERE fpe.preqnum = fpr.pr_num);";

            $result = mysqli_query($con, $query);
            $request_number = "<option value=''>--- Select Request Number ---</option>";

            while ($row = mysqli_fetch_assoc($result)) {
                $request_number .= "<option value='{$row['pr_num']}'>{$row['pr_num']}</option>";
            }
        }

        // Prepare response
        echo json_encode(
            [
                "request_num" => $request_number,
                "linkwith" => $linkwith_data
            ]
        );
        exit;
    }
}
else if($_POST['type'] == 'req_num')
{
    if (isset($_POST['req_num'])) {
        $othrreq = mysqli_real_escape_string($con, $_POST['req_num']);
        $othqr = mysqli_query($con, "SELECT * FROM fin_payment_request_others where oreqnum = '" . $othrreq . "'");
        $fthoth = mysqli_fetch_object($othqr);
        $gtpnm = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `id`='$fthoth->prjid'");
        $fthpnm = mysqli_fetch_object($gtpnm);
        $prj_id = "<option value='".$fthpnm->id."'>".$fthpnm->pname."</option>";
        $spqr = mysqli_query($con, "SELECT id,spname FROM `prj_subproject` WHERE `id`='$fthoth->subprj_id'");
        $sprjnm = mysqli_fetch_object($spqr);
        $subprid = "<option value='".$sprjnm->id."'>".$sprjnm->spname."</option>";
        $sql2 = mysqli_query($con, "SELECT `lnkwith`, `col_nm` FROM `fin_grouping_subtype` WHERE `id`='$fthoth->othrhead'");
        $result1 = mysqli_fetch_object($sql2);
        $col_nm = $result1->col_nm;
        $lnkwith = $result1->lnkwith;
        $gtprtclr = mysqli_query($con, "SELECT `id`, $col_nm FROM $lnkwith WHERE `id`='$fthoth->prtclr'");
        $fthprtclr = mysqli_fetch_object($gtprtclr);
        $pay_to_cr = "<option value='".$fthprtclr->id."'>".$fthprtclr->$col_nm."</option>";
        echo json_encode(
            [
                "prjid" => $prj_id,
                "subprid" => $subprid,
                "pay_to_cr" => $pay_to_cr,
                "requested_amt" => $fthoth->reqst_amt,
                "pay_req_id" => $fthoth->payreq_id
            ]
        );
        exit;
    }

}
?>
