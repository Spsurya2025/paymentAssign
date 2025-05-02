<?php 
include("../../../config.php");
// Ensure the required variable is set
if (!isset($_GET['request_num'])) {
  echo "<p style='color: red;'>Error: Payment Request number is missing.</p>";
  exit;
}else{
    $request_no = mysqli_real_escape_string($con, $_GET['request_num']);
    
}
$chequedetails = mysqli_query($con, "SELECT y.*,y.id FROM `chq_rqentry` y WHERE y.status = '1' AND y.chquniqnm = '".$request_no."' ORDER BY y.id DESC");
$chequerecord = mysqli_fetch_object($chequedetails);
// echo "SELECT y.*,y.id FROM `chq_rqentry` y WHERE y.status = '1' AND y.chquniqnm = '".$request_no."' ORDER BY y.id DESC";
// echo "SELECT x.chqno,x.id as entryid FROM `chqissueentry` x WHERE x.status = '1' AND x.reqno = '".$chequerecord->id."' AND NOT EXISTS ( SELECT 1 FROM `fin_payment_entry` p WHERE p.preqnum = x.chqno ) ORDER BY x.id DESC";

?>
<script>
    function chequenochanges(value) {
      console.log("Selected Cheque ID:", value);
      $.ajax({
        url: "cheque_pay_assign/ajax_cheque_details.php",  // Corrected URL format
        type: "POST",
        data: { cheque_id: value },  // Corrected parameter name
        success: function (response) {
          const data = JSON.parse(response); // Parse JSON response
          if (data.status === "success") {
            // Populate form fields with returned data
            $("#chqpurpose").val(data.purpose);
            $("#chqclient").html(data.client);
            $("#issuedate").val(data.issuedate);
            $("#chqrqamnt").val(data.req_amount);
            $("#chqentryamnt").val(data.amount);
            $("#req_by").val(data.req_by);
            $("#reqno").val(data.reqno);
            
          }else{
            alert("Error");
          }
        },
        error: function () {
          alert("Failed to fetch data");
        }
      });
    }

</script>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Cheque Assign Details</h4></center>
    <div class="col-lg-12">
        <div class="col-lg-4">
          <div class="form-group">
            <label for="org_id">Cheque No<span style="color:red">*</span></label>
             <?php if (isset($_GET['peid'])) { ?>
              <select class="form-control" name="chqno" id="chqno" readonly>
              <?php 
               $paymentent = mysqli_query($con, "SELECT y.*,y.chqno as issue_id FROM fin_payment_entry x JOIN fin_payment_entry_chq y ON x.id=y.payent_id WHERE x.status='1'");
               $fetchquery = mysqli_fetch_object($paymentent);
               $entrydetails = "SELECT x.chqno,x.id as entryid FROM `chqissueentry` x WHERE x.status = '1' AND x.id = '".$fetchquery->issue_id."'";
               $entryrecord = mysqli_query($con,$entrydetails);
               while ($exf = mysqli_fetch_object($entryrecord))
               {
                 echo '<option value="'. $exf->entryid . '">' . $exf->chqno .'</option>';
               } 
              ?>
              </select>
             <?php }else{ ?>
              <select class="form-control" name="chqno" id="chqno" onchange="chequenochanges(this.value)">
              <option value="">-- Select Cheque No. --</option>
              <?php
                $entrydetails = "SELECT x.chqno,x.id as entryid FROM `chqissueentry` x WHERE x.status = '1' AND x.reqno = '".$chequerecord->id."' AND NOT EXISTS ( SELECT 1 FROM `fin_payment_entry_chq` p WHERE p.chqno = x.id) ORDER BY x.id DESC";
                $entryrecord = mysqli_query($con,$entrydetails);
                while ($exf = mysqli_fetch_object($entryrecord))
                {
                  echo '<option value="'. $exf->entryid . '">' . $exf->chqno .'</option>';
                }
              } ?>
            </select>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
            <label for="orga_name">Organization Name</label>
            <select class="form-control" name="ch_organization_name" id="ch_organization" readonly>
              <?php
                $sql1 = mysqli_query($con, "SELECT id,organisation FROM prj_organisation WHERE id='$chequerecord->organisation'");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
              ?>
            </select>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <label for="org_id">Purpose</label>
            <?php if (isset($_GET['peid'])) { ?>
              <input type="text" class="form-control" name="chqpurpose" id="chqpurpose" value="<?php echo $fetchquery->purpose?>" readonly>
            <?php } else { ?>
            <input type="text" class="form-control" name="chqpurpose" id="chqpurpose" value="" readonly>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="form-group">
            <label for="org_id">Client</label>
            <?php if (isset($_GET['peid'])) { ?>
              <select class="form-control" name="chqclient" id="chqclient"readonly>
                <?php
                    $sql = "SELECT id,`supplier_name` FROM prj_supplier  where `status`='1' and `id`='$fetchquery->chqclient' ";
                    $res = mysqli_query($con, $sql);
                    $fthsplr = mysqli_fetch_object($res);
                    echo "<option value='".$fthsplr->id."'>".$fthsplr->supplier_name."</option>";
                ?>
              </select>
            <?php } else { ?>
            <select class="form-control" name="chqclient" id="chqclient"readonly></select>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="form-group">
            <label for="sub_prjct">Issue Date</label>
            <?php if (isset($_GET['peid'])) { ?>
              <?php
                $sql1 = mysqli_query($con,"SELECT issuedate FROM chqissueentry WHERE `id`='".$fetchquery->issue_id."'");
                $fetch1 = mysqli_fetch_object($sql1);
              ?>
              <input type="text" class="form-control" name="chqpurpose" id="chqpurpose" value="<?php echo $fetch1->issuedate;?>" readonly>
            <?php } else { ?>
            <input type="text" class="form-control" name="issuedate" id="issuedate" value="" readonly>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="form-group">
            <label for="bmsnm">Cheque Request Amount</label>
            <?php if (isset($_GET['peid'])) { ?>
              <input type="text" class="form-control" name="chq_rqst_amt_e" id="chqrqamnt" value="<?php echo $fetchquery->chq_rqst_amt;?>" readonly>
            <?php } else { ?>
              <input type="text" class="form-control" name="chq_rqst_amt_e" id="chqrqamnt" value="" readonly>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="form-group">
            <label for="bmsnm">Cheque Entry Amount</label>
            <?php if (isset($_GET['peid'])) { ?>
              <input type="text" class="form-control" name="chq_rqst_amt" id="chqentryamnt" value="<?php echo $fetchquery->chq_rqst_amt;?>" readonly>
              <input type="hidden" class="form-control" name="req_by" id="req_by" value="<?php echo $fetchquery->req_by;?>" readonly>
              <input type="hidden" class="form-control" name="payrqstid" id="reqno" value="<?php echo $fetchquery->pay_rqst_id;?>" readonly>
            <?php } else { ?>
              <input type="text" class="form-control" name="chq_rqst_amt" id="chqentryamnt" value="" readonly>
              <input type="hidden" class="form-control" name="req_by" id="req_by" value="" readonly>
              <input type="hidden" class="form-control" name="payrqstid" id="reqno" value="" readonly>
            <?php } ?>
          </div>
        </div> 
    </div>  
    <div class="col-lg-12">
      <div class="col-lg-12">
        <label for="type">Message<span style="color:red">*</span></label>
        <textarea class="form-control" name="chqmessage" id="chqmessage"></textarea>
      </div>
    </div>
</div>