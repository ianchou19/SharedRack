<?php
$dirLevel = "../../";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode2="";$Mode3="Normal";$PN="";
if (isset($_GET['Mode'])) { $Mode = $_GET['Mode'];} //user mode
if (isset($_GET['Mode2'])) { $Mode2 = $_GET['Mode2'];} //action mode
if (isset($_GET['Mode3'])) { $Mode3=$_GET['Mode3'];} //page type mode
//---------------------
if (isset($_GET['PN'])) { $PN = $_GET['PN'];}

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query="SELECT * FROM project WHERE P_Permit=''"; //select project which already being approved
$test=mysql_query($sql_query,$ShareRack)or die(mysql_error());

while($row_test=mysql_fetch_assoc($test)){
$P_ID[]=$row_test['P_ID']; //project id
$P_Name[]=$row_test['P_Name']; //project name
$P_BeginDate[]=$row_test['P_BeginDate']; //project begin date
$P_EndDate[]=$row_test['P_EndDate']; //project end date
$P_Mail[]=$row_test['P_Mail']; //project mail
}

//------------------------------------------------------------------------------------------------- Action -->	
//--------------------------------------------------------------------- Return
if(isset($_POST["action"])&&($_POST["action"]=="Return")){
   header("Location:../index.php?Mode=$Mode"); //return to upper layer
//--------------------------------------------------------------------- Show Edit Project
}elseif(isset($_POST["action"])&&($_POST["action"]=="Show Edit Project")){
   foreach($_POST['project']as $value){
     $Project_List[]=$value;
   } 
   $Project_List=implode("",$Project_List); //package all the project name as a list
   header("Location:.?Mode=$Mode&Mode3=$Mode3&PN=$Project_List"); //reload the page and display the project info which selected by user 
   
//--------------------------------------------------------------------- Add New Project  
}elseif(isset($_POST["action"])&&($_POST["action"]=="Add New Project")){ 
  $ProjectName=$_POST["ProjectName"]; //get project value input by user
  $BeginDate=$_POST["BeginDate"]; //get begin date
  $EndDate=$_POST["EndDate"]; //get end date
  $Mail=$_POST["Mail"]; //get mail
  
//--------------------- switch the date format for begin & end time
  $BeginDate=date('M-d-Y',strtotime($BeginDate));   
  $EndDate=date('M-d-Y',strtotime($EndDate)); 
  
//--------------------- check is the selected project exist in database or not
  $Check_Exist="no";
  for($i=0;$i<count($P_ID);$i++){
    if($P_Name[$i]==$ProjectName){ 
      $Check_Exist="yes"; //change variable value if find the project is already exist
    }
  } 
  
//--------------------- add new project into database and reload page with different status 
  if($Check_Exist=="yes"){ 
 	 header("Location:.?Mode=$Mode&Mode2=Exist&Mode3=$Mode3"); //reload the page with "project already exist" status 
  }elseif($Check_Exist=="no"){
     if($Mode=="Admin"){
       $sql_query="INSERT INTO `project`(`P_Name`,`P_BeginDate`,`P_EndDate`,`P_Mail`,`P_RequestForm`)VALUES('$ProjectName','$BeginDate','$EndDate','$Mail','Add New Project')"; //insert new project 
       mysql_query($sql_query);
	   header("Location:.?Mode=$Mode&Mode2=Request_Add&Mode3=$Mode3"); //reload the page with "request add successfully" status 
	 }elseif($Mode=="User"){
	   $sql_query="INSERT INTO `project`(`P_Name`,`P_BeginDate`,`P_EndDate`,`P_Mail`,`P_RequestForm`,`P_Permit`)VALUES('$ProjectName','$BeginDate','$EndDate','$Mail','Add New Project','no')"; //insert new project request
       mysql_query($sql_query);
	   header("Location:../mail_project.php?Mode=$Mode&Mode2=Request_Add&Mode3=$Mode3&P_Name=$ProjectName"); //go sending notification mail with "request add successfully" status   
	 }
  } 
//--------------------------------------------------------------------- Edit and Remove Project
}elseif(isset($_POST["action"])&&$_POST["action"]=="Edit And Remove Project"){
//---------------------
   $Button_Save=$_POST["Save"]; //set "save button" 
   $Button_Remove=$_POST["Remove"]; //set "remove button" 
   $Button_Cancel=$_POST["Cancel"]; //set "cancel button" 
   $ProjectName=$_POST["ProjectName"]; //get new project value input by user
   
//--------------------- action under save button    
   if($Button_Save=="Save"){
//---------------------
      $BeginDate=$_POST["BeginDate"]; //get new begin date
      $EndDate=$_POST["EndDate"]; //get new end date 
      $Mail=$_POST["Mail"]; //get new mail    

//--------------------- get old project value from database  
      $sql_query = "SELECT P_Name,P_BeginDate,P_EndDate,P_Mail FROM project WHERE P_Name='$PN'";
      $test = mysql_query($sql_query, $ShareRack) or die(mysql_error());

      $row_test = mysql_fetch_assoc($test);
      $ProjectName_Old=$row_test['P_Name']; //get old project value input by user
      $BeginDate_Old=$row_test['P_BeginDate']; //get old begin date
      $EndDate_Old=$row_test['P_EndDate']; //get old end date
	  $Mail_Old=$row_test['P_Mail']; //get old mail 
   
//--------------------- switch the date format for begin & end time
      $BeginDate=date('M-d-Y',strtotime($BeginDate));   
      $EndDate=date('M-d-Y',strtotime($EndDate)); 

//--------------------- check is the selected project exist in database or not	  
      $Check_Exist="no";
      for($i=0;$i<count($P_ID);$i++){
        if($P_Name[$i]==$ProjectName){
		  $Check_Exist="yes"; //change variable value if find the project is already exist  
        }
      }	  
//--------------------- edit project into database and reload page with different status 	  
      if(($Check_Exist=="yes"&&$ProjectName!=$ProjectName_Old)||($ProjectName==$ProjectName_Old&&$BeginDate==$BeginDate_Old&&$EndDate==$EndDate_Old&&$Mail==$Mail_Old)){ //if user input the new project name and can find this project from DB   
 	     header("Location:.?Mode=$Mode&Mode2=Exist&Mode3=$Mode3"); //reload the page with "project already exist" status   
      }else{
	     if($Mode=="Admin"){
           $sql_query = "UPDATE `project` SET `P_Name`='$ProjectName',`P_BeginDate`='$BeginDate',`P_EndDate`='$EndDate',`P_Mail`='$Mail',`P_RequestForm`='Edit Old Project',`P_Permit`='' WHERE `P_Name`='$PN'"; //edit this project 
		   mysql_query($sql_query);
           $sql_query = "UPDATE `schedule` SET `Sc_project`='$ProjectName' WHERE `Sc_project`='$PN'"; //change those schedules which belong to this project old name to the new project name
		   mysql_query($sql_query);
	       header("Location:.?Mode=$Mode&Mode2=Request_Edit&Mode3=$Mode3"); //reload the page with "request edit successfully" status 
		 }elseif($Mode=="User"){
           $sql_query = "INSERT INTO `project`(`P_Name`,`P_Name_Old`,`P_BeginDate`,`P_BeginDate_Old`,`P_EndDate`,`P_EndDate_Old`,`P_Mail`,`P_Mail_Old`,`P_RequestForm`,`P_Permit`)VALUES('$ProjectName','$ProjectName_Old','$BeginDate','$BeginDate_Old','$EndDate','$EndDate_Old','$Mail','$Mail_Old','Edit Old Project','no')"; //insert edit project request
		   mysql_query($sql_query);
		   header("Location:../mail_project.php?Mode=$Mode&Mode2=Request_Edit&Mode3=$Mode3&P_Name=$ProjectName"); //go sending notification mail with "request edit successfully" status   
		 }	
	  }
//--------------------- action under remove button   
   }elseif($Button_Remove=="Remove"){
      if($Mode=="Admin"){
        $sql_query = "DELETE FROM `project` WHERE `P_Name`='$PN'"; //delete this project 
        mysql_query($sql_query);
        header("Location:.?Mode=$Mode&Mode2=Request_Remove&Mode3=$Mode3"); //reload the page with "request remove successfully" status     	  
      }elseif($Mode=="User"){
        $sql_query = "UPDATE `project` SET `P_RequestForm`='Delete Project',`P_Delete_Permit`='no' WHERE `P_Name`='$PN'"; //change project to request delete mode
	    mysql_query($sql_query); 
		header("Location:../mail_project.php?Mode=$Mode&Mode2=Request_Remove&Mode3=$Mode3&P_Name=$PN"); //go sending notification mail with "request remove successfully" status  
	  }	
//--------------------- action under cancel button 
   }elseif($Button_Cancel=="Cancel"){
      header("Location:.?Mode=$Mode&Mode3=$Mode3");    	  
   }
}?>

