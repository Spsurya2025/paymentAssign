<?php include("../../../config.php"); ?>
<script>
  $(document).ready(function(){
    $("#othrhead").change(function () {
        var headdid = $(this).val();
        var orgid = $("#ot_organization").val();
        $("#request_num_dr").html('<option value="">--- Select request number ---</option>');
        $("#prjnm").val('').trigger('change');
        $("#subprjnm").html('');
        $("#prjnm_req_num").html('');
        $("#subprjnm_req_num").html('');
        $("#paytcr").html('');
        $("#requested_amt").val('');
        $("#requested_amt_oth").val('');
        $("#pay_rqst_id").val('');
        $("#othres").val('').trigger('change');
        $("#oth_amount").val('');
        if (headdid !== "") {
          $.ajax({
              url: "<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/get_data_oth.php",
              type: "POST",
              data: { othrhead: headdid, type: "head_debit", org_id:orgid},
              dataType: "json",
              success: function (response) {
                  if(response.linkwith == 'Indivisual')
                  {
                      $("#showContent").hide();
                      $("#showContentInd").show();
                      $("#linkedwith").val(response.linkwith)
                  }
                  else
                  {
                      $("#showContent").show();
                      $("#showContentInd").hide();
                      if (response.request_num) {
                          $("#request_num_dr").html(response.request_num);
                      } 
                      $("#linkedwith").val(response.linkwith)
                  } 
              },
              error: function () {
                alert("Error fetching data.");
              }
          });
        }
        else
        {
          $("#othres").val('');
          $("#showContent").hide();
          $("#showContentInd").hide();
          $("#showContentOth").hide();
        }
    });  
    $("#request_num_dr").change(function(){
      var requ_num = $(this).val();
      if (requ_num != "") {
        $('#othres').prop('disabled', false);
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/get_data_oth.php",
          data:{req_num:requ_num,type:'req_num'},
          type:'POST',
          dataType: "json",
          success:function(response) {
            $("#prjnm_req_num").html(response.prjid);
            $("#subprjnm_req_num").html(response.subprid);
            $("#paytcr").html(response.pay_to_cr);
            $("#requested_amt").val(response.requested_amt);
            $("#requested_amt_oth").val(response.requested_amt);
            $("#pay_rqst_id").val(response.pay_req_id);
            $("#showContentOth").show();
            calc();
          }
        });
      }
      else{
        $("#prjnm_req_num").html('');
        $("#subprjnm_req_num").html('');
        $("#paytcr").html('');
        $("#requested_amt").val('');
        $("#requested_amt_oth").val('');
        $("#pay_rqst_id").val('');
        $("#othres").val('').trigger('change');
        $("#oth_amount").val('');
        $('#othres').prop('disabled', true);
      }
    });  
    $("#prjctnm").change(function(){
      $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
      var prjid = $(this).val();
      if (prjid != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/get_data.php",
          data:{prjid:prjid,type:'prjnm'},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#sbprjctnm").html(rslt);
          }
        });
      }
    }); 
    $('#othres').select2();
    // Disable the 'Other Amount' field initially
    $('#oth_amount').prop('disabled', true);
   
    // Enable 'Other Amount' field when a valid option is selected
    $('#othres').on('change', function () {
        if ($(this).val() === '') {
            $('#oth_amount').prop('disabled', true).val('');
            calc();
        } else {
            $('#oth_amount').prop('disabled', false);
        }
    });
    // const subtotal = parseFloat($("#requested_amt").val()) || 0.00;
    // $("#requested_amt").val(subtotal.toFixed(2));
    // Function to calculate total
    function calc() {
      const subtotal = parseFloat($("#requested_amt_oth").val()) || 0.00;
      const othAmt = parseFloat($("#oth_amount").val()) || 0.00;
      const grandTotal = subtotal + othAmt;
      $("#requested_amt").val(grandTotal.toFixed(2));
    }

    // Trigger calc when 'Other Amount' is changed
    $('#oth_amount').on('input', calc); 
  });
</script>
<script>
    $(document).ready(function () {
      $('#othrhead').select2();
      $('#ptcrt').select2();
      $('#prjctnm').select2();
      $('#sbprjctnm').select2();
      $('#request_num_dr').select2();
    });
