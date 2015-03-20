<?php
$dirLevel="../";
//--------------------------------------------------------------------------------------------- GET Variable
$Mode3="";$Acknowledge=array();
if (isset($_GET['Mode3'])) { $Mode3 = $_GET['Mode3'];}

//--------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

//--------------------------------------------------------------------------------------------- renew the latest update iLO time 
$date=date("Y-m-d H:i:s");
$sql_query = "UPDATE `Time_record` SET `Tr_time`='$date' WHERE `Tr_type`='Status_update_time'";
$result = mysql_query($sql_query);

//--------------------------------------------------------------------------------------------- change Ch_Status2 to remove first
$sql_query = "UPDATE `chassis_info` SET `Ch_Status2`='remove'";
$result = mysql_query($sql_query);

$sql_query = "UPDATE `switch_info` SET `Sw_Status2`='remove'";
$result = mysql_query($sql_query);

$sql_query = "UPDATE `cartridge_info` SET `Ca_Status2`='remove'";
$result = mysql_query($sql_query);

$sql_query = "UPDATE `node_info` SET `Nd_Status2`='remove'";
$result = mysql_query($sql_query);	

//---------------------------------------------------------------------------------------------- change `Sf_latest`='1' to `Sf_latest`='2' with `Sf_fix`='no'
$query_test = "UPDATE `status_failure` SET `Sf_latest`='2',`Sf_fix`='no' WHERE `Sf_latest`='1'";
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

