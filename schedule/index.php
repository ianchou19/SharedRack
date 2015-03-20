<?php
$dirLevel="../";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode2="";
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //action mode
//---------------------
if($Mode=="User"){
  if(isset($_GET['P_Begin'])){$P_Begin=$_GET['P_Begin'];} //project begin date
  if(isset($_GET['P_End'])){$P_End=$_GET['P_End'];}// project end date
}

//------------------------------------------------------------------------------------------------- Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query1="SELECT Ch_ChassisName FROM chassis_info"; //get chassis name 
$test1=mysql_query($sql_query1, $ShareRack) or die(mysql_error());
$sql_query2 = "SELECT * FROM project"; //get project name
$test2=mysql_query($sql_query2, $ShareRack) or die(mysql_error());
$sql_query3 = "SELECT * FROM team"; //get team name
$test3=mysql_query($sql_query3, $ShareRack) or die(mysql_error());
$sql_query4="SELECT * FROM schedule ORDER BY Sc_start2 ASC"; //get reservation info which order by begin date (for request display)
$test4=mysql_query($sql_query4, $ShareRack) or die(mysql_error());

while($row_test1 = mysql_fetch_assoc($test1)){
 $Ch_ChassisName[]=$row_test1['Ch_ChassisName'];
}?>
<?php
while($row_test2 = mysql_fetch_assoc($test2)){
 $P_Name[]=$row_test2['P_Name'];
 $P_BeginDate[]=$row_test2['P_BeginDate'];
 $P_EndDate[]=$row_test2['P_EndDate'];
 $P_Permit[]=$row_test2['P_Permit'];
}?>
<?php
while($row_test3 = mysql_fetch_assoc($test3)){
 $T_Name[]=$row_test3['T_Name'];
 $T_Permit[]=$row_test3['T_Permit'];
}?>
<?php
$Sc_chassis=array();
while($row_test4 = mysql_fetch_assoc($test4)){
 $Sc_id[]=$row_test4['Sc_id'];
 $Sc_chassis[]=$row_test4['Sc_chassis'];
 $Sc_project[]=$row_test4['Sc_project'];
 $Sc_team[]=$row_test4['Sc_team'];
 $Sc_email[]=$row_test4['Sc_email'];
 $Sc_start2[]=$row_test4['Sc_start2'];
 $Sc_end2[]=$row_test4['Sc_end2'];
 $Sc_comment[]=$row_test4['Sc_comment']; 
 $Sc_permit[]=$row_test4['Sc_permit'];
} 

