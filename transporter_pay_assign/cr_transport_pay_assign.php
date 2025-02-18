
<?php include("../../../config.php"); ?>
<!-- Start of Scripts -->
  <!-- Form Validation -->

  <!-- End of Form Validation -->
  <!-- Get Sub-Project, BMS & Po Number As Per Project Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#prjctnm").change(function(){
        var prjctid = $(this).val();
        if (prjctid != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/transporter_pay_assign/sp_bms_po_frpayasgn.php",
            data:{prjctnm:prjctid},
            type:'POST',
            success:function(response) {
              var resp = JSON.parse(response);

              $("#subprjnm").html(resp.subprjs);
              $("#bmsnm").html(resp.tbms);
              $("#ponum").html(resp.pos);
            }
          });
        }
        else {
          $("#subprjnm").html("<option value=''>Pick A Valid Project</option>");
          $("#bmsnm").html("<option value=''>Pick A Valid Project</option>");
          $("#ponum").html("<option value=''>Pick A Valid Project</option>");
        }
      });
    });
  </script>
  <!-- End of Get Sub-Project, BMS & Po Number As Per Project Selection -->

  <!-- Start of Calculations -->
  <script type="text/javascript">
    function calc() {
      var distance = 0;
      var weight = 0;
      var total_amt = 0;
      var adv_prcnt = 0;
      var rateper_km = 0;
      var rateper_kg = 0;
      var adv_amt = 0;
      var remain_amt = 0;

      distance = Number($("#distance").val());
      weight = Number($("#mtrl_weight").val());
      total_amt = Number($("#totalamnt").val());

      rateper_km = (total_amt / distance).toFixed(2);
      rateper_kg = (total_amt / weight).toFixed(2);

      $("#rateper_km").val(parseFloat(rateper_km));
      $("#rateper_kg").val(parseFloat(rateper_kg));

    }
  </script>
  <!-- End of Calculations -->
  <script>
   $(document).ready(function () {
      $('#trnsprtrnm').select2();
      $('#prjctnm').select2();
      $('#bmsnm').select2(); 
      $('#subprjnm').select2(); 
      $('#ponum').select2();
    });
  </script>
<!-- End of Scripts -->

<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Transporter Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="trnsprtrnm">Transporter Name</label>
        <select class="form-control" name="trnsprtrnm" id="trnsprtrnm">
          <option value="">--- Select Transporter Name ---</option>
          <?php
            $vndrqr = mysqli_query($con, "SELECT * FROM `prj_transport` WHERE `status`='1' ORDER BY `transport_name` ASC");
            while ($fthvndr = mysqli_fetch_object($vndrqr)) {
              echo "<option value='".$fthvndr->id."'>".$fthvndr->transport_name."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjctnm">Project Name</label>
        <select class="form-control" name="prjctnm" id="prjctnm">
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
        <label for="subprjnm">Sub Project Name</label>
        <select class="form-control" name="subprjnm" id="subprjnm">
          <option value="">--- Select Sub Project ---</option>
        </select>
      </div>
    </div>
    <!-- <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="bmsnm">Billing Milestone</label>
        <select class="form-control" name="bmsnm" id="bmsnm">
          <option value="">--- Select BMS ---</option>
        </select>
      </div>
    </div> -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">PO Number</label>
        <select class="form-control" name="ponum" id="ponum">
          <option value="">--- Select PO Number ---</option>
        </select>
      </div>
    </div>
  </div>
</div>