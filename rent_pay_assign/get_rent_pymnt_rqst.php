<?php include("../../../config.php");setlocale(LC_MONETARY, 'en_IN'); ?>
<?php if(isset($_POST['org_id'])) {?>
<legend>
    <h6><strong style="color: #2bc59b;">Rent Details</strong></h6>
</legend>
<div class="table-responsive">
    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Payment Date</th>
                <th>Client</th>
                <th>Project</th>
                <th>Sub Project</th>
                <th>Amount</th>
            </tr>
        </thead>
        <script>
            $(document).ready(function() {
                $('#choose_rnt_code').click(function() {
                    $('.rnt_code,.rnt_pymnt_dt,.client,.p_id,.sp_id,.rate').attr('disabled', false);
                    var cal_sum = 0;
                    $(this.form.elements).filter(':checkbox').prop('checked', this.checked);
                    if ($("#choose_rnt_code").is(":checked")) { 
                        $(".rate").each(function(){
                            cal_sum += +$(this).val();
                        });
                        $(".ttl_rate").val(cal_sum.toFixed(2));
                    }else{
                        $(".ttl_rate").val('');
                        //var cal_sum = 0;
                    } 
               });
            });
        </script>
        <tbody>
            <?php 
                $org_id = $_POST['org_id']; 
                $yr_nm = $_POST['yr_nm'];
                $mnth_nm = $_POST['mnth_nm'];
                $type = $_POST['type'];
                $purpose = $_POST['purpose'];
                $qry_nw = mysqli_query($con,"SELECT x.* FROM `fin_payment_request_rent_details` x,`fin_payment_request_rent` y WHERE y.`payreq_id`= x.`payreq_id` AND y.`org_id` = '$org_id' AND y.`year`='$yr_nm' AND y.`month`='$mnth_nm' AND y.`type`='$type' AND y.`purpose`='$purpose'AND y.`status`!= '6'");
                if(mysqli_num_rows($qry_nw) == 0){
                        $qry = mysqli_query($con,"SELECT x.*,y.*,k.`rate` FROM `rent_entry_details` x,`rent_emi_details` y,`rent_approval` k WHERE  x.`rent_code`=y.`rent_code` AND x.`req_no`=k.`req_no_rt` AND y.`year`='$yr_nm' AND y.`month`='$mnth_nm' AND y.`rent_type`='$type' AND y.`purpose`='$purpose' AND y.`paid_status`= '0' AND x.`is_renew`= '0'");
                        $i =1;
                        while ($fetch=mysqli_fetch_object($qry)){
                            $paydate = $fetch->pymnt_dt;
                            $paydate = explode("-", $paydate);
                            $paymentdate = $paydate[2]."-".$mnth_nm."-".$yr_nm;
                            $day = $fetch->day;
                            $month = $fetch->month;
                            $year = $fetch->year;
                            $ownerdetaisl = mysqli_query($con,"SELECT * FROM `prj_creditor` WHERE id = ".$fetch->owner_id);
                            $ownerdet = mysqli_fetch_object($ownerdetaisl);
                            $projectdetails = mysqli_query($con,"SELECT * FROM `prj_project` WHERE id = ".$fetch->prj_id);
                            $prohect = mysqli_fetch_object($projectdetails);
                            $subprojectdetails = mysqli_query($con,"SELECT * FROM `prj_subproject` WHERE id = ".$fetch->sp_id);
                            $subprohect = mysqli_fetch_object($subprojectdetails);
                        ?>
                    <tr>
                        <td><input type="radio" class="form-check-input" name="selected_row" value="<?php echo $i; ?>"> </td>
                        <td><input type="hidden" class="form-control rnt_code" name="rnt_code[<?php echo $i; ?>]" value="<?php echo $fetch->rent_code; ?>"> <?php echo $fetch->rent_code; ?></td>
                        <td><input type="hidden" class="form-control rnt_pymnt_dt" name="rnt_pymnt_dt[<?php echo $i; ?>]" value="<?php echo $year."-".$month."-".$day; ?>"><?php echo $day."-".$month."-".$year; ?></td>
                        <td><input type="hidden" class="form-control client" name="client[<?php echo $i; ?>]" value="<?php echo $fetch->owner_id; ?>"><?php echo $ownerdet->companynm; ?></td>
                        <td><input type="hidden" class="form-control p_id" name="p_id[<?php echo $i; ?>]" id="p_id<?php echo $i; ?>" value="<?php echo $fetch->prj_id; ?>"><?php echo $prohect->pname; ?></td>
                        <td><input type="hidden" class="form-control sp_id" name="sp_id[<?php echo $i; ?>]" id="sp_id<?php echo $i; ?>" value="<?php echo $fetch->sp_id; ?>"><?php echo $subprohect->spname; ?></td>
                        <td><input type="hidden" class="form-control rate" name="rate[<?php echo $i; ?>]" id="rate<?php echo $i; ?>" value="<?php echo $fetch->rate; ?>"><?php echo $fetch->rate;?></td>
                    </tr>  
                        <?php $i++; } 
                } else {
                $items = array();
                while($row = mysqli_fetch_array($qry_nw)){
                    $items[] = $row['rnt_code'];
                }
                $rentcode = "'" .implode("', '",$items). "'";
                $qry = mysqli_query($con,"SELECT x.*,y.*,k.`rate` FROM `rent_entry_details` x,`rent_emi_details` y,`rent_approval` k WHERE  x.`rent_code`=y.`rent_code` AND x.`req_no`=k.`req_no_rt` AND y.`year`='$yr_nm' AND y.`month`='$mnth_nm' AND y.`rent_type`='$type' AND y.`purpose`='$purpose' AND y.`paid_status`= '0' AND x.`is_renew`= '0' AND x.`rent_code` NOT IN ($rentcode)");
                $i =1;
                while ($fetch=mysqli_fetch_object($qry)){
                    $day = $fetch->day;
                    $month = $fetch->month;
                    $year = $fetch->year;
                    $ownerdetaisl = mysqli_query($con,"SELECT * FROM `prj_creditor` WHERE id = ".$fetch->owner_id);
                    $ownerdet = mysqli_fetch_object($ownerdetaisl);
                    $projectdetails = mysqli_query($con,"SELECT * FROM `prj_project` WHERE id = ".$fetch->prj_id);
                    $prohect = mysqli_fetch_object($projectdetails);
                    $subprojectdetails = mysqli_query($con,"SELECT * FROM `prj_subproject` WHERE id = ".$fetch->sp_id);
                    $subprohect = mysqli_fetch_object($subprojectdetails);
                ?>
            <tr>
                <td>
                    <?php 
                        $pay_dt = $fetch->pymnt_dt;
                        $end_dt = $fetch->rent_end_dt;
                        $date1_ts = strtotime($pay_dt);
                        $date2_ts = strtotime($end_dt);
                        $diff = $date2_ts - $date1_ts;
                        $final_op = round($diff / 86400); 
                        if ($final_op < 30) {
                           $dt_nw = $pay_dt;
                        }else {
                            $dt_nw = date('Y-m-d', strtotime($pay_dt. ' + 1 month'));
                        }
                    ?>
                    <input type="hidden" readonly="" class="form-control " name="rent_tbl_id" id="rent_tbl_id" value="<?php echo $fetch->id; ?>" >
                    <input type="hidden" readonly="" class="form-control " name="updt_dt" id="updt_dt" value="<?php echo $dt_nw; ?>" >   
                    <input type="radio" class="form-check-input" id="rnt_chk_bx<?php echo $i; ?>" name="selected_row" value="<?php echo $i; ?>">
                </td>
                <td><input type="hidden" class="form-control rnt_code" name="rnt_code[<?php echo $i; ?>]" value="<?php echo $fetch->rent_code; ?>"><?php echo $fetch->rent_code; ?></td>
                <td><input type="hidden" class="form-control rnt_pymnt_dt" name="rnt_pymnt_dt[<?php echo $i; ?>]" value="<?php echo $year."-".$month."-".$day;?>"  ><?php echo $day."-".$month."-".$year; ?></td>
                <td><input type="hidden" class="form-control client" name="client[<?php echo $i; ?>]" value="<?php echo $fetch->owner_id;?>" ><?php echo $ownerdet->companynm; ?></td>
                <td><input type="hidden" class="form-control p_id" name="p_id[<?php echo $i; ?>]" value="<?php echo $fetch->prj_id; ?>" ><?php echo $prohect->pname; ?></td>
                <td><input type="hidden" class="form-control sp_id" name="sp_id[<?php echo $i; ?>]" value="<?php echo $fetch->sp_id; ?>" ><?php echo $subprohect->spname; ?></td>
                <td><input type="hidden" class="form-control rate" name="rate[<?php echo $i; ?>]" value="<?php echo $fetch->rate; ?>" ><?php echo $fetch->rate;?></td>
            </tr>
            <?php $i++; } 
                }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>