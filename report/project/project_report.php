<?php
$dirLevel="../../";
//------------------------------------------------------------------------------------------------- GET Variable
if (isset($_GET['BG'])) {$BG = $_GET['BG'];}
if (isset($_GET['END'])) {$END = $_GET['END'];}
if (isset($_GET['PJ'])) {$PJ = $_GET['PJ'];}

//----------------------------------------------------------------------------------------- Got begin date & end date, transfer to M-d-Y
if($BG=="Select Begin Date"){
   $BG="";
}if($BG!=""){
   $BG_m=substr($BG,0,2);
   if($BG_m=="01"){$BG_m2="Jan";}elseif($BG_m=="02"){$BG_m2="Feb";}elseif($BG_m=="03"){$BG_m2="Mar"; }elseif($BG_m=="04"){$BG_m2="Apr";}elseif($BG_m=="05"){$BG_m2="May";}elseif($BG_m=="06"){$BG_m2="Jun";}elseif($BG_m=="07"){$BG_m2="Jul";}elseif($BG_m=="08"){$BG_m2="Aug";}elseif($BG_m=="09"){$BG_m2="Sep";}elseif($BG_m=="10"){$BG_m2="Oct";}elseif($BG_m=="11"){$BG_m2="Nov";}elseif($BG_m=="12"){$BG_m2="Dec";}	
   $BG=$BG_m2."-".substr($BG,3,2)."-".substr($BG,6,4);
}elseif($END=="Select End Date"){
   $END="";
}elseif($END!=""){
   $END_m=substr($END,0,2);
   if($END_m=="01"){$END_m2="Jan";}elseif($END_m=="02"){$END_m2="Feb";}elseif($END_m=="03"){$END_m2= "Mar";}elseif($END_m=="04"){$END_m2="Apr";}elseif($END_m=="05"){$END_m2="May";}elseif($END_m=="06"){$END_m2="Jun";}elseif($END_m=="07"){$END_m2="Jul";}elseif($END_m=="08"){$END_m2="Aug";}elseif($END_m=="09"){$END_m2="Sep";}elseif($END_m=="10"){$END_m2="Oct";}elseif($END_m=="11"){$END_m2="Nov";}elseif($END_m=="12"){$END_m2="Dec";}
   $END=$END_m2."-".(substr($END,3,2)+1)."-".substr($END,6,4);
}

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack,$ShareRack);

$PJ=explode(",",$PJ);
for($i=0;$i<count($PJ);$i++){
  $query_test1 = "SELECT * FROM project WHERE P_ID='$PJ[$i]'";
  $test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

  $row_test1 = mysql_fetch_assoc($test1);
  $P_Name[]=$row_test1['P_Name'];
  $P_BeginDate[]=$row_test1['P_BeginDate'];
  $P_EndDate[]=$row_test1['P_EndDate'];
}

$query_test2 = "SELECT * FROM schedule";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
$query_test3 = "SELECT *, Count(*) FROM schedule GROUP BY Sc_project";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());
?>
<?php
while($row_test2 = mysql_fetch_assoc($test2)){ //schedule info
  $Sc_chassis[]=$row_test2['Sc_chassis'];
  $Sc_start[]=$row_test2['Sc_start'];
  $Sc_end[]=$row_test2['Sc_end'];
  $Sc_project1[]=$row_test2['Sc_project'];
  $Sc_project2[]=$row_test2['Sc_project'];
  $Sc_project3[]=$row_test2['Sc_project'];
  $RS[]=$row_test2['Sc_start'];
  $RE[]=$row_test2['Sc_end'];
}
?>
<?php
while($row_test3 = mysql_fetch_assoc($test3)){ //Count project times
  $Sc_project_count[]=$row_test3['Sc_project'];
  $Count[]=$row_test3['Count(*)'];
}

//----------------------------------------------------------------------------------------- 
header("Content-Type: application/vnd.ms-cecel");
header("Content-Disposition: attachment; filename=Project_View_report.xlsx");
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Detail report
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('Detail Display');
//----------------------------------------------------------------------------------------- Excel column size
$Column=array('B','C','D','E','F');
$Content=array(60,31,24,23,23);  
for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- Project detail header
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Project Utilization Detail');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('0000CC');
$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
//----------------------------------------------------------------------------------------- Header
$setCellValue=array('Project Name','Reserved Chassis Under Project','Chassis Reserved Times','Chassis Reserved Days','Utilization Distribution ( = Each Chassis Reserved Days In The Project/ All Chassis Reserved Days In The Project)');

