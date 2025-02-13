<?php require_once('../../../config.php'); ?>

<?php
if($_POST['type']=='head')
{
  if(isset($_POST['othrhead'])) {
    $othrhead = $_POST['othrhead'];
    $sql = mysqli_query($con,"SELECT `lnkwith`, `col_nm` FROM `fin_grouping_subtype` WHERE `id`='$othrhead'");
    $res = mysqli_fetch_object($sql);
    $lnkwith=$res->lnkwith;
    $col_nm=$res->col_nm;
  ?>
    <option value=''>--- Select A Particular Name ---</option>";
 <?php
    if($col_nm != '') {
      if($othrhead==1 || $othrhead==2 || $othrhead==3 || $othrhead==10 || $othrhead==38 || $othrhead==65 || $othrhead==66 || $othrhead==67 || $othrhead==78)
      {
        $where="";
      }
      else 
      {
        $where=" WHERE group_subtype=$othrhead";
      }
      $qury=mysqli_query($con,"SELECT `id`, $col_nm FROM $lnkwith".$where);
      while($row = mysqli_fetch_object($qury)){
        echo "<option value='".$row->id."'>".$row->$col_nm."</option>";
      }
    }
    else {
      echo "<option value='0'>NA</option>";
    }
  }
} 
  else if($_POST['type']=='prjnm')
{
  if(isset($_POST['prjid'])) {
   $prjid = $_POST['prjid'];
   $spqr = mysqli_query($con, "SELECT id,spname FROM `prj_subproject` WHERE `pid`='$prjid'");
   if(mysqli_num_rows($spqr) > 0) 
   {
      echo "<option value=''>--- Select Sub Project ---</option>";
      while($sprjnm = mysqli_fetch_object($spqr))
      {
      echo "<option value='".$sprjnm->id."'>".$sprjnm->spname."</option>";
      }
   }
   else 
   {
    echo "<option value=''>No Sub Project Found</option>";
   }
  }
}
 ?>