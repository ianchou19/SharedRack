<?php
$dirLevel="../../";
//------------------------------------------------------------------------------------------------- GET Variable
if (isset($_GET['Mode'])) { $Mode = $_GET['Mode'];}
if (isset($_GET['ACK'])) { $ACK = $_GET['ACK'];} 

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack,$ShareRack);

$query_test = "SELECT * FROM chassis_setting WHERE `Ch_remove`='remove'";
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

while($row_test = mysql_fetch_assoc($test)){
 $Ch_ChassisName[]= $row_test['Ch_ChassisName'];
}

if(date("m")=="01"){$Mon="Jan";}elseif(date("m")=="02"){$Mon="Feb";}elseif(date("m")=="03"){$Mon="Mar";}elseif(date("m")=="04"){$Mon="Apr";}elseif(date("m")=="05"){$Mon="May";}elseif(date("m")=="06"){$Mon="Jun";}elseif(date("m")=="07"){$Mon="Jul";}elseif(date("m")=="08"){$Mon="Aug";}elseif(date("m")=="09"){$Mon="Sep";}elseif(date("m")=="10"){$Mon="Oct";}elseif(date("m")=="11"){$Mon="Nov";}elseif(date("m")=="12"){$Mon="Dec";}
$EndDate=$Mon."-".date("d")."-".date("Y");
$EndDate2=date("Y-m-d");

for($i=0;$i<count($Ch_ChassisName);$i++){
 if(strpos($ACK,$Ch_ChassisName[$i])!==false){
  $sql_query = "UPDATE `chassis_setting` SET `Ch_IP`='',`Using_Status`='Off',`Ch_remove`='',`Ch_EndDate`='$EndDate',`Ch_EndDate2`='$EndDate2' WHERE `Ch_ChassisName`='$Ch_ChassisName[$i]'";
  mysql_query($sql_query);
  $sql_query = "DELETE FROM `chassis_info` WHERE `Ch_ChassisName`='$Ch_ChassisName[$i]' AND `Ch_Status2`='remove'";
  mysql_query($sql_query); 
  $sql_query = "UPDATE `status_changing` SET `Stc_Acknowledge`='' WHERE `Stc_ChassisName`='$Ch_ChassisName[$i]' AND `Stc_Acknowledge`='no'";
  mysql_query($sql_query);
 }
}

//------------------------------------------------------------------------------------------------- 
$date=date("Y-m-d H:i:s");
$sql_query = "UPDATE `Time_record` SET `Tr_time`='$date' WHERE `Tr_type`='Status_update_time'";
mysql_query($sql_query);		
//------------------------------------------------------------------------------------------------- 

header('Location:.?Mode=Admin');
?>
