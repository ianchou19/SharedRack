<?php
$dirLevel="./";
//------------------------------------------------------------------------------------------------- GET Variable
$Mode2="";$Select_User="";
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //action mode  
if (isset($_GET['Select_User'])) { $Select_User = $_GET['Select_User'];} //admin name which selected by user

//------------------------------------------------------------------------------------------------- Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query1 = "SELECT * FROM chassis_setting";
$test1 = mysql_query($sql_query1, $ShareRack) or die(mysql_error()); //get all the chassis in default environment configuration 
$sql_query2 = "SELECT * FROM user WHERE U_Level='common'";
$test2 = mysql_query($sql_query2, $ShareRack) or die(mysql_error()); //get admin info ( without super admin )
 
while($row_test1 = mysql_fetch_assoc($test1)){
$Ch_ChassisName[]=$row_test1['Ch_ChassisName'];
$Ch_IP[]=$row_test1['Ch_IP'];
$Ch_UserName[]=$row_test1['Ch_UserName'];
$Ch_Password[]=$row_test1['Ch_Password'];
$Using_Status[]=$row_test1['Using_Status'];
}?>
<?php 
while($row_test2 = mysql_fetch_assoc($test2)){
$U_ID[]=$row_test2['U_ID'];
$U_Name[]=$row_test2['U_Name'];
$U_Password[]=$row_test2['U_Password'];
$U_Mail[]=$row_test2['U_Mail'];
} 

//------------------------------------------------------------------------------------------------- Action -->
//--------------------------------------------------------------------- Start To Refresh
if(isset($_POST["action"])&&($_POST["action"]=="Start To Refresh")){
   $output=shell_exec("/var/www/html/perl/refresh.sh");
//--------------------------------------------------------------------- Add / Edit Chassis
}elseif(isset($_POST["action"])&&($_POST["action"]=="Add / Edit Chassis")){
   header("Location:./create/chassis/index.php?Mode=$Mode&Mode3=Management"); //go add & edit chassis with "management mode" 
//--------------------------------------------------------------------- Show Edit Team
}elseif(isset($_POST["action"])&&($_POST["action"]=="Show User Account")){
//--------------------- combine the string of selected admin
   foreach($_POST['admin']as $value){
     $User_List[]=$value;
   } 
   $User_List=implode("",$User_List);
   header("Location:management.php?Mode=$Mode&Select_User=$User_List");
//--------------------------------------------------------------------- Add New Account
}elseif(isset($_POST["action"])&&($_POST["action"]=="Add New Account")){ 
   $UserName=$_POST["UserName"];	
   $Password=$_POST["Password"];
   $Email=$_POST["Email"]; 
//--------------------- check is the selected admin exist in database or not   
   $Check_Exist="no";
   for($i=0;$i<count($U_ID);$i++){
     if($U_Name[$i]==$UserName){
       $Check_Exist="yes";	  
     }
   }
//--------------------- add new admin into database and reload page with different status 
   if($Check_Exist=="yes"){
 	  header("Location:.?Mode=$Mode&Mode2=Exist"); //reload the page with "admin name already exist" status
   }elseif($Check_Exist=="no"){   
      $sql_query ="INSERT INTO `user`(`U_Name`,`U_Password`,`U_Mail`,`U_Level`)VALUES('$UserName','$Password','$Email','common')";
      mysql_query($sql_query);
	  header("Location:management.php?Mode=$Mode&Mode2=Request_Add"); //make next page have "Add Successfully"		
   }
//--------------------------------------------------------------------- Edit and Remove Team
}elseif(isset($_POST["action"])&&($_POST["action"]=="Edit and Remove")){
   $Button_Save=$_POST["Save"]; //set "save button"
   $Button_Remove=$_POST["Remove"]; //set "remove button"
   $Button_Cancel=$_POST["Cancel"]; //set "cancel button" 
//---------------------   
   $UserName=$_POST["UserName"]; //get user name	
   $Password=$_POST["Password"]; //get password
   $Email=$_POST["Email"]; //get mail
   
//--------------------- action under save button      
   if($Button_Save=="Save"){	 
//--------------------- check is the selected admin exist in database or not   
     $Check_Exist="no";
     for($i=0;$i<count($U_ID);$i++){
       if($U_Name[$i]==$UserName){
         $Check_Exist="yes";	  
       }
     }	   
//--------------------- add new admin into database and reload page with different status 	 
	 if($Check_Exist=="yes"){
        header("Location:management.php?Mode=$Mode&Mode2=Exist");//check if team name already exist	 
     }elseif($Check_Exist=="no"){  	   
 	    $sql_query = "UPDATE `user` SET `U_Name`='$UserName',`U_Password`='$Password',`U_Mail`='$Email',`U_Permit`='' WHERE `U_Name`='$Select_User'";
        mysql_query($sql_query);
		header("Location:management.php?Mode=$Mode&Mode2=Request_Edit");
     }  
//--------------------- action under remove button 
   }elseif($Button_Remove=="Remove"){ 
      $sql_query = "DELETE FROM `user` WHERE `U_Name`='$Select_User'";
      mysql_query($sql_query);
      header("Location:management.php?Mode=$Mode&Mode2=Request_Remove");  
//--------------------- action under cancel button 
   }elseif($Button_Cancel=="Cancel"){
      header("Location:management.php?Mode=$Mode");    	  
   } 
}?>

