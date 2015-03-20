<?php 
$dirLevel="../../";
//------------------------------------------------------------------------------------------------- GET Variable
$BG2="";$END2="";$CH="";$SW="";$CA="";
if (isset($_GET['BG'])) {$BG2 = $_GET['BG'];}
if (isset($_GET['END'])) {$END2 = $_GET['END'];}
//if (isset($_GET['CH'])) {$CH = $_GET['CH'];}
//if (isset($_GET['SW'])) {$SW = $_GET['SW'];}
//if (isset($_GET['CA'])) {$CA = $_GET['CA'];}
$CH="Chassis";
$SW="Switch";
$CA="Cartridge";

//----------------------------------------------------------------------------------------- Got begin date & end date, transfer to M-d-Y
/*
$ab=count($Sf_date);
$DeployBG=$Sf_date[1];
$DeployEND=$Sf_date[$ab-1];

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
*/
//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack,$ShareRack);

$query_test1 = "SELECT * FROM status_failure ORDER BY Sf_date";
$test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());
$query_test2 = "SELECT * FROM status_failure WHERE Sf_type='Chassis' ORDER BY Sf_date";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
$query_test3 = "SELECT * FROM status_failure WHERE Sf_type='Switch' ORDER BY Sf_date";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());
$query_test4 = "SELECT * FROM status_failure WHERE Sf_type='Cartridge' ORDER BY Sf_date";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error());
/*
$query_test2 = "SELECT * FROM status_failure WHERE Sf_type='Chassis' AND Sf_date>='$BG' AND Sf_date<='$END' ORDER BY Sf_date";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
$query_test3 = "SELECT * FROM status_failure WHERE Sf_type='Switch' AND Sf_date>='$BG' AND Sf_date<='$END' ORDER BY Sf_date";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());
$query_test4 = "SELECT * FROM status_failure WHERE Sf_type='Cartridge' AND Sf_date>='$BG' AND Sf_date<='$END' ORDER BY Sf_date";
$test4 = mysql_query($query_test4, $ShareRack) or die(mysql_error());
*/

$Sf_date=array();
while($row_test1 = mysql_fetch_assoc($test1)){
$Sf_date[]=$row_test1['Sf_date'];
}?>
<?php //chassis_info info
$Sf_date1=array();
while($row_test2 = mysql_fetch_assoc($test2)){
$Sf_date1[]=$row_test2['Sf_date'];
$Sf_name[]=$row_test2['Sf_name'];
$Sf_status[]=$row_test2['Sf_status'];
}?>
<?php //switch_info info
$Sf_date2=array();
while($row_test3 = mysql_fetch_assoc($test3)){
$Sf_date2[]=$row_test3['Sf_date'];
$Sf_name2[]=$row_test3['Sf_name'];
$Sf_chassis2[]=$row_test3['Sf_chassis'];
$Sf_status2[]=$row_test3['Sf_status'];
}?>
<?php //cartridge_info info
$Sf_date3=array();
while($row_test4 = mysql_fetch_assoc($test4)){
$Sf_date3[]=$row_test4['Sf_date'];
$Sf_name3[]=$row_test4['Sf_name'];
$Sf_chassis3[]=$row_test4['Sf_chassis'];
$Sf_status3[]=$row_test4['Sf_status'];
}

//-----------------------------------------------------------------------------------------
header("Content-Type: application/vnd.ms-cecel");
header("Content-Disposition: attachment; filename=Inventory_failure_report.xlsx");
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

$Column=array('A','B','C','D','E');
$Column2=array('A','A','B','C','D');
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- failure report
//--------------------------------------------------------------------------------------------------------------------------------Cartridge failure report
if($CA=="Cartridge"){

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Cartridge');
//----------------------------------------------------------------------------------------- Excel column size
unset($setWidth);
$setWidth=array(30,30,30,30);  

for($i=0;$i<count($setWidth);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($setWidth[$i]);
}
//----------------------------------------------------------------------------------------- Cartridge Header
unset($setCellValue);unset($Color);unset($Row);
$setCellValue=array('Cartridge Failure Status Record','Date','Cartridge Serial Number','Belong To','Failure Message');
$Color=array('FF0000','D8D8D8','D8D8D8','D8D8D8','D8D8D8'); 
$Row=array(1,2,2,2,2);

for($i=0;$i<count($setCellValue);$i++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column2[$i].$Row[$i],$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFill()->getStartColor()->setARGB($Color[$i]);

  if($i==0){
    $objPHPExcel->getActiveSheet()->mergeCells($Column2[$i].$Row[$i].':D1');	
    $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight(27);
    $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);  
  }elseif($i==1){
    $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight(22);
  }
}
//----------------------------------------------------------------------------------------- Cartridge content
$N=3;

