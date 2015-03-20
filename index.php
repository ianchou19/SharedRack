<?php
$dirLevel="./";
//------------------------------------------------------------------------------------ GET Variable
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack,$ShareRack);

$query_test0="SELECT * FROM chassis_info"; //get chassis info
$test0=mysql_query($query_test0,$ShareRack)or die(mysql_error());
$query_test1="SELECT * FROM chassis_info"; //get chassis info
$test1=mysql_query($query_test1,$ShareRack)or die(mysql_error());
$query_test2="SELECT * FROM cartridge_info"; //get cartridge info
$test2=mysql_query($query_test2,$ShareRack)or die(mysql_error());
$query_test3="SELECT * FROM switch_info"; //get switch info
$test3=mysql_query($query_test3, $ShareRack)or die(mysql_error());
$query_test4="SELECT * FROM schedule";
$test4=mysql_query($query_test4, $ShareRack)or die(mysql_error());

while($row_test0=mysql_fetch_assoc($test0)){
$Ch_ChassisName0[]=$row_test0['Ch_ChassisName'];
$Ch_Status0[]=$row_test0['Ch_Status'];
}?>
<?php 
$Ch_ChassisName=array();
while($row_test1=mysql_fetch_assoc($test1)){
$Ch_ChassisName[]=$row_test1['Ch_ChassisName'];
$Ch_Status[]=$row_test1['Ch_Status'];
$Ch_Status2[]=$row_test1['Ch_Status2'];
}?>
<?php
$Ca_ProductName=array();
while($row_test2=mysql_fetch_assoc($test2)){
$Ch_ChassisName2[]=$row_test2['Ch_ChassisName'];
$Ca_ProductName[]=$row_test2['Ca_ProductName'];
$Ca_Slot[]=$row_test2['Ca_Slot'];
}?>
<?php 
$Sw_ProductName=array();
while($row_test3 = mysql_fetch_assoc($test3)){
$Sw_ProductName[]=$row_test3['Sw_ProductName'];
$Sw_Slot[]=$row_test3['Sw_Slot'];
$Ch_ChassisName4[]=$row_test3['Ch_ChassisName'];
}?>
<?php 
while($row_test4=mysql_fetch_assoc($test4)){
$Sc_chassis[]=$row_test4['Sc_chassis'];
$Sc_start2[]=$row_test4['Sc_start2'];
$Sc_end2[]=$row_test4['Sc_end2'];
}
//------------------------------------------------------------------------------------------------- Action -->	
if(isset($_POST["action"])&&($_POST["action"]=="user_guide")){
  header("Content-type: application/force-download");
  header("Content-type: application/pdf");
  header("Content-Disposition: attachment; filename=User_Guide");
  readfile("User_Guide.pdf");
}?>

<!-- ------------------------------------------------------------------------------------ -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Moonshot Shared Rack</title>
<link rel="stylesheet" href="css/main.css" />
<link rel="stylesheet" href="css/main.ie.css" />
<link href="<?php echo DIR_LEVEL; ?>img/favicon.ico" rel="shortcut icon"/>
<link rel="stylesheet" href="css/index.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<style>
.ui-tooltip {
  max-width: none !important;
}
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
$(function() {
  $( document ).tooltip({ position:{ my: "left top", at: "left bottom", collision: "flipfit" } });
});
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Home";//Tell page header to underline Home tab
if($Mode=="User"){
  $pages=array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode"); 
}elseif($Mode=="Admin"){
  $pages=array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode",  
	"Management"=>$dirLevel."management.php?Mode=$Mode");       
  $headerAppend=" - Management"; 
}  
$_GET['Mode3']="main_page";	
include $dirLevel."include/header.php";
?>
<!-- ----------------------------------------------------------------------- -->
<div class="content">
<div id="content-container">
  <div class="status-example">
    <span class="error">Failure</span>
    <span class="warning">Alert</span>
	<span class="available">Available</span>
    <span>Reserved</span>  </div>
    <h2>Status</h2>
    <hr>
	<br>