for($i=0;$i<count($Column);$i++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i].'2',$setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->getStartColor()->setARGB('CCFFFF');
}
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22);
//----------------------------------------------------------------------------------------- Each project Content  
$N=2;$N2=3;
for($i=0;$i<count($P_Name);$i++){

  if($P_BeginDate[$i]!=""&&$P_EndDate[$i]!=""){ //check the record has deploy and end date or not, and only show under parameter 
//----------------------------------------------------------------------------------------- Report project name column
  if($N2!=3){
    $N++;
    $N2++;
  }

  $title=array($P_Name[$i]);
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$N2, $P_Name[$i]);
  $objPHPExcel->getActiveSheet()->getStyle('B'.$N2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('B'.$N2)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('B'.$N2)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
  $objPHPExcel->getActiveSheet()->getStyle('B'.$N2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle('B'.$N2)->getFill()->getStartColor()->setARGB('F6CECE');
 
  $N_Start=$N2;
//----------------------------------------------------------------------------------------- 
  for($i2=0;$i2<count($Sc_project1);$i2++){
    if($P_Name[$i]==$Sc_project1[$i2]){
       $Sum4="";$times="";$Sum5="";
	   
       for($i3=0;$i3<count($Sc_project1);$i3++){//Collect the total account for same equip
         if($Sc_chassis[$i3]==$Sc_chassis[$i2]&&$P_Name[$i]==$Sc_project1[$i3]){

            if(strtotime($BG)<=strtotime($Sc_end[$i3])){
              if(strtotime($END)>=strtotime($Sc_start[$i3])||$END==""){
              //only show the time during tje two parameter
                 if(strtotime($BG)>=strtotime($Sc_start[$i3])&&$BG!=""){
	                $NewRS=$BG;
                 }else{
				    $NewRS=$Sc_start[$i3];
				 }
				 
                 if(strtotime($END)<=strtotime($Sc_end[$i3])&&$END!=""){
	                $NewRE=$END;
                 }else{
				    $NewRE=$Sc_end[$i3];
			     }
              }
		    } 
            $Sum4=$Sum4+round((strtotime($NewRE)-strtotime($NewRS))/3600/24)+1; //Chassis Reserved Days
            $times++;//Chassis Reserved Times
         }
       } 

       for($i3=0;$i3<count($Sc_project2);$i3++){
         if($P_Name[$i]==$Sc_project2[$i3]){ //group2 for couning total
           if(strtotime($BG)<=strtotime($Sc_end[$i3])){
             if(strtotime($END)>=strtotime($Sc_start[$i3])||$END==""){
	           //only show the time during tje two parameter
	            if(strtotime($BG)>=strtotime($Sc_start[$i3])&&$BG!=""){
		           $New_Sc_start=$BG;
                }else{
				   $New_Sc_start=$Sc_start[$i3];
				}
				
	            if(strtotime($END)<=strtotime($Sc_end[$i3])&&$END!=""){
		           $New_Sc_end=$END;
	            }else{
				   $New_Sc_end=$Sc_end[$i3];
				}
             } 
           } 
           $Sum5=$Sum5+round((strtotime($Sc_end[$i3])-strtotime($Sc_start[$i3]))/3600/24)+1;
         }
       }

$Utilization=round(($Sum4/$Sum5)*100)."%";
//----------------------------------------------------------------------------------------- Detail display content
$setCellValue=array($Sc_chassis[$i2],$times,$Sum4,$Utilization); 
$Column=array('C','D','E','F');

for($i3=0;$i3<count($setCellValue);$i3++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i3].$N2, $setCellValue[$i3]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N2)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N2)->getFill()->getStartColor()->setARGB('FBFBEF');
}
$N2++;
$N++;
//----------------------------------------------------------------------------------------- 
$DeleteWord=$Sc_chassis[$i2]; //Delete all the same equip
for($i3=0;$i3<count($Sc_project1);$i3++){
  if($DeleteWord==$Sc_chassis[$i3] && $P_Name[$i]==$Sc_project1[$i3]){
    $Sc_project1[$i3]='';
  }
}

}}
$N_End=$N2-1;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$N_Start.':'.'B'.$N_End);
//----------------------------------------------------------------------------------------- Radar chart
//	Set the Labels for each data series we want to plot
$dataseriesLabels = array(
	new PHPExcel_Chart_DataSeriesValues('String', 'Detail!$B$'.$N_Start, NULL, 1),	//	2011
);
//	Set the X-Axis Labels
$xAxisTickValues = array(
	new PHPExcel_Chart_DataSeriesValues('String', 'Detail!$C$'.$N_Start.':$C$'.$N_End, NULL, 3),	//	Q1 to Q4
);
//	Set the Data values for each data series we want to plot
$dataSeriesValues = array(
	new PHPExcel_Chart_DataSeriesValues('Number', 'Detail!$E$'.$N_Start.':$E$'.$N_End, NULL, 3),
);

