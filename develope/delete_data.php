<?php
$dirLevel="../";
//------------------------------------------------------------------------------------------------- GET Variable
if(isset($_GET['Mode3'])){$Mode3=$_GET['Mode3'];}

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

if($Mode3=="equip"){
 $sql_query = "DELETE FROM `chassis_info`";
 mysql_query($sql_query);
 $sql_query = "DELETE FROM `switch_info`";
 mysql_query($sql_query);
 $sql_query = "DELETE FROM `cartridge_info`";
 mysql_query($sql_query);
 $sql_query = "DELETE FROM `node_info`";
 mysql_query($sql_query);
}

if($Mode3=="chassis_setting"){
 $sql_query = "DELETE FROM `chassis_setting`";
 mysql_query($sql_query);
}

if($Mode3=="project"){
 $sql_query = "DELETE FROM `project`";
 mysql_query($sql_query);
}

if($Mode3=="team"){
 $sql_query = "DELETE FROM `team`";
 mysql_query($sql_query);
}

if($Mode3=="schedule"){
 $sql_query = "DELETE FROM `schedule`";
 mysql_query($sql_query);
}

if($Mode3=="changing"){
 $sql_query = "DELETE FROM `status_changing`";
 mysql_query($sql_query);
}

if($Mode3=="failure"){
 $sql_query = "DELETE FROM `status_failure`";
 mysql_query($sql_query);
}

if($Mode3=="clear_chassisname"){
 $sql_query = "UPDATE `chassis_setting` SET `Ch_ChassisName`=''";
 mysql_query($sql_query);	
}

if($Mode3=="clear_remove"){
 $sql_query = "UPDATE `chassis_setting` SET `Ch_remove`='',`Ch_Edit`=''";
 mysql_query($sql_query);	
}

if($Mode3=="firmware"){
 $sql_query = "UPDATE `firmware` SET `Fw_Latest_File`=''";
 mysql_query($sql_query);	
}

header("Location:.");	
?>