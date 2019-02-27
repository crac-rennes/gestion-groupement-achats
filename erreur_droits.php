<?php
session_start();
require("fonctions.php");
verification_identification();
	
?>
<html> 
<head> 
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H4>Vous n'avez pas les droits pour accéder à cette page !</H4>

Debogage si vous arrivez sur cette page merci de noter l'indication ci-dessous et de contacter l'administrateur du site : <p>
<?php echo $_SESSION['statut'] ?>
</div>
</body>
</html>	
