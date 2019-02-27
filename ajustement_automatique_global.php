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
</head>

<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">





<?php

// echo "Produit &nbsp total quantite &nbsp udv/cond &nbsp ajust+ &nbsp ajust- &nbsp verdict <p>";

//fonction de selection aleatoire des membres
function selection_membres_ajustement($requete,$a_regler)
{
	//Remplissage d'un tableau :
	// une ligne (quantité de 1 pour 1 membre) : id_membre, nombre aléatoire
	// un meme membre peut être présent plusieurs fois s'il a mis +2 ou +3...
	$choix_possibles=array();
	$nb_choix_possibles=0;
	while(($resultat = mysqli_fetch_array($requete)))
	{
		$ajustement_possible=abs($resultat["commande_ajustement"]);
		while ($ajustement_possible>0)
		{
			$nb_choix_possibles++; // on ajoute une ligne
			$ajustement_possible--;
			$choix_possibles[$nb_choix_possibles]['membre_ID']=$resultat["commande_membre"];
			// attribution d'un nombre aléatoire pour classement au hasard
			do
			{
				$doublon=0;
				$aleatoire=rand(1,100000);
				// On teste si un nombre identique existe déjà
				for ($i=1;$i<$nb_choix_possibles;$i++)
					if ($aleatoire==$choix_possibles[$i]['aleatoire'])
						$doublon=1;
			} while ($doublon=0);
			$choix_possibles[$nb_choix_possibles]['aleatoire']=$aleatoire;
			//echo $choix_possibles[$nb_choix_possibles]['membre_nom']."&nbsp".$choix_possibles[$nb_choix_possibles]['aleatoire']."<p>";
		}
	}
	
	// Selection aleatoire des membres à qui ajouter de la quantite en utilisant la colonne aléatoire
	$choisis=array();
	$nb_choisis=0;
	while ($a_regler>0)
	{
		// Initialisation pour la recherche du minimum
		$min=$choix_possibles[1]['aleatoire'];
		$argmin=1;
		for ($i=2;$i<=$nb_choix_possibles;$i++)
			if ($choix_possibles[$i]['aleatoire']<$min)	
			{
				$min=$choix_possibles[$i]['aleatoire'];
				$argmin=$i;
			}
		// On a la ligne à rajouter, on  teste si le meme membre n'a pas déjà été choisi
		$doublon=0;
		for ($i=1;$i<=$nb_choisis;$i++)
			if ($choisis[$i]['membre_ID']==$choix_possibles[$argmin]['membre_ID'])
				{
				$doublon=1;
				$ligne_doublon=$i;
				}
		if ($doublon)
		{
			if ($a_regler<1)
				$choisis[$ligne_doublon]['ajustement_retenu']+=$a_regler;
			else
				$choisis[$ligne_doublon]['ajustement_retenu']+=1;
		}
		else
		{
			$nb_choisis++;
			$choisis[$nb_choisis]['membre_ID']=$choix_possibles[$argmin]['membre_ID'];
			if ($a_regler<1)
				$choisis[$nb_choisis]['ajustement_retenu']=$a_regler;
			else
				$choisis[$nb_choisis]['ajustement_retenu']=1;
		}
		$choix_possibles[$argmin]['aleatoire']=100000;
		$a_regler--;		
	}
	return array($choisis,$nb_choisis);
}

// Sélection de tous les produits susceptibles d'être ajustés + total quantite + nombre d'udv par conditionnement + ajustement +/-
$requete=mysqli_query($link, "select produits_ID,produits_conditionnement,produits_nom,commande_produit,sum(commande_ajustement*(commande_ajustement>0)) as ajustement_plus,sum(-commande_ajustement*(commande_ajustement<0)) as ajustement_moins,produits_vrac,sum(commande_quantite) as total_quantite from commande,produits where commande_commission=2 and commande_produit=produits_ID and produits_vrac!=0 group by commande_produit order by produits_nom;");

