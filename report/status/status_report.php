<?php 
$dirLevel="../../";
if (isset($_GET['BG'])) {$BG2 = $_GET['BG'];}
if (isset($_GET['END'])) {$END2 = $_GET['END'];}

//----------------------------------------------------------------------------------------- Got begin date & end date, transfer to M-d-Y
if (isset($_GET['BG'])) {$BG2 = $_GET['BG'];}
if (isset($_GET['END'])) {$END2 = $_GET['END'];}

$DeployBG="";$DeployEND="";

if($BG2=="Select Begin Date"||$BG2==""){
$BG=$DeployBG;
}else{
$BG=substr($BG2,6,4)."-".substr($BG2,0,2)."-".substr($BG2,3,2);
}

if($END2=="Select End Date"||$END2==""){
$END=$DeployEND;
}else{
$END=substr($END2,6,4)."-".substr($END2,0,2)."-".(substr($END2,3,2)+1);
}

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack,$ShareRack);

$query_test1 = "SELECT * FROM chassis_info WHERE Ch_Status2!='remove' ORDER BY Ch_ChassisName";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());
$query_test2 = "SELECT * FROM chassis_setting WHERE Using_status='On' ORDER BY Ch_ChassisName";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
$query_test3 = "SELECT * FROM chassis_setting  WHERE Using_status='Off' ORDER BY Ch_EndDate2";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());
$query_test4 = "SELECT * FROM status_changing WHERE Stc_ChassisName!=''";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error());
$query_test5 = "SELECT * FROM chassis_setting WHERE Ch_EndDate2>='$BG' AND Ch_EndDate2<='$END' ORDER BY Ch_EndDate2 DESC";
$test5 = mysql_query($query_test5, $ShareRack) or die(mysql_error());

while($row_test1 = mysql_fetch_assoc($test1)){
$Ch_ChassisName1[]=$row_test1['Ch_ChassisName'];
}?>
<?php
while($row_test2 = mysql_fetch_assoc($test2)){
$Ch_ChassisName2[]=$row_test2['Ch_ChassisName'];
$Ch_remove[]=$row_test2['Ch_remove'];
}?>
<?php
while($row_test3 = mysql_fetch_assoc($test3)){
$Ch_EndDate2[]=$row_test3['Ch_EndDate2'];
}?>
<?php
$Stc_Date=array();
while($row_test4 = mysql_fetch_assoc($test4)){
$Stc_ID[]=$row_test4['Stc_ID'];
$Stc_Date[]=$row_test4['Stc_Date'];
$Stc_ChassisName[]=$row_test4['Stc_ChassisName'];
$Stc_StatusMessage[]=$row_test4['Stc_StatusMessage'];
}?>
<?php
while($row_test5 = mysql_fetch_assoc($test5)){
$Ch_EndDate[]=$row_test5['Ch_EndDate2'];
$Ch_ChassisName[]=$row_test5['Ch_ChassisName'];
}

//-----------------------------------------------------------------------------------------
header("Content-Type: application/vnd.ms-cecel");
header("Content-Disposition: attachment; filename=Inventory_status_report.xlsx");
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- History page
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('History');
$N2=3;
//----------------------------------------------------------------------------------------- Excel column size
$Column=array('B','C','D');
$Content=array(30,30,45);  
for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- History Header
$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Inventory Changing Status Record');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF0000');
//----------------------------------------------------------------------------------------- Header
$setCellValue=array('Date','Chassis Name','Status Message');

for($i=0;$i<count($Column);$i++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i].'2',$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->getStartColor()->setARGB('D8D8D8');
}
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22);
//----------------------------------------------------------------------------------------- History Content
$N2=3;
unset($setCellValue);

