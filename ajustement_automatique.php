<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

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
			// Test si la quantité restant dans ajustement n'est pas égale à 0,5
			if ($ajustement_possible<1)
				{
				$ajustement_possible=0;
				$choix_possibles[$nb_choix_possibles]['quantite']=0.5;
				}
			else 
				{
				$ajustement_possible--;
				$choix_possibles[$nb_choix_possibles]['quantite']=1;
				}
			$choix_possibles[$nb_choix_possibles]['membre_ID']=$resultat["commande_membre"];
			$choix_possibles[$nb_choix_possibles]['membre_nom']=$resultat["nom_complet"];
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
				$choisis[$ligne_doublon]['ajustement_retenu']+=$choix_possibles[$argmin]['quantite'];
		}
		else
		{
			$nb_choisis++;
			$choisis[$nb_choisis]['membre_ID']=$choix_possibles[$argmin]['membre_ID'];
			$choisis[$nb_choisis]['membre_nom']=$choix_possibles[$argmin]['membre_nom'];
			if ($a_regler<1)
				$choisis[$nb_choisis]['ajustement_retenu']=$a_regler;
			else
				$choisis[$nb_choisis]['ajustement_retenu']=$choix_possibles[$argmin]['quantite'];
		}
		$choix_possibles[$argmin]['aleatoire']=100000;
		$a_regler-=$choix_possibles[$argmin]['quantite'];		
	}
	return array($choisis,$nb_choisis);
}
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



<table  border='1pt'>
	<thead>
		<th> Membre sélectionné pour ajustement</th>
		<th> Modification apportée à la quantité</th>
	</thead>
<?php
// Cas ajustement vers le haut :
if (isset($_POST['ajustement_haut']))
{
	// Selection des membres dispo pour ajustement
	$requete = mysqli_query($link, "select commande_ajustement, commande_membre, nom_complet from commande left join membres on commande.commande_membre=membres.ID where (commande_produit=".$_POST['produits_ID']." and commande_ajustement>0);");

	$a_regler=$_POST['a_regler'];

	list($choisis,$nb_choisis)=selection_membres_ajustement($requete,$a_regler);
	for ($i=1;$i<=$nb_choisis;$i++)
		{
		echo "<tr>\n";
		echo "<td align='left'>";
		echo $choisis[$i]['membre_nom'];
		echo "</td>\n";
		echo "<td align='right'>";
		echo "+".$choisis[$i]['ajustement_retenu'];
		echo "</td>\n";
		echo "</tr>\n";
		}
}
elseif (isset($_POST['ajustement_bas']))
{
	// Selection des membres dispo pour ajustement
	$requete = mysqli_query($link, "select commande_ajustement, commande_membre, nom_complet from commande left join membres on commande.commande_membre=membres.ID where (commande_produit=".$_POST['produits_ID']." and commande_ajustement<0);");

	$a_regler=$_POST['a_regler'];

	list($choisis,$nb_choisis)=selection_membres_ajustement($requete,$a_regler);
	for ($i=1;$i<=$nb_choisis;$i++)
		{
		$choisis[$i]['ajustement_retenu']=-$choisis[$i]['ajustement_retenu'];
		echo "<tr>\n";
		echo "<td align='left'>";
		echo $choisis[$i]['membre_nom'];
		echo "</td>\n";
		echo "<td align='right'>";
		echo $choisis[$i]['ajustement_retenu'];
		echo "</td>\n";
		echo "</tr>\n";
		}
}
?>
</table>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$commission;?>" method="post">
<input type=hidden name='produits_ID' value='<?php echo $_POST['produits_ID'];?>' >
<input type=hidden name='a_regler' value='<?php echo $a_regler;?>' >
<input type=hidden name='<?php 
if (isset($_POST['ajustement_haut']))
	echo 'ajustement_haut';
elseif (isset($_POST['ajustement_bas']))
	echo 'ajustement_bas';
?>' >
<input class='bouton' type='submit' name='autre' value='Une autre répartition'>
</form>

<form action="validation_ajustement.php?commission=<?php echo $commission;?>" method="post">
<input type=hidden name='produits_ID' value='<?php echo $_POST['produits_ID'];?>' >
<input type=hidden name='choisis' value='<?php /*/echo serialize($choisis);/*/echo rawurlencode(serialize($choisis));/**/?>' >
<input type=hidden name='nb_choisis' value='<?php echo $nb_choisis;?>' >
<input class='bouton' type='submit' name='autre' value='Valider cette répartition'>
</form>


</div>
</body>
 </html>
