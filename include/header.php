<?php
if($_GET['Mode3']=="main_page"){
  $dirLevel="./";
}else{
  $dirLevel="../";
}
//------------------------------------------------------------------------------------------------- GET Variable
require_once($dirLevel.'../Connections/ShareRack.php');
mysql_select_db($database_ShareRack, $ShareRack);

$query_test="SELECT Tr_time FROM Time_record WHERE Tr_type='Include_header'"; // get chassis info
$test=mysql_query($query_test,$ShareRack)or die(mysql_error());

$row_test = mysql_fetch_assoc($test);
$Title=$row_test['Tr_time'];
?>
<div class="header gradient_hp">
  <div class="navbar">
    <ul><?php
if (isset($pages))
{
  foreach($pages as $page=>$link)
  {?>
      <li class="first<?php if (strcmp($here,$page)==0) echo " here" ?>"><a href="<?php echo $link; ?>"><?php echo $page; ?></a></li><?php
  }
}?>
    </ul>
  </div>
  <div class="account_info">
    <ul><?php  
  if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']){?>
      <li>
	    <a href="<?php echo $dirLevel; ?>logout.php">Logout</a>
	  </li>	
  <?php
  }else{?>
      <li>     
	    <a href="<?php echo $dirLevel; ?>login.php">Login</a>
	  </li>	
      <li> 
        <a href="mailto:moonshotsharedracktech@hp.com">Contact</a>
	  </li>			
  <?php
  }?>
    <ul>
  </div>
  <h1 class="banner"><?php echo $Title; if (isset($headerAppend)) echo $headerAppend; ?></h1>
</div>