<?php 
  require_once('../../auth.php'); 
  require_once('../../config.php'); 

  function getDtls($con , $scrchfld , $table , $tblcndtion , $srchcndtn)
  {
  	$sqlqry = mysqli_query($con,"select $scrchfld from $table where $tblcndtion='$srchcndtn'") or die(mysqli_error($con));
  	 while ($fetchs = mysqli_fetch_object($sqlqry)) 
  	 {
  		 $returnfld = $fetchs->$scrchfld;
  	 }
  	 return $returnfld;
  }

  function gettablecount($con ,$table,$cond,$requestfor) // get total count of record
  {
    $sqlqry = mysqli_query($con,"select COUNT(*) as total from ".$table." where request_for = '".$requestfor."' AND ".$cond) or die(mysqli_error($con));
    $countde = mysqli_fetch_assoc($sqlqry);
    return $countde['total'];
  }
    function getexpensetablecount($con ,$table,$cond) // get total count of record
  {
    $sqlqry = mysqli_query($con,"select COUNT(*) as total from ".$table." where ".$cond) or die(mysqli_error($con));
    $countde = mysqli_fetch_assoc($sqlqry);
    return $countde['total'];
  }

   function getalltablecount($con ,$table,$cond) // get total count of record
  {
    $sqlqry = mysqli_query($con,"select COUNT(*) as total from ".$table." where ".$cond) or die(mysqli_error($con));
    $countde = mysqli_fetch_assoc($sqlqry);
    return $countde['total'];
  }

  function getbeneficiarydeatils($con,$table,$feildname,$condid,$rowid){// get beneficiary details 
    echo "SELECT $feildname as beneficiary FROM $table WHERE condid ='".$rowid."'";
    $beneficiarydetails = mysqli_query($con, "SELECT $feildname as beneficiary FROM $table WHERE condid ='".$rowid."'");
    $fthsplr = mysqli_fetch_assoc($beneficiarydetails);
    return $fthsplr['beneficiary'];

  }

  function getcurrency($con,$table,$feildname,$condid,$rowid){ // get currency form dynamic table
    $getcurrencydetails = mysqli_query($con, "SELECT ".$feildname." as value FROM ".$table." WHERE ".$condid."='".$rowid."'");
    $getcurrency = mysqli_fetch_assoc($getcurrencydetails);
    if($getcurrency['value'] == '' || $getcurrency['value'] == 'INR'){
      $currency =  'Rs';
    }else if($getcurrency['value'] == 'USD'){
      $currency =  '$';
    }else if($getcurrency['value'] == 'EURO'){
      $currency = '€';
    }else if($getcurrency['value'] == 'GBP'){
      $currency = '£';
    }else if($getcurrency['value'] == 'CNY'){
      $currency = '¥';
    }
    return $currency;
  }
?>