//	Build the dataseries
$series = new PHPExcel_Chart_DataSeries(
	PHPExcel_Chart_DataSeries::TYPE_RADARCHART,				// plotType
	NULL,													// plotGrouping
	range(0, count($dataSeriesValues)-1),					// plotOrder
	$dataseriesLabels,										// plotLabel
	$xAxisTickValues,										// plotCategory
	$dataSeriesValues,										// plotValues
	NULL,													// smooth line
	PHPExcel_Chart_DataSeries::STYLE_MARKER					// plotStyle
);

//	Set up a layout object for the Pie chart
$layout = new PHPExcel_Chart_Layout();
//	Set the series in the plot area
$plotarea = new PHPExcel_Chart_PlotArea($layout, array($series));
//	Set the chart legend
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
$title = new PHPExcel_Chart_Title($P_Name[$i].' Chassis');


//	Create the chart
$chart = new PHPExcel_Chart(
	'chart1',		// name
	$title,			// title
	$legend,		// legend
	$plotarea,		// plotArea
	true,			// plotVisibleOnly
	0,				// displayBlanksAs
	NULL,			// xAxisLabel
	NULL			// yAxisLabel		- Radar charts don't have a Y-Axis
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('G'.$N_Start);
$chart->setBottomRightPosition('L'.($N_Start+15));

//	Add the chart to the worksheet
$objPHPExcel->getActiveSheet()->addChart($chart);
//-----------------------------------------------------------------------------------------
if(($N_End-$N_Start+1)<=15){
  $N2=$N_Start+15;
}else{
  $N2=$N_Start+($N_End-$N_Start+1);
}

}}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Overview report
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Overview');
//----------------------------------------------------------------------------------------- Excel column size
unset($Column);
$Column=array("B","C","D","E","F","G",'H');
$Content=array(60,20,17,25,22,19,35);  
for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- Top Header
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Project Utilization Overview');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('0000CC');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
//----------------------------------------------------------------------------------------- Second Top Header
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22);
unset($Content);
$Content=array('Project Name','Project Begin Date','Project End Date','Chassis Reserved Amount','Chassis Reserved Days','Chassis Utilization ( = Chassis Reserved Days / Project Life Cycle )','Chassis Utilization Among All Chassis');  

