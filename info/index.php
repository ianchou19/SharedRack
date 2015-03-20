<?php
$dirLevel="../";
//------------------------------------------------------------------------------------ GET Variable
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];}

//------------------------------------------------------------------------------------ Check Session
if($Mode=="Admin"){$private = true;} //check if display User or Admin mode
include($dirLevel.'include/session_check.php');

//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$query_test1 = "SELECT * FROM chassis_info ORDER BY `Ch_ID` ASC";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error()); //get chassis info
$query_test2 = "SELECT * FROM cartridge_info ORDER BY `Ca_ID` ASC";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error()); //get cartridge info
$query_test3 = "SELECT * FROM node_info";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error()); //get node info
$query_test4 = "SELECT * FROM switch_info";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error()); //get switch info

while($row_test1 = mysql_fetch_assoc($test1)){ 
$Ch_ChassisName[]=$row_test1['Ch_ChassisName'];
$Ch_AssetTag[]=$row_test1['Ch_AssetTag'];
}?>
<?php
while($row_test2 = mysql_fetch_assoc($test2)){ 
$Ca_Slot[]=$row_test2['Ca_Slot'];
$Ch_ChassisName2[]=$row_test2['Ch_ChassisName'];
$Ca_SerialNumber[]=$row_test2['Ca_SerialNumber'];
}?>
<?php
while($row_test3 = mysql_fetch_assoc($test3)){ 
$Number[]=$row_test3['Number'];
$Ca_Slot2[]=$row_test3['Ca_Slot'];
$Ch_ChassisName3[]=$row_test3['Ch_ChassisName'];
}?>
<?php
while($row_test4 = mysql_fetch_assoc($test4)){ 
$Sw_Slot[]=$row_test4['Sw_Slot'];
$Ch_ChassisName4[]=$row_test4['Ch_ChassisName'];
$Sw_Type[]=$row_test4['Sw_Type'];
}?>

<!-- ------------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Info Management | Moonshot Shared Rack</title>
<link rel="stylesheet" href="../css/main.css" />
<link rel="stylesheet" href="../css/main.ie.css" />
<link rel="stylesheet" href="../css/jqtree.css">
<link rel="stylesheet" href="../css/info_index.css" />

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="../js/jquery.tree.js"></script>
<script src="../js/jquery.cookie.js"></script>
<script src='../js/general.js' type='text/javascript'></script>
<script src='../js/ajax.js' type='text/javascript'></script>
<script type="text/javascript">
var data = [<?php
// Load the rack info xml
$firstFlag = true;
 for($i=0;$i<count($Ch_ChassisName);$i++){// Loop through the chassis
   $chassis = $Ch_ChassisName[$i];
   if ($firstFlag){
     $firstFlag = false;
     echo "{";
   }else{
     echo ",{";
   }?>

   label: '<?php //echo (($text = $Ch_AssetTag[$i]!="")?$text:"Chassis ({$chassis})";
   echo "Chassis ({$chassis})"?>',
   id: "<?php echo $chassis; ?>",
   data: ['<?php echo $chassis; ?>' ],
   //outdated: <?php //echo checkFirmware($rackChild)?'false':'true'; ?>,
   children: [<?php   
     $firstFlag1 = true;
     for($i2=0;$i2<count($Ca_Slot);$i2++){// Loop through the cartridges
	  if($Ch_ChassisName2[$i2]==$Ch_ChassisName[$i]){
	    $cartridge = $Ca_Slot[$i2];
        if ($firstFlag1){
          $firstFlag1 = false;
          echo "{";
        }else{
          echo ",{";
        }?>

        label: 'Cartridge <?php echo $cartridge; ?>',
        id: "<?php echo $chassis; ?>-C<?php echo $cartridge; ?>",
        data: [
            '<?php echo $chassis; ?>',
            'C<?php echo $cartridge; ?>'
        ],
        //outdated: <?php //echo checkFirmware($chassisChild)?'false':'true'; ?>,
        children: [<?php 
          $firstFlag2 = true;
		  for($i3=0;$i3<count($Number);$i3++){// Loop through the cartridge nodes
            if($Ca_Slot2[$i3]==$Ca_Slot[$i2]&&$Ch_ChassisName3[$i3]==$Ch_ChassisName[$i]){
     		  $node = $Number[$i3];
              if ($firstFlag2){
                $firstFlag2 = false;
                echo "{";
              }else{
                echo ",{";
              }?>

             label: 'Node <?php echo $node; ?>',
             id: "<?php echo $chassis; ?>-C<?php echo $cartridge; ?>N<?php echo $node; ?>",
             data: [
                '<?php echo $chassis; ?>',
                'C<?php echo $cartridge; ?>',
                'N<?php echo $node; ?>'
             ]}<?php 
		   }}?>	
	    ]}<?php		 
     }}	 	  
      for($i4=0;$i4<count($Sw_Slot);$i4++){
        if($Ch_ChassisName4[$i4]==$Ch_ChassisName[$i]&&$Sw_Type[$i4]=="downlink"){
  	      $switch = $Sw_Slot[$i4];
          if ($firstFlag1){
            $firstFlag1 = false;
            echo "{";
          }else{
            echo ",{";
          }?>

          label: 'Switch-Downlink <?php echo $switch; ?>',
          id: "<?php echo $chassis; ?>-S<?php echo $switch; ?>",
          data: [
            '<?php echo $chassis; ?>',
            'S<?php echo $switch; ?>'
          ]}<?php
          //outdated: <?php// echo checkFirmware($chassisChild)?'false':'true';      
        }elseif($Ch_ChassisName4[$i4]==$Ch_ChassisName[$i]&&$Sw_Type[$i4]=="uplink"){ 
 	      $switch = $Sw_Slot[$i4];
          if ($firstFlag1){
            $firstFlag1 = false;
            echo "{";
          }else{
            echo ",{";
          }?>

          label: 'Switch-Uplink <?php echo $switch; ?>',
          id: "<?php echo $chassis; ?>-S<?php echo $switch; ?>",
          data: [
            '<?php echo $chassis; ?>',
            'S<?php echo $switch; ?>'
          ]}<?php
          //outdated: <?php// echo checkFirmware($chassisChild)?'false':'true';   
		}  
	  }?>
    ]}<?php
  }?>
];

