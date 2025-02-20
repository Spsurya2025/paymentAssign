<?php include("../../../config.php"); ?>

  
<!-- End of Scripts -->
<script>
    $(document).ready(function () {
      $('#op_id').select2();
    });
</script>
<!-- Oerator Form -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Operator Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="suplrnm">Operator Name</label>
        <select class="form-control" name="op_id" id="op_id">
            <option value="">--Select Operator--</option>
          <?php
            $sql1 = mysqli_query($con,"SELECT id,operatorid,operatorname FROM prj_optr_master WHERE status='1'");
            while($result1 = mysqli_fetch_object($sql1)){
          ?>
         <option value="<?php echo $result1->operatorid;?>"><?php echo $result1->operatorname;?></option>
         <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="project">Project</label>
        <select class="form-control" name="project" id="project" readonly>
          <option value=""></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="subproject">Sub Project</label>
        <select class="form-control" name="subproject" id="subproject" readonly>
          <option value=""></option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="state">State</label>
        <input type="text" class="form-control" name="state" id="state" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="district">District</label>
        <input type="text" class="form-control"  name="district" id="district" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="block">Block</label>
        <input type="text" class="form-control" name="block" id="block" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="g_panch">Gram Panchayat</label>
        <input type="text" class="form-control" name="g_panch" id="g_panch" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="village">Village</label>
        <input type="text" class="form-control" name="village" id="village" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="accnum">Account No.</label>
        <input type="text" class="form-control" name="op_accno" id="accnum" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="rate">Operator Rate</label>
        <input type="text" class="form-control" name="op_rate" id="rate" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="month">Number of months</label>
        <input type="text" class="form-control" name="op_mnth" id="month" pattern="[0-9]+" onKeyPress="if(this.value.length==2) return false;" oninput="this.value=this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" onkeyup="calc();">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="total_amt">Total Amount</label>
        <input type="text" class="form-control" name="op_req_amt" id="total_amt" readonly>
      </div>
    </div>
  </div>
</div>

<!-- End of operator Form -->
<script type="text/javascript">
  function calc() {
    var rate = parseFloat($("#rate").val()) || 0.0;
    var month = $("#month").val() || 0;
    var all_total = parseFloat(rate) * month;
    $("#total_amt").val(all_total.toFixed(2));
  }
</script>
 <script>
  $("#op_id").change(function () {
    const selectedValue = $(this).val();
    $.ajax({
      url: "<?php echo SITE_URL; ?>/basic/finance/payment_assign/operator_pay_assign/get_optr_details.php",
      type: 'GET',
      data: { optr_id: selectedValue }, // Fix data format
      success: function (response) {
        const data = JSON.parse(response); // Parse JSON response
        // Populate fields with response data
        $("#project").html(data.project_options);
        $("#subproject").html(data.subproject_options);
        $("#state").val(data.state);
        $("#district").val(data.district);
        $("#block").val(data.block);
        $("#g_panch").val(data.g_panch);
        $("#village").val(data.village);
        $("#accnum").val(data.accnum);
        $("#rate").val(data.rate);
      },
      error: function () {
        alert("Failed to fetch data");
      }
    });
  });
 </script>