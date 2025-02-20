<?php include("../../../config.php"); ?>

  
<!-- End of Scripts -->
<script>
    $(document).ready(function () {
      $('#org_id').select2();
      $('#year').select2();
      $('#month').select2();
      
    });
</script>
<!-- Oerator Form -->
<div class="row" style="margin-top: 20px;">
  <center><h4 style="text-decoration: underline; font-weight: bold; color: #37909e;">Rent Payment Details</h4></center>
  <div class="col-lg-12">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="project">Year</label>
        <select class="form-control year-select rentdetails" name="year" id="year">
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <div class="form-group">
        <label for="project">Month</label>
        <select class="form-control month-select rentdetails" name="month" id="month">
        </select>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <label for="type">Type</label>
      <select name="type" class="form-control rentdetails" id="type">
        <option value="">---Select---</option>
        <?php 
          $rnt_type = mysqli_query($con,"SELECT DISTINCT `rent_type` FROM `rent_approval` WHERE `status` = '1'");
          while ($typ_ftch = mysqli_fetch_object($rnt_type)) { 
            echo "<option value='".$typ_ftch->rent_type ."'>".$typ_ftch->rent_type."</option>";
          } 
        ?>
      </select>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
      <label for="purpose">Purpose</label>
      <select name="purpose" class="form-control rentdetails" id="purpose">
        <option value="">---Select---</option>
        <?php 
          $rnt_pr = mysqli_query($con,"SELECT DISTINCT `purpose` FROM `rent_approval` WHERE `status` = '1'");
          while ($pr_ftch = mysqli_fetch_object($rnt_pr)) { 
            echo "<option value='".$pr_ftch->purpose."'>".$pr_ftch->purpose."</option>";
          }
        ?>
      </select>
    </div>
  </div>
  
  <div class="col-lg-12 col-md-12 col-sm-12" id="rent_bind"></div>
</div>

<!-- End of operator Form -->

 <!-- Year Dropdown -->

<!-- Month Dropdown -->

<script>
    var date = new Date();
    var currentYear = date.getFullYear();
    var currentMonth = date.getMonth() + 1; // JavaScript months are 0-based, so +1

    // Month Names with Corresponding Values
    var months = [
        { value: "01", name: "January" },
        { value: "02", name: "February" },
        { value: "03", name: "March" },
        { value: "04", name: "April" },
        { value: "05", name: "May" },
        { value: "06", name: "June" },
        { value: "07", name: "July" },
        { value: "08", name: "August" },
        { value: "09", name: "September" },
        { value: "10", name: "October" },
        { value: "11", name: "November" },
        { value: "12", name: "December" }
    ];

    // Populate Year Dropdown
    var yearSelect = document.getElementById("year");
    for (var i = currentYear; i >= 2000; i--) {
        var option = new Option(i, i);
        if (i === currentYear) option.selected = true;
        yearSelect.appendChild(option);
    }

    // Populate Month Dropdown
    var monthSelect = document.getElementById("month");

    function updateMonths() {
        monthSelect.innerHTML = ""; // Clear existing options
        let selectedYear = parseInt(yearSelect.value);
        
        // If the selected year is the current year, limit to current month; otherwise, show all 12 months
        let maxMonth = selectedYear === currentYear ? 12 : 12;
        
        for (var i = 0; i < maxMonth; i++) {
            var option = new Option(months[i].name, months[i].value);
            monthSelect.appendChild(option);
        }
    }

    // Initial call to populate months on page load
    updateMonths();

    // Update months when the year selection changes
    yearSelect.addEventListener("change", updateMonths);
    // $("#year").change(function(){
    //     updateMonths();
    // });  
</script>
<script>
  $(document).ready(function() {
    $(".rentdetails").change(function() {
      if ($(this).val() != '') {
        var org = $("#pay_orgnstn").val();
        var yr = $("#year").val();
        var mnth = $("#month").val();
        var typ = $("#type").val();
        var prps = $("#purpose").val();
        if (org != '' && yr != '' && mnth != '' && typ != '' && prps != '') {
          $.ajax({
            url: "<?php echo SITE_URL; ?>/basic/finance/payment_assign/rent_pay_assign/get_rent_pymnt_rqst.php",
            data: {
              org_id: org,
              yr_nm: yr,
              mnth_nm: mnth,
              type: typ,
              purpose: prps
            },
            type: 'POST',
            success: function(response) {
              var resp = $.trim(response);
              $("#rent_bind").html(resp);
            }
          })
        }
      } else {
          $("#rent_bind").html('');
      }
    });
  });
</script>

