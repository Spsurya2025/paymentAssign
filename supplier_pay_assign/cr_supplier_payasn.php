<?php include("../../../config.php"); ?>

<!-- Start of Scripts -->
  <script>
    $(document).ready(function () {
      $('#suplrnm').selectize({
        sortField: 'text'
      });
    });
  </script>
  <!-- Get Cr Limit & Project As Per Supplier Selection -->
  <script type="text/javascript">
    function getCrPrj() {
      var splrid = $("#suplrnm").val();
      if (splrid != "") {
        $.ajax({
          url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/supplier_pay_assign/get_prj.php",
          data:{sup_id:splrid},
          type:'POST',
          success:function(response) {
            var res = $.trim(response);
            $("#prj_name").html(res);
          }
        });
      }
      else {
        $("#prj_name").html("<option value=''>Pick A Valid Supplier</option>");
      }
    }

    $(document).ready(function(){
      $("#suplrnm").change(function(){
        getCrPrj();
      });
    });

  </script>
  <!-- Get PO As Per Project Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#prj_name").change(function(){
        var prjid = $("#prj_name").val();
        var splrid = $("#suplrnm").val();

        if (prjid != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/supplier_pay_assign/get_po.php",
            data:{prjj:prjid,supl:splrid},
            type:'POST',
            success:function(response) {
              var resp = $.trim(response);
              $("#ponum").html(resp);
            }
          });
        }
        else {
          $("#ponum").html("<option value=''>No PO Found</option>");
        }
      });
    });
  </script>
  <!-- End of Get PO As Per Project Selection -->

  <!-- Get PO Details As Per PO No. Selection -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#ponum").change(function(){
        var ponm = $("#ponum").val();

        if (ponm != "") {
          $.ajax({
            url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/supplier_pay_assign/get_po.php",
            data:{ponum:ponm},
            type:'POST',
            success:function(result) {
              var rslt = $.trim(result);
              var poinfo = rslt.split("#");
              
              document.getElementById("poamnt").value = poinfo[0];
              document.getElementById("podate").value = poinfo[1];
            }
          });
        }
        else {
          $("#poamnt").val("");
          $("#podate").val("");
        }
      });
    });
  </script>
  <!-- End of Get PO Details As Per PO No. Selection -->  
<!-- End of Scripts -->

<!-- Supplier Form -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Supplier Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="suplrnm">Supplier Name</label>
        <select class="form-control" name="suplrnm" id="suplrnm">
          <option value="">--- Supplier Name ---</option>
          <?php
            $splrqr = mysqli_query($con, "SELECT id,supplier_name FROM `prj_supplier` WHERE `status`='1' ORDER BY `supplier_name` ASC");
            while ($fthsplr = mysqli_fetch_object($splrqr)) {
              echo "<option value='".$fthsplr->id."'>".$fthsplr->supplier_name."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="prj_name">Project Name</label>
        <select class="form-control" name="prj_name" id="prj_name">
          <option value="">--- Select Project ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="ponum">PO Number</label>
        <select class="form-control" name="ponum" id="ponum">
          <option value="">--- Purchase Order ---</option>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="podate">PO Date</label>
        <input type="text" name="podate" id="podate" class="form-control" placeholder="yyyy-mm-dd" readonly>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="poamnt">PO Amount</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-rupee"></i></span>
          <input type="text" class="form-control" name="poamnt" id="poamnt" placeholder="9999.99" readonly>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End of Supplier Form -->