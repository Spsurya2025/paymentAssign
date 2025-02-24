<?php include("../../../config.php"); ?>
<?php
if(isset($_GET['pay_orgnstn'])){
 $pay_orgnstn = $_GET['pay_orgnstn'];
 $paidamt = $_GET['paidamt'];
 $bankaccdi = $_GET['bankaccdi'];
}
 ?>
 <script>
    $(document).ready(function () {
      $('#org_nm').select2();
      $('#bnkaccnt').select2();
    });
</script>
<script>
      $(document).ready(function() {
        $("#org_nm").change(function() {
          var org_nm = $("#org_nm").val();
          if(org_nm != "") {
            $.ajax({
              url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/banktr_pay_assign/get_bankaccdtls.php",
              data:{org_nm:org_nm},
              type:'POST',
              success:function(response) {
                var resp = $.trim(response);
                $("#bnkaccnt").html(resp);
                document.getElementById("brnch_nm").value = '';
                document.getElementById("accno").value = '';
                document.getElementById("ifsc_cd").value = '';
                document.getElementById("cif_no").value = '';
                document.getElementById("loc_id").value = '';
                document.getElementById("loc_nm").value = '';
              }
            });
          }
          else {
            $("#bnkaccnt").html('<option value="">Select A Valid Organisation</option>');
            document.getElementById("brnch_nm").value = '';
            document.getElementById("accno").value = '';
            document.getElementById("ifsc_cd").value = '';
            document.getElementById("cif_no").value = '';
            document.getElementById("loc_id").value = '';
            document.getElementById("loc_nm").value = '';
          }
        });
        $("#bnkaccnt").change(function() {
          var bnkaccnt = $(this).val();
          if(bnkaccnt != "") {
            $.ajax({
              url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/banktr_pay_assign/get_bankaccdtls.php",
              data:{bankid:bnkaccnt},
              type:'POST',
              success:function(response) 
              {
                var sd = $.trim(response);
                var str = sd;
                var dprtinfo = str.split("*");
                document.getElementById("brnch_nm").value = dprtinfo[0];
                document.getElementById("accno").value = dprtinfo[1];
                document.getElementById("ifsc_cd").value = dprtinfo[2];
                document.getElementById("cif_no").value = dprtinfo[3];
                document.getElementById("loc_id").value = dprtinfo[4];
                document.getElementById("loc_nm").value = dprtinfo[5];
              }
            });
          }
          else {
            document.getElementById("brnch_nm").value = '';
            document.getElementById("accno").value = '';
            document.getElementById("ifsc_cd").value = '';
            document.getElementById("cif_no").value = '';
            document.getElementById("loc_id").value = '';
            document.getElementById("loc_nm").value = '';
          }
        });
      });
    </script>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Bank Transfer</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Organization<span style="color:red">*</span></label>
        <select class="form-control splpr" name="org_nm" id="org_nm">
          <option value="">--- Select Organisation ---</option>
            <?php
              $orgnsnqr = mysqli_query($con, "SELECT id,organisation FROM `prj_organisation` WHERE `status`='1'");
              while ($fthorgnsn = mysqli_fetch_object($orgnsnqr)) {
                echo "<option value='".$fthorgnsn->id."'>".$fthorgnsn->organisation."</option>";
              }
            ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Bank Allias Name<span style="color:red">*</span></label>
        <select class="form-control" name="bnkaccnt" id="bnkaccnt">
          <option value="">--- Select Bank Account ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Bank Acc no.</label>
        <input type="text" class="form-control" name="accno" id="accno" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Branch</label>
        <input type="text" class="form-control" name="brnch_nm" id="brnch_nm" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group splpr">
        <label for="org_id">Location</label>
        <input type="hidden" class="form-control" name="loc_id" id="loc_id">
        <input type="text" class="form-control" name="loc_nm" id="loc_nm" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">IFSC Code</label>
        <input type="text" class="form-control" name="ifsc_cd" id="ifsc_cd" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">CIF No/ CRN No</label>
        <input type="text" class="form-control" name="cif_no" id="cif_no" readonly>
      </div>
    </div>
    <div class="col-lg-3">
      <label for="type">Remarks<span style="color:red">*</span></label>
      <textarea class="form-control" name="bnktrn_remarks" id="bnktrn_remarks"></textarea>
    </div>
  </div>
  <div class="col-lg-12" id="rent_bind">
  </div>        
</div>