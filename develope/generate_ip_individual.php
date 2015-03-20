<?php 
$dirLevel="../";
//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$sql_query = "SELECT * FROM create_temporary";
$test = mysql_query($sql_query);

while($row_test = mysql_fetch_assoc($test)){
$Ip_Content=$row_test['Ct_IP'];
}

//------------------------------------------------------------------------------------ 
header("Content-type: application/force-download");
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=chassis.txt");
$Txt= fopen('php://output','w');

$ip=$Ip_Content;

fwrite($Txt,$ip);
 
fclose($Txt);
?>

