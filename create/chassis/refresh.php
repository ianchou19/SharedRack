<?php
$dirLevel = "../../";
//------------------------------------------------------------------------------------------------- GET Variable 
if (isset($_GET['Mode'])) { $Mode = $_GET['Mode'];} //user mode
if (isset($_GET['Mode2'])) { $Mode2 = $_GET['Mode2'];} //action mode
if (isset($_GET['Mode3'])) { $Mode3=$_GET['Mode3'];} //page type mode
//---------------------
if (isset($_GET['IP'])) {$IP = $_GET['IP'];} //chassis IP given when add IP
if (isset($_GET['CH_Old'])) {$CH_Old = $_GET['CH_Old'];} //chassis old IP given when edit IP

//------------------------------------------------------------------------------------------------- SQL area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query = "SELECT Ch_UserName,Ch_Password FROM chassis_setting WHERE Ch_IP='$IP'"; //select project chassis which belong to this variable IP
$test = mysql_query($sql_query);

$row_test = mysql_fetch_assoc($test);
$UserName=$row_test['Ch_UserName']; //chassis User name
$Password=$row_test['Ch_Password']; //chassis password

//------------------------------------------------------------------------------------------------- 
$IP_content=$IP.":".$UserName.":".$Password; //combine the IP & user name & password as a string

$sql_query="INSERT INTO `create_temporary`(`Ct_IP`)VALUES('$IP_content')"; //insert this string into database temporary table
mysql_query($sql_query);

//------------------------------------------------------------------------------------------------- 
$output=shell_exec("/var/www/html/perl/refresh_each.sh"); //run the refresh script of the bash file 

//------------------------------------------------------------------------------------------------- 
$sql_query = "DELETE FROM `create_temporary`"; //clean the IP_content string of database temporary table
mysql_query($sql_query);

?>
<?php
$sql_query = "SELECT Ch_ChassisName FROM chassis_info WHERE Ch_IP='$IP'"; //get chassis name after using IP to download iLO info
$test = mysql_query($sql_query);

while($row_test = mysql_fetch_assoc($test)){
$Ch_ChassisName=$row_test['Ch_ChassisName'];
}

//------------------------------------------------------------------------------------------------- 
if(count($Ch_ChassisName)!=0){ //read this chassis info successfully 
  $sql_query = "DELETE FROM `chassis_setting` WHERE Ch_Edit='old'"; //delete the old chassis record from chassis_setting table
  mysql_query($sql_query);
//--------------------- delete all info related to this chassis name  
  $sql_query = "DELETE FROM `chassis_info` WHERE `Ch_ChassisName`='$CH_Old'"; //delete the old chassis record from chassis_info table by old IP
  mysql_query($sql_query); 
  $sql_query = "DELETE FROM `switch_info` WHERE `Ch_ChassisName`='$CH_Old'"; //delete the old chassis record from chassis_info table by old IP
  mysql_query($sql_query); 
  $sql_query = "DELETE FROM `cartridge_info` WHERE `Ch_ChassisName`='$CH_Old'"; //delete the old chassis record from chassis_info table by old IP
  mysql_query($sql_query);   

//--------------------- re-load the new individual chassis's XML into database  
$output2=shell_exec("/var/www/html/ShareRack/develope/insert_equip_each.php"); //run the "insert individual equip into database" script 

//---------------------       
  if($Mode2=="Request_Add"){
    header("Location:.?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3"); //go back with "add chassis successfully" status 
  }elseif($Mode2=="Request_Edit"){
    header("Location:.?Mode=Admin&Mode2=$Mode2&Mode3=$Mode3"); //go back with "edit chassis successfully" status 
  }
//--------------------- 
}elseif(count($Ch_ChassisName)==0){ //fail to read this chassis info
  $sql_query = "DELETE FROM `chassis_setting` WHERE Ch_IP='$IP'"; //delete the new chassis record from chassis_setting table
  mysql_query($sql_query);
 
  if($Mode2=="Request_Add"||"Request_Edit"){
    header("Location:.?Mode=$Mode&Mode2=Fail&Mode3=$Mode3"); //go back with "fail to add chassis" status  
  }
}?>
