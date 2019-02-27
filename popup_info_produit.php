<?php
session_start();
require("fonctions.php");
verification_identification();

$requete=mysqli_query($link,"select produits_description from produits where produits_ID=".$_GET['produits_ID'].";");
if ($resultat=mysqli_fetch_array($requete))
	$info=$resultat['produits_description'];
else
	$info='';
	
?>

<html> 
<head> 
<title>Info produit</title> 

<SCRIPT LANGUAGE="JavaScript">
function resize(x,y) {
window.resizeTo(x,y); window.focus();
}
</SCRIPT>
<link rel="stylesheet" href="style.css">

</head> 

<body>

<?php 
if ($info!='')
	echo $info;
else
	echo "Pas d'informations disponibles";
?>

<p align='center'><a href='javascript:window.close()'>Fermer</a></p>

</body> 
</html>
