<?php 
$dirLevel="../";
//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$query_test1 = "SELECT * FROM schedule";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

$query_test2 = "SELECT * FROM chassis_info";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());

while($row_test1 = mysql_fetch_assoc($test1)){
 $Sc_id[]=$row_test1['Sc_id'];
 $Sc_type[]=$row_test1['Sc_type'];
 $Sc_chassis[]=$row_test1['Sc_chassis'];
 $Sc_project[]=$row_test1['Sc_project'];
 $Sc_team[]=$row_test1['Sc_team'];
 $Sc_email[]=$row_test1['Sc_email'];
 $Sc_start2[]=$row_test1['Sc_start2'];
 $Sc_end2[]=$row_test1['Sc_end2'];
 $Sc_comment[]=$row_test1['Sc_comment'];
 $Sc_permit[]=$row_test1['Sc_permit'];
}?>
<?php
while($row_test2 = mysql_fetch_assoc($test2)){
 $Ch_ChassisName[]=$row_test2['Ch_ChassisName'];
 $Ch_AssetTag[]=$row_test2['Ch_AssetTag'];
}?>

<script type="text/javascript">

var ganttData = [<?php
$firstFlag = true;

for($i=0;$i<count($Ch_ChassisName);$i++){
  $chassis = $Ch_ChassisName[$i];
  $chassisName = $Ch_AssetTag[$i];      
      if ($firstFlag){
        $firstFlag = false;
        echo "{";
      }else{
        echo ",{";
      }?>     
	  id: '<?php echo $Ch_ChassisName[$i]; ?>',
      name: "<?php echo $Ch_ChassisName[$i]; ?>",
      desc: "<a href=\"../info/?l0=<?php echo $chassis."&Mode=".$_GET['Mode']; ?>\" target=\"_parent\" style=\"margin-left:10px;\">Chassis Info</a>", 
      values: [<?php 	  
	    $firstFlag1 = true;
	  
	    for($i2=0;$i2<count($Sc_id);$i2++){
	      if($Sc_start2[$i2]!=""&&$Sc_chassis[$i2]==$Ch_ChassisName[$i]&&$Sc_permit[$i2]!="no"){
            if ($firstFlag1){
              $firstFlag1 = false;
              echo "{";
            }else{
              echo ",{";
            } 
            $from = (string)max(time(),(int)substr($Sc_start2[$i2],0,-3)) . substr($Sc_start2[$i2],-3);?>

            from: "/Date(<?php echo ($from+86400000); ?>)/",
            to: "/Date(<?php echo ($Sc_end2[$i2]+86400000); ?>)/",
            label: "<?php echo htmlspecialchars($Sc_project[$i2]); ?>",
            customClass: "<?php if ($Sc_type[$i2]==1){ echo "ganttOrange";}else{echo "ganttBlue";}?>",
            dataObj: [{ 
              id: "<?php echo $Sc_id[$i2]; ?>",
              type: "<?php echo $Sc_type[$i2]; ?>",
              chassis: "<?php echo (($text = $Ch_AssetTag[$i])!="")?$text:$chassis; ?>",
              project: "<?php echo htmlspecialchars($Sc_project[$i2]); ?>",
			  team: "<?php echo htmlspecialchars($Sc_team[$i2]); ?>",
              email: "<?php echo htmlspecialchars($Sc_email[$i2]); ?>",
              from: new Date(<?php echo $Sc_start2[$i2]; ?>),
              to: new Date(<?php echo $Sc_end2[$i2]; ?>),
              spec_instr: "<?php echo $Sc_comment[$i2]; ?>"
            }]}<?php
         } }   
    ?>]}<?php
}?>
];
</script>

