<?php
$dirLevel="../";
//--------------------------------------------------------------------------------------------- GET Variable
$Mode3="";
if (isset($_GET['Mode3'])) { $Mode3 = $_GET['Mode3'];}

//--------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

//--------------------------------------------------------------------------------------------- renew the latest update iLO time 
$date=date("Y-m-d H:i:s");
$sql_query = "UPDATE `Time_record` SET `Tr_time`='$date' WHERE `Tr_type`='Status_update_time'";
$result = mysql_query($sql_query);

//---------------------------------------------------------------------------------------------- change `Sf_latest`='1' to `Sf_latest`='2' with `Sf_fix`='no'
$query_test = "UPDATE `status_failure` SET `Sf_latest`='2',`Sf_fix`='no' WHERE `Sf_latest`='1'";
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

$xml = new DOMDocument();//Read rack.xml
//---------------------------------------------------------------------------------------------- different Mode3 of insert fake data 
if($Mode3==""){
  $xml->load($dirLevel.'../perl/rack.xml');
}elseif($Mode3=="normal"){
  $xml->load($dirLevel.'../perl/test/one_normal/rack.xml');
}elseif($Mode3=="failure"){
  $xml->load($dirLevel.'../perl/test/one_failure/rack.xml');
}
//------------------------------------------------------------------------------------------ scan rack 
$Rack=$xml->getElementsByTagName('Rack');
foreach($Rack as $rack){
//------------------------------------------------------------------------------------------ scan chassis  
 $Chassis=$rack->getElementsByTagName('Chassis');//insert Chassis info
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
      $Car_Firmware=$cartridge->getElementsByTagName('Firmware');//insert Car_fw info
      foreach($Car_Firmware as $car_firmware){
        $car_fw1 = $car_firmware->getAttribute('CartridgeData');
        $car_fw2 = $car_firmware->getAttribute('CartridgeProgrammableLogicDevice');
        $car_fw3 = $car_firmware->getAttribute('CartridgeSatelliteFirmware');
        $car_fw4 = $car_firmware->getAttribute('CartridgeSystemROMFirmware');
		
        $sql_query2 = "UPDATE `cartridge_info` SET `CartridgeData`='$car_fw1',`CartridgeProgrammableLogicDevice`='$car_fw2',`CartridgeSatelliteFirmware`='$car_fw3',`CartridgeSystemROMFirmware`='$car_fw4' WHERE Ca_SerialNumber='$car8'";
		$result2 = mysql_query($sql_query2);	
      }
//------------------------------------------------------------------------------------------ scan node 
	  $Car_Node=$cartridge->getElementsByTagName('Node');//insert Cartridge_node info
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
	$Cha_Firmware=$chassis->getElementsByTagName('Firmware');//insert Switch_fw info
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
	$Switch=$chassis->getElementsByTagName('Switch');//insert Switch info
	foreach($Switch as $switch){
	  $swNO++;
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
?>
<?php
//---------------------------------------------------------------------------------------------------------Swap chassis name
$query_test = "SELECT * FROM chassis_info";
$test = mysql_query($query_test, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM cartridge_info";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());

$query_test3 = "SELECT * FROM switch_info";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());

$query_test4 = "SELECT * FROM node_info";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error());
?>
<?php 
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
}?>
<?php 
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
//------------------------------------------------------------------------------------------ find if the individual has failure

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

if($send_mail_failure=="no"){
  header("Location:.");
}elseif($send_mail_failure=="yes"){
  header("Location:mail_status_failure.php");
}
?>
