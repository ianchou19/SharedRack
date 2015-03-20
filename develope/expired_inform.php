<?php
$dirLevel="../";
//------------------------------------------------------------------------------------------------- Check Session
include($dirLevel.'include/session_check.php');
//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack,$ShareRack);

$Current=strtotime(date("Y-m-d H:i:s"))."000";

$query_test5="SELECT * FROM schedule WHERE `Sc_end2`>'$Current'";
$test5=mysql_query($query_test5,$ShareRack)or die(mysql_error());

while($row_test5=mysql_fetch_assoc($test5)){
 $Sc_id[]=$row_test5['Sc_id'];
 $Sc_email[]=$row_test5['Sc_email'];
 $Sc_end2[]=$row_test5['Sc_end2'];
}

for($i=0;$i<count($Sc_id);$i++){
$Alert="no";
  if(($Sc_end2[$i]-$Current)<=86400000&&$Alert=="no"){//one day is 86400000
    echo $Sc_id[$i]." 1 day Send Alert to ".$Sc_email[$i]." ".($Sc_end2[$i]-$Current-86400000)."<br>";
	$reservation[]=$Sc_id[$i];
	$mail[]=$Sc_email[$i];
	$day[]=1;
	$Alert="yes";	
  } 
  if(($Sc_end2[$i]-$Current)<=259200000&&($Sc_end2[$i]-$Current)>=172800000&&$Alert=="no"){//one day is 172800000
    echo $Sc_id[$i]." 3 day Send Alert to ".$Sc_email[$i]." ".($Sc_end2[$i]-$Current-259200000)."<br>";
	$reservation[]=$Sc_id[$i];
	$mail[]=$Sc_email[$i];
	$day[]=3;
	$Alert="yes";
  } 
}

$Reservation=implode(",",(array)$reservation);
$Mail=implode(",",(array)$mail);
$Day=implode(",",(array)$day);

header("Location:mail_status.php?Mode2=Expired&Reservation=$Reservation&Mail=$Mail&Day=$Day");
?>
