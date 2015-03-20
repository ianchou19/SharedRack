<?php
$dirLevel="../../";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode2="";$Mode3="Normal";$TN="";
if (isset($_GET['Mode'])) { $Mode = $_GET['Mode'];} //user mode
if (isset($_GET['Mode2'])) { $Mode2 = $_GET['Mode2'];} //action mode
if (isset($_GET['Mode3'])) { $Mode3=$_GET['Mode3'];} //page type mode
//---------------------
if (isset($_GET['TN'])) { $TN = $_GET['TN'];}

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query = "SELECT * FROM team WHERE T_Permit=''"; //select team which already being approved
$test = mysql_query($sql_query, $ShareRack) or die(mysql_error());

while($row_test = mysql_fetch_assoc($test)){
$T_ID[]=$row_test['T_ID']; //team id
$T_Name[]=$row_test['T_Name']; //team name
$T_Mail[]=$row_test['T_Mail']; //team mail
}

//------------------------------------------------------------------------------------------------- Action 
//--------------------------------------------------------------------- Return
if(isset($_POST["action"])&&($_POST["action"]=="Return")){
   header("Location:../index.php?Mode=$Mode"); //return to upper layer
//--------------------------------------------------------------------- Show Edit Team
}elseif(isset($_POST["action"])&&($_POST["action"]=="Show Edit Team")){
   foreach($_POST['team']as $value){
     $Team_List[]=$value;
   } 
   $Team_List=implode("",$Team_List); //package all the team name as a list
   header("Location:.?Mode=$Mode&Mode3=$Mode3&TN=$Team_List"); //reload the page and display the team info which selected by user
//--------------------------------------------------------------------- Add New Team
}elseif(isset($_POST["action"])&&($_POST["action"]=="Add New Team")){ 
  $TeamName=$_POST["TeamName"]; //get team value input by user
  $Mail=$_POST["Mail"]; //get team
  
//--------------------- check is the selected project exist in database or not
  $Check_Exist="no";
  for($i=0;$i<count($T_ID);$i++){
    if($T_Name[$i]==$TeamName){
      $Check_Exist="yes"; //change variable value if find the project is already exist	  
    }
  }
  
//--------------------- add new project into database and reload page with different status 
  if($Check_Exist=="yes"){
 	 header("Location:.?Mode=$Mode&Mode2=Exist&Mode3=$Mode3"); //reload the page with "team already exist" status
  }elseif($Check_Exist=="no"){   
    if($Mode=="Admin"){
      $sql_query ="INSERT INTO `team`(`T_Name`,`T_Mail`,`T_RequestForm`)VALUES('$TeamName','$Mail','Add New Team')"; //insert new team
      mysql_query($sql_query);
	  header("Location:index.php?Mode=$Mode&Mode2=Request_Add&Mode3=$Mode3"); //reload the page with "request add successfully" status 	
    }elseif($Mode=="User"){
	  $sql_query="INSERT INTO `team`(`T_Name`,`T_Mail`,`T_RequestForm`,`T_Permit`)VALUES('$TeamName','$Mail','Add New Team','no')"; //insert new team request
      mysql_query($sql_query);
	  header("Location:../mail_team.php?Mode=$Mode&Mode2=Request_Add&Mode3=$Mode3&T_Name=$TeamName"); //go sending notification mail with "request add successfully" status     
    } 
  }
//--------------------------------------------------------------------- Edit and Remove Team
}elseif(isset($_POST["action"])&&($_POST["action"]=="Edit and Remove Team")){
   $Button_Save=$_POST["Save"]; //set "save button"
   $Button_Remove=$_POST["Remove"]; //set "remove button"
   $Button_Cancel=$_POST["Cancel"]; //set "cancel button"
   $TeamName=$_POST["TeamName"]; //get new project value input by user   
   
//--------------------- action under save button    
   if($Button_Save=="Save"){	
//---------------------
      $Mail=$_POST["Mail"]; //get new mail  
   
//--------------------- get old project value from database     
      $sql_query = "SELECT T_Name,T_Mail FROM team WHERE T_Name='$TN'";
      $test = mysql_query($sql_query, $ShareRack) or die(mysql_error());

      $row_test = mysql_fetch_assoc($test);
      $TeamName_Old=$row_test['T_Name']; //get old team value input by user
	  $Mail_Old=$row_test['T_Mail']; //get old mail  
    
//--------------------- check is the selected team exist in database or not	 
      $Check_Exist="no";
      for($i=0;$i<count($T_ID);$i++){
        if($T_Name[$i]==$TeamName){
           $Check_Exist="yes"; //change variable value if find the team is already exist  	  
        }
      }	  
//--------------------- edit team into database and reload page with different status 	 
	  if(($Check_Exist=="yes"&&$TeamName!=$TeamName_Old)||($TeamName==$TeamName_Old&&$Mail==$Mail_Old)){ //if user input the new team name and can find this team from DB   
        header("Location:.?Mode=$Mode&Mode2=Exist&Mode3=$Mode3"); //reload the page with "team already exist" status  
      }else{  	   
  	    if($Mode=="Admin"){
 	      $sql_query = "UPDATE `team` SET `T_Name`='$TeamName',`T_RequestForm`='Edit Old Team',`T_Permit`='' WHERE `T_Name`='$TN'"; //edit this team 
          mysql_query($sql_query);
		  $sql_query = "UPDATE `schedule` SET `Sc_team`='$TeamName' WHERE `Sc_team`='$TN'"; //change those schedules which belong to this team old name to the new team name
		  mysql_query($sql_query);
		  header("Location:.?Mode=$Mode&Mode2=Request_Edit&Mode3=$Mode3"); //reload the page with "request edit successfully" status 
        }elseif($Mode=="User"){	 
          $sql_query = "INSERT INTO `team`(`T_Name`,`T_Name_Old`,`T_Mail`,`T_Mail_Old`,`T_RequestForm`,`T_Permit`)VALUES('$TeamName','$TeamName_Old','$Mail','$Mail_Old','Edit Old Team','no')"; //insert edit team request
	      mysql_query($sql_query);
	      header("Location:../mail_team.php?Mode=$Mode&Mode2=Request_Edit&Mode3=$Mode3&T_Name=$TeamName"); //go sending notification mail with "request edit successfully" status   		
        }
      }  
//--------------------- action under remove button   
   }elseif($Button_Remove=="Remove"){ 
      if($Mode=="Admin"){
        $sql_query = "DELETE FROM `team` WHERE `T_Name`='$TN'"; //delete this team
        mysql_query($sql_query);
        header("Location:.?Mode=$Mode&Mode2=Request_Remove&Mode3=$Mode3"); //reload the page with "request remove successfully" status 
      }elseif($Mode=="User"){
        $sql_query = "UPDATE `team` SET `T_RequestForm`='Delete Team',`T_Delete_Permit`='no' WHERE `T_Name`='$TN'"; //change team to request delete mode
	    mysql_query($sql_query);
	    header("Location:../mail_team.php?Mode=$Mode&Mode2=Request_Remove&Mode3=$Mode3&T_Name=$TeamName"); //go sending notification mail with "request remove successfully" status  		 	   
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
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../../css/main.css" />
<link rel="stylesheet" href="../../css/main.ie.css" />
<link rel="stylesheet" href="../../css/manage_schedule_index.css" />

<script src="../../js/general.js"></script>
<script src="../../js/ajax.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
//------------------------------------------ Add New Team
$(function(){
 $("#AddNewTeam").click(function(){
   var TeamName = $("#TeamName").val();
   var Mail = $("#Mail").val();
   if( TeamName==''||Mail==''){
     alert('Please do not leave blank !');
     return false;
   } 
   if(!confirm('Are you sure to add this team ?')){ 
     return false;
   }  
 });
});
//------------------------------------------ Show Edit Team
$(function(){
 $("#ShowEditTeam").click(function(){
   var team = $("#team").val();
   if( team=='Select Team'){
     alert('You have not selected any team !');
     return false;
   } 
 });
});
//------------------------------------------ Edit And Remove Team Save
$(function(){
 $("#EditAndRemoveTeam_Save").click(function(){
   var TeamName = $("#TeamName").val();
   var Mail = $("#Mail").val();
   if( TeamName==''||Mail==''){
     alert('Please do not leave blank !');
     return false;
   } 
   if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Edit And Remove Team Remove
$(function(){
 $("#EditAndRemoveTeam_Remove").click(function(){
  if(!confirm('Are you sure to remove this team ?')){ 
     return false;
   } 
 });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Create Team";//Tell page header to underline Home tab
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
if($Mode3=="AddTM"){?>
  <h2>Create Team</h2>
  <hr>
<!-- ------------------------ Come from "create item page" -->
<?php 
}elseif($Mode3=="Normal"){?>
  <h2>Add / Edit Team</h2> 
  <hr>
  <form method="post" action="">
    <input type="submit" class="button" value="Return">
    <input type="hidden" name="action" id="action" value="Return">
  </form>
<br><br><br><br>
<!-- ----------------------------------------------------------------------- Show Edit Team -->
<form method="post" action="">
  <h3>Edit Current Teams:</h3>
  <br>
  <table>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <select name="team[]" id="team">
          <option selected>Select Team</option>
          <?php 
          for($i=0;$i<count($T_ID);$i++){?>
            <option> <?php echo $T_Name[$i]."<br>";
          }?>
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <input type="submit" id="ShowEditTeam" class="button" value="Select">
		<input type="hidden" name="action" id="action" value="Show Edit Team">
      </td>
    </tr>
  </table>
</form>
<br><br><br> 
<!-- ----------------------------------------------------------------------- Edit and Remove Team --> 
<form method="post" action=""> 
  <table class="infotable" cellspacing="0">
    <?php
    for($i=0;$i<count($T_Name);$i++){
      if(strcmp($TN,$T_Name[$i])==0){
        $alt = 1;
        ?>
        <tr>
          <td scope="col" class="first">
		    <center><h3>Team Name</h3></center>
		  </td>
          <td scope="col">
			<center><h3>Contact Mail</h3></center>
		  </td>
	      <td scope="col">
			<center><h3>Save / Delete</h3></center>
		  </td>
        </tr>  
        <tr>
          <td  class="first<?php if ($alt==1) {echo ' alt';} ?>">
		    <br><br>
			<input type="text" name="TeamName" id="TeamName" size="15" maxlength="100" value="<?php echo $T_Name[$i];?>">
			<br><br><br>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="Mail" id="Mail" size="20" maxlength="50" value="<?php echo $T_Mail[$i];?>">
		  </td>	
	      <td <?php if ($alt==1) {echo ' class="alt"';} ?>>	          
	        <input type="submit" name="Remove" id="EditAndRemoveTeam_Remove" class="button" value="Remove">&nbsp 
	        <input type="submit" name="Save" id="EditAndRemoveTeam_Save" class="button" value="Save">
            <input type="hidden" name="action" id="action" value="Edit and Remove Team">			  
	      </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
            <input type="submit" name="Cancel" id="EditAndRemoveTeam_Cancel" class="button" value="Cancel">			 
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
<!-- ----------------------------------------------------------------------- Add New Team -->
<?php
if($Mode3=="AddTM"||$Mode3=="Normal"){ 
  if($Mode3=="AddTM"){ ?>
    <a href="javascript:void(0);" class="button" style="margin-left: 5px;" onClick="window.location='../../sche_user/schedule/index.php?Mode=User'">Return</a> 
    <br><br><br><br>
    <?php 
  }?>
  <form method="post" action="" onSubmit="return checkForm();">
    <h3>Add New Team:</h3><br>
    <table>
      <tr>
        <td colspan="30" style="border:none;padding:0;">
		  <br>
          Team Name <input type="text" name="TeamName" id="TeamName" size="15" maxlength="100">&nbsp&nbsp&nbsp&nbsp
          Contact Mail <input type="text" name="Mail" id="Mail" size="20" maxlength="50">
        </td>
      </tr>
      <tr>
        <td colspan="30" style="border:none;padding:0;"><br>         
          <input type="submit" id="AddNewTeam" class="button" value="Add">
		  <input type="hidden" name="action" id="action" value="Add New Team">
        </td>
      </tr>
    </table>
  </form>
<!-- ----------------------------------------------------------------------- different status alert of Add Team -->  
<?php
if($Mode2=="Exist"){ //display "team already exist" alert
  echo "<script type='text/javascript'>alert('This team name is already in use !');</script>";
}elseif($Mode2=="Request_Add"){
  if($Mode=="Admin"){ //display "add team successfully" alert
    echo "<script type='text/javascript'>alert('Add new team successfully !');</script>";
  }elseif($Mode=="User"){ //display "request add team successfully" alert
	echo "<script type='text/javascript'>alert('Submit new team request successfully !');</script>";
  } 
}elseif($Mode2=="Request_Edit"){
  if($Mode=="Admin"){ //display "edit team successfully" alert
    echo "<script type='text/javascript'>alert('Edit team successfully !');</script>";
  }elseif($Mode=="User"){ //display "request edit team successfully" alert
    echo "<script type='text/javascript'>alert('Submit edit team request successfully !');</script>";
  }
}elseif($Mode2=="Request_Remove"){
  if($Mode=="Admin"){ //display "remove team successfully" alert
    echo "<script type='text/javascript'>alert('Remove team successfully !');</script>";
  }elseif($Mode=="User"){ //display "request remove team successfully" alert
    echo "<script type='text/javascript'>alert('Submit remove team request successfully !');</script>";
  }
}?>
<!-- -------------------------------------------------------------------------------------------------  -->
<?php
}?>
</center>
</div>
</body>
</html>