$xml = new DOMDocument();//Read rack.xml
//---------------------------------------------------------------------------------------------- different Mode3 of insert fake data 
if($Mode3==""){
  $xml->load($dirLevel.'../perl/rack.xml');
}elseif($Mode3=="normal"){
  $xml->load($dirLevel.'../perl/test/normal/rack.xml');
}elseif($Mode3=="change"){
  $xml->load($dirLevel.'../perl/test/change/rack.xml');
}elseif($Mode3=="failure"){
  $xml->load($dirLevel.'../perl/test/failure/rack.xml');
}elseif($Mode3=="failure_one"){
  $xml->load($dirLevel.'../perl/test/failure_one/rack.xml');
}elseif($Mode3=="change_failure"){
  $xml->load($dirLevel.'../perl/test/change_failure/rack.xml');
}
//------------------------------------------------------------------------------------------ scan rack 
$Rack=$xml->getElementsByTagName('Rack');
foreach($Rack as $rack){
//------------------------------------------------------------------------------------------ scan chassis 
 $Chassis=$rack->getElementsByTagName('Chassis');
 foreach($Chassis as $chassis){
  $cha1 = $chassis->getAttribute('AssetTag');
  $cha2 = $chassis->getAttribute('ChassisName');
  $cha3 = $chassis->getAttribute('IP');
  $cha4 = $chassis->getAttribute('ProductID');
  $cha5 = $chassis->getAttribute('ProductName');
  $cha6 = $chassis->getAttribute('SerialNumber');
  $cha7 = $chassis->getAttribute('UUID');
  $cha8 = $chassis->getAttribute('Status');
  
  if($cha1!=""){
    if($cha8=="Ok"){
      $sql_query1 = "INSERT INTO chassis_info(Ch_AssetTag,Ch_ChassisName,Ch_IP,Ch_ProductID,Ch_ProductName,Ch_SerialNumber,Ch_UUID,Ch_Status)VALUES('$cha1','$cha2','$cha3','$cha4','$cha5','$cha6','$cha7','$cha8')";
      $result1 = mysql_query($sql_query1);
	  $sql_query1 = "UPDATE `chassis_setting` SET `Ch_ChassisName`='$cha1' WHERE Ch_IP='$cha3'";
	  $result1 = mysql_query($sql_query1);
    }else{
      $cha0="failure";
      $sql_query1 = "INSERT INTO chassis_info(Ch_Status2,Ch_AssetTag,Ch_ChassisName,Ch_IP,Ch_ProductID,Ch_ProductName,Ch_SerialNumber,Ch_UUID,Ch_Status)VALUES('$cha0','$cha1','$cha2','$cha3','$cha4','$cha5','$cha6','$cha7','$cha8')";
      $result1 = mysql_query($sql_query1);
		  
      $sql_query1 = "INSERT INTO status_failure(Sf_latest,Sf_date,Sf_type,Sf_name,Sf_chassis,Sf_status)VALUES('1','$date','Chassis','$cha1','$cha1','$cha8')";
      $result1 = mysql_query($sql_query1);
	  
	  $sql_query1 = "UPDATE `chassis_setting` SET `Ch_ChassisName`='$cha1' WHERE Ch_IP='$cha3'";
	  $result1 = mysql_query($sql_query1);
    }
  }
  $status_changing[]=$cha1;
//------------------------------------------------------------------------------------------ scan cartridge
    $Cartridge=$chassis->getElementsByTagName('Cartridge');//insert Cartridge info
    foreach($Cartridge as $cartridge){
      $car2 = $cartridge->getAttribute('ManagementStatus');
      $car3 = $cartridge->getAttribute('Manufacturer');
      $car4 = $cartridge->getAttribute('Power');
      $car5 = $cartridge->getAttribute('ProductID');
      $car6 = $cartridge->getAttribute('ProductName');
      $car8 = $cartridge->getAttribute('SerialNumber');
      $car9 = $cartridge->getAttribute('Slot');
      $car10 = $cartridge->getAttribute('Status');
      $car11 = $cartridge->getAttribute('Type');
	  if($car10=="OK"){
        $sql_query2 = "INSERT INTO cartridge_info(Ch_ChassisName,Ca_ManagementStatus,Ca_Manufacturer,Ca_Power,Ca_ProductID,Ca_ProductName,Ca_SerialNumber,Ca_Slot,Ca_Status,Ca_Type)VALUES('$cha1','$car2','$car3','$car4','$car5','$car6','$car8','$car9','$car10','$car11')";
        $result2 = mysql_query($sql_query2);
	  }else{
        $car0="failure";
        $sql_query2 = "INSERT INTO cartridge_info(Ca_Status2,Ch_ChassisName,Ca_ManagementStatus,Ca_Manufacturer,Ca_Power,Ca_ProductID,Ca_ProductName,Ca_SerialNumber,Ca_Slot,Ca_Status,Ca_Type)VALUES('$car0','$cha1','$car2','$car3','$car4','$car5','$car6','$car8','$car9','$car10','$car11')";
        $result2 = mysql_query($sql_query2);
			
        $sql_query2 = "INSERT INTO status_failure(Sf_latest,Sf_date,Sf_type,Sf_name,Sf_chassis,Sf_status)VALUES('1','$date','Cartridge','$car8','$cha1','$car10')";
        $result2 = mysql_query($sql_query2);
		  
        $sql_query2 = "UPDATE `chassis_info` SET `Ch_Status2`='failure_cartridge' WHERE `Ch_AssetTag`='$cha1' AND `Ch_Status2`!='remove'";
        $result2 = mysql_query($sql_query2);
	  }	
//------------------------------------------------------------------------------------------ scan cartridge firmware 
      $Car_Firmware=$cartridge->getElementsByTagName('Firmware');
      foreach($Car_Firmware as $car_firmware){
        $car_fw1 = $car_firmware->getAttribute('CartridgeData');
        $car_fw2 = $car_firmware->getAttribute('CartridgeProgrammableLogicDevice');
        $car_fw3 = $car_firmware->getAttribute('CartridgeSatelliteFirmware');
        $car_fw4 = $car_firmware->getAttribute('CartridgeSystemROMFirmware');
		
        $sql_query2 = "UPDATE `cartridge_info` SET `CartridgeData`='$car_fw1',`CartridgeProgrammableLogicDevice`='$car_fw2',`CartridgeSatelliteFirmware`='$car_fw3',`CartridgeSystemROMFirmware`='$car_fw4' WHERE Ca_SerialNumber='$car8'";
		$result2 = mysql_query($sql_query2);	
      }
//------------------------------------------------------------------------------------------ scan node 
	  $Car_Node=$cartridge->getElementsByTagName('Node');
	  foreach($Car_Node as $car_node){
	    $car_nd1 = $car_node->getAttribute('BootConfiguration');
	    $car_nd2 = $car_node->getAttribute('DIMM1Capacity');
	    $car_nd3 = $car_node->getAttribute('DIMM1SerialNumber');
	    $car_nd4 = $car_node->getAttribute('NIC1MACAddress');
	    $car_nd5 = $car_node->getAttribute('NIC2MACAddress');
	    $car_nd6 = $car_node->getAttribute('Number');
	    
		$nd_id=$car8.$car_nd6;//Combine Ca_serialnumber and 
		
	    $sql_query5 = "INSERT INTO node_info(Nd_ID,Ca_Slot,Ch_ChassisName,BootConfiguration,DIMM1Capacity,DIMM1SerialNumber,NIC1MACAddress,NIC2MACAddress,Number)VALUES('$nd_id','$car9','$cha1','$car_nd1','$car_nd2','$car_nd3','$car_nd4','$car_nd5','$car_nd6')";
	    $result5 = mysql_query($sql_query5);
      }
    }
//------------------------------------------------------------------------------------------ scan chassis firmware
	$Cha_Firmware=$chassis->getElementsByTagName('Firmware');
    foreach($Cha_Firmware as $cha_firmware){
      $cha_fw0 = $cha_firmware->getAttribute('CLIVersion');
      $cha_fw1 = $cha_firmware->getAttribute('HPMoonshot1500ChassisFirmware');
      $cha_fw2 = $cha_firmware->getAttribute('HPMoonshot1500ChassisFirmwareFrontDisplayProgrammableLogicDevice');
      $cha_fw3 = $cha_firmware->getAttribute('HPMoonshot1500ChassisFirmwareProgrammableLogicDevice');
      $cha_fw4 = $cha_firmware->getAttribute('iLOChassisManagementFirmware');
      $cha_fw5 = $cha_firmware->getAttribute('iLOChassisManagementModuleProgrammableLogicDevice');
      
      if($cha_fw1!=""){	    
        $sql_query1 = "UPDATE `chassis_info` SET `CLIVersion`='$cha_fw0',`HPMoonshot1500ChassisFirmware`='$cha_fw1',`HPMoonshot1500ChassisFirmwareFrontDisplayProgrammableLogicDevice`='$cha_fw2',`HPMoonshot1500ChassisFirmwareProgrammableLogicDevice`='$cha_fw3',`iLOChassisManagementFirmware`='$cha_fw4',`iLOChassisManagementModuleProgrammableLogicDevice`='$cha_fw5' WHERE Ch_AssetTag='$cha1'";
	    $result1 = mysql_query($sql_query1);
      }
	}	
//------------------------------------------------------------------------------------------ scan switch	
	$Switch=$chassis->getElementsByTagName('Switch');
	foreach($Switch as $switch){
	  $sw1 = $switch->getAttribute('ManagementStatus');
	  $sw2 = $switch->getAttribute('Power');
	  $sw3 = $switch->getAttribute('ProductID');
	  $sw4 = $switch->getAttribute('ProductName'); // uplink
	  $sw5 = $switch->getAttribute('SerialNumber');
	  $sw6 = $switch->getAttribute('Slot');
	  $sw7 = $switch->getAttribute('Status');
	  $sw10 = $switch->getAttribute('UID');
	  $sw11 = $switch->getAttribute('UUID');
      $sw12 = $switch->getAttribute('ProductNameDownlink'); //downlink
      //---------------------------------------------------------------------------------------------------------------  downlink module
	  if($sw7=="OK"){
        $sql_query2 = "INSERT INTO switch_info(Sw_Type,Ch_ChassisName,Sw_ManagementStatus,Sw_Power,Sw_ProductID,Sw_ProductName,Sw_SerialNumber,Sw_Slot,Sw_Status,Sw_UID,Sw_UUID)VALUES('downlink','$cha1','$sw1','$sw2','$sw3','$sw12','$sw5','$sw6','$sw7','$sw10','$sw11')";
        $result2 = mysql_query($sql_query2);
      }else{
        $sw0="failure";
        $sql_query2 = "INSERT INTO switch_info(Sw_Status2,Sw_Type,Ch_ChassisName,Sw_ManagementStatus,Sw_Power,Sw_ProductID,Sw_ProductName,Sw_SerialNumber,Sw_Slot,Sw_Status,Sw_UID,Sw_UUID)VALUES('$sw0','downlink','$cha1','$sw1','$sw2','$sw3','$sw12','$sw5','$sw6','$sw7','$sw10','$sw11')";
        $result2 = mysql_query($sql_query2);
					  
        $sql_query2 = "INSERT INTO status_failure(Sf_latest,Sf_date,Sf_type,Sf_name,Sf_chassis,Sf_status)VALUES('1','$date','Switch','$sw5','$cha1','$sw7')";
        $result2 = mysql_query($sql_query2);
		  
        $sql_query2 = "UPDATE `chassis_info` SET `Ch_Status2`='failure_switch' WHERE `Ch_AssetTag`='$cha1' AND `Ch_Status2`!='remove'";
        $result2 = mysql_query($sql_query2);
      }
	  //----------------------------------------------------------------------------------------------------------------  uplink module
      $sql_query2 = "INSERT INTO switch_info(Sw_Type,Ch_ChassisName,Sw_ProductName,Sw_SerialNumber,Sw_Slot)VALUES('uplink','$cha1','$sw4','$sw5','$sw6')";
      $result2 = mysql_query($sql_query2);	  
	  //----------------------------------------------------------------------------------------------------------------
		
      $S_Firmware=$switch->getElementsByTagName('Firmware');//insert Switch_fw info
      foreach($S_Firmware as $s_firmware){
        $sw_fw1 = $s_firmware->getAttribute('CartridgeData');
        $sw_fw2 = $s_firmware->getAttribute('CartridgeSatelliteFirmware');
        $sw_fw3 = $s_firmware->getAttribute('SwitchFirmware');
		
        $sql_query2 = "UPDATE `switch_info` SET `CartridgeData`='$sw_fw1',`CartridgeSatelliteFirmware`='$sw_fw2',`SwitchFirmware`='$sw_fw3' WHERE Sw_SerialNumber='$sw5'";
		$result2 = mysql_query($sql_query2);
	  }
	}
  }
}

