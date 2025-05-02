<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#fdno').select2();
      $('#prjctnm').select2();
      $('#sbprjctnm').select2();
      $('#othres').select2();
      
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
</script>
<script>
  $("#fdno").change(function () {
    $("#oth_amount").val('');
    const selectedValue = $(this).val();
    $.ajax({
      url: "<?php echo SITE_URL; ?>/basic/finance/payment_assign/fd_pay_assign/get_fd_details.php",
      type: 'GET',
      data: { fdno: selectedValue }, // Fix data format
      success: function (response) {
        const data = JSON.parse(response); // Parse JSON response
        // Populate fields with response data
        $("#f_amount").val(data.f_amt);
        $("#amount").val(data.f_amt);
      },
      error: function () {
        alert("Failed to fetch data");
      }
    });
  });
</script>
<!-- FD -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">FD Payment Details</h4></center>
  <div class="col-lg-12">
  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="org_id">FD No.<span style="color:red">*</span></label>
        <select class="form-control" name="fdno" id="fdno">
        <option value="">-- Select FD No. --</option>
        <?php
          $orgdd = mysqli_query($con, "SELECT id,fd_no FROM `fin_fddtls` WHERE `status` IN ('9', '5')");
          $total_results = mysqli_num_rows($orgdd);
          if($total_results>0)
          {
            while ($fthodd = mysqli_fetch_object($orgdd)) { 
            echo "<option value='".$fthodd->id ."'>".$fthodd->fd_no."</option>";
            } 
          }else{
            echo "<option value=''>No FD Found</option>";
          }
        ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="prj_name" id="prjctnm">
          <option value="">--- Select Project ---</option>
        <?php
          $prjqr = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `status`='1' AND (ptype_org='$_GET[org_id]' OR ptype='Corporate')");
          while ($prjnm = mysqli_fetch_object($prjqr)) {
            echo "<option value='$prjnm->id'>".$prjnm->pname."</option>";
          }
        ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sbprjctnm">Sub Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="sprj_name" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="purpose">Purpose<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="fdpurpose" id="purpose">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="amount">FD Amount</label>
        <input type="text" class="form-control" name="fd_rqst_amt" id="f_amount" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="amount">Other Charges Reason</label>
        <select class="form-control" name="other_reason" id="othres">
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
        <label for="amount">Other Charges</label>
        <input type="text" class="form-control" name="other_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*)\\./g, '$1')" id="oth_amount" onkeyup="calc();">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="amount">Total Amount</label>
        <input type="text" class="form-control" name="total_amt" id="amount" readonly>
      </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="message">Message<span style="color:red">*</span></label>
        <textarea name="fdmessage" id="message" class="form-control"></textarea>
      </div>
    </div>
  </div>        
</div>

<!-- End of FD -->
 <script>
  // Function to calculate total
  function calc() {
    var fd_amt = parseFloat($("#f_amount").val()) || 0.00;
    var oth_amt = parseFloat($("#oth_amount").val()) || 0.00;
    var all_total = parseFloat(fd_amt) + parseFloat(oth_amt);
    $("#amount").val(all_total.toFixed(2));
  }
  $(document).ready(function () {
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

    
  });
 </script>