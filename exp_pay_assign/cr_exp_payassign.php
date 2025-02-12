<?php include("../../../config.php"); ?>

<!-- Start of Scripts -->
<script>
    $(document).ready(function () {
      $('#expns_for').select2();
      $('#prjct').select2(); 
      $('#sub_prjct').select2();
      $('#bmsnm').select2();
      
    });
</script>
  <!-- Get Employee Code As Per Employee Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#expns_for").change(function(){
        var emid = $("#expns_for").val();
        if (emid != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/exp_pay_assign/get_empcode.php",
            data:{empnm:emid},
            type:'POST',
            success:function(response) {
              var rslt = $.trim(response);
              document.getElementById("exp_for_empcode").value = rslt;
            }
          });
        }
      });
    });
  </script>
  <!-- End of Get Employee Code As Per Employee Selection -->

  <!-- Get Sub-Project & BMS As Per Project Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#prjct").change(function(){
        var prjctid = $(this).val();
        if (prjctid != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/exp_pay_assign/get_subprj_bms.php",
            data:{prjctnm:prjctid},
            type:'POST',
            success:function(response) {
              var resp = JSON.parse(response);

              $("#sub_prjct").html(resp.sbprjnm);
              $("#bmsnm").html(resp.blng_ms);
            }
          });
        }
        else {
          $("#sub_prjct").html("<option value=''>Pick A Valid Project</option>");
          $("#bmsnm").html("<option value=''>Pick A Valid Project</option>");
        }
      });
    });
  </script>
  <!-- End of Sub-Project & BMS As Per Project Selection -->
<!-- End of Scripts -->

<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Expense Payment Details</h4></center>
	<div class="col-lg-12">
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
			<div class="form-group">
        <label for="expns_for">Expense for</label>
        <select class="form-control" name="expns_for" id="expns_for">
        	<option value="">--- Employee Name ---</option>
        	<?php
        		$empqr = mysqli_query($con, "SELECT id,fullname FROM `mstr_emp` WHERE `status`='1' ORDER BY `fullname` ASC");
        		while ($fthemp = mysqli_fetch_object($empqr)) {
        			echo "<option value='".$fthemp->id."'>".$fthemp->fullname."</option>";
        		}
        	?>
        </select>
      </div>
		</div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="exp_for_empcode">Employee Code</label>
        <input type="text" class="form-control" name="exp_for_empcode" id="exp_for_empcode" placeholder="Unique Employee Code" readonly>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjct">Project</label>
        <select class="form-control" name="prjct" id="prjct">
          <option value="">--- Project Name ---</option>
          <?php
            $prjqr = mysqli_query($con, "SELECT id,pname FROM `prj_project` WHERE `status`='1' ORDER BY `pname` ASC");
            while ($fthprj = mysqli_fetch_object($prjqr)) {
              echo "<option value='".$fthprj->id."'>".$fthprj->pname."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="sub_prjct">Sub-Project</label>
        <select class="form-control" name="sub_prjct" id="sub_prjct">
          <option value="">--- Sub-Project Name ---</option>
        </select>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="bmsnm">Billing Milestone</label>
        <select class="form-control" name="bmsnm" id="bmsnm">
          <option value="">--- BMS Name ---</option>
        </select>
      </div>
    </div>
	</div>
</div>