for($i=0;$i<(count($Column));$i++){    
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i].'2',$Content[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->getStartColor()->setARGB('CCFFFF');
}
//----------------------------------------------------------------------------------------- Count all the schedule days
$Sum3="";
for($i=0;$i<count($Sc_chassis);$i++){ //Find the reserved days in line with device
if(strtotime($BG)<=strtotime($Sc_end[$i])){
 if(strtotime($END)>=strtotime($Sc_start[$i])||$END==""){
   if(strtotime($BG)>=strtotime($Sc_start[$i])&&$BG!=""){//only show the time during tje two parameter
      $NewRS=$BG;
   }else{
      $NewRS=$Sc_start[$i];
   }
   
   if(strtotime($END)<=strtotime($Sc_end[$i])&&$END!=""){
      $NewRE=$END;
   }else{
      $NewRE=$Sc_end[$i];
   }
   $Sum3=$Sum3+round((strtotime($NewRE)-strtotime($NewRS))/3600/24)+1; //Get reserved days
    }
  } 
}
//----------------------------------------------------------------------------------------- Each Project Content
$N=2;$N2=3;
for($i=0;$i<count($P_Name);$i++){
  if($P_BeginDate[$i]!=""&&$P_EndDate[$i]!=""){ //check the record has deploy and end date or not, and only show under parameter
    if(strtotime($BG)>strtotime($P_BeginDate[$i])){//check and change to begin/end date
      $P_BeginDate[$i]=$BG;
	}
    if(strtotime($END)<strtotime($P_EndDate[$i])&&$END!=""){
      $P_EndDate[$i]=$END;
	}

	unset($Sum2);
    $Duration=round((strtotime($P_EndDate[$i])-strtotime($P_BeginDate[$i]))/3600/24);//Duration

    $Num2=count($Sc_project_count); 
    for($i2=0;$i2<$Num2;$i2++){
      if($Sc_project_count[$i2]==$P_Name[$i]){
         $Amount=$Count[$i2];//Chassis Reserved Amount
      } 
	}

    $Sum2="";
    for($i2=0;$i2<count($Sc_chassis);$i2++){ //Find the reserved days in line with device
      if($Sc_project3[$i2]==$P_Name[$i]){
        if(strtotime($BG)<=strtotime($Sc_end[$i2])){
          if(strtotime($END)>=strtotime($Sc_start[$i2])||$END==""){
            if(strtotime($BG)>=strtotime($Sc_start[$i2])&&$BG!=""){ //only show the time during tje two parameter
              $NewRS=$BG;
            }else{
			  $NewRS=$Sc_start[$i2];
			}
			   
            if(strtotime($END)<=strtotime($Sc_end[$i2])&&$END!=""){
	          $NewRE=$END;
	        }else{
			  $NewRE=$Sc_end[$i2];
			}
	        $Sum2=$Sum2+round((strtotime($NewRE)-strtotime($NewRS))/3600/24)+1;//Chassis Reserved Days
          }
        }
      }
    }

    $Utilization1=round(($Sum2/$Duration)*100);//Chassis Uitilization for display
    $Utilization2=round(($Sum2/$Sum3)*100);//Chassis Uitilization Among All Chassis for display
//----------------------------------------------------------------------------------------- Overview report content
    unset($Content);
    $Content=array($P_Name[$i],$P_BeginDate[$i],$P_EndDate[$i],$Amount,$Sum2,$Utilization1."%",$Utilization2."%");  
    $Color=array('F6CECE','FBFBEF','FBFBEF','FBFBEF','FBFBEF','FBFBEF','FBFBEF');  

    for($i2=0;$i2<count($Column);$i2++){  
       $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].$N2,$Content[$i2]);
       $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
       $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
       $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N2)->getFill()->getStartColor()->setARGB($Color[$i2]);
    }
    $objPHPExcel->getActiveSheet()->setCellValue("Y".$N2, $Utilization1);
    $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(1);
    $objPHPExcel->getActiveSheet()->setCellValue("Z".$N2, $Utilization2);
    $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(1);

    $N++;
	$N2++;
  }
}
//----------------------------------------------------------------------------------------- Bar chart  
$dataseriesLabels = array( //	Set the Labels for each data series we want to plot
    new PHPExcel_Chart_DataSeriesValues('String', 'Overview!$G$2', NULL, 1),	
	new PHPExcel_Chart_DataSeriesValues('String', 'Overview!$H$2', NULL, 1),	
);

$xAxisTickValues = array( //		Data Marker
	new PHPExcel_Chart_DataSeriesValues('String', 'Overview!$B$3:$B$'.$N2, NULL, ($N2-2)),
);

$dataSeriesValues = array( //	Set the Data values for each data series we want to plot
	new PHPExcel_Chart_DataSeriesValues('Number', 'Overview!$Y$3:$Y$'.$N2, NULL, ($N2-2)),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Overview!$Z$3:$Z$'.$N2, NULL, ($N2-2))
);

$series = new PHPExcel_Chart_DataSeries( //	Build the dataseries
	PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
	PHPExcel_Chart_DataSeries::GROUPING_STANDARD,	// plotGrouping
	range(0, count($dataSeriesValues)-1),			// plotOrder
	$dataseriesLabels,								// plotLabel
	$xAxisTickValues,								// plotCategory
	$dataSeriesValues								// plotValues
);

//	Make it a vertical column rather than a horizontal bar graph
$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
//	Set the series in the plot area
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
//	Set the chart legend
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

$title = new PHPExcel_Chart_Title('Chassis Utilization Chart');
$yAxisLabel = new PHPExcel_Chart_Title('Percentage (%)');

$chart1 = new PHPExcel_Chart( //	Create the chart
	'chart1',		// name
	$title,			// title
	$legend,		// legend
	$plotarea,		// plotArea
	true,			// plotVisibleOnly
	0,				// displayBlanksAs
	NULL,			// xAxisLabel
	$yAxisLabel		// yAxisLabel
);

//	Set the position where the chart should appear in the worksheet
$chart1->setTopLeftPosition('B'.($N2+4));
$chart1->setBottomRightPosition('K'.($N2+28));

//	Add the chart to the worksheet
$objPHPExcel->getActiveSheet()->addChart($chart1);

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
?>