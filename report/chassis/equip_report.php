<?php
$dirLevel="../../";
//------------------------------------------------------------------------------------------------- GET Variable
if (isset($_GET['BG'])) {$BG = $_GET['BG'];}
if (isset($_GET['END'])) {$END = $_GET['END'];}
if (isset($_GET['INT'])){$INT_All = $_GET['INT'];}
if (isset($_GET['CH'])){$CH = $_GET['CH'];}

//----------------------------------------------------------------------------------------- Got begin date & end date, transfer to M-d-Y
$BG_m2="";$END_m2="";
if($BG=="Select Begin Date"){
   $BG="";
}else{
   $BG_m=substr($BG,0,2);
   if($BG_m=="01"){$BG_m2="Jan";}elseif($BG_m=="02"){$BG_m2="Feb";}elseif($BG_m=="03"){$BG_m2="Mar"; }elseif($BG_m=="04"){$BG_m2="Apr";}elseif($BG_m=="05"){$BG_m2="May";}elseif($BG_m=="06"){$BG_m2="Jun";}elseif($BG_m=="07"){$BG_m2="Jul";}elseif($BG_m=="08"){$BG_m2="Aug";}elseif($BG_m=="09"){$BG_m2="Sep";}elseif($BG_m=="10"){$BG_m2="Oct";}elseif($BG_m=="11"){$BG_m2="Nov";}elseif($BG_m=="12"){$BG_m2="Dec";}	
   $BG=$BG_m2."-".substr($BG,3,2)."-".substr($BG,6,4);
}

if($END=="Select End Date"){
   $END="";
}elseif($END!=""){
   $END_m=substr($END,0,2);
   if($END_m=="01"){$END_m2="Jan";}elseif($END_m=="02"){$END_m2="Feb";}elseif($END_m=="03"){$END_m2= "Mar";}elseif($END_m=="04"){$END_m2="Apr";}elseif($END_m=="05"){$END_m2="May";}elseif($END_m=="06"){$END_m2="Jun";}elseif($END_m=="07"){$END_m2="Jul";}elseif($END_m=="08"){$END_m2="Aug";}elseif($END_m=="09"){$END_m2="Sep";}elseif($END_m=="10"){$END_m2="Oct";}elseif($END_m=="11"){$END_m2="Nov";}elseif($END_m=="12"){$END_m2="Dec";}
   $END=$END_m2."-".(substr($END,3,2)+1)."-".substr($END,6,4);
}