//------------------------------------------------------------------------------------------ Swap chassis name
$query_test = "SELECT * FROM chassis_info";
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM cartridge_info";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());

$query_test3 = "SELECT * FROM switch_info";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());

$query_test4 = "SELECT * FROM node_info";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error());
 
while($row_test = mysql_fetch_assoc($test)){
$Ch_ChassisName[]=$row_test['Ch_ChassisName'];
$Ch_SerialNumber[]=$row_test['Ch_SerialNumber'];
$Ch_AssetTag[]=$row_test['Ch_AssetTag'];
}?>
<?php 
while($row_test2 = mysql_fetch_assoc($test2)){
$Ch_ChassisName2[]=$row_test2['Ch_ChassisName'];
$Ca_SerialNumber[]=$row_test2['Ca_SerialNumber'];
}?>
<?php 
while($row_test3 = mysql_fetch_assoc($test3)){
$Ch_ChassisName3[]=$row_test3['Ch_ChassisName'];
$Sw_SerialNumber[]=$row_test3['Sw_SerialNumber'];
}?>
<?php 
while($row_test4 = mysql_fetch_assoc($test4)){
$Ch_ChassisName4[]=$row_test4['Ch_ChassisName'];
$DIMM1SerialNumber[]=$row_test4['DIMM1SerialNumber'];
}

