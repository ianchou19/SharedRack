<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../css/main.css" />
<link rel="stylesheet" href="../css/main.ie.css" />
<script src='../js/update.js' type='text/javascript'></script>
<script src='../js/general.js' type='text/javascript'></script>
<script src='../js/ajax.js' type='text/javascript'></script>
<script type="text/javascript">

function showInfo()
{
    parent.flag = true;
    parent.showInfo.apply(null,arguments);
}

function updateComponent()
{
    parent.showOverlay();
	parent.anUpdater = new Updater(parent.document.getElementById("overlay-container").getElementsByTagName("p")[0]);
	parent.anUpdater.update.apply(parent.anUpdater,arguments);
}

function showSelected()
{
    parent.setselect("<?php
    if (isset($_GET['l0']))
    {
        echo $_GET['l0'];
		$test=$_GET['l0'];
    }
    if (isset($_GET['l1']))
    {
      echo "-" . $_GET['l1'];
    }
    if (isset($_GET['l2']))
    {
      echo $_GET['l2'];
    }
    ?>");
}

function refresh(chassis)
{
    window.location.href = "refresh.php?c=" + chassis;
}

function submitLog(form,chassis)
{
    var postdata = "log_text=" + form.log_text.value;
    getHTML("log_append.php?c=" + chassis, function (resp)
    {
        if (resp != "")
        {
            alert(resp);
        }
        else
        {
            window.location.href += "";
        }
    }, postdata);
    
    return false;
}
</script>
<!-- <base target="_top"/> <!-- jump out of iframe-->
</head>
<body class="inner-frame" onLoad="showSelected();">
<h2>Shared Rack Info</h3>
<hr style="margin-right:15px;"><?php

/*******************************************
 **            Main Script                **
 *******************************************/

if ( !isset($_GET['l0']))
{
?>
<div style="margin:0 auto;width:800px;">
  <p>This is the the info page, the brief instructions below should get you started.</p>
  <ul>
    <li>Select a component from the left to view its information or click the arrow next to a component to view its subcomponents.</li>
    <li>Deselect a component to return to this page.</li>
    <li>When adding a new chassis, or removing an old one. The system will automatically update information for every three hours.</li>
  </ul>

<script language="JavaScript">

function abc(yyy){
var CC=yyy;
  if(CC=="yo"){
     document.write(yyy)
  }
} 
</script>
  
  
</body> 
</html><?php
  exit(0);
}

// Checks for a level 0 info request
if (!isset($_GET['l1']))
{
  printChassisInfo($_GET['l0']);
  printFooter();
}

$l1_type = substr($_GET['l1'],0,1);
$l1_id = substr($_GET['l1'],1);

// Checks for a level 1 info request
if (!isset($_GET['l2']))
{
  switch($l1_type)
  {
    case 'C':
        printCartridgeInfo($_GET['l0'],$l1_id);
        break;
    case 'S':
        printSwitchInfo($_GET['l0'],$l1_id);
        break;
  }
  printFooter();
}

$l2_type = substr($_GET['l2'],0,1);
$l2_id = substr($_GET['l2'],1);

// Checks for a level 2 info request (not needed now, left it just in case)
if (!isset($_GET['l3']))
{
  switch($l2_type)
  {
    case 'N' && $l1_type=='C':
        printNodeInfo($_GET['l0'],$l1_id,$l2_id);
  }
  printFooter();
}


/*******************************************
 **        Function Definitions           **
 *******************************************/

/* Name: Print Footer
 * Description:
 *   Prints the page footer and ends the script.
 * Params: void
 * Return: void
 */
function printFooter()
{
  echo "</body>\n</html>";
  exit(0);
}


/* Name: Print Chassis Info
 * Description:
 *   Prints formatted info about the given chassis.
*/
function printChassisInfo($chassis)
{
  $alt = 0;
?>  <div class="table-heading">
    <div class="bread">
      <ul>
        <li class="first"><a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>');">Chassis</a></li>
      </ul>
    </div>
    <div class="layeredheading">
<?php 
require_once('../../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);
$query_test1 = "SELECT * FROM chassis_info WHERE Ch_ChassisName='$chassis'";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM firmware WHERE Fw_Type='Chassis'";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());

$query_cat = "SELECT COUNT(*) FROM cartridge_info WHERE Ch_ChassisName='$chassis'";
$cat1 = mysql_query($query_cat, $ShareRack) or die(mysql_error());
?> 
<?php //chassis_info info
while($row_test1 = mysql_fetch_assoc($test1)){
$Ch_ChassisName=$row_test1['Ch_ChassisName'];
$Ch_IP=$row_test1['Ch_IP'];
$Ch_AssetTag=$row_test1['Ch_AssetTag'];
$Ch_ProductID=$row_test1['Ch_ProductID'];
$Ch_ProductName=$row_test1['Ch_ProductName'];
$Ch_SerialNumber=$row_test1['Ch_SerialNumber'];
$Ch_UUID=$row_test1['Ch_UUID'];
$Ch_fw[0]=$row_test1['CLIVersion'];
$Ch_fw[1]=$row_test1['HPMoonshot1500ChassisFirmware'];
$Ch_fw[2]=$row_test1['HPMoonshot1500ChassisFirmwareFrontDisplayProgrammableLogicDevice'];
$Ch_fw[3]=$row_test1['HPMoonshot1500ChassisFirmwareProgrammableLogicDevice'];
$Ch_fw[4]="";
$Ch_fw[5]=$row_test1['iLOChassisManagementFirmware'];
$Ch_fw[6]=$row_test1['iLOChassisManagementModuleProgrammableLogicDevice'];
}
?>  
<?php
$row_cat1 = mysql_fetch_assoc($cat1);
$Ch_count=$row_cat1['COUNT(*)'];
?> 
<?php //chassis_info info
while($row_test2 = mysql_fetch_assoc($test2)){
$Fw_Name[]=$row_test2['Fw_Name'];
$Fw_Latest_File[]=$row_test2['Fw_Latest_File'];
}
?>

    </div> 
  </div>  
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobg">Component Info</th>
      <th scope="col" class="nobg">&nbsp;</th>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Chassis Name</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_ChassisName; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Chassis Manager IP</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_IP; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Asset Tag</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_AssetTag; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Product ID</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_ProductID; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Product Name</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_ProductName; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Serial Number</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_SerialNumber; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">UUID</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_UUID; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Cartridges</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ch_count;//echo countChildrenOfTag($chassisInfo,"Cartridge"); ?></td>
    </tr>
  </table>
  <br>
  <br>
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobg">Component Firmware</th>
      <th scope="col" class="nobg">&nbsp;</th>
    </tr>
<?php 
for($i=0;$i<count($Fw_Name);$i++){
  if($Fw_Name[$i]!="USER"){?>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>"><?php echo $Fw_Name[$i];?>
	  </th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>>
	     <?php
//-------------------------------------------------------------------------------------------------------- compare firmware		 
	     if($Fw_Latest_File[$i]==$Ch_fw[$i]){  
	        echo $Ch_fw[$i];
	     }else{
	        echo $Ch_fw[$i];
			/*
	        echo "<input class='button' type='submit' value='Update' style='height:25px;font-size:10px;'/>
                  <br>
				  <font color='red'><h4>( The latest version: ".$Fw_Latest_File[$i]." )</font>";	
            */				  
	     }
//--------------------------------------------------------------------------------------------------------		 
		 ?> 
         <br><br>     	
       </td>	  
    </tr>
<?php 
} } ?>
  </table> 
<br>

  <form name="refresh" action="." method="post" enctype="multipart/form-data" onsubmit="return removeMsgs();">
   <table class="infotable" cellspacing="0">
    <tr>
	  <td colspan="5" style="border:none;padding:0;">
        <input name="action" type="hidden" id="action" value="refresh">
	  </td>
    </tr>		 
   </table>
  </form>
<?php
}

/* Name: Print Cartridge Info
 * Description:
 *   Prints formatted info about the given cartridge.
*/
function printCartridgeInfo($chassis,$cartridge)
{
  $alt = 0;
?>  <div class="table-heading">
    <div class="bread">
      <ul>
        <li class="first"><a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>');">Chassis</a>
        <ul>
          <li>&raquo; <a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>','C<?php echo $cartridge; ?>');"> <?php echo "Cartridge ".$cartridge;?></a></li>
        </ul></li>
      </ul>
    </div>
  </div>
<?php 
require_once('../../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);
$query_test1 = "SELECT * FROM cartridge_info WHERE Ca_Slot='$cartridge' AND Ch_ChassisName='$chassis'";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM firmware WHERE Fw_Type='Cartridge'";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());?>
<?php //chassis_info info
while($row_test1 = mysql_fetch_assoc($test1)){
$Ca_Slot=$row_test1['Ca_Slot'];
$Ca_Type=$row_test1['Ca_Type'];
$Ca_Manufacturer=$row_test1['Ca_Manufacturer'];
$Ca_ProductName=$row_test1['Ca_ProductName'];
$Ca_SerialNumber=$row_test1['Ca_SerialNumber'];
$Ca_Status=$row_test1['Ca_Status'];
$Ca_fw[0]=$row_test1['CartridgeData'];
$Ca_fw[1]=$row_test1['CartridgeProgrammableLogicDevice'];
$Ca_fw[2]=$row_test1['CartridgeSatelliteFirmware'];
$Ca_fw[3]=$row_test1['CartridgeSystemROMFirmware'];
$Ca_Power=$row_test1['Ca_Power'];
}
?>   
<?php //chassis_info info
while($row_test2 = mysql_fetch_assoc($test2)){
$Fw_Name[]=$row_test2['Fw_Name'];
$Fw_Latest_File[]=$row_test2['Fw_Latest_File'];
}
?>   
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobgalt">Component Info</th>
      <th scope="col" <?php
  if ( $Ca_Power=="On" )
  {
    echo " class=\"green\">Powered On";
  }
  else if ( $Ca_Power=="Off" )
  {
    echo " class=\"red\">Powered Off";
  }
  else
  {
    echo ">Power Unavailable";
  }
?>      </th>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Cartridge Slot</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ca_Slot; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Cartridge Type</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ca_Type; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Manufacturer</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ca_Manufacturer; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Product Name</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ca_ProductName; ?></td>
    </tr>
    <tr>
      <th scope="row"class="spec<?php if ($alt==1) {echo "alt";} ?>">Serial Number</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ca_SerialNumber; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Status</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Ca_Status; ?></td>
    </tr>
  </table>
  <br>
  <br>
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobg">Component Firmware</th>
      <th scope="col" class="nobg">&nbsp;</th>
    </tr>
<?php for($i=1;$i<count($Fw_Name);$i++){?>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">
	    <?php echo $Fw_Name[$i];?>
	  </th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>>
	     <?php
//-------------------------------------------------------------------------------------------------------- compare firmware		    
		if($Fw_Latest_File[$i]==$Ca_fw[$i]){  
	        echo $Ca_fw[$i];
	     }else{
	        echo $Ca_fw[$i];
			/*
	        echo "<input class='button' type='submit' value='Update' style='height:25px;font-size:10px;'/>
                  <br>
				  <font color='red'><h4>( The latest version: ".$Fw_Latest_File[$i]." )</font>";	 
            */				  
	     }
//--------------------------------------------------------------------------------------------------------		 
		 ?>
        <br><br> 		 
        </td>
    </tr>
 <?php }?>
  </table>
<?php
}

/* Name: Print Switch Info
 * Description:
 *   Prints formatted info about the given switch.
*/
function printSwitchInfo($chassis,$switch){
  $alt = 0;
?>  <div class="table-heading">
    <div class="bread">
      <ul>
        <li class="first"><a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>');">Chassis</a>
        <ul>
          <li>&raquo; <a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>','S<?php echo $switch; ?>');"><?php echo "Switch ".$switch; ?></a></li>
        </ul></li>
      </ul>
    </div>
    <div class="layeredheading">
    </div> 
  </div>
<?php require_once('../../Connections/ShareRack.php'); ?>
<?php
mysql_select_db($database_ShareRack, $ShareRack);
$query_test1 = "SELECT * FROM switch_info WHERE Sw_Type='downlink' AND Sw_Slot='$switch' AND Ch_ChassisName='$chassis'";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM firmware WHERE Fw_Type='Switch'";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());?>
<?php //chassis_info info
while($row_test1 = mysql_fetch_assoc($test1)){
$Sw_Slot=$row_test1['Sw_Slot'];
$Sw_ProductName=$row_test1['Sw_ProductName'];
$Sw_SerialNumber=$row_test1['Sw_SerialNumber'];
$Sw_ProductID=$row_test1['Sw_ProductID'];
$Sw_UUID=$row_test1['Sw_UUID'];
$Sw_fw[0]=$row_test1['CartridgeData'];
$Sw_fw[1]=$row_test1['CartridgeSatelliteFirmware'];
$Sw_fw[2]=$row_test1['SwitchFirmware'];
}
?>   
<?php //chassis_info info
while($row_test2 = mysql_fetch_assoc($test2)){
$Fw_Name[]=$row_test2['Fw_Name'];
$Fw_Latest_File[]=$row_test2['Fw_Latest_File'];
}
?>  
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobg">Component Info</th>
      <th scope="col" class="nobg">&nbsp;</th>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Switch Slot</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Sw_Slot; ?></td>
    </tr>
    <tr>
      <th scope="row"class="spec<?php if ($alt==1) {echo "alt";} ?>">Product Name</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Sw_ProductName; ?></td>
    </tr>
    <tr>
      <th scope="row"class="spec<?php if ($alt==1) {echo "alt";} ?>">Serial Number</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Sw_SerialNumber; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Product ID</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Sw_ProductID; ?></td>
    </tr>
    <tr>
      <th scope="row"class="spec<?php if ($alt==1) {echo "alt";} ?>">UUID</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Sw_UUID; ?></td>
    </tr>
  </table>
  <br>
  <br>
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobg">Component Firmware</th>
      <th scope="col" class="nobg">&nbsp;</th>
    </tr>
<?php for($i=0;$i<count($Fw_Name);$i++){?>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">
	    <?php echo $Fw_Name[$i];?>
	  </th>
      <td <?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>>
	     <?php
//-------------------------------------------------------------------------------------------------------- compare firmware		    
		if($Fw_Latest_File[$i]==$Sw_fw[$i]){  
	        echo $Sw_fw[$i];
	     }else{
	        echo $Sw_fw[$i];
			/*
	        echo "<input class='button' type='submit' value='Update' style='height:25px;font-size:10px;'/>
                  <br>
				  <font color='red'><h4>( The latest version: ".$Fw_Latest_File[$i]." )</font>";	
            */				  
	     }	 
//--------------------------------------------------------------------------------------------------------		 
		 ?>
        <br><br> 		 
        </td>
    </tr><?php
    }?>
  </table>
<?php
}

/* Name: Print Node Info
 * Description:
 *   Prints formatted info about the given cartridge's node.
*/
function printNodeInfo($chassis,$cartridge,$node)
{
  $alt = 0;
?>  <div class="table-heading">
    <div class="bread">
      <ul>
        <li class="first"><a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>');">Chassis</a>
        <ul>
          <li>&raquo; <a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>','C<?php echo $cartridge; ?>');">Cartridge <?php echo $cartridge; ?></a>
          <ul>
            <li>&raquo; <a href="javascript:void(0);" onClick="showInfo('<?php echo $chassis; ?>','C<?php echo $cartridge; ?>','N<?php echo $node; ?>');"><?php echo "Node ".$node; ?></a></li>
          </ul></li>
        </ul></li>
      </ul>
    </div>
    <div class="layeredheading">
    </div> 
  </div>
<?php require_once('../../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);
$query_test1 = "SELECT * FROM node_info WHERE Number='$node' AND Ca_Slot='$cartridge' AND Ch_ChassisName='$chassis'";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());?>
<?php //chassis_info info
while($row_test1 = mysql_fetch_assoc($test1)){
$Number=$row_test1['Number'];
$NIC1MACAddress=$row_test1['NIC1MACAddress'];
$NIC2MACAddress=$row_test1['NIC2MACAddress'];
$BootConfiguration=$row_test1['BootConfiguration'];
$DIMM1Capacity=$row_test1['DIMM1Capacity'];
$DIMM1SerialNumber=$row_test1['DIMM1SerialNumber'];
}
?>     
  <table class="infotable" cellspacing="0">
    <tr>
      <th scope="col" class="nobg">Component Info</th>
      <th scope="col" class="nobg">&nbsp;</th>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Node #</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $Number; ?></td>
    </tr>
    <tr>
      <th scope="row"class="spec<?php if ($alt==1) {echo "alt";} ?>">NIC-1 MAC Address</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $NIC1MACAddress; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">NIC-2 MAC Address</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $NIC2MACAddress; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">Boot Configuration</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $BootConfiguration; ?></td>
    </tr>  
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">DIMM0 Capacity</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $DIMM1Capacity; ?></td>
    </tr>
    <tr>
      <th scope="row" class="spec<?php if ($alt==1) {echo "alt";} ?>">DIMM0 Serial Number</th>
      <td<?php if ($alt==1) {echo ' class="alt"'; $alt--;} else {$alt++;}?>><?php echo $DIMM1SerialNumber; ?></td>
    </tr>
</table>
<?php
}?>