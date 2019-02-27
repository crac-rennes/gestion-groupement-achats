<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

?>
<html>

<head>
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<link rel="icon" type="image/png" href="logo.png" />

</head>

<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">



<?php

// Sélection de tous les produits susceptibles d'être ajustés + total quantite + nombre d'udv par conditionnement + ajustement +/-
$requete=mysqli_query($link, "select produits_ID,produits_conditionnement,produits_nom,commande_produit,sum(commande_ajustement*(commande_ajustement>0)) as ajustement_plus,sum(-commande_ajustement*(commande_ajustement<0)) as ajustement_moins,produits_vrac,sum(commande_quantite) as total_quantite from commande,produits where commande_commission=$commission and commande_produit=produits_ID and produits_vrac!=0 group by commande_produit order by produits_nom;");

$produits_ajustables=array();
$produits_non_ajustables=array();

// Pour toutes les réponses
if (mysqli_num_rows($requete)!=0)
while($resultat = mysqli_fetch_array($requete))
	{
	// Initialisation des variables
	$total_quantite=$resultat['total_quantite'];
	$produits_vrac=abs($resultat['produits_vrac']);
	$ajustement_plus=$resultat['ajustement_plus'];
	$ajustement_moins=$resultat['ajustement_moins'];
	$produit=$resultat['produits_nom'];
	$produit_ID=$resultat['produits_ID'];
	// echo "$produit &nbsp commandés:$total_quantite &nbsp condit:$produits_vrac &nbsp +$ajustement_plus/$ajustement_moins &nbsp ";
	
	// Test pour savoir si un ajustement est possible
	$nb_conditionnement=floor($total_quantite/$produits_vrac);
	$reste_conditionnement=fmod($total_quantite,$produits_vrac);
	if ( ($reste_conditionnement==0) or ($reste_conditionnement<=$ajustement_moins) or (($produits_vrac-$reste_conditionnement)<=$ajustement_plus) )
		{
		// Commande ajustable
		$produits_ajustables[]=$resultat['produits_nom']." en ".$resultat['produits_conditionnement'];
		}
	else
		{
		// Commande non ajustable
		$produits_non_ajustables[]=$resultat['produits_nom']." en ".$resultat['produits_conditionnement'];
		}

	}

echo "<p><p><H4>Liste des produits ajustables automatiquement: </H4><p>";

foreach ($produits_ajustables as $nom_produit)
	{echo " &nbsp - $nom_produit<p>";}

echo "<p><p><h4>Liste des produits non ajustables automatiquement : </H4><p>";
foreach ($produits_non_ajustables as $nom_produit)
	{echo " &nbsp - $nom_produit<p>";}

?>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
</body>
</html>
