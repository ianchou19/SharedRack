<?php
$dirLevel="../";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode="";
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
//---------------------
if(isset($_GET['PJ'])){$Mode3=$_GET['PJ'];} //page type mode
if(isset($_GET['TM'])){$Mode3=$_GET['TM'];} //page type mode

//------------------------------------------------------------------------------------------------- Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$query_test1="SELECT * FROM chassis_info"; // get chassis info
$test1=mysql_query($query_test1,$ShareRack)or die(mysql_error());
$query_test2="SELECT * FROM project"; // get project info
$test2=mysql_query($query_test2,$ShareRack)or die(mysql_error());
$query_test3="SELECT * FROM team"; // get team info
$test3=mysql_query($query_test3,$ShareRack)or die(mysql_error());
$query_test4="SELECT * FROM schedule ORDER BY Sc_start2 ASC"; // put in order by schedule begin date
$test4=mysql_query($query_test4,$ShareRack)or die(mysql_error());

while($row_test2 = mysql_fetch_assoc($test2)){
$P_ID[]=$row_test2['P_ID'];
$P_Name[]=$row_test2['P_Name'];
$P_Name_Old[]=$row_test2['P_Name_Old'];
$P_BeginDate[]=$row_test2['P_BeginDate'];
$P_BeginDate_Old[]=$row_test2['P_BeginDate_Old'];
$P_EndDate[]=$row_test2['P_EndDate'];
$P_EndDate_Old[]=$row_test2['P_EndDate_Old'];
$P_Mail[]=$row_test2['P_Mail'];
$P_Mail_Old[]=$row_test2['P_Mail_Old'];
$P_RequestForm[]=$row_test2['P_RequestForm'];
$P_Permit[]=$row_test2['P_Permit'];
$P_Delete_Permit[]=$row_test2['P_Delete_Permit'];
}?>
<?php
while($row_test3 = mysql_fetch_assoc($test3)){
$T_ID[]=$row_test3['T_ID'];
$T_Name[]=$row_test3['T_Name'];
$T_Name_Old[]=$row_test3['T_Name_Old'];
$T_Mail[]=$row_test3['T_Mail'];
$T_Mail_Old[]=$row_test3['T_Mail_Old'];
$T_RequestForm[]=$row_test3['T_RequestForm'];
$T_Permit[]=$row_test3['T_Permit'];
$T_Delete_Permit[]=$row_test3['T_Delete_Permit'];
}

