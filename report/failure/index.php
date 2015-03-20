<?php
$dirLevel = "../../";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode2="";
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //action mode  

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack,$ShareRack);

$sql_query1 = "SELECT Ch_ChassisName,Ch_remove,Using_Status FROM chassis_setting"; //get chassis info
$test1 = mysql_query($sql_query1, $ShareRack) or die(mysql_error());
$sql_query2 = "SELECT * FROM status_failure WHERE `Sf_latest`='1'"; //get "just happened" failures 
$test2 = mysql_query($sql_query2, $ShareRack) or die(mysql_error());
$sql_query3 = "SELECT * FROM status_failure WHERE `Sf_latest`='2' AND `Sf_fix`='no'"; //get "not just happened" failures 
$test3 = mysql_query($sql_query3, $ShareRack) or die(mysql_error());
$sql_query4 = "SELECT Sf_ID FROM status_failure"; //get all failures record
$test4 = mysql_query($sql_query4, $ShareRack) or die(mysql_error());
$sql_query5 = "SELECT Tr_time FROM Time_record WHERE `Tr_type`='Status_update_time'"; //get latest update time
$test5 = mysql_query($sql_query5, $ShareRack) or die(mysql_error());

while($row_test1 = mysql_fetch_assoc($test1)){
 $Ch_ChassisName[]=$row_test1['Ch_ChassisName'];
 $Ch_remove[]=$row_test1['Ch_remove'];
 $Using_Status[]=$row_test1['Using_Status'];
}?>
<?php
$Sf_name=array();
while($row_test2 = mysql_fetch_assoc($test2)){
 $Sf_date[]=$row_test2['Sf_date'];
 $Sf_type[]=$row_test2['Sf_type'];
 $Sf_name[]=$row_test2['Sf_name'];
 $Sf_chassis[]=$row_test2['Sf_chassis'];
 $Sf_status[]=$row_test2['Sf_status'];
}?>
<?php
$Sf_name2=array();
while($row_test3 = mysql_fetch_assoc($test3)){
 $Sf_date2[]=$row_test3['Sf_date'];
 $Sf_type2[]=$row_test3['Sf_type'];
 $Sf_name2[]=$row_test3['Sf_name'];
 $Sf_chassis2[]=$row_test3['Sf_chassis'];
 $Sf_status2[]=$row_test3['Sf_status'];
}?>
<?php
$Sf_ID=array();
while($row_test4 = mysql_fetch_assoc($test4)){
 $Sf_ID[]=$row_test4['Sf_ID'];
}?>
<?php
$row_test5 = mysql_fetch_assoc($test5);
$Tr_time=$row_test5['Tr_time'];

//------------------------------------------------------------------------------------------------- Action -->	
//--------------------------------------------------------------------- Return
if(isset($_POST["action"])&&($_POST["action"]=="Return")){
   header("Location:../index.php?Mode=$Mode"); //return to upper layer	
//--------------------------------------------------------------------- Generate Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Generate Report")){
  //$CH=$_POST['Chassis'];
  //$SW=$_POST['Switch'];
  //$CA=$_POST['Cartridge'];
  $BeginDate=$_POST["BeginDate"];
  $EndDate=$_POST["EndDate"];
//---------------------  
  if(count($Sf_ID)>0){ //check if database has any failure record or not  
    //header("Location:failure_report.php?CH=$CH&SW=$SW&CA=$CA&BG=$BeginDate&END=$EndDate"); //run the "generate failure report" page
    header("Location:failure_report.php?BG=$BeginDate&END=$EndDate"); //run the "generate failure report" page with begin/end date
  }else{
	header("Location:.?Mode=$Mode&Mode2=NotRecord"); //reload the page with "not any historical failure exist" status 
  }
}?>

<!-- ------------------------------------------------------------------------------------------------- -->	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Report | Moonshot Shared Rack</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../../css/main.css" />
<link rel="stylesheet" href="../../css/main.ie.css" />
<link rel="stylesheet" href="../../css/manage_schedule_index.css" />

<script src="../../js/general.js"></script>
<script src="../../js/ajax.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
//------------------------------------------ Calendar
$(function() {
	$( "#BeginDate" ).datepicker();
    $( "#EndDate" ).datepicker();
});

