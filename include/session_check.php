<?php
if(session_id()=='')session_start();

function isLoggedIn() {
  return (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']); //"isset" check if variable exist or not
}

if(isset($private) && $private && !isLoggedIn())
{
  header('Location: ' . $dirLevel . 'login.php?r=' . $_SERVER["REQUEST_URI"]);
  exit(0);
}

?>