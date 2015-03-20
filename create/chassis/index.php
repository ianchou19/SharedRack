<?php
$dirLevel = "../../";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode2="";$Mode3="Normal";$CH="";
if (isset($_GET['Mode'])) { $Mode = $_GET['Mode'];} //user mode
if (isset($_GET['Mode2'])) { $Mode2 = $_GET['Mode2'];} //action mode
if (isset($_GET['Mode3'])) { $Mode3=$_GET['Mode3'];} //page type mode
//---------------------
if (isset($_GET['CH'])) { $CH = $_GET['CH'];}

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query = "SELECT * FROM chassis_setting WHERE Using_Status='On'"; //select chassis which using status is online
$test = mysql_query($sql_query, $ShareRack) or die(mysql_error());

$Ch_ChassisName=array(); 
while($row_test = mysql_fetch_assoc($test)){
$Ch_S_ID[]=$row_test['Ch_S_ID']; //chassis id
$Ch_IP[]=$row_test['Ch_IP']; //chassis IP
$Ch_ChassisName[]=$row_test['Ch_ChassisName']; //chassis name
$Ch_UserName[]=$row_test['Ch_UserName']; //chassis user name
$Ch_Password[]=$row_test['Ch_Password']; //chassis password
$Ch_DeployDate[]=$row_test['Ch_DeployDate']; //chassis deploy date
$Ch_EndDate[]=$row_test['Ch_EndDate']; //chassis end date
$Using_Status[]=$row_test['Using_Status']; //chassis using status
}

