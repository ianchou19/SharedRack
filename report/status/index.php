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

$sql_query1 = "SELECT Ch_remove FROM chassis_setting WHERE `Ch_ChassisName`!='' AND `Ch_remove`='remove'"; //get chassis info which being removed and has no name
$test1 = mysql_query($sql_query1, $ShareRack) or die(mysql_error());
$sql_query2 = "SELECT * FROM status_changing WHERE `Stc_Acknowledge`='no'"; //get changing record which haven't being acknowledged""
$test2 = mysql_query($sql_query2, $ShareRack) or die(mysql_error());
$sql_query3 = "SELECT Stc_ID FROM status_changing"; //get all changing record
$test3 = mysql_query($sql_query3, $ShareRack) or die(mysql_error());
$sql_query4 = "SELECT Tr_time FROM Time_record WHERE `Tr_type`='Status_update_time'"; //get latest update time
$test4 = mysql_query($sql_query4, $ShareRack) or die(mysql_error());

$Ch_remove=array();
while($row_test1 = mysql_fetch_assoc($test1)){
 $Ch_remove[]=$row_test1['Ch_remove'];
}?>
<?php
$Stc_ID=array();
while($row_test2 = mysql_fetch_assoc($test2)){
 $Stc_ID[]=$row_test2['Stc_ID'];
 $Stc_Date[]=$row_test2['Stc_Date'];
 $Stc_ChassisName[]=$row_test2['Stc_ChassisName'];
 $Stc_StatusMessage[]=$row_test2['Stc_StatusMessage'];
}?>
<?php
$row_test4 = mysql_fetch_assoc($test4);
$Tr_time=$row_test4['Tr_time'];

//------------------------------------------------------------------------------------------------- Action -->
//--------------------------------------------------------------------- Return
if(isset($_POST["action"])&&($_POST["action"]=="Return")){
   header("Location:../index.php?Mode=$Mode"); //return to upper layer		
//--------------------------------------------------------------------- Generate Report
}elseif(isset($_POST["action"])&&($_POST["action"]=="Generate Report")){
   $BeginDate=$_POST["BeginDate"];
   $EndDate=$_POST["EndDate"];
//---------------------  
  if(count($Stc_ID)>0){ //check if database has any changing record or not  
    header("Location:status_report.php?BG=$BeginDate&END=$EndDate"); //run the "generate status report" page with begin/end date
  }else{
	header("Location:.?Mode=$Mode&Mode2=NotRecord"); //reload the page with "not any historical changing exist" status 
  }
//--------------------------------------------------------------------- Acknowledge Change
}elseif(isset($_POST["action"])&&($_POST["action"]=="Acknowledge Change")){
   foreach($_POST['acknowledge']as $value){
     $Acknowledge_List[]=$value;
   }
   $Acknowledge_List=implode("",(array)$Acknowledge_List);
   header("Location:equips_yesterday_status.php?Mode=$Mode&ACK=$Acknowledge_List");
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
 $("#GenerateReportChange").click(function(){
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
//------------------------------------------ Acknowledge Change
$(function(){
 $("#AcknowledgeChange").click(function(){
   var CheckExist='';  
   $("input[name='acknowledge[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
//--------------------- "does not selected any issue" alert  
   if(CheckExist==''){
     alert('You have not selected any issue !');
     return false;
   }   
//--------------------- double confirm the action   
   else{
     if(!confirm('Are you sure to remove this issue ?')){
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
$here="Report-Change";//Tell page header to underline Home tab
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
<h2>Inventory Status Report</h2>
<hr><br>
<!-- ----------------------------------------------------------------------- Return -->
<form method="post" action="">
  <input type="submit" class="button" value="Return">
  <input type="hidden" name="action" id="action" value="Return">
</form>
<br><br><br><br>
<!-- ----------------------------------------------------------------------- Generate Report -->
<form method="post" action="">
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
          <font color="blue">( If leaving blank, system will automatically calculate from Shared Rack deploy date till today )</font>
		  <br><br><br><br>
        </center>
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;">
        <input type="submit" id="GenerateReportChange" class="button" value="Generate Report">
        <input type="hidden" name="action" id="action" value="Generate Report">
      </td>
    </tr>
  </table>
</form>
<br><br><br>
<!-- ----------------------------------------------------------------------- The Current Inventory Status Change--> 
<form method="post" action="">
<h3>The Current Inventory Status Change:</h3>
<font color="grey">(Last update: <?php echo $Tr_time;?>)</font>

<?php
if(count($Ch_remove)!=0){ ?>
<br><br><br>
  <center>
    <table class="infotable" cellspacing="0">
      <tr>
      <?php 
	  if($Mode=="Admin"){ ?>		
        <th scope="col" class="first">Select</th>
		<th scope="col">Time</th>
      <?php 
	  }elseif($Mode=="User"){ ?>
        <th scope="col" class="first">Time</th>
	  <?php
	  } ?>
        <th scope="col">Inventory Type</th>
        <th scope="col">Inventory Name</th>
        <th scope="col">Status Message</th>
      </tr>
      <?php 
      $alt = 1;
      for($i=0;$i<count($Stc_ID);$i++){ 
	   if($Mode=="Admin"){ ?>
         <tr>
           <td class="first<?php if ($alt==1) {echo ' alt';} ?>">
		     <input type="checkbox" name="acknowledge[]" id="acknowledge" value=<?php echo $Stc_ChassisName[$i] ;?> ></td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo $Stc_Date[$i]; ?></font>
		   </td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo "Chassis"; ?></font></td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo $Stc_ChassisName[$i]; ?></font>
		   </td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo $Stc_StatusMessage[$i]; ?></font>
		   </td>
         </tr>
		 <?php
       }elseif($Mode=="User"){?>
         <tr>
           <td class="first<?php if ($alt==1) {echo ' alt';} ?>">
		     <font color="red"><?php echo $Stc_Date[$i]; ?></font>
		   </td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo "Chassis"; ?></font>
		   </td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo $Stc_ChassisName[$i]; ?></font>
		   </td>
           <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		     <font color="red"><?php echo $Stc_StatusMessage[$i]; ?></font>
		   </td>
         </tr>  
         <?php
	   } 
	  }?>
      <tr>
        <td colspan="30" style="border:none;padding:0;">
        <?php 
        if(count($Stc_ID)!=0&&$Mode=="Admin"){?>
          <br><br>
          <input type="submit" id="AcknowledgeChange" class="button" value="Acknowledge Change">
          <input type="hidden" name="action" id="action" value="Acknowledge Change">
        <?php 
        }?>
        </td>
      </tr>
    </table>
  </center>
</form>
<?php 
// ------------------------------- display there's not any status change
}else{
  echo "<br><br><br><h2><font color='green'>There's not any status change</font></h2>";
}?>
<!-- ----------------------------------------------------------------------- different status alert -->  
<?php
if($Mode2=="NotRecord"){ //display "not any failure records exist" alert
    echo "<script type='text/javascript'>alert('There is not any records exist !');</script>";
}?>
<!-- -------------------------------------------------------------------------------------------------  -->
</center>
</div>
</body>
</html>