var flag = true;

$(function() {
    $('#tree1').tree({
        data: data,
        saveState: 'tree1',
        onCreateLi: function(node, $li) {
            $li.find('.jqtree-element').addClass('transition_bg_mid');
        }
    });
    
    $('#tree1').bind(
        'tree.click',
        function(event) {
            var aNode = $('#tree1').tree('getSelectedNode');
            if (aNode == event.node)
            {
                showInfo();
            }
            else{
                showInfo.apply(null,event.node.data);
            }
        }
    );
});

function setselect(nodeID)
{
    if (nodeID == "") 
    {
        if ($('#tree1').tree('getSelectedNode'))
        {
            $('#tree1').tree('selectNode', node);
        }
        return;
    }
    var node = $('#tree1').tree('getNodeById', nodeID);
    if (flag)
    {
        $('#tree1').tree('selectNode', node);
        if (! $('#tree1').tree('getSelectedNode'))
        {
            $('#tree1').tree('selectNode', node);
        }
        openToNode(node);
        flag = false;
    }
}

function openToNode(node)
{
    if (node.parent.parent != null)
    {
        openToNode(node.parent);
        $('#tree1').tree('openNode', node.parent, false);
    }
}

function showInfo()
{
	var q = "";
	for(var i=0; i<arguments.length; i++)
	{
		if (i>0)
		{
			q += "&";
		}
		q += "l" + i + "=" + arguments[i];
	}
	document.getElementById("info-panel").src = "info.php?" + q;
}

var sendingFlag = false;
function validateForm()
{
    if (document.reqrep.type.value == "" ||
        document.reqrep.msg.value == "")
    {
        document.getElementById("request-error").innerHTML = "All fields required!";
        return false;
    }
    
    if (sendingFlag) return false;
    sendingFlag = true;
    document.getElementById("request-error").innerHTML = "&nbsp;";
    document.getElementById("request-success").innerHTML = "&nbsp;";
    
    var reqdata = "type=" + document.reqrep.type.value +
                  "&chassis=" + document.reqrep.chassis.value +
                  "&eml=" + document.reqrep.eml.value +
                  "&msg=" + document.reqrep.msg.value;
    getHTML("send_msg.php?", function(req)
    {
        if (req != "")
        {
            document.getElementById("request-error").innerHTML = req;
        }
        else
        {
            document.getElementById("request-success").innerHTML = "We've received it!";
            document.getElementById("overlay-form-content-2").style.display = "none";
            document.getElementById("overlay-form-content-3").style.display = "block";
        }
        sendingFlag = false;
    }, reqdata);
    return false;
}

function reqWinClose()
{
    document.getElementById("request-success").innerHTML = "&nbsp;";
    document.getElementById("overlay-form-content-2").style.display = "block"; document.getElementById("overlay-form-content-3").style.display = "none";
    hideOverlay(1);
}
</script>
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here="Info";//Tell page header to underline Home tab
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
include $dirLevel."include/header.php";
?>
<!-- ----------------------------------------------------------------------- insert iframe -->
<div class="content nopadding">
<div class="left-column">
<div id="tree1"></div>
</div>
<div class="right-column">
  <iframe frameborder="0" id="info-panel" src="info.php?<?php echo $_SERVER['QUERY_STRING']; ?>"></iframe>
</div>
</div>
</body>
</html>