<!-- ------------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Management | Moonshot Shared Rack</title>
<link rel="stylesheet" href="css/main.css" />
<link rel="stylesheet" href="css/main.ie.css" />
<link rel="stylesheet" href="css/manage_index.css" />

<script src="js/general.js"></script>
<script src="js/ajax.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
//------------------------------------------ Refresh Whole The Chassis
$(function(){
 $("#RefreshChassis").click(function(){
   if(!confirm('Are you sure to refresh ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Add New Account
$(function(){
 $("#AddNewAccount").click(function(){
   var UserName = $("#UserName").val();
   var Password = $("#Password").val();
   var Email = $("#Email").val();
   if(UserName==''||Password==''||Email==''){
     alert('Please do not leave blank!');
     return false;
   } 
   if(!confirm('Are you sure to add this account ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Show Edit Account
$(function(){
 $("#ShowEditAccount").click(function(){
   var admin = $("#admin").val();
   if( admin=='Select Account'){
     alert('You have not selected any account !');
     return false;
   } 
 });
});
//------------------------------------------ Edit And Remove Account Save
$(function(){
 $("#EditAndRemoveAccount_Save").click(function(){
   var UserName = $("#UserName").val();
   var Password = $("#Password").val();
   var Email = $("#Email").val();
   if(UserName==''||Password==''||Email==''){
     alert('Please do not leave blank !');
     return false;
   } 
   if(!confirm('Are you sure to save this change ?')){ 
     return false;
   }       
 });
});
//------------------------------------------ Edit And Remove Account Remove
$(function(){
 $("#EditAndRemoveAccount_Remove").click(function(){
  if(!confirm('Are you sure to remove this account ?')){ 
     return false;
   } 
 });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Management";//Tell page header to underline Home tab
  $pages=array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode",  
	"Management"=>$dirLevel."management.php?Mode=$Mode");     
  $headerAppend=" - Management";  
$_GET['Mode3']="main_page";	
include $dirLevel."include/header.php";
?>
<!-- ----------------------------------------------------------------------- Head Title -->
<div class="content">
<h2>Management Home</h2>
<hr>
<center>
<!-- ----------------------------------------------------------------------- Refresh Whole The Chassis -->
<h3>Refresh Whole The Chassis</h3>
<form method="post" action="">
 <table class="infotable" cellspacing="0">
  <tr>
	<td colspan="5" style="border:none;padding:0;">
      <input type="submit" id="RefreshChassis" class="button" value="Start To Refresh" />
      <input type="hidden" name="action" id="action" value="Start To Refresh">
	</td>
  </tr>		 
 </table>
</form> 
<br>
<hr>  
<!-- ----------------------------------------------------------------------- Information Of Whole The Chassis --> 
<h3>Information Of Whole The Chassis</h3>
<form method="post" action="">
  <div style="margin:0 auto 30px;width:370px;">
    <center>
	  <select name="chassis[]" id="chassis" multiple="multiple" size="8">
        <?php 
        for($i=0;$i<count($Ch_ChassisName);$i++){ ?>
            <option> <?php echo $Ch_IP[$i].":".$Ch_UserName[$i].":".$Ch_Password[$i]." (".$Ch_ChassisName[$i].")";
		} ?>
      </select>&nbsp;&nbsp;
	  <br><br>
      <input type="submit" class="button" value="Add / Edit Chassis">
	  <input type="hidden" name="action" id="action" value="Add / Edit Chassis">
  </div>
</form>
<br>
<hr>
<!-- ----------------------------------------------------------------------- Admin Account Management -->
<form method="post" action="">
  <h3>Admin Account Management:</h3>
  <br>
  <table>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <select name="admin[]" id="admin">
          <option selected>Select Account</option>
          <?php 
          for($i=0;$i<count($U_ID);$i++){?>
            <option> <?php echo $U_Name[$i]."<br>";
          }?>
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <input type="submit" id="ShowEditAccount" class="button" value="Select">
		<input type="hidden" name="action" id="action" value="Show User Account">
      </td>
    </tr>
  </table>
</form>
<!-- ----------------------------------------------------------------------- Edit and Remove Admin Account --> 
<form method="post" action=""> 
  <table class="infotable" cellspacing="0">
    <?php
    for($i=0;$i<count($U_Name);$i++){
      if(strcmp($Select_User,$U_Name[$i])==0){
        $alt = 1;
        ?>
        <tr>
          <td scope="col" class="first">
		    <center><h3>Admin Name</h3></center>
		  </td>
          <td scope="col">
			<center><h3>PASSWORD</h3></center>
		  </td>
          <td scope="col">
			<center><h3>EMAIL</h3></center>
		  </td>
	      <td scope="col">
			<center><h3>Save / Delete</h3></center>
		  </td>
        </tr>  
        <tr>
          <td  class="first<?php if ($alt==1) {echo ' alt';} ?>">
			<input type="text" name="UserName" id="UserName" size="15" maxlength="100" value="<?php echo $U_Name[$i];?>">
		  </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="Password" id="Password" size="20" maxlength="50" value="<?php echo $U_Password[$i];?>">
		  </td>	
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
		    <input type="text" name="Email" id="Email" size="20" maxlength="50" value="<?php echo $U_Mail[$i];?>">
		  </td>
	      <td <?php if ($alt==1) {echo ' class="alt"';} ?>>	          
	        <input type="submit" name="Remove" id="EditAndRemoveAccount_Remove" class="button" value="Remove">&nbsp 
	        <input type="submit" name="Save" id="EditAndRemoveAccount_Save" class="button" value="Save">
            <input type="hidden" name="action" id="action" value="Edit and Remove">			  
	      </td>
          <td <?php if ($alt==1) {echo ' class="alt"';} ?>>
            <input type="submit" name="Cancel" id="UserAccount_Cancel" class="button" value="Cancel">			 
	      </td>			
        </tr>
	    <?php 
      }	  
    }?>
  </table>
</form>
<br><br><br>
<!-- ----------------------------------------------------------------------- Add New Admin Account --> 
<h3>Add New Admin Account:</h3>
<br>
<form method="post" action="">
  <table class="infotable" cellspacing="0">
    <tr>
      <td colspan="30" style="border:none;padding:0;">
         User Name <input type="text" name="UserName" id="UserName" size="15" maxlength="50">&nbsp&nbsp&nbsp&nbsp  
         Password <input type="text" name="Password" id="Password" size="15" maxlength="50">&nbsp&nbsp&nbsp&nbsp
         Email <input type="text" name="Email" id="Email" size="20" maxlength="70">&nbsp&nbsp&nbsp&nbsp
      </td>
    </tr>
    <tr>
      <td colspan="30" style="border:none;padding:0;"><br>
        <input type="submit" id="AddNewAccount" class="button" value="Add Account">
        <input type="hidden" name="action" id="action" value="Add New Account">     
      </td>
    </tr>
  </table>
</form>
<!-- ----------------------------------------------------------------------- different status alert of Add Team -->  
<?php
if($Mode2=="Exist"){ //display "user already exist" alert
  echo "<script type='text/javascript'>alert('This admin name is already in use !');</script>";
}elseif($Mode2=="Request_Add"){ //display "add team successfully" alert
  echo "<script type='text/javascript'>alert('Add new admin account successfully !');</script>";  
}elseif($Mode2=="Request_Edit"){ //display "edit team successfully" alert
  echo "<script type='text/javascript'>alert('Edit admin account successfully !');</script>";
}elseif($Mode2=="Request_Remove"){ //display "remove team successfully" alert
  echo "<script type='text/javascript'>alert('Remove admin account successfully !');</script>";
}?>
<!-- -------------------------------------------------------------------------------------------------  -->
</center>  
</div>
</body>
</html>