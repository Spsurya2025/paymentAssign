<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#sbprjctnm').select2();
      $('#prjctnm').select2();
      $('#dbtr_typ').select2();
      $('#client_nm').select2();
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
    $("#dbtr_typ").change(function(){
      $("#client_nm").html('<option value="">--- Select Client Name ---</option>');
      var dbtr_typ_id = $(this).val();
      if (dbtr_typ_id != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/colctn_pay_assign/get_client.php",
          data:{dbtr_typ:dbtr_typ_id},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#client_nm").html(rslt);
          }
        });
      }
    });
</script>
<!-- Start Collection -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Collection Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="dbtr_typ">Debtor Type<span style="color:red">*</span></label>
          <select class="form-control select2" name="debtor_typ" id="dbtr_typ">
            <option value="">--- Select Debtor Type ---</option>
            <?php
              $dbtrqr = mysqli_query($con, "SELECT id,subtypenm FROM `fin_grouping_subtype` WHERE `undergrp`='Type' AND `grptypnm`='5' AND `status`='1'");
              while ($fthdbtr = mysqli_fetch_object($dbtrqr)) {
                echo "<option value='".$fthdbtr->id."'>".$fthdbtr->subtypenm."</option>";
              }
            ?>
          </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="client_nm">Client Name<span style="color:red">*</span></label>
          <select class="form-control client_nm" name="clientnm" id="client_nm">
            <option value="">--- Select Client Name ---</option>
          </select>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
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
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sbprjctnm">Sub Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="sbprjctnm" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="remark">Remark<span style="color:red">*</span></label>
        <textarea class="form-control" name="remark" id="remark"></textarea>
      </div>
    </div>
  </div>        
</div>

<!-- End of Collection -->