<!-- ----------------------------------------------------------------------- User Guide -->
    <form id="form" name="form" method="post" action="">
      <input name="action" type="hidden" id="action" value="user_guide">
      <input type="submit" class="button" value="User Guide">
    </form>
	<br><br>
  <ul class="tripleCol">
<!-- ----------------------------------------------------------------------- -->
<?php
for($i=0;$i<count($Ch_ChassisName);$i++){// Loop through the chassis
  $chassisName = $Ch_ChassisName[$i];?>
<!-- ----------------------------------------------------------------------- -->
    <li>	  
      <a class="text-color" href="info/?l0=<?php echo $Ch_ChassisName[$i];?>&Mode=<?php echo $Mode;?>"><h3
        <?php
        if($Ch_Status2[$i]=="reserved"){
          $key=-1;
        }elseif($Ch_Status2[$i]==""){
          echo ' class="available"';
          $key=0;
        }elseif($Ch_Status2[$i]=="remove"){
          echo ' class="warning"';
          $key=1;
        }elseif($Ch_Status2[$i]=="failure"||$Ch_Status2[$i]=="failure_cartridge"||$Ch_Status2[$i]=="failure_switch"){
          echo ' class="error"';
          $key=2;
        } 
	  ?>><?php echo $Ch_ChassisName[$i]; ?></h3></a>
<!-- -------------------------------------------------------------------------------------------------  -->
      <ul>
	  <?php
	  unset($Ca_ProductName2);
	  $Ca_ProductName2=array();
	  $query_test6 = "SELECT DISTINCT Ca_ProductName FROM cartridge_info WHERE Ch_ChassisName='$chassisName'";
      $test6 = mysql_query($query_test6, $ShareRack) or die(mysql_error());
	  while($row_test6 = mysql_fetch_assoc($test6)){
	  $Ca_ProductName2[]=$row_test6['Ca_ProductName'];
	  }?>

	  <?php
	  unset($Sw_ProductName2);
	  $Sw_ProductName2=array();
	  $query_test7 = "SELECT DISTINCT Sw_ProductName FROM switch_info WHERE Ch_ChassisName='$chassisName'";
      $test7 = mysql_query($query_test7, $ShareRack) or die(mysql_error());
	  while($row_test7 = mysql_fetch_assoc($test7)){
	  $Sw_ProductName2[]=$row_test7['Sw_ProductName'];
	  }?>	  
	  <?php
      $cartridgeArr=array();// Cartridge Array to count types of cartridges
      $lastSlot=0;// Last slot number (in case current slot wasn't found)
		for($i2=0;$i2<count($Ca_ProductName);$i2++){// Loop through the cartridge		
          $pname=trim((string)$Ca_ProductName[$i2]);
        }
		for($i2=0;$i2<count($Sw_ProductName);$i2++){// Loop through the switch
          $pname=trim((string)$Sw_ProductName[$i2]);
        } 
        if (trim((string)$Ca_ProductName[$i2])=="" ||// Collect "Other" cartridge types
            trim((string)$Ca_ProductName[$i2])=="N/A" ||
            trim((string)$Ca_ProductName[$i2])=="unavailable"){
          if (array_key_exists("Other",$cartridgeArr)){
            $cartridgeArr["Other"]['count']++;
            $cartridgeArr["Other"]['cartridges'].=','.(empty($slot)?$lastSlot + 1:$slot);
          }else{
            $cartridgeArr["Other"]=array('count' => 1, 'cartridges' => (empty($slot)?$lastSlot + 1:$slot));
          }
        }elseif(array_key_exists($pname,$cartridgeArr)){//Otherwise increment if cartridge is in the array
          $cartridgeArr[$pname]['count']++;
          $cartridgeArr[$pname]['cartridges'] .= ',' . (empty($slot)?$lastSlot + 1:$slot);
        }else{// Otherwise add the cartridge to the array
          $cartridgeArr[$pname] = array('count' => 1, 'cartridges' => (empty($slot)?$lastSlot + 1:$slot));
        }
        $lastSlot++;
     
      ksort($cartridgeArr);// Sort the array based on key (keys are type names)
      if (isset($cartridgeArr['Other'])){// Move the "Other" type to the end of the array, if it exists $tmp = $cartridgeArr['Other'];
        unset($cartridgeArr['Other']);
        $cartridgeArr['Other'] = $tmp;
      }

      $alt = 1;// Alternating flag for table rows
	  if(count($Ca_ProductName2)!=0){
       for($i2=0;$i2<count($Ca_ProductName2);$i2++){// Loop through cartridge array and display
	   $query_test6 = "SELECT * FROM cartridge_info WHERE Ch_ChassisName='$chassisName' AND Ca_ProductName='$Ca_ProductName2[$i2]'";
       $test6 = mysql_query($query_test6, $ShareRack) or die(mysql_error());
       $COUNT = mysql_num_rows($test6);
	   ?>
        <li <?php 
            if ($alt==1){
              echo ' class="alt"';
              $alt--;
            }else{
              $alt++;
            }
			$Cart2=$Ca_ProductName2[$i2];
			?> title="Slots: <?php 
			$previous="";
			$k2=0;$k=0;$k3=0;
			for($i3=0;$i3<count($Ca_Slot);$i3++){
			 if($Ch_ChassisName2[$i3]==$Ch_ChassisName[$i]&&$Ca_ProductName[$i3]==$Ca_ProductName2[$i2]){

//---------------------------------------------------- To gather the location display into group style 
			  if(($Ca_Slot[$i3]-$previous)!=1){	//detect when the location difference is more than one slot		    
				if($k==0){
				  if($previous!=""){  
					echo $previous.",";
				  }
				}elseif($k==1){
				  echo "-".$previous.",";
				  $k=0;
				}  
			  }else{
			    if($k2==0&&$k==0){
				  if($Ca_Slot[$i3]==1){
				    echo $Ca_Slot[$i3]; //the first group's first number
				  }elseif($Ca_Slot[$i3]!=1){
				    echo $Ca_Slot[$i3-1]; //the first group's first number				  
				  }	
				  $k2=1;$k=1;
			    }elseif($k2!=0&&$k==0){
				  echo $previous; // the second group's first number
				  $k=1;
				}
			  }
			  
			  $previous=$Ca_Slot[$i3];		  
			}}
			
			if($k==0){
			  echo $previous; // the last and single cartridge
			}elseif($k==1){
			  echo "-".$previous;  // the last and group cartridge
			}
//---------------------------------------------------- 
			
			?>" >
          <div class="cart-name"><?php echo $Ca_ProductName2[$i2]; ?></div>
          <div class="right"><div class="bottom"><?php echo $COUNT; ?></div></div>
        </li><?php
       }
	  }
	  for($i2=0;$i2<count($Sw_ProductName2);$i2++){// Loop through switch array and display
	   $query_test8 = "SELECT * FROM switch_info WHERE Ch_ChassisName='$chassisName' AND Sw_ProductName='$Sw_ProductName2[$i2]'";
       $test8 = mysql_query($query_test8, $ShareRack) or die(mysql_error());
       $COUNT2 = mysql_num_rows($test8);
	   ?>
         <li <?php 
            if ($alt==1){
              echo ' class="alt"';
              $alt--;
            }else{
              $alt++;
            }
			$Sw2=$Sw_ProductName2[$i2];
			?> title="Slots: <?php 
			$k=0;
			for($i3=0;$i3<count($Sw_Slot);$i3++){
			 if($Ch_ChassisName4[$i3]==$Ch_ChassisName[$i]&&$Sw_ProductName[$i3]==$Sw_ProductName2[$i2]){
			  if($k==1){//let comma go to the back
     			 echo ",";
              }
              echo $Sw_Slot[$i3]; 
              $k=1;			         			
			}}
			?>" >
		  <div class="cart-name"><?php echo $Sw_ProductName2[$i2];; ?></div>
          <div class="right"><div class="bottom"><?php echo $COUNT2; ?></div></div>
        </li><?php
      }?>	  
      </ul>
    </li><?php 
    } ?>
  </ul>
</div>
</body>
</html>