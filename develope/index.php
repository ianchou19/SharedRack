<?php
$dirLevel="../";
//------------------------------------------------------------------------------------------------- GET Variable
include($dirLevel.'include/session_check.php');
require_once($dirLevel.'../Connections/ShareRack.php');
?>

<!-- ------------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Development | Moonshot Shared Rack</title>
<link rel="stylesheet" href="../css/main.css" />
<link rel="stylesheet" href="../css/main.ie.css" />
<link rel="stylesheet" href="../css/manage_firmware_index.css" />
<script src='../js/general.js' type='text/javascript'></script>
<script src='../js/ajax.js' type='text/javascript'></script>
<script src='../js/firmware.js' type='text/javascript'></script>
</head>
<body>
<?php 
$Mode="User";
$here="Report";//Tell page header to underline Home tab
  $pages=array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode",  
	"Management"=>$dirLevel."management.php?Mode=$Mode");     
  $headerAppend=" - Management"; 
include $dirLevel."include/header.php";
?>
<div class="content">
<h2 class="page-heading">Development lab</h2>
<hr>
<!-- -------------------------------------------------------------------------------------------- -->
<h2>Insert Data:<h2>
<h3>
&nbsp&nbsp&nbsp&nbsp Equip_info:&nbsp&nbsp&nbsp&nbsp
<a href="insert_equip_all.php">real</a>&nbsp&nbsp/&nbsp&nbsp
Insert (&nbsp
<a href="insert_equip_all.php?Mode3=normal">normal</a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_equip_all.php?Mode3=change">change</a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_equip_all.php?Mode3=failure">failure</a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_equip_all.php?Mode3=change_failure">change_failure</a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_equip_each.php?Mode3=normal">one_normal</a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_equip_each.php?Mode3=failure">one_failure</a>&nbsp
)&nbsp&nbsp/&nbsp&nbsp
<a href="delete_data.php?Mode3=equip">Clear</a>
<br><br>

&nbsp&nbsp&nbsp&nbsp Chassis_setting:&nbsp&nbsp&nbsp&nbsp
Insert (&nbsp
<a href="insert_data.php?Mode3=Taipei_chassis_setting">Taipei</a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_data.php?Mode3=Houston_chassis_setting">Houston</a>&nbsp
)&nbsp&nbsp/&nbsp&nbsp
Clear (&nbsp
<a href="delete_data.php?Mode3=clear_chassisname">ChassisName</a>&nbsp&nbsp&nbsp&nbsp
<a href="delete_data.php?Mode3=clear_remove">Remove</a>&nbsp
)
<br><br> 

&nbsp&nbsp&nbsp&nbsp Clear:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<a href="delete_data.php?Mode3=changing">Status Change Record</a>&nbsp&nbsp&nbsp&nbsp
<a href="delete_data.php?Mode3=failure">Failure Record</a>&nbsp&nbsp&nbsp&nbsp
<a href="delete_data.php?Mode3=firmware">Latest Firmware</a>

<br><br>

&nbsp&nbsp&nbsp&nbsp Expired:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<a href="expired_inform.php">Test Expired </a>
<br><br> 

<!-- &nbsp&nbsp&nbsp&nbsp Schedules:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp --> 
<br><br> 

<!-- -------------------------------------------------------------------------------------------- -->
<hr>
<h3>Reset Basic Setting:&nbsp&nbsp&nbsp&nbsp
<a href="insert_data.php?Mode3=Taipei_basic_setting">Taipei </a>&nbsp&nbsp&nbsp&nbsp
<a href="insert_data.php?Mode3=Houston_basic_setting">Houston </a>

</h3>  
</div>
</body>