//------------------------------------------------------------------------------------------------- Action -->	
//--------------------------------------------------------------------- Add / Edit Chassis
if(isset($_POST["action"])&&($_POST["action"]=="Chassis")){
   header("Location:./chassis/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Add / Edit Project
}elseif(isset($_POST["action"])&&($_POST["action"]=="Project")){
   header("Location:./project/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Add / Edit Team
}elseif(isset($_POST["action"])&&($_POST["action"]=="Team")){
   header("Location:./team/index.php?Mode=$Mode");
//--------------------------------------------------------------------- Confirm New Project Requests
}elseif(isset($_POST["action"])&&($_POST["action"]=="Confirm New Project Requests")){
  foreach($_POST['project']as $value){
	$Button_Accept=$_POST["Accept"]; //set "accept button" 
    $Button_Reject=$_POST["Reject"]; //set "reject button" 
	
    $query_test = "SELECT P_Name,P_Name_Old,P_RequestForm FROM project WHERE P_ID='$value'"; //find project which selected by user 
    $test = mysql_query($query_test, $ShareRack) or die(mysql_error());
	 
    $row_test = mysql_fetch_assoc($test);
    $P_Name=$row_test['P_Name'];
    $P_Name_Old=$row_test['P_Name_Old'];
    $P_RequestForm=$row_test['P_RequestForm'];
	
    if($Button_Accept=="Accept"){ //if "accept button" is true	      
	  if($P_RequestForm=="Add New Project"){ //check Request type
         $sql_query = "UPDATE `project` SET `P_Permit`='' WHERE `P_ID`='$value'"; 
         mysql_query($sql_query);	  
		 $Mode2="Accept_Add";
	  }elseif($P_RequestForm=="Edit Old Project"){	
  	     $sql_query = "DELETE FROM `project` WHERE `P_Name`='$P_Name_Old'";
         mysql_query($sql_query);
		 $Mode2="Accept_Edit";
	  }elseif($P_RequestForm=="Delete Project"){
		 $Mode2="Accept_Remove";
	  }   
    }elseif($Button_Reject=="Reject"){ //if "reject button" is true
      if($P_RequestForm=="Add New Project"){ 
		 $Mode2="Reject_Add";
      }elseif($P_RequestForm=="Edit Old Project"){
		 $Mode2="Reject_Edit";
	  }elseif($P_RequestForm=="Delete Project"){
	     $sql_query = "UPDATE `project` SET `P_Delete_Permit`='' WHERE `P_ID`='$value'";
         mysql_query($sql_query);	
         $Mode2="Reject_Remove";		 
	  }	    
    }
  } 
  header("Location:mail_project.php?Mode=Admin&Mode2=$Mode2&P_Name=$P_Name");
  
//--------------------------------------------------------------------- Confirm New Team Requests
}elseif(isset($_POST["action"])&&($_POST["action"]=="Confirm New Team Requests")){
  foreach($_POST['team']as $value){
    $Button_Accept=$_POST["Accept"]; //set "accept button" 
    $Button_Reject=$_POST["Reject"]; //set "reject button" 
   
    $sql_query = "SELECT T_Name,T_Name_Old,T_RequestForm FROM team WHERE T_ID='$value'"; //find team which selected by user
    $test = mysql_query($sql_query, $ShareRack) or die(mysql_error());	

    $row_test = mysql_fetch_assoc($test);
    $T_Name=$row_test['T_Name'];
    $T_Name_Old=$row_test['T_Name_Old'];
    $T_RequestForm=$row_test['T_RequestForm'];	
  
    if($Button_Accept=="Accept"){ //if "accept button" is true	    
	  if($T_RequestForm=="Add New Team"){ //check Request type
         $sql_query = "UPDATE `team` SET `T_Permit`='' WHERE `T_ID`='$value'";
         mysql_query($sql_query); 
         $Mode2="Accept_Add";	
	  }elseif($T_RequestForm=="Edit Old Team"){
  	     $sql_query = "DELETE FROM `team` WHERE `T_Name`='$T_Name_Old'";
         mysql_query($sql_query);
		 $Mode2="Accept_Edit";
	  }elseif($T_RequestForm=="Delete Team"){
		 $Mode2="Accept_Remove";
	  }  
    }elseif($Button_Reject=="Reject"){ //if "reject button" is true
      if($T_RequestForm=="Add New Team"){ 
		 $Mode2="Reject_Add";
      }elseif($T_RequestForm=="Edit Old Team"){
         $Mode2="Reject_Edit";		 
	  }elseif($T_RequestForm=="Delete Team"){
         $sql_query = "UPDATE `team` SET `T_Delete_Permit`='' WHERE `T_ID`='$value'";
         mysql_query($sql_query);
         $Mode2="Reject_Remove";		 
	  }	    
    }
  } 
  header("Location:mail_team.php?Mode=Admin&Mode2=$Mode2&T_Name=$T_Name");
}?>

<!-- ----------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Create Item | Moonshot Shared Rack</title>
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
$(function(){
    $( "#BeginDate" ).datepicker();
    $( "#EndDate" ).datepicker();
});

function setDate(elem,dt){
    $( elem ).datepicker("setDate", new Date(parseInt(dt)));
}

//------------------------------------------ Project Request
//----------------------- Accept 
$(function(){
 $("#AcceptProjectRequest").click(function(){
   var CheckExist=''; 
   $("input[name='project[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
   if(CheckExist==''){
     alert('You have not selected any project request !');
     return false;
   }
   else{
     if(!confirm('Are you sure to accept this request ?')){
       return false;	 
	 } 
   }  
 });
});
//----------------------- Reject 
$(function(){
 $("#RejectProjectRequest").click(function(){
   var CheckExist=''; 
   $("input[name='project[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
   if(CheckExist==''){
     alert('You have not selected any project request !');
     return false;
   }
   else{
     if(!confirm('Are you sure to remove this request ?')){
       return false;	 
	 } 
   }  
 });
});

//------------------------------------------ Team Request
//----------------------- Accept 
$(function(){
 $("#AcceptTeamRequest").click(function(){
   var CheckExist=''; 
   $("input[name='team[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
   if(CheckExist==''){
     alert('You have not selected any team request !');
     return false;
   }
   else{
     if(!confirm('Are you sure to accept this request ?')){
       return false;	 
	 } 
   }  
 });
});
//----------------------- Reject 
$(function(){
 $("#RejectTeamRequest").click(function(){
   var CheckExist=''; 
   $("input[name='team[]']:checkbox:checked").each(function(){
	  CheckExist = $(this).val();  	   
   }); 
   if(CheckExist==''){
     alert('You have not selected any team request !');
     return false;
   }
   else{
     if(!confirm('Are you sure to remove this request ?')){
       return false;	 
	 } 
   }  
 });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Create Item";//Tell page header to underline Home tab
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
<h2>Create Item</h2> 
<hr>
<br><br>
<!-- ----------------------------------------------------------------------- Select Item Type -->
<h3 class="subheading">Select Item Type :</h3>
<br>
<center>
 <table>
  <tr>
   <td colspan="30" style="border:none;padding:0;">
    <?php 
    if($Mode=="Admin"){
    ?>
      <form method="post" action="">
       <input type="submit" class="button" value="Add / Edit Chassis">
       <input type="hidden" name="action" id="action" value="Chassis">
      </form>
      <br><br><br><br>
    <?php 
    }?>
      <form method="post" action="">
       <input type="submit" class="button" value=" Add / Edit Project ">
       <input type="hidden" name="action" id="action" value="Project">
	  </form>
      <br><br><br><br>
      <form method="post" action="">
       <input type="submit" class="button" value="  Add / Edit Team  ">
       <input type="hidden" name="action" id="action" value="Team">
      </form>
   </td>
  </tr>
 </table>
</center>
<?php 
//----------------------------------------------------------------------- Confirm New Project Requests
if($Mode=="Admin"){
?>
<br><br>
<hr>
<br><br>
<h3 class="subheading">Confirm New Project Requests :</h3><?php
date_default_timezone_set('America/Chicago');?><br>
 <form id="form4" name="form4" method="post" action="">
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="first">Request Form</th>
	  <th scope="col">Project Name</th>
      <th scope="col">Begin Date</th>
	  <th scope="col">End Date</th>
	  <th scope="col">Contact Mail</th>
	  <th scope="col">Select</th>
    </tr>
<?php
$alt = 1;
for($i=0;$i<count($P_Name);$i++){
 if($P_Permit[$i]=="no"||$P_Delete_Permit[$i]=="no"){ //check if project's request was being approved or not 
 ?>
    <tr>
	  <?php
//----------------------- check the type of request
	  if($P_RequestForm[$i]=="Add New Project"){ ?>
        <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><font color="royalblue"><?php echo $P_RequestForm[$i]; ?></font></td>
	  <?php
      }elseif($P_RequestForm[$i]=="Edit Old Project"){ ?>
	    <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><font color="darkolivegreen"><?php echo $P_RequestForm[$i]; ?></font></td>
	  	  <?php
      }elseif($P_RequestForm[$i]=="Delete Project"){ ?>
	    <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><font color="crimson"><?php echo $P_RequestForm[$i]; ?></font></td>
	  <?php
	  }
//----------------------- check if is edit request or not 	  
	  if($P_RequestForm[$i]=="Edit Old Project"&&$P_Name_Old[$i]!=$P_Name[$i]){ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo "<font color='darkolivegreen'>New:&nbsp".$P_Name[$i]."</font><br>(Original:&nbsp".$P_Name_Old[$i].")"; 
	  }else{ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $P_Name[$i]; 
	  } ?>  
	  </td>	  
	  <?php
//----------------------- check if is edit request or not 
	  if($P_RequestForm[$i]=="Edit Old Project"&&$P_BeginDate_Old[$i]!=$P_BeginDate[$i]){ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo "<font color='darkolivegreen'>New:&nbsp".$P_BeginDate[$i]."</font><br>(Original:&nbsp".$P_BeginDate_Old[$i].")"; 
	  }else{ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $P_BeginDate[$i];
	  } ?>  
	  </td>  
	  <?php
//----------------------- check if is edit request or not 
	  if($P_RequestForm[$i]=="Edit Old Project"&&$P_EndDate_Old[$i]!=$P_EndDate[$i]){ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo "<font color='darkolivegreen'>New:&nbsp".$P_EndDate[$i]."</font><br>(Original:&nbsp".$P_EndDate_Old[$i].")"; 
	  }else{ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $P_EndDate[$i];
	  } ?>  
	  </td>
	  <?php
//----------------------- check if is edit request or not 
	  if($P_RequestForm[$i]=="Edit Old Project"&&$P_Mail_Old[$i]!=$P_Mail[$i]){ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo "<font color='darkolivegreen'>New:&nbsp".$P_Mail[$i]."</font><br>(Original:&nbsp".$P_Mail_Old[$i].")"; 
	  }else{ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $P_Mail[$i];
	  } ?>  
	  </td>
	  <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
	  <input type="checkbox" id="project" name="project[]" value="<?php echo $P_ID[$i]; ?>"></td> 
    </tr>
<?php
}} ?>
	<tr>
      <td colspan="30" style="border:none;padding:0;"><br>
		<input type="submit" id="RejectProjectRequest" name="Reject" class="button" value="Reject">
		<input type="submit" id="AcceptProjectRequest" name="Accept" class="button" value="Accept">
		<input type="hidden" name="action" id="action" value="Confirm New Project Requests">
	  </td>
    </tr>
</form>
</table>
<br><br><br>
<hr>

<!-- --------------------------------------------------------------------- Confirm New Team Requests -->
<br><br>
<h3 class="subheading">Confirm New Team Requests :</h3><?php
date_default_timezone_set('America/Chicago');?><br>
<form id="form5" name="form5" method="post" action=""> 
<table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="first">Request Form</th>
      <th scope="col">Team</th>
	  <th scope="col">Contact Mail</th>
      <th scope="col">Select</th>
    </tr>
<?php
$alt = 1;
for($i=0;$i<count($T_Name);$i++){
 if($T_Permit[$i]=="no"||$T_Delete_Permit[$i]=="no"){ //check if team's request was being approved or not 
?>
    <tr>
	  <?php
//----------------------- check the type of request
	  if($T_RequestForm[$i]=="Add New Team"){ ?>
        <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><font color="royalblue"><?php echo $T_RequestForm[$i]; ?></font></td>
	  <?php
      }elseif($T_RequestForm[$i]=="Edit Old Team"){ ?>
	    <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><font color="darkolivegreen"><?php echo $T_RequestForm[$i]; ?></font></td>
  	  <?php
      }elseif($T_RequestForm[$i]=="Delete Team"){ ?>
	    <td class="first<?php if ($alt==1) {echo ' alt';} ?>"><font color="crimson"><?php echo $T_RequestForm[$i]; ?></font></td>
	  <?php
	  } 	
//----------------------- check if is edit request or not 	  
	  if($T_RequestForm[$i]=="Edit Old Team"&&$T_Name_Old[$i]!=$T_Name[$i]){ ?>
	    <td <?php if($alt==1) {echo ' class="alt"';}?>><?php echo "<font color='darkolivegreen'>New:&nbsp".$T_Name[$i]."</font><br>(Original:&nbsp".$T_Name_Old[$i].")"; 
	  }else{ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';}?>><?php echo $T_Name[$i]; 
	  } ?>  
	  </td>
	  <?php
//----------------------- check if is edit request or not 
	  if($T_RequestForm[$i]=="Edit Old Team"&&$T_Mail_Old[$i]!=$T_Mail[$i]){ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo "<font color='darkolivegreen'>New:&nbsp".$T_Mail[$i]."</font><br>(Original:&nbsp".$T_Mail_Old[$i].")"; 
	  }else{ ?>
	    <td <?php if ($alt==1) {echo ' class="alt"';} ?>><?php echo $T_Mail[$i];
	  } ?>  
	  </td>	  
      <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
	    <input type="checkbox" name="team[]" id="team" value="<?php echo $T_ID[$i]; ?>">
	  </td>
    </tr>
<?php
}} ?>
	<tr>
      <td colspan="30" style="border:none;padding:0;"><br>
		<input name="Reject" id="RejectTeamRequest" class="button" type="submit" value="Reject" />
		<input name="Accept" id="AcceptTeamRequest" class="button" type="submit" value="Accept" />
		<input name="action" type="hidden" id="action" value="Confirm New Team Requests">
	  </td>
    </tr>
</form>
</table>
<?php 
}?>
<!-- ---------------------------------------------------------------------  -->
</div>
</body>