//------------------------------------------------------------------------------------------------- SQL Area
require_once($dirLevel.'../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack,$ShareRack);

$CH=explode(",",$CH);
for($i=0;$i<count($CH);$i++){
  $query_test1 = "SELECT * FROM chassis_setting WHERE Ch_S_ID='$CH[$i]'";
  $test1 = mysql_query($query_test1, $ShareRack) or die(mysql_error());

  $row_test1 = mysql_fetch_assoc($test1);//chassis_info info
  $Ch_IP[]=$row_test1['Ch_IP'];
  $Ch_ChassisName[]=$row_test1['Ch_ChassisName'];
  $Ch_DeployDate[]=$row_test1['Ch_DeployDate'];
  $Ch_EndDate[]=$row_test1['Ch_EndDate'];
}

$query_test2 = "SELECT * FROM schedule";
$test2 = mysql_query($query_test2, $ShareRack) or die(mysql_error());
$query_test3 = "SELECT * FROM month";
$test3 = mysql_query($query_test3, $ShareRack) or die(mysql_error());
?>
<?php 
while($row_test2 = mysql_fetch_assoc($test2)){ //schedule info
  $Sc_chassis[]=$row_test2['Sc_chassis'];
  $Sc_start[]=$row_test2['Sc_start'];
  $Sc_end[]=$row_test2['Sc_end'];
  $Sc_start2[]=$row_test2['Sc_start2'];
  $Sc_end2[]=$row_test2['Sc_end2'];
}?>
<?php
while($row_test3 = mysql_fetch_assoc($test3)){ //month info
  $M_ID[]=$row_test3['M_ID'];
  $M_Name[]=$row_test3['M_Name'];
  $M_Number[]=$row_test3['M_Number'];
  $M_Days[]=$row_test3['M_Days'];
  $Quarter[]=$row_test3['Quarter'];
  $Year[]=$row_test3['Year'];
}
 
//----------------------------------------------------------------------------------------- create excel code
header("Content-Type: application/vnd.ms-cecel");
header("Content-Disposition: attachment; filename=Chassis_Utilization_report.xlsx");
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//-----------------------------------------------------------------------------------------

if($INT_All!=""){
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Detail view of chassis
//----------------------------------------------------------------------------------------- Check run one detail display or both
$Quarterly_Action="not yet";
$Monthly_Action="not yet";

for($i0=0;$i0<2;$i0++){
  if(strpos($INT_All,"Quarterly")!==FALSE){
     
	 if($Quarterly_Action=="not yet"){
	   $INT="Quarterly";
	   $Quarterly_Action="done"; 
	   
	   $objPHPExcel->createSheet();
       $objPHPExcel->setActiveSheetIndex(1);
       $objPHPExcel->getActiveSheet()->setTitle('Quarterly Display');	
	 }elseif($Quarterly_Action="done"&&strpos($INT_All,"Monthly")!==FALSE){
	   $INT="Monthly";
	   
	   $objPHPExcel->createSheet();
       $objPHPExcel->setActiveSheetIndex(2);
       $objPHPExcel->getActiveSheet()->setTitle('Monthly Display');	   
	 }else{
	   break;	 
	 }
  
  }elseif(strpos($INT_All,"Monthly")!==FALSE){

	 if($Monthly_Action=="not yet"){	 
	   $INT="Monthly";
	   $Monthly_Action="done";	 
       
	   $objPHPExcel->createSheet();
       $objPHPExcel->setActiveSheetIndex(1);
       $objPHPExcel->getActiveSheet()->setTitle('Monthly Display');	
	 }else{
	   break;
	 }
  }else{
     break;
  }
//----------------------------------------------------------------------------------------- Excel column size
$N=4;
$Column=array('A','B','C');
$Content=array(5,5,20);  
for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- Chassis detail header
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chassis Detail');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('0000CC');
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(34);
$objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);   
//----------------------------------------------------------------------------------------- 
if($INT=="Quarterly"){  
  $setCellValue=array('Quarterly View Of Chassis','( = Each Month Reserved Days / Each Month deployed Days )','Chassis Name');
}elseif($INT=="Monthly"){
  $setCellValue=array('Monthly View Of Chassis','( = Each Month Reserved Days / Each Month deployed Days )','Chassis Name'); 
}
//----------------------------------------------------------------------------------------- Header
$Column1=array('B','E','C');
$Column2=array('D','K');
$setSize=array(14,11);
$setRowHeight=array(30,26,26);
$Row=array(2,2,3);
$Color=array('FF0000','FF0000','CCFFFF');

for($i=0;$i<count($Column1);$i++){
  $objPHPExcel->getActiveSheet()->setCellValue($Column1[$i].($Row[$i]+($N-4)), $setCellValue[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getFill()->getStartColor()->setARGB($Color[$i]);
  
  if($i!=1){
    $objPHPExcel->getActiveSheet()->getRowDimension(($Row[$i]+($N-4)))->setRowHeight($setRowHeight[$i]);
  }   
  if($i!=2){ 
    $objPHPExcel->getActiveSheet()->mergeCells($Column1[$i].($Row[$i]+($N-4)).':'.$Column2[$i].($Row[$i]+($N-4)));
    $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle($Column1[$i].($Row[$i]+($N-4)))->getFont()->setSize($setSize[$i]);  
  }
}
//----------------------------------------------------------------------------------------- Each Chassis Content  
for($i=0;$i<count($Ch_ChassisName);$i++){
//----------------------------------------------------------------------------------------- define chassis end date
  if($Ch_EndDate[$i]==""){
    $Ch_EndDate[$i]=date("M-d-Y");
    for($i2=0;$i2<count($Sc_chassis);$i2++){	
      if($Sc_chassis[$i2]==$Ch_ChassisName[$i]&&$Sc_end2[$i2]>(strtotime(date("d M Y"))."000")){
        $Ch_EndDate[$i]=$Sc_end[$i2];
	  }
	} 
  }
//----------------------------------------------------------------------------------------- announce array
$Chassis_Schedule=array();  
//----------------------------------------------------------------------------------------- check each schedule, save quarter, year, days record into $record3 & $record4 array  
  for($i2=0;$i2<count($Sc_chassis);$i2++){
//-----------------------------------------------------------------------------------------
    if($Ch_ChassisName[$i]==$Sc_chassis[$i2]){//check the $Sc_chassis with $Ch_ChassisName    
	   $find_first_month="not yet";	
       for($i3=0;$i3<count($M_ID);$i3++){ //run each month   	   
	     if((substr($Sc_start[$i2],0,3).substr($Sc_start[$i2],7,4))==($M_Name[$i3].$Year[$i3])&&$find_first_month=="not yet"){ //find the schedule's first month & year in month table					  
		    if((substr($Sc_start[$i2],0,3).substr($Sc_start[$i2],7,4))==(substr($Sc_end[$i2],0,3).substr($Sc_end[$i2],7,4))){//if the reserved begin/end month + begin/end year is the same 			  
              $Chassis_Schedule[]=$Year[$i3].$Quarter[$i3].$M_Name[$i3].(substr($Sc_end[$i2],4,2)-substr($Sc_start[$i2],4,2)+1);//the only month's reservation info 	
			  break; //jump out the read months table loop 			  
		    }else{ //if the reserved begin/end month + begin/end year is not the same 
              $Chassis_Schedule[]=$Year[$i3].$Quarter[$i3].$M_Name[$i3].($M_Days[$i3]-substr($Sc_start[$i2],4,2)+1);//put the first month's reservation info
		    }
		    $find_first_month="done";
	     }elseif($find_first_month=="done"){// already find the schedule's first month & year in month table, and continue from second month
		    if((substr($Sc_end[$i2],0,3).substr($Sc_end[$i2],7,4))!=($M_Name[$i3].$Year[$i3])){ //if haven't run to the end month 
			  $Chassis_Schedule[]=$Year[$i3].$Quarter[$i3].$M_Name[$i3].$M_Days[$i3];
		    }else{ //run to the end month 
		      $Chassis_Schedule[]=$Year[$i3].$Quarter[$i3].$M_Name[$i3].substr($Sc_end[$i2],4,2);//put the end month's reservation info
			  break; //jump out the read months table loop 
		    }		 
		 }		  
	   }		  		
	}
  } 
//----------------------------------------------------------------------------------------- check each chassis
  $Print_Year=array();
  $Print_Quarter=array();
  $Print_Month=array();
  $Print_Days=array();
  $Reserved_Days=array();
  $One_Quarter_Days=0;
  $Total=0;

if($INT=="Quarterly"){  
  
  $find_first_month2="not yet";
  for($i2=0;$i2<count($M_ID);$i2++){
    if((substr($Ch_DeployDate[$i],0,3).substr($Ch_DeployDate[$i],7,4))==($M_Name[$i2].$Year[$i2])&&$find_first_month2=="not yet"){ //find the first month of the chassis's deployment in month table     
      if((substr($Ch_EndDate[$i],0,3).substr($Ch_EndDate[$i],7,4))==($M_Name[$i2].$Year[$i2])){ //if find the first month is also the End of Deployment, print directly 

	     $Print_Year[]=$Year[$i2];	 
		 $Print_Quarter[]=$Quarter[$i2]; 
		 $Print_Days[]=substr($Ch_EndDate[$i],4,2)-substr($Ch_DeployDate[$i],4,2)+1;// Save first month's being reserved days into $One_Quarter_Days
  
         for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	        if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	           $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		    }
		 }
	     $Reserved_Days[]=$Total;
         $Total=0;		 

	  }else{
	     $One_Quarter_Days=$M_Days[$i2]-substr($Ch_DeployDate[$i],4,2);// Save first month's being reserved days into $One_Quarter_Days

         for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	        if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	           $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		    }
		 }
	     $find_first_month2="done";		
	  }	
    }elseif($find_first_month2=="done"){ //already finish first month process
	   if((substr($Ch_EndDate[$i],0,3).substr($Ch_EndDate[$i],7,4))==($M_Name[$i2].$Year[$i2])){ //if find the month is the End of Deployment 
          if($Quarter[$i2]==$Quarter[$i2-1]){ //if this month is still in the first quarter as same as last month
		     $Print_Year[]=$Year[$i2];
		     $Print_Quarter[]=$Quarter[$i2];
             $Print_Days[]=$One_Quarter_Days+substr($Ch_EndDate[$i],4,2);
			 
             for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }
             $Reserved_Days[]=$Total;
             $Total=0;			 
             break;			 
		  }else{ // if this month go to the next quarter
		     $Print_Year[]=$Year[$i2-1];
		     $Print_Quarter[]=$Quarter[$i2-1];
			 $Print_Days[]=$One_Quarter_Days;// last Q days
             $Reserved_Days[]=$Total;            
             $Total=0;		 
			 
			 $Print_Year[]=$Year[$i2];
		     $Print_Quarter[]=$Quarter[$i2];//this Q
             $Print_Days[]=substr($Ch_EndDate[$i],4,2); //this Q days			 

             for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }
             $Reserved_Days[]=$Total;
			 $Total=0;
			 break;
		  }
	   }else{ //if find the month is not the End of Deployment 
          if($Quarter[$i2]==$Quarter[$i2-1]){ //if this month is still in the first quarter as same as last month
             $One_Quarter_Days=$One_Quarter_Days+$M_Days[$i2]; //count to Save the rest month's being reserved day into $One_Quarter_Days			 
             
			 for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }
			 
		  }else{ // if this month go to the next quarter
		     $Print_Year[]=$Year[$i2-1];
		     $Print_Quarter[]=$Quarter[$i2-1];	 
			 $Print_Days[]=$One_Quarter_Days;// last Q days
             $Reserved_Days[]=$Total;
			 $Total=0;
			 
             for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }			 
             $One_Quarter_Days=$M_Days[$i2]; //this Q days	  
		  }
	   }
	}
  }	
  

}elseif($INT=="Monthly"){


  $find_first_month2="not yet";
  for($i2=0;$i2<count($M_ID);$i2++){
    if((substr($Ch_DeployDate[$i],0,3).substr($Ch_DeployDate[$i],7,4))==($M_Name[$i2].$Year[$i2])&&$find_first_month2=="not yet"){ //find the first month of the chassis's deployment in month table     
      if((substr($Ch_EndDate[$i],0,3).substr($Ch_EndDate[$i],7,4))==($M_Name[$i2].$Year[$i2])){ //if find the first month is also the End of Deployment, print directly 

	     $Print_Year[]=$Year[$i2];	 
		 $Print_Month[]=$M_Name[$i2]; 
		 $Print_Days[]=substr($Ch_EndDate[$i],4,2)-substr($Ch_DeployDate[$i],4,2)+1;// Save first month's being reserved days into $One_Quarter_Days
  
         for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	        if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	           $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		    }
		 }
	     $Reserved_Days[]=$Total;
         $Total=0;		 

	  }else{
	     $One_Quarter_Days=$M_Days[$i2]-substr($Ch_DeployDate[$i],4,2);// Save first month's being reserved days into $One_Quarter_Days

         for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	        if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	           $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		    }
		 }
	     $find_first_month2="done";		
	  }	
    }elseif($find_first_month2=="done"){ //already finish first month process
	   if((substr($Ch_EndDate[$i],0,3).substr($Ch_EndDate[$i],7,4))==($M_Name[$i2].$Year[$i2])){ //if find the month is the End of Deployment 
          if($M_Name[$i2]==$M_Name[$i2-1]){ //if this month is still in the first quarter as same as last month
		     $Print_Year[]=$Year[$i2];
		     $Print_Month[]=$M_Name[$i2];
             $Print_Days[]=$One_Quarter_Days+substr($Ch_EndDate[$i],4,2);
			 
             for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }
             $Reserved_Days[]=$Total;
             $Total=0;			 
             break;			 
		  }else{ // if this month go to the next quarter
		     $Print_Year[]=$Year[$i2-1];
		     $Print_Month[]=$M_Name[$i2-1];
			 $Print_Days[]=$One_Quarter_Days;// last Q days
             $Reserved_Days[]=$Total;            
             $Total=0;		 
			 
			 $Print_Year[]=$Year[$i2];
		     $Print_Month[]=$M_Name[$i2];//this Q
             $Print_Days[]=substr($Ch_EndDate[$i],4,2); //this Q days			 

             for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }
             $Reserved_Days[]=$Total;
			 $Total=0;
			 break;
		  }
	   }else{ //if find the month is not the End of Deployment 
          if($M_Name[$i2]==$Quarter[$i2-1]){ //if this month is still in the first quarter as same as last month
             $One_Quarter_Days=$One_Quarter_Days+$M_Days[$i2]; //count to Save the rest month's being reserved day into $One_Quarter_Days			 
             
			 for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }
			 
		  }else{ // if this month go to the next quarter
		     $Print_Year[]=$Year[$i2-1];
		     $Print_Month[]=$M_Name[$i2-1];	 
			 $Print_Days[]=$One_Quarter_Days;// last Q days
             $Reserved_Days[]=$Total;
			 $Total=0;
			 
             for($i3=0;$i3<count($Chassis_Schedule);$i3++){//run $Chassis_Schedule[]
	            if(substr($Chassis_Schedule[$i3],0,9)==($Year[$i2].$Quarter[$i2].$M_Name[$i2])){//check if there the reserved month or not
	               $Total=$Total+substr($Chassis_Schedule[$i3],9,2);//accumulate the reserved months' days
		        }
		     }			 
             $One_Quarter_Days=$M_Days[$i2]; //this Q days	  
		  }
	   }
	}
  }	


} 
//-----------------------------------------------------------------------------------------  
   $objPHPExcel->getActiveSheet()->setCellValue('C'.$N, $Ch_ChassisName[$i]);
   $objPHPExcel->getActiveSheet()->getStyle('C'.$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
   $objPHPExcel->getActiveSheet()->getStyle('C'.$N)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
   $objPHPExcel->getActiveSheet()->getStyle('C'.$N)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
   $objPHPExcel->getActiveSheet()->getStyle('C'.$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
   $objPHPExcel->getActiveSheet()->getStyle('C'.$N)->getFill()->getStartColor()->setARGB('F6CECE');
 
   unset($Column);unset($Color);unset($setCellValue);
   $Column=array("D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ"); 
   //$Color=array('A4A4A4','FBFBEF','FBFBEF');  
   $Color=array('A4A4A4','FBFBEF','FBFBEF','FBFBEF','FBFBEF');  
   
   $c="no";

   if($INT=="Quarterly"){
   
      for($i2=0;$i2<count($Print_Quarter);$i2++){ 
         $setCellValue=array($Print_Year[$i2],$Print_Quarter[$i2],(round(($Reserved_Days[$i2]/$Print_Days[$i2])*100)."%"));   
	     //$setCellValue=array($Print_Year[$i2],$Print_Quarter[$i2],$Print_Days[$i2],$Reserved_Days[$i2],(round(($Reserved_Days[$i2]/$Print_Days[$i2])*100)."%")); //check each column Deploy days & reserved days  
         $N2=0;
//----------------------------------------------------------------------------------------- for merge year column		 
         if($Print_Quarter[$i2]=="Q1"){
            $c="no"; 
         }  	 
//----------------------------------------------------------------------------------------- 
	     for($i3=0;$i3<count($setCellValue);$i3++){
//----------------------------------------------------------------------------------------- for merge year column	 
	       if($c=="no"){
		      $k=$Column[$i2];
		      $c="yes";
           }
	    			
	       if($i3==0&&$i2<(count($Print_Quarter)-1)){
	           if($Print_Year[$i2]!=$Print_Year[$i2+1]){
                  $objPHPExcel->getActiveSheet()->mergeCells($k.($N+$N2).':'.$Column[$i2].($N+$N2));		
	           }
		   }elseif($i3==0&&$i2==(count($Print_Quarter)-1)){
         	      $objPHPExcel->getActiveSheet()->mergeCells($k.($N+$N2).':'.$Column[$i2].($N+$N2));			   
		   }  			
//----------------------------------------------------------------------------------------- 
	       $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].($N+$N2), $setCellValue[$i3]);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getFill()->getStartColor()->setARGB($Color[$i3]);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	       if($i3==0){
	           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
	           $objPHPExcel->getActiveSheet()->getRowDimension($N)->setRowHeight(18);
	           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFont()->setBold(true);  
	           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFont()->setSize(12);
	       }elseif($i3!=0){
	           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
	           $objPHPExcel->getActiveSheet()->getRowDimension(($N+1))->setRowHeight(16);		
	       }		
	           $objPHPExcel->getActiveSheet()->getRowDimension(($N+3))->setRowHeight(10);
	           $N2++;
	     }
      }
      $N=$N+4;
   
   }elseif($INT=="Monthly"){

      for($i2=0;$i2<count($Print_Month);$i2++){ 
         $setCellValue=array($Print_Year[$i2],$Print_Month[$i2],(round(($Reserved_Days[$i2]/$Print_Days[$i2])*100)."%"));   
	     //$setCellValue=array($Print_Year[$i2],$Print_Month[$i2],$Print_Days[$i2],$Reserved_Days[$i2],(round(($Reserved_Days[$i2]/$Print_Days[$i2])*100)."%")); //check each column Deploy days & reserved days 
         $N2=0;
//----------------------------------------------------------------------------------------- for merge year column		 
         if($Print_Month[$i2]=="Jan"){
            $c="no"; 
         }  	 
//----------------------------------------------------------------------------------------- 
	     for($i3=0;$i3<count($setCellValue);$i3++){
//----------------------------------------------------------------------------------------- for merge year column	 
	       if($c=="no"){
		      $k=$Column[$i2];
		      $c="yes";
           }

	       if($i3==0&&$i2<(count($Print_Month)-1)){
	           if($Print_Year[$i2]!=$Print_Year[$i2+1]){
                  $objPHPExcel->getActiveSheet()->mergeCells($k.($N+$N2).':'.$Column[$i2].($N+$N2));		
	           }
		   }elseif($i3==0&&$i2==(count($Print_Month)-1)){
         	      $objPHPExcel->getActiveSheet()->mergeCells($k.($N+$N2).':'.$Column[$i2].($N+$N2));			   
		   }  
//----------------------------------------------------------------------------------------- 
	       $objPHPExcel->getActiveSheet()->setCellValue($Column[$i2].($N+$N2), $setCellValue[$i3]);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getFill()->getStartColor()->setARGB($Color[$i3]);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
           $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+$N2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	       if($i3==0){
	          $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
	          $objPHPExcel->getActiveSheet()->getRowDimension($N)->setRowHeight(18);
	          $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFont()->setBold(true);  
	          $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].$N)->getFont()->setSize(12);
	       }elseif($i3!=0){
	          $objPHPExcel->getActiveSheet()->getStyle($Column[$i2].($N+1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
	          $objPHPExcel->getActiveSheet()->getRowDimension(($N+1))->setRowHeight(16);		
	       }		
	          $objPHPExcel->getActiveSheet()->getRowDimension(($N+3))->setRowHeight(10);
	          $N2++;
	     }
      }
      $N=$N+4;   
   
   }   
//$N=$N+6; //check each column Deploy days & reserved days 
}
}
//----------------------------------------------------------------------------------------- 
}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Chassis Overview page
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Overview');
//----------------------------------------------------------------------------------------- Excel column size
$Column=array("B","C","D","E","F","G",'H');
$Content=array(20,31,24,23,23,20,35);  
for($i=0;$i<count($Column);$i++){  
  $objPHPExcel->getActiveSheet()->getColumnDimension($Column[$i])->setWidth($Content[$i]);
}
//----------------------------------------------------------------------------------------- Top Header
$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Chassis Overview');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('0000CC');
//----------------------------------------------------------------------------------------- Second Top Header
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22);
unset($Content);
$Content=array('Chassis Name','Deployment Begin Date','Deployment End Date','Chassis Reserved Times','Chassis Reserved Days','Chassis Utilization','Chassis Utilization Among All Chassis');  

$N=2;
for($i=0;$i<(count($Column));$i++){ 
  $objPHPExcel->getActiveSheet()->setCellValue($Column[$i].'2',$Content[$i]);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $objPHPExcel->getActiveSheet()->getStyle($Column[$i].'2')->getFill()->getStartColor()->setARGB('CCFFFF');
}
//----------------------------------------------------------------------------------------- Each Chassis Content
$N=3;
for($i=0;$i<count($Ch_ChassisName);$i++){ //run each chassis
//----------------------------------------------------------------------------------------- define chassis end date
  if($Ch_EndDate[$i]==""){
    $Ch_EndDate[$i]=date("M-d-Y");
    for($i2=0;$i2<count($Sc_chassis);$i2++){	
      if($Sc_chassis[$i2]==$Ch_ChassisName[$i]&&$Sc_end2[$i2]>(strtotime(date("d M Y"))."000")){
        $Ch_EndDate[$i]=$Sc_end[$i2];
	  }
	} 
  }
  
  $Reserved_Time=0; $Reserved_Day=0; $Reserved_Day_All=0;
  for($i2=0;$i2<count($Sc_chassis);$i2++){
    if($Ch_ChassisName[$i]==$Sc_chassis[$i2]){//check the equip name and only show the info between input begin/end date
       $Reserved_Time++;
       $Reserved_Day=$Reserved_Day+round((strtotime($Sc_end[$i2])-strtotime($Sc_start[$i2]))/3600/24)+1; //Get reserved days
	}
    $Reserved_Day_All=$Reserved_Day_All+round((strtotime($Sc_end[$i2])-strtotime($Sc_start[$i2]))/3600/24)+1; //Get reserved days	
  }
  
  $Chassis_Life=round((strtotime($Ch_EndDate[$i])-strtotime($Ch_DeployDate[$i]))/3600/24)+1; //put time different
  $Utilization1=round(($Reserved_Day/$Chassis_Life)*100);//Chassis Utilization 
  $Utilization2=round(($Reserved_Day/$Reserved_Day_All)*100);//Chassis Utilization Among All Chassis
//----------------------------------------------------------------------------------------- 
  unset($Content);
  $Content=array($Ch_ChassisName[$i],$Ch_DeployDate[$i],$Ch_EndDate[$i],$Reserved_Time,$Reserved_Day,$Utilization1."%",$Utilization2."%");  
//------------------------------------------- 
  for($i3=0;$i3<count($Column);$i3++){ 
    $objPHPExcel->getActiveSheet()->setCellValue($Column[$i3].$N, $Content[$i3]);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($Column[$i3].$N)->getFill()->getStartColor()->setARGB('FBFBEF');
  }
  $objPHPExcel->getActiveSheet()->setCellValue("Y".$N, $Utilization1);
  $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(1);
  $objPHPExcel->getActiveSheet()->setCellValue("Z".$N, $Utilization2);
  $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(1);
  $N++;
}

//----------------------------------------------------------------------------------------- Bar chart  
$dataseriesLabels = array( //	Set the Labels for each data series we want to plot
    new PHPExcel_Chart_DataSeriesValues('String', 'Overview!$G$2', NULL, 1),	
	new PHPExcel_Chart_DataSeriesValues('String', 'Overview!$H$2', NULL, 1),	
);

$xAxisTickValues = array( //		Data Marker
	new PHPExcel_Chart_DataSeriesValues('String', 'Overview!$B$3:$B$'.$N, NULL, ($N-2)),
);

$dataSeriesValues = array( //	Set the Data values for each data series we want to plot
	new PHPExcel_Chart_DataSeriesValues('Number', 'Overview!$Y$3:$Y$'.$N, NULL, ($N-2)),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Overview!$Z$3:$Z$'.$N, NULL, ($N-2))
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
$chart1->setTopLeftPosition('B'.($N+3));
$chart1->setBottomRightPosition('H'.($N+22));

//	Add the chart to the worksheet
$objPHPExcel->getActiveSheet()->addChart($chart1);

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
?>