<?php
$dirLevel="../";
//------------------------------------------------------------------------------------ GET Variable
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];}

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------ Action -->	
//--------------------------------------------------------------------- Chassis Utilization Report
if(isset($_POST["action"])&&($_POST["action"]=="Chassis Utilization")){
  header("Location:chassis/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Project Utilization Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Project Utilization")){
  header("Location:project/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Team Utilization Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Team Utilization")){
  header("Location:team/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Status Change Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Status Change")){
  header("Location:status/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Equipment Failure Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Equipment Failure")){
  header("Location:failure/index.php?Mode=$Mode");
}?>

<!-- ------------------------------------------------------------------------------------ -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Report | Moonshot Shared Rack</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../css/main.css" />
<link rel="stylesheet" href="../css/main.ie.css" />
<link rel="stylesheet" href="../css/manage_schedule_index.css" />

<script src="../js/general.js"></script>
<script src="../js/ajax.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Report";//Tell page header to underline Home tab
if($Mode=="User"){
  $pages=array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode"); 
}elseif($Mode=="Admin"){
  $pages=array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode",  
	"Management"=>$dirLevel."management.php?Mode=$Mode");       
  $headerAppend=" - Management"; 
  }  
include $dirLevel."include/header.php";
?>

<!-- ----------------------------------------------------------------------- Head Title -->
<div class="content">
<h2>Generate Report</h2>
<hr><br>
<!-- ----------------------------------------------------------------------- Select Report Type -->
<br><br>
<h2 class="subheading">Select Report type :</h2>
<br><br>
<center>
  <table>
    <tr>
      <td colspan="30" style="border:none;padding:0;">
        <form id="form" name="form" method="post" action="">
          <input name="action" type="hidden" id="action" value="Chassis Utilization">
          <input type="submit" class="button" value="Chassis Utilization">
        </form>
        <br><br><br><br><br>
        <form id="form" name="form" method="post" action="">
          <input name="action" type="hidden" id="action" value="Project Utilization">
          <input type="submit" class="button" value=" Project Utilization ">
        </form>  
        <br><br><br><br><br>
        <form id="form" name="form" method="post" action="">
          <input name="action" type="hidden" id="action" value="Team Utilization">
          <input type="submit" class="button" value="  Team Utilization   ">
        </form>
        <br><br><br><br><br>
        <form id="form" name="form" method="post" action="">
          <input name="action" type="hidden" id="action" value="Status Change">
          <input type="submit" class="button" value="   Status Change    ">
        </form>
        <br><br><br><br><br>
        <form id="form" name="form" method="post" action="">
          <input name="action" type="hidden" id="action" value="Equipment Failure">
          <input type="submit" class="button" value=" Equipment Failure ">
        </form> 
      </td>
    </tr>
  </table>
</center>
<!-- ------------------------------------------------------------------------------------------------- -->	
</div>
</body>