<?php
$dirLevel="../";
//------------------------------------------------------------------------------------ GET Variable
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //action mode
if(isset($_GET['Mode3'])){$Mode3=$_GET['Mode3'];} //page type mode
//---------------------
if(isset($_GET['T_Name'])){$T_Name=$_GET['T_Name'];} //team which selected by user

//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$query_test="SELECT * FROM team WHERE T_Name='$T_Name'";
$test=mysql_query($query_test,$ShareRack)or die(mysql_error());

$row_test = mysql_fetch_assoc($test);
$T_Name=$row_test['T_Name']; //the new team name
$T_Name_Old=$row_test['T_Name_Old']; //the old team name
$T_Mail=$row_test['T_Mail']; //the new contact mail
$T_Mail_Old=$row_test['T_Mail_Old']; //the original contact mail

//------------------------------------------------------------------------------------ Set Receiver 
if(substr($Mode2,0,7)=="Request"){ 
  $query_test="SELECT U_Mail FROM user WHERE U_Level='super'";
  $test=mysql_query($query_test,$ShareRack)or die(mysql_error());

  $row_test = mysql_fetch_assoc($test);
  $U_Mail=$row_test['U_Mail']; //set super admin as the receiver
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
  $U_Mail=$T_Mail; //extract team creator as the receiver
}

//------------------------------------------------------------------------------------ Subject Of The Mail 
if($Mode2=="Request_Add"){ //request add team
   $subject="Add New Team Request";  
}elseif($Mode2=="Request_Edit"){ //request edit team
   $subject="Edit Team Request";
}elseif($Mode2=="Request_Remove"){ //request remove team
   $subject="Remove Team Request";
//---------------------
}elseif($Mode2=="Accept_Add"){ //accept add request
   $subject="Add New Team Request Was Being Accepted";
}elseif($Mode2=="Reject_Add"){ //reject add request
   $subject="Add New Team Request Was Being Rejected"; 
}elseif($Mode2=="Accept_Edit"){ //accept edit request
   $subject="Edit Team Request Was Being Accepted"; 
}elseif($Mode2=="Reject_Edit"){ //reject edit request
   $subject="Edit Team Request Was Being Rejected"; 
}elseif($Mode2=="Accept_Remove"){ //accept remove request
   $subject="Remove Team Request Was Being Accepted"; 
}elseif($Mode2=="Reject_Remove"){ //reject remove request
   $subject="Remove Team Request Was Being Rejected"; 
}

//------------------------------------------------------------------------------------ Info And Link For Guiding Receiver 
if(substr($Mode2,0,7)=="Request"){ 
   $guide='Please go to <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/create/index.php?Mode=Admin">Create Item</a> page to handle the request.';
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
   $guide='Please go to the <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/create/team/index.php?Mode=User">Create Item Page</a> to check the result.';
}

//------------------------------------------------------------------------------------ 
$alt = 0; 
//------------------------------------------------------------------------------------ Mail Content
$message='
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
	
//----------------------------------------------------- Team Name
      $message.='	
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'top" style="border-top: 1px solid #acd4e6;">Team Name: </th>
       <td class="top"';
         if($alt==1){
           $message.='class="alt"';
           $alt--;
         }else{
           $alt++;
         }	   
	     $message.='style="border-top: 1px solid #acd4e6;">'.$T_Name.'</td>';
//---------------------
	     if(substr($Mode2,-4)=="Edit"){ //only run when in edit mode
		   if($T_Name!=$T_Name_Old){
             $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Name: </b>'.$T_Name_Old.'</td>'; //show original name when there's any change
	       }else{
             $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Name Is The Same</b></td>';	//show "not any change" when there's not any change	   
		   }
		 }
//---------------------
	  $message.='
      </tr>';

//----------------------------------------------------- Contact Mail	   
	  $message.=' 
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">Contact Mail: </th>
       <td';
         if($alt==1){
           $message.= ' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.='>'.$T_Mail.'</td>';		 
//---------------------	 
	     if(substr($Mode2,-4)=="Edit"){ //only run when in edit mode
		   if($T_Mail!=$T_Mail_Old){	
	        $message.='	
	        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	        <td><b>Original Contact Mail: </b>  '.$T_Mail_Old.'</td>';
  	       }else{
              $message.='
	          <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	          <td><b>Original Mail Is The Same</b></td>';		   
		   } 	 
	     }
//---------------------	
	   $message.='		 
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
mail($U_Mail,"[Shared Rack] ".$subject,$message,$headers);
//-------------------------------------------------------------------------------------------------------------------------------- 
if($Mode2=="Reject_Add"||$Mode2=="Accept_Remove"||$Mode2=="Reject_Edit"){
   $sql_query = "DELETE FROM `team` WHERE `T_Name`='$T_Name'";
   mysql_query($sql_query);
}elseif($Mode2=="Accept_Edit"){
   $sql_query = "UPDATE `team` SET `T_Name_Old`='',`T_Mail_Old`='',`T_Permit`='' WHERE `T_Name`='$T_Name'";
   mysql_query($sql_query);	
   $sql_query = "UPDATE `schedule` SET `Sc_team`='$T_Name' WHERE `Sc_team`='$T_Name_Old'";
   mysql_query($sql_query);	
}		 		 
//------------------------------------------------------------------------------------ 	 
if($Mode2=="Request_Add"){
  header("Location:./team/index.php?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3");
}elseif($Mode2=="Request_Edit"){
  header("Location:./team/index.php?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3");
}elseif($Mode2=="Request_Remove"){
  header("Location:./team/index.php?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3");
}else{
  header("Location:.?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3"); 
}?>