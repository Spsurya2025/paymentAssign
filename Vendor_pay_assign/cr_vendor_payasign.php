<?php include("../../../config.php"); ?>
  <!-- Get project Details As Per Vendor Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#vndrnm").change(function(){
        var vndrid = $(this).val();
        if (vndrid != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/prj_for_payasgn.php",
            data:{vndrnm:vndrid},
            type:'POST',
            success:function(response) {
              var resp = JSON.parse(response);

              $("#prjct_name").html(resp.prjnames);
            }
          });
        }
        else {
          $("#prjct_name").html("<option value=''>Pick A Valid Vendor</option>");
        }
      });
    });
  </script>
  <!-- End of Get project Details As Per Vendor Selection -->

  <!-- Get JO As Per Project Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#prjct_name").change(function(){
        var vndr = $("#vndrnm").val();
        var prjid = $("#prjct_name").val();
        if (prjid != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/jo_for_payasgn.php",
            data:{vndrnm:vndr,prjct_name:prjid},
            type:'POST',
            success:function(response) {
              var resp = $.trim(response);
              $("#jobodr_num").html(resp);
            }
          });
        }
        else {
          $("#jobodr_num").html("<option value=''>Pick A Valid Project</option>");
        }
      });
    });
  </script>
  <!-- End of Get JO As Per Project Selection -->

  <!-- Get JO Value & Sub-Project As Per JO Selection -->
  <script type="text/javascript">
    function showSbPrj() {
      var joboid = $("#jobodr_num").val();
      if (joboid != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/joval_for_payasgn.php",
          data:{jobodr_num:joboid},
          type:'POST',
          success:function(response) {
            var response = $.trim(response);
            var joval = response.split("#");

            $("#jobodr_val").val(joval[0]);

            // Fetch of Table
            $.ajax({
              url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/subprj_for_payasgn.php",
              data:{jonum:joboid,jo_unqcd:joval[1]},
              type:'POST',
              success:function(resp) {
                var resp = $.trim(resp);
                $("#showSubPrj").html(resp);
              }
            });
          }
        });
      }
      else {
        $("#jobodr_val").val("");
        $("#showSubPrj").html("");
      }
    }
  </script>

  <script>
    $(document).ready(function(){
      $("#jobodr_num").change(function(){
        showSbPrj();
      });
    });
  </script>
  <script>
   $(document).ready(function () {
      $('#vndrnm').selectize({
      sortField: 'text'
      });
    });
  </script>
  <!-- End of Get JO Value & Sub-Project As Per JO Selection -->

<!-- End of Scripts -->

<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Vendor Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="vndrnm">Vendor Name</label>
        <select class="form-control" name="vndrnm" id="vndrnm">
          <option value="">--- Select Vendor Name ---</option>
          <?php
            $vndrqr = mysqli_query($con, "SELECT id,vendor_name FROM `prj_vendor` WHERE `status`='1' ORDER BY `vendor_name` asc");
            while ($fthvndr = mysqli_fetch_object($vndrqr)) {
              echo "<option value='".$fthvndr->id."'>".$fthvndr->vendor_name."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prjct_name">Project Name</label>
        <select class="form-control" name="prjct_name" id="prjct_name">
          <option value="">--- Select Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="jobodr_num">Job Order Number</label>
        <select class="form-control" name="jobodr_num" id="jobodr_num">
          <option value="">--- Select Job Order ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="jobodr_val">Job Order Value</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="jobodr_val" id="jobodr_val" placeholder="Job Order Amount" readonly>
        </div>
      </div>
    </div>
    <div id="showSubPrj">
      
    </div>
  </div>

</div>