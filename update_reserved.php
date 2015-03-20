<?php
//------------------------------------------------------------------------------------------------- GET Variable
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];}

//------------------------------------------------------------------------------------------------- SQL Area 
require_once('../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack, $ShareRack);

$current_time=time()."000"; //find current time

$query_test = "SELECT * FROM schedule WHERE Sc_start2<=$current_time AND Sc_end2>=$current_time"; //find the chassis schedule which is current in progress  
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

$Sc_chassis=array();
while($row_test = mysql_fetch_assoc($test)){
$Sc_chassis[]=$row_test['Sc_chassis'];
}

if(count($Sc_chassis)!=0){ //if there exist any schedule is current in progress 
  for($i=0;$i<count($Sc_chassis);$i++){
    $query_test = "UPDATE chassis_info SET `Ch_Status2`='reserved' WHERE `Ch_ChassisName`='$Sc_chassis[$i]'"; //update the chassis current reserved status
    $test = mysql_query($query_test, $ShareRack) or die(mysql_error()); 
  }
}

header("Location:index.php?Mode=$Mode");
?>