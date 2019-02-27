<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);
	
// Récupération du nom du produit
$produit_ID=$_POST['produit_ID'];
$requete=mysqli_query($link, "select produits_nom,produits_conditionnement from produits where produits_ID=$produit_ID;");
$resultat=mysqli_fetch_array($requete);
$produit_nom=$resultat['produits_nom'];
$produit_conditionnement=$resultat['produits_conditionnement'];



if (isset($_POST['oui']))
{
	$requete = mysqli_query($link, "delete from commande where commande_produit=$produit_ID;");
	header("Location: $BASE_URL/gestion_commande.php?commission=".$_GET['commission']);
}
elseif (isset($_POST['non']))
{
	header("Location: $BASE_URL/gestion_commande.php?commission=".$_GET['commission']);
}
?>

<html> 
<head> 
<title>Suppression des commandes de <?php echo $produit_nom;?>
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> Vous allez supprimer les commandes de <?php echo $produit_nom." en ".$produit_conditionnement; ?></font><p>

<font color='red'> Souhaitez vous poursuivre ? </font><p>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$_GET['commission']; ?>" method="post">
	<fieldset style="width: 120px; border-color:#FF0000">
		<center>       
		<input type="hidden" name="produit_ID" value="<?php echo $produit_ID; ?>">
		<input class="bouton" type="submit" name="oui" value="Oui" />        
		<input class="bouton" type="submit" name="non" value="Non" />        
		</center>
	</fieldset>    
</form>  


</div>
</body>
</html>
