<?php 
$dirLevel="../";
//------------------------------------------------------------------------------------ GET Variable
$Mode2="";$Mode3="";
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //action mode
//---------------------
if (isset($_GET['Sc_id'])) { $Sc_id = $_GET['Sc_id'];} //reservation which created by user

//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack, $ShareRack);

if(substr($Mode2,0,7)=="Request"){ 
  $sql_query = "SELECT MAX(Sc_id) FROM schedule"; //get the latest reservation request
  $test=mysql_query($sql_query,$ShareRack)or die(mysql_error());
  $row_test=mysql_fetch_assoc($test);
  $Sc_id=$row_test['MAX(Sc_id)'];
 
  $sql_query = "SELECT * FROM schedule WHERE `Sc_id`='$Sc_id'";
  $test=mysql_query($sql_query,$ShareRack)or die(mysql_error()); 
  
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
  $sql_query = "SELECT * FROM schedule WHERE `Sc_id`='$Sc_id'"; //get the reservation request which selected by admin
  $test=mysql_query($sql_query,$ShareRack)or die(mysql_error());
}

$row_test=mysql_fetch_assoc($test);
$Sc_chassis=$row_test['Sc_chassis'];
$Sc_project=$row_test['Sc_project'];
$Sc_team=$row_test['Sc_team'];
$Sc_email=$row_test['Sc_email'];
$Sc_start=$row_test['Sc_start'];
$Sc_end=$row_test['Sc_end'];
$Sc_comment=$row_test['Sc_comment'];

//------------------------------------------------------------------------------------ Set Receiver 
if(substr($Mode2,0,7)=="Request"){ 
  $query_test="SELECT U_Mail FROM user WHERE U_Level='super'";
  $test=mysql_query($query_test,$ShareRack)or die(mysql_error());

  $row_test = mysql_fetch_assoc($test);
  $U_Mail=$row_test['U_Mail']; //set super admin as the receiver
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
  $U_Mail=$Sc_email; //extract project creator as the receiver
}

//------------------------------------------------------------------------------------ Subject Of The Mail
if($Mode2=="Request_Add"){ //request add reservation
   $subject = "Add New Schedule Request"; 
//---------------------
}elseif($Mode2=="Accept_Request"){ //accept add request
   $subject = "Add New Schedule Request Was Being Accepted"; 
}elseif($Mode2=="Reject_Request"){ //reject add request
   $subject = "Add New Schedule Request Was Being Rejected";   
}

//------------------------------------------------------------------------------------ Info And Link For Guiding Receiver 
if(substr($Mode2,0,7)=="Request"){ 
   $guide='Please go to <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/schedule/index.php?Mode=Admin">Schedule</a> page to handle the request.';
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
   $guide='Please go to the <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/schedule/index.php?Mode=User">Schedule</a> to check the result.';
}

//------------------------------------------------------------------------------------ 
$alt = 0; 
//------------------------------------------------------------------------------------ Mail Content
$message ='
  <html>
  <head>
     <title>'.$subject.'</title>
     <link rel="stylesheet" href="http://'.$_SERVER['SERVER_NAME'].'/css/main.css" />
  </head>
     <body>
     <h2>'.$subject.'</h2>
     <hr>
     <p style="padding-left:20px">'.$guide.'</p>
     <br>
     <table class="infotable" cellspacing="0">';

//----------------------------------------------------- Reserved Chassis Name
      $message.='	
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').' top" style="border-top: 1px solid #acd4e6;">Reserved Chassis</th>
	   <td></td>
       <td class="top"';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
		 $message.=' style="border-top: 1px solid #acd4e6;">'.$Sc_chassis.'</td>
       </tr>';
//----------------------------------------------------- Belong To Project
	  $message.='	   
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">Belong To Project</th>
	   <td></td>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.= '>'.$Sc_project.'</td>
       </tr>';
//----------------------------------------------------- Belong To Team
	  $message.='	   
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">Belong To Team</th>
	   <td></td>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.= '>'.$Sc_team.'</td>
       </tr>';
//----------------------------------------------------- Team Contact Email
	  $message.='	   
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'') . '">Team Contact Email</th>
	   <td></td>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.='>'.$Sc_email.'</td>
       </tr>';
//----------------------------------------------------- From
	  $message.='	   
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">From</th>
	   <td></td>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.='>'.$Sc_start.'</td>
       </tr>';
//----------------------------------------------------- To
	  $message.='   
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">To</th>
	   <td></td> 
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.='>'.$Sc_end.'</td>
       </tr>';
//----------------------------------------------------- Special Instructions
	  $message.='  
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">Special Instructions</th>
	   <td></td>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.='>'.$Sc_comment.'</td>
       </tr>';
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
mail($U_Mail,"[Shared Rack] ".$subject, $message, $headers);
 //--------------------------------------------------------------------------------------------------------------------------------  
if($Mode2=="Reject_Request"){
   $sql_query = "DELETE FROM `schedule` WHERE `Sc_id`='$Sc_id'";
   mysql_query($sql_query);
}		 
//------------------------------------------------------------------------------------ 
header("Location:.?Mode=$Mode&Mode2=$Mode2");
?>