//----------------------------------------------------------------------------------------------------------------- Action -->	
//------------------------------------------------------------------------------------- Request Usage & Add Item 
if((isset($_POST["action"])&&$_POST["action"]=="Add Item")||(isset($_POST["action"])&&$_POST["action"]=="Request Usage")){
//--------------------- User Mode
  if($_POST["action"]=="Request Usage"){
    $Mode="User";   
//---------------------	Admin Mode
  }elseif($_POST["action"]=="Add Item"){
    $Mode="Admin";  
  }
  
//--------------------- get each info input by user
  $chassis=$_POST["chassis"];
  $project=$_POST["project"];
  $team=$_POST["team"];
  $eml=$_POST["eml"];
  $from=strtotime($_POST["from"])."000"; //transform the time format to which timeline can accept
  $to=strtotime($_POST["to"])."000"; //transform the time format to which timeline can accept
  $spec_instr=$_POST["spec_instr"];   
 
//--------------------- find the project info which selected by user   
  $query_test = "SELECT * FROM project WHERE P_Name='$project'";
  $test = mysql_query($query_test, $ShareRack) or die(mysql_error());
  $row_test = mysql_fetch_assoc($test);
  $P_BeginDate=strtotime($row_test['P_BeginDate'])."000";
  $P_EndDate=strtotime($row_test['P_EndDate'])."000";
 
//--------------------- check if reservation exceed project duration or not 
  if($from>=$P_BeginDate&&$to<=$P_EndDate){ //if not exceed  
//--------------------- check if the reserved chassis already being overlap in this time or not
    $Check_Overlap="no";
    for($i=0;$i<count($Sc_id);$i++){
      if( $Sc_chassis[$i]==$chassis && ( ($from<=$Sc_start2[$i]&&$to>=$Sc_start2[$i]) || ($from>=$Sc_start2[$i]&&$from<=$Sc_end2[$i]) ) ){ //if find the record with same chassis name and overlap reserved time 	  
	    $Check_Overlap="yes"; //change variable value if find the reserved chassis already being overlap in this time
      }
    } 
//--------------------- add new reservation into database and reload page with different status 
    if($Check_Overlap=="yes"){ 	
      header("Location:.?Mode=$Mode&Mode2=Overlap"); //reload the page with "reservation overlap" status	
	}else{	  
//--------------------- switch the date format for begin & end time
      $from2=date('M-d-Y',strtotime($_POST["from"]));   
      $to2=date('M-d-Y',strtotime($_POST["to"])); 	 
//--------------------- 
      if($Mode=="User"){
	    $sql_query="INSERT INTO `schedule`(`Sc_type`,`Sc_chassis`,`Sc_project`,`Sc_team`,`Sc_email`,`Sc_start`,`Sc_end`,`Sc_start2`,`Sc_end2`,`Sc_comment`,`Sc_permit`)VALUES('Normal','$chassis','$project','$team','$eml','$from2','$to2','$from','$to','$spec_instr','no')"; //insert new reservation request
        mysql_query($sql_query);
		
	    header("Location:mail_schedule.php?Mode=$Mode&Mode2=Request_Add"); //reload the page with "add reservation successfully" status			
	  }elseif($Mode=="Admin"){
	    $sql_query="INSERT INTO `schedule`(`Sc_type`,`Sc_chassis`,`Sc_project`,`Sc_team`,`Sc_email`,`Sc_start`,`Sc_end`,`Sc_start2`,`Sc_end2`,`Sc_comment`)VALUES('Normal','$chassis','$project','$team','$eml','$from2','$to2','$from','$to','$spec_instr')"; //insert new reservation request
        mysql_query($sql_query);	  
	    header("Location:.?Mode=$Mode&Mode2=Request_Add"); //reload the page with "add reservation successfully" status		
	  }
	}  
//--------------------- 
  }else{ //if exceed
    header("Location:.?Mode=$Mode&Mode2=ExceedProject"); //reload the page with "reservation time exceed project duration" status 
  }   
  
  
//------------------------------------------------------------------------------------- Scheduled Usage
}elseif(isset($_POST["action"])&&($_POST["action"]=="Scheduled Usage")){
  $Mode="Admin";
//--------------------- get each info input by user 
  $id=$_POST["id"];  
  $chassis=$_POST["chassis2"];
  $project=$_POST["project2"];
  $team=$_POST["team2"];
  $eml=$_POST["email2"];
  $from=strtotime($_POST["from2"])."000"; //transform the time format to which timeline can accept
  $to=strtotime($_POST["to2"])."000"; //transform the time format to which timeline can accept 
  $spec_instr=$_POST["spec_instr"];
//--------------------- switch the date format for begin & end time   
  $from2=date('M-d-Y',strtotime($_POST["from"]));   
  $to2=date('M-d-Y',strtotime($_POST["to"]));   
 
//--------------------- find the project info which selected by user 
  $query_test = "SELECT * FROM project WHERE P_Name='$project'";
  $test = mysql_query($query_test, $ShareRack) or die(mysql_error());  
  $row_test = mysql_fetch_assoc($test);
  $P_BeginDate=strtotime($row_test['P_BeginDate'])."000";;
  $P_EndDate=strtotime($row_test['P_EndDate'])."000";;

//--------------------- check if reservation exceed project duration or not   
  if($from>=$P_BeginDate&&$to<=$P_EndDate){ //if not exceed    
//--------------------- check if the reserved chassis already being overlap in this time or not
    $Check_Overlap="no";
    for($i=0;$i<count($Sc_id);$i++){
      if( $Sc_chassis[$i]==$chassis && ( ($from<=$Sc_start2[$i]&&$to>=$Sc_start2[$i]) || ($from>=$Sc_start2[$i]&&$from<=$Sc_end2[$i]) ) && $Sc_id[$i]!=$id ){ //if find the record with same chassis name and overlap reserved time 	  
	    $Check_Overlap="yes"; //change variable value if find the reserved chassis already being overlap in this time
      }
    } 
//--------------------- add new reservation into database and reload page with different status 
    if($Check_Overlap=="yes"){ 	
      header("Location:.?Mode=$Mode&Mode2=Overlap"); //reload the page with "reservation overlap" status	
	}else{	 
      $Save=$_POST["Save"];
      $Delete=$_POST["Delete"];	
	  
//--------------------- action under save button  	 
	  if($Save=="Save"){  
//--------------------- check if user selected new chassis/project/team or not	  
        $query_test = "SELECT * FROM schedule WHERE Sc_id='$id'";
        $test = mysql_query($query_test, $ShareRack) or die(mysql_error());  
        $row_test = mysql_fetch_assoc($test);

		if($chassis==""){ $chassis=$row_test['Sc_chassis']; }
		if($project==""){ $project=$row_test['Sc_project']; }
		if($team==""){ $team=$row_test['Sc_team']; }
		if($eml=="Type new email"){ $eml=$row_test['Sc_email']; }		
		if($_POST["from2"]=="Select new begin date"){ $from=$row_test['Sc_start2']; $from2=$row_test['Sc_start']; }
		if($_POST["to2"]=="Select new end date"){ $to=$row_test['Sc_end2']; $to2=$row_test['Sc_end']; }
		
//--------------------- 		
	    $query_update="UPDATE `schedule` SET `Sc_chassis`='$chassis',`Sc_project`='$project',`Sc_team`='$team',`Sc_email`='$eml',`Sc_start`='$from2',`Sc_end`='$to2',`Sc_start2`='$from',`Sc_end2`='$to' WHERE `Sc_id`='$id'";
        mysql_query($query_update);	   
	    header("Location:index.php?Mode=$Mode&Mode2=Request_Edit");	
//--------------------- action under remove button 	   
      }elseif($Delete=="Delete"){
        $sql_query = "DELETE FROM `schedule` WHERE `Sc_id`='$id'";
        mysql_query($sql_query);
	    header("Location:index.php?Mode=$Mode&Mode2=Request_Remove");
      }
	}  
  }else{ //if exceed
    header("Location:index.php?Mode=$Mode&Mode2=ExceedProject"); //reload the page with "reservation time exceed project duration" status 
  }  	 
   
   
//------------------------------------------------------------------------------------- Shift
}elseif(isset($_POST["action"])&&($_POST["action"]=="Shift")){
   $chassis=$_POST["chassis"];
   $from=strtotime($_POST["from"])."000";
   $to=strtotime($_POST["to"])."000";

   for($i=0;$i<count($Sc_id);$i++){
     if($Sc_chassis[$i]==$chassis){  
       if($from<=$Sc_start2[$i]&&$to>=$Sc_start2[$i]){ 
	     $dif=($to/1000-$Sc_start2[$i]/1000);//add "schedule begine till $to" since this schedule begin
  	     for($i2=$i;$i2<count($Sc_id);$i2++){
	       if($Sc_chassis[$i2]==$chassis){
    	     $newStart=($Sc_start2[$i2]/1000+$dif+86400)."000";
	         $newEnd=($Sc_end2[$i2]/1000+$dif+86400)."000";
	         $Sc_id2=$Sc_id[$i2];
	         $query_update="UPDATE `schedule` SET `Sc_start2`='$newStart',`Sc_end2`='$newEnd' WHERE `Sc_id`='$Sc_id2'";
             mysql_query($query_update);
	         $sql_query = "INSERT INTO `schedule`(`Sc_team`,`Sc_project`)VALUES('$i','$Sc_id2')";
	         mysql_query($sql_query);
	       }
	     }
	     break;
      }
   
      if($from>=$Sc_start2[$i]&&$from<=$Sc_end2[$i]){ 
	    $Sc_id_=$Sc_id[$i];
	    $Sc_type_=$Sc_type[$i];
	    $Sc_chassis_=$Sc_chassis[$i];
	    $Sc_project_=$Sc_project[$i];
	    $Sc_team_=$Sc_team[$i];
	    $Sc_email_=$Sc_email[$i];
	    $Sc_start2_=$Sc_start2[$i];
	    $Sc_end2_=$Sc_end2[$i];
	    $Sc_comment_=$Sc_comment[$i];
	    $Sc_permit_=$Sc_permit[$i];
	    $newEnd_=($from/1000-86400)."000";
	    $query_update="UPDATE `schedule` SET `Sc_end2`='$newEnd_' WHERE `Sc_id`='$Sc_id_'";
	    mysql_query($query_update);
	 
	    $newStart_=($to/1000+86400)."000";
	    $newEnd2_=($newStart_/1000+($Sc_end2_-$from)/1000)."000";
	    $sql_query = "INSERT INTO `schedule`(`Sc_type`,`Sc_chassis`,`Sc_project`,`Sc_team`,`Sc_email`,`Sc_start2`,`Sc_end2`,`Sc_comment`)VALUES('$Sc_type_','$Sc_chassis_','$Sc_project_','$Sc_team_','$Sc_email_','$newStart_','$newEnd2_','$Sc_comment_')";
	    mysql_query($sql_query);	 
	 
	    $dif2=($to/1000-$from/1000);
	
	    for($i2=$i+1;$i2<count($Sc_id);$i2++){
	      if($Sc_chassis[$i2]==$chassis){
	        $newStart=($Sc_start2[$i2]/1000+$dif2+86400)."000";
	        $newEnd=($Sc_end2[$i2]/1000+$dif2+86400)."000";
	        $Sc_id2=$Sc_id[$i2];
	        $query_update="UPDATE `schedule` SET `Sc_start2`='$newStart',`Sc_end2`='$newEnd' WHERE `Sc_id`='$Sc_id2'";
            mysql_query($query_update);
	      }
	    }
	    break;
      }
    }
  }
  header("Location:index.php?Mode=Admin");
//------------------------------------------------------------------------------------- Confirm Request
}elseif(isset($_POST["action"])&&($_POST["action"]=="Confirm Request")){
  foreach($_POST['schedule']as $value){
    $Button_Accept=$_POST["Accept"];
    $Button_Reject=$_POST["Reject"];
	
    if($Button_Accept=="Accept"){
       $sql_query = "UPDATE `schedule` SET `Sc_permit`='' WHERE `Sc_id`='$value'";
       mysql_query($sql_query);	  
	   $Mode2="Accept_Request";
    }elseif($Button_Reject=="Reject"){
	   $Mode2="Reject_Request";
    }
  } 
  $Sc_id=$value;
  header("Location:mail_schedule.php?Mode=$Mode&Mode2=$Mode2&Sc_id=$Sc_id");
}?>

