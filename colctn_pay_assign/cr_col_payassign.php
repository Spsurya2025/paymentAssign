<?php include("../../../config.php"); ?>
<script>
    $(document).ready(function () {
      $('#sbprjctnm').select2();
      $('#prjctnm').select2();
      $('#dbtr_typ').select2();
      $('#client_nm').select2();
      $('#unit_nmid').select2();
      $('#transaction_for').select2();
      if ($('#transaction_for').val() != '') {
            $('#transaction_for').trigger('change');
        }
    });
    $("#prjctnm").change(function(){
      $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
      var transact_for = $("#transaction_for").val();
      var prjid = $(this).val();
      if (transact_for == "RESCO" && prjid != ""){
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/get_prj_subprj_unt_forpayasgn.php",
          data:{project_id:prjid,action:'show_subprjdtls',client_id:client_nm},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#sbprjctnm").html(rslt);
          }
        });
      }else if(prjid != ""){
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
      $("#prjctnm").html('<option value="">--- Select Project ---</option>');
      $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
      $("#unit").css("display","none");
      $("#transaction_for").val('');
      var org_id = $("#org_id").val();
      var dbtr_typ_id = $(this).val();
      if(dbtr_typ_id == "12"){
          $("#trans_for").css("display","block");
      }else{
          $("#trans_for").css("display","none"); 
      }
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
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/colctn_pay_assign/get_unit_data.php",
          data:{dbtr_typ:dbtr_typ_id,type:'db_type',org_id:org_id},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#prjctnm").html(rslt);
          }
        });
      }
    });
    $("#transaction_for").change(function(){
      $("#prjctnm").html('<option value="">--- Select Project ---</option>');
      $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
      var org_id = $("#org_id").val();
      var client_nm = $("#client_nm").val();
      var trans_for = $("#transaction_for").val();
      if(trans_for == "RESCO"){
        $("#unit").css("display","block");
      }else{
        $("#unit").css("display","none");
      }
      if (client_nm != "" && trans_for == "RESCO") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/get_prj_subprj_unt_forpayasgn.php",
            data:{client_id:client_nm,org_nm:org_id,action:'show_prjdtls'},
            type:'POST',
            success:function(response) {
              var rslt = $.trim(response);
              $("#prjctnm").html(rslt);
            }
          });
      }
      else{
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/colctn_pay_assign/get_unit_data.php",
            data:{transaction_for:trans_for,org_id:org_id},
            type:'POST',
            success:function(response) {
              var rslt = $.trim(response);
              $("#prjctnm").html(response);
              $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
            }
          });
      }
    });
    // $("#transaction_for").change(function(){
    //   //$("#client_nm").html('<option value="">--- Select Client Name ---</option>');
      
    //   var transaction_for = $(this).val();
    //   // if(transaction_for == "RESCO"){
    //   //   $("#unit").css("display","block");
    //   // }else{
    //   //   $("#unit").css("display","none");
    //   // }
    //   var org_id = $("#org_id").val();
    //     $.ajax({
    //       url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/colctn_pay_assign/get_unit_data.php",
    //       data:{transaction_for:transaction_for,org_id:org_id},
    //       type:'POST',
    //       success:function(response) {
    //         var rslt = $.trim(response);
    //         $("#prjctnm").html(response);
    //         $("#sbprjctnm").html('<option value="">--- Select Sub Project ---</option>');
    //       }
    //     });
      
    // });
    $("#sbprjctnm").change(function(){
      var transact_for = $("#transaction_for").val();
      var subprj = $(this).val();
      var prjid = $("#prjctnm").val();
      if (transact_for == "RESCO" && prjid != "" && subprj != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/get_prj_subprj_unt_forpayasgn.php",
          data:{project_id:prjid,subprj_id:subprj,transact_for:transact_for,action:'show_unitdtls'},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#unit_nmid").html(rslt);
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
              $dbtrqr = mysqli_query($con, "SELECT id,subtypenm FROM `fin_grouping_subtype` WHERE `undergrp`='Type' AND `grptypnm`='5' AND `lnkwith`= 'fin_customers' AND `status`='1'");
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
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6" id="trans_for" style="display:none">
      <div class="form-group">
        <label for="transaction_for">Transaction For<span style="color:red">*</span></label>
        <select class="form-control" name="transaction_for" id="transaction_for">
          <option value="">--- Select ---</option>
          <option value="EPC">EPC</option>
          <option value="RESCO">RESCO</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name<span style="color:red">*</span></label>
        <select class="form-control" name="prj_name" id="prjctnm">
          <option value="">--- Select Project ---</option>
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
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6" id="unit" style="display:none">
      <div class="form-group">
        <label for="unit_nmid">UNIT Name<span style="color:red">*</span></label>
        <select class="form-control select2" name="unit_nmid" id="unit_nmid">

        </select>
      </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="remark">Remark<span style="color:red">*</span></label>
        <textarea class="form-control" name="remark" id="remark"></textarea>
        <input type="hidden" name="org_id" id="org_id" value="<?php echo $_GET['org_id'] ?>">
      </div>
    </div>
  </div>        
</div>

<!-- End of Collection -->