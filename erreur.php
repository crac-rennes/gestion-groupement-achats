<?php  
session_start();
$_SESSION = array();  
session_destroy();  
unset($_SESSION);
//exit();
?>

<html>  
<head>    
<title>Erreur</title>
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body onload="if (self != top) top.location = self.location">    
<div id="menu">
	
</div>
<div id="main">

Erreur, vous devez être identifié pour accéder à cette page.
</p>
<a href="index.php"> Retour à la page d'authentification</a>
</div>
</body>
</html>