</script>
<!-- Other Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Other Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="orga_name">Organization Name</label>
        <select class="form-control" name="ot_organization_name" id="ot_organization" readonly>
          <?php
            if (isset($_GET['organisation_id'])) {
                $org_id = $_GET['organisation_id'];
                $sql1 = mysqli_query($con, "SELECT id,organisation FROM prj_organisation WHERE id='$org_id'");
                $fthorg = mysqli_fetch_object($sql1);
                echo "<option value='".$fthorg->id."'>".$fthorg->organisation."</option>";
            }
          ?>
        </select>
        
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="othrhead">Head <span style="color:red">*</span></label>
        <select class="form-control" name="othrhead" id="othrhead">
            <option value="">---Select Head---</option>
            <?php
              $gethdqr = mysqli_query($con, "SELECT `id`,`subtypenm` FROM `fin_grouping_subtype` WHERE `status`='1'");
              while($fchhd = mysqli_fetch_object($gethdqr)){
                echo "<option value='".$fchhd->id."'>".$fchhd->subtypenm."</option>";
              }
            ?>
        </select>
        <input type="hidden" class="form-control" name="linkedwith" id="linkedwith">
      </div>
    </div>
    <div id="showContent" style="display:none">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="ptcrt">Request Number<span style="color:red"> This additional request number is required when the grouping subtype is not 'individual'.</span></label>
                <select class="form-control" name="oth_req_num" id="request_num_dr">
                 <option value="">--- Select Requset Number ---</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="prjnm">Project Name <span style="color:red">*</span></label>
                <select class="form-control" name="prjnm_req_num" id="prjnm_req_num" readonly>
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="subprjnm">Sub Project Name <span style="color:red">*</span></label>
                <select class="form-control" name="subprjnm_req_num" id="subprjnm_req_num" readonly>
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="paytcr">Payment to be Credit to <span style="color:red">*</span></label>
                <select class="form-control" name="paytcr" id="paytcr" readonly>
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="requested_amt">Requested Amount <span style="color:red">*</span></label>
                <input type="text" class="form-control" name="requested_amt" id="requested_amt_oth" readonly>
                <input type="hidden" name="pay_rqst_id" id="pay_rqst_id">
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
            <label for="trnsp_req_amt">Other Charges Reason</label>
            <select class="form-control select2" name="other_reason" id="othres">
              <option value="">---Select---</option>
              <?php 
                $queryoth = mysqli_query($con, "SELECT id,subtypenm FROM fin_grouping_subtype WHERE lnkwith LIKE 'Indivisual'");
                while($other = mysqli_fetch_object($queryoth))
                {
                  echo "<option value='$other->id'>".$other->subtypenm."</option>"; 
                }
              ?>
            </select>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
            <label for="trnsp_req_amt">Other Charges Amount</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
              <input type="text" class="form-control" name="other_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*)\\./g, '$1')" id="oth_amount">
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="requested_amt">Total Amount <span style="color:red">*</span></label>
                <input type="text" class="form-control" name="requested_amt_oth" id="requested_amt" readonly>
            </div>
        </div>
      </div>
      <!-- <div id="showContentOth" style="display:none">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
            <label for="trnsp_req_amt">Other Charges Reason</label>
            <select class="form-control select2" name="other_reason" id="othres">
              <option value="">---Select---</option>
              <?php 
                $queryoth = mysqli_query($con, "SELECT id,subtypenm FROM fin_grouping_subtype WHERE lnkwith LIKE 'Indivisual'");
                while($other = mysqli_fetch_object($queryoth))
                {
                  echo "<option value='$other->id'>".$other->subtypenm."</option>"; 
                }
              ?>
            </select>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
            <label for="trnsp_req_amt">Other Charges Amount</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
              <input type="text" class="form-control" name="other_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*)\\./g, '$1')" id="oth_amount">
            </div>
          </div>
        </div>
    </div> -->
    <div id="showContentInd" style="display:none">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
              <label for="prjctnm">Project Name <span style="color:red">*</span></label>
              <select class="form-control" name="prjnm" id="prjctnm">
              <option value="">--- Select Project ---</option>
              <?php
              $prjqr = mysqli_query($con, "SELECT * FROM `prj_project` WHERE `status`='1' AND (ptype_org='$_GET[organisation_id]' OR ptype='Corporate')");
              while ($prjnm = mysqli_fetch_object($prjqr)) {
                  echo "<option value='$prjnm->id'>".$prjnm->pname."</option>";
              }
              ?>
              </select>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
          <div class="form-group">
              <label for="sbprjctnm">Sub Project Name <span style="color:red">*</span></label>
              <select class="form-control" name="subprjnm" id="sbprjctnm">
              <option value="">--- Select Sub Project ---</option>
              </select>
          </div>
        </div>
    </div>
    
    
  </div>        
</div>
<!-- End Other Payment Details form -->
<script>
  $(document).ready(function () {
    
    
  });
</script>
