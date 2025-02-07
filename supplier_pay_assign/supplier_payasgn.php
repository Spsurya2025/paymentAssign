
<?php include("../../../config.php"); ?>
<?php
// Ensure the required variable is set
if (!isset($_GET['py_req_id']) ) {
    echo "<p style='color: red;'>Error: Payment Request ID is missing.</p>";
    exit;
}
if (!isset($_GET['request_num'])) {
  echo "<p style='color: red;'>Error: Payment Request number is missing.</p>";
  exit;
}else{
  $request_no = $_GET['request_num'];
}
?>
<!-- End of Scripts -->
<!-- Supplier Form -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Supplier Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="suplrnm">Supplier Name</label>
        <select class="form-control" name="suplrnm" id="suplrnm" readonly>
          <?php
              if(isset($_GET['py_req_id'])){
              $pay_req_id = $_GET['py_req_id'];
              $splquery = "SELECT y.id as spl_id, y.supplier_name, z.pname, z.id AS prj_id, x.*, x.id AS spleqid FROM `fin_payment_request_supplier` x, `prj_supplier` y, `prj_project` z WHERE x.payreq_id='$pay_req_id' AND x.`prjctnm`=z.`id` AND x.splrnm=y.id";
              $spldetail = mysqli_query($con, $splquery);
              $fthsplr = mysqli_fetch_object($spldetail);
                echo "<option value='".$fthsplr->spl_id."'>".$fthsplr->supplier_name."</option>";
              }
          ?>
        </select>
        <input type="hidden" name="pay_rqst_id" value="<?php echo $_GET['py_req_id'];?>">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="s_organization_name" id="s_organization" readonly>
          <?php
            if (isset($_GET['py_req_id'])) {
                $pay_rid = $_GET['py_req_id'];
                $sql1 = mysqli_query($con, "SELECT y.id,y.organisation FROM `fin_all_pay_request` x, `prj_organisation` y WHERE x.`pay_request_id`='$pay_rid' AND x.`organisation_id`=y.`id`");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">Request No.</label>
        <?php
              if(isset($_GET['py_req_id']))
              {
                $all_req = mysqli_query($con, "SELECT pr_num FROM `fin_all_pay_request` WHERE pay_request_id='$pay_req_id'");
                $result_all = mysqli_fetch_object($all_req);
              }
        ?>
        <input type="text" name="podate" id="podate" value="<?php echo $result_all->pr_num; ?>" class="form-control" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prj_name">Project Name</label>
        <select class="form-control" name="prj_name" id="prj_name" readonly>
          <option value="<?php echo $fthsplr->prj_id; ?>"><?php echo $fthsplr->pname; ?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">PO Number</label>
        <select class="form-control" name="ponum" id="ponum" readonly>
          <option value="<?php echo $fthsplr->po_num;?>"><?php echo $fthsplr->po_num;?></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">PO Date</label>
        <input type="text" name="podate" id="podate" value="<?php echo $fthsplr->podt; ?>" class="form-control" placeholder="yyyy-mm-dd" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="poamnt">PO Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="poamnt" value="<?php echo $fthsplr->poamt; ?>" id="poamnt" placeholder="9999.99" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="poamnt">Transportation Amount:</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="transptamt" value="<?php echo $fthsplr->trnsprtamt; ?>" id="transptamt" placeholder="9999.99" readonly>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="poamnt">Inspection Amount:</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="inspamt" value="<?php echo $fthsplr->inspcamt; ?>" id="inspamt" placeholder="9999.99" readonly>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
  </div>
    <div class="col-lg-12" id="prDtls">
      <div class="col-lg-12">
        <legend><h6><strong style="color: #2bc59b;">PR Details</strong></h6></legend>
        <div class="table-responsive">
          <table class="table table-bordered table-responsive">
            <thead>
              <tr>
                <th>#</th>
                <th style="width: 120px;">Request No.</th>
                <th>PR No.</th>
                <th>Sub Project</th>
                <th>BMS</th>
                <th>Work Desc(Alias)</th>
                <th>PR Amount</th>
                <th>Request Amount</th>
                <th>Paid</th>
                <th>In process</th>
                <th>Balance</th>
              </tr>
            </thead>
            <tbody>
             <?php
               $prqr = mysqli_query($con, "SELECT * FROM `fin_payment_request_supplier_pr` WHERE `payreq_id`='$fthsplr->payreq_id' AND `splr_req_id`='$fthsplr->spleqid' AND `po_num`='$fthsplr->po_num' ");
               $a = 1;
               $po_num = $fthsplr->po_num;
               while ($fthprs = mysqli_fetch_object($prqr)) {
                $inprocessamounts = mysqli_query($con, "SELECT SUM(reqamt) AS sum_pay FROM fin_payment_request_supplier_pr WHERE po_num ='$po_num'");
                $processamts = mysqli_fetch_object($inprocessamounts);
                $paidamounts = mysqli_query($con, "SELECT SUM(paid_amt) AS paid_sum FROM fin_payment_request_supplier_pr WHERE po_num ='$fthsplr->po_num' and `status`='1'");
                $paidamts = mysqli_fetch_object($paidamounts);
                $singleinprocessamount = mysqli_query($con, "SELECT SUM(reqamt) AS sum_singpay FROM fin_payment_request_supplier_pr WHERE po_num ='$fthsplr->po_num' AND sreqnum ='$fthprs->sreqnum' AND `status`='1'");
                $singleprocessamt = mysqli_fetch_object($singleinprocessamount);
             ?>
              <!-- <td><?= $row['name']; ?><input type="hidden" name="name[<?= $row['id']; ?>]" value="<?= $row['name']; ?>"></td> -->
              <tr>
                <td>
                 <?php 
                  if(!isset($_GET['peid'])){
                    if($fthprs->sreqnum == $request_no){?>
                      <input type="checkbox" name="pr_data[]" value="<?php echo $fthprs->id;?>" id="pr_data_id">
                    <?php }}?>
                </td>
                <td>
                  <select class="form-control" readonly>
                    <option><?php echo $fthprs->sreqnum; ?></option>
                  </select>
                </td>
                <td><input type="text" class="form-control" name="pr_numbr[<?=$fthprs->id;?>]" id="pr_numbr" value="<?php echo $fthprs->prnum; ?>" readonly></td>
                <td>
                  <input type="text" class="form-control" name="subprj_nm[<?=$fthprs->id;?>]" id="subprj_nm" value="<?php echo $fthprs->subprj ? $fthprs->subprj : "Not Available";; ?>" placeholder="Sub Project Name" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" name="bms_name[<?=$fthprs->id;?>]" id="bms_name" value="<?php echo $fthprs->bmsnm ? $fthprs->bmsnm : "Not Available"; ?>" placeholder="BMS Name" readonly>
                </td>
                <td>
                <?php
                   $work = '';
                   $sub_projectid = mysqli_query($con, "SELECT y.`id` FROM `prj_subproject` y WHERE  y.`spname`='$fthprs->subprj'");
                   $spftchd = mysqli_fetch_object($sub_projectid);
                   $bms_qry = mysqli_query($con, "SELECT y.`alias_name` FROM `prj_sub_bms` y WHERE  y.`sub_bms`='$fthprs->bmsnm' AND  y.`project_id`='$fthsplr->prjctnm' AND y.`sproject_id` = '$spftchd->id'");
                   $bms_ftch = mysqli_fetch_object($bms_qry);
                   if (!empty($bms_ftch->alias_name)) {
                       $work = $bms_ftch->alias_name;
                   } else {
                       $work = "Not Available";
                   };
                ?>
                  <input type="text" class="form-control" name="" id="" value="<?php echo $work;?>" readonly></td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                    <input type="text" name="pramnt[<?=$fthprs->id;?>]" id="pramnt" class="form-control" value="<?php echo $fthprs->pramt; ?>" placeholder="9999.99" readonly>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                    <input type="text" name="pr_reqamt[<?=$fthprs->id;?>]" id="pr_reqamt" class="form-control" value="<?php echo $fthprs->reqamt; ?>" placeholder="9999.99" readonly>
                    <input type="hidden" name="reqamt_p" id="reqamt" class="form-control" value="<?php echo $fthprs->reqamt; ?>" placeholder="9999.99" readonly>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                    <input type="text" name="paid" id="paid" class="form-control" value="<?php echo $fthprs->paid_amt; ?>" placeholder="9999.99" readonly>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                     <?php $process = $processamts->sum_pay - $paidamts->paid_sum;
                      ?>
                    <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                    <input type="text" name="process" id="process" class="form-control" value="<?php echo number_format((float)$process, 2, '.', ''); ?>" placeholder="9999.99" readonly>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                     <?php 
                        $balanc = $fthprs->pramt - $processamts->sum_pay;
                      ?>
                    <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                    <input type="text" name="balance" id="balance" class="form-control" value="<?php echo number_format((float)$balanc, 2, '.', ''); ?>" placeholder="9999.99" readonly>
                  </div>
                </td>
              </tr>
              <?php $a++;
              } ?>
            </tbody>
            <tbody>
                <tr>
                    <th colspan="3">Total PR Amount: </th>
                    <th><input type="text" class="form-control" id="total_re_amount" readonly></th>
                </tr>
              </tbody>
            
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-12" id="trDtls">
      <div class="col-lg-12">
        <legend><h6><strong style="color: #2bc59b;">Other Details</strong></h6></legend>
        <div class="table-responsive">
          <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sl No.</th>
                    <th>Request No.</th>
                    <th>Other Reason</th>
                    <th>Request Amount</th>
                    <th>Paid Amount</th>
                    <th>Balance Amount</th>
                </tr>
            </thead>
            <?php
            $prq_id = $fthpr->id;
            $splr_id = $fthsplr->id;
            $strqr = mysqli_query($con, "SELECT x.*,y.subtypenm,y.id AS subtype_id FROM `fin_payment_request_supplier_transport` x, `fin_grouping_subtype` y WHERE x.`trns_rsn`=y.`id` AND x.`payreq_id`='$fthsplr->payreq_id' AND x.`splr_req_id`='$fthsplr->spleqid'");
            $q = 1;
            while ($fchtr = mysqli_fetch_object($strqr)) {
            ?>
            <tbody>
                <tr>
                  <td>
                    <?php if($fchtr->streqnum == $request_no){?>
                    <input type="checkbox" name="tr_data[]"  id="tr_data_id" value="<?php echo $fchtr->id;?>">
                    <?php } ?>
                  </td>
                  <td><?php echo $q; ?></td>
                  <td><input type="text" class="form-control" value="<?php echo $fchtr->streqnum; ?>" readonly></td>
                  <td>
                    <input type="text" class="form-control" value="<?php echo $fchtr->subtypenm; ?>" readonly>
                    <input type="hidden" class="form-control" name="trnsrsn[<?=$fchtr->id;?>]" value="<?php echo $fchtr->subtype_id; ?>" readonly>
                  </td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                      <input type="text" name="trreqamt[<?=$fchtr->id;?>]" id="trreqamt" class="form-control" value="<?php echo $fchtr->trans_req; ?>" placeholder="9999.99" readonly>
                      <input type="hidden" name="trreqamt_d" id="trreqamt" class="form-control" value="<?php echo $fchtr->trans_req; ?>" placeholder="9999.99" readonly>
                    </div>
                  </td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                      <input type="text" name="tr_paid" id="tr_paid" class="form-control" value="<?php echo $fchtr->trans_paid_amount; ?>" placeholder="9999.99" readonly>
                   </div>
                  </td>
                  <td>
                    <div class="input-group">
                     <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
                     <input type="text" name="tr_paid" id="tr_paid" class="form-control" value="<?php echo $fthprs->trans_paid_amount; ?>" placeholder="9999.99" readonly>
                    </div>
                  </td>
               </tr>
            </tbody>
            <?php $q++;
              } ?>
              
              <tbody>
                <tr>
                    <th colspan="3">Total Transportation Amount: </th>
                    <th><input type="text" class="form-control" id="total_tr_amount" value="0.00" readonly> </th>
                    <th>Total Requested amount</th>
                    <th>
                      <input class="form-control" type="text" id="all_total" value="0.00" readonly>
                      <span id="amt-error" class="error-message"></span>
                    </th>
                </tr>
              </tbody>
          </table>
        </div>
      </div>
    </div>  
</div>
<script>
    $(document).ready(function () {
        function updateTotalReqAmt() {
            let total = 0;
            $('input[name="pr_data[]"]:checked').each(function () {
                const reqAmtField = $(this).closest('tr').find('input[name="reqamt_p"]');
                if (reqAmtField.length) {
                    total += parseFloat(reqAmtField.val()) || 0;
                }
            });
            $('#total_re_amount').val(total.toFixed(2));
            updateGrandTotal(); 
        }

        // Function to calculate total for Transportation Amount
        function updateTotalTrAmt() {
            let total_tr = 0;
            $('input[name="tr_data[]"]:checked').each(function () {
                const reqAmtField_tr = $(this).closest('tr').find('input[name="trreqamt_d"]');
                if (reqAmtField_tr.length) {
                    total_tr += parseFloat(reqAmtField_tr.val()) || 0;
                }
            });
            $('#total_tr_amount').val(total_tr.toFixed(2));
            updateGrandTotal();
        }

        // Function to update the grand total
        function updateGrandTotal() {
            const totalReqAmt = parseFloat($('#total_re_amount').val()) || 0;
            const totalTrAmt = parseFloat($('#total_tr_amount').val()) || 0;
            const grandTotal = totalReqAmt + totalTrAmt;

            $('#all_total').val(grandTotal.toFixed(2));
        }
        $('input[name="pr_data[]"]').on('change', updateTotalReqAmt);
        $('input[name="tr_data[]"]').on('change', updateTotalTrAmt);
    });
</script>

<!-- End of Supplier Form -->