<?php require_once('../../../auth.php'); ?>
<?php require_once('../../../config.php'); ?>	
<?php
    function getEMIPayBankName($emibankid){
		try{
			$sql = "SELECT id, bnkname, branch, accnum FROM fin_bank WHERE id='$emibankid'";
			$result = mysqli_query($con, $sql);
			if($result->num_rows > 0){
				while($data = mysqli_fetch_object($result)){
					$bankid = $data->id;
					$bankname = $data->bnkname;
					$branchname = $data->branch;
					$accntnum = $data->accnum;
					$bankDetails = $bankname.'-'.$branchname.'-'.$accntnum;
				}
				return $bankDetails;
			}
			else{
				return false;
			}
		}
		catch(Exception $ex){
			echo $ex->getMessage();
		}
	}
	$account_no = $_REQUEST['account_no'];
	$sqlquery="SELECT `loanamnt`,`reffno` FROM `fin_loan_master` WHERE `loan_accntno`='$account_no'";
	
  $res = mysqli_query($con, $sqlquery);
  $cnt=mysqli_num_rows($res);
  $row = mysqli_fetch_object($res);
  $loanamnt=$row->loanamnt;
  $reffno=$row->reffno;

  if($cnt>0){
    $sl=1; 
	
    $sql1 = "SELECT * FROM fin_loan_emi_details WHERE flmreffno='$reffno'";
    $fetchemidetails = mysqli_query($con, $sql1);
    while($arrEMIDatas = mysqli_fetch_object($fetchemidetails)){
		
    $tenure = $arrEMIDatas->tenure;
    $emitype = $arrEMIDatas->emi_type;
    $totemi = $arrEMIDatas->noofemi;
    $intrstrate = $arrEMIDatas->intrst_rate;

    $emiamount = $arrEMIDatas->emi_amount;
    $totalinterest = $arrEMIDatas->total_interest;
    $emiaccntno = $arrEMIDatas->emi_pay_accnt;
    $emipaybank = getEMIPayBankName($emiaccntno);
    $emipaymode = $arrEMIDatas->emi_paymode;
    $emistartdate = $arrEMIDatas->emi_startdate;
    $emienddate = $arrEMIDatas->emi_enddate;
	
    ?>
	<table class="table table-bordered table-condensed table-striped">
		<thead>
			<tr style="background-color:#00CC99; color:#FFF"><td class="text-center" colspan="10"> EMI Table </td></tr>
			<tr>
				<th class="text-center">Sl No</th>
				<th class="text-center">EMI Date</th>
				<th class="text-center">Interest</th>
				<th class="text-center">Principal</th>
				<th class="text-center">Total</th>
				<th class="text-center">Outstanding</th>
				<th class="text-center">Status</th>
			</tr>
		</thead>
		<tbody>
			<tr style="display:none"> 
				<td colspan="5"></td> 
				<td class="text-right"> <?php echo $loanamnt;?> </td>
				<td colspan="2"></td>
			</tr>
			<?php
				$emimonth = explode("-", $emistartdate);
				$fixedDateEveryMonth = $emimonth[2];
				$start = new DateTime(date('Y-m-') . $fixedDateEveryMonth);
				$outstanding = $latestoutstanding = "";
				//$P = $T = $R ="";
				for($i=1; $i<=$totemi; $i++)
				{
					if($i==1){
					$LoanDate = $emistartdate;
					}
					if($i!=1){
						if($emitype=='monthly'){
						$LoanDate = date('Y-m-d', strtotime( '+1 month', strtotime($LoanDate) ));
						}
						if($emitype=='quaterly'){
						$LoanDate = date('Y-m-d', strtotime( '+3 month', strtotime($LoanDate) ));
						}
					}
					if($i==1){
					$P = $loanamnt;
					}
					else if($i==2){
					$P = $outstanding;
					}
					else{
					$P = $latestoutstanding;
					}
					$T = $tenure;
					$R = $intrstrate;
					$instrt = ($P*$R)/100;
					if($emitype=='monthly'){
					$mnthlyintrst = sprintf('%.2f', $instrt/12);
					}
					if($emitype=='quaterly'){
					$mnthlyintrst = sprintf('%.2f', $instrt/4);
					}
					$principl = $emiamount-$mnthlyintrst;
					$prncplamnt = sprintf('%.2f', $principl);
					$totalprnclamnt = $mnthlyintrst + $prncplamnt;
					if($i==1){
					$outstandingamnt = $loanamnt-$prncplamnt;
					}
					else if($i==2){
					$outstandingamnt = $outstanding-$prncplamnt;
					}
					else{
					$outstandingamnt = $latestoutstanding-$prncplamnt;
					}
					$totalprnclamt = sprintf('%.2f', $totalprnclamnt);
					$outstandingamt = sprintf('%.2f', $outstandingamnt);
			     ?>
			<tr>
				<td class="text-center"> <?php echo $i;?> </td>
				<!--EMI Date Block-->
				<td class="text-center"> 
					<?php	echo $LoanDate;	?> 
				</td>
				<!--Interest Block-->
				<td class="text-center">
					<?php	echo $mnthlyintrst;	?>
				</td>
				<!--Principal Block-->
				<td class="text-center">
					<?php echo $prncplamnt;?>
				</td>
				<!--Total Block-->
				<td class="text-center">
					<?php echo sprintf('%.2f', $totalprnclamnt);?>
				</td>
				<!--Outstanding Block-->
				<td class="text-center">
					<?php echo sprintf('%.2f', $outstandingamnt);?>
				</td>
				<?php
					 $getqry = mysqli_query($con,"SELECT `emi_dt` FROM `fin_payment_entry_term_loan` WHERE `loan_id`='$reffno' AND `emi_dt`='$LoanDate' AND paid_status='1'");
					$rows = mysqli_fetch_object($getqry);
					$total_results = mysqli_num_rows($getqry);
					if ($total_results>=1) {
						$paidstatus = "Paid";
						$chkst = "disabled";
					}else{ 
						$paidstatus = "Unpaid";
						$chkst = "";
					} 
						//echo $rows->emi_dt;
					?>
				<td class="text-center">
					<?php echo $paidstatus; ?>
				</td>
				<td>
				<input type='checkbox' name='chkemi[]' id='chkemi' <?php echo $chkst; ?> value="<?php echo $reffno; ?>/<?php echo $LoanDate; ?>/<?php echo $emiamount; ?>/<?php echo $mnthlyintrst; ?>/<?php echo $prncplamnt; ?>/<?php echo $totalprnclamt; ?>/<?php echo $outstandingamt; ?>" >
				</td>
			</tr>
				<?php
				if($i==1){
				$outstanding = $loanamnt-$prncplamnt;
				}
				else{
				 $latestoutstanding = $outstandingamnt;
				}
			}?>
		</tbody>
	</table>
 <?php	}	 } ?>
	<table class="table table-bordered table-condensed">
		<tfoot>
			<tr>
				<input type="hidden" class="form-control" name="chkamt" id="chkamt" value="">
				<input type="hidden" class="form-control" name="demo" id="demo" value="">
				<td style="color:blue;" align="center" colspan="1">
					<label for="ltr_ctg_nm">Other :</label>
				</td>
				<td style="color:blue;" align="center" colspan="2">
					<select class="form-control" name="other_crg_emi" id="other_crg_emi">
					<option value="">-- Select --</option>
					<?php
					$getheadqr = mysqli_query($con, "SELECT `id`,`subtypenm` FROM `fin_grouping_subtype` WHERE `status`='1' AND `lnkwith`!=''");

					while ($fthhd = mysqli_fetch_object($getheadqr)) {
					echo "<option value='".$fthhd->id."'>".$fthhd->subtypenm."</option>";
					}
					?>
					</select>
				</td>
				<td style="color:blue;" align="center">
					<input type="text" class="form-control persum" name="other_amt_emi" id="other_amt_emi"  value="" placeholder="Charges">
				</td>
			</tr>
			<tr>
				<td style="color:blue;" align="center" colspan="1">
				<label for="ltr_ctg_nm">Total :</label>
				</td>
				<td style="color:blue;" align="center">
					<input type="text" class="form-control persum" name="total_amt" id="total_amt_emi" value="" placeholder="Total">
				</td>
			</tr>
		</tfoot>
	</table>
	<script type="text/javascript">
	  $('input:checkbox').change(function ()
	  {
	    var total = 0;
	    $('input:checkbox:checked').each(function(){
	      var a = $(this).val();
	      var b = a.split("/");
	      total += isNaN(parseInt(b[2])) ? 0 : parseInt(b[2]);
	    });
	  $("#chkamt").val(total);
	    var test = document.getElementById('other_amt_emi').value;
	    if (test=='') {
	      var tstamt = 0;
	    }else{
	      var tstamt = test;
	    }
	    var chkamt = document.getElementById('chkamt').value;
	    if (chkamt=='') {
	      var chkamt1 = 0;
	    }else{
	      var chkamt1 = chkamt;
	    }
	    var fin_amt = parseInt(tstamt)+parseInt(chkamt1);
	  	var amt = parseFloat(fin_amt).toFixed(2);
	  	$("#total_amt_emi").val(amt);
	  });

	  $("#other_amt_emi").keyup(function(){  
	 	var totalsum = $(this).val();
	  	$("#demo").val(totalsum);
	  	var test = document.getElementById('demo').value;
	    if (test=='') {
	      var tstamt = 0;
	    }else{
	      var tstamt = test;
	    }
	    var chkamt = document.getElementById('chkamt').value;
	    if (chkamt=='') {
	      var chkamt1 = 0;
	    }else{
	      var chkamt1 = chkamt;
	    }
	  	var fin_amt = parseInt(tstamt)+parseInt(chkamt1);
	  	var amt = parseFloat(fin_amt).toFixed(2);
	  $("#total_amt_emi").val(amt);
	});
	</script>