//------------------------------------------------------------------------------------------ Swap chassis name in cartridge
for($i=0;$i<count($Ca_SerialNumber);$i++){ 
  $test2=$Ca_SerialNumber[$i];
  for($i2=0;$i2<count($Ch_SerialNumber);$i2++){
    if($Ch_ChassisName2[$i]==$Ch_ChassisName[$i2]){
	$test1=$Ch_AssetTag[$i2];
      $sql_query1 = "UPDATE `cartridge_info` SET `Ch_ChassisName`='$test1' WHERE `Ca_SerialNumber`='$test2'";
      mysql_query($sql_query1);
} } }
//------------------------------------------------------------------------------------------ Swap chassis name in switch
for($i=0;$i<count($Sw_SerialNumber);$i++){
  $test2=$Sw_SerialNumber[$i];
  for($i2=0;$i2<count($Ch_SerialNumber);$i2++){
    if($Ch_ChassisName3[$i]==$Ch_ChassisName[$i2]){
	$test1=$Ch_AssetTag[$i2];
      $sql_query1 = "UPDATE `switch_info` SET `Ch_ChassisName`='$test1' WHERE `Sw_SerialNumber`='$test2'";
      mysql_query($sql_query1);
} } }
//------------------------------------------------------------------------------------------ Swap chassis name in node
for($i=0;$i<count($DIMM1SerialNumber);$i++){
  $test2=$DIMM1SerialNumber[$i];
  for($i2=0;$i2<count($Ch_SerialNumber);$i2++){
    if($Ch_ChassisName4[$i]==$Ch_ChassisName[$i2]){
	$test1=$Ch_AssetTag[$i2];
      $sql_query1 = "UPDATE `node_info` SET `Ch_ChassisName`='$test1' WHERE `DIMM1SerialNumber`='$test2'";
      mysql_query($sql_query1);
} } }
//------------------------------------------------------------------------------------------ Swap chassis name in chassis
for($i=0;$i<count($Ch_SerialNumber);$i++){
  $test1=$Ch_AssetTag[$i]; $test2=$Ch_SerialNumber[$i];
  $sql_query1 = "UPDATE `chassis_info` SET `Ch_ChassisName`='$test1' WHERE `Ch_SerialNumber`='$test2'";
  mysql_query($sql_query1);
}
//------------------------------------------------------------------------------------------ find failure
$query_test1 = "SELECT * FROM status_failure WHERE Sf_latest='1'";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());
$query_test2 = "SELECT * FROM status_failure WHERE Sf_latest='2' AND `Sf_fix`='no'";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
?>
<?php
$Sf_name=array();
while($row_test1 = mysql_fetch_assoc($test1)){
$Sf_ID[]=$row_test1['Sf_ID'];
$Sf_name[]=$row_test1['Sf_name'];
$Sf_fix[]=$row_test1['Sf_fix'];
} ?>
<?php 
$Sf_name2=array();
while($row_test2 = mysql_fetch_assoc($test2)){
$Sf_ID2[]=$row_test2['Sf_ID'];
$Sf_name2[]=$row_test2['Sf_name'];
} ?>
<?php
$send_mail_failure="no";//set send_mail default as "no"