<!-- ----------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Create Item | Moonshot Shared Rack</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<link rel="stylesheet" href="../../css/main.css"/>
<link rel="stylesheet" href="../../css/main.ie.css"/>
<link rel="stylesheet" href="../../css/manage_schedule_index.css"/>

<script src="../../../js/general.js"></script>
<script src="../../../js/ajax.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
//------------------------------------------ Calendar
$(function(){
    $( "#BeginDate" ).datepicker();
    $( "#EndDate" ).datepicker();
	$( "#BeginDate2" ).datepicker();
    $( "#EndDate2" ).datepicker();
});

function setDate(elem,dt){
    $( elem ).datepicker("setDate", new Date(parseInt(dt)));
}
//------------------------------------------ Add New Project
$(function(){
 $("#AddNewProject").click(function(){
   var ProjectName = $("#ProjectName").val();
   var BeginDate = $("#BeginDate").val();
   var EndDate = $("#EndDate").val();
   var Mail = $("#Mail").val();
   if(ProjectName==''||BeginDate==''||EndDate==''||Mail==''){
     alert('Please do not leave blank !');
     return false;
   } 
   else if(BeginDate.length != 10 || EndDate.length != 10) {
     alert('The date format is incorrect !');
     return false;     
   }     
   else if( ((BeginDate.substring(6,10))+(BeginDate.substring(0,2))+(BeginDate.substring(3,5))) > ((EndDate.substring(6,10))+(EndDate.substring(0,2))+(EndDate.substring(3,5))) ) {   
     alert('End date shouldn not earlier than begin date !');
     return false;     
   }
   else if(!confirm('Are you sure to add this project ?')){ 
     return false;
   }    
 });
});
//------------------------------------------ Show Edit Project
$(function(){
 $("#ShowEditProject").click(function(){
   var project = $("#project").val();
   if( project=='Select Project'){
     alert('You have not selected any project !');
     return false;
   } 
 });
});
//------------------------------------------ Edit And Remove Project Save
$(function(){
 $("#EditAndRemoveProject_Save").click(function(){
   var ProjectName = $("#ProjectName").val();
   var BeginDate = $("#BeginDate").val();
   var EndDate = $("#EndDate").val();
   var Mail = $("#Mail").val();
   if( ProjectName==''||BeginDate==''||EndDate==''||Mail==''){
     alert('Please do not leave blank !');
     return false;
   } 
   else if(BeginDate.length != 10 || EndDate.length != 10) {
     alert('The date format is incorrect !');
     return false;     
   }     
   else if( ((BeginDate.substring(6,10))+(BeginDate.substring(0,2))+(BeginDate.substring(3,5))) > ((EndDate.substring(6,10))+(EndDate.substring(0,2))+(EndDate.substring(3,5))) ) {   
     alert('End date shouldn not earlier than begin date !');
     return false;     
   }
   else if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Edit And Remove Project Remove
$(function(){
 $("#EditAndRemoveProject_Remove").click(function(){
  if(!confirm('Are you sure to remove this project ?')){ 
     return false;
   } 
 });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Create Project";//Tell page header to underline Home tab
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
<!-- ------------------------ Come from "schedule page" -->
<?php
if($Mode3=="AddPJ"){?>
  <h2>Create Project</h2>
  <hr>
<!-- ------------------------ Come from "create item page" or "check project duration" -->
<?php 
}elseif($Mode3=="EditPJ"||$Mode3=="Normal"){
  if($Mode3=="EditPJ"){?>
     <h2>Check Project Duration</h2>
	 <hr>
	 <a href="javascript:void(0);" class="button" style="margin-left: 5px;" onClick="window.location='../../schedule/index.php?Mode=User'">Return</a>	 
  <?php
  }elseif($Mode3=="Normal"){?>
     <h2>Add / Edit Project</h2>
     <hr>
     <form method="post" action="">
       <input type="submit" class="button" value="Return">
       <input type="hidden" name="action" id="action" value="Return">
     </form>
  <?php 
  }?>  
  <br><br><br><br>
<!-- ----------------------------------------------------------------------- Show Edit Project or Check Project Duration -->
<form method="post" action="">
  <?php
  if($Mode3=="EditPJ"){?>
    <h3>Check Project Duration:</h3>
  <?php
  }elseif($Mode3=="Normal"){?>
    <h3>Edit Current Projects:</h3>  
  <?php
  }?>
  <br>
  <table>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <select name="project[]" id="project">
          <option selected>Select Project</option>
          <?php 
          for($i=0;$i<count($P_ID);$i++){?>
            <option> <?php echo $P_Name[$i]."<br>";
          }?>
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <input type="submit" id="ShowEditProject" class="button" value="Select">
		<input type="hidden" name="action" id="action" value="Show Edit Project">
      </td>
    </tr>
  </table>
</form>
<br><br><br> 
<!-- ----------------------------------------------------------------------- Edit And Remove Project --> 
<form method="post" action=""> 
  <table class="infotable" cellspacing="0">
    <?php 
    for($i=0;$i<count($P_Name);$i++){
      if(strcmp($PN,$P_Name[$i])==0){  
//--------------------- switch the date format for begin & end time
        $P_BeginDate=date('m/d/Y',strtotime($P_BeginDate[$i]));   
        $P_EndDate=date('m/d/Y',strtotime($P_EndDate[$i])); 		

//--------------------- 
        $alt = 1;
        ?>
        <tr>
          <td scope="col" class="first">
	        <center><h3>Project Name</h3></center>
	      </td>
          <td scope="col">
			<center><h3>Begin Date</h3></center>
	      </td>
          <td scope="col">
			<center><h3>End Date</h3></center>
		  </td>
          <td scope="col">
			<center><h3>Contact Mail</h3></center>
		  </td>
		  
          <?php
          if($Mode3==""){?>		  
          <td scope="col">
			<center><h3>Save / Remove</h3></center>
		  </td>
          <?php
		  }?>
		  
		</tr>
        <tr>
          <td class="first<?php if ($alt==1) {echo ' alt';} ?>">
			<input type="text" name="ProjectName" id="ProjectName" size="25" maxlength="100" value="<?php echo $P_Name[$i];?>">
	      </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <br>
			<input type="text" name="BeginDate" id="BeginDate" size="10" maxlength="30" value="<?php echo $P_BeginDate;?>">
			<br>( format:&nbsp MM/DD/YYYY )<br><br>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <br><br>
		    <input type="text" name="EndDate" id="EndDate" size="10" maxlength="30" value="<?php echo $P_EndDate;?>">
			<br>( format:&nbsp MM/DD/YYYY )<br><br>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="Mail" id="Mail" size="20" maxlength="50" value="<?php echo $P_Mail[$i];?>">
		  </td>		  

          <?php
          if($Mode3=="Normal"){?>			  
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
            <input type="submit" name="Remove" id="EditAndRemoveProject_Remove" class="button" value="Remove">&nbsp 
            <input type="submit" name="Save" id="EditAndRemoveProject_Save" class="button" value="Save">
          </td>
          <?php
		  }?>		  
		  
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
			<input type="hidden" name="action" id="action" value="Edit And Remove Project">
            <input type="submit" name="Cancel" id="EditAndRemoveProject_Cancel" class="button" value="Cancel">			 
	      </td>
        </tr>
        <?php
      }
    }?>
  </table>
</form>
<br><br><br>
<?php 
}?>
<!-- ----------------------------------------------------------------------- Add New Project -->
<?php
if($Mode3=="AddPJ"||$Mode3=="Normal"){ 
  if($Mode3=="AddPJ"){ ?>
    <a href="javascript:void(0);" class="button" style="margin-left: 5px;" onClick="window.location='../../sche_user/schedule/index.php?Mode=User'">Return</a> 
    <br><br><br><br>
    <?php 
  }?>
  <form method="post" action="" onSubmit="return checkForm();">
    <h3>Add New Project:</h3><br>
    <table>
      <tr>
        <td colspan="30" style="border:none;padding:0;"><br>
          Project Name <input type="text" name="ProjectName" id="ProjectName" size="15" maxlength="100">&nbsp&nbsp&nbsp&nbsp  
          Begin Date <input type="text" name="BeginDate" id="BeginDate" size="10" maxlength="30">&nbsp&nbsp&nbsp&nbsp
		  End Date <input type="text" name="EndDate" id="EndDate" size="10" maxlength="30">&nbsp&nbsp&nbsp&nbsp
          Contact Mail <input type="text" name="Mail" id="Mail" size="20" maxlength="50">
		  <p align="center">( format:&nbsp MM/DD/YYYY )&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp( format:&nbsp MM/DD/YYYY )</p>
        </td>
      </tr>
      <tr>
        <td colspan="30" style="border:none;padding:0;"><br>
          <input type="submit" id="AddNewProject" class="button" value="Add">
          <input type="hidden" name="action" id="action" value="Add New Project">
        </td>
      </tr>
    </table>	  
  </form>
<!-- ----------------------------------------------------------------------- different status alert -->  
<?php
if($Mode2=="Exist"){ //display "project already exist" alert
    echo "<script type='text/javascript'>alert('This project name is already in use !');</script>";
}elseif($Mode2=="Request_Add"){
  if($Mode=="Admin"){ //display "add project successfully" alert
	echo "<script type='text/javascript'>alert('Add new project successfully !');</script>";
  }elseif($Mode=="User"){ //display "request add project successfully" alert
	echo "<script type='text/javascript'>alert('Submit new project request successfully !');</script>";
  } 
}elseif($Mode2=="Request_Edit"){
  if($Mode=="Admin"){ //display "edit project successfully" alert
    echo "<script type='text/javascript'>alert('Edit project successfully !');</script>";
  }elseif($Mode=="User"){ //display "request edit project successfully" alert
    echo "<script type='text/javascript'>alert('Submit edit project request successfully !');</script>";
  }
}elseif($Mode2=="Request_Remove"){
  if($Mode=="Admin"){ //display "remove project successfully" alert
    echo "<script type='text/javascript'>alert('Remove project successfully !');</script>";
  }elseif($Mode=="User"){ //display "request remove project successfully" alert
    echo "<script type='text/javascript'>alert('Submit remove project request successfully !');</script>";
  }
}?>
<!-- -------------------------------------------------------------------------------------------------  -->
<?php
}?>
</center>
</div>
</body>
</html>