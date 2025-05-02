<?php include("../../../config.php");
?>
<?php
if(isset($_GET['ddexprsn'])) {
  $exp = $_GET['ddexprsn'];
  $sql = mysqli_query($con,"SELECT lnkwith,col_nm from fin_grouping_subtype where `id`='$exp'");
  $res = mysqli_fetch_object($sql);
  $lnkwith=$res->lnkwith;
  $col_nm=$res->col_nm;
    if($col_nm!='')
  { 
      if($exp==11 || $exp==12 || $exp==70)
      { 
        $where=" WHERE group_subtype=$exp";
      }
      else
      {
        $where="";
      }
      // echo "select id,$col_nm from $lnkwith".$where;
      // exit;
     $qury=mysqli_query($con,"select id,$col_nm from $lnkwith".$where);
     ?>
    <option value=''>--SELECT--</option>";
    <?php  while($row = mysqli_fetch_object($qury)) 
     {
      echo "<option value='".$row->id."'>".$row->$col_nm."</option>";
     }
  }else
  {
    echo "<option value='0'>NA</option>";
  }
  }
?>