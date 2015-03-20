<?php 
$dirLevel="../";
//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query = "SELECT * FROM chassis_setting WHERE Using_Status='On'";
$test = mysql_query($sql_query);

while($row_test = mysql_fetch_assoc($test)){
$Ch_IP[]=$row_test['Ch_IP'];
}

//------------------------------------------------------------------------------------ 
header("Content-type: application/force-download");
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=chassis.txt");
$Txt= fopen('php://output','w');

$num=count($Ch_IP);

for($i=0;$i<($num-1);$i++){
  $ip=$Ch_IP[$i].":Administrator:password\r\n";
  fwrite($Txt,$ip);
}

$ip=$Ch_IP[$num-1].":Administrator:password";
fwrite($Txt,$ip);
 
fclose($Txt);
?>

