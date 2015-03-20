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

$sql_query="SELECT DISTINCT Sc_project FROM schedule WHERE Sc_project!=''"; //get project which only belong to certain reservation
$test=mysql_query($sql_query,$ShareRack)or die(mysql_error());

while($row_test=mysql_fetch_assoc($test)){
 $Sc_project[]=$row_test['Sc_project'];
}

//------------------------------------------------------------------------------------------------- Action -->
//--------------------------------------------------------------------- Return
if(isset($_POST["action"])&&($_POST["action"]=="Return")){
   header("Location:../index.php?Mode=$Mode");	
//--------------------------------------------------------------------- Generate Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Generate Report")){
//--------------------- combine the string of selected project
   foreach($_POST['project']as $value){
     $sql_query = "SELECT P_ID FROM project WHERE `P_Name`='$value'"; //get project info which selected by user
     $test = mysql_query($sql_query, $ShareRack) or die(mysql_error());
     $row_test = mysql_fetch_assoc($test);
     $P_ID_List[]=$row_test['P_ID'];  
   }
   $P_ID_List=implode(",",(array)$P_ID_List); //combine each project info as a string
//--------------------- 
   $BeginDate=$_POST["BeginDate"];
   $EndDate=$_POST["EndDate"];
 
   header("Location:project_report.php?PJ=$P_ID_List&BG=$BeginDate&END=$EndDate"); //run the "project report" page with project/begin/end date
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
 $("#GenerateReportProject").click(function(){
   var project = $("#project").val();
   var BeginDate = $("#BeginDate").val();
   var EndDate = $("#EndDate").val();   
//--------------------- "does not selected any project" alert    
   if(project==null){
     alert('You have not selected any project !');
	 return false;
   }else if( (BeginDate.length!=10 && BeginDate!='' && BeginDate!='Select Begin Date') || (EndDate.length!=10 && EndDate!='' && EndDate!='Select End Date') ) {
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
$here="Report-Project";//Tell page header to underline Home tab
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
<h2>Project Utilization Report</h2>
<hr><br>
<!-- ----------------------------------------------------------------------- Return -->
<form method="post" action="">
  <input type="submit" class="button" value="Return">
  <input type="hidden" name="action" id="action" value="Return">
</form>
<br><br><br><br>
<!-- ----------------------------------------------------------------------- Select Project AND BEGIN & END Date -->
<form method="post" action="">
  <h3>Select Project: (Required)</h3>
  <table>
   <tr>
    <td colspan="30" style="border:none;padding:0;"><br>
     <center>
      <select name="project[]" id="project" multiple="multiple" size="5">
        <?php 
	    for($i=0;$i<count($Sc_project);$i++){?>
          <option> <?php echo $Sc_project[$i]."<br>";
	    }?>
      </select>
      <br><br> 	
      <font color="blue">( Multiple choice )</font>	
      <br><br><br><br>
      <h3>Select Time Interval: (Optional)</h3>
      <br>
	  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
	  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
      <input type="text" name="BeginDate" id="BeginDate" size="15" value="Select Begin Date">&nbsp&nbsp&nbsp&nbsp
      <input type="text" name="EndDate" id="EndDate" size="15" value="Select End Date">&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
      <br><br>
      <font color="blue">( If leaving blank, system will automatically calculate from project begin date till end date )</font>
	  <br><br><br><br>
	 </center>
    </td>
   </tr>
   <tr>
    <td colspan="30" style="border:none;padding:0;">
     <input type="submit" id="GenerateReportProject" class="button" value="Generate Report">
     <input type="hidden" name="action" id="action" value="Generate Report">
    </td>
   </tr>
  </table>
</form>
<br><br>
<!-- ------------------------------------------------------------------------------------------------- --> 
</center>
</div>
</body>
</html>