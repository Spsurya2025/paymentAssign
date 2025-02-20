<?php include("../../../config.php"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
<script>
    $(function () {
      $('#datetimepicker1, #datetimepicker2').datetimepicker({
        format:'YYYY-MM-DD',
      });
      $("#datetimepicker1").on("dp.change", function (e) {
        var fromDate = e.date;
        // Ensure To Date is at least the selected From Date
        $('#datetimepicker2').data("DateTimePicker").minDate(fromDate);

        // Auto-update To Date if it is before From Date
        var toDate = $('#todt').val();
        if (!toDate || moment(toDate).isBefore(fromDate, 'day')) {
          $('#todt').val(fromDate.format('YYYY-MM-DD'));
        }
      });
      // Ensure From Date is not after To Date
      $("#datetimepicker2").on("dp.change", function (e) {
        var toDate = e.date;
        $('#datetimepicker1').data("DateTimePicker").maxDate(false); // Allow any From Date
      });

    });
  </script>
<script>
    $(document).ready(function () {
      $('#statenm').select2();
    });
</script>
<script>
    $(document).ready(function () {    
        $("#statenm").change(function(){
            var statenm = $(this).val();
            var org_id = <?php echo $_GET['org_id']; ?>;
            if (statenm != "") {
            $.ajax({
                url:"<?php echo SITE_URL; ?>/basic/finance/payment_assign/gst_pay_assign/get_gstno.php",
                data:{statenm:statenm,addressType:'Billing Address',org_id:org_id},
                type:'POST',
                success:function(response) {
                var rslt = $.trim(response);
                $("#gstno").val(rslt);
                }
            });
            }
        }); 
    }); 
 </script>
<!-- GST ther Payment Details Form -->
<div class="row" style="margin-top: 20px;">
	<center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">GST Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="statenm">State<span style="color:red">*</span></label>
        <select class="form-control select2" name="statenm" id="statenm">
            <option value="">---Select State---</option>
          <?php
            $stateqr = mysqli_query($con, "SELECT x.id,x.sname FROM `prj_state` x JOIN `pur_address` y ON x.id=y.sid WHERE x.`status`='1' AND y.org_id='$_GET[org_id]' AND y.addrss_type = 'Billing Address'");
            while ($fthstate = mysqli_fetch_object($stateqr)) {
              echo "<option value='".$fthstate->id."'>".$fthstate->sname."</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            <label for="gstno">GSTIN No.</label>
            <input type="text" class="form-control" name="gstno" id="gstno" readonly>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="fromdt">From Date <span style="color:red">*</span></label>
        <div class='input-group date' id='datetimepicker1'>
          <input type="text" name="fromdate" id="fromdt" class="form-control" placeholder="yyyy-mm-dd" autocomplete="off">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label for="todt">To Date <span style="color:red">*</span></label>
        <div class='input-group date' id='datetimepicker2'>
          <input type="text" name="todate" id="todt" class="form-control" placeholder="yyyy-mm-dd" autocomplete="off">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>
      </div>
    </div>
  </div>        
</div>

<!-- End of GST Payment Details Form -->
