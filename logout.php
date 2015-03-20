<?php 
session_start();
$_SESSION['isLoggedIn'] = false;
    
if (isset($_GET['r'])){
  header('Location:.'. $_GET['r']);
}else{
  header('Location:./index.php?Mode=User');
}
?>