$produits_ajustes=array();
$nb_produits_ajustes=0;

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
	if ($reste_conditionnement==0)
		{
		// Commande qui tombe pile-poil !
//		echo "bon ($nb_conditionnement)<p>\n";
		mysqli_query($link, "update commande set commande_ajustement=0 where (commande_produit=$produit_ID and commande_commission=$commission);");
		$produits_ajustes[$nb_produits_ajustes++]=$resultat['produits_nom']." en ".$resultat['produits_conditionnement'];
		}
	elseif (($produits_vrac-$reste_conditionnement)<=$ajustement_plus)
		{
		// ajustement à la hausse
		$nb_conditionnement++;
//		echo "hausse ($nb_conditionnement)<p>";
		
		// selection des candidats à l'ajustement
		$requete_membre = mysqli_query($link, "select commande_ajustement, commande_membre from commande left join membres on commande.commande_membre=membres.ID where (commande_produit=".$resultat['commande_produit']." and commande_ajustement>0);");
		list($choisis,$nb_choisis)=selection_membres_ajustement($requete_membre,$produits_vrac-$reste_conditionnement);
		
		//echo $produit_ID." **";
		//pour empecher un probleme si on recharge la page...
		if (mysqli_num_rows(mysqli_query($link, "select * from commande where (commande_produit=$produit_ID  and commande_ajustement<>0);")))
			{
			for ($i=1;$i<=$nb_choisis;$i++)
				{
				$membre=$choisis[$i]['membre_ID'];
				$ajustement=$choisis[$i]['ajustement_retenu'];
//				echo $membre."//".$ajustement;
				mysqli_query($link, "update commande set commande_quantite=commande_quantite+$ajustement where (commande_produit=$produit_ID and commande_membre=$membre and commande_commission=$commission);");
//				echo "update commande set commande_quantite=commande_quantite+$ajustement where (commande_produit=$produit_ID and commande_membre=$membre and commande_commission=$commission);";
				}
			mysqli_query($link, "update commande set commande_ajustement=0 where (commande_produit=$produit_ID and commande_commission=$commission);");
			}
		$produits_ajustes[$nb_produits_ajustes++]=$resultat['produits_nom']." en ".$resultat['produits_conditionnement'];

		}
	elseif ($reste_conditionnement<=$ajustement_moins)
		{
		// ajustement à la baisse (on reste sur le nombre de conditionnement mais on enleve des quantites sur certaines commandes
//		echo "baisse ($nb_conditionnement)<p>";
		
		// selection des candidats à l'ajustement
		$requete_membre = mysqli_query($link, "select commande_ajustement, commande_membre from commande left join membres on commande.commande_membre=membres.ID where (commande_produit=".$resultat['commande_produit']." and commande_ajustement<0);");
		list($choisis,$nb_choisis)=selection_membres_ajustement($requete_membre,$reste_conditionnement);
		
		if (mysqli_num_rows(mysqli_query($link, "select * from commande where (commande_produit=$produit_ID  and commande_ajustement<>0);"))) //pour empecher un probleme si on recharge la page...
			{
			for ($i=1;$i<=$nb_choisis;$i++)
				{
				$membre=$choisis[$i]['membre_ID'];
				$ajustement=$choisis[$i]['ajustement_retenu'];
				mysqli_query($link, "update commande set commande_quantite=commande_quantite-$ajustement where (commande_produit=$produit_ID and commande_membre=$membre and commande_commission=$commission);");
				}
			mysqli_query($link, "update commande set commande_ajustement=0 where (commande_produit=$produit_ID and commande_commission=$commission);");
			// Suppression des commandes pour lesquelles la quantité est nulle :
			mysqli_query($link, "delete from commande where (commande_quantite=0 and commande_commission=$commission);");
			}	
		$produits_ajustes[$nb_produits_ajustes++]=$resultat['produits_nom']." en ".$resultat['produits_conditionnement'];

		}
//	else
//		echo "Impossible :-( <p>\n";

	}
	
echo "<p><p>".$nb_produits_ajustes." produits ajustés : <p>";
for ($i=0;$i<$nb_produits_ajustes;$i++)
	echo " &nbsp - ".$produits_ajustes[$i]."<p>";
?>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour Ã  la gestion de commande</a></div>

</div>
</body>
</html>
