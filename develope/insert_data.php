<?php
$dirLevel="../";
//------------------------------------------------------------------------------------------------- GET Variable
if(isset($_GET['Mode3'])){$Mode3=$_GET['Mode3'];}

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

//------------------------------------------------------------------------------------------------- Insert Chassis_Setting table
if(substr($Mode3,0,6)=="Taipei"||substr($Mode3,0,7)=="Houston"){

  $sql_query = "DELETE FROM `chassis_setting`";
  $result = mysql_query($sql_query);

  if(substr($Mode3,0,6)=="Taipei"){
     $Ch_IP=array("192.168.1.254","192.168.2.254","192.168.3.254","192.168.4.254","192.168.5.254","192.168.6.254","192.168.10.254","192.168.11.254","192.168.12.254","192.168.201.254","192.168.202.254","192.168.203.254");   
//------------------
  }elseif(substr($Mode3,0,7)=="Houston"){
     $Ch_IP=array("16.91.21.45","16.91.21.135","16.91.22.0","16.91.22.44","16.91.22.177","16.91.22.187","16.91.22.224","16.91.23.47","16.91.23.50","16.91.23.61");  
  }

  for($i=0;$i<count($Ch_IP);$i++){
     $sql_query="INSERT INTO `chassis_setting`(`Ch_IP`)VALUES('$Ch_IP[$i]')";
     mysql_query($sql_query);
  } 
}

//------------------further basic setting
if($Mode3=="Taipei_basic_setting"||$Mode3=="Houston_basic_setting"){
  if($Mode3=="Taipei_basic_setting"){
    $sql_query = "UPDATE `Time_record` SET `Tr_time`='Taipei Shared Rack'";
    mysql_query($sql_query);  
//------------------
  }elseif($Mode3=="Houston_basic_setting"){
    $sql_query = "UPDATE `Time_record` SET `Tr_time`='Houston Shared Rack'";
    mysql_query($sql_query);
  }
  
  $sql_query = "DELETE FROM `chassis_info`";
  mysql_query($sql_query);
  $sql_query = "DELETE FROM `switch_info`";
  mysql_query($sql_query);
  $sql_query = "DELETE FROM `cartridge_info`";
  mysql_query($sql_query);
  $sql_query = "DELETE FROM `node_info`";
  mysql_query($sql_query);   
  $sql_query = "DELETE FROM `status_changing`";
  mysql_query($sql_query); 
  $sql_query = "DELETE FROM `status_failure`";
  mysql_query($sql_query);
  $sql_query = "UPDATE `chassis_setting` SET `Ch_ChassisName`='',`Ch_remove`='',`Ch_Edit`=''";
  mysql_query($sql_query);	 
} 
 
//------------------------------------------------------------------------------------------------- SQL Area
if($Mode3=="team"){
$query_test = "SELECT Sc_team FROM schedule GROUP BY Sc_team";
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

while($row_test = mysql_fetch_assoc($test)){//chassis_info info
$Sc_team[]=$row_test['Sc_team'];
}

for($i=0;$i<count($Sc_team);$i++){
  $query_insert="INSERT INTO `team`(`T_Name`,`T_RequestForm`)VALUES('$Sc_team[$i]','Add New Team')";
  mysql_query($query_insert);
} 
}

//------------------------------------------------------------------------------------------------- SQL Area
if($Mode3=="schedule"){
$xml = new DOMDocument();
$xml->load('schedule.xml'); //Read rack.xml

$schedule=$xml->getElementsByTagName('schedule');
foreach($schedule as $Schedule){

$item=$Schedule->getElementsByTagName('item'); //insert schedule item info
foreach($item as $Item){
$item1 = $Item->getAttribute('id');
$item2 = $Item->getAttribute('type');
$item3 = $Item->getAttribute('chassis');
$item4 = $Item->getAttribute('project');
$item5 = $Item->getAttribute('group');
$item6 = $Item->getAttribute('');
$item7 = $Item->getAttribute('email');
$item8 = $Item->getAttribute('start');
$item9 = $Item->getAttribute('end');

$start=date("M-d-Y",$item8/1000);
$end=date("M-d-Y",$item9/1000);

$sql_query2 = "INSERT INTO schedule (Sc_id,Sc_type,Sc_chassis,Sc_project,Sc_team,Sc_group,Sc_email,Sc_start,Sc_start2,Sc_end,Sc_end2)VALUES('$item1','$item2','$item3','$item4','$item5','$item6','$item7','$start','$item8','$end','$item9')";
$result2 = mysql_query($sql_query2);
}}
}

//------------------------------------------------------------------------------------------------- SQL Area
if($Mode3=="project_into_schedule"){
$query_test1 = "SELECT P_Name FROM project ORDER BY P_ID";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());
$query_test2 = "SELECT Sc_id FROM schedule";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());?>
<?php
do{
$P_Name[]=$row_test1['P_Name'];
}while($row_test1 = mysql_fetch_assoc($test1))?>
<?php
do{
$Sc_id[]=$row_test2['Sc_id'];
}while($row_test2 = mysql_fetch_assoc($test2))?>
<?php
$i2=1;
for($i=0;$i<count($Sc_id);$i++){
  $sql_query = "UPDATE `schedule` SET `Sc_project`='$P_Name[$i2]' WHERE `Sc_id`='$Sc_id[$i]'";
  mysql_query($sql_query);
  
  $i2++;
  if($i2==11){
    $i2=1;
  }
} 
}

header("Location:.");
?>