function setDate(elem,dt)
{
    $( elem ).datepicker("setDate", new Date(parseInt(dt)));
}
//------------------------------------------ Select Time Interval 
$(function(){
 $("#GenerateReportFailure").click(function(){
   var BeginDate = $("#BeginDate").val();
   var EndDate = $("#EndDate").val();
   
   if( (BeginDate.length!=10 && BeginDate!='' && BeginDate!='Select Begin Date') || (EndDate.length!=10 && EndDate!='' && EndDate!='Select End Date') ) {
     alert('The date format is incorrect !');
     return false;     
   }else{ //interval date incorrect  
     if(BeginDate!='' && BeginDate!='Select Begin Date' && EndDate!='' && EndDate!='Select End Date' && ((BeginDate.substring(6,10))+(BeginDate.substring(0,2))+(BeginDate.substring(3,5))) > ((EndDate.substring(6,10))+(EndDate.substring(0,2))+(EndDate.substring(3,5))) ) {
       alert('End date shouldn not earlier than begin date !');
       return false;     	 
     }
   }
//---------------------     
 });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php
$here="Report-Failure";//Tell page header to underline Home tab
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
<center>
<h2>Equipment Failure Report</h2>
<hr><br>
<!-- ----------------------------------------------------------------------- Return -->
<form method="post" action="">
  <input type="submit" class="button" value="Return">
  <input type="hidden" name="action" id="action" value="Return">
</form>
<br><br><br><br>
<!-- ----------------------------------------------------------------------- Generate Report -->
<form method="post" action="">
<!--
	<h3>Select Equipment Type: (Optional)
	<br><br>
	<input name="Chassis" type="Checkbox" value="Chassis"> Chassis &nbsp&nbsp
	<input name="Switch" type="Checkbox" value="Switch"> Switch &nbsp&nbsp
    <input name="Cartridge" type="Checkbox" value="Cartridge"> Cartridge
	</h3>
	<font color="blue">( Multiple choice )</font>    
	<br><br><br><br>
-->
<h3>Select Time Interval: (Optional)</h3>
<table>
 <tr>
  <td colspan="30" style="border:none;padding:0;"><br>
   <center>
    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
	&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp 		   
    <input type="text" name="BeginDate" id="BeginDate" size="15" value="Select Begin Date">&nbsp&nbsp&nbsp&nbsp
    <input type="text" name="EndDate" id="EndDate" size="15" value="Select End Date">&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
	<br><br>
    <font color="blue">( If leaving blank, system will automatically calculate from Shared Rack deploy date till today )</font><br><br><br>
    <br>
   </center>
  </td>
 </tr>
 <tr>
   <td colspan="30" style="border:none;padding:0;">
    <input type="submit" id="GenerateReportFailure" class="button" value="Generate Report">
    <input type="hidden" name="action" id="action" value="Generate Report">
  </td>
 </tr>
</table>
</form>
<br><br><br>
<!-- ----------------------------------------------------------------------- New Failure Issues--> 
  <h3>New Failure Issues:</h3>
  <font color="grey">(Last update: <?php echo $Tr_time;?>)</font>
  <br><br><br>  
  <center>
  <?php  
  if(count($Sf_name)==0){ //check if database has any new failure record or not  
    echo "<h2><font color='green'>There's not any new issue</font><h2>"; 
  }else{ ?>
    <table class="infotable" cellspacing="0">
      <tr>
        <th scope="col" class="first"></th>
        <th scope="col">Time</th>
        <th scope="col">Inventory Type</th>
        <th scope="col">Inventory Name</th>
        <th scope="col">Belong to Chassis</th>
        <th scope="col">Failure Message</th>
      </tr>
      <?php 
      $n2=0;$alt = 1;
      for($i=0;$i<count($Sf_name);$i++){ 
        $n2++; ?>
        <tr>
          <td class="first<?php if ($alt==1) {echo ' alt';} ?>">
		    <font color="red"><?php echo $n2; ?></font></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_date[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_type[$i]; ?></font></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_name[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_chassis[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_status[$i]; ?></font>
		  </td>
        </tr>
      <?php	   
	  }?>
    </table>
  <?php	   
  } ?>
  </center> 
  <br><br><br>
<!-- ----------------------------------------------------------------------- The other Unfixed Failure Issues--> 
  <h3>The other Unfixed Failure Issues:</h3>
  <font color="grey">(Last update: <?php echo $Tr_time;?>)</font>
  <br><br><br>
  <center>
  <?php  
  if(count($Sf_name2)!=0){?> <!--check if database has any old unfixed failure record or not-->   
    <table class="infotable" cellspacing="0">
      <tr>
        <th scope="col" class="first"></th>
        <th scope="col">Time</th>
        <th scope="col">Inventory Type</th>
        <th scope="col">Inventory Name</th>
        <th scope="col">Belong to Chassis</th>
        <th scope="col">Failure Message</th>
      </tr>
      <?php 
      $n2=0;$alt = 1;
      for($i=0;$i<count($Sf_name2);$i++){ 
        $n2++; ?>
        <tr>
          <td class="first<?php if ($alt==1) {echo ' alt';} ?>">
		    <font color="red"><?php echo $n2; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_date2[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_type2[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_name2[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_chassis2[$i]; ?></font>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <font color="red"><?php echo $Sf_status2[$i]; ?></font>
		  </td>
        </tr>
      <?php	   
	  }?>
    </table>
  <?php
// ------------------------------- display there's not any failure   
  }else{
     echo "<h2><font color='green'>There's not any unfixed issue</font><h2>"; 
  }?>
  </center>
<!-- ----------------------------------------------------------------------- different status alert -->  
<?php
if($Mode2=="NotRecord"){ //display "not any failure records exist" alert
    echo "<script type='text/javascript'>alert('There is not any records exist !');</script>";
}?>
<!-- -------------------------------------------------------------------------------------------------  -->
</div>
</body>
</html>