for($i=0;$i<count($Stc_Date);$i++){
  $setCellValue=array($Stc_Date[$i],$Stc_ChassisName[$i],$Stc_StatusMessage[$i]);
  for($i2=0;$i2<count($Column);$i2++){
    $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].$N2, $setCellValue[$i2]);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getFill()->getStartColor()->setARGB('FBFBEF');
  } 
 $N2++;
}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Chassis Configuration page
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Chassis Configuration');
//-------------------------------------------------------------------------------------------------------------------------- Current Inventory Status
//----------------------------------------------------------------------------------------- Excel column size
unset($Column);unset($Content);
$Column=array('A','B','C');
$Content=array(12,34,12); 
 
for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- header
unset($Column);unset($Content);unset($setCellValue);
$setCellValue=array('Current Inventory Status','(update on '.date("Y-m-d").' '.date("H:i:s").')','Chassis Name');
$Column=array('A','A','B');
$Color=array('FF0000','B18904','D8D8D8');  
$setRowHeight=array(27,15,20);
$setSize=array(14,9,'');

for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i].($i+1),$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getRowDimension(($i+1))->setRowHeight($setRowHeight[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getFill()->getStartColor()->setARGB($Color[$i]);

  if($i!=(count($Column)-1)){
    $objPHPExcel->getActiveSheet()->mergeCells($Column[$i].($i+1).':C'.($i+1));
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i].($i+1))->getFont()->setSize($setSize[$i]);  
  }
}
//----------------------------------------------------------------------------------------- Content
$N=4;
for($i=0;$i<count($Ch_ChassisName1);$i++){
 $objPHPExcel->getActiveSheet()->setCellValue('B'.$N, $Ch_ChassisName1[$i]);
 $objPHPExcel->getActiveSheet()->getStyle('B'.$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
 $objPHPExcel->getActiveSheet()->getStyle('B'.$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
 $objPHPExcel->getActiveSheet()->getStyle('B'.$N)->getFill()->getStartColor()->setARGB('FBFBEF');
 
 $N++;
}

unset($Chassis);
//-------------------------------------------------------------------------------------------------------------------------- Latest Inventory Status Acknowledged By Admin
//----------------------------------------------------------------------------------------- Excel column size
unset($Column);unset($Content);
$Column=array('D','E','F','G');
$Content=array(15,12,34,12);  

for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- header
unset($Column);unset($Content);unset($setCellValue);unset($Color);unset($setSize);
$setCellValue=array('Latest Inventory Status Acknowledged By Admin','(update on '.date("Y-m-d").' '.date("H:i:s").')','Chassis Name','Alert');
$Column=array('E','E','F','G');
$Color=array('FF0000','B18904','D8D8D8','D8D8D8');  
$setRowHeight=array(27,15,'','');
$setSize=array(14,9,'','');
$Row=array(1,2,3,3);

for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i].$Row[$i],$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getFill()->getStartColor()->setARGB($Color[$i]);

  if($i<(count($Column)-2)){
     $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getFont()->setSize($setSize[$i]);
     $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight($setRowHeight[$i]);
     $objPHPExcel->getActiveSheet()->getStyle($Column[$i].$Row[$i])->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
     $objPHPExcel->getActiveSheet()->mergeCells($Column[$i].$Row[$i].':H'.$Row[$i]);
  }
}
//----------------------------------------------------------------------------------------- Content
$N=4;
for($i=0;$i<count($Ch_ChassisName2);$i++){
  if($Ch_ChassisName2[$i]!=""){
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$N, $Ch_ChassisName2[$i]);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$N)->getFill()->getStartColor()->setARGB('FBFBEF');
  
    if($Ch_remove[$i]!=""){
      $objPHPExcel->getActiveSheet()->getStyle('F'.$N)->getFill()->getStartColor()->setARGB('F2F5A9'); 
	  $objPHPExcel->getActiveSheet()->setCellValue('G'.$N, 'remove');
	  $objPHPExcel->getActiveSheet()->getStyle('G'.$N)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
      $objPHPExcel->getActiveSheet()->getStyle('G'.$N)->getFill()->getStartColor()->setARGB('FFFF00');
	  $objPHPExcel->getActiveSheet()->getStyle('G'.$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);     
    }   
  $N++;
  }
}

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
?>