for($i2=0;$i2<count($Sf_name2);$i2++){//Sf_latest = 2	
  $query_test = "UPDATE `status_failure` SET `Sf_fix`='' WHERE `Sf_ID`='$Sf_ID2[$i2]'";//clean those Sf_latest='2' with `Sf_fix`='no'
  $test = mysql_query($query_test, $ShareRack) or die(mysql_error());  
  for($i=0;$i<count($Sf_name);$i++){//Sf_latest = 1
    if($Sf_name[$i]==$Sf_name2[$i2]){//check if Sf_latest = 1 already exist in Sf_latest = 2, means continuous failure 
	  $query_test = "DELETE FROM `status_failure` WHERE `Sf_ID`='$Sf_ID[$i]'";//delete Sf_latest = 1
      $test = mysql_query($query_test, $ShareRack) or die(mysql_error());	  
	  $query_test = "UPDATE `status_failure` SET `Sf_fix`='no' WHERE `Sf_ID`='$Sf_ID2[$i2]'";//change those found item to `Sf_fix`='no'
      $test = mysql_query($query_test, $ShareRack) or die(mysql_error());
      break;
    }
  }
}
//------------------------------------------------------------------------------------------ update status_changing
for($i=0;$i<count($status_changing);$i++){  
      $query_test = "UPDATE `status_changing` SET `Stc_Acknowledge`='' WHERE `Stc_ChassisName`='$status_changing[$i]' AND `Stc_Acknowledge`='no'";
      $test = mysql_query($query_test, $ShareRack) or die(mysql_error());
}
//------------------------------------------------------------------------------------------ 

mysql_select_db($database_ShareRack, $ShareRack);
$query_test1 = "SELECT * FROM chassis_setting WHERE Using_Status='On'";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM chassis_info WHERE Ch_Status2!='remove'";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
?>
<?php
while($row_test1 = mysql_fetch_assoc($test1)){
$CH1[]=$row_test1['Ch_ChassisName'];
$Ch_remove[]=$row_test1['Ch_remove'];
} ?>
<?php 
while($row_test2 = mysql_fetch_assoc($test2)){
$CH2[]=$row_test2['Ch_ChassisName'];
} ?>
<?php
$CH1_count = count($CH1); 
$CH2_count = count($CH2); 
$CH3=array();
$date=date("Y-m-d H:i:s"); 

