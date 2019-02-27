<?php
session_start();
require("fonctions.php");
//verification_identification();

$buffer = '<?xml version="1.0"?>';

$prod=$_POST["produit"];
$fourn=$_POST["fournisseur"];
$com=$_POST["commission"];

$buffer .= "<reponse>";


if ($prod==-1)
	{
	$requete="select commande_membre from produits,commande where (produits_fournisseur=$fourn and produits_ID=commande_produit and produits_commission=$com and produits_actif=1) group by commande_membre;";
	}
else
	{
	$requete="select commande_membre from produits,commande where (produits_id=$prod and produits_ID=commande_produit and produits_actif=1);";
	}

$res = mysqli_query($requete);
while($row = mysqli_fetch_assoc($res))
		{
		$ID=$row['commande_membre'];
		$buffer.= "<id>".$ID."</id>";
		}


$buffer .= "</reponse>";

header('Content-Type: text/xml');
echo $buffer;

?>