for($i=0;$i<count($Sf_date3);$i++){

  unset($setCellValue);
  $setCellValue=array($Sf_date3[$i],$Sf_name3[$i],$Sf_chassis3[$i],$Sf_status3[$i]);
  
  for($i2=0;$i2<count($setCellValue);$i2++){
    $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].$N,$setCellValue[$i2]);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFill()->getStartColor()->setARGB('FBFBEF');
  }
 $N++;
}
}

//--------------------------------------------------------------------------------------------------------------------------------Switch failure report

if($SW=="Switch"){

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('Switch');

//----------------------------------------------------------------------------------------- Excel column size
unset($setWidth);
$setWidth=array(30,30,30,30);  

for($i=0;$i<count($setWidth);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($setWidth[$i]);
}
//----------------------------------------------------------------------------------------- Switch header
unset($setCellValue);unset($Color);unset($Row);
$setCellValue=array('Switch Failure Status Record','Date','Switch Serial Number','Belong To','Failure Message');
$Color=array('FF0000','D8D8D8','D8D8D8','D8D8D8','D8D8D8'); 
$Row=array(1,2,2,2,2);

for($i=0;$i<count($setCellValue);$i++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column2[$i].$Row[$i],$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFill()->getStartColor()->setARGB($Color[$i]);

  if($i==0){
    $objPHPExcel->getActiveSheet()->mergeCells($Column2[$i].$Row[$i].':D1');	
    $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight(27);
    $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);  
  }elseif($i==1){
    $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight(22);
  }
}
//----------------------------------------------------------------------------------------- Switch content
$N=3;

for($i=0;$i<count($Sf_date2);$i++){
  
  unset($setCellValue);
  $setCellValue=array($Sf_date2[$i],$Sf_name2[$i],$Sf_chassis2[$i],$Sf_status2[$i]);
  
  for($i2=0;$i2<count($setCellValue);$i2++){
    $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].$N,$setCellValue[$i2]);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFill()->getStartColor()->setARGB('FBFBEF');
  }
 $N++;
}
}

//--------------------------------------------------------------------------------------------------------------------------------Chassis failure report
if($CH=="Chassis"){

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(2);
$objPHPExcel->getActiveSheet()->setTitle('Chassis');
$N2=3;
//----------------------------------------------------------------------------------------- Excel column size
$setWidth=array(30,30,30); 
 
for($i=0;$i<count($setWidth);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($setWidth[$i]);
}
//----------------------------------------------------------------------------------------- Chassis Header
$setCellValue=array('Chassis Failure Status Record','Date','Chassis Name','Failure Message');
$Color=array('FF0000','D8D8D8','D8D8D8','D8D8D8'); 
$Row=array(1,2,2,2);

for($i=0;$i<count($setCellValue);$i++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column2[$i].$Row[$i],$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFill()->getStartColor()->setARGB($Color[$i]);

  if($i==0){
     $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->setSize(14);
     $objPHPExcel->getActiveSheet()->mergeCells($Column2[$i].$Row[$i].':C1');
     $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight(27);
     $objPHPExcel->getActiveSheet()->getStyle($Column2[$i].$Row[$i])->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);  
  }elseif($i==1){
     $objPHPExcel->getActiveSheet()->getRowDimension($Row[$i])->setRowHeight(22);  
  }
}
//----------------------------------------------------------------------------------------- Chassis content
$N=3;

for($i=0;$i<count($Sf_date1);$i++){

  unset($setCellValue);
  $setCellValue=array($Sf_date1[$i],$Sf_name[$i],$Sf_status[$i]);
  
  for($i2=0;$i2<count($setCellValue);$i2++){
    $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].$N,$setCellValue[$i2]);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFill()->getStartColor()->setARGB('FBFBEF');
  }
 $N++;
}
}

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
?>
