<?php 
session_start();
setcookie("identifier","",time()-(3600)); 
setcookie("securitytoken","",time()-(3600)); 
setcookie("username","",time()-(3600)); 
setcookie("usergroup","",time()-(3600)); 
header("Location: index.php");
session_destroy();
?>




 