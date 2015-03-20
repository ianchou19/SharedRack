<?php
//------------------------------------------------------------------------------------------------- Set Variable
$Mode="User";

//------------------------------------------------------------------------------------------------- SQL Area
require_once('../Connections/ShareRack.php'); 
mysql_select_db($database_ShareRack, $ShareRack);

$query_test="SELECT * FROM user";
$test=mysql_query($query_test,$ShareRack)or die(mysql_error());

while($row_test=mysql_fetch_assoc($test)){
$U_ID[]=$row_test['U_ID'];
$U_Name[]=$row_test['U_Name'];
$U_Password[]=$row_test['U_Password'];
$U_Mail[]=$row_test['U_Mail'];
}

//------------------------------------------------------------------------------------------------- 
Define("KEY",'the quick brown fox jumps over the lazy dog');
$dirLevel="";
session_start();
$WrongPW="";
$WrongUser="";
if($_POST){
$foundUser = false;
  for($i=0;$i<count($U_ID);$i++){
	if(strcmp($U_Name[$i],$_POST['username'])==0){
	  $WrongUser=0;
      if($U_Password[$i]==$_POST['pw']){
        $foundUser = true;
		$WrongPW=0;
		break;
      }else{
        $WrongPW=1;
      }
	  break;
    }else{
	  $WrongUser=1;
	}
  }  
  if ($foundUser){
    $_SESSION['isLoggedIn'] = true;  
    if (isset($_GET['r'])){
      header('Location:' . $_GET['r']);
    }else{
      header('Location:./index.php?Mode=Admin');
    }
    exit(0);
  }
}elseif($_SESSION['isLoggedIn']){
  if (isset($_GET['r'])){
    header('Location: ../' . $_GET['r']);
  }else{
    header('Location:./index.php?Mode=Admin');
  }
  exit(0);
}
?>

<!-- ------------------------------------------------------------------------------------------------- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Login | Moonshot Shared Rack</title>
<link rel="stylesheet" href="css/main.css" />
<link rel="stylesheet" href="css/main.ie.css" />
</head>
<body>
<!-- ----------------------------------------------------------------------- paging tag -->
<?php 
$here = "Login";//Includes header with Login tab underlined
$pages = array(//Pages and their relative URLs
    "Home"=>$dirLevel."update_reserved.php?Mode=$Mode",
    "Info"=>$dirLevel."info/index.php?Mode=$Mode",
    "Schedule"=>$dirLevel."schedule/index.php?Mode=$Mode",
    "Create Item"=>$dirLevel."create/index.php?Mode=$Mode",	
    "Report" =>$dirLevel."report/index.php?Mode=$Mode"); 
$_GET['Mode3']="main_page";		
include($dirLevel.'include/header.php');
?>
<!-- ----------------------------------------------------------------------- -->
<div class="content">
  <form name="login" action="login.php<?php if (isset($_GET['r'])) echo "?r=" . $_GET['r']; ?>" method="post" enctype="multipart/form-data">
  <div style="margin:30px auto;width:300px;">
    <h2>Login</h2>
    <hr>
    <div style="padding:5px 15px;text-align:center;">
      Username<br>
      <input name="username" type="text" /><br>
      Password<br>
      <input name="pw" type="password" /><br>
    </div>
    <hr>
    <input class="button" type="submit" value="Login" />
  </div>
  </form>
  <br><br>
  <center><h3>
  <?php
  if($WrongUser==1){
    //echo "Username is not exist, please type the username again <br>";
    echo "<script type='text/javascript'>alert('Username is not exist, please type the username again !');</script>";	
  }elseif($WrongPW==1){
    //echo "Password is incorrect, please type the password again";
    echo "<script type='text/javascript'>alert('Password is incorrect, please type the password again !');</script>";	
  }
  ?>
  </h3>
  </center>
</div>
</body>
</html>