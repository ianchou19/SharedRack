<?php
$dirLevel="../";
//------------------------------------------------------------------------------------ GET Variable
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
if(isset($_GET['Mode2'])){$Mode2=$_GET['Mode2'];} //action mode
if(isset($_GET['Mode3'])){$Mode3=$_GET['Mode3'];} //page type mode
//---------------------
if(isset($_GET['P_Name'])){$P_Name=$_GET['P_Name'];} //project which selected by user

//------------------------------------------------------------------------------------ SQL Area
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$query_test="SELECT * FROM project WHERE P_Name='$P_Name'";
$test=mysql_query($query_test,$ShareRack)or die(mysql_error());

$row_test = mysql_fetch_assoc($test);
$P_Name=$row_test['P_Name']; //the new project name
$P_Name_Old=$row_test['P_Name_Old']; //the old project name
$P_BeginDate=$row_test['P_BeginDate']; //the new begin date
$P_BeginDate_Old=$row_test['P_BeginDate_Old']; //the original begin date
$P_EndDate=$row_test['P_EndDate']; //the new end date
$P_EndDate_Old=$row_test['P_EndDate_Old']; //the original end date
$P_Mail=$row_test['P_Mail']; //the new contact mail
$P_Mail_Old=$row_test['P_Mail_Old']; //the original contact mail

//------------------------------------------------------------------------------------ Set Receiver 
if(substr($Mode2,0,7)=="Request"){ 
  $query_test="SELECT U_Mail FROM user WHERE U_Level='super'";
  $test=mysql_query($query_test,$ShareRack)or die(mysql_error());

  $row_test = mysql_fetch_assoc($test);
  $U_Mail=$row_test['U_Mail']; //set super admin as the receiver
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
  $U_Mail=$P_Mail; //extract project creator as the receiver
}

//------------------------------------------------------------------------------------ Subject Of The Mail 
if($Mode2=="Request_Add"){ //request add project
   $subject="Add New Project Request";  
}elseif($Mode2=="Request_Edit"){ //request edit project
   $subject="Edit Project Request";
}elseif($Mode2=="Request_Remove"){ //request remove project
   $subject="Remove Project Request";
//---------------------
}elseif($Mode2=="Accept_Add"){ //accept add request
   $subject="Add New Project Request Was Being Accepted";
}elseif($Mode2=="Reject_Add"){ //reject add request
   $subject="Add New Project Request Was Being Rejected"; 
}elseif($Mode2=="Accept_Edit"){ //accept edit request
   $subject="Edit Project Request Was Being Accepted"; 
}elseif($Mode2=="Reject_Edit"){ //reject edit request
   $subject="Edit Project Request Was Being Rejected"; 
}elseif($Mode2=="Accept_Remove"){ //accept remove request
   $subject="Remove Project Request Was Being Accepted"; 
}elseif($Mode2=="Reject_Remove"){ //reject remove request
   $subject="Remove Project Request Was Being Rejected"; 
}

//------------------------------------------------------------------------------------ Info And Link For Guiding Receiver 
if(substr($Mode2,0,7)=="Request"){ 
   $guide='Please go to <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/create/index.php?Mode=Admin">Create Item</a> page to handle the request.';
}elseif(substr($Mode2,0,6)=="Accept"||substr($Mode2,0,6)=="Reject"){
   $guide='Please go to the <a href="http://'.$_SERVER['SERVER_NAME'].'/ShareRack/create/project/index.php?Mode=User">Create Item Page</a> to check the result.';
}

//------------------------------------------------------------------------------------ 
$alt = 0; 
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
	
//----------------------------------------------------- Project Name
      $message.='	
      <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'top" style="border-top: 1px solid #acd4e6;">Project Name: </th>
       <td class="top"';
         if($alt==1){
           $message.='class="alt"';
           $alt--;
         }else{
           $alt++;
         }	   
	     $message.='style="border-top: 1px solid #acd4e6;">'.$P_Name.'</td>';
//---------------------
	     if(substr($Mode2,-4)=="Edit"){ //only run when in edit mode
		   if($P_Name!=$P_Name_Old){
             $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Name: </b>'.$P_Name_Old.'</td>'; //show original name when there's any change
	       }else{
             $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Name Is The Same</b></td>';	//show "not any change" when there's not any change	   
		   }
		 }
//---------------------
	  $message.='
      </tr>';
	  
//----------------------------------------------------- Project Begin Date	   
	  $message.=' 
	  <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">Project Begin Date: </th>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.='>'.$P_BeginDate.'</td>';
//---------------------	 
	     if(substr($Mode2,-4)=="Edit"){ //only run when in edit mode
		   if($P_BeginDate!=$P_BeginDate_Old){ //check if the new begin date equal to the old begin date or not
	         $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Begin Date: </b>'.$P_BeginDate_Old.'</td>'; 
 	       }else{
             $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Date Is The Same</b></td>';		   
		   }
		 }   
//---------------------
	  $message.='
	  </tr>';
	  
//----------------------------------------------------- Project End Date	   
	  $message.=' 	      
	  <tr>
       <th scope="row" class="spec'.($alt==1?'alt':'').'">Project End Date: </th>
       <td';
         if($alt==1){
           $message.=' class="alt"';
           $alt--;
         }else{
           $alt++;
         }
         $message.= '>'.$P_EndDate.'</td>';	
//---------------------	 
	     if(substr($Mode2,-4)=="Edit"){ //only run when in edit mode
		   if($P_EndDate!=$P_EndDate_Old){		 
	         $message.='	
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original End Date: </b>'.$P_EndDate_Old.'</td>';
 	       }else{
             $message.='
	         <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	         <td><b>Original Date Is The Same</b></td>';		   
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
         $message.='>'.$P_Mail.'</td>';		 
//---------------------	 
	     if(substr($Mode2,-4)=="Edit"){ //only run when in edit mode
		   if($P_Mail!=$P_Mail_Old){	
	        $message.='	
	        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	        <td><b>Original Contact Mail: </b>  '.$P_Mail_Old.'</td>';
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
//------------------------------------------------------------------------------------ 
if($Mode2=="Reject_Add"||$Mode2=="Accept_Remove"||$Mode2=="Reject_Edit"){
   $sql_query = "DELETE FROM `project` WHERE `P_Name`='$P_Name'";
   mysql_query($sql_query);
}elseif($Mode2=="Accept_Edit"){
   $sql_query = "UPDATE `project` SET `P_Name_Old`='',`P_BeginDate_Old`='',`P_EndDate_Old`='',`P_Mail_Old`='',`P_Permit`='' WHERE `P_Name`='$P_Name'";
   mysql_query($sql_query);	
   $sql_query = "UPDATE `schedule` SET `Sc_project`='$P_Name' WHERE `Sc_project`='$P_Name_Old'";
   mysql_query($sql_query);	
}
//------------------------------------------------------------------------------------ 	 
if($Mode2=="Request_Add"){
  header("Location:./project/index.php?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3");
}elseif($Mode2=="Request_Edit"){
  header("Location:./project/index.php?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3");
}elseif($Mode2=="Request_Remove"){
  header("Location:./project/index.php?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3");
}else{
  header("Location:.?Mode=$Mode&Mode2=$Mode2&Mode3=$Mode3"); 
}?>