for($i=0;$i<$CH1_count;$i++){//chassis_setting
  $check="";
  for($i2=0;$i2<$CH2_count;$i2++){//chassis_info	
	if($CH1[$i]==$CH2[$i2]){
	  $check="find"; 
      break;
    }
  }
  
  if($check!="find"){
	$sql_query = "UPDATE `chassis_setting` SET `Ch_remove`='remove' WHERE `Ch_ChassisName`='$CH1[$i]'";
    mysql_query($sql_query);
  }else{
  	$sql_query = "UPDATE `chassis_setting` SET `Ch_remove`='' WHERE `Ch_ChassisName`='$CH1[$i]'";
    mysql_query($sql_query);
  }
  
  
  if($Ch_remove[$i]!="remove"&&$CH1[$i]!=""){ 
    $check="";
    for($i2=0;$i2<$CH2_count;$i2++){//chassis_info	
	 if($CH1[$i]==$CH2[$i2]){
	   $check="find"; 
       break;
     }
    }
    
	if($check!="find"){
      $sql_query = "INSERT INTO `status_changing`(`Stc_Date`,`Stc_ChassisName`,`Stc_StatusMessage`,`Stc_Acknowledge`)VALUES('$date','$CH1[$i]','Chassis was being removed','no')";
      mysql_query($sql_query);
	  if($CH1[$i]!=""){
	    $CH3[]=$CH1[$i];
	  }
	}
  }
}

$query_test3 = "SELECT * FROM chassis_setting WHERE Using_Status='On' AND Ch_remove=''";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());

while($row_test3 = mysql_fetch_assoc($test3)){
 $CH4[]=$row_test3['Ch_ChassisName'];
}

for($i=0;$i<count($CH4);$i++){
$sql_query = "DELETE FROM `chassis_info` WHERE Ch_Status2='remove' AND Ch_ChassisName='$CH4[$i]'";
$result = mysql_query($sql_query);

$sql_query = "DELETE FROM `switch_info` WHERE Sw_Status2='remove' AND Ch_ChassisName='$CH4[$i]'";
$result = mysql_query($sql_query);

$sql_query = "DELETE FROM `cartridge_info` WHERE Ca_Status2='remove' AND Ch_ChassisName='$CH4[$i]'";
$result = mysql_query($sql_query);

$sql_query = "DELETE FROM `node_info` WHERE Nd_Status2='remove' AND Ch_ChassisName='$CH4[$i]'";
$result = mysql_query($sql_query);
}

$send_mail_status="no";

if(count($CH3)!=0){
  $send_mail_status="yes";
}

$CH_Total=implode(",",$CH3);

$query_test4 = "SELECT * FROM status_failure WHERE Sf_latest='1'";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error());

$Sf_latest_1=array();
while($row_test4 = mysql_fetch_assoc($test4)){
 $Sf_latest_1[]=$row_test4['Sf_latest'];
}

$send_mail_failure="no";

if(count($Sf_latest_1)!=0){
 $send_mail_failure="yes";
}

if($send_mail_status=="no"&&$send_mail_failure=="no"){
  if($Mode3=""){
    header("Location:../update_reserved.php?Mode=Admin");
  }else{
    header("Location:."); 
  }
}elseif($send_mail_status=="yes"&&$send_mail_failure=="yes"){
  //header("Location:mail_status_change.php?CH=$CH_Total&Fail=yes");
  header("Location:mail_status.php?Mode2=ChangeFailure&CH=$CH_Total");
}elseif($send_mail_status=="no"&&$send_mail_failure=="yes"){
  //header("Location:mail_status_failure.php");
  header("Location:mail_status.php?Mode2=Failure");
}elseif($send_mail_status=="yes"&&$send_mail_failure=="no"){
  //header("Location:mail_status_change.php?CH=$CH_Total&Fail=no");
  header("Location:mail_status.php?Mode2=Change&CH=$CH_Total");
}
?>
