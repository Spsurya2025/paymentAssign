<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#fdno').select2();
      $('#prjctnm').select2();
      $('#sbprjctnm').select2();
      
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
<!-- FD -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">FD Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="prj_name" id="prjctnm">
          <option value="">--- Select Project ---</option>
        <?php
          $prjqr = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `status`='1' AND ptype_org='$_GET[org_id]'");
          while ($prjnm = mysqli_fetch_object($prjqr)) {
            echo "<option value='$prjnm->id'>".$prjnm->pname."</option>";
          }
        ?>
        </select>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sbprjctnm">Sub Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="sprj_name" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="purpose">Purpose<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="fdpurpose" id="purpose">
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