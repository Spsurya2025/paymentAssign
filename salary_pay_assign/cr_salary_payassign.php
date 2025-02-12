<?php require_once('../../../auth.php');
if(isset($_GET['pay_orgnstn'])){
 $pay_orgnstn = $_GET['pay_orgnstn'];
}
 ?>
<script>
    $(document).ready(function () {
      $('#benif_acc').select2();
      $('#location').select2();
      $('#year').select2();
      $('#month').select2();
    });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $(document).on('change', '.splpr', function(){
      var benif_acc = $("#benif_acc").val();
      var orgid = $("#orgname").val();
      var location = $("#location").val();
      var month = $("#month").val();
      var year = $("#year").val();
      if (year != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/salary_pay_assign/get_fin_ajax.php",
          data:{benif_acc:benif_acc,location:location,month:month,year:year,orgid:orgid},
          type:'GET',
          success:function(response) {
            var resp = $.trim(response);
            document.getElementById("req_id").value = resp;
            $("#req_id").html(resp);
          }
        });
      }
      else {
        $("#req_id").val("<option value=''>No Result Found</option>");
      }
    });
  });
</script>
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Salary Processing</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Benificiary A/c</label>
        <select class="form-control splpr" name="benif_acc" id="benif_acc">
          <option value="">---Select---</option>
          <?php
            $eq = "SELECT * FROM `mstr_emp` order by `fullname` ASC";
            $efq=mysqli_query($con,$eq);
            while ($egq = mysqli_fetch_object($efq))
            {
              echo '<option value="'. $egq->id . '">' . $egq->fullname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Location</label>
        <select class="form-control" name="location" id="location">
          <option value="">---Select---</option>
          <?php
            $loc = "SELECT * FROM `hr_location` order by `lname` ASC";
            $eloc=mysqli_query($con,$loc);
            while ($elocq = mysqli_fetch_object($eloc))
            {
              echo '<option value="'. $elocq->id . '">' . $elocq->lname .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group">
        <label for="org_id">Year</label>
        <select class="form-control splpr" name="year" id="year">
          <option value="">---Select---</option>
          <?php
            for($i=0;$i<=5;$i++){
            $year=date('Y',strtotime("last day of +$i year"));
            echo '<option value="'. $year . '">' . $year .'</option>';
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="form-group splpr">
        <label for="org_id">Month</label>
        <select class="form-control" name="month" id="month">
          <option value="">---Select---</option>
          <option value="01">January</option>
          <option value="02">February</option>
          <option value="03">March</option>
          <option value="04">April</option>
          <option value="05">May</option>
          <option value="06">June</option>
          <option value="07">July</option>
          <option value="08">August</option>
          <option value="09">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3">
      <label for="type">Remarks</label>
      <textarea class="form-control" name="sp_remarks" id="sp_remarks"></textarea>
    </div>
  </div>
  <div class="col-lg-12" id="rent_bind">
  </div>        
</div>