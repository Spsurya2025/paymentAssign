<?php include("../../../config.php"); ?>
<script>
  $(document).ready(function(){
    $("#othrhead").change(function(){
      $("#ptcrt").html('<option value="">--- Select A Particular Name ---</option>');
      var headdid = $(this).val();
      if (headdid != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/get_data.php",
          data:{othrhead:headdid,type:'head'},
          type:'POST',
          success:function(response) {
            var rslt = $.trim(response);
            $("#ptcrt").html(rslt);
          }
        });
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
  });
</script>
<script>
    $(document).ready(function () {
      $('#othrhead').select2();
      $('#ptcrt').select2();
      $('#prjctnm').select2();
    });
</script>
<!-- Other Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Other Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="othrhead">Head</label>
        <select class="form-control" name="othrhead" id="othrhead">
            <option value="">---Select Head---</option>
            <?php
              $gethdqr = mysqli_query($con, "SELECT `id`,`subtypenm` FROM `fin_grouping_subtype` WHERE `status`='1' AND `lnkwith`!='Indivisual' AND `lnkwith`!=''");
              while($fchhd = mysqli_fetch_object($gethdqr)){
                echo "<option value='".$fchhd->id."'>".$fchhd->subtypenm."</option>";
              }
            ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="ptcrt">Payment to be Credit to</label>
        <select class="form-control" name="paytcr" id="ptcrt">
           <option value="">--- Select A Particular Name ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name</label>
        <select class="form-control" name="prjnm" id="prjctnm">
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
        <select class="form-control" name="subprjnm" id="sbprjctnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    
  </div>        
</div>
<!-- End Other Payment Details form -->