<!-- ------------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Schedule Management | Moonshot Shared Rack</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../css/main.css" />
<link rel="stylesheet" href="../css/main.ie.css" />
<link rel="stylesheet" href="../css/manage_schedule_index.css" />

<script src="../js/general.js"></script>
<script src="../js/ajax.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
//------------------------------------------ Calendar
$(function() {
    $( "#fromDate-1" ).datepicker();
    $( "#toDate-1" ).datepicker();
    $( "#fromDate-2" ).datepicker();
    $( "#toDate-2" ).datepicker();
	$( "#fromDate-3" ).datepicker();
    $( "#toDate-3" ).datepicker();
});
//------------------------------------------ Set Today As Default Day
function setDate(elem,dt)
{
    $("#fromDate-1" ).datepicker("setDate", new Date(parseInt(dt)));
}
//------------------------------------------ Request Usage Request
$(function(){
 $("#RequestUsage_Request").click(function(){
   var Chassis = $("#Chassis").val();
   var Project = $("#Project").val();
   var Team = $("#Team").val();
   var Email = $("#Email").val();
   var From = $("#fromDate-1").val();   
   var To = $("#toDate-1").val(); 
   if( Chassis==''||Project==''||Team==''||Email==''||From==''||To==''){
     alert('Please do not leave blank!');
     return false;
   } 
   else if(From.length != 10 || To.length != 10) {
     alert('The date format is incorrect !');
     return false;     
   }   
   else if( ((From.substring(6,10))+(From.substring(0,2))+(From.substring(3,5))) > ((To.substring(6,10))+(To.substring(0,2))+(To.substring(3,5))) ) {
     alert('End date shouldn not earlier than begin date !');
     return false;     
   }   
   else if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Add Item Add
$(function(){
 $("#AddItem_Add").click(function(){
   var Chassis = $("#Chassis").val();
   var Project = $("#Project").val();
   var Team = $("#Team").val();
   var Email = $("#Email").val();
   var From = $("#fromDate-1").val();
   var To = $("#toDate-1").val();    
   if(Chassis==''||Project==''||Team==''||Email==''||From==''||To==''){
     alert('Please do not leave blank!');
     return false;
   }else if(From.length != 10 || To.length != 10) {
     alert('The date format is incorrect !');
     return false;     
   }else if( ((From.substring(6,10))+(From.substring(0,2))+(From.substring(3,5))) > ((To.substring(6,10))+(To.substring(0,2))+(To.substring(3,5))) ) {
     alert('End date shouldn not earlier than begin date !');
     return false;     
   }else if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Scheduled Usage Save
$(function(){
 $("#ScheduledUsage_Save").click(function(){
   var Chassis = $("#Chassis2").val();
   var Project = $("#Project2").val();
   var Team = $("#Team2").val();
   var Email = $("#Email2").val();
   var From2 = $("#fromDate-2").val();
   var To2 = $("#toDate-2").val();
   if(Chassis=='' && Project=='' && Team=='' && Email=='Type new email' && From2=='Select new begin date' && To2=='Select new end date'){
     alert('You did not do any change !');
     return false;
   }else if( (From2.length != 10 && From2 != '' && From2 != 'Select new begin date') || (To2.length != 10 && To2 != '' && To2 != 'Select new end date') ) {
     alert('The date format is incorrect !');
     return false;     
   }else if( From2!='Select new begin date' && To2!='Select new end date' && ((From2.substring(6,10))+(From2.substring(0,2))+(From2.substring(3,5))) > ((To2.substring(6,10))+(To2.substring(0,2))+(To2.substring(3,5))) ) {
     alert('End date shouldn not earlier than begin date !');
     return false;     
   }else if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Scheduled Usage Delete
$(function(){
 $("#ScheduledUsage_Delete").click(function(){
   if(!confirm('Are you sure to delete this schedule ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Accept Schedule Request
$(function(){
 $("#AcceptScheduleRequest").click(function(){
   var CheckExist=''; 
   $("input[name='schedule[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
   if(CheckExist==''){
     alert('You have not selected any schedule request !');
     return false;
   }else{
     if(!confirm('Are you sure to accept this request ?')){
       return false;	 
	 } 
   }  
 });
});
//------------------------------------------ Reject Schedule Request
$(function(){
 $("#RejectScheduleRequest").click(function(){
   var CheckExist=''; 
   $("input[name='schedule[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
   if(CheckExist==''){
     alert('You have not selected any schedule request !');
     return false;
   }else{
     if(!confirm('Are you sure to remove this request ?')){
       return false;	 
	 } 
   }  
 });
});
//------------------------------------------
function shiftWinOpen()
{
    showOverlay(4);
    document.schedshift.reset();
    document.getElementById("request-error-4").innerHTML = "&nbsp;";
    document.getElementById("request-success-4").innerHTML = "&nbsp;";
    document.schedshift.chassis.focus();
}

function shiftWinClose()
{
    document.getElementById("request-success-4").innerHTML = "&nbsp;";
    document.getElementById("overlay-form-content-4-2").style.display = "block"; document.getElementById("overlay-form-content-4-3").style.display = "none";
    hideOverlay(4);
}

var itemElem = null;
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Schedule";//Tell page header to underline Home tab
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

<!-- ----------------------------------------------------------------------- Request Usage & Add Item -->
<div id="overlay-3" class="overlay">
 <div id="overlay-container-3" class="overlay-container">
  <form name="schedadd" action="." method="post" enctype="multipart/form-data">
   <?php	
   if($Mode=="User"){?>
     <h2>Request Usage</h2>
   <?php	
   }elseif($Mode=="Admin"){?>
     <h2>Add Item</h2>     
   <?php
   }?>	
   <hr>
   <br>
   <div id="overlay-form-content-1">
     <label>Chassis</label>
	 <br>
      <select name="chassis" id="Chassis" style="width:140px;">
        <option value="" selected>Select a chassis...</option>
		<?php for($i=0;$i<count($Ch_ChassisName);$i++){ ?>
		<option> <?php echo $Ch_ChassisName[$i]."<br>";}?>
      </select>
	  <br><br>
	  <label>Project</label><br>
	  <select name="project" id="Project" style="width:140px;">
        <option value="" selected>Select a project...</option>
		<?php for($i=0;$i<count($P_Name);$i++){ ?>
		<option> <?php echo $P_Name[$i]."<br>"?></option><?php }?>
      </select>
	  <input type="button" name="Add Project" value="Add" onClick="window.location='../create/project?PJ=PJ&Mode=Admin'">
	  <br><br>
	  <label>Team</label>
	  <br>
	  <select name="team" id="Team" style="width:140px;">
	    <option value="" selected>Select a team...</option>
		<?php for($i=0;$i<count($T_Name);$i++){ ?>
		<option> <?php echo $T_Name[$i]."<br>";}?>
      </select>
	  <input type="button" name="Add Team" value="Add" onClick="window.location='../create/team?TM=TM&Mode=Admin'">
	  <br><br>
      <label>From</label>
	  <br>
      <input type="text" name="from" id="fromDate-1" style="width:150px;"/>
	  <br>&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
	  <br>
      <label>To</label>
	  <br>
      <input type="text" name="to" id="toDate-1" style="width:150px;"/>
	  <br>&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
	  <br><br>
      <label>Item Contact Email</label><br>
      <input type="text" name="eml" id="Email" style="width:220px;"/>
	  <br><br>
      <label>Special Instructions (Optional)</label>
	  <br>
      <textarea name="spec_instr" rows="5" cols="25" style="overflow-y:scroll;"></textarea>
	  <br>     
	  <span id="request-error-3" class="red">&nbsp;</span>
      <span id="request-success-3" class="green">&nbsp;</span>
    </div>
    <div id="overlay-form-content-3-2" class="overlay-form-content-centered">
<!-- -------------------------------- User Mode -->		
    <?php	
    if($Mode=="User"){?>
	  <input type="hidden" name="action" id="action" value="Request Usage">  
      <input type="submit" name="Request" id="RequestUsage_Request" class="button nofloat" value="Request">&nbsp&nbsp&nbsp&nbsp&nbsp
<!-- -------------------------------- Admin Mode -->	  
    <?php	
    }elseif($Mode=="Admin"){?>
	  <input type="hidden" name="action" id="action" value="Add Item"> 	
      <input type="submit" name="Add" id="AddItem_Add" class="button nofloat" value="Add">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
    <?php
    }?>	
<!-- -------------------------------- --> 
      <input type="button" class="button nofloat" value="Cancel" onclick="hideOverlay(3);">
    </div>
    </form>
  </div>
</div>


<!-- ----------------------------------------------------------------------- Scheduled Usage Box-->
<div id="overlay" class="overlay_background">&nbsp;</div>
<div id="overlay-1" class="overlay">
  <div id="overlay-container-1" class="overlay-container">
    <form name="scheddisp"  action="." method="post" enctype="multipart/form-data">
<!-- -------------------------------- User Mode -->		
   <?php	
   if($Mode=="User"){?>
     <h2>Check Usage</h2>
<!-- -------------------------------- Admin Mode -->		 
   <?php	
   }elseif($Mode=="Admin"){?>
     <h2>Scheduled Usage</h2>    
   <?php
   }?>	
<!-- -------------------------------- -->   
      <div id="overlay-form-content">
		<input type="hidden" name="id">
        <input type="hidden" name="type" style="width:170px;" />
<!-- -------------------------------- User Mode -->		
        <?php	
        if($Mode=="User"){?>
		  <hr>
	      <br>
          <label>Chassis</label>
		  <br>
          <input type="text" name="chassis" id="Chassis" style="width:230px;" disabled/>
		  <br><br>
          <label>Project Name</label><br>
          <input type="text" name="project" id="Project" style="width:230px;" disabled/>
		  <br><br>
	      <label>Team Name</label><br>
          <input type="text" name="team" id="Team" style="width:230px;" disabled/>
		  <br><br>
          <label>Group Contact Email</label><br>
          <input type="text" name="email" id="Email" style="width:230px;" disabled/>
		  <br><br>
          <label>From</label><br>
          <input type="text" name="from" id="fromDate-2" style="width:120px;" disabled/>
		  <br>
          <label>To</label><br>
          <input type="text" name="to" id="toDate-2" style="width:120px;" disabled/>
		  <br><br>
	      <label>Special Instructions</label><br>
          <textarea name="spec_instr" rows="4" cols="25" style="overflow-y:scroll;" disabled/></textarea>		  
		  <br><br>
        </div>
	    <br>
        <div id="overlay-form-content-4">
<!-- -------------------------------- Admin Mode -->		
        <?php	
        }elseif($Mode=="Admin"){?>	
		  <hr>
          <label>Current Chassis</label>
          <input type="text" name="chassis" id="Chassis" style="width:230px;" disabled/>
          <select name="chassis2" id="Chassis2" style="width:200px;">
            <option value="" selected>Change to new chassis...</option>
		    <?php for($i=0;$i<count($Ch_ChassisName);$i++){ ?>
		    <option> <?php echo $Ch_ChassisName[$i]."<br>";}?>
          </select>		 		  
		  <br><br>
		  
          <label>Current Project</label><br>
          <input type="text" name="project" id="Project" style="width:230px;" disabled/>
	      <select name="project2" id="Project2" style="width:200px;">
            <option value="" selected>Change to new project...</option>
		    <?php for($i=0;$i<count($P_Name);$i++){ ?>
		    <option> <?php echo $P_Name[$i]."<br>"?></option><?php }?>
          </select>		  
		  <br><br>
		  
	      <label>Current Team</label><br>
          <input type="text" name="team" id="Team" style="width:230px;" disabled/>
	      <select name="team2" id="Team2" style="width:200px;">
	        <option value="" selected>Change to new team...</option>
		    <?php for($i=0;$i<count($T_Name);$i++){ ?>
		    <option> <?php echo $T_Name[$i]."<br>";}?>
          </select>
	  
		  <br><br>
          <label>Current Contact Email</label><br>
          <input type="text" name="email" id="Email" style="width:230px;" disabled/>
		  <br>
		  <input type="text" name="email2" id="Email2" style="width:230px;" value="Type new email"/>
		  <br><br>
          <label>From</label><br>
          <input type="text" name="from" style="width:60px;" disabled/>&nbsp&nbsp&nbsp
		  <input type="text" name="from2" id="fromDate-2" style="width:170px;" value="Select new begin date"/>
		  <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
		  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
		  <br>
          <label>To</label><br>
          <input type="text" name="to" style="width:60px;" disabled/>&nbsp&nbsp&nbsp
		  <input type="text" name="to2" id="toDate-2" style="width:170px;" value="Select new end date"/>
		  <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
		  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
		  <br><br>
	      <label>Current Special Instructions</label><br>
          <textarea name="spec_instr" rows="2" cols="25" style="overflow-y:scroll;"/></textarea>
		  <br>
        </div>
        <div id="overlay-form-content-4">
	    <input type="hidden" name="action" id="action" value="Scheduled Usage">
        <input type="submit" name="Save" id="ScheduledUsage_Save" class="button nofloat" value="Save">
	    <input type="submit" name="Delete" id="ScheduledUsage_Delete" class="button nofloat" value="Delete">

        <?php
        }?>	
		
        <input type="button" class="button" value="Close" onclick="hideOverlay(1);">
      </div>
    </form>
  </div>
</div>

<!-- ----------------------------------------------------------------------- Shift Box (Admin Mode)-->
<?php	
if($Mode=="Admin"){?>
<div id="overlay-4" class="overlay">
  <div id="overlay-container-4" class="overlay-container">
    <form name="schedshift" action="." method="post" enctype="multipart/form-data">
    <h2>Shift Schedule</h2>
    <hr>
    <div id="overlay-form-content-1">
	  <br>
      <label>Chassis</label>
	  <br>
      <select name="chassis" style="width:150px;">
        <option value="" selected>Select a chassis...</option>
		<?php for($i=0;$i<count($Ch_ChassisName);$i++){ ?>
		<option> <?php echo $Ch_ChassisName[$i]."<br>";}?>
	  </select>
	  <br><br>
      <label>From</label>
	  <br>
      <input name="from" type="text" id="fromDate-3" style="width:160px;"/>
	  <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
	  <br><br>
	  <label>To</label>
	  <br>
      <input name="to" type="text" id="toDate-2" style="width:160px;"/>
	  <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )
	  <br>
      <span id="request-error-4" class="red">&nbsp;</span>
      <span id="request-success-4" class="green">&nbsp;</span>
    </div>
	
    <div id="overlay-form-content-4-2" class="overlay-form-content-centered">
      <input name="action" type="hidden" id="action" value="Shift">
	  <input id="submit-request" name="submit" class="button nofloat" type="submit" value="Shift" />
      <input class="button nofloat" type="button" value="Cancel" onclick="hideOverlay(4);"/>
    </div>
    <div id="overlay-form-content-4-3" class="overlay-form-content-centered" style="display:none;">
      <input class="button" type="button" value="Close" onclick="shiftWinClose();"/>
    </div>
    </form>
  </div>
</div>
<?php
}?>	

<!-- ----------------------------------------------------------------------- Schedule description -->
<div class="content">
  <h2 class="page-heading">Schedule Management</h2>
  <hr>
  <div id="schedule-column">
    <p>Below you will find the usage schedule for the shared rack. Here are some quick instructions on how to use it.</p>
    <ul>
<!-- -------------------------------- User Mode -->	
  <?php	
  if($Mode=="User"){?>	
      <li>Scroll or click-and-drag the scroll button at the bottom center of the schedule window to view all of the schedule.</li>
      <li>Click on a scheduled item to view more information about it, edit it, or delete it.</li>
      <li>Click on an empty slot in the schedule to add an item starting on that date.</li>
      <li>Shift creates a gap in the schedule between and including the specified dates.</li>
      <li>All actions on this page will produce an email alert to the group contact upon successful completion of the action.</li>
      <li>All requested usages will <span class="red">end at 12:01pm CST</span> of their last scheduled day.</li>
    </ul>		
    <input type="button" name="Add Project" value="Check  Project  Duration" class="button" onClick="window.location='../create/project/index.php?Mode=User&Mode3=EditPJ'">	
  </div>	
<!-- -------------------------------- Admin Mode -->	  
  <?php	
  }elseif($Mode=="Admin"){?>	  
      <li style="font-size:12px;font-weight:bold;">Click on an empty slot in the schedule to request usage starting on that date.</li>
      <li>Click on a scheduled item to view more information about it.</li>
      <li>Scroll or click-and-drag the scroll button at the bottom center of the schedule window to view all of the schedule.</li>	
      <li>All requested usages will <span class="red">end at 12:01pm CST</span> of their last scheduled day.</li>
    </ul>	
    <input type="button" class="button" value="Shift" onclick="shiftWinOpen();" />
  </div>	  
    
  <?php
  }?>				

<!-- ----------------------------------------------------------------------- insert iframe -->
<?php
if($Mode=="User"){?>
  <iframe frameborder="0" id="schedule-panel" src="schedule.php?Mode=User" style="width:100%;"></iframe>
<?php
}elseif($Mode=="Admin"){?>
  <iframe frameborder="0" id="schedule-panel" src="schedule.php?Mode=Admin" style="width:100%;"></iframe>
<?php  
}?>
  
  <div id="reqs">
</center>

<!-- ----------------------------------------------------------------------- Confirm schedule request (Admin Mode) -->
<?php	
if($Mode=="Admin"){?>	

<br>
<hr>
<h3 class="subheading">Shared Rack Chassis Scheduling Requests</h3><?php
date_default_timezone_set('America/Chicago');?>
<br>
 <form method="post" action="">
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="first">Chassis</th>
      <th scope="col">Project</th>
	  <th scope="col">Team</th>
      <th scope="col">Email</th>
      <th scope="col">From</th>
      <th scope="col">To</th>
      <th scope="col">Special Instructions</th>
      <th scope="col">Select</th>
    </tr>   
    <?php
    $alt = 1;
    for($i=0;$i<count($Sc_chassis);$i++){
      if($Sc_permit[$i]=="no"){
      ?>
        <tr>
          <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><?php echo $Sc_chassis[$i];?></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $Sc_project[$i];?></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $Sc_team[$i];?></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>><a href="mailto:<?php echo $Sc_email[$i]; ?>"><?php echo $Sc_email[$i];?></a></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo date("m/d/Y", (int)substr($Sc_start2[$i],0,-3));?></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo date("m/d/Y", (int)substr($Sc_end2[$i],0,-3));?></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $Sc_comment[$i]; ?></td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="checkbox" name="schedule[]" id="schedule" value="<?php echo $Sc_id[$i];?>">
		  </td>
        </tr>
        <?php
      }
	}?>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
		<input type="submit" name="Reject" id="RejectScheduleRequest" class="button" value="Reject" />
		<input type="submit" name="Accept" id="AcceptScheduleRequest" class="button" value="Accept" />
		<input type="hidden" name="action" id="action" value="Confirm Request">
	  </td>
    </tr>
  </table>	
 </form>
 
<?php
}?>	 
 
</div>
</div>
<!-- ----------------------------------------------------------------------- different status alert -->  
<?php
if($Mode2=="Request_Add"){
    echo "<script type='text/javascript'>alert('Add new schedule successfully !');</script>";
}elseif($Mode2=="ExceedProject"){
    echo "<script type='text/javascript'>alert('Schedule time is exceed Project Duration !');</script>";
}elseif($Mode2=="Overlap"){ //display "reservation overlap" alert
    echo "<script type='text/javascript'>alert('Your schedule overlap to the others !');</script>";
//------------------------------------------ 
}elseif($Mode2=="Request_Edit"){
    echo "<script type='text/javascript'>alert('Edit schedule successfully !');</script>";
}elseif($Mode2=="Request_Remove"){
    echo "<script type='text/javascript'>alert('Delete schedule successfully !');</script>";
}?>
<!-- ------------------------------------------------------------------------------------------------- -->
</body>
</html>