//------------------------------------------------------------------------------------------------- Action -->	
//--------------------------------------------------------------------- Return
if(isset($_POST["action"])&&($_POST["action"]=="Return")){
   header("Location:../index.php?Mode=$Mode"); //return to upper layer
//--------------------------------------------------------------------- Show Edit Chassis
}elseif(isset($_POST["action"])&&($_POST["action"]=="Show Edit Chassis")){
   foreach($_POST['chassis']as $value){
     $Chassis_List[]=$value;
   } 
   $Chassis_List=implode("",$Chassis_List); //package all the chassis name as a list
   header("Location:.?Mode=$Mode&Mode3=$Mode3&CH=$Chassis_List"); //reload the page and display the chassis info which selected by user
//--------------------------------------------------------------------- Add New Chassis
}elseif(isset($_POST["action"])&&($_POST["action"]=="Add New Chassis")){
   $iLOIP=$_POST["iLOIP"]; //get chassis IP value input by user
   //$UserName=$_POST["UserName"]; //get user name
   $UserName="Administrator"; //fixed the value because the unexpected result 
   //$Password=$_POST["Password"]; //get password
   $Password="password";
   $BeginDate=$_POST["BeginDate"]; //get begin date 

//--------------------- switch the date format for begin & end time
   $BeginDate=date('M-d-Y',strtotime($BeginDate));   

//--------------------- check is the selected chassis exist in database or not  
   $Check_Exist="no";
   for($i=0;$i<count($Ch_S_ID);$i++){
     if($Ch_IP[$i]==$iLOIP){
       $Check_Exist="yes"; //change variable value if find the chassis is already exist	
     }
   }
   
//--------------------- add new chassis into database and reload page with different status 
   if($Check_Exist=="yes"){
	  header("Location:.?Mode=$Mode&Mode2=Exist&Mode3=$Mode3"); //reload the page with "chassis already exist" status 
   }elseif($Check_Exist=="no"){//if couldn't find the IP in database
	  $sql_query="INSERT INTO `chassis_setting`(`Ch_IP`,`Ch_UserName`,`Ch_Password`,`Ch_DeployDate`)VALUES('$iLOIP','$UserName','$Password','$BeginDate')"; //insert new chassis 
      mysql_query($sql_query);
	  header("Location:refresh.php?Mode=$Mode&Mode2=Request_Add&Mode3=$Mode3&IP=$iLOIP"); //go refreshing with "request add successfully" status 
   }
   
//--------------------------------------------------------------------- Edit and Remove Chassis
}elseif(isset($_POST["action"])&&($_POST["action"]=="Edit and Remove Chassis")){
   $Button_Save=$_POST["Save"]; //set "save button" 
   $Button_Remove=$_POST["Remove"]; //set "remove button" 
   $Button_Cancel=$_POST["Cancel"]; //set "cancel button" 
   $iLOIP=$_POST["iLOIP"]; //get new chassis value input by user
   
//--------------------- action under save button     
   if($Button_Save=="Save"){
//---------------------
	  //$UserName=$_POST["UserName"]; //get new user name
      $UserName="Administrator";
	  //$Password=$_POST["Password"]; //get new password
      $Password="password";      
	  $BeginDate=$_POST["BeginDate"]; //get new begin date    

//--------------------- get old chassis IP value from database  
      $sql_query = "SELECT Ch_IP FROM chassis_setting WHERE Ch_ChassisName='$CH'";
      $test = mysql_query($sql_query, $ShareRack) or die(mysql_error());

	  $row_test = mysql_fetch_assoc($test);
      $iLOIP_Old=$row_test['Ch_IP']; //get old IP value input by user
	  
//--------------------- switch the date format for begin & end time
      $BeginDate=date('M-d-Y',strtotime($BeginDate));  

//--------------------- check is the selected chassis exist in database or not	
      $Check_Exist="no";
      for($i=0;$i<count($Ch_S_ID);$i++){
        if($Ch_IP[$i]==$iLOIP){
          $Check_Exist="yes"; //change variable value if find the chassis is already exist  	
        }
      }	
//--------------------- edit chassis into database and reload page with different status 
	  if($Check_Exist=="yes"&&$iLOIP!=$iLOIP_Old){ //if user input the new iLO IP and can find this iLO IP from DB 
	     header("Location:.?Mode=$Mode&Mode2=Exist&Mode3=$Mode3");
      }else{ 
	     if($iLOIP==$iLOIP_Old){ //if IP doesn't change when editing chassis
           $sql_query="UPDATE `chassis_setting` SET `Ch_UserName`='$UserName',`Ch_Password`='$Password',`Ch_DeployDate`='$BeginDate' WHERE `Ch_ChassisName`='$CH' AND `Using_Status`='On'";
           mysql_query($sql_query);	
		   header("Location:.?Mode=$Mode&Mode2=Request_Edit&Mode3=$Mode3");
		 }else{	//if IP does change when editing chassis	 
 		   $sql_query="INSERT INTO `chassis_setting`(`Ch_IP`,`Ch_UserName`,`Ch_Password`,`Ch_DeployDate`,`Ch_Edit`)VALUES('$iLOIP','$UserName','$Password','$BeginDate','new')"; //insert new chassis data first
           mysql_query($sql_query);		
           $sql_query="UPDATE `chassis_setting` SET `Ch_Edit`='old' WHERE `Ch_ChassisName`='$CH' AND `Using_Status`='On'"; //update old chassis data to "old" mode 
           mysql_query($sql_query);	 
		   
           header("Location:refresh.php?$Mode=$Mode&Mode2=Request_Edit&Mode3=$Mode3&IP=$iLOIP&CH_Old=$CH"); //go refreshing with "request edit successfully" status 
		 }
	  }
//--------------------- action under remove button 
    }elseif($Button_Remove=="Remove"){ 
//--------------------- switch the date format for two types of end time  		
       if(date("m")=="01"){$END_m="Jan";}elseif(date("m")=="02"){$END_m="Feb";}elseif(date("m")=="03"){$END_m="Mar";}elseif(date("m")=="04"){$END_m="Apr";}elseif(date("m")=="05"){$END_m="May";}elseif(date("m")=="06"){$END_m="Jun";}elseif(date("m")=="07"){$END_m="Jul";}elseif(date("m")=="08"){$END_m="Aug";}elseif(date("m")=="09"){$END_m="Sep";}elseif(date("m")=="10"){$END_m="Oct";}elseif(date("m")=="11"){$END_m="Nov";}elseif(date("m")=="12"){$END_m="Dec";}
       $EndDate=$END_m."-".date("d")."-".date("Y");
       $EndDate2=date("Y-m-d");
//--------------------- 	   
       $sql_query = "UPDATE `chassis_setting` SET `Ch_IP`='',`Ch_EndDate`='$EndDate',`Ch_EndDate2`='$EndDate2',`Using_Status`='Off',`Ch_remove`='' WHERE `Ch_ChassisName`='$CH'"; //set chassis to offline in chassis_setting 
       mysql_query($sql_query);   
	   $sql_query = "DELETE FROM `chassis_info` WHERE `Ch_ChassisName`='$CH'"; //clear related info in chassis_info
       mysql_query($sql_query);
	   $sql_query = "DELETE FROM `switch_info` WHERE `Ch_ChassisName`='$CH'"; //clear related info in switch_info
       mysql_query($sql_query);
	   $sql_query = "DELETE FROM `cartridge_info` WHERE `Ch_ChassisName`='$CH'"; //clear related info in cartridge_info
       mysql_query($sql_query);
	   header("Location:.?Mode=$Mode&Mode2=Request_Remove&Mode3=$Mode3");
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
//------------------------------------------ Calendar
$(function(){
    $( "#BeginDate" ).datepicker();
	$( "#BeginDate2" ).datepicker();
});
/*
function setDate(elem,dt){
    $( elem ).datepicker("setDate", new Date(parseInt(dt)));
}
*/
//------------------------------------------ Add New Chassis
$(function(){
 $("#AddNewChassis").click(function(){
   var iLOIP = $("#iLOIP").val();
   var UserName = $("#UserName").val();
   var Password = $("#Password").val();
   var BeginDate = $("#BeginDate").val();
   if( iLOIP==''||UserName==''||Password==''||BeginDate==''){
     alert('Please do not leave blank !');
     return false;
   }
   else if(BeginDate.length != 10) {
     alert('The date format is incorrect !');
     return false;     
   }    
   else if(!confirm('Are you sure to add this chassis ?')){ 
     return false;
   }      
 });
});
//------------------------------------------ Show Edit Chassis
$(function(){
 $("#ShowEditChassis").click(function(){
   var chassis = $("#chassis").val();
   if(chassis=='Select Chassis'){
     alert('You have not selected any chassis !');
     return false;
   } 
 });
});
//------------------------------------------ Edit And Remove Chassis Save
$(function(){
 $("#EditAndRemoveChassis_Save").click(function(){
   var iLOIP = $("#iLOIP").val();
   var UserName = $("#UserName").val();
   var Password = $("#Password").val();
   var BeginDate = $("#BeginDate").val();
   if(iLOIP==''||UserName==''||Password==''||BeginDate==''){
     alert('Please do not leave blank !');
     return false;
   } 
   else if(BeginDate.length != 10) {
     alert('The date format is incorrect !');
     return false;     
   }      
   else if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Edit And Remove Chassis Remove
$(function(){
 $("#EditAndRemoveChassis_Remove").click(function(){
  if(!confirm('Are you sure to remove this chassis ?')){ 
     return false;
   } 
 });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Create Chassis";//Tell page header to underline Home tab
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
<h2>Add / Edit Chassis</h2> 
<hr>
<!-- ----------------------------------------------------------------------- Come from management page -->
<?php
if($Mode3=="Management"){?>
  <a href="javascript:void(0);" class="button" style="margin-left: 5px;" onClick="window.location='../../management.php?Mode=Admin'">Return To Manage Home</a>
  <?php
}else{?>
<!-- ------------------------ Come from create item page -->
  <form method="post" action="">
    <input type="submit" class="button" value="Return">
	<input type="hidden" name="action" id="action" value="Return">
  </form>
<?php
}?>
<br><br><br>
<!-- ------------------------ Show Edit Chassis -->
<form method="post" action="">
  <h3>Edit Current Chassis:</h3><br>
  <table>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <select name="chassis[]" id="chassis">
          <option selected>Select Chassis</option>
		  <?php 
            for($i=0;$i<count($Ch_ChassisName);$i++){ 
              if($Ch_ChassisName[$i]!=""&&$Using_Status[$i]=="On"){?>
                <option> <?php echo $Ch_ChassisName[$i]."<br>";
	          }
		    }?>
        </select>&nbsp&nbsp
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;">
        <br>
        <input type="submit" id="ShowEditChassis" class="button" value="Select" align="right">
		<input type="hidden" name="action" value="Show Edit Chassis">
      </td>
    </tr>
  <table>
</form>
<!-- ----------------------------------------------------------------------- Edit and Remove Chassis --> 
<br><br><br>
<form method="post" action=""> 
  <table class="infotable" cellspacing="0">
  <?php 
    for($i=0;$i<count($Ch_ChassisName);$i++){
      if($Ch_ChassisName[$i]!=""&&strcmp($CH,$Ch_ChassisName[$i])==0&&$Using_Status[$i]=="On"){	
		
//--------------------- switch the date format for begin & end time
        $Ch_DeployDate=date('m/d/Y',strtotime($Ch_DeployDate[$i]));   
		
//--------------------- 		
        $alt = 1;
        ?>
        <tr>
          <td  scope="col" class="first">
		    <center><h3>Chassis Name</h3></center>
		  </td>
          <td  scope="col">
		    <center><h3>iLO IP</h3></center>
		  </td>
          <td  scope="col">
		    <center><h3>User Name</h3></center>
		  </td>
          <td  scope="col">
		    <center><h3>Password</h3></center>
		  </td>
          <td  scope="col">
		    <center><h3>Deployed Date</h3></center>
		  </td>
          <td  scope="col">
		    <center><h3>Save / Remove</h3></center>
		  </td>
        </tr>
        <tr>
          <td class="first<?php if ($alt==1) {echo ' alt';}?>">
		    <?php echo $Ch_ChassisName[$i];?>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="iLOIP" id="iLOIP" size="15" maxlength="25" value="<?php echo $Ch_IP[$i];?>">
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="UserName" id="UserName" size="10" maxlength="25" value="<?php echo $Ch_UserName[$i];?>" disabled="disabled">
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="Password" id="Password" size="10" maxlength="25" value="<?php echo $Ch_Password[$i];?>" disabled="disabled">
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <br><br>
		    <input type="text" name="BeginDate" id="BeginDate" size="12" maxlength="25" value="<?php echo $Ch_DeployDate;?>">
			<br>( format:&nbsp MM/DD/YYYY )<br><br>
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
            <input type="submit" name="Remove" id="EditAndRemoveChassis_Remove" class="button" value="Remove">&nbsp 
            <input type="submit" name="Save" id="EditAndRemoveChassis_Save" class="button" value="Save">
			<input type="hidden" name="action" value="Edit and Remove Chassis">
          </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
            <input type="submit" name="Cancel" id="EditAndRemoveChassis_Cancel" class="button" value="Cancel">			 
	      </td>
        </tr>
        <?php
      }
    }?> 
  </table>
</form>
<br><br><br>
<!-- ----------------------------------------------------------------------- Add New Chassis -->
<form method="post" action="">
  <h3>Add New Chassis:</h3><br>
  <table>
    <tr>
      <td colspan="30" style="border:none;padding:0;">
	    <br>
        iLO IP <input type="text" name="iLOIP" id="iLOIP" size="15" maxlength="20">&nbsp&nbsp&nbsp&nbsp
        User Name <input type="text" name="UserName" id="UserName" value="Administrator" size="15" maxlength="20" disabled="disabled">&nbsp&nbsp&nbsp&nbsp
        Password <input type="text" name="Password" id="Password" value="password" size="15" maxlength="20" disabled="disabled">&nbsp&nbsp&nbsp&nbsp
        Deployed Date <input type="text" name="BeginDate" id="BeginDate" size="10" maxlength="20">&nbsp&nbsp&nbsp
		<p align="right">( format:&nbsp MM/DD/YYYY )</p>
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
		<input type="submit" id="AddNewChassis" class="button" value="Add">
        <input type="hidden" name="action" id="action" value="Add New Chassis">
      </td>
    </tr>
  </table>	  
</form>
<!-- ----------------------------------------------------------------------- different status alert of Add Chassis -->  
<?php
if($Mode2=="Exist"){ //display "chassis already exist" alert
    echo "<script type='text/javascript'>alert('This chassis name is already in use !');</script>";
}elseif($Mode2=="Request_Add"){ //display "add chassis successfully" alert
	echo "<script type='text/javascript'>alert('Add new chassis successfully !');</script>";
}elseif($Mode2=="Request_Edit"){ //display "edit chassis successfully" alert
    echo "<script type='text/javascript'>alert('Edit chassis successfully !');</script>"; 
}elseif($Mode2=="Request_Remove"){ //display "remove chassis successfully" alert
    echo "<script type='text/javascript'>alert('Remove chassis successfully !');</script>";
}elseif($Mode2=="Fail"){ //display "request edit chassis successfully" alert
	echo "<script type='text/javascript'>alert('This IP does not exist or other information is incorrect !');</script>";	
}?>
<!-- -------------------------------------------------------------------------------------------------  -->
</center>
</div>
</body>
</html>