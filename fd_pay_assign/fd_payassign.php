<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#fdno').select2();
      $('#prjctnm').select2();
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
    const selectedValue = $(this).val();
    $.ajax({
      url: "<?php echo SITE_URL; ?>/basic/finance/payment_assign/fd_pay_assign/get_fd_details.php",
      type: 'GET',
      data: { fdno: selectedValue }, // Fix data format
      success: function (response) {
        const data = JSON.parse(response); // Parse JSON response
        // Populate fields with response data
        $("#amount").val(data.f_amt);
      },
      error: function () {
        alert("Failed to fetch data");
      }
    });
  });
 </script>
<?php
  if (!isset($_GET['organisation_id'])) {
    echo "<p style='color: red;'>Error: Organisation name is missing.</p>";
    exit;
  }else{
    $org_id = $_GET['organisation_id'];
  }
?>
<!-- FD -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">FD Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
        <div class="form-group">
            <label for="org_id">FD No.</label>
            <select class="form-control" name="fdno" id="fdno">
            <option value="">-- Select FD No. --</option>
            <?php
                $orgdd = mysqli_query($con, "SELECT fd_no FROM `fin_fddtls` WHERE `status`='1' AND `orgnist`='$org_id'");
                $total_results = mysqli_num_rows($orgdd);
                if($total_results>0)
                {
                    while ($fthodd = mysqli_fetch_object($orgdd)) { 
                    echo "<option value='".$fthodd->fd_no ."'>".$fthodd->fd_no."</option>";
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
        <label for="prjctnm">Project Name</label>
        <select class="form-control" name="prj_name" id="prjctnm">
          <option value="">--- Select Project ---</option>
        <?php
          $prjqr = mysqli_query($con, "SELECT * FROM `prj_project` WHERE `status`='1'");
          while ($prjnm = mysqli_fetch_object($prjqr)) {
            echo "<option value='$prjnm->id'>".$prjnm->pname."</option>";
          }
        ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sbprjctnm">Sub Project Name</label>
        <select class="form-control" name="sprj_name" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="purpose">Purpose</label>
        <input type="text" class="form-control" name="fdpurpose" id="purpose">
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="message">Message</label>
        <textarea name="fdmessage" id="message" class="form-control"></textarea>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="amount">FD Amount</label>
        <input type="text" class="form-control" name="fd_rqst_amt" id="amount" readonly>
      </div>
    </div>
  </div>        
</div>

<!-- End of FD -->