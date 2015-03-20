<?php 
$dirLevel="../";
//------------------------------------------------------------------------------------ GET Variable
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //mail type mode

if($Mode2=="Change"||$Mode2=="ChangeFailure"){
  if (isset($_GET['CH'])) { $CH = $_GET['CH'];}
}elseif($Mode2=="Expired"){
  if(isset($_GET['Reservation'])){$Reservation=$_GET['Reservation'];}
  if(isset($_GET['Mail'])){$Mail=$_GET['Mail'];}
  if(isset($_GET['Day'])){$Day=$_GET['Day'];}
}

//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

if($Mode2=="Failure"){ 
   $query_test = "SELECT * FROM status_failure WHERE Sf_latest='1'";
   $test = mysql_query($query_test, $ShareRack) or die(mysql_error());

   while($row_test = mysql_fetch_assoc($test)){
    $Sf_ID[]=$row_test['Sf_ID'];
    $Sf_date[]=$row_test['Sf_date'];
    $Sf_type[]=$row_test['Sf_type'];
    $Sf_name[]=$row_test['Sf_name'];
    $Sf_chassis[]=$row_test['Sf_chassis'];
    $Sf_status[]=$row_test['Sf_status'];
   }   
}

//------------------------------------------------------------------------------------ Set Receiver 
if($Mode2=="Change"||$Mode2=="ChangeFailure"||$Mode2=="Failure"){
  $query_test="SELECT U_Mail FROM user WHERE U_Level='super'";
  $test=mysql_query($query_test,$ShareRack)or die(mysql_error());

  $row_test = mysql_fetch_assoc($test);
  $U_Mail=$row_test['U_Mail']; //set super admin as the receiver
}

//------------------------------------------------------------------------------------ Subject Of The Mail 
if($Mode2=="Change"||$Mode2=="ChangeFailure"){ //
   $subject="New Inventory Status Change";   
}elseif($Mode2=="Failure"){ //
   $subject="New Equipment Failure Issue";
}elseif($Mode2=="Expired"){ //
   $subject="Reservation Expired Notification";
}

//------------------------------------------------------------------------------------ Info And Link For Guiding Receiver 
if($Mode2=="Change"||$Mode2=="ChangeFailure"){ 
   $guide='Please go to <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/report/status/index.php?Mode=Admin">Status Change Management</a> page to handle the issue.';
}elseif($Mode2=="Failure"){ 
   $guide='Please go to <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/report/failure/index.php?Mode=Admin">Status Failure Management</a> page to handle the issue.';
}elseif($Mode2=="Expired"){  
   $guide='Please go to <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/schedule/index.php?Mode=User">Schedule</a> page to check the schedule.';
}

//------------------------------------------------------------------------------------
$alt=0; 
//------------------------------------------------------------------------------------ Mail Content
$message='
  <html>
  <head>
    <title>'.$subject.'</title>
    <link rel="stylesheet" href="http://'.$_SERVER['SERVER_NAME'].'/css/main.css"/>
  </head>
    <body>
	<h2>'.$subject.'</h2>
    <hr>
    <p style="padding-left:20px">'.$guide.'</p>
    <br>
    <table class="infotable" cellspacing="0">';

//----------------------------------------------------- Status Change part
if($Mode2=="Change"||$Mode2=="ChangeFailure"){

    $CH2=explode(",",$CH);

    for($i=0;$i<count($CH);$i++){	
      $query_test = "SELECT * FROM status_changing WHERE Stc_ChassisName='$CH2[$i]' AND Stc_Acknowledge='no'";
      $test = mysql_query($query_test, $ShareRack) or die(mysql_error());
	
      $row_test = mysql_fetch_assoc($test);
      $Stc_ChassisName=$row_test['Stc_ChassisName'];	  
      $Stc_Date=$row_test['Stc_Date'];
      $Stc_StatusMessage=$row_test['Stc_StatusMessage'];
      
	  $message.='
      <tr>
	    <font color="deepskyblue"><b>Item '.($i+1).'</b></font>
      </tr>
      <tr>
	    <th scope="row" class="spec'.($alt==1?'alt':'').' top" style="border-top: 1px solid #acd4e6;">Chassis Name: </th>
	    <td class="top"';
          if($alt==1){
            $message.=' class="alt"';
            $alt--;
          }else{
            $alt++;
          }
          $message.=' style="border-top: 1px solid #acd4e6;">'.$Stc_ChassisName.'
	    </td> 
      </tr>
      <tr>
	    <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Time: </th>
	    <td';
          if($alt==1){
            $message.=' class="alt"';
            $alt--;
          }else{
            $alt++;
          }
          $message.= '>'.$Stc_Date.'
	    </td> 
      </tr>
      <tr>
	    <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Status Message: </th>
	    <td';
          if($alt==1){
            $message.=' class="alt"';
            $alt--;
          }else{
            $alt++;
          }
          $message.= '>'.$Stc_StatusMessage.'
	    </td> 
      </tr>
      <tr></tr><tr></tr>';
    }

	
//----------------------------------------------------- Failure part
}elseif($Mode2=="Failure"){
    for($i=0;$i<count($Sf_ID);$i++){	
      $message.='
	  <tr>
	    <font color="deepskyblue"><b>Issue '.($i+1).'</b></font>
	  </tr>
	  <tr>
        <th scope="row" class="spec'.($alt==1?'alt':'').' top" style="border-top: 1px solid #acd4e6;">Equipment Type: </th>
        <td class="top"';
          if($alt==1){
            $message.=' class="alt"';
            $alt--;
          }else{
            $alt++;
          }
          $message.=' style="border-top: 1px solid #acd4e6;">'.$Sf_type[$i].'
	    </td> 
	  </tr>

      <tr>
        <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Equipment Name: </th>
        <td';
          if($alt==1){
            $message.=' class="alt"';
            $alt--;
          }else{
            $alt++;
          }
          $message.= '>'.$Sf_name[$i].'</td>
	    </td> 
	  </tr>
	  
      <tr>
        <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Time: </th>
        <td';
          if($alt==1){
             $message.=' class="alt"';
             $alt--;
          }else{
             $alt++;
          }
          $message.= '>'.$Sf_date[$i].'</td>
	    </td> 
	  </tr>
	  
      <tr>
        <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Belong To Chassis: </th>
        <td';
          if($alt==1){
             $message.=' class="alt"';
             $alt--;
          }else{
             $alt++;
          }
          $message.= '>'.$Sf_chassis[$i].'</td>
	    </td> 
	  </tr>

      <tr>
        <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Failure Message: </th>
        <td';
        if($alt==1){
           $message.=' class="alt"';
           $alt--;
        }else{
           $alt++;
        }
        $message.= '>'.$Sf_status[$i].'</td>
	    </td> 
	  </tr>
	  <tr></tr><tr></tr>';
    }		

	
//----------------------------------------------------- Reservation Expired	
}elseif($Mode2=="Expired"){
  $reservation=explode(",",$Reservation);
  $mail=explode(",",$Mail);
  $day=explode(",",$Day);

  for($i=0;$i<count($day);$i++){
    if($day[$i]==1){
      $Day="One";
    }elseif($day[$i]==3){
      $Day="Three";
    }

    $query_test = "SELECT * FROM schedule WHERE Sc_id='$reservation[$i]'";
    $test = mysql_query($query_test, $ShareRack) or die(mysql_error());

    $row_test = mysql_fetch_assoc($test);
    $Sc_id=$row_test['Sc_id'];
    $Sc_chassis=$row_test['Sc_chassis'];
    $Sc_project=$row_test['Sc_project'];
    $Sc_team=$row_test['Sc_team'];
	$U_Mail=$row_test['Sc_email']; //
    $Sc_start=$row_test['Sc_start'];
    $Sc_end=$row_test['Sc_end'];
	
    $message.='
	<tr>
	  <font color="deepskyblue"><b>Reservation Detail</b></font>
	</tr>
	<tr>
      <th scope="row" class="spec'.($alt==1?'alt':'').' top" style="border-top: 1px solid #acd4e6;">Chassis Name: </th>
      <td class="top"';
        if($alt==1){
          $message.=' class="alt"';
          $alt--;
        }else{
          $alt++;
        }
        $message.=' style="border-top: 1px solid #acd4e6;">'.$Sc_chassis.'
	  </td> 
	</tr>
    <tr>
      <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Begin Date Of Reservation: </th>
      <td';
        if($alt==1){
          $message.=' class="alt"';
          $alt--;
        }else{
          $alt++;
        }
        $message.= '>'.$Sc_start.'</td>
	  </td> 
    </tr>
    <tr>
      <th scope="row" class="spec' .  ($alt==1?'alt':'') . '"><font color="red">End Date Of Reservation: </font></th>
      <td';
        if($alt==1){
          $message.=' class="alt"';
          $alt--;
        }else{
          $alt++;
        }
        $message.= '><font color="red">'.$Sc_end.'</font></td>
	  </td> 
	</tr>
    <tr>
      <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Project Being Assigned To: </th>
      <td';
        if($alt==1){
          $message.=' class="alt"';
          $alt--;
        }else{
          $alt++;
        }
        $message.= '>'.$Sc_project.'</td>
	  </td> 
	</tr>
    <tr>
      <th scope="row" class="spec' .  ($alt==1?'alt':'') . '">Team Being Assigned To: </th>
      <td';
        if($alt==1){
          $message.=' class="alt"';
          $alt--;
        }else{
          $alt++;
        }
        $message.= '>'.$Sc_team.'</td>
	  </td> 
	</tr>
	<tr></tr><tr></tr>';	
  }	
}
//----------------------------------------------------- 		
    $message.='	
   </table>
 </body>
</html>';

//------------------------------------------------------------------------------------ Headers
$headers = "MIME-Version: 1.0\r\n"; // To send HTML mail, you can set the Content-type header
$headers .= "Content-type: text/html; charset=big5\r\n";
$headers .= "From: Moonshot SharedRack Tech <moonshotsharedracktech@hp.com>\r\n";
//------------------------------------------------------------------------------------ mail function
mail($U_Mail,"[Shared Rack] ".$subject,$message,$headers);
//------------------------------------------------------------------------------------ 	 
if($Mode2=="ChangeFailure"){
  header("Location:mail_status.php?Mode2=Failure");
}else{
  header("Location:../update_reserved